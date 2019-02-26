<?php

class Affiliate_WP_Sprout_Invoices extends Affiliate_WP_Base {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.6
	 */
	public function init() {
		$this->context = 'sproutinvoices';

		add_action( 'payment_authorized', array( $this, 'add_pending_referral' ) );
		add_action( 'payment_complete', array( $this, 'mark_referral_complete' ) );
		add_action( 'si_void_payment', array( $this, 'revoke_referral_on_refund' ) );

		add_filter( 'affwp_referral_reference_column', array( $this, 'reference_link' ), 10, 2 );

	}

	/**
	 * Record a pending referral when a payment is authorized
	 *
	 * @access  public
	 * @since   1.6
	 */
	public function add_pending_referral( SI_Payment $payment ) {

		if( $this->was_referred() ) {
			$payment_id = $payment->get_id();
			$referral_total = $this->calculate_referral_amount( $payment->get_amount(), $payment_id );
			$this->insert_pending_referral( $referral_total, $payment_id, $payment->get_title() );
		}

	}

	/**
	 * Update a referral to Unpaid when a payment is completed
	 *
	 * @access  public
	 * @since   1.6
	 */
	public function mark_referral_complete( SI_Payment $payment ) {
		$payment_id = $payment->get_id();
		$this->complete_referral( $payment_id );
		$referral = affiliate_wp()->referrals->get_by( 'reference', $payment_id, $this->context );
		if ( !is_object( $referral ) ) {
			return;
		}
		$amount   = affwp_currency_filter( affwp_format_amount( $referral->amount ) );
		$name     = affiliate_wp()->affiliates->get_affiliate_name( $referral->affiliate_id );
		$note     = sprintf( __( 'Referral #%d for %s recorded for %s', 'affiliate-wp' ), $referral->referral_id, $amount, $name );

		$new_data = wp_parse_args( $payment->get_data(), array( 'affwp_notes' => $note ) );
		$payment->set_data( $new_data );
	}

	/**
	 * Revoke a referral when a payment is refunded
	 *
	 * @access  public
	 * @since   1.6
	 */
	public function revoke_referral_on_refund( $payment_id = 0 ) {

		if( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
			return;
		}

		$this->reject_referral( $payment_id );

		$referral = affiliate_wp()->referrals->get_by( 'reference', $payment_id, $this->context );
		$amount   = affwp_currency_filter( affwp_format_amount( $referral->amount ) );
		$name     = affiliate_wp()->affiliates->get_affiliate_name( $referral->affiliate_id );
		$note     = sprintf( __( 'Referral #%d for %s for %s rejected', 'affiliate-wp' ), $referral->referral_id, $amount, $name );

		$payment  = SI_Payment::get_instance( $payment_id );
		$new_data = wp_parse_args( $payment->get_data(), array( 'affwp_notes' => $note ) );
		$payment->set_data( $new_data );
	}

	/**
	 * Setup the reference link in the referrals table
	 *
	 * @access  public
	 * @since   1.6
	 */
	public function reference_link( $reference = 0, $referral ) {

		if( empty( $referral->context ) || $this->context != $referral->context ) {
			return $reference;
		}

		$payment = SI_Payment::get_instance( $reference );
		$invoice_id = $payment->get_invoice_id();
		$url = get_edit_post_link( $invoice_id );
		$reference = get_the_title( $invoice_id );

		return '<a href="' . esc_url( $url ) . '">' . $reference . '</a>';
	}

}

if ( class_exists( 'SI_Payment' ) ) {
	new Affiliate_WP_Sprout_Invoices;
}
