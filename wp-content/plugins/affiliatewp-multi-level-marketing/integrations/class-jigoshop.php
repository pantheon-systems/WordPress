<?php

class AffiliateWP_MLM_Jigoshop extends AffiliateWP_MLM_Base {

	/**
	 * The order object
	 *
	 * @access  private
	 * @since   1.1
	*/
	private $order;

	/**
	 * Get things started
	 *
	 * @access public
	 * @since  1.1
	*/
	public function init() {

		$this->context = 'jigoshop';
		
		/* Check for JigoShop */
		$integrations = affiliate_wp()->settings->get( 'affwp_mlm_integrations' );
				
		if ( ! isset( $integrations['jigoshop'] ) ) return; // MLM integration for JigoShop is disabled 
		
		add_action( 'jigoshop_order_status_completed', array( $this, 'mark_referrals_complete' ), 10 ); 
		add_action( 'jigoshop_order_status_completed_to_refunded', array( $this, 'revoke_referrals_on_refund' ), 10 ); 

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
		$data['context']      = 'jigoshop';

		unset( $data['date'] );
		unset( $data['currency'] );
		unset( $data['status'] );

		if ( ! (bool) apply_filters( 'affwp_mlm_create_indirect_referral', true, $data, $parent_affiliate_id, $affiliate_id, $referral_id, $level_count ) ) {
			return false; // Allow extensions to prevent indirect referrals from being created
		}
		
		// Create the referral
		$referral_id = affiliate_wp()->referrals->add( apply_filters( 'affwp_mlm_insert_pending_referral', $data, $parent_affiliate_id, $affiliate_id, $referral_id, $level_count ) );

		if ( $referral_id ) {

			$amount 	= affwp_currency_filter( affwp_format_amount( $amount ) );
			$name   	= affiliate_wp()->affiliates->get_affiliate_name( $parent_affiliate_id );
			$order_id = $data['reference'];
			$this->order = apply_filters( 'affwp_get_jigoshop_order', new jigoshop_order( $order_id ) );
			
			$this->order->add_order_note( sprintf( __( 'Indirect Referral #%d for %s recorded for %s', 'affiliatewp-multi-level-marketing' ), $referral_id, $amount, $name ) );

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
		$this->order = apply_filters( 'affwp_get_jigoshop_order', new jigoshop_order( $order_id ) );
		$amount = $this->order->order_total;
		
		if( affiliate_wp()->settings->get( 'exclude_tax' ) ) {
			$amount -= $this->order->get_total_tax();
		}
		if( affiliate_wp()->settings->get( 'exclude_shipping' ) ) {
			$amount -= $this->order->order_shipping;
		}

		if ( 0 == $amount && affiliate_wp()->settings->get( 'ignore_zero_referrals' ) ) {
			return false; // Ignore a zero amount referral
		}

		$product_id = ''; // Leave empty until this integration supports per-product rates
		$referral_total = $this->calculate_referral_amount( $parent_affiliate_id, $amount, $order_id, $product_id, $level_count );

		return $referral_total;

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
	 * Retrieve the Jigoshop referral description
	 *
	 * @since   1.1
	*/
	public function get_referral_description( $level_count, $direct_affiliate, $order_id = 0 ) {

		if ( empty( $order_id ) ) {
			return;
		}
		
		$this->order = apply_filters( 'affwp_get_jigoshop_order', new jigoshop_order( $order_id ) );
		$description = array();
		$items       = $this->order->items;
		$item_names = array();
		
		foreach( $items as $key => $item ) {

			$item_names[] = $item['name'];

		}
		
		$description[] = $direct_affiliate . ' | Level '. $level_count . ' | ' . implode( ', ', $item_names );
		
		return implode( ', ', $description );

	}

}
new AffiliateWP_MLM_Jigoshop;