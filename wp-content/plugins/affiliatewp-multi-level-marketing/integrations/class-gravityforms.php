<?php

class AffiliateWP_MLM_Gravity_Forms extends AffiliateWP_MLM_Base {

	/**
	 * The order total
	 *
	 * @since 1.6.1
	 */
	public $total;
	
	/**
	 * Get things started
	 *
	 * @access public
	 * @since  1.0
	*/
	public function init() {

		$this->context = 'gravityforms';
		
		/* Check for Gravity Forms */
		$integrations = affiliate_wp()->settings->get( 'affwp_mlm_integrations' );
				
		if ( ! isset( $integrations['gravityforms'] ) ) return; // MLM integration for Gravity Forms is disabled 
		
		add_action( 'gform_post_payment_completed', array( $this, 'mark_referrals_complete' ), 10, 2 );
		add_action( 'gform_post_payment_refunded', array( $this, 'revoke_referrals_on_refund' ), 10, 2);
		
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
	 * @since 1.0
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
		$data['context']      = 'gravityforms';

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
			
			if ( empty( $this->total ) ) {

				$referral = affiliate_wp()->referrals->get_by( 'referral_id', $referral_id, $this->context );
				$this->complete_referral( $referral, $this->context );
			}
		}
	}

	/**
	 * Process order
	 *
	 * @since 1.0
	 */
	public function process_order( $parent_affiliate_id, $data, $level_count = 0 ) {
		
		$entry_id = $data['reference'];
		
		// Get entry object by entry id
		$entry = apply_filters( 'affwp_get_gravityforms_order', GFFormsModel::get_lead( $entry_id ) );
		
		$form = GFAPI::get_form( $entry['form_id'] ); 
		$products = GFCommon::get_product_fields( $form, $entry );
		$total = 0;
		
		foreach ( $products['products'] as $key => $product ) {	

			$price = GFCommon::to_number( $product['price'] );
			
			if ( is_array( rgar( $product,'options' ) ) ) {
				$count = sizeof( $product['options'] );
				$index = 1;
				foreach ( $product['options'] as $option ) {
					$price += GFCommon::to_number( $option['price'] );
				}
			}
			
			$subtotal = floatval( $product['quantity'] ) * $price;
			$total += $subtotal;

		}

		$total += floatval( $products['shipping']['price'] );
		$this->total = $total;
		$base_amount = $total;
		$reference = $entry['id'];
		$product_id = ''; // Leave empty until GF integration supports per-product rates
		
		$referral_total = $this->calculate_referral_amount( $parent_affiliate_id, $base_amount, $reference, $product_id, $level_count );

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
	public function mark_referrals_complete( $entry, $action ) {

		$reference = $entry['id'];
		$referrals = affwp_mlm_get_referrals_for_order( $reference, $this->context );

		if ( empty( $referrals ) ) {
			return false;
		}

		foreach ( $referrals as $referral ) {
		
			$this->complete_referral( $referral, $reference );
			
			$amount   = affwp_currency_filter( affwp_format_amount( $referral->amount ) );
			$name     = affiliate_wp()->affiliates->get_affiliate_name( $referral->affiliate_id );
			$note     = sprintf( __( 'Indirect Referral #%d for %s recorded for %s', 'affiliatewp-multi-level-marketing' ), $referral->referral_id, $amount, $name );
	
			GFFormsModel::add_note( $entry["id"], 0, 'AffiliateWP', $note );
		
		}

	}

	/**
	 * Revoke referrals on refund
	 *
	 * @since 1.0
	 */
	public function revoke_referrals_on_refund( $entry, $action ) {


		if( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
			return;
		}

		$reference = $entry['id'];
		$referrals = affwp_mlm_get_referrals_for_order( $reference, $this->context );

		if ( empty( $referrals ) ) {
			return false;
		}

		foreach ( $referrals as $referral ) {
		
			$this->reject_referral( $referral );
			
			$amount   = affwp_currency_filter( affwp_format_amount( $referral->amount ) );
			$name     = affiliate_wp()->affiliates->get_affiliate_name( $referral->affiliate_id );
			$note     = sprintf( __( 'Referral #%d for %s for %s rejected', 'affiliate-wp' ), $referral->referral_id, $amount, $name );

			GFFormsModel::add_note( $entry["id"], 0, 'AffiliateWP', $note );
		
		}

	}

}
new AffiliateWP_MLM_Gravity_Forms;