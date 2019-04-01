<?php
/**
 * @author OnTheGo Systems
 */
class WPML_Support_Info_UI_Factory {
	function create() {
		global $wpdb;
		$support_info = new WPML_Support_Info( $wpdb );

		$template_paths   = array(
			WPML_PLUGIN_PATH . '/templates/support/info/',
		);

		$template_loader = new WPML_Twig_Template_Loader( $template_paths );
		$template_service = $template_loader->get_template();

		return new WPML_Support_Info_UI( $support_info, $template_service );
	}
}