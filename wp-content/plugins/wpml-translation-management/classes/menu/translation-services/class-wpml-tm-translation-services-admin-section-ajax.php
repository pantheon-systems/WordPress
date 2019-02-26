<?php

class WPML_TM_Translation_Services_Admin_Section_Ajax {

	const NONCE_ACTION           = 'translation_service_toggle';
	const REFRESH_TS_INFO_ACTION = 'refresh_ts_info';

	/** @var WPML_TP_Client */
	private $tp_client;

	/** @var WPML_TM_Translation_Services_Admin_Active_Template_Factory */
	private $active_service_template_factory;

	public function __construct(
		WPML_TP_Client $tp_client,
		WPML_TM_Translation_Services_Admin_Active_Template_Factory $active_service_template_factory
	) {
		$this->tp_client                       = $tp_client;
		$this->active_service_template_factory = $active_service_template_factory;
	}

	public function add_hooks() {
		add_action( 'wp_ajax_translation_service_toggle', array( $this, 'translation_service_toggle' ) );
		add_action( 'wp_ajax_refresh_ts_info', array( $this, 'refresh_ts_info' ) );
	}

	public function translation_service_toggle( ) {
		if ( $this->is_valid_request( self::NONCE_ACTION ) ) {

			if ( ! isset( $_POST[ 'service_id' ] ) ) {
				return;
			}

			$service_id = (int) filter_var( $_POST[ 'service_id' ], FILTER_SANITIZE_NUMBER_INT );
			$enable = false;
			$response = false;

			if ( isset( $_POST[ 'enable' ] ) ) {
				$enable = filter_var( $_POST[ 'enable' ], FILTER_SANITIZE_NUMBER_INT );
			}

			if ( $enable ) {
				if ( $service_id !== TranslationProxy::get_current_service_id() ) {
					$response = $this->activate_service( $service_id );
				} else {
					$response = array( 'activated' => true );
				}
			}

			if ( ! $enable && $service_id === TranslationProxy::get_current_service_id() ) {
				TranslationProxy::clear_preferred_translation_service();
				$response = $this->deactivate_service();
			}

			wp_send_json_success( $response );
			return;
		}

		$this->send_invalid_nonce_error();
	}

	public function refresh_ts_info() {
		if ( $this->is_valid_request( self::REFRESH_TS_INFO_ACTION ) ) {
			$active_service = $this->tp_client->services()->get_active( true );

			if ( $active_service ) {
				$active_service = (object) (array) $active_service;
				TranslationProxy::build_and_store_active_translation_service( $active_service, $active_service->custom_fields_data );

				$active_service_template = $this->active_service_template_factory->create();

				$data = array(
					'active_service_block' => $active_service_template->render(),
				);

				wp_send_json_success( $data );
				return;
			}

			wp_send_json_error(
				array( 'message' => __( 'It was not possible to refresh the active translation service information.', 'wpml-translation-management' ) )
			);
			return;
		}

		$this->send_invalid_nonce_error();
	}

	/**
	 * @param int $service_id
	 *
	 * @return array
	 * @throws \InvalidArgumentException
	 */
	private function activate_service( $service_id ) {
		$result  = TranslationProxy::select_service( $service_id );
		$message = '';
		if ( is_wp_error( $result ) ) {
			$message = $result->get_error_message();
		}

		return array(
			'message'   => $message,
			'reload'    => 1,
			'activated' => 1,
		);
	}

	private function deactivate_service() {
		TranslationProxy::deselect_active_service();

		return array(
			'message'   => '',
			'reload'    => 1,
			'activated' => 0,
		);
	}

	/**
	 * @param string $action
	 *
	 * @return bool
	 */
	private function is_valid_request( $action ) {
		if ( ! isset( $_POST[ 'nonce' ] ) ) {
			return false;
		}

		return wp_verify_nonce( filter_var( $_POST[ 'nonce' ], FILTER_SANITIZE_FULL_SPECIAL_CHARS ), $action );
	}

	private function send_invalid_nonce_error() {
		$response = array(
			'message' => __( 'You are not allowed to perform this action.', 'wpml-translation-management' ),
			'reload'  => 0,
		);

		wp_send_json_error( $response );
	}
}
