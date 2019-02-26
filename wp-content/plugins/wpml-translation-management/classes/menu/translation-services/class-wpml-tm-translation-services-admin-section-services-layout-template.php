<?php

class WPML_TM_Translation_Services_Admin_Section_Services_Layout_Template {

	const SERVICES_LIST_TEMPLATE = 'services-layout.twig';

	/**
	 * @var IWPML_Template_Service
	 */
	private $template_service;

	/**
	 * @var WPML_TM_Translation_Services_Admin_Active_Template
	 */
	private $active_service_template;

	/**
	 * @var bool
	 */
	private $has_preferred_service;

	/**
	 * @var WPML_TM_Services_List_Template[]
	 */
	private $services;

	/**
	 * @param IWPML_Template_Service $template_service
	 * @param WPML_TM_Translation_Services_Admin_Active_Template $active_service_template
	 * @param bool $has_preferred_service
	 * @param WPML_TM_Services_List_Template[] $services
	 */
	public function __construct(
		IWPML_Template_Service $template_service,
		WPML_TM_Translation_Services_Admin_Active_Template $active_service_template,
		$has_preferred_service,
		array $services
	) {
		$this->template_service = $template_service;
		$this->active_service_template = $active_service_template;
		$this->has_preferred_service = $has_preferred_service;
		$this->services = $services;
	}


	public function render() {
		echo $this->template_service->show( $this->get_services_list_model(), self::SERVICES_LIST_TEMPLATE );
	}

	/**
	 * @return array
	 */
	private function get_services_list_model() {
		$services = '';
		foreach ( $this->services as $service ) {
			$services .= $service->render();
		}

		$model = array(
			'active_service'        => $this->active_service_template->render(),
			'services'              => $services,
			'has_preferred_service' => $this->has_preferred_service,
			'has_services'          => ! empty( $this->services ),
			'nonces'                => array(
				WPML_TM_Translation_Services_Admin_Section_Ajax::NONCE_ACTION => wp_create_nonce( WPML_TM_Translation_Services_Admin_Section_Ajax::NONCE_ACTION ),
				WPML_TM_Translation_Service_Authentication_Ajax::AJAX_ACTION  => wp_create_nonce( WPML_TM_Translation_Service_Authentication_Ajax::AJAX_ACTION ),
			),
			'strings'               => array(
				'no_service_found' => array(
					__( 'WPML cannot load the list of translation services. This can be a connection problem. Please wait a minute and reload this page.',
						'wpml-translation-management' ),
					__( 'If the problem continues, please contact %s.', 'wpml-translation-management' ),
				),
				'wpml_support'     => 'WPML support',
				'support_link'     => 'https://wpml.org/forums/forum/english-support/',
				'activate'         => __( 'Activate', 'wpml-translation-management' ),
				'documentation'    => __( 'Documentation', 'wpml-translation-management' ),
				'ts'               => array(
					'different'   => __( 'Looking for a different translation service?',
						'wpml-translation-management' ),
					'tell_us_url' => 'https://wpml.org/documentation/content-translation/how-to-add-translation-services-to-wpml/#add-service-form',
					'tell_us'     => __( 'Tell us which one', 'wpml-translation-management' ),
				),
			)
		);

		return $model;
	}
}