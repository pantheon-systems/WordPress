<?php
/**
 * Class: License Manager.
 *
 * License manager used in all the Add-Ons licenses.
 *
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Since other plugins might use this class.
if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	require_once( 'EDD_SL_Plugin_Updater.php' );
}

/**
 * Class: License Manager.
 *
 * License Manager used in all the Add-Ons licenses.
 *
 * @package Wsal
 */
class WSAL_LicenseManager {

	/**
	 * Instance of WpSecurityAuditLog.
	 *
	 * @var WpSecurityAuditLog
	 */
	protected $plugin;

	/**
	 * Array of Add-Ons.
	 *
	 * @var array
	 */
	protected $plugins = array();

	/**
	 * Method: Constructor.
	 *
	 * @param WpSecurityAuditLog $plugin - Instance of WpSecurityAuditLog.
	 */
	public function __construct( WpSecurityAuditLog $plugin ) {
		$this->plugin = $plugin;
		add_action( 'plugins_loaded', array( $this, 'LoadPlugins' ) );
	}

	/**
	 * Method: Get Store URL.
	 */
	protected function GetStoreUrl() {
		return 'https://www.wpsecurityauditlog.com/';
	}

	/**
	 * Method: Count Add-Ons.
	 */
	public function CountPlugins() {
		return count( $this->plugins );
	}

	/**
	 * Method: Get Add-Ons.
	 */
	public function Plugins() {
		return $this->plugins;
	}

	/**
	 * Method: Load Add-Ons.
	 */
	public function LoadPlugins() {
		foreach ( apply_filters( 'wsal_register', array() ) as $plugin_file ) {
			$this->AddPremiumPlugin( $plugin_file );
		}
	}

	/**
	 * Method: Get Plugin Data.
	 *
	 * @param string $plugin_file - Plugin file.
	 * @param string $license - Plugin License.
	 */
	protected function GetPluginData( $plugin_file, $license ) {
		// A hack since get_plugin_data() is not available now.
		$plugin_data = get_file_data(
			$plugin_file, array(
				'Name' => 'Plugin Name',
				'PluginURI' => 'Plugin URI',
				'Version' => 'Version',
				'Description' => 'Description',
				'Author' => 'Author',
				'TextDomain' => 'Text Domain',
				'DomainPath' => 'Domain Path',
			), 'plugin'
		);

		$plugin_updater = is_null( $license )
			? null
			: new EDD_SL_Plugin_Updater(
				$this->GetStoreUrl(),
				$plugin_file,
				array(
					'license'   => $license,
					'item_name' => $plugin_data['Name'],
					'author'    => $plugin_data['Author'],
					'version'   => $plugin_data['Version'],
				)
			);

		return array(
			'PluginData' => $plugin_data,
			'EddUpdater' => $plugin_updater,
		);
	}

	/**
	 * Method: Add Premium Plugin.
	 *
	 * @param string $plugin_file - Plugin File.
	 */
	public function AddPremiumPlugin( $plugin_file ) {
		if ( isset( $plugin_file ) ) {
			$name = sanitize_key( basename( $plugin_file ) );
			$license = $this->plugin->settings->GetLicenseKey( $name );
			$this->plugins[ $name ] = $this->GetPluginData( $plugin_file, $license );
		}
	}

	/**
	 * Method: Add Plugin.
	 *
	 * @param string $plugin_file - Plugin File.
	 */
	public function AddPlugin( $plugin_file ) {
		if ( isset( $plugin_file ) ) {
			$name = sanitize_key( basename( $plugin_file ) );
			$this->plugins[ $name ] = $this->GetPluginData( $plugin_file, null );
		}
	}

	/**
	 * Method: Get Blog IDs.
	 */
	protected function GetBlogIds() {
		global $wpdb;
		$sql = 'SELECT blog_id FROM ' . $wpdb->blogs;
		return $wpdb->get_col( $sql );
	}

	/**
	 * Method: Activate Premium Plugin license.
	 *
	 * @param string $name - Plugin name.
	 * @param string $license - Plugin license.
	 */
	public function ActivateLicense( $name, $license ) {
		$this->plugin->settings->SetLicenseKey( $name, $license );

		$plugins = $this->Plugins();
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'   => urlencode( $license ),
			'item_name' => urlencode( $plugins[ $name ]['PluginData']['Name'] ),
			'url'       => urlencode( home_url() ),
		);

		$blog_ids = $this->plugin->IsMultisite() ? $this->GetBlogIds() : array( 1 );

		foreach ( $blog_ids as $blog_id ) {
			if ( $this->plugin->IsMultisite() ) {
				$api_params['url'] = urlencode( get_home_url( $blog_id ) );
			}

			$response = wp_remote_get(
				esc_url_raw( add_query_arg( $api_params, $this->GetStoreUrl() ) ),
				array(
					'timeout' => 15,
					'sslverify' => false,
				)
			);

			if ( is_wp_error( $response ) ) {
				$this->plugin->settings->SetLicenseErrors( $name, 'Invalid Licensing Server Response: ' . $response->get_error_message() );
				$this->DeactivateLicense( $name, $license );
				return false;
			}

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( is_object( $license_data ) ) {
				$this->plugin->settings->SetLicenseStatus( $name, $license_data->license );
				if ( 'valid' !== $license_data->license ) {
					$error = 'License Not Valid';
					if ( isset( $license_data->error ) ) {
						$error .= ': ' . ucfirst( str_replace( '_', ' ', $license_data->error ) );
					}
					$this->plugin->settings->SetLicenseErrors( $name, $error );
					$this->DeactivateLicense( $name, $license );
					return false;
				}
			} else {
				$this->plugin->settings->SetLicenseErrors( $name, 'Unexpected Licensing Server Response' );
				$this->DeactivateLicense( $name, $license );
				return false;
			}
		}

		return true;
	}

	/**
	 * Method: Check Plugin License.
	 *
	 * @param  string $name - Plugin name.
	 */
	public function IsLicenseValid( $name ) {
		return trim( strtolower( $this->plugin->settings->GetLicenseStatus( $name ) ) ) === 'valid';
	}

	/**
	 * Method: Deactivate Premium Plugin license.
	 *
	 * @param string $name - Plugin name.
	 * @param string $license - Plugin license.
	 */
	public function DeactivateLicense( $name, $license = null ) {
		$this->plugin->settings->SetLicenseStatus( $name, '' );

		// Deactivate it on the server (if license was given).
		if ( ! is_null( $license ) ) {
			$plugins = $this->Plugins();
			$api_params = array(
				'edd_action' => 'deactivate_license',
				'license'   => urlencode( $license ),
				'item_name' => urlencode( $plugins[ $name ]['PluginData']['Name'] ),
				'url'       => urlencode( home_url() ),
			);

			$blog_ids = $this->plugin->IsMultisite() ? $this->GetBlogIds() : array( 1 );

			foreach ( $blog_ids as $blog_id ) {
				if ( $this->plugin->IsMultisite() ) {
					$api_params['url'] = urlencode( get_home_url( $blog_id ) );
				}

				$response = wp_remote_get(
					esc_url_raw( add_query_arg( $api_params, $this->GetStoreUrl() ) ),
					array(
						'timeout' => 15,
						'sslverify' => false,
					)
				);

				if ( is_wp_error( $response ) ) {
					return false;
				}

				wp_remote_retrieve_body( $response );
			}
		}
	}
}
