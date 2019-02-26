<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * @since 4.4 vendors initialization moved to hooks in autoload/vendors.
 *
 * Used to initialize plugin mqtranslate vendor
 */
add_action( 'plugins_loaded', 'vc_init_vendor_mqtranslate' );
function vc_init_vendor_mqtranslate() {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); // Require plugin.php to use is_plugin_active() below
	if ( is_plugin_active( 'mqtranslate/mqtranslate.php' ) || function_exists( 'mqtranslate_activation_check' ) ) {
		require_once vc_path_dir( 'VENDORS_DIR', 'plugins/class-vc-vendor-mqtranslate.php' );
		$vendor = new Vc_Vendor_Mqtranslate();
		add_action( 'vc_after_set_mode', array(
			$vendor,
			'load',
		) );
	}
}
