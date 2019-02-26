<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * @since 4.4 vendors initialization moved to hooks in autoload/vendors.
 *
 * Used to initialize plugin jwplayer vendor for frontend editor.
 */
add_action( 'plugins_loaded', 'vc_init_vendor_jwplayer' );
function vc_init_vendor_jwplayer() {
	if ( is_plugin_active( 'jw-player-plugin-for-wordpress/jwplayermodule.php' ) || defined( 'JWP6' ) || class_exists( 'JWP6_Plugin' ) ) {
		require_once vc_path_dir( 'VENDORS_DIR', 'plugins/class-vc-vendor-jwplayer.php' );
		$vendor = new Vc_Vendor_Jwplayer();
		$vendor->load();
	}
}
