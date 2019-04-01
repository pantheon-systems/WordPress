<?php
/*
Plugin Name: WPML Translation Management
Plugin URI: https://wpml.org/
Description: Add a complete translation process for WPML | <a href="https://wpml.org">Documentation</a> | <a href="https://wpml.org/version/translation-management-2-7-3/">WPML Translation Management 2.7.3 release notes</a>
Author: OnTheGoSystems
Author URI: http://www.onthegosystems.com/
Version: 2.7.3
Plugin Slug: wpml-translation-management
*/

if ( defined( 'WPML_TM_VERSION' ) || get_option( '_wpml_inactive' ) ) {
	return;
}

define( 'WPML_TM_VERSION', '2.7.3' );

// Do not uncomment the following line!
// If you need to use this constant, use it in the wp-config.php file
//define( 'WPML_TM_DEV_VERSION', '2.0.3-dev' );

if ( ! defined( 'WPML_TM_PATH' ) ) {
    define('WPML_TM_PATH', dirname(__FILE__));
}

$autoloader_dir = WPML_TM_PATH . '/vendor';
if ( version_compare( PHP_VERSION, '5.3.0' ) >= 0 ) {
	$autoloader = $autoloader_dir . '/autoload.php';
} else {
	$autoloader = $autoloader_dir . '/autoload_52.php';
}
require_once $autoloader;

require_once WPML_TM_PATH . '/inc/constants.php';
require_once WPML_TM_PATH . '/inc/translation-proxy/wpml-pro-translation.class.php';
require_once WPML_TM_PATH . '/inc/functions-load.php';
require_once WPML_TM_PATH . '/inc/js-scripts.php';

new WPML_TM_Requirements();

function wpml_tm_load( $sitepress = null ) {
	if ( ! WPML_Core_Version_Check::is_ok( dirname( __FILE__ ) . '/wpml-dependencies.json' ) ) {
		return;
	}

	if ( ! $sitepress ) {
		global $sitepress;
	}

	if ( ! $sitepress || ! $sitepress instanceof SitePress || ! $sitepress->is_setup_complete() ) {
		return;
	}

	require_once WPML_TM_PATH . '/menu/basket-tab/sitepress-table-basket.class.php';
	require_once WPML_TM_PATH . '/menu/dashboard/wpml-tm-dashboard.class.php';
	require_once WPML_TM_PATH . '/menu/wpml-tm-menus.class.php';

	$action_filter_loader = new WPML_Action_Filter_Loader();

	if ( version_compare( ICL_SITEPRESS_VERSION, '3.3.1', '>=' ) ) {
		global $wpdb, $WPML_Translation_Management, $ICL_Pro_Translation;

		$core_translation_management = wpml_load_core_tm();
		$tm_loader                   = new WPML_TM_Loader();
		$wpml_tp_translator          = new WPML_TP_Translator();
		$WPML_Translation_Management = new WPML_Translation_Management( $sitepress, $tm_loader, $core_translation_management, $wpml_tp_translator );
		$WPML_Translation_Management->init();
		$WPML_Translation_Management->load();

		if ( ! $ICL_Pro_Translation ) {
			$job_factory         = wpml_tm_load_job_factory();
			$ICL_Pro_Translation = new WPML_Pro_Translation( $job_factory );
		}

		if ( is_admin() ) {
			$wpml_wp_api      = new WPML_WP_API();
			$TranslationProxy = new WPML_Translation_Proxy_API();
			new WPML_TM_Troubleshooting_Reset_Pro_Trans_Config( $sitepress, $TranslationProxy, $wpml_wp_api, $wpdb );
			new WPML_TM_Troubleshooting_Clear_TS( $wpml_wp_api );
			new WPML_TM_Promotions( $wpml_wp_api );

			if ( defined( 'DOING_AJAX' ) ) {
				$wpml_tm_options_ajax = new WPML_TM_Options_Ajax( $sitepress );
				$wpml_tm_options_ajax->ajax_hooks();

				$wpml_tm_pickup_mode_ajax = new WPML_TM_Pickup_Mode_Ajax( $sitepress, $ICL_Pro_Translation );
				$wpml_tm_pickup_mode_ajax->ajax_hooks();
			}
		}

		if ( class_exists( 'WPML_TF_Settings_Read' ) ) {
			$tf_settings_read = new WPML_TF_Settings_Read();
			/** @var WPML_TF_Settings $tf_settings */
			$tf_settings = $tf_settings_read->get( 'WPML_TF_Settings' );
			$translation_feedback_module = new WPML_TM_TF_Module( $action_filter_loader, $tf_settings );
			$translation_feedback_module->run();
		}

		$action_filter_loader->load( array(
			                             'WPML_TM_Jobs_Deadline_Estimate_AJAX_Action_Factory',
			                             'WPML_TM_Jobs_Deadline_Cron_Hooks_Factory',
			                             'WPML_TM_Emails_Settings_Factory',
			                             'WPML_TM_Jobs_Summary_Report_Hooks_Factory',
		));
	}

	$actions = array(
		'WPML_TM_Translation_Services_Admin_Section_Resources_Factory',
		'WPML_TM_Translation_Services_Admin_Section_Ajax_Factory',
		'WPML_TM_Translation_Service_Authentication_Ajax_Factory',
		'WPML_TM_Translation_Services_Refresh_Services_Factory',
		'WPML_TM_Default_Settings_Factory',
		'WPML_TP_Lock_Notice_Factory',
		'WPML_TM_API_Hooks_Factory',
		'WPML_TM_Parent_Filter_Ajax_Factory',
		'WPML_TM_Upgrade_Loader_Factory',
		'WPML_TM_Translation_Priorities_Factory',
		'WPML_Upgrade_Admins_To_Manage_Translations_Factory',
		'WPML_Translation_Roles_Ajax_Factory',
		'WPML_TM_Wizard_Steps_Factory',
		'WPML_TM_Translation_Basket_Hooks_Factory',
		'WPML_TM_Word_Count_Hooks_Factory',
		'WPML_TM_Admin_Menus_Factory',
		'WPML_TM_Privacy_Content_Factory',
		'WPML_TM_ATE_Translator_Login_Factory',
		'WPML_TM_Serialized_Custom_Field_Package_Handler_Factory',
		'WPML_TM_MCS_Pagination_Ajax_Factory',
	);
	$action_filter_loader->load( $actions );

	$rest_actions = array(
		'WPML_TM_REST_ATE_Jobs_Factory',
		'WPML_TM_REST_XLIFF_Factory',
		'WPML_TM_REST_AMS_Clients_Factory',
		'WPML_TM_REST_ATE_API_Factory',
		'WPML_TM_REST_Jobs_Factory',
		'WPML_TM_REST_ATE_Public_Factory',
		'WPML_TM_REST_Settings_Translation_Editor_Factory',
	);
	$action_filter_loader->load( $rest_actions );

	$ams_ate_actions = array(
		'WPML_TM_AMS_Synchronize_Actions_Factory',
		'WPML_TM_ATE_Jobs_Store_Actions_Factory',
		'WPML_TM_ATE_Jobs_Actions_Factory',
		'WPML_TM_ATE_Post_Edit_Actions_Factory',
		'WPML_TM_ATE_Translator_Message_Classic_Editor_Factory',
	);
	$action_filter_loader->load( $ams_ate_actions );
}

