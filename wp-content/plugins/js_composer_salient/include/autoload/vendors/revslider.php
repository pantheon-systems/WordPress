<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * @since 4.4 vendors initialization moved to hooks in autoload/vendors.
 *
 * Used to initialize plugin revslider vendor.
 */
add_action( 'plugins_loaded', 'vc_init_vendor_revslider' );
function vc_init_vendor_revslider() {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); // Require plugin.php to use is_plugin_active() below
	if ( is_plugin_active( 'revslider/revslider.php' ) || class_exists( 'RevSlider' ) ) {
		require_once vc_path_dir( 'VENDORS_DIR', 'plugins/class-vc-vendor-revslider.php' );
		$vendor = new Vc_Vendor_Revslider();
		$vendor->load();
	}
}
