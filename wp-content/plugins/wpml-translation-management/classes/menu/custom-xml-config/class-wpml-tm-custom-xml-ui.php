<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TM_Custom_XML_UI {
	private $custom_xml;
	private $template_service;

	function __construct( WPML_Custom_XML $custom_xml, IWPML_Template_Service $template_service ) {
		$this->custom_xml       = $custom_xml;
		$this->template_service = $template_service;
	}

	public function show() {
		$model = $this->get_model();

		return $this->template_service->show( $model, 'main.twig' );
	}

	private function get_model() {
		return array(
			'strings' => array(
				'content'       => __( 'Custom XML configuration', 'wpml-translation-management' ),
				'save'          => __( 'Save', 'wpml-translation-management' ),
				'shortcuts'     => __( 'Shortcuts', 'wpml-translation-management' ),
				'keysmap'       => array(
					'Ctrl-K/CMD-K' => __( 'Fold/Unfold code', 'wpml-translation-management' ),
					'Ctrl-F/CMD-F' => __( 'Autoformat', 'wpml-translation-management' ),
					'Ctrl-S/CMD-S' => __( 'Save', 'wpml-translation-management' ),
				),
				'documentation' => __( 'How to write Language Configuration Files', 'wpml-translation-management' ),
			),
			'data'    => array(
				'action'         => WPML_TM_Custom_XML_AJAX::AJAX_ACTION_BASE,
				'nonceValidate' => wp_create_nonce( WPML_TM_Custom_XML_AJAX::AJAX_ACTION_BASE . '-validate' ),
				'nonceSave'     => wp_create_nonce( WPML_TM_Custom_XML_AJAX::AJAX_ACTION_BASE . '-save' ),
			),
			'links'   => array(
				'documentation' => 'https://wpml.org/documentation/support/language-configuration-files',
			),
			'content' => $this->custom_xml->get(),
		);
	}
}