add_action( 'wpml_loaded', 'wpml_tm_load', 10, 1 );

/**
 * @param array $blocks
 *
 * @return array
 */
function wpml_tm_support_info( array $blocks ) {
	$support_info = new WPML_TM_Support_Info();

	$ui = new WPML_TM_Support_Info_Filter( $support_info );

	return $ui->filter_blocks( $blocks );
}

/** This filter is documented WPML Core in classes/support/class-wpml-support-info-ui.php */
add_filter( 'wpml_support_info_blocks', 'wpml_tm_support_info' );

function wpml_tm_icl20_migration() {
	//@todo Remove `|| ( defined( 'WPML_TP_ICL_20_MIGRATION_OFF' ) && WPML_TP_ICL_20_MIGRATION_OFF )` after testing?
	if ( defined( 'WPML_TP_ICL_20_MIGRATION_OFF' )
	     && WPML_TP_ICL_20_MIGRATION_OFF ) {
		return;
	}

	global $sitepress;
	$loader = new WPML_TM_ICL20_Migration_Loader($sitepress->get_wp_api(), new WPML_TM_ICL20_Migration_Factory());
	$loader->run();
}

if ( ! empty( $GLOBALS['sitepress'] ) && is_admin() ) {
	add_action( 'wpml_tm_loaded', 'wpml_tm_icl20_migration' );
}

add_filter( 'wpml_reset_options', 'wpml_tm_reset_options' );
function wpml_tm_reset_options( array $options ) {
	$options[] = WPML_TM_ATE_Job_Records::WPML_TM_ATE_JOB_RECORDS;
	$options[] = WPML_TM_ATE_Authentication::AMS_DATA_KEY;
	$options[] = WPML_Upgrade_Admins_To_Manage_Translations_Factory::HAS_RUN_OPTION;
	$options[] = WPML_TM_Wizard_For_Manager_Options::WIZARD_COMPLETE;
	$options[] = WPML_TM_Wizard_For_Manager_Options::CURRENT_STEP;

	return $options;
}

add_filter( 'wpml_reset_user_options', 'wpml_tm_reset_user_options' );
function wpml_tm_reset_user_options( array $options ) {
	$options[] = WPML_TM_Menus_Management::SKIP_TM_WIZARD_META_KEY;

	return $options;
}
