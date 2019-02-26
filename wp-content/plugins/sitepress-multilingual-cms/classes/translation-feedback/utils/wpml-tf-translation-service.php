<?php

/**
 * Class WPML_TF_Translation_Service
 *
 * @author OnTheGoSystems
 */
class WPML_TF_Translation_Service {

	/** @var WPML_TP_Client_Factory $tp_client_factory */
	private $tp_client_factory;
	/**
	 * WPML_TF_Translation_Service constructor.
	 *
	 * @param WPML_TP_Client_Factory $tp_client_factory
	 */
	public function __construct( WPML_TP_Client_Factory $tp_client_factory = null ) {
		$this->tp_client_factory = $tp_client_factory;
	}

	/** @return bool */
	public function allows_translation_feedback() {
		if ( ! $this->tp_client_factory ) {
			return true;
		}

		$translation_service = $this->tp_client_factory->create()->services()->get_active();

		if ( isset( $translation_service->translation_feedback ) && ! $translation_service->translation_feedback ) {
			return false;
		}

		return true;
	}
}
