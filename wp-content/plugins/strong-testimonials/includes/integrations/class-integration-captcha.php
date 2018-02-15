<?php
class Strong_Testimonials_Integration_Captcha {

	public function __construct() {}

	public function add_captcha() {
		return '';
	}

	public function check_captcha( $form_errors ) {
		return $form_errors;
	}

}