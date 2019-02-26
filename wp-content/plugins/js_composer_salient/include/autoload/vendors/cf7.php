<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * @since 4.4 vendors initialization moved to hooks in autoload/vendors.
 *
 * Used to initialize plugin contact form 7 vendor - fix load cf7 shortcode when in editor (frontend)
 */
add_action( 'plugins_loaded', 'vc_init_vendor_cf7' );
function vc_init_vendor_cf7() {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); // Require plugin.php to use is_plugin_active() below
	if ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) || defined( 'WPCF7_PLUGIN' ) ) {
		require_once vc_path_dir( 'VENDORS_DIR', 'plugins/class-vc-vendor-contact-form7.php' );
		$vendor = new Vc_Vendor_ContactForm7();
		add_action( 'vc_after_set_mode', array(
			$vendor,
			'load',
		) );
	} // if contact form7 plugin active
}
