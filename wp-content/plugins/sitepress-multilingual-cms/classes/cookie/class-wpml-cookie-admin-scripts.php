<?php

/**
 * Class WPML_Cookie_Admin_Scripts
 */
class WPML_Cookie_Admin_Scripts {

	public function enqueue() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	public function enqueue_scripts() {
		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wpml-cookie-ajax-setting', ICL_PLUGIN_URL . '/res/js/cookies/cookie-ajax-setting.js', array( 'jquery', 'wp-pointer' ), ICL_SITEPRESS_VERSION );

		wp_localize_script( 'wpml-cookie-ajax-setting', 'wpml_cookie_setting', array(
			'nonce'            => WPML_Cookie_Setting_Ajax::NONCE_COOKIE_SETTING,
			'button_id'        => WPML_Cookie_Admin_UI::BUTTON_ID,
			'ajax_response_id' => WPML_Cookie_Setting_Ajax::AJAX_RESPONSE_ID,
			'field_name'       => WPML_Cookie_Setting::COOKIE_SETTING_FIELD,
			'ajax_action'      => WPML_Cookie_Setting_Ajax::ACTION
		) );
	}
}