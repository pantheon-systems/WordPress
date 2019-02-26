<?php

class WPML_TM_Wizard_Steps implements IWPML_Action {

	/** @var WPML_Translation_Manager_Records $translation_manager_records */
	private $translation_manager_records;

	/** @var WPML_Translator_Records $translator_records */
	private $translator_records;

	/** @var WPML_TM_Translation_Services_Admin_Section_Factory $translation_services_factory */
	private $translation_services_factory;

	/** @var SitePress $sitepress */
	private $sitepress;

	public function __construct(
		WPML_Translation_Manager_Records $translation_manager_records,
		WPML_Translator_Records $translator_records,
		WPML_TM_Translation_Services_Admin_Section_Factory $translation_services_factory,
		SitePress $sitepress
	) {
		$this->translation_manager_records  = $translation_manager_records;
		$this->translator_records           = $translator_records;
		$this->translation_services_factory = $translation_services_factory;
		$this->sitepress                    = $sitepress;
	}

	public function add_hooks() {
		add_filter( 'wpml_wizard_fetch_tm_select_manager', array( $this, 'select_manager' ) );
		add_filter( 'wpml_wizard_fetch_tm_finish', array( $this, 'finish' ) );
		add_filter( 'wpml_wizard_fetch_tm_translation_method', array( $this, 'translation_manager_step' ) );
		add_filter( 'wpml_wizard_fetch_tm_translation_service', array( $this, 'translation_manager_step' ) );
		add_filter( 'wpml_wizard_fetch_tm_local_translators', array( $this, 'translation_manager_step' ) );
		add_filter( 'wpml_wizard_fetch_tm_translation_editor', array( $this, 'translation_manager_step' ) );
		add_filter( 'wpml_wizard_fetch_tm_summary', array( $this, 'translation_manager_step' ) );
		add_filter( 'wp_ajax_wpml_tm_wizard_done', array( $this, 'done' ) );
	}

	public function select_manager( $content ) {
		$step = new WPML_TM_Wizard_Select_Translation_Manager_Step();

		return $step->render();
	}

	public function finish( $content ) {
		$step = new WPML_TM_Wizard_Translation_Manager_Finish( $this->translation_manager_records );

		return $step->render();
	}

	public function translation_manager_step( $content ) {
		$step_slug = $this->get_step_slug();

		$this->save_current_step( $step_slug );

		$step = $this->get_step( $step_slug );

		return $step->render();
	}

	public function get_step( $step_slug ) {
		switch ( $step_slug ) {
			case 'tm_translation_method':
				return new WPML_TM_Wizard_Translation_Method_Step();

			case 'tm_translation_service':
				return new WPML_TM_Wizard_Translation_Service_Step( $this->translation_services_factory );

			case 'tm_local_translators':
				return new WPML_TM_Wizard_Translators_Step(
					$this->translator_records,
					new WPML_Language_Collection( $this->sitepress, array_keys( $this->sitepress->get_active_languages() ) ),
					$this->get_active_translation_service()
				);

			case 'tm_translation_editor':
				$tm_strings_factory = new WPML_TM_Scripts_Factory();

				return new WPML_TM_Wizard_Translation_Editor_Step( $tm_strings_factory->create_ate() );

			case 'tm_summary':
				return new WPML_TM_Wizard_Summary_Step(
					$this->translator_records,
					$this->get_active_translation_service()
				);
		}
	}

	public function done() {
		delete_option( WPML_TM_Wizard_For_Manager_Options::CURRENT_STEP );
		update_option( WPML_TM_Wizard_For_Manager_Options::WIZARD_COMPLETE, true );
		wp_send_json_success();
	}

	private function save_current_step( $step_slug ) {
		update_option( WPML_TM_Wizard_For_Manager_Options::CURRENT_STEP, $step_slug );
	}

	private function get_step_slug() {
		return str_replace( 'wpml_wizard_fetch_', '', current_filter() );
	}

	private function get_active_translation_service() {
		$active_service = $this->sitepress->get_setting( 'translation_service' );
		return $active_service ? new WPML_TP_Service( $active_service ) : null;
	}
}