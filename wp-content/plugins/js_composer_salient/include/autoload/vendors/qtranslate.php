<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * @since 4.4 vendors initialization moved to hooks in autoload/vendors.
 *
 * Used to initialize plugin qtranslate vendor.
 */
add_action( 'plugins_loaded', 'vc_init_vendor_qtranslate' );
function vc_init_vendor_qtranslate() {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); // Require plugin.php to use is_plugin_active() below
	if ( is_plugin_active( 'qtranslate/qtranslate.php' ) || defined( 'QT_SUPPORTED_WP_VERSION' ) ) {
		require_once vc_path_dir( 'VENDORS_DIR', 'plugins/class-vc-vendor-qtranslate.php' );
		$vendor = new Vc_Vendor_Qtranslate();
		add_action( 'vc_after_set_mode', array(
			$vendor,
			'load',
		) );
	}
}
