<?php

/**
 * This class provides the list of system status values
 *
 */

/**
 * Show list of system critical data
 *
 * @since   1.0.0
 * @ignore
 * @access  private
 */
if (!class_exists('OceanWP_Theme_Panel_System_Status')) {
	class OceanWP_Theme_Panel_System_Status
	{
		/**
		 * OceanWP_Theme_Panel_System_Status constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct()
		{
			add_action('wp_ajax_oceanwp_cp_system_status', array($this, 'ajax_handler'));
			// add_filter('oceanwp_control_panel_pane_system_status', [$this, 'view']);
			// add_action('wp_ajax_oceanwp_cp_cleanup_mods', [$this, 'cleanup_mods']);
		}

		/**
		 * Handles AJAX requests.
		 *
		 * @since 1.3.0
		 */
		public function ajax_handler()
		{
			OceanWP_Theme_Panel::check_ajax_access( $_REQUEST['nonce'], 'oceanwp_theme_panel' );

			$type = $_POST['type'];

			if (!$type) {
				wp_send_json_error(esc_html__('Type param is missing.', 'ocean-extra'));
			}

			$this->$type();

			wp_send_json_error(
				sprintf(esc_html__('Type param (%s) is not valid.', 'ocean-extra'), $type)
			);
		}


		/**
		 * Load system status.
		 *
		 * @since 1.9.0
		 *
		 * @return string
		 */
		// public function view() {
		// 	return oceanwp_core()->plugin_dir() . 'includes/control-panel/views/system-status.php';
		// }

		/**
		 * Clean up theme mods data and languages specific theme mods.
		 * This will removes all unwanted data (integer keys) from theme mods in multiple requests to avoid timeout.
		 *
		 * @since 1.4.0
		 *
		 * @return void
		 */
		// public function cleanup_mods() {
		// 	$theme_slug = get_option( 'stylesheet' );

		// 	if ( ! wp_verify_nonce( $_POST['nonce'], 'oceanwp_mods_cleanup' ) ) {
		// 		wp_send_json_error( [ 'message' => __( 'Nonce can\'t be verified', 'jupiterx-core' ) ] );
		// 	}

		// 	$mods      = get_option( 'theme_mods_' . $theme_slug );
		// 	$mods_size = sizeof( $mods );

		// 	$i = 0; // Index.
		// 	$j = 0; // Numeric keys.
		// 	foreach ( $mods as $key => $value ) {
		// 		if ( is_numeric( $key ) ) {
		// 			unset( $mods[ $key ] );
		// 			$j++;
		// 		}

		// 		$i++;
		// 		// Remove bunch of unwanted data.
		// 		if ( $j >= 1000 ) {
		// 			update_option( 'theme_mods_' . $theme_slug, $mods );
		// 			wp_send_json_success();
		// 		// If unwanted data length is less than 1000, remove all of them.
		// 		} elseif ( $i >= $mods_size ) {
		// 			update_option( 'theme_mods_' . $theme_slug, $mods );
		// 		}
		// 	}

		// 	$multilingual  = new CoreCustomizerMultilingual();
		// 	$theme_slug    = get_option( 'template' );
		// 	$option_prefix = str_replace( '-', '_', $theme_slug );
		// 	$option_name   = $option_prefix . $multilingual::get_option_key();
		// 	$languages     = $multilingual::get_languages_list();
		// 	$k = 0; // Index.
		// 	$l = 0; // Numeric keys.
		// 	foreach ( $languages as $language ) {
		// 		$lang_option_name = $option_name . $language['slug'];
		// 		$lang_option      = get_option( $lang_option_name );
		// 		$lang_mods        = isset( $lang_option['mods'] ) ? $lang_option['mods'] : [];
		// 		$lang_mode_size   = sizeof( $lang_mods );

		// 		foreach ( $lang_mods as $key => $value ) {
		// 			if ( is_numeric( $key ) ) {
		// 				unset( $lang_mods[ $key ] );
		// 				$l++;
		// 			}

		// 			$k++;
		// 			// Remove bunch of unwanted data.
		// 			if ( $l >= 1000 ) {
		// 				$lang_option['mods'] = $lang_mods;
		// 				update_option( $lang_option_name, $lang_option );
		// 				wp_send_json_success();
		// 			// If unwanted data length is less than 1000, remove all of them.
		// 			} elseif( $k >= $lang_mode_size ) {
		// 				$lang_option['mods'] = $lang_mods;
		// 				update_option( $lang_option_name, $lang_option );
		// 				wp_send_json_error();
		// 			}
		// 		}
		// 	}

		// 	wp_send_json_error();
		// }

		/**
		 * Checks whether HTTP requests are blocked.
		 *
		 * @see test_http_requests() in Health Check plugin.
		 * @since 1.3.0
		 */
		private function http_requests()
		{
			$blocked = false;
			$hosts   = [];

			if (defined('WP_HTTP_BLOCK_EXTERNAL')) {
				$blocked = true;
			}

			if (defined('WP_ACCESSIBLE_HOSTS')) {
				$hosts = explode(',', WP_ACCESSIBLE_HOSTS);
			}

			if ($blocked && 0 === sizeof($hosts)) {
				wp_send_json_error(esc_html__('HTTP requests have been blocked by the WP_HTTP_BLOCK_EXTERNAL constant, with no allowed hosts.', 'ocean-extra'));
			}

			if ($blocked && 0 < sizeof($hosts)) {
				wp_send_json_error(
					sprintf(
						esc_html__('HTTP requests have been blocked by the WP_HTTP_BLOCK_EXTERNAL constant, with some hosts whitelisted: %s.', 'ocean-extra'),
						implode(',', $hosts)
					)
				);
			}

			if (!$blocked) {
				wp_send_json_success();
			}
		}

		/**
		 * Checks whether artbees.net is accessible.
		 *
		 * @since 1.3.0
		 */
		private function oceanwp_server()
		{
			$response = wp_remote_get('https://oceanwp.org', array(
				'timeout' => 10,
			));

			if (is_wp_error($response)) {
				wp_send_json_error($response->get_error_message());
			}

			wp_send_json_success();
		}

		/**
		 * Create an array of system status
		 *
		 * @since 1.0.0
		 *
		 * @return array
		 */
		public static function compile_system_status()
		{
			global $wpdb;

			$sysinfo    = array();
			$upload_dir = wp_upload_dir();

			$sysinfo['home_url'] = esc_url(home_url('/'));
			$sysinfo['site_url'] = esc_url(site_url('/'));

			$sysinfo['wp_content_url']      = WP_CONTENT_URL;
			$sysinfo['wp_upload_dir']       = $upload_dir['basedir'];
			$sysinfo['wp_upload_url']       = $upload_dir['baseurl'];
			$sysinfo['wp_ver']              = get_bloginfo('version');
			$sysinfo['wp_multisite']        = is_multisite();
			// $sysinfo['permalink_structure'] = get_option( 'permalink_structure' ) ? get_option( 'permalink_structure' ) : 'Default';
			$sysinfo['front_page_display']  = get_option('show_on_front');
			if ('page' == $sysinfo['front_page_display']) {
				$front_page_id = get_option('page_on_front');
				$blog_page_id  = get_option('page_for_posts');

				$sysinfo['front_page'] = 0 != $front_page_id ? get_the_title($front_page_id) . ' (#' . $front_page_id . ')' : 'Unset';
				$sysinfo['posts_page'] = 0 != $blog_page_id ? get_the_title($blog_page_id) . ' (#' . $blog_page_id . ')' : 'Unset';
			}

			$sysinfo['wp_mem_limit']['raw']  = OceanWP_Theme_Panel_Helpers::let_to_num(WP_MEMORY_LIMIT);
			$sysinfo['wp_mem_limit']['size'] = size_format($sysinfo['wp_mem_limit']['raw']);

			// $sysinfo['db_table_prefix'] = 'Length: ' . strlen( $wpdb->prefix ) . ' - Status: ' . (strlen( $wpdb->prefix ) > 16 ? 'ERROR: Too long' : 'Acceptable');

			$sysinfo['wp_debug'] = 'false';
			if (defined('WP_DEBUG') && WP_DEBUG) {
				$sysinfo['wp_debug'] = 'true';
			}

			// $sysinfo['wp_lang'] = get_locale();

			$sysinfo['wp_writable']         = get_home_path();
			$sysinfo['wp_content_writable'] = WP_CONTENT_DIR;
			$sysinfo['wp_uploads_writable'] = $sysinfo['wp_upload_dir'];
			$sysinfo['wp_plugins_writable'] = WP_PLUGIN_DIR;
			$sysinfo['wp_themes_writable']  = get_theme_root();

			// if ( ! class_exists( 'Browser' ) ) {
			// 	oceanwp_core()->load_files( [
			// 		'control-panel/includes/class-browser',
			// 	] );
			// }

			// $browser = new Browser();

			// $sysinfo['browser'] = array(
			// 	'agent'    => $browser->getUserAgent(),
			// 	'browser'  => $browser->getBrowser(),
			// 	'version'  => $browser->getVersion(),
			// 	'platform' => $browser->getPlatform(),
			// );

			$sysinfo['server_info'] = esc_html($_SERVER['SERVER_SOFTWARE']);
			$sysinfo['localhost']   = OceanWP_Theme_Panel_Helpers::make_bool_string(OceanWP_Theme_Panel_Helpers::is_localhost());
			$sysinfo['php_ver']     = function_exists('phpversion') ? esc_html(phpversion()) : 'phpversion() function does not exist.';
			// $sysinfo['abspath']     = ABSPATH;

			if (function_exists('ini_get')) {
				$sysinfo['php_mem_limit']['raw']      = OceanWP_Theme_Panel_Helpers::let_to_num(ini_get('memory_limit'));
				$sysinfo['php_mem_limit']['size']     = size_format($sysinfo['php_mem_limit']['raw']);
				$sysinfo['php_post_max_size']         = size_format(OceanWP_Theme_Panel_Helpers::let_to_num(ini_get('post_max_size')));
				$sysinfo['php_time_limit']            = ini_get('max_execution_time');
				$sysinfo['php_upload_max_filesize']   = ini_get('upload_max_filesize');
				$sysinfo['php_max_input_var']         = ini_get('max_input_vars');
				// $sysinfo['suhosin_request_max_vars']  = ini_get( 'suhosin.request.max_vars' );
				// $sysinfo['suhosin_post_max_vars']     = ini_get( 'suhosin.post.max_vars' );
				$sysinfo['php_display_errors']        = OceanWP_Theme_Panel_Helpers::make_bool_string(ini_get('display_errors'));
			}

			// $sysinfo['suhosin_installed'] = extension_loaded( 'suhosin' );
			$sysinfo['mysql_ver']         = $wpdb->db_version();
			$sysinfo['max_upload_size']   = size_format(OceanWP_Theme_Panel_Helpers::let_to_num(ini_get('upload_max_filesize')));
			if (is_multisite()) {
				$sysinfo['network_upload_limit'] = get_site_option('fileupload_maxk') . ' KB';
			}

			// $sysinfo['def_tz_is_utc'] = 'true';
			// if ( date_default_timezone_get() !== 'UTC' ) {
			// 	$sysinfo['def_tz_is_utc'] = 'false';
			// }

			$sysinfo['fsockopen_curl'] = 'false';
			if (function_exists('fsockopen') || function_exists('curl_init')) {
				$sysinfo['fsockopen_curl'] = 'true';
			}

			$sysinfo['soap_client'] = 'false';
			if (class_exists('SoapClient')) {
				$sysinfo['soap_client'] = 'true';
			}

			$sysinfo['dom_document'] = 'false';
			if (class_exists('DOMDocument')) {
				$sysinfo['dom_document'] = 'true';
			}

			$sysinfo['gzip'] = 'false';
			if (is_callable('gzopen')) {
				$sysinfo['gzip'] = 'true';
			}

			$sysinfo['mbstring'] = 'false';

			if (extension_loaded('mbstring') && function_exists('mb_eregi') && function_exists('mb_ereg_match')) {
				$sysinfo['mbstring'] = 'true';
			}

			$sysinfo['simplexml'] = 'false';

			if (class_exists('SimpleXMLElement') && function_exists('simplexml_load_string')) {
				$sysinfo['simplexml'] = 'true';
			}

			$sysinfo['phpxml'] = 'false';

			if (function_exists('xml_parse')) {
				$sysinfo['phpxml'] = 'true';
			}

			$active_plugins = (array) get_option('active_plugins', array());

			if (is_multisite()) {
				$active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
			}

			$sysinfo['plugins'] = array();

			foreach ($active_plugins as $plugin) {
				$plugin_data = @get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin);
				$plugin_name = esc_html($plugin_data['Name']);

				$sysinfo['plugins'][$plugin_name] = $plugin_data;
			}

			$active_theme = wp_get_theme();

			$sysinfo['theme']['name']       = $active_theme->Name;
			$sysinfo['theme']['version']    = $active_theme->Version;
			$sysinfo['theme']['author_uri'] = $active_theme->{'Author URI'};
			$sysinfo['theme']['is_child']   = OceanWP_Theme_Panel_Helpers::make_bool_string(is_child_theme());

			if (is_child_theme()) {
				$parent_theme = wp_get_theme($active_theme->Template);

				$sysinfo['theme']['parent_name']       = $parent_theme->Name;
				$sysinfo['theme']['parent_version']    = $parent_theme->Version;
				$sysinfo['theme']['parent_author_uri'] = $parent_theme->{'Author URI'};
			}

			return $sysinfo;
		}

		/**
		 * Create an array of system status warnings.
		 *
		 * @since 1.9.0
		 *
		 * @return array
		 */
		// public static function compile_system_status_warnings() {
		// 	$helper   = OceanWP_Theme_Panel_Helpers::class;
		// 	$sysinfo  = self::compile_system_status();
		// 	$warnings = [];
		// 	$link = '<a href="https://themes.artbees.net/docs/jupiter-x-server-requirements/" target="_blank">' . __( 'Read More', 'jupiterx-core' ) . '</a>';

		// 	if ( $helper::bytes_to_mb( $sysinfo['wp_mem_limit']['raw'] ) < 256 ) {
		// 		$warnings['wp_mem_limit']['message'] = __( 'Insufficient memory. You need at least 256MB of memory.', 'jupiterx-core' );
		// 		$warnings['wp_mem_limit']['message'] .= $link;
		// 	}

		// 	if ( $helper::bytes_to_mb( $sysinfo['php_mem_limit']['raw'] ) < 256 ) {
		// 		$warnings['php_mem_limit']['message'] = __( 'Insufficient memory. You need at least 256MB of memory.', 'jupiterx-core' );
		// 		$warnings['php_mem_limit']['message'] .= $link;
		// 	}

		// 	return $warnings;
		// }
	}
	new OceanWP_Theme_Panel_System_Status();
}
