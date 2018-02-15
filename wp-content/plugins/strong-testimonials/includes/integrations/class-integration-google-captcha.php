<?php
class Strong_Testimonials_Integration_Google_Captcha extends Strong_Testimonials_Integration_Captcha {

	public function __contruct() {
		parent::__construct();
	}

	public function add_captcha() {
		if ( function_exists( 'gglcptch_display_custom' ) ) {
			return gglcptch_display_custom();
		}

		return '';
	}

	public function check_captcha( $form_errors ) {
		if ( function_exists( 'gglcptch_check_custom' ) && gglcptch_check_custom() !== true ) {
			$form_errors['captcha'] = __( 'The Captcha failed. Please try again.', 'strong-testimonials' );
		}

		return $form_errors;
	}

}
