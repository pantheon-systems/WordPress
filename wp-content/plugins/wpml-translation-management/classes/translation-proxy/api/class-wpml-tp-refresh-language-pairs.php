<?php

class WPML_TP_Refresh_Language_Pairs {

	const AJAX_ACTION = 'wpml-tp-refresh-language-pairs';

	/**
	 * @var WPML_TP_API
	 */
	private $tp_api;

	/**
	 * @var
	 */
	private $tp_project;

	/**
	 * WPML_TP_AJAX constructor.
	 *
	 * @param WPML_TP_API $wpml_tp_api
	 */
	public function __construct( WPML_TP_API $wpml_tp_api, WPML_TP_Project $wpml_tp_project ) {
		$this->tp_api     = $wpml_tp_api;
		$this->tp_project = $wpml_tp_project;
	}

	public function add_hooks() {
		add_action( 'wp_ajax_' . self::AJAX_ACTION, array( $this, 'refresh_language_pairs' ) );
	}

	public function refresh_language_pairs() {
		$project = $this->tp_project;

		if ( $this->is_valid_request() ) {
			try {
				$this->tp_api->refresh_language_pairs( $project );
				wp_send_json_success( array(
					'msg' => __( 'Language pairs refreshed', 'wpml-translation-management' )
				) );
			} catch ( Exception $e ) {
				wp_send_json_error( array(
					'msg' => __( 'Language pairs not refreshed, please try again', 'wpml-translation-management' ),
				) );
			}
		} else {
			wp_send_json_error( array(
				'msg' => __( 'Invalid Request', 'wpml-translation-management' ),
			) );
		}
	}

	/**
	 * @return bool
	 */
	private function is_valid_request() {
		return array_key_exists( 'nonce', $_POST ) &&
		       wp_verify_nonce( filter_var( $_POST['nonce'], FILTER_SANITIZE_FULL_SPECIAL_CHARS ), self::AJAX_ACTION );
	}
}
