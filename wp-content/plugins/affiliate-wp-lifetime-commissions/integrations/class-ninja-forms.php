<?php

class Affiliate_WP_Lifetime_Commissions_Ninja_Forms extends Affiliate_WP_Lifetime_Commissions_Base {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function init() {
		$this->context = 'ninja-forms';
	}

	/**
	 * Retrieve the email address of a customer from the form data
	 *
	 * @access  public
	 * @since   2.0
	 * @return  string
	 */
	public function get_email( $reference = 0 ) {

		$email = '';

		if ( version_compare( get_option( 'ninja_forms_version', '0.0.0' ), '3', '<' ) || get_option( 'ninja_forms_load_deprecated', false ) ) {

			global $ninja_forms_processing;

			$user_info = $ninja_forms_processing->get_user_info();

			if ( isset( $user_info['billing']['email'] ) ) {

				$email = $user_info['billing']['email'];

			} else {

				$email = $user_info['email'];

			}

		} else {

			$sub = Ninja_Forms()->form()->get_sub( $reference );

			$form_id = $sub->get_form_id();

			$form_actions = Ninja_Forms()->form( $form_id )->get_actions();

			$email_field_id = '';

			foreach ( $form_actions as $action ) {

				$type = $action->get_setting( 'type' );

				if ( 'affiliatewp_add_referral' == $type ) {

					$settings = $action->get_settings();

					$email_field_id = $settings['affiliatewp_email'];

					break;
				}

			}

			if ( ! empty( $email_field_id ) ) {

				if ( is_email( $email_field_id ) ) {

					$email = $email_field_id;

				} else {

					$email_field_id = preg_replace( '/[{}]/', '', $email_field_id );

					$email_field_id = explode( ':', $email_field_id );

					$email = $sub->get_field_value( $email_field_id[1] );

				}
				
			}

		}

		return $email;

	}

}
new Affiliate_WP_Lifetime_Commissions_Ninja_Forms;
