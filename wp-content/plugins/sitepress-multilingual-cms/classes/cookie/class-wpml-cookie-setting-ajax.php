<?php

/**
 * Class WPML_Frontend_Cookie_Setting_Ajax
 */
class WPML_Cookie_Setting_Ajax {

	const NONCE_COOKIE_SETTING = 'wpml-frontend-cookie-setting-nonce';
	const AJAX_RESPONSE_ID     = 'icl_ajx_response_cookie';
	const ACTION               = 'wpml_update_cookie_setting';

	/**
	 * @var WPML_Cookie_Setting
	 */
	private $wpml_frontend_cookie_setting;

	/**
	 * WPML_Frontend_Cookie_Setting_Ajax constructor.
	 *
	 * @param WPML_Cookie_Setting $wpml_frontend_cookie_setting
	 */
	public function __construct( WPML_Cookie_Setting $wpml_frontend_cookie_setting ) {
		$this->wpml_frontend_cookie_setting = $wpml_frontend_cookie_setting;
	}

	public function add_hooks() {
		add_action( 'wp_ajax_wpml_update_cookie_setting', array( $this, 'update_cookie_setting' ) );
	}

	public function update_cookie_setting() {
		if ( ! $this->is_valid_request() ) {
			wp_send_json_error();
		} else {

			if( array_key_exists( WPML_Cookie_Setting::COOKIE_SETTING_FIELD, $_POST ) ) {
				$store_frontend_cookie = filter_var( $_POST[ WPML_Cookie_Setting::COOKIE_SETTING_FIELD ], FILTER_SANITIZE_NUMBER_INT, FILTER_NULL_ON_FAILURE );
				$this->wpml_frontend_cookie_setting->set_setting( $store_frontend_cookie );
			} else {
				$this->wpml_frontend_cookie_setting->set_setting( 0 );
			}

			wp_send_json_success();
		}
	}

	/**
	 * @return bool
	 */
	private function is_valid_request() {
		$valid_request = false;

		if ( array_key_exists( 'nonce', $_POST ) ) {
			$valid_request = wp_verify_nonce( $_POST['nonce'], self::NONCE_COOKIE_SETTING );
		}

		return $valid_request;
	}
}