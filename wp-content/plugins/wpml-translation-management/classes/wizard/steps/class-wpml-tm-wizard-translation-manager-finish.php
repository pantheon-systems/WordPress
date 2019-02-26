<?php

class WPML_TM_Wizard_Translation_Manager_Finish extends WPML_Twig_Template_Loader {

	private $model = array();

	/** @var WPML_Translation_Manager_Records $translation_manager_records */
	private $translation_manager_records;

	public function __construct( WPML_Translation_Manager_Records $translation_manager_records ) {
		parent::__construct( array(
				WPML_TM_PATH . '/templates/wizard',
			)
		);
		$this->translation_manager_records = $translation_manager_records;
	}

	public function render() {
		$this->add_strings();
		$this->add_nonce();

		return $this->get_template()->show( $this->model, 'translation-manager-finish-step.twig' );
	}

	public function add_strings() {
		$translation_manager = $this->get_translation_manager();

		$this->model['strings'] = array(
			'title'                    => __( 'Waiting for the Translation Manager to complete the setup…', 'wpml-translation-management' ),
			'summary'                  => __( 'The Translation Manager that you selected should complete the rest of this setup-wizard.', 'wpml-translation-management' ),
			'send_email_text'          => sprintf( __( 'Send instructions to %s', 'wpml-translation-management' ), $translation_manager->user_login ),
			'button_text'              => __( 'Done!', 'wpml-translation-management' ),
			'manage_translators_url'   => 'admin.php?page=' . WPML_TM_FOLDER . '/menu/main.php&sm=translators',
			'translation_manager_id'   => $translation_manager->ID,
		);
	}

	private function get_translation_manager() {
		$translation_managers = $this->translation_manager_records->get_users_with_capability();

		return $translation_managers[0];  // There will only be a single one at this point.
	}

	private function add_nonce() {
		$this->model['nonce'] = wp_create_nonce( WPML_Translation_Manager_Settings::NONCE_ACTION );
	}

}