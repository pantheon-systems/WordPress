<?php

/**
 * @author OnTheGo Systems
 */
class WPML_XML_Config_Log_Factory {
	private $log;

	function create_log() {
		if ( ! $this->log ) {
			$this->log = new WPML_Config_Update_Log();
		}

		return $this->log;
	}

	function create_ui() {
		$template_paths = array(
			WPML_PLUGIN_PATH . '/templates/xml-config/log/',
		);

		$template_loader  = new WPML_Twig_Template_Loader( $template_paths );
		$template_service = $template_loader->get_template();

		return new WPML_XML_Config_Log_UI( $this->create_log(), $template_service );
	}

	function create_notice() {
		return new WPML_XML_Config_Log_Notice( $this->create_log() );
	}
}