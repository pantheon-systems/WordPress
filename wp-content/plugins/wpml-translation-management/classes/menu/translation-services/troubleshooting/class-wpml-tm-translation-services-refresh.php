<?php

class WPML_TM_Translation_Services_Refresh {

	const TEMPLATE = 'refresh-services.twig';
	const AJAX_ACTION = 'wpml_tm_refresh_services';

	/**
	 * @var IWPML_Template_Service
	 */
	private $template;

	/**
	 * @var WPML_TP_API_Services
	 */
	private $tp_services;

	public function __construct( IWPML_Template_Service $template, WPML_TP_API_Services $tp_services ) {
		$this->template    = $template;
		$this->tp_services = $tp_services;
	}

	public function add_hooks() {
		add_action( 'after_setup_complete_troubleshooting_functions', array( $this, 'render' ), 1 );
		add_action( 'wp_ajax_' . self::AJAX_ACTION, array( $this, 'refresh_services' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	public function render() {
		echo $this->template->show( $this->get_model(), self::TEMPLATE );
	}

	/**
	 * @return array
	 */
	private function get_model() {
		return array(
			'button_text'          => __( 'Refresh Translation Services', 'wpml-translation-management' ),
			'nonce'                => wp_create_nonce( self::AJAX_ACTION ),
		);
	}

	public function refresh_services() {
		if ( $this->is_valid_request() ) {
			if ( $this->tp_services->refresh_cache() && $this->refresh_active_service() ) {
				wp_send_json_success( array(
					'message' => __( 'Services Refreshed.', 'wpml-translation-management' ),
				));
			} else {
				wp_send_json_error( array(
					'message' => __( 'WPML cannot load the list of translation services. This can be a connection problem. Please wait a minute and reload this page.
 If the problem continues, please contact WPML support.', 'wpml-translation-management' ),
				));
			}
		} else {
			wp_send_json_error( array(
				'message' => __( 'Invalid Request.', 'wpml-translation-management' ),
			));
		}
	}

	private function refresh_active_service() {
		$active_service = $this->tp_services->get_active();

		if ( $active_service ) {
			$active_service = (object) (array) $active_service; // Cast to stdClass
			TranslationProxy::build_and_store_active_translation_service( $active_service, $active_service->custom_fields_data );
		}

		return true;
	}

	/**
	 * @return bool
	 */
	private function is_valid_request() {
		return isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], self::AJAX_ACTION );
	}

	public function enqueue_scripts() {
		wp_enqueue_script(
			'wpml-tm-refresh-services',
			WPML_TM_URL . '/res/js/refresh-services.js',
			array(),
			WPML_TM_VERSION
		);
	}
}