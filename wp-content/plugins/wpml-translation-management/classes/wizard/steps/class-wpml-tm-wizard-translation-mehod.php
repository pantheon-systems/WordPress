<?php

class WPML_TM_Wizard_Translation_Method_Step extends WPML_Twig_Template_Loader {

	private $model = array();

	public function __construct() {
		parent::__construct( array(
				WPML_TM_PATH . '/templates/wizard',
			)
		);
	}

	public function render() {
		$this->add_strings();

		return $this->get_template()->show( $this->model, 'translation-method-step.twig' );
	}

	public function add_strings() {

		$this->model['strings'] = array(
			'title'                      => __( 'Select Translation Method', 'wpml-translation-management' ),
			'sub_title'                  => __( 'Do you want to use a translation service for some or all of your site’s content?', 'wpml-translation-management' ),
			'skip_translation_services'  => __( "No thanks, I'm using my own translators", 'wpml-translation-management' ),
			'select_translation_service' => __( "Yes, Show me the available translation services", 'wpml-translation-management' ),
		);
	}

}