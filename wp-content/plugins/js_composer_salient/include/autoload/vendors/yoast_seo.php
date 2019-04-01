<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * @since 4.4 vendors initialization moved to hooks in autoload/vendors.
 *
 * Used to initialize plugin yoast vendor.
 */
// 16 is required to be called after WPSEO_Admin_Init constructor. @since 4.9
add_action( 'plugins_loaded', 'vc_init_vendor_yoast', 16 );
// add_action( 'plugins_loaded', 'vc_init_vendor_yoast_reset_page_now', 16 );
function vc_init_vendor_yoast() {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); // Require plugin.php to use is_plugin_active() below
	if ( is_plugin_active( 'wordpress-seo/wp-seo.php' ) || class_exists( 'WPSEO_Metabox' ) ) {
		require_once vc_path_dir( 'VENDORS_DIR', 'plugins/class-vc-vendor-yoast_seo.php' );
		$vendor = new Vc_Vendor_YoastSeo();
		if ( defined( 'WPSEO_VERSION' ) && version_compare( WPSEO_VERSION, '3.0.0' ) === - 1 ) {
			add_action( 'vc_after_set_mode', array(
				$vendor,
				'load',
			) );
		} elseif ( is_admin() && 'vc_inline' === vc_action() ) {
			// $GLOBALS['pagenow'] = 'post.php?vc_action=vc_inline';
			$vendor->frontendEditorBuild();
		}
	}
}
/*function vc_init_vendor_yoast_reset_page_now() {
	$GLOBALS['pagenow'] = 'post.php';

}*/
