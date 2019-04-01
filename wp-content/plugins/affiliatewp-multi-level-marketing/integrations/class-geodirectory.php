<?php

class AffiliateWP_MLM_GeoDirectory extends AffiliateWP_MLM_Base {

	/**
	 * Array of payment completed status.
	 *
	 * @access  public
	 * @since   1.1.2
	 */
	public $paid_status;
	
	/**
	 * Gets things started.
	 *
	 * @access  public
	 * @since   1.1.2
	 * @return  void
	 */
	public function init() {
		
		$this->context = 'geodirectory';
		$this->paid_status = array( 'paid', 'active', 'subscription-payment', 'confirmed' );
		
		/* Check for GeoDirectory */
		$integrations = affiliate_wp()->settings->get( 'affwp_mlm_integrations' );
				
		if ( ! isset( $integrations['geodirectory'] ) ) return; // MLM integration for GeoDirectory is disabled 
		
		add_action( 'geodir_payment_invoice_status_changed', array( $this, 'mark_referrals_complete' ), 10 );
		add_action( 'geodir_payment_invoice_status_changed', array( $this, 'revoke_referrals_on_refund' ), 10 );

		// Process referral
		add_action( 'affwp_post_insert_referral', array( $this, 'process_referral' ), 10, 2 );		
		
	}

	/**
	 * Process referral
	 *
	 * @since 1.1.2
	 */
	public function process_referral( $referral_id, $data ) {
		
		$this->prepare_indirect_referrals( $referral_id, $data );

	}

	/**
	 * Creates the referral for parent affiliate
	 *
	 * @since 1.1.2
	 */
	public function create_parent_referral( $parent_affiliate_id, $referral_id, $data, $level_count = 0, $affiliate_id ) {

		$direct_affiliate = affiliate_wp()->affiliates->get_affiliate_name( $affiliate_id );
		
		// Process cart and get amount
		$amount = $this->process_order( $parent_affiliate_id, $data, $level_count );
		
		// Get invoice object by invoice id
		$invoice = geodir_get_invoice( $data['reference'] );
		
		$data['affiliate_id'] = $parent_affiliate_id;
		$data['description']  = $direct_affiliate . ' | Level '. $level_count . ' | GeoDirectory: ' . $invoice->package_title;
		$data['amount']       = $amount;
		$data['custom']       = 'indirect'; // Add referral type as custom referral data
		$data['context']      = 'geodirectory';

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
			
			if ( in_array( geodir_strtolower( $invoice->status ), $this->paid_status ) ) {
				// Add referral
				$this->complete_referral( $data['reference'] );
			}

		}

	}

	/**
	 * Process order
	 *
	 * @since 1.1.2
	 */
	public function process_order( $parent_affiliate_id, $data, $level_count = 0 ) {
		
		$invoice_id = $data['reference'];
		
		// Get invoice object by invoice id
		$invoice = geodir_get_invoice( $invoice_id );
		apply_filters( 'affwp_get_geodirectory_order', $invoice );
		
		$package_id = $invoice->package_id;
		$reference = $invoice->id;
		$base_amount = (float)$invoice->paied_amount;
		
		if ( affiliate_wp()->settings->get( 'exclude_tax' ) ) {

			/* VAT Tax Settings - Custom GD Invoicing add-on
			global $wpdb;
			$vat_sql = "SELECT * FROM `wp_global_vat_settings`";
			$vat_result = $wpdb->get_row( $vat_sql );
			$vat_calc = $vat_result->calc_taxable; // 'add' (Add tax to Listing Price) OR 'subtract' (Subtract from Listing Price)
			$tax_value = $vat_result->tax_value;
			*/

			$tax_amount = $invoice->tax_amount;
			$base_amount = $base_amount - $tax_amount;

		}
			
		$referral_total = $this->calculate_referral_amount( $parent_affiliate_id, $base_amount, $reference, $package_id, $level_count );

		if ( 0 == $referral_total && affiliate_wp()->settings->get( 'ignore_zero_referrals' ) ) {
			return false; // Ignore a zero amount referral
		}
		
		return $referral_total;
		
	}

	/**
	 * Mark referrals as complete
	 *
	 * @since 1.1.2
	 */
	public function mark_referrals_complete( $invoice_id = 0, $status = '' ) {

		if ( !(int)$invoice_id > 0 ) {
			return; // Invalid invoice id
		}
			
		$invoice = geodir_get_invoice( $invoice_id );

		if ( empty( $invoice ) ) {
			return; // Invalid invoice
		}
		
		$payment_status = geodir_strtolower( $invoice->status );
		
		if ( $status != '' && $status == $payment_status ) {
			return; // No status change
		}
		
		if ( in_array( $payment_status, $this->paid_status ) ) {

			$reference = $invoice_id;
			$referrals = affwp_mlm_get_referrals_for_order( $invoice_id, $this->context );

			if ( empty( $referrals ) ) {
				return;
			}

			foreach ( $referrals as $referral ) {

				$this->complete_referral( $referral, $reference );

			}
		}
	}

	/**
	 * Revoke referrals when an order is refunded
	 *
	 * @since 1.1.2
	 */
	public function revoke_referrals_on_refund( $invoice_id = 0, $status = '' ) {

		if ( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
			return;
		}		
		
		if ( !(int)$invoice_id > 0 ) {
			return; // Invalid invoice id
		}
			
		$invoice = geodir_get_invoice( $invoice_id );
		
		if ( empty( $invoice ) ) {
			return; // Invalid invoice
		}
		
		$payment_status = geodir_strtolower( $invoice->status );
		
		if ( !in_array( $payment_status, $this->paid_status ) ) {
			
			$referrals = affwp_mlm_get_referrals_for_order( $invoice_id, $this->context );

			if ( empty( $referrals ) ) {
				return;
			}

			foreach ( $referrals as $referral ) {

				$this->reject_referral( $referral );

			}
		}
	}
}
new AffiliateWP_MLM_GeoDirectory;
