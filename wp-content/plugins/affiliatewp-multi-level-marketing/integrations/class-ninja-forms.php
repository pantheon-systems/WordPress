<?php

class AffiliateWP_MLM_Ninja_Forms extends AffiliateWP_MLM_Base {

	/**
	 * Get things started
	 *
	 * @access public
	 * @since  1.1
	*/
	public function init() {

		$this->context = 'ninja-forms';
		
		/* Check for Ninja Forms */
		$integrations = affiliate_wp()->settings->get( 'affwp_mlm_integrations' );
				
		if ( ! isset( $integrations['ninja-forms'] ) ) return; // MLM integration for Ninja Forms is disabled 

		add_action( 'delete_post', array( $this, 'revoke_referrals_on_delete' ) );
		add_action( 'wp_trash_post', array( $this, 'revoke_referrals_on_delete' ) );
		add_action( 'untrash_post', array( $this, 'restore_referrals' ) );
		
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
	 * Creates the referral for the parent affiliate
	 *
	 * @since 1.1
	 */
	public function create_parent_referral( $parent_affiliate_id, $referral_id, $data, $level_count = 0, $affiliate_id ) {

		$direct_affiliate = affiliate_wp()->affiliates->get_affiliate_name( $affiliate_id );

		// Get amount
		$amount = $this->process_order( $parent_affiliate_id, $data, $level_count );
		$form = $data['description'];

		$data['affiliate_id'] = $parent_affiliate_id;
		$data['description']  = $direct_affiliate . ' | Level '. $level_count . ' | ' . $form;
		$data['amount']       = $amount;
		$data['custom']       = 'indirect'; // Add referral type as custom referral data
		$data['context']      = 'ninja-forms';

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
		
		global $ninja_forms_processing;
		
		$sub_id = $data['reference'];
		$total = $ninja_forms_processing->get_calc_total();
		
		if ( is_array ( $total ) ) {
			// If this is an array, grab the string total.
			if ( isset ( $total['total'] ) ) {
				$purchase_total = $total['total'];
			} else {
				$purchase_total = '';
			}
		} else {
			// This isn't an array, so $purchase_total can just be set to the string value.
			if ( ! empty( $total ) ) {
				$purchase_total = $total;
			} else {
				$purchase_total = 0.00;
			}
		}
		
		$total = affwp_sanitize_amount( $purchase_total );
		$base_amount = $total;
		$reference = $sub_id;
		$product_id = $ninja_forms_processing->get_form_ID();
		
		$referral_total = $this->calculate_referral_amount( $parent_affiliate_id, $base_amount, $reference, $product_id, $level_count );

		if ( 0 == $referral_total && affiliate_wp()->settings->get( 'ignore_zero_referrals' ) ) {
			return false; // Ignore a zero amount referral
		}
		
		return $referral_total;
		
	}

	/**
	 * Mark referrals as complete
	 *
	 * @since 1.1
	 */
	public function mark_referrals_complete( $sub_id = 0 ) {

		if( 'nf_sub' != get_post_type( $sub_id ) ) {
			return;
		}
		
		$reference = $sub_id;
		$referrals = affwp_mlm_get_referrals_for_order( $reference, $this->context );

		if ( empty( $referrals ) ) {
			return false;
		}

		foreach ( $referrals as $referral ) {
		
			$this->complete_referral( $referral, $reference );
		
		}

	}

	/**
	 * Revoke referrals on deleted submissions
	 *
	 * @since 1.1
	 */
	public function revoke_referrals_on_delete( $sub_id = 0 ) {


		if( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
			return;
		}
		
		if( 'nf_sub' != get_post_type( $sub_id ) ) {
			return;
		}
		
		$reference = $sub_id;
		$referrals = affwp_mlm_get_referrals_for_order( $reference, $this->context );

		if ( empty( $referrals ) ) {
			return false;
		}

		foreach ( $referrals as $referral ) {
		
			$this->reject_referral( $referral );
		
		}

	}


	/**
	 * Restore rejected referrals when untrashing a submission
	 *
	 * @since   1.1
	 */
	public function restore_referrals( $sub_id = 0 ) {
		
		if( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
			return;
		}
		if( 'nf_sub' != get_post_type( $sub_id ) ) {
			return;
		}

		$reference = $sub_id;
		$referrals = affwp_mlm_get_referrals_for_order( $reference, $this->context );

		if ( empty( $referrals ) ) {
			return false;
		}

		foreach ( $referrals as $referral ) {
			
			affwp_set_referral_status( $referral->referral_id, 'unpaid' );
		
		}

	}

}
new AffiliateWP_MLM_Ninja_Forms;