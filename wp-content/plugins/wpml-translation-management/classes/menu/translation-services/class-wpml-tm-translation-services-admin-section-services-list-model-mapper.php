<?php

class WPML_TM_Translation_Services_Admin_Section_Services_List_Model_Mapper {

	/**
	 * @var WPML_TM_Translation_Services_Admin_Active_Template
	 */
	private $active_service_template;

	public function __construct( WPML_TM_Translation_Services_Admin_Active_Template $active_service_template ) {
		$this->active_service_template = $active_service_template;
	}

	/**
	 * @param WPML_TP_Service $service
	 *
	 * @return array
	 */
	public function map(WPML_TP_Service $service) {
		return array(
			'id'                             => $service->get_id(),
			'logo_url'                       => $service->get_logo_url(),
			'name'                           => $service->get_name(),
			'description'                    => $service->get_description(),
			'doc_url'                        => $service->get_doc_url(),
			'active'                         => $service->get_id() === $this->active_service_template->get_id() ? 'active' : 'inactive',
			'popularity'                     => $service->get_rankings()->popularity,
			'speed'                          => $service->get_rankings()->speed,
			'how_to_get_credentials_desc'    => $service->get_how_to_get_credentials_desc(),
			'how_to_get_credentials_url'     => $service->get_how_to_get_credentials_url(),
			'client_create_account_page_url' => $service->get_client_create_account_page_url(),
			'custom_fields'                  => $service->get_custom_fields(),
		);
	}

}