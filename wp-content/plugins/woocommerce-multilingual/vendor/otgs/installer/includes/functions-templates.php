<?php

// Ext function
function WP_Installer_Show_Products($args = array()){
	WP_Installer()->show_products($args);
}

function WP_Installer_get_local_components_setting_ui( $args ) {
	$installer_factory = get_OTGS_Installer_Factory();

	ob_start();
	$installer_factory->create_settings_hooks()
					  ->render_local_components_setting( $args );

	return ob_get_clean();
}