<?php

class AffiliateWP_MLM_Membermouse extends AffiliateWP_MLM_Base {

	/**
	 * The affiliate data object
	 *
	 * @access  public
	 * @since   1.0.5
	*/
	public $aff_data;

	/**
	 * Get things started
	 *
	 * @access public
	 * @since  1.0.5
	*/
	public function init() {

		$this->context = 'membermouse';
		
		/* Check for MemberMouse */
		$integrations = affiliate_wp()->settings->get( 'affwp_mlm_integrations' );
				
		if ( ! isset( $integrations['membermouse'] ) ) return; // MLM integration for MemberMouse is disabled 
		
		add_action( 'mm_commission_initial', array( $this, 'store_affiliate_data' ), -1, 1 );
		add_action( 'affwp_complete_referral', array( $this, 'mark_referrals_complete' ), 10, 3 );
		add_action( 'mm_refund_issued', array( $this, 'revoke_referrals_on_refund' ), 10 );

		// Process referral
		add_action( 'affwp_post_insert_referral', array( $this, 'process_referral' ), 10, 2 );
		
	}

	/**
	 * Store affiliate data for the order
	 *
	 * @since 1.0.5
	 */
	public function store_affiliate_data( $affiliate_data ) {

		$this->aff_data = $affiliate_data;
	
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
	 * @since 1.0.5
	 */
	public function create_parent_referral( $parent_affiliate_id, $referral_id, $data, $level_count = 0, $affiliate_id ) {

		$direct_affiliate = affiliate_wp()->affiliates->get_affiliate_name( $affiliate_id );

		// Process order and get amount
		$amount = $this->process_order( $parent_affiliate_id, $data, $level_count );

		$data['affiliate_id'] = $parent_affiliate_id;
		$data['description']  = $direct_affiliate . ' | Level '. $level_count . ' | ' . $data['description'];
		$data['amount']       = $amount;
		$data['custom']       = 'indirect'; // Add referral type as custom referral data
		$data['context']      = 'membermouse';

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

			do_action( 'affwp_mlm_indirect_referral_created', $referral_id, $data );

		}

	}

	/**
	 * Process order
	 *
	 * @since 1.0.5
	 */
	public function process_order( $parent_affiliate_id, $data, $level_count = 0  ) {
		
		$reference = $data['reference'];
		$affiliate_data = $this->aff_data;
		$order_total = $affiliate_data['order_total'];

		$amount = $this->calculate_referral_amount( $parent_affiliate_id, $order_total, $reference, $product_id, $level_count );

		if( 0 == $amount && affiliate_wp()->settings->get( 'ignore_zero_referrals' ) ) {
			return false; // Ignore a zero amount referral
		}

		return $amount;

	}

	/**
	 * Mark referrals as complete
	 *
	 * @since 1.0.5
	 */
	public function mark_referrals_complete( $referral_id = 0, $referral = array(), $reference = '' ) {

		if ( empty( $reference ) ) {
			return false;
		}

		$referrals = affwp_mlm_get_referrals_for_order( $reference, $this->context );
		
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
	 * @since 1.0.5
	 */
	public function revoke_referrals_on_refund( $data ) {
	
		if ( empty( $data ) ) {
			return;
		}
		
		if ( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
			return;
		}
		
		$reference = $data['member_id'] . '|' . $data['order_number'] . '-' . $data['order_transaction_id'];

		$referrals = affwp_mlm_get_referrals_for_order( $reference, $this->context );

		if ( empty( $referrals ) ) {
			return;
		}

		foreach ( $referrals as $referral ) {

			$this->reject_referral( $referral );

		}

	}

}
new AffiliateWP_MLM_Membermouse;