<?php

class WPML_TM_Wizard_Summary_Step extends WPML_Twig_Template_Loader {

	private $model = array();

	/** @var WPML_Translator_Records $translator_records */
	private $translator_records;

	/** @var WPML_TP_Service $active_translation_service */
	private $active_translation_service;

	public function __construct(
		WPML_Translator_Records $translator_records,
		WPML_TP_Service $active_translation_service = null
	) {
		parent::__construct( array(
				WPML_TM_PATH . '/templates/wizard',
			)
		);
		$this->translator_records         = $translator_records;
		$this->active_translation_service = $active_translation_service;
	}

	public function render() {
		$this->add_strings();
		$this->add_translators();
		$this->add_translation_service();

		return $this->get_template()->show( $this->model, 'summary-step.twig' );
	}

	public function add_strings() {

		$this->model['strings'] = array(
			'title'                   => __( 'Summary', 'wpml-translation-management' ),
			'translation_service'     => __( 'Activated translation service', 'wpml-translation-management' ),
			'local_translators'       => __( 'Local translators', 'wpml-translation-management' ),
			'local_summary'           => __( 'WPML created the accounts for your translators and sent them instructions.', 'wpml-translation-management' ),
			'translators_note'        => __( 'Your translators can work inside WordPress, using WPML’s Translation Editor or with their own CAT (Computer Assisted Translation) programs.', 'wpml-translation-management' ),
			'translation_how_to'      => __( 'How to send content to your translators', 'wpml-translation-management' ),
			'translation_how_to_link' => 'https://wpml.org/documentation/beginners-guide-to-site-translation/sending-contents-for-translation/',
			'go_back'                 => __( "Go back", 'wpml-translation-management' ),
			'done'                    => __( "Done!", 'wpml-translation-management' ),
		);
	}

	public function add_translators() {
		$this->model['translators'] = $this->translator_records->get_users_with_capability();
	}

	public function add_translation_service() {
		$this->model['translation_service'] = $this->active_translation_service;
	}

}