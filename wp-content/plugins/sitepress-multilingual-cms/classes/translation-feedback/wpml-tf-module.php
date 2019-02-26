<?php

/**
 * Class WPML_TF_Module
 *
 * @author OnTheGoSystems
 */
class WPML_TF_Module {

	/** @var WPML_Action_Filter_Loader $action_filter_loader */
	private $action_filter_loader;

	/** @var WPML_TF_Settings $settings */
	private $settings;

	/**
	 * WPML_TF_Module constructor.
	 *
	 * @param WPML_Action_Filter_Loader $action_filter_loader
	 * @param WPML_TF_Settings          $settings
	 */
	public function __construct( WPML_Action_Filter_Loader $action_filter_loader, WPML_TF_Settings $settings ) {
		$this->action_filter_loader = $action_filter_loader;
		$this->settings             = $settings;
	}

	public function run() {
		$this->action_filter_loader->load( $this->get_actions_to_load_always() );

		if ( $this->settings->is_enabled() ) {
			$this->action_filter_loader->load( $this->get_actions_to_load_when_module_enabled() );
		}
	}

	/**
	 * @return array
	 */
	private function get_actions_to_load_always() {
		return array(
			'WPML_TF_Backend_Options_Hooks_Factory',
			'WPML_TF_Backend_Options_AJAX_Hooks_Factory',
			'WPML_TF_Backend_Promote_Hooks_Factory',
		);
	}

	/**
	 * @return array
	 */
	private function get_actions_to_load_when_module_enabled() {
		return array(
			'WPML_TF_Common_Hooks_Factory',
			'WPML_TF_Backend_Hooks_Factory',
			'WPML_TF_Frontend_Hooks_Factory',
			'WPML_TF_Frontend_AJAX_Hooks_Factory',
			'WPML_TF_Backend_AJAX_Feedback_Edit_Hooks_Factory',
			'WPML_TF_Backend_Post_List_Hooks_Factory',
		);
	}
}
