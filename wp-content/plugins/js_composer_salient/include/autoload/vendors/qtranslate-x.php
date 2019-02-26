<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * @since 4.4 vendors initialization moved to hooks in autoload/vendors.
 *
 * Used to initialize plugin qtranslate vendor.
 */
add_action( 'plugins_loaded', 'vc_init_vendor_qtranslatex' );
function vc_init_vendor_qtranslatex() {
	if ( defined( 'QTX_VERSION' ) ) {
		require_once vc_path_dir( 'VENDORS_DIR', 'plugins/class-vc-vendor-qtranslate-x.php' );
		$vendor = new Vc_Vendor_QtranslateX();
		add_action( 'vc_after_set_mode', array(
			$vendor,
			'load',
		) );
	}
}
