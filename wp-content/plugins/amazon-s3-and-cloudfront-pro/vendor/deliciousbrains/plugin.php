<?php
/**
 * Plugin Class
 *
 *
 * @package     deliciousbrains
 * @subpackage  api/plugin
 * @copyright   Copyright (c) 2015, Delicious Brains
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Delicious_Brains_API_Plugin Class
 *
 * This class holds all the information about the plugin used by the common API classes
 *
 * @since 0.1
 */
class Delicious_Brains_API_Plugin {

	/**
	 * @var string Plugin slug
	 */
	public $slug;
	/**
	 * @var string Plugin name
	 */
	public $name;
	/**
	 * @var string Plugin version
	 */
	public $version;
	/**
	 * @var string Plugin basename
	 */
	public $basename;
	/**
	 * @var string Plugin Directory Path
	 */
	public $dir_path;
	/**
	 * @var string Prefix to use in nonces and transients
	 */
	public $prefix;
	/**
	 * @var string Path for the URL to the plugin's settings page, passed to admin_url()
	 */
	public $settings_url_path;
	/**
	 * @var string Hash for the URL to the plugin's settings page, passed to admin_url()
	 */
	public $settings_url_hash;
	/**
	 * @var string Hook for the plugin's menu
	 */
	public $hook_suffix;
	/**
	 * @var bool For Multisite installs is the plugin network activated
	 */
	public $is_network_activated = true;
	/**
	 * @var bool Do we allow expired licenses as valid, ie. we don't cripple functionality
	 */
	public $expired_licence_is_valid = true;
	/**
	 * @var string name of email address, usually the same as the prefix
	 */
	public $email_address_name;
	/**
	 * @var string prefix of the global meta data for the plugins
	 */
	public $global_meta_prefix;
	/**
	 * @var string Action hook used to display notices in the plugin
	 */
	public $notices_hook;
	/**
	 * @var string Action hook fired on plugin page load
	 */
	public $load_hook;
	/**
	 * @var string URL to purchase a license for the plugin
	 */
	public $purchase_url;
	/**
	 * @var string URL to access My Account of Delicious Brains
	 */
	public $account_url = 'https://deliciousbrains.com/my-account/';

	/**
	 * Return the name of the email address.
	 * This is by default the plugin prefix
	 *
	 * @return string
	 */
	public function get_email_address_name() {
		if ( $this->email_address_name ) {
			return $this->email_address_name;
		}

		return $this->prefix;
	}

	/**
	 * Return the key for the global meta where all the version info is stored
	 * for the plugin and addons.
	 *
	 * @param string $suffix
	 *
	 * @return string
	 */
	public function get_global_meta_key( $suffix = 'meta' ) {
		$prefix = $this->prefix;
		if ( $this->global_meta_prefix ) {
			$prefix = $this->global_meta_prefix;
		}

		return $prefix . '_' . $suffix;
	}

	/**
	 * Gets a plugin basename for one of our plugins.
	 * This is made up of the plugin folder and filename.
	 *
	 * @param string $slug
	 *
	 * @return string eg. 'akismet/akismet.php'
	 */
	function get_plugin_basename( $slug ) {
		$meta_key = $this->get_global_meta_key();
		if ( ! isset( $GLOBALS[ $meta_key ][ $slug ]['folder'] ) ) {
			$plugin_folder = $slug;
		} else {
			$plugin_folder = $GLOBALS[ $meta_key ][ $slug ]['folder'];
		}

		$plugin_basename = sprintf( '%s/%s.php', $plugin_folder, $slug );

		return $plugin_basename;
	}

	/**
	 * Get the data for a plugin
	 *
	 * @param string $slug
	 *
	 * @return array|bool
	 */
	function get_plugin_data( $slug ) {
		$plugin_path = WP_PLUGIN_DIR . '/' . $this->get_plugin_basename( $slug );
		if ( file_exists( $plugin_path ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
			$plugin_data = get_plugin_data( $plugin_path );
			if ( ! empty( $plugin_data['Name'] ) ) {
				return $plugin_data;
			}
		}

		return false;
	}

	/**
	 * Get the name of a plugin
	 *
	 * @param string $slug
	 *
	 * @return string
	 */
	function get_plugin_name( $slug ) {
		$data = $this->get_plugin_data( $slug );
		if ( $data && ! empty( $data['Name'] ) ) {
			$name = $data['Name'];
		} else {
			$name = ucwords( str_replace( '-', ' ', $slug ) );
		}

		return $name;
	}

	/**
	 * Get the installed version of a plugin
	 *
	 * @param string $slug
	 *
	 * @return int
	 */
	function get_plugin_version( $slug ) {
		$data = $this->get_plugin_data( $slug );
		if ( $data && ! empty( $data['Version'] ) ) {
			return $data['Version'];
		}

		return false;
	}
}