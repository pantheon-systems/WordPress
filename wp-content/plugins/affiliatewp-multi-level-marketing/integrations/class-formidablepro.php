<?php

class AffiliateWP_MLM_Formidable_Pro extends AffiliateWP_MLM_Base {

	/**
	 * The referral total
	 *
	 * @since 1.1
	 */
	public $referral_total;
	
	/**
	 * Get things started
	 *
	 * @access public
	 * @since  1.1
	*/
	public function init() {

		$this->context = 'formidablepro';
		
		/* Check for Formidable Forms */
		$integrations = affiliate_wp()->settings->get( 'affwp_mlm_integrations' );
				
		if ( ! isset( $integrations['formidablepro'] ) ) return; // MLM integration for Formidable Forms is disabled 

		add_action( 'frm_after_payment_completed', array( $this, 'mark_referrals_complete' ), 10, 2 );
		add_action( 'frm_after_payment_refunded', array( $this, 'revoke_referrals_on_refund' ), 10, 2 );

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
		$product = $data['description'];

		$data['affiliate_id'] = $parent_affiliate_id;
		$data['description']  = $direct_affiliate . ' | Level '. $level_count . ' | ' . $product;
		$data['amount']       = $amount;
		$data['custom']       = 'indirect'; // Add referral type as custom referral data
		$data['context']      = 'formidablepro';

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
			
			if ( empty( $this->referral_total ) ) {

				$referral = affiliate_wp()->referrals->get_by( 'referral_id', $referral_id, $this->context );
				$this->complete_referral( $referral, $this->context );
			}
		}
	}

	/**
	 * Process order
	 *
	 * @since 1.1
	 */
	public function process_order( $parent_affiliate_id, $data, $level_count = 0 ) {
		
		global $frm_entry_meta, $frm_form;
		
		$entry_id = $data['reference'];
		
		// Get form id by entry id
		$entry = FrmEntry::getOne( $entry_id );
		$form_id = $entry->form_id;
		
		// Get form object by form id
		$form = $frm_form->getOne( $form_id );
		$purchase_amount = floatval( $frm_entry_meta->get_entry_meta_by_field( $entry_id, $form->options['affiliatewp']['purchase_amount_field'] ) );
		$base_amount = $purchase_amount;
		$reference = $entry_id;
		$product_id = ''; // Leave empty until Formidable Pro integration supports per-product rates
		
		$referral_total = $this->calculate_referral_amount( $parent_affiliate_id, $base_amount, $reference, $product_id, $level_count );
		$this->referral_total = $referral_total;
		
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
	public function mark_referrals_complete( $entry_id, $form_id ) {

		global $frm_entry_meta;
		
		$reference = $entry_id;
		$referrals = affwp_mlm_get_referrals_for_order( $reference, $this->context );

		if ( empty( $referrals ) ) {
			return false;
		}
		
		foreach ( $referrals as $referral ) {
		
			$this->complete_referral( $referral, $reference );
			
			$amount   = affwp_currency_filter( affwp_format_amount( $referral->amount ) );
			$name     = affiliate_wp()->affiliates->get_affiliate_name( $referral->affiliate_id );
			$note     = sprintf( __( 'AffiliateWP: Indirect Referral #%d for %s recorded for %s', 'affiliatewp-multi-level-marketing' ), $referral->referral_id, $amount, $name );
			$frm_entry_meta->add_entry_meta( $entry_id, 0, '', array( 'comment' => $note, 'user_id' => 0 ) );
		
		}

	}

	/**
	 * Revoke referrals on refund
	 *
	 * @since 1.1
	 */
	public function revoke_referrals_on_refund( $entry_id, $form_id ) {


		if( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
			return;
		}
		
		global $frm_entry_meta;
		
		$reference = $entry_id;
		$referrals = affwp_mlm_get_referrals_for_order( $reference, $this->context );

		if ( empty( $referrals ) ) {
			return false;
		}

		foreach ( $referrals as $referral ) {
		
			$this->reject_referral( $referral );
			
			$amount   = affwp_currency_filter( affwp_format_amount( $referral->amount ) );
			$name     = affiliate_wp()->affiliates->get_affiliate_name( $referral->affiliate_id );		
			$note     = sprintf( __( 'AffiliateWP: Referral #%d for %s for %s rejected', 'affiliate-wp' ), $referral->referral_id, $amount, $name );
			$frm_entry_meta->add_entry_meta( $entry_id, 0, '', array( 'comment' => $note, 'user_id' => 0 ) );
		
		}

	}

}
new AffiliateWP_MLM_Formidable_Pro;