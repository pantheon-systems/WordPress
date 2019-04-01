<?php

class Affiliate_WP_Invoice extends Affiliate_WP_Base {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.7.5
	*/
	public function init() {

		$this->context = 'wp-invoice';

		add_action( 'wpi_successful_payment', array( $this, 'track_successful_payment' ) );
		add_action( 'wpi_object_updated', array( $this, 'track_refund' ), 10, 2 );
	}

	/**
	 * Track Successful Payment
	 * @param $invoice
	 * @since 1.7.5
	 */
	public function track_successful_payment( $invoice ) {

		if( $this->was_referred() ) {

			$new_invoice = new WPI_Invoice();
			$new_invoice->load_invoice("id={$invoice->data['invoice_id']}");

			$this->insert_pending_referral(
				$new_invoice->data['total_payments'] ? $new_invoice->data['total_payments'] : $new_invoice->data['net'],
				$new_invoice->data['invoice_id'],
				$new_invoice->data['post_title']
			);

			if ( $new_invoice->data['post_status'] == 'paid' ) {
				$this->complete_referral( $new_invoice->data['invoice_id'] );
			}

		}
	}

	/**
	 * Handle refunds
	 * @param $old_invoice
	 * @param $new_post
	 * @since 1.7.5
	 */
	public function track_refund( $old_invoice, $new_post ) {

		if ( $new_post['post_status'] !== 'refund' ) {
			return;
		}

		if( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
			return;
		}

		$this->reject_referral( $old_invoice['invoice_id'] );

	}

}

if ( class_exists( 'WPI_Invoice' ) ) {
	new Affiliate_WP_Invoice;
}
