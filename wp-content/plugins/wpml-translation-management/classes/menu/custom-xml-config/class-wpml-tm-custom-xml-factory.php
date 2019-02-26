<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TM_Custom_XML_Factory {

	private $custom_xml;

	function __construct() {
		$this->custom_xml = new WPML_Custom_XML();
	}

	public function create_ui() {
		$template_paths = array(
			WPML_TM_PATH . '/templates/custom-xml/',
		);

		$template_loader  = new WPML_Twig_Template_Loader( $template_paths );

		return new WPML_TM_Custom_XML_UI( $this->custom_xml, $template_loader->get_template() );
	}

	public function create_resources( WPML_WP_API $wpml_wp_api ) {
		return new WPML_TM_Custom_XML_UI_Resources( $wpml_wp_api );
	}

	public function create_ajax() {
		return new WPML_TM_Custom_XML_AJAX(
			$this->custom_xml,
			new WPML_XML_Config_Validate( WPML_PLUGIN_PATH . '/res/xsd/wpml-config.xsd' ),
			array( 'WPML_Config', 'load_config_run' )
		);
	}
}
