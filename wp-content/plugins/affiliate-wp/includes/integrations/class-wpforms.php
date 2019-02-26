<?php

class Affiliate_WP_WPForms extends Affiliate_WP_Base {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   2.0
	*/
	public function init() {

		$this->context = 'wpforms';

		add_action( 'wpforms_process_complete', array( $this, 'add_pending_referral' ), 10, 4 );
		add_action( 'wpforms_paypal_standard_process_complete', array( $this, 'mark_referral_complete' ), 10, 4 );
		add_action( 'wpforms_stripe_process_complete', array( $this, 'mark_referral_complete' ), 10, 4 );
		add_action( 'wpforms_form_settings_general', array( $this, 'add_settings' ) );
		add_filter( 'affwp_referral_reference_column', array( $this, 'reference_link' ), 10, 2 );

	}

	/**
	 * Register the form-specific settings
	 *
	 * @since  2.0
	 * @return void
	 */
	public function add_settings( $instance ) {

		//  Enable affiliate referral creation for this form
		wpforms_panel_field(
			'checkbox',
			'settings',
			'affwp_allow_referrals',
			$instance->form_data,
			__( 'Allow referrals', 'wpforms' )
		);

	}

	/**
	 * Records a pending referral when a pending payment is created
	 *
	 * @access  public
	 * @since   2.0
	*/
	public function add_pending_referral( $fields, $entry, $form_data, $entry_id ) {

		$affiliate_id = $this->affiliate_id;

		// Return if the customer was not referred or the affiliate ID is empty
		if ( ! $this->was_referred() && empty( $affiliate_id ) ) {
			return;
		}

		// prevent referral creation unless referrals enabled for the form
		if ( ! $form_data['settings']['affwp_allow_referrals'] ) {

			$this->log( 'Referral not created because referrals are not enabled onform.' );

			return;
		}

		$customer_email = '';

		// get the customer email
		foreach ( $fields as $field ) {
			if ( $field['type'] === 'email' ) {
				$customer_email = $field['value'];
				break;
			}
		}

		// Customers cannot refer themselves
		if ( $this->is_affiliate_email( $customer_email, $affiliate_id ) ) {

			$this->log( 'Referral not created because affiliate\'s own account was used.' );

			return;
		}

		// get referral total
		$total          = wpforms_get_total_payment( $fields );
		$referral_total = $this->calculate_referral_amount( $total, $entry_id );

		// use form title as description
		$description = $form_data['settings']['form_title'];

		// use products purchased as description
		if ( $this->get_product_description( $fields ) ) {
			$description = $this->get_product_description( $fields );
		}

		// insert a pending referral
		$referral_id = $this->insert_pending_referral( $referral_total, $entry_id, $description );

		// set the referral to "unpaid" if there's no total
		if ( empty( $referral_total ) ) {
			$this->complete_referral( $entry_id );
		}

	}

	/**
	 * Sets a referral to unpaid when payment is completed
	 *
	 * @access  public
	 * @since   2.0
	*/
	public function mark_referral_complete( $fields, $form_data, $entry_id, $data ) {
		$this->complete_referral( $entry_id );
	}

	/**
	 * Sets up the reference link in the Referrals table
	 *
	 * @access  public
	 * @since   2.0
	*/
	public function reference_link( $reference = 0, $referral ) {

		if ( empty( $referral->context ) || 'wpforms' != $referral->context ) {
			return $reference;
		}

		$url = admin_url( 'admin.php?page=wpforms-entries&view=details&entry_id=' . $reference );

		return '<a href="' . esc_url( $url ) . '">' . $reference . '</a>';
	}

	/**
	 * Builds an array of all the products purchased in the form
	 *
	 * @access  public
	 * @since   2.0
	*/
	public function get_product_description( $fields = array() ) {

		$description = array();

		// get the customer email
		foreach ( $fields as $field ) {

			// single items
			if ( $field['type'] === 'payment-single' ) {
				$description[] = $field['name'];
			}

			// multiple items
			if ( $field['type'] === 'payment-multiple' ) {
				$description[] = $field['name'] . ' | ' . $field['value_choice'];
			}

		}

		return implode( ', ', $description );

	}

}

if ( class_exists( 'WPForms' ) ) {
	new Affiliate_WP_WPForms;
}
