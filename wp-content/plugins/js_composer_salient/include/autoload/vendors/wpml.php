<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

add_action( 'plugins_loaded', 'vc_init_vendor_wpml' );
function vc_init_vendor_wpml() {
	if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
		require_once vc_path_dir( 'VENDORS_DIR', 'plugins/class-vc-vendor-wpml.php' );
		$vendor = new Vc_Vendor_WPML();
		add_action( 'vc_after_set_mode', array(
			$vendor,
			'load',
		) );
	}
}
