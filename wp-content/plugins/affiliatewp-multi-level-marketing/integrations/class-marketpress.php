<?php

class AffiliateWP_MLM_MarketPress extends AffiliateWP_MLM_Base {

	var $is_version_3 = true;

	/**
	 * Get things started
	 *
	 * @access public
	 * @since  1.1
	*/
	public function init() {

		$this->context = 'marketpress';
		$this->is_version_3 = $this->get_mp_version() == '2.0' ? false : true;
		
		/* Check for Marketpress */
		$integrations = affiliate_wp()->settings->get( 'affwp_mlm_integrations' );
				
		if ( ! isset( $integrations['marketpress'] ) ) return; // MLM integration for Marketpress is disabled 

		if( $this->is_version_3 ){
			add_action( 'mp_order_order_paid', array( $this, 'mark_referrals_complete' ) );
			add_action( 'mp_order_trashed', array( $this, 'revoke_referrals_on_delete' ) );
		} else {
			add_action( 'mp_order_paid', array( $this, 'mark_referrals_complete' ) );
			add_action( 'trash_mp_order', array( $this, 'revoke_referrals_on_delete' ), 10, 2 );
		}

		// Process referral
		add_action( 'affwp_post_insert_referral', array( $this, 'process_referral' ), 10, 2 );
		
	}


	/**
	 * Get MarketPress version.
	 *
	 * @access  public
	 */
	public function get_mp_version() {
		$mp_version = false;
		if ( defined( 'MP_VERSION' ) ) {
			$mp_version = MP_VERSION;
		} else {
			global $mp_version;
		}
		// Strip out any beta or RC components from version... get base version
		$mp_version = preg_replace( '/\.\D.*/', '', $mp_version );
		$mp_version = version_compare( $mp_version, '3.0', '>=' ) ? '3.0' : '2.0';
		return $mp_version;
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

		$data['affiliate_id'] = $parent_affiliate_id;
		$data['description']  = $this->get_referral_description( $level_count, $direct_affiliate, $data['reference'] );
		$data['amount']       = $amount;
		$data['custom']       = 'indirect'; // Add referral type as custom referral data
		$data['context']      = 'marketpress';

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
	public function process_cart( $parent_affiliate_id, $data, $level_count = 0  ) {

		global $mp;
		$order_id = $data['reference'];
		$order = $mp->get_order( $order_id );

		if( $this->is_version_3 ) {
			$amount         = $order->get_meta( 'mp_order_total' );
			$cart           = $order->get_meta( 'mp_cart_info' );
			$items          = wp_list_pluck( $cart->get_items_as_objects(), 'ID' );
			$tax_total      = $order->get_meta( 'mp_tax_total', 0 );
			$shipping_total = $order->get_meta( 'mp_shipping_total', 0 );
		} else {
			$amount         = $order->mp_order_total;
			$items          = $order->mp_cart_info;
			$tax_total      = $order->mp_tax_total;
			$shipping_total = $order->mp_shipping_total;
		}

		if( affiliate_wp()->settings->get( 'exclude_tax' ) ) {
			$amount -= $tax_total;
		}
		if( affiliate_wp()->settings->get( 'exclude_shipping' ) ) {
			$amount -= $shipping_total;
		}

		$product_id = ''; // Leave empty until MarketPress integration supports per-product rates
		$referral_total = $this->calculate_referral_amount( $parent_affiliate_id, $amount, $order_id, $product_id, $level_count );

		return $referral_total;

	}

	/**
	 * Mark referrals as complete
	 *
	 * @since 1.1
	 */
	public function mark_referrals_complete( $order = array() ) {

		$order_id = $order->ID;
		
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
	 * Revokes referrals when an order is deleted
	 *
	 * @access  public
	 * @since   1.1
	*/
	public function revoke_referrals_on_delete( $order ) {

		if( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
			return;
		}

		$order_id = $order;
		
		if( $this->is_version_3 ){
			$order_id = $order->ID;
		}

		if( 'mp_order' != get_post_type( $order_id ) ) {
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
	 * Retrieve the MarketPress referral description
	 *
	 * @since   1.1
	*/
	public function get_referral_description( $level_count, $direct_affiliate, $order_id = 0 ) {

		if ( empty( $order_id ) ) {
			return;
		}
		
		global $mp;
		$order = $mp->get_order( $order_id );

		if( $this->is_version_3 ) {
			$cart = $order->get_meta( 'mp_cart_info' );
			$items = wp_list_pluck( $cart->get_items_as_objects(), 'ID' );
		} else {
			$items = $order->mp_cart_info;
		}
		
		$description = array();
		$item_names = array();
		
		foreach( $items as $item ) {
			if ( is_array( $item ) ) {
				$order_items = $item;
				foreach( $order_items as $order_item ) {
					$item_names[] = $order_item['name'];
				}
			} else {
				$item_names[] = get_the_title( $item );
			}
		}

		$description[] = $direct_affiliate . ' | Level '. $level_count . ' | ' . implode( ', ', $item_names );
		
		return implode( ', ', $description );
	}

}
new AffiliateWP_MLM_MarketPress;