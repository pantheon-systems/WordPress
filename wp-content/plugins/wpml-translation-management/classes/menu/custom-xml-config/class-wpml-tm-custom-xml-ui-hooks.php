<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TM_Custom_XML_UI_Hooks {
	private $ajax;
	private $resources;
	private $ui;

	function __construct( WPML_TM_Custom_XML_UI $ui, WPML_TM_Custom_XML_UI_Resources $resources, WPML_TM_Custom_XML_AJAX $ajax ) {
		$this->ui        = $ui;
		$this->resources = $resources;
		$this->ajax      = $ajax;
	}

	function init() {
		add_filter( 'wpml_tm_tab_items', array( $this, 'add_items' ) );
		add_action( 'admin_enqueue_scripts', array( $this->resources, 'admin_enqueue_scripts' ) );
		add_action( 'wp_ajax_' . WPML_TM_Custom_XML_AJAX::AJAX_ACTION_BASE . '-validate', array( $this->ajax, 'validate_content' ) );
		add_action( 'wp_ajax_' . WPML_TM_Custom_XML_AJAX::AJAX_ACTION_BASE . '-save', array( $this->ajax, 'save_content' ) );
	}

	function add_items( $tab_items ) {
		$tab_items['custom-xml-config']['caption']          = __( 'Custom XML Configuration', 'wpml-translation-management' );
		$tab_items['custom-xml-config']['callback']         = array( $this, 'build_content' );
		$tab_items['custom-xml-config']['current_user_can'] = 'manage_options';

		return $tab_items;
	}

	public function build_content() {
		echo $this->ui->show();
	}
}
