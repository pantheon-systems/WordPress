<?php

class AffiliateWP_MLM_PMP extends AffiliateWP_MLM_Base {

	/**
	 * The order object
	 *
	 * @access  private
	 * @since   1.1
	*/
	public $order;

	/**
	 * Get things started
	 *
	 * @access public
	 * @since  1.0
	*/
	public function init() {

		$this->context = 'pmp';
		
		/* Check for Paid Memberships Pro */
		$integrations = affiliate_wp()->settings->get( 'affwp_mlm_integrations' );
				
		if ( ! isset( $integrations['pmp'] ) ) return; // MLM integration for Paid Memberships Pro is disabled 

		add_action( 'pmpro_updated_order', array( $this, 'mark_referrals_complete' ), 10 );
		add_action( 'admin_init', array( $this, 'revoke_referrals_on_refund_and_cancel' ), 10);
		add_action( 'pmpro_delete_order', array( $this, 'revoke_referrals_on_delete' ), 10, 2 );
		
		// Process referral
		add_action( 'affwp_post_insert_referral', array( $this, 'process_referral' ), 10, 2 );

		/* Per level referral rates
		add_action( 'pmpro_membership_level_after_other_settings', array( $this, 'affwp_membership_settings' ) );
		add_action( 'pmpro_save_membership_level', array( $this, 'save_membership_settings' ) );
		add_action( 'pmpro_delete_membership_level', array( $this, 'delete_membership_settings' ), 10, 1 );
		*/
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
	 * @since 1.0
	 */
	public function create_parent_referral( $parent_affiliate_id, $referral_id, $data, $level_count = 0, $affiliate_id ) {

		$direct_affiliate = affiliate_wp()->affiliates->get_affiliate_name( $affiliate_id );

		// Get amount
		$amount = $this->process_order( $parent_affiliate_id, $data, $level_count );
		$membership = $data['description'];

		$data['affiliate_id'] = $parent_affiliate_id;
		$data['description']  = $direct_affiliate . ' | Level '. $level_count . ' | ' . $membership;
		$data['amount']       = $amount;
		$data['custom']       = 'indirect'; // Add referral type as custom referral data
		$data['context']      = 'pmp';

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
	 * @since 1.0
	 */
	public function process_order( $parent_affiliate_id, $data, $level_count = 0 ) {
		
		$order_id = $data['reference'];
		
		// Get order object by order id
		$morder = new MemberOrder();
		apply_filters( 'affwp_get_pmp_order', $morder->getMemberOrderByID( $order_id ), $morder );
		
		$membership_level = $morder->membership_id;
		$reference = $morder->id;
		$base_amount = $morder->subtotal;

		if ( get_option( "affwp_pmp_membership_referrals_disabled_" . $membership_level ) ) {
			return; // Referrals are disabled for this membership
		}
			
		$referral_total = $this->calculate_referral_amount( $parent_affiliate_id, $base_amount, $reference, $membership_level, $level_count );

		if ( 0 == $referral_total && affiliate_wp()->settings->get( 'ignore_zero_referrals' ) ) {
			return false; // Ignore a zero amount referral
		}
		
		return $referral_total;
		
	}

	/**
	 * Mark referrals as complete
	 *
	 * @since 1.0
	 */
	public function mark_referrals_complete( $order ) {

		if ( 'success' !== strtolower( $order->status ) ) {
			return;
		}
		
		$reference = $order->id;
		$referrals = affwp_mlm_get_referrals_for_order( $reference, $this->context );

		if ( empty( $referrals ) ) {
			return false;
		}

		foreach ( $referrals as $referral ) {
		
			$this->complete_referral( $referral, $reference );
			
			$order = new MemberOrder( $order->id );
			
			// Prevent infinite loop
			remove_action( 'pmpro_updated_order', array( $this, 'mark_referrals_complete' ), 10 );

			$amount              = html_entity_decode( affwp_currency_filter( affwp_format_amount( $referral->amount ) ), ENT_QUOTES, 'UTF-8' );
			$name                = affiliate_wp()->affiliates->get_affiliate_name( $referral->affiliate_id );
			$note                = sprintf( __( 'Indirect Referral #%d for %s recorded for %s', 'affiliatewp-multi-level-marketing' ), $referral->referral_id, $amount, $name );
			
			if ( empty( $order->notes ) ) {
				$order->notes = $note;
			} else {
				$order->notes = $order->notes . "\n\n" . $note;
			}
			$order->saveOrder();
			
		}
	}

	/**
	 * Revoke referrals when an order is refunded
	 *
	 * @since 1.0
	 */
	public function revoke_referrals_on_refund_and_cancel() {
		/*
		 * PMP does not have hooks for when an order is refunded or voided, so we detect the form submission manually
		 */

		if( ! isset( $_REQUEST['save'] ) ) {
			return;
		}

		if( ! isset( $_REQUEST['order'] ) ) {
			return;
		}

		if( ! isset( $_REQUEST['status'] ) ) {
			return;
		}

		if( ! isset( $_REQUEST['membership_id'] ) ) {
			return;
		}

		if( 'refunded' != $_REQUEST['status'] ) {
			return;
		}

		if( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
			return;
		}

		$reference = absint( $_REQUEST['order'] );
		$referrals = affwp_mlm_get_referrals_for_order( $reference, $this->context );

		if ( empty( $referrals ) ) {
			return false;
		}

		foreach ( $referrals as $referral ) {

			$this->reject_referral( $referral );

		}

	}

	/**
	 * Revoke referrals when an order is deleted
	 *
	 * @since 1.0
	 */
	public function revoke_referrals_on_delete( $order_id = 0, $order ) {

		if( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
			return;
		}
		
		$reference = $order->id;
		$referrals = affwp_mlm_get_referrals_for_order( $reference, $this->context );

		if ( empty( $referrals ) ) {
			return false;
		}

		foreach ( $referrals as $referral ) {

			$this->reject_referral( $referral );

		}

	}

}
new AffiliateWP_MLM_PMP;