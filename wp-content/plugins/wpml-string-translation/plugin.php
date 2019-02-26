<?php
/*
Plugin Name: WPML String Translation
Plugin URI: https://wpml.org/
Description: Adds theme and plugins localization capabilities to WPML | <a href="https://wpml.org">Documentation</a> | <a href="https://wpml.org/version/string-translation-2-9-2/">WPML String Translation 2.9.2 release notes</a>
Author: OnTheGoSystems
Author URI: http://www.onthegosystems.com/
Version: 2.9.2
Plugin Slug: wpml-string-translation
*/

if ( defined( 'WPML_ST_VERSION' ) || get_option( '_wpml_inactive' ) ) {
	return;
}

define( 'WPML_ST_VERSION', '2.9.2' );

// Do not uncomment the following line!
// If you need to use this constant, use it in the wp-config.php file
//define( 'WPML_PT_VERSION_DEV', '2.2.3-dev' );
define( 'WPML_ST_PATH', dirname( __FILE__ ) );

$autoloader_dir = WPML_ST_PATH . '/vendor';
if ( version_compare( PHP_VERSION, '5.3.0' ) >= 0 ) {
	$autoloader = $autoloader_dir . '/autoload.php';
} else {
	$autoloader = $autoloader_dir . '/autoload_52.php';
}
require_once $autoloader;

add_action( 'admin_init', 'wpml_st_verify_wpml' );
function wpml_st_verify_wpml() {
	$verifier     = new WPML_ST_Verify_Dependencies();
	$wpml_version = defined( 'ICL_SITEPRESS_VERSION' ) ? ICL_SITEPRESS_VERSION : false;
	$verifier->verify_wpml( $wpml_version );
}

function wpml_st_core_loaded() {
	global $WPML_String_Translation, $st_gettext_hooks, $sitepress, $wpdb, $wpml_admin_notices;
	new WPML_ST_TM_Jobs( $wpdb );

	$setup_complete = apply_filters( 'wpml_get_setting', false, 'setup_complete' );
	$theme_localization_type = new WPML_Theme_Localization_Type( $sitepress );
	$is_admin = $sitepress->get_wp_api()->is_admin();

	$fastest_settings = new WPML_ST_Fastest_Settings_Notice( $sitepress, $wpml_admin_notices ? $wpml_admin_notices : wpml_get_admin_notices() );
	$fastest_settings->remove();

	if ( isset( $wpml_admin_notices ) && $theme_localization_type->is_st_type() && $is_admin && $setup_complete ) {
		global $wpml_st_admin_notices;
		$themes_and_plugins_settings = new WPML_ST_Themes_And_Plugins_Settings();
		$wpml_st_admin_notices = new WPML_ST_Themes_And_Plugins_Updates( $wpml_admin_notices, $themes_and_plugins_settings, $fastest_settings );
		$wpml_st_admin_notices->init_hooks();
	}

	$pb_plugin = new WPML_ST_PB_Plugin();
	if ( $pb_plugin->is_active() ) {
		$pb_plugin->ask_to_deactivate();
	} else {
		$app = new WPML_Page_Builders_App( new WPML_Page_Builders_Defined() );
		$app->add_hooks();

		$st_settings = new WPML_ST_Settings();
		new WPML_PB_Loader( $sitepress, $wpdb, $st_settings );
	}

	$actions = array(
		'WPML_ST_Theme_Plugin_Localization_Resources_Factory',
		'WPML_ST_Theme_Plugin_Localization_Options_UI_Factory',
		'WPML_ST_Theme_Plugin_Localization_Options_Settings_Factory',
		'WPML_ST_Theme_Plugin_Scan_Dir_Ajax_Factory',
		'WPML_ST_Theme_Plugin_Scan_Files_Ajax_Factory',
		'WPML_ST_Update_File_Hash_Ajax_Factory',
		'WPML_ST_Options_All_Strings_English_Factory',
		'WPML_ST_Theme_Plugin_Hooks_Factory',
		'WPML_ST_Track_Strings_Notice_Hooks_Factory',
		'WPML_ST_Taxonomy_Labels_Translation_Factory',
		'WPML_ST_String_Translation_AJAX_Hooks_Factory',
		'WPML_ST_Remote_String_Translation_Factory',
		'WPML_ST_Privacy_Content_Factory',
		'WPML_ST_Multisite_Filters_Cleaner_Factory',
		'WPML_ST_String_Tracking_AJAX_Factory',
		'WPML_ST_Translation_Memory_Factory',
	);

	$action_filter_loader = new WPML_Action_Filter_Loader();
	$action_filter_loader->load( $actions );

	$st_gettext_hooks_factory = new WPML_ST_Gettext_Hooks_Factory( $sitepress, $WPML_String_Translation, $theme_localization_type->is_st_type() );
	$st_gettext_hooks = $st_gettext_hooks_factory->create();

	$st_gettext_hooks->init_hooks();
}

function load_wpml_st_basics() {
	if ( ! WPML_Core_Version_Check::is_ok( dirname( __FILE__ ) . '/wpml-dependencies.json' ) ) {
		return;
	}

	global $WPML_String_Translation, $wpdb, $wpml_st_string_factory, $sitepress;

	$wpml_st_string_factory = new WPML_ST_String_Factory( $wpdb );

	require WPML_ST_PATH . '/inc/functions-load.php';
	require WPML_ST_PATH . '/inc/wpml-string-translation.class.php';
	require WPML_ST_PATH . '/inc/constants.php';

	$WPML_String_Translation = new WPML_String_Translation( $sitepress, $wpml_st_string_factory );
	$WPML_String_Translation->set_basic_hooks();

	require WPML_ST_PATH . '/inc/package-translation/wpml-package-translation.php';

	add_action( 'wpml_loaded', 'wpml_st_core_loaded', 10 );

	$troubleshooting = new WPML_ST_DB_Troubleshooting();
	$troubleshooting->add_hooks();

	$st_theme_localization_type = new WPML_ST_Theme_Localization_Type( $wpdb );
	$st_theme_localization_type->add_hooks();

	if ( $sitepress->is_setup_complete() ) {
		$mo_scan_factory = new WPML_ST_MO_Scan_Factory();

		if ( $mo_scan_factory->check_core_dependencies() ) {
			$mo_scan_hooks = $mo_scan_factory->create_hooks();
			foreach ( $mo_scan_hooks as $mo_scan_hook ) {
				$mo_scan_hook->add_hooks();
			}
		}
	}
}

add_action( 'wpml_before_init', 'load_wpml_st_basics' );

/**
 * @param array $blocks
 *
 * @return array
 */
function wpml_st_support_info( array $blocks ) {
	$support_info = new WPML_ST_Support_Info();

	$ui = new WPML_ST_Support_Info_Filter( $support_info );

	return $ui->filter_blocks( $blocks );
}

/** This filter is documented WPML Core in classes/support/class-wpml-support-info-ui.php */
add_filter( 'wpml_support_info_blocks', 'wpml_st_support_info' );
