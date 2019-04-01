<?php

class Affiliate_WP_Lifetime_Commissions_Gravity_Forms extends Affiliate_WP_Lifetime_Commissions_Base {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function init() {
		$this->context = 'gravityforms';
	}

	/**
	 * Retrieve the email address of a customer from the from data
	 *
	 * @access  public
	 * @since   2.0
	 * @return  string
	 */
	public function get_email( $reference = 0 ) {

		if( empty( $reference ) ) {
			return false;
		}

		$entry = GFFormsModel::get_lead( $reference );

		if( empty( $entry ) ) {
			return false;
		}

		$form = GFAPI::get_form( $entry['form_id'] );

		// get email field
		$email_fields = GFCommon::get_email_fields( $form );

		$field_id = '';

		// get value of first email field. The form should only have 1 email field if it's a product form
		if ( $email_fields ) {
			foreach ( $email_fields as $email_field ) {
				$field_id = $email_field['id'];
				break;
			}
		}

		return isset( $entry[$field_id] ) ? $entry[$field_id] : false;
	
	}

}
new Affiliate_WP_Lifetime_Commissions_Gravity_Forms;
