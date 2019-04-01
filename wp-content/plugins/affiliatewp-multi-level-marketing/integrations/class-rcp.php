<?php

class AffiliateWP_MLM_RCP extends AffiliateWP_MLM_Base {

	/**
	 * The post data
	 *
	 * @access  public
	 * @since   1.1
	*/
	public $post_data;
	
	/**
	 * The user id
	 *
	 * @access  public
	 * @since   1.1
	*/
	public $user_id;

	/**
	 * The price
	 *
	 * @access  public
	 * @since   1.1
	*/
	public $price;

	/**
	 * Get things started
	 *
	 * @access public
	 * @since  1.1
	*/
	public function init() {

		$this->context = 'rcp';
		
		/* Check for Restrict Content Pro */
		$integrations = affiliate_wp()->settings->get( 'affwp_mlm_integrations' );
				
		if ( ! isset( $integrations['rcp'] ) ) return; // MLM integration for Restrict Content Pro is disabled 
		
		// Hook in before add_pending_referral() runs to save the data
		add_action( 'rcp_form_processing', array( $this, 'get_order_data' ), -1, 3 );
		add_action( 'rcp_insert_payment', array( $this, 'mark_referrals_complete' ), 10, 3 );
		add_action( 'rcp_delete_payment', array( $this, 'revoke_referrals_on_delete' ), 10 );

		// Process referral
		add_action( 'affwp_post_insert_referral', array( $this, 'process_referral' ), 10, 2 );
		
	}

	/**
	 * Get the order data
	 *
	 * @since 1.1
	 */
	public function get_order_data( $post_data, $user_id, $price ) {

		// Store the data for later
		$this->post_data = $post_data;
		$this->user_id = $user_id;
		$this->price = $price;
		
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
		$user_id = $this->user_id;

		// Process order and get amount
		$amount = $this->process_order( $parent_affiliate_id, $data, $level_count );

		$data['affiliate_id'] = $parent_affiliate_id;
		$data['description']  =  $direct_affiliate . ' | Level '. $level_count . ' | ' . rcp_get_subscription( $user_id );
		$data['amount']       = $amount;
		$data['custom']       = 'indirect'; // Add referral type as custom referral data
		$data['context']      = 'rcp';

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
	 * Process the order
	 *
	 * @since 1.1
	 */
	public function process_order( $parent_affiliate_id, $data, $level_count = 0 ) {

		$key = $data['reference'];
		$post = $this->post_data;
		$user_id = $this->user_id;
		$price = $this->price;
			
		$amount = $this->calculate_referral_amount( $parent_affiliate_id, $price, $key, absint( $post['rcp_level'] ), $level_count );

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
	public function mark_referrals_complete( $payment_id, $args, $amount ) {

		if ( empty( $args ) ) {
			return false;
		}

		$reference = $args['subscription_key'];
		$referrals = affwp_mlm_get_referrals_for_order( $reference, $this->context );
		
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
	public function revoke_referrals_on_delete( $payment_id = 0 ) {
	
		if ( empty( $payment_id ) ) {
			return;
		}
		
		if ( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
			return;
		}
		
		$payments = new RCP_Payments;
		$payment  = $payments->get_payment( $payment_id );
		$referrals = affwp_mlm_get_referrals_for_order( $payment->subscription_key, $this->context );

		if ( empty( $referrals ) ) {
			return;
		}

		foreach ( $referrals as $referral ) {

			$this->reject_referral( $referral );

		}

	}

}
new AffiliateWP_MLM_RCP;