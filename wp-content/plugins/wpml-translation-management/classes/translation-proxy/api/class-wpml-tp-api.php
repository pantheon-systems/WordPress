<?php

class WPML_TP_API {
	private $logger;
	private $params = array();
	/**
	 * @var WPML_TP_Communication
	 */
	private $wpml_tp_communication;

	/**
	 * WPML_TP_API constructor.
	 *
	 * @param WPML_TP_Communication $wpml_tp_communication
	 * @param string                $api_version
	 * @param WPML_TM_Log           $logger
	 */
	public function __construct( WPML_TP_Communication $wpml_tp_communication, $api_version = '1.1', WPML_TM_Log $logger = null ) {
		$this->wpml_tp_communication = $wpml_tp_communication;
		$this->params['api_version'] = $api_version;
		$this->logger                = $logger;
	}

	public function get_current_project() {
		return TranslationProxy::get_current_project();
	}

	/**
	 * @param WPML_TP_Project $project
	 *
	 * @return mixed
	 */
	public function refresh_language_pairs( WPML_TP_Project $project ) {

		$this->log( 'Refresh language pairs -> Request sent' );

		$this->add_param( 'project', array( 'refresh_language_pairs' => 1 ) );
		$this->add_param( 'refresh_language_pairs', 1 );
		$this->add_param( 'project_id', $project->get_id() );
		$this->add_param( 'accesskey', $project->get_access_key() );

		$this->wpml_tp_communication->set_method( 'PUT' );
		$this->wpml_tp_communication->set_request_format();
		$this->wpml_tp_communication->set_response_format();
		$this->wpml_tp_communication->request_must_respond( false );

		return $this->wpml_tp_communication->projects( $this->params );
	}

	public function get_current_service() {
		return TranslationProxy::get_current_service();
	}

	/**
	 * @param $service_id
	 * @param bool $custom_fields
	 *
	 * @throws WPMLTranslationProxyApiException
	 */
	public function select_service( $service_id, $custom_fields = false ) {
		TranslationProxy::select_service( $service_id, $custom_fields );
	}

	public function get_projects() {
		return TranslationProxy::get_translation_projects();
	}

	private function add_param( $name, $value ) {
		$this->params[ $name ] = $value;
	}

	private function log( $action, array $params = array() ) {
		if ( null !== $this->logger ) {
			$this->logger->log( $action, $params );
		}
	}
}
