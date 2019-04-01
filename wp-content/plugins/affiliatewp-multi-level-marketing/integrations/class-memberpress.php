<?php

class AffiliateWP_MLM_MemberPress extends AffiliateWP_MLM_Base {
	
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
	 * @access  public
	 * @since   1.0.1
	*/
	public function init() {

		$this->context = 'memberpress';
		
		/* Check for Memberpress */
		$integrations = affiliate_wp()->settings->get( 'affwp_mlm_integrations' );
				
		if ( ! isset( $integrations['memberpress'] ) ) return; // MLM integration for Memberpress is disabled 

		add_action( 'mepr-txn-status-pending', array( $this, 'store_transaction' ), 10, 1 );
		add_action( 'mepr-txn-status-complete', array( $this, 'mark_referrals_complete' ), 10 );
		add_action( 'mepr-txn-status-refunded', array( $this, 'revoke_referrals_on_refund' ), 10 );
		
		// Process referral
		add_action( 'affwp_post_insert_referral', array( $this, 'process_referral' ), 10, 2 );

	}

	public function store_transaction( $txn ){
	
		$this->order = $txn;
	
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
	 * Creates the referral for the parent affiliate
	 *
	 * @since 1.0.1
	 */
	public function create_parent_referral( $parent_affiliate_id, $referral_id, $data, $level_count = 0, $affiliate_id ) {

		$direct_affiliate = affiliate_wp()->affiliates->get_affiliate_name( $affiliate_id );

		// Get amount
		$amount = $this->process_order( $parent_affiliate_id, $data, $level_count );
		$product_name = $data['description'];

		$data['affiliate_id'] = $parent_affiliate_id;
		$data['description']  = $direct_affiliate . ' | Level '. $level_count . ' | ' . $product_name;
		$data['amount']       = $amount;
		$data['custom']       = 'indirect'; // Add referral type as custom referral data
		$data['context']      = 'memberpress';

		unset( $data['date'] );
		unset( $data['currency'] );
		unset( $data['status'] );

		if ( ! (bool) apply_filters( 'affwp_mlm_create_indirect_referral', true, $data, $parent_affiliate_id, $affiliate_id, $referral_id, $level_count ) ) {
			return false; // Allow extensions to prevent indirect referrals from being created
		}
		
		// Create referral
		$referral_id = affiliate_wp()->referrals->add( apply_filters( 'affwp_mlm_insert_pending_referral', $data, $parent_affiliate_id, $affiliate_id, $referral_id, $level_count ) );

		if ( $referral_id ) {

			do_action( 'affwp_mlm_indirect_referral_created', $referral_id, $data );

		}
	}

	/**
	 * Process order
	 *
	 * @since 1.0.1
	 */
	public function process_order( $parent_affiliate_id, $data, $level_count = 0 ) {
		
		$order_id = $data['reference'];
		
		$txn = apply_filters( 'affwp_get_mepr_order', $this->order );
		$product_id = $txn->product_id;
		$base_amount = $txn->amount;
		$reference = $txn->id;

		$referral_total = $this->calculate_referral_amount( $parent_affiliate_id, $base_amount, $reference, $product_id, $level_count );

		if ( 0 == $referral_total && affiliate_wp()->settings->get( 'ignore_zero_referrals' ) ) {
			return false; // Ignore a zero amount referral
		}
		
		return $referral_total;
		
	}

	/**
	 * Mark referrals as complete
	 *
	 * @since 1.0.1
	 */
	public function mark_referrals_complete( $txn ) {

		$reference = $txn->id;
		$referrals = affwp_mlm_get_referrals_for_order( $reference, $this->context );

		if ( empty( $referrals ) ) {
			return false;
		}

		foreach ( $referrals as $referral ) {
		
			$this->complete_referral( $referral, $reference );
			
		}

	}

	/**
	 * Revoke referrals when a transaction is refunded
	 *
	 * @since 1.0.1
	 */
	public function revoke_referrals_on_refund( $txn ) {

		if( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
			return;
		}

		$reference = $txn->id;
		$referrals = affwp_mlm_get_referrals_for_order( $reference, $this->context );

		if ( empty( $referrals ) ) {
			return false;
		}

		foreach ( $referrals as $referral ) {

			$this->reject_referral( $referral );

		}

	}

}
new AffiliateWP_MLM_MemberPress;