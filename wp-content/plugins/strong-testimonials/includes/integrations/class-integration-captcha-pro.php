<?php
class Strong_Testimonials_Integration_Captcha_Pro extends Strong_Testimonials_Integration_Captcha {

	public function __contruct() {
		parent::__construct();
	}

	public function add_captcha() {
		if ( function_exists( 'cptch_display_captcha_custom' ) ) {
			return '<input type="hidden" name="cntctfrm_contact_action" value="true">' .
			       cptch_display_captcha_custom();
		}

		return '';
	}

	public function check_captcha( $form_errors ) {
		if ( function_exists( 'cptch_check_custom_form' ) && cptch_check_custom_form() !== true ) {
			$form_errors['captcha'] = __( 'The Captcha failed. Please try again.', 'strong-testimonials' );
		}

		return $form_errors;
	}

}
