<?php

class WPML_TM_Word_Count_Hooks_Factory implements IWPML_Backend_Action_Loader, IWPML_Frontend_Action_Loader {

	const PROCESS_PENDING     = 'pending';
	const PROCESS_IN_PROGRESS = 'in-progress';
	const PROCESS_COMPLETED   = 'completed';

	const OPTION_KEY_REQUESTED_TYPES_STATUS = 'wpml_word_count_requested_types_status';
	const NONCE_ACTION                      = 'wpml_tm_word_count_ajax';

	private $hooks = array();
	private $requested_types_status;
	private $translation_element_factory;
	private $words_count_background_process_factory;
	private $words_count_single_process_factory;

	public function create() {
		$this->requested_types_status = get_option( self::OPTION_KEY_REQUESTED_TYPES_STATUS, false );

		$this->add_refresh_hooks();
		$this->add_process_hooks();
		$this->add_admin_hooks();
		$this->add_ajax_hooks();

		return $this->hooks;
	}

	private function add_refresh_hooks() {
		if ( $this->is_heartbeat_autosave() ) {
			return;
		}

		$this->hooks['refresh'] = new WPML_TM_Word_Count_Refresh_Hooks(
			$this->get_words_count_single_process_factory(),
			$this->get_translation_element_factory(),
			class_exists( 'WPML_ST_Package_Factory' ) ? new WPML_ST_Package_Factory() : null
		);
	}

	private function add_process_hooks() {
		if ( $this->requested_types_status === self::PROCESS_IN_PROGRESS ) {
			$this->hooks['process'] = new WPML_TM_Word_Count_Process_Hooks(
				$this->get_words_count_background_process_factory()
			);
		}
	}

	private function add_admin_hooks() {
		if ( is_admin() ) {
			$this->hooks['admin'] = new WPML_TM_Word_Count_Admin_Hooks( new WPML_WP_API() );
		}
	}

	private function add_ajax_hooks() {
		if ( isset( $_POST['module'] ) && 'wpml_word_count' === $_POST['module'] && wpml_is_ajax() ) {
			$report_view = new WPML_TM_Word_Count_Report_View(
				new WPML_Twig_Template_Loader( array( WPML_TM_PATH . WPML_TM_Word_Count_Report_View::TEMPLATE_PATH ) ),
				new WPML_WP_Cron_Check( new WPML_PHP_Functions() )
			);

			$records_factory = new WPML_TM_Word_Count_Records_Factory();

			$report = new WPML_TM_Word_Count_Report(
				$report_view,
				$records_factory->create(),
				$this->get_sitepress(),
				class_exists( 'WPML_Package_Helper' ) ? new WPML_Package_Helper() : null,
				$this->requested_types_status
			);

			$this->hooks['ajax'] = new WPML_TM_Word_Count_Ajax_Hooks(
				$report,
				$this->get_words_count_background_process_factory(),
				$this->requested_types_status
			);
		}
	}

	/**
	 * @return WPML_TM_Word_Count_Single_Process_Factory
	 */
	private function get_words_count_single_process_factory() {
		if ( ! $this->words_count_single_process_factory ) {
			$this->words_count_single_process_factory = new WPML_TM_Word_Count_Single_Process_Factory();
		}

		return $this->words_count_single_process_factory;
	}

	private function get_translation_element_factory() {
		if ( ! $this->translation_element_factory ) {
			$this->translation_element_factory = new WPML_Translation_Element_Factory( $this->get_sitepress() );
		}

		return $this->translation_element_factory;
	}

	/**
	 * @return WPML_TM_Word_Count_Background_Process_Factory
	 */
	private function get_words_count_background_process_factory() {
		if ( ! $this->words_count_background_process_factory ) {
			$this->words_count_background_process_factory = new WPML_TM_Word_Count_Background_Process_Factory();
		}

		return $this->words_count_background_process_factory;
	}

	/**
	 * @return SitePress
	 */
	private function get_sitepress() {
		global $sitepress;

		return $sitepress;
	}

	private function is_heartbeat_autosave() {
		return isset( $_POST['data']['wp_autosave'] );
	}
}