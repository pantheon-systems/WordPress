<?php

class WPML_TM_Translation_Services_Admin_Section_No_Site_Key_Template {

	const TEMPLATE = 'no-site-key.twig';

	/**
	 * @var IWPML_Template_Service
	 */
	private $template_service;

	public function __construct( IWPML_Template_Service $template_service ) {
		$this->template_service = $template_service;
	}

	public function render() {
		echo $this->template_service->show( $this->get_no_site_key_model(), self::TEMPLATE );
	}

	/**
	 * @return array
	 */
	private function get_no_site_key_model() {
		return array(
			'registration' => array(
				'link' => admin_url('plugin-install.php?tab=commercial#repository-wpml'),
				'text' => __( 'Please register WPML to enable the professional translation option', 'wpml-translation-management'),
			),
		);
	}
}