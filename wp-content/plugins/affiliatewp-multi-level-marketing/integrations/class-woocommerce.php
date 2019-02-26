<?php

class AffiliateWP_MLM_WooCommerce extends AffiliateWP_MLM_Base {

	/**
	 * The order object
	 *
	 * @access  public
	 * @since   1.0
	*/
	public $order;

	/**
	 * Get things started
	 *
	 * @access public
	 * @since  1.0
	*/
	public function init() {

		$this->context = 'woocommerce';
		
		/* Check for WooCommerce */
		$integrations = affiliate_wp()->settings->get( 'affwp_mlm_integrations' );
				
		if ( ! isset( $integrations['woocommerce'] ) ) return; // MLM integration for WooCommerce is disabled 
		
		add_action( 'woocommerce_order_status_completed', array( $this, 'mark_referrals_complete' ), 5 );
		add_action( 'woocommerce_order_status_processing', array( $this, 'mark_referrals_complete' ), 5 );

		// Handle order updates/cancellations
		add_action( 'woocommerce_order_status_completed_to_refunded', array( $this, 'revoke_referrals_on_refund' ), 10 );
		add_action( 'woocommerce_order_status_on-hold_to_refunded', array( $this, 'revoke_referrals_on_refund' ), 10 );
		add_action( 'woocommerce_order_status_processing_to_refunded', array( $this, 'revoke_referrals_on_refund' ), 10 );
		add_action( 'woocommerce_order_status_processing_to_cancelled', array( $this, 'revoke_referrals_on_refund' ), 10 );
		add_action( 'woocommerce_order_status_completed_to_cancelled', array( $this, 'revoke_referrals_on_refund' ), 10 );
		add_action( 'woocommerce_order_status_pending_to_cancelled', array( $this, 'revoke_referrals_on_refund' ), 10 );
		add_action( 'woocommerce_order_status_pending_to_failed', array( $this, 'revoke_referrals_on_refund' ), 10 );
		add_action( 'wc-on-hold_to_trash', array( $this, 'revoke_referrals_on_refund' ), 10 );
		add_action( 'wc-processing_to_trash', array( $this, 'revoke_referrals_on_refund' ), 10 );
		add_action( 'wc-completed_to_trash', array( $this, 'revoke_referrals_on_refund' ), 10 );

		// Process referral
		add_action( 'affwp_post_insert_referral', array( $this, 'process_referral' ), 10, 2 );
		
	}

	/**
	 * Process referral
	 *
	 * @since 1.1
	 */
	public function process_referral( $referral_id, $data ) {
		
		$this->prepare_indirect_referrals( $referral_id, $data );

	}

	/**
	 * Creates the referral for parent affiliate
	 *
	 * @since 1.0
	 */
	public function create_parent_referral( $parent_affiliate_id, $referral_id, $data, $level_count = 0, $affiliate_id ) {

		$direct_affiliate = affiliate_wp()->affiliates->get_affiliate_name( $affiliate_id );

		// Process cart and get amount
		$amount = $this->process_cart( $parent_affiliate_id, $data, $level_count );

		$data['affiliate_id'] = $parent_affiliate_id;
		$data['description']  = $this->get_referral_description( $level_count, $direct_affiliate );
		$data['amount']       = $amount;
		$data['custom']       = 'indirect'; // Add referral type as custom referral data
		$data['context']      = 'woocommerce';

		unset( $data['date'] );
		unset( $data['currency'] );
		unset( $data['status'] );

		if ( ! (bool) apply_filters( 'affwp_mlm_create_indirect_referral', true, $data, $parent_affiliate_id, $affiliate_id, $referral_id, $level_count ) ) {
			return false; // Allow extensions to prevent indirect referrals from being created
		}
		
		// create referral
		$referral_id = affiliate_wp()->referrals->add( apply_filters( 'affwp_mlm_insert_pending_referral', $data, $parent_affiliate_id, $affiliate_id, $referral_id, $level_count ) );

		if ( $referral_id ) {

			$amount = affwp_currency_filter( affwp_format_amount( $amount ) );
			$name   = affiliate_wp()->affiliates->get_affiliate_name( $parent_affiliate_id );

			$this->order->add_order_note( sprintf( __( 'Indirect Referral #%d for %s recorded for %s', 'affiliatewp-multi-level-marketing' ), $referral_id, $amount, $name ) );

			do_action( 'affwp_mlm_indirect_referral_created', $referral_id, $data );

		}

	}

