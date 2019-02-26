<?php

class WPML_TM_Translation_Service_Authentication_Ajax {

	const AJAX_ACTION = 'translation_service_authentication';

	/**
	 * @var WPML_TP_Service_Authentication_Ajax_Action
	 */
	private $service_authentication;

	/**
	 * @var WPML_TP_Service_Invalidation_Ajax_Action
	 */
	private $service_invalidation;

	public function __construct(
		WPML_TP_Service_Authentication_Ajax_Action $service_authentication,
		WPML_TP_Service_Invalidation_Ajax_Action $service_invalidation
	) {
		$this->service_authentication = $service_authentication;
		$this->service_invalidation = $service_invalidation;
	}

	public function add_hooks() {
		add_action( 'wp_ajax_translation_service_authentication', array( $this, 'authenticate_service' ) );
		add_action( 'wp_ajax_translation_service_invalidation', array( $this, 'invalidate_service' ) );
	}

	public function authenticate_service() {
		if ( isset( $_POST['service_id'], $_POST['custom_fields'] ) && $this->is_valid_request() ) {
			wp_send_json_success( $this->service_authentication->run() );
		} else {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid Request', 'wpml-translation-management' )
				)
			);
		}
	}

	public function invalidate_service() {
		if ( $this->is_valid_request() ) {
			wp_send_json_success( $this->service_invalidation->run() );
		} else {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid Request', 'wpml-translation-management' )
				)
			);
		}
	}

	/**
	 * @return bool
	 */
	private function is_valid_request() {
		return isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], self::AJAX_ACTION );
	}
}