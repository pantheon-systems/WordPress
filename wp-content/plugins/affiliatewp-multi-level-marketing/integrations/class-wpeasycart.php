<?php

class AffiliateWP_MLM_WPEasyCart extends AffiliateWP_MLM_Base {

	/**
	 * The cart data
	 *
	 * @access  public
	 * @since   1.1
	*/
	public $cart;
	
	/**
	 * The order data
	 *
	 * @access  public
	 * @since   1.1
	*/
	public $order_totals;

	/**
	 * Get things started
	 *
	 * @access public
	 * @since  1.1
	*/
	public function init() {

		$this->context = 'wpeasycart';
		
		/* Check for WP Easy Cart */
		$integrations = affiliate_wp()->settings->get( 'affwp_mlm_integrations' );
				
		if ( ! isset( $integrations['wpeasycart'] ) ) return; // MLM integration for WP Easy Cart is disabled 
		
		// Hook in before add_pending_referral() runs to save the data
		add_action( 'wpeasycart_order_inserted', array( $this, 'get_order_data' ), -1, 5 );
		add_action( 'wpeasycart_order_paid', array( $this, 'mark_referrals_complete' ), 10 );
		add_action( 'wpeasycart_full_order_refund', array( $this, 'revoke_referrals_on_refund' ), 10 );

		// Process referral
		add_action( 'affwp_post_insert_referral', array( $this, 'process_referral' ), 10, 2 );
	}

	/**
	 * Get the order data
	 *
	 * @since 1.1
	 */
	public function get_order_data( $order_id, $cart, $order_totals, $user, $payment_type ) {

		// Store the data for later
		$this->cart = $cart;
		$this->order_totals = $order_totals;
		
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
	 * @since 1.1
	 */
	public function create_parent_referral( $parent_affiliate_id, $referral_id, $data, $level_count = 0, $affiliate_id ) {

		$direct_affiliate = affiliate_wp()->affiliates->get_affiliate_name( $affiliate_id );

		// Process cart and get amount
		$amount = $this->process_cart( $parent_affiliate_id, $data, $level_count );
		$cart = $this->cart;
		$items = $cart->cart;

		$data['affiliate_id'] = $parent_affiliate_id;
		$data['description']  = $this->get_referral_description( $level_count, $direct_affiliate, $items );
		$data['amount']       = $amount;
		$data['custom']       = 'indirect'; // Add referral type as custom referral data
		$data['context']      = 'wpeasycart';

		unset( $data['date'] );
		unset( $data['currency'] );
		unset( $data['status'] );

		if ( ! (bool) apply_filters( 'affwp_mlm_create_indirect_referral', true, $data, $parent_affiliate_id, $affiliate_id, $referral_id, $level_count ) ) {
			return false; // Allow extensions to prevent indirect referrals from being created
		}
		
		// Create the referral
		$referral_id = affiliate_wp()->referrals->add( apply_filters( 'affwp_mlm_insert_pending_referral', $data, $parent_affiliate_id, $affiliate_id, $referral_id, $level_count ) );

		if ( $referral_id ) {

			do_action( 'affwp_mlm_indirect_referral_created', $referral_id, $data );

		}

	}

	/**
	 * Process cart
	 *
	 * @since 1.1
	 */
	public function process_cart( $parent_affiliate_id, $data, $level_count = 0 ) {

		$order_id = $data['reference'];

		$cart_shipping = $this->order_totals->shipping_total;
		$cart_tax      = $this->order_totals->tax_total;
		$cart = $this->cart;
		$items = $cart->cart;

		// Calculate the referral amount based on product prices
		$amount = 0.00;

		foreach( $items as $cart_item ) {
			if( $cart_item->has_affiliate_rule ) {
				continue; // Referrals are disabled on this product
			}
			// The order discount has to be divided across the items
			$product_total = $cart_item->total_price;
			$shipping      = 0;
			if( $cart_shipping > 0 && ! affiliate_wp()->settings->get( 'exclude_shipping' ) ) {
				$shipping       = $cart_shipping / count( $items );
				$product_total += $shipping;
			}
			if( $cart_tax > 0 && ! affiliate_wp()->settings->get( 'exclude_tax' ) ) {
				$tax            = $cart_tax / count( $items );
				$product_total += $tax;
			}
			if( $product_total <= 0 ) {
				continue;
			}
			
			$amount += $this->calculate_referral_amount( $parent_affiliate_id, $product_total, $order_id, $cart_item->product_id, $level_count );

		}

		if ( 0 == $amount && affiliate_wp()->settings->get( 'ignore_zero_referrals' ) ) {
			return false; // Ignore a zero amount referral
		}

		return $amount;

	}

	/**
	 * Mark referrals as complete
	 *
	 * @since 1.1
	 */
	public function mark_referrals_complete( $order_id = 0 ) {

		if ( empty( $order_id ) ) {
			return false;
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
	 * Revoke referrals when a payment is refunded
	 *
	 * @since 1.1
	 */
	public function revoke_referrals_on_refund( $order_id = 0 ) {
	
		if ( empty( $order_id ) ) {
			return;
		}
		
		if ( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
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
	 * Retrieve the WP EasyCart referral description
	 *
	 * @since   1.1
	*/
	public function get_referral_description( $level_count, $direct_affiliate, $items ) {

		if ( empty( $items ) ) {
			return;
		}

		$description = array();
		$item_names = array();

		foreach ( $items as $key => $item ) {

			$item_names[] = $item->title;

		}
		
		$description[] = $direct_affiliate . ' | Level '. $level_count . ' | ' . implode( ', ', $item_names );
		
		return implode( ', ', $description );

	}

}
new AffiliateWP_MLM_WPEasyCart;