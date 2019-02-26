<?php

class WPML_TM_Translation_Services_Admin_Active_Template {

	const ACTIVE_SERVICE_TEMPLATE = 'active-service.twig';
	const HOURS_BEFORE_TS_REFRESH = 24;

	/** @var IWPML_Template_Service */
	private $template_service;

	/** @var WPML_TP_Service */
	private $active_service;

	/**
	 * @param IWPML_Template_Service $template_service
	 * @param WPML_TP_Service        $active_service
	 */
	public function __construct( IWPML_Template_Service $template_service, WPML_TP_Service $active_service = null ) {
		$this->template_service = $template_service;
		$this->active_service   = $active_service;
	}

	public function render() {
		if ( $this->active_service ) {
			return $this->template_service->show( $this->get_model(), self::ACTIVE_SERVICE_TEMPLATE );
		}

		return null;
	}

	/**
	 * @return array
	 */
	private function get_model() {
		$model = array();

		if ( $this->active_service ) {
			$model = array(
				'strings' => array(
					'title'        => __( 'Active service:', 'wpml-translation-management' ),
					'deactivate'   => __( 'Deactivate', 'wpml-translation-management' ),
					'modal_header' => sprintf( __( 'Enter here your %s authentication details', 'wpml-translation-management' ), $this->active_service->get_name() ),
					'modal_tip'    => $this->active_service->get_popup_message() ?
						$this->active_service->get_popup_message() :
						__( 'You can find API token at %s site', 'wpml-translation-management' ),
					'modal_title'  => sprintf( __( '%s authentication', 'wpml-translation-management' ), $this->active_service->get_name() ),
					'refresh_language_pairs'  => __( 'Refresh language pairs', 'wpml-translation-management' ),
					'refresh_ts_info'         => __( 'Refresh information', 'wpml-translation-management' ),
					'documentation_lower'     => __( 'documentation', 'wpml-translation-management' ),
					'refreshing_ts_message'   => __( 'Refreshing translation service information...', 'wpml-translation-management' ),
				),
				'active_service' => $this->active_service,
				'nonces' => array(
					WPML_TP_Refresh_Language_Pairs::AJAX_ACTION                             => wp_create_nonce( WPML_TP_Refresh_Language_Pairs::AJAX_ACTION ),
					WPML_TM_Translation_Services_Admin_Section_Ajax::REFRESH_TS_INFO_ACTION => wp_create_nonce( WPML_TM_Translation_Services_Admin_Section_Ajax::REFRESH_TS_INFO_ACTION ),
				),
				'needs_info_refresh' => $this->should_refresh_data(),
			);

			$authentication_message = array();
			/* translators: sentence 1/3: create account with the translation service ("%1$s" is the service name) */
			$authentication_message[] = __( 'To send content for translation to %1$s, you need to have an %1$s account.', 'wpml-translation-management' );
			/* translators: sentence 2/3: create account with the translation service ("one" is "one account) */
			$authentication_message[] = __( "If you don't have one, you can create it after clicking the authenticate button.", 'wpml-translation-management' );
			/* translators: sentence 3/3: create account with the translation service ("%2$s" is "documentation") */
			$authentication_message[] = __( 'Please, check the %2$s page for more details.', 'wpml-translation-management' );

			$model['strings']['authentication'] = array(
				'description'         => implode( ' ', $authentication_message ),
				'authenticate_button' => __( 'Authenticate', 'wpml-translation-management' ),
				'de_authorize_button' => __( 'De-authorize', 'wpml-translation-management' ),
				'is_authorized'       => sprintf( __( '%s is authorized.', 'wpml-translation-management' ), $this->active_service->get_name() ),
			);
		}

		return $model;
	}

	private function should_refresh_data() {
		$refresh_time = time() - ( self::HOURS_BEFORE_TS_REFRESH * HOUR_IN_SECONDS );

		if ( ! $this->active_service->get_last_refresh()
		     || $this->active_service->get_last_refresh() < $refresh_time
		) {
			return true;
		}

		return false;
	}

	/** @return int|null */
	public function get_id() {
		if ( $this->active_service ) {
			return $this->active_service->get_id();
		}

		return null;
	}
}