	/**
	 * Process cart
	 *
	 * @since 1.0
	 */
	public function process_cart( $parent_affiliate_id, $data, $level_count = 0  ) {

		$order_id      = $data['reference'];

		$this->order   = apply_filters( 'affwp_get_woocommerce_order', new WC_Order( $order_id ) );
		
		$cart_shipping = $this->order->get_total_shipping();

		$items         = $this->order->get_items();

		// Calculate the referral amount based on product prices
		$amount = 0.00;
		foreach( $items as $product ) {

			if( get_post_meta( $product['product_id'], '_affwp_' . $this->context . '_referrals_disabled', true ) ) {
				continue; // Referrals are disabled on this product
			}

			// The order discount has to be divided across the items
			$product_total = $product['line_total'];
			$shipping      = 0;

			if( $cart_shipping > 0 && ! affiliate_wp()->settings->get( 'exclude_shipping' ) ) {

				$shipping       = $cart_shipping / count( $items );
				$product_total += $shipping;

			}

			if( ! affiliate_wp()->settings->get( 'exclude_tax' ) ) {

				$product_total += $product['line_tax'];

			}

			if( $product_total <= 0 ) {
				continue;
			}

			$amount += $this->calculate_referral_amount( $parent_affiliate_id, $product_total, $order_id, $product['product_id'], $level_count );

		}

		if ( 0 == $amount && affiliate_wp()->settings->get( 'ignore_zero_referrals' ) ) {
			return false; // Ignore a zero amount referral
		}

		return $amount;

	}

	/**
	 * Mark referrals as complete
	 *
	 * @since 1.0
	 */
	public function mark_referrals_complete( $order_id = 0 ) {

		if ( empty( $order_id ) ) {
			return false;
		}
		
		$this->order = apply_filters( 'affwp_get_woocommerce_order', new WC_Order( $order_id ) );
		
		if ( true === version_compare( WC()->version, '3.0.0', '>=' ) ) {
			$payment_method = $this->order->get_payment_method();
		} else {
			$payment_method = get_post_meta( $order_id, '_payment_method', true );
		}
		
		// If the WC status is 'wc-processing' and a COD order, leave as 'pending'.
		if ( 'wc-processing' == $this->order->get_status() && 'cod' === $payment_method ) {
			return;
		}
		
		$reference = $order_id;
		$referrals = affwp_mlm_get_referrals_for_order( $order_id, $this->context );
		
		if ( empty( $referrals ) ) {
			return false;
		}

		foreach ( $referrals as $referral ) {
		
			$this->complete_referral( $referral, $reference );
			
		}

	}

	/**
	 * Revoke referrals when an order is refunded
	 *
	 * @since 1.0
	 */
	public function revoke_referrals_on_refund( $order_id = 0 ) {
	
		if ( empty( $order_id ) ) {
			return;
		}
		
		if ( is_a( $order_id, 'WP_Post' ) ) {
			$order_id = $order_id->ID;
		}
		
		if ( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
			return;
		}
		
		if( 'shop_order' != get_post_type( $order_id ) ) {
			return;
		}

		$referrals = affwp_mlm_get_referrals_for_order( $order_id, $this->context );

		if ( empty( $referrals ) ) {
			return;
		}

		foreach ( $referrals as $referral ) {

			$this->reject_referral( $referral );

		}

	}

	/**
	 * Retrieve the WooCommerce referral description
	 *
	 * @since   1.0
	*/
	public function get_referral_description( $level_count, $direct_affiliate ) {

		$items       = $this->order->get_items();
		$description = array();
		$item_names = array();

		foreach ( $items as $key => $item ) {

			if ( get_post_meta( $item['product_id'], '_affwp_' . $this->context . '_referrals_disabled', true ) ) {
				continue; // Referrals are disabled on this product
			}

			$item_names[] = $item['name'];

		}
		
		$description[] = $direct_affiliate . ' | Level '. $level_count . ' | ' . implode( ', ', $item_names );
		
		return implode( ', ', $description );

	}



}
new AffiliateWP_MLM_WooCommerce;