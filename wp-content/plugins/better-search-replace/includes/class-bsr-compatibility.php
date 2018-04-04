<?php

/**
 * Processes compatibility functionality.
 * @since      1.0
 *
 * @package    Better_Search_Replace
 * @subpackage Better_Search_Replace/includes
 */

// Prevent direct access.
if ( ! defined( 'BSR_PATH' ) ) exit;

class BSR_Compatibility {

	/**
	 * Returns the system info.
	 * @access public
	 * @return string
	 */
	public static function get_sysinfo() {

		global $wpdb;

		$return = '### Begin System Info ###' . "\n\n";

		// Basic site info
		$return .= '-- WordPress Configuration' . "\n\n";
		$return .= 'Site URL:                 ' . site_url() . "\n";
		$return .= 'Home URL:                 ' . home_url() . "\n";
		$return .= 'Multisite:                ' . ( is_multisite() ? 'Yes' : 'No' ) . "\n";
		$return .= 'Version:                  ' . get_bloginfo( 'version' ) . "\n";
		$return .= 'Language:                 ' . get_locale() . "\n";
		$return .= 'Table Prefix:             ' . 'Length: ' . strlen( $wpdb->prefix ) . "\n";
		$return .= 'WP_DEBUG:                 ' . ( defined( 'WP_DEBUG' ) ? WP_DEBUG ? 'Enabled' : 'Disabled' : 'Not set' ) . "\n";
		$return .= 'Memory Limit:             ' . WP_MEMORY_LIMIT . "\n";

		// Plugin Configuration
		$return .= "\n" . '-- Better Search Replace Configuration' . "\n\n";
		$return .= 'Plugin Version:           ' . BSR_VERSION . "\n";
		$db      = new BSR_DB();
		$return .= 'Max Page Size:            ' . $db->get_page_size() . "\n";

		// Server Configuration
		$return .= "\n" . '-- Server Configuration' . "\n\n";
		$return .= 'Operating System:         ' . php_uname( 's' ) . "\n";
		$return .= 'PHP Version:              ' . PHP_VERSION . "\n";
		$return .= 'MySQL Version:            ' . $wpdb->db_version() . "\n";

		$return .= 'Server Software:          ' . $_SERVER['SERVER_SOFTWARE'] . "\n";

		// PHP configs... now we're getting to the important stuff
		$return .= "\n" . '-- PHP Configuration' . "\n\n";
		$return .= 'Safe Mode:                ' . ( ini_get( 'safe_mode' ) ? 'Enabled' : 'Disabled' . "\n" );
		$return .= 'Memory Limit:             ' . ini_get( 'memory_limit' ) . "\n";
		$return .= 'Post Max Size:            ' . ini_get( 'post_max_size' ) . "\n";
		$return .= 'Upload Max Filesize:      ' . ini_get( 'upload_max_filesize' ) . "\n";
		$return .= 'Time Limit:               ' . ini_get( 'max_execution_time' ) . "\n";
		$return .= 'Max Input Vars:           ' . ini_get( 'max_input_vars' ) . "\n";
		$return .= 'Display Errors:           ' . ( ini_get( 'display_errors' ) ? 'On (' . ini_get( 'display_errors' ) . ')' : 'N/A' ) . "\n";

		// WordPress active plugins
		$return .= "\n" . '-- WordPress Active Plugins' . "\n\n";
		$plugins = get_plugins();
		$active_plugins = get_option( 'active_plugins', array() );
		foreach( $plugins as $plugin_path => $plugin ) {
			if( !in_array( $plugin_path, $active_plugins ) )
				continue;
			$return .= $plugin['Name'] . ': ' . $plugin['Version'] . "\n";
		}

		// WordPress inactive plugins
		$return .= "\n" . '-- WordPress Inactive Plugins' . "\n\n";
		foreach( $plugins as $plugin_path => $plugin ) {
			if( in_array( $plugin_path, $active_plugins ) )
				continue;
			$return .= $plugin['Name'] . ': ' . $plugin['Version'] . "\n";
		}

		if( is_multisite() ) {
			// WordPress Multisite active plugins
			$return .= "\n" . '-- Network Active Plugins' . "\n\n";
			$plugins = wp_get_active_network_plugins();
			$active_plugins = get_site_option( 'active_sitewide_plugins', array() );
			foreach( $plugins as $plugin_path ) {
				$plugin_base = plugin_basename( $plugin_path );
				if( !array_key_exists( $plugin_base, $active_plugins ) )
					continue;
				$plugin  = get_plugin_data( $plugin_path );
				$return .= $plugin['Name'] . ': ' . $plugin['Version'] . "\n";
			}
		}

		$return .= "\n" . '### End System Info ###';
		return $return;
	}

}
