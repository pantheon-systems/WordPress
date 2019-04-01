<?php

class WPML_TM_Wizard_Translators_Step extends WPML_TM_Translators_View {

	public function __construct(
		WPML_Translator_Records $user_records,
		WPML_Language_Collection $active_languages,
		WPML_TP_Service $active_translation_service = null
	) {
		parent::__construct( $user_records, $active_languages );
		$active_translation_service_id = $active_translation_service ? $active_translation_service->get_id() : 0;
		$this->model[ 'active_translation_service_id' ] = $active_translation_service_id;
	}

	public function get_twig_template() {
		return 'translators-step.twig';
	}

	public function get_template_paths() {
		return array ( WPML_TM_PATH . '/templates/wizard' );
	}

}