<?php

function icl_reset_wpml( $blog_id = false ) {
	global $wpdb, $sitepress_settings;

	if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'resetwpml' ) {
		check_admin_referer( 'resetwpml' );
	}

	if ( empty( $blog_id ) ) {
	    $filtered_id = filter_input( INPUT_POST, 'id', FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_NULL_ON_FAILURE );
		$filtered_id = $filtered_id ? $filtered_id : filter_input( INPUT_GET, 'id', FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_NULL_ON_FAILURE );
        $blog_id = $filtered_id !== false ? $filtered_id : $wpdb->blogid;
	}

	if ( $blog_id || ! function_exists( 'is_multisite' ) || ! is_multisite() ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			switch_to_blog( $blog_id );
		}

		do_action( 'wpml_reset_plugins_before' );

		wp_clear_scheduled_hook( 'update_wpml_config_index' );

		$icl_tables = array(
			$wpdb->prefix . 'icl_languages',
			$wpdb->prefix . 'icl_languages_translations',
			$wpdb->prefix . 'icl_translations',
			$wpdb->prefix . 'icl_translation_status',
			$wpdb->prefix . 'icl_translate_job',
			$wpdb->prefix . 'icl_translate',
			$wpdb->prefix . 'icl_locale_map',
			$wpdb->prefix . 'icl_flags',
			$wpdb->prefix . 'icl_content_status',
			$wpdb->prefix . 'icl_core_status',
			$wpdb->prefix . 'icl_node',
			$wpdb->prefix . 'icl_strings',
			$wpdb->prefix . 'icl_string_packages',
			$wpdb->prefix . 'icl_translation_batches',
			$wpdb->prefix . 'icl_string_translations',
			$wpdb->prefix . 'icl_string_status',
			$wpdb->prefix . 'icl_string_positions',
			$wpdb->prefix . 'icl_message_status',
			$wpdb->prefix . 'icl_reminders',
			$wpdb->prefix . 'icl_mo_files_domains',
			$wpdb->prefix . 'icl_string_pages',
			$wpdb->prefix . 'icl_string_urls',
			$wpdb->prefix . 'icl_cms_nav_cache',
		);
		$icl_tables = apply_filters( 'wpml_reset_tables', $icl_tables, $blog_id );

		foreach ( $icl_tables as $icl_table ) {
			$wpdb->query( "DROP TABLE IF EXISTS " . $icl_table );
		}

		$wpml_options = array(
			'icl_sitepress_settings',
			'icl_sitepress_version',
			'_icl_cache',
			'_icl_admin_option_names',
			'wp_icl_translators_cached',
			'wpml32_icl_non_translators_cached',
			'wpml-package-translation-db-updates-run',
			'wpml-package-translation-refresh-required',
			'wpml-package-translation-string-packages-table-updated',
			'wpml-package-translation-string-table-updated',
			'icl_translation_jobs_basket',
			'widget_icl_lang_sel_widget',
			'icl_admin_messages',
			'icl_adl_settings',
			'wpml_tp_com_log',
			'wpml_config_index',
			'wpml_config_index_updated',
			'wpml_config_files_arr',
			'wpml_language_switcher',
			'wpml_notices',
			'wpml_start_version',
			'wpml_dependencies:installed_plugins',
			'wpml_translation_services',
			'wpml_update_statuses',
			'_wpml_dismissed_notices',
			'wpml_translation_services_timestamp',
			'wpml_string_table_ok_for_mo_import',
			'wpml-charset-validation',
			'_wpml_media',
			'wpml_st_display_strings_scan_notices',
			'wpml-st-all-strings-are-in-english',
			'wpml_strings_need_links_fixed',
			'_wpml_batch_report',
			'wpml_cms_nav_settings',
			'WPML_CMS_NAV_VERSION',
			'icl_st_settings',
			'wpml-tm-custom-xml',
			'wpml-st-persist-errors',
			'wpml_base_slug_translation',
		);
		$wpml_options = apply_filters( 'wpml_reset_options', $wpml_options, $blog_id );

		foreach ( $wpml_options as $wpml_option ) {
			delete_option( $wpml_option );
		}

		$wpml_user_options = array(
			'language_pairs'
		);
		$wpml_user_options = apply_filters( 'wpml_reset_user_options', $wpml_user_options, $blog_id );
		if ( $wpml_user_options ) {
			foreach ( $wpml_user_options as $wpml_user_option ) {

				$meta_key = $wpdb->get_blog_prefix( $blog_id ) . $wpml_user_option;
				$users    = get_users( array(
					'blog_id'  => $blog_id,
					'meta_key' => $meta_key,
					'fields'   => array( 'ID' ),
				) );

				/** @var WP_User $user */
				foreach ( $users as $user ) {
					delete_user_option( $user->ID, $wpml_user_option );
				}
			}
		}

		$capabilities = array(
			'wpml_manage_translation_management',
			'wpml_manage_languages',
			'wpml_manage_theme_and_plugin_localization',
			'wpml_manage_support',
			'wpml_manage_woocommerce_multilingual',
			'wpml_operate_woocommerce_multilingual',
			'wpml_manage_media_translation',
			'wpml_manage_navigation',
			'wpml_manage_sticky_links',
			'wpml_manage_string_translation',
			'wpml_manage_translation_analytics',
			'wpml_manage_wp_menus_sync',
			'wpml_manage_taxonomy_translation',
			'wpml_manage_troubleshooting',
			'wpml_manage_translation_options',
			'manage_translations',
			'translate',
		);

		$capabilities = apply_filters( 'wpml_reset_user_capabilities', $capabilities, $blog_id );
		if ( $capabilities ) {
			$users = get_users( array(
				'blog_id' => $blog_id,
			) );

			/** @var WP_User $user */
			foreach ( $users as $user ) {
				foreach ( $capabilities as $capability ) {
					$user->remove_cap( $capability );
				}
			}
		}

		$sitepress_settings = null;
		wp_cache_init();

		$wpml_cache_directory = new WPML_Cache_Directory( new WPML_WP_API() );
		$wpml_cache_directory->remove();

		do_action( 'wpml_reset_plugins_after' );
		
		$wpmu_sitewide_plugins = (array) maybe_unserialize( get_site_option( 'active_sitewide_plugins' ) );
		if ( ! isset( $wpmu_sitewide_plugins[ WPML_PLUGIN_BASENAME ] ) ) {
			remove_action( 'deactivate_' . WPML_PLUGIN_BASENAME, 'icl_sitepress_deactivate' );
			deactivate_plugins( WPML_PLUGIN_BASENAME );
			$ra                                                   = get_option( 'recently_activated' );
			$ra[ WPML_PLUGIN_BASENAME ] = time();
			update_option( 'recently_activated', $ra );
		} else {
			update_option( '_wpml_inactive', true );
		}

		$options_to_delete_after_deactivation = array(
			'wpml_dependencies:needs_validation',
			'wpml_dependencies:valid_plugins',
			'wpml_dependencies:invalid_plugins',
		);
		$options_to_delete_after_deactivation = apply_filters( 'wpml_reset_options_after_deactivation', $options_to_delete_after_deactivation, $blog_id );

		foreach ( $options_to_delete_after_deactivation as $option ) {
			delete_option( $option );
		}

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			restore_current_blog();
		}
	}
}

/**
 * Ajax handler for type assignment fix troubleshoot action
 */
function icl_repair_broken_type_and_language_assignments() {
	global $sitepress;

	$lang_setter = new WPML_Fix_Type_Assignments( $sitepress );
	$rows_fixed  = $lang_setter->run();

	wp_send_json_success( $rows_fixed );
}