<?php
class Strong_Testimonials_Integration_Really_Simple_Captcha extends Strong_Testimonials_Integration_Captcha {

	public function __contruct() {
		parent::__construct();
	}

	public function add_captcha() {
		if ( class_exists( 'ReallySimpleCaptcha' ) ) {
			$captcha_instance = new ReallySimpleCaptcha();
			$word = $captcha_instance->generate_random_word();
			$prefix = mt_rand();
			$image = $captcha_instance->generate_image( $prefix, $word );
			$html = '<span>' . _x( 'Input this code:', 'Captcha', 'strong-testimonials' ) . '&nbsp;<input type="hidden" name="captchac" value="'.$prefix.'"><img class="captcha" src="' . plugins_url( 'really-simple-captcha/tmp/' ) . $image . '"></span>';
			$html .= '<input type="text" class="captcha" name="captchar" maxlength="4" size="5">';
			return $html;
		}

		return '';
	}

	public function check_captcha( $form_errors ) {
		if ( class_exists( 'ReallySimpleCaptcha' ) ) {
			$captcha_instance = new ReallySimpleCaptcha();
			$prefix = isset( $_POST['captchac'] ) ? (string) $_POST['captchac'] : '';
			$response = isset( $_POST['captchar'] ) ? (string) $_POST['captchar'] : '';
			$correct = $captcha_instance->check( $prefix, $response );
			if ( !$correct ) {
				$errors['captcha'] = __( 'The Captcha failed. Please try again.', 'strong-testimonials' );
			}
			// remove the temporary image and text files (except on Windows)
			if ( '127.0.0.1' != $_SERVER['SERVER_ADDR'] ) {
				$captcha_instance->remove( $prefix );
			}
		}

		return $form_errors;
	}

}
