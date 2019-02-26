<?php

class AffiliateWP_MLM_S2Member extends AffiliateWP_MLM_Base {

	/**
	 * The transaction data
	 *
	 * @access  public
	 * @since   1.1
	*/
	public $txn_data;

	/**
	 * Get things started
	 *
	 * @access public
	 * @since  1.1
	*/
	public function init() {

		$this->context = 's2member';
		
		/* Check for S2Member */
		$integrations = affiliate_wp()->settings->get( 'affwp_mlm_integrations' );
				
		if ( ! isset( $integrations['s2member'] ) ) return; // MLM integration for S2Member is disabled 

		add_action( 'init', array( $this, 'get_transaction_data' ) );
		add_action( 'init', array( $this, 'revoke_referrals_on_refund' ) );

		// Process referral
		add_action( 'affwp_post_insert_referral', array( $this, 'process_referral' ), 10, 2 );
		
	}

	/**
	 * Get the data from the S2Member transaction
	 *
	 * @since 1.1
	 */
	public function get_transaction_data() {
		
		if( ! empty( $_REQUEST['s2member_affiliatewp_notify'] ) && 'payment' === $_REQUEST['s2member_affiliatewp_notify'] ) {
		
			$integrations = affiliate_wp()->settings->get( 'affwp_mlm_integrations' );
			
			if ( ! $integrations[$this->context] ) return; // Indirect Referrals are disabled for this integration 
			
			// Sanitize submitted form data
			$affiliate_id 	= (int) $_REQUEST['affiliate_id'];
			$user_id 		= (int) $_REQUEST['user_id'];
			$amount 		= affwp_sanitize_amount( $_REQUEST['amount'] );
			$txn_id 		= sanitize_text_field( $_REQUEST['txn_id'] );
			$item_name 		= sanitize_text_field( $_REQUEST['item_name'] );
			
			// Store the data for later
			$this->txn_data = array(
				'affiliate_id' => $affiliate_id,
				'user_id'      => $user_id,
				'amount'       => $amount,
				'txn_id'       => $txn_id,
				'item_name'    => $item_name
			);
		
		}
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

		// Process order and get amount
		$amount = $this->process_order( $parent_affiliate_id, $data, $level_count );
		
		$txn_data = $this->txn_data;
		$item_name = $txn_data['item_name'];
		
		$data['affiliate_id'] = $parent_affiliate_id;
		$data['description']  = $direct_affiliate . ' | Level '. $level_count . ' | ' . $item_name;
		$data['amount']       = $amount;
		$data['custom']       = 'indirect'; // Add referral type as custom referral data
		$data['context']      = 's2member';

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

			$referral = affiliate_wp()->referrals->get_by( 'referral_id', $referral_id, $this->context );
			$this->complete_referral( $referral, $this->context );			
		}

	}

	/**
	 * Process order
	 *
	 * @since 1.1
	 */
	public function process_order( $parent_affiliate_id, $data, $level_count = 0 ) {

		$txn_id = $data['reference'];
		$txn_data = $this->txn_data;
		$amount = $txn_data['amount'];

		if ( 0 == $amount && affiliate_wp()->settings->get( 'ignore_zero_referrals' ) ) {
			return false; // Ignore a zero amount referral
		}

		$product_id = ''; // Leave empty until this integration supports per-product rates
		$referral_total = $this->calculate_referral_amount( $parent_affiliate_id, $amount, $txn_id, $product_id, $level_count );

		return $referral_total;

	}

	/**
	 * Mark referrals as complete
	 *
	 * @since 1.1
	 */
	public function mark_referrals_complete( $txn_id = 0 ) {

		if ( empty( $txn_id ) ) {
			return false;
		}

		$reference = $txn_id;
		$referrals = affwp_mlm_get_referrals_for_order( $txn_id, $this->context );
		
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
	public function revoke_referrals_on_refund() {

		if( ! empty( $_REQUEST['s2member_affiliatewp_notify'] ) && $_REQUEST['s2member_affiliatewp_notify'] === 'refund' ) {
				
			$txn_id = $_REQUEST['txn_id'];
			
			if ( empty( $txn_id ) ) {
				return;
			}
			
			if ( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
				return;
			}
	
			$referrals = affwp_mlm_get_referrals_for_order( $txn_id, $this->context );
	
			if ( empty( $referrals ) ) {
				return;
			}
	
			foreach ( $referrals as $referral ) {
	
				$this->reject_referral( $referral );
	
			}
			exit;
		}
	}

}
new AffiliateWP_MLM_S2Member;