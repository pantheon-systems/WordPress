<?php
/**
 * Updates Class
 *
 *
 * @package     deliciousbrains
 * @subpackage  api/updates
 * @copyright   Copyright (c) 2015, Delicious Brains
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Delicious_Brains_API_Updates Class
 *
 * This class handles the updates to a plugin
 *
 * @since 0.1
 */
class Delicious_Brains_API_Updates {

	/**
	 * @var Delicious_Brains_API_Licences
	 */
	protected $licences;

	/**
	 * @var array
	 */
	private $plugin_notices = array();

	function __construct( Delicious_Brains_API_Licences $licences ) {
		$this->licences = $licences;

		add_filter( 'delicious_brains_plugins', array( $this, 'register_plugin_for_updates' ) );
		add_filter( 'site_transient_update_plugins', array( $this, 'site_transient_update_plugins' ) );
		add_filter( 'plugins_api', array( $this, 'short_circuit_wordpress_org_plugin_info_request' ), 10, 3 );
		add_filter( 'http_response', array( $this, 'verify_download' ), 10, 3 );
		add_filter( 'plugins_api', array( $this, 'inject_addon_install_resource' ), 10, 3 );

		add_action( 'admin_print_scripts-plugins.php', array( $this, 'enqueue_plugin_update_script' ) );
		add_action( 'admin_print_scripts-update-core.php', array( $this, 'enqueue_plugin_update_script' ) );
		add_action( 'current_screen', array( $this, 'check_again_clear_transients' ) );
		add_action( 'install_plugins_pre_plugin-information', array( $this, 'plugin_update_popup' ) );

		add_action( 'load-plugins.php', array( $this, 'clear_licence_transient' ) );

		$this->add_plugin_notice( $this->licences->plugin->basename );
		if ( $this->licences->addons ) {
			foreach ( $this->licences->addons as $basename => $addon ) {
				if ( ! ( $addon['available'] && $addon['installed'] ) ) {
					// Only register addons for updates that are installed and available for license
					continue;
				}
				$this->add_plugin_notice( $basename );
			}
		}
	}

	/**
	 * Add handlers for displaying update notices for a plugin on our settings page
	 *
	 * @param string $basename
	 */
	function add_plugin_notice( $basename ) {
		if ( ! empty( $basename ) ) {
			if ( empty( $this->plugin_notices ) ) {
				add_action( $this->licences->plugin->notices_hook, array( $this, 'handle_plugin_notices' ) );
			}
			$this->plugin_notices[ $basename ] = $basename;
		}
	}

	/**
	 * Display update notices for a plugin on our settings page
	 */
	function handle_plugin_notices() {
		if ( ! empty( $this->plugin_notices ) ) {
			foreach ( $this->plugin_notices as $basename ) {
				$this->version_update_notice( $basename );
			}
		}
	}

	/**
	 * Clear the license response on the plugins page so we get up to date license info
	 */
	function clear_licence_transient() {
		delete_site_transient( $this->licences->plugin->prefix . '_licence_response' );
	}

	/**
	 * Adds the plugin to the array of plugins used by the update JS
	 *
	 * @param array $plugins
	 *
	 * @return array
	 */
	public function register_plugin_for_updates( $plugins ) {
		$plugins[ $this->licences->plugin->slug ] = array_merge(
			(array) $this->licences->plugin,
			array(
				'addons'  => $this->licences->addons,
				'license' => $this->licences->is_licence_expired()
			)
		);

		return $plugins;
	}

	/**
	 * Take over the update check for plugins
	 *
	 * @param object $trans
	 *
	 * @return object
	 */
	function site_transient_update_plugins( $trans ) {
		$plugin_upgrade_data = $this->get_upgrade_data();

		$plugin_basename = $this->licences->plugin->get_plugin_basename( $this->licences->plugin->slug );
		if ( isset( $trans->no_update[ $plugin_basename ] ) ) {
			// Ensure the pro plugin always has the correct info and WP API doesn't confuse with free version
			$trans->no_update[ $plugin_basename ]->slug        = $this->licences->plugin->slug;
			$trans->no_update[ $plugin_basename ]->url         = $this->licences->api_base;
			$trans->no_update[ $plugin_basename ]->new_version = $this->licences->plugin->version;
		}

		if ( false === $plugin_upgrade_data || ! isset( $plugin_upgrade_data[ $this->licences->plugin->slug ] ) ) {
			return $trans;
		}

		foreach ( $plugin_upgrade_data as $plugin_slug => $upgrade_data ) {
			$plugin_basename = $this->licences->plugin->get_plugin_basename( $plugin_slug );
			if ( isset( $this->licences->addons[ $plugin_basename ] ) && ! ( $this->licences->addons[ $plugin_basename ]['available'] && $this->licences->addons[ $plugin_basename ]['installed'] ) ) {
				// Addon not installed or available for license, ignore
				continue;
			}
			$installed_version = $this->get_installed_version( $plugin_slug );
			$latest_version    = $this->get_latest_version( $plugin_slug, $installed_version );

			if ( false === $installed_version || false === $latest_version ) {
				continue;
			}

			if ( version_compare( $installed_version, $latest_version, '<' ) ) {
				$is_beta = $this->is_beta_version( $latest_version );

				$trans->response[ $plugin_basename ]              = new stdClass();
				$trans->response[ $plugin_basename ]->url         = $this->licences->api_base;
				$trans->response[ $plugin_basename ]->slug        = $plugin_slug;
				$trans->response[ $plugin_basename ]->package     = $this->get_plugin_update_download_url( $plugin_slug, $is_beta );
				$trans->response[ $plugin_basename ]->new_version = $latest_version;
				$trans->response[ $plugin_basename ]->id          = '0';
				$trans->response[ $plugin_basename ]->plugin      = $plugin_basename;
			}
		}

		return $trans;
	}

	/**
	 * Add some custom JS into the plugin page for our updates process
	 */
	public function enqueue_plugin_update_script() {
		$handle = 'dbrains-plugin-update-script';

		// This script should only be enqueued once if the site has multiple
		// Delicious Brains plugins installed
		if ( wp_script_is( $handle, 'enqueued' ) ) {
			return;
		}

		$version = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? time() : $this->licences->plugin->version;
		$min     = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$src     = plugins_url( "assets/js/plugin-update$min.js", __FILE__ );

		wp_enqueue_script( $handle, $src, array( 'jquery', 'underscore' ), $version, true );

		wp_localize_script( $handle,
			'dbrains',
			array(
				'nonces'  => array(
					'check_licence' => wp_create_nonce( 'check-licence' ),
				),
				'strings' => array(
					'check_license_again'     => __( 'Check my license again', 'amazon-s3-and-cloudfront' ),
					'license_check_problem'   => __( 'A problem occurred when trying to check the license, please try again.', 'amazon-s3-and-cloudfront' ),
					'requires_parent_license' => __( 'Requires a valid license for %s.', 'amazon-s3-and-cloudfront' )
				),
				'plugins' => apply_filters( 'delicious_brains_plugins', array() ),
			)
		);
	}

	/**
	 * Short circuits the HTTP request to WordPress.org servers to retrieve plugin information.
	 * Will only fire on the update-core.php admin page.
	 *
	 * @param  object|bool $res    Plugin resource object or boolean false.
	 * @param  string      $action The API call being performed.
	 * @param  object      $args   Arguments for the API call being performed.
	 *
	 * @return object|bool Plugin resource object or boolean false.
	 */
	function short_circuit_wordpress_org_plugin_info_request( $res, $action, $args ) {
		if ( 'plugin_information' != $action || empty( $args->slug ) || $this->licences->plugin->slug != $args->slug ) {
			return $res;
		}

		$screen = get_current_screen();

		// Only fire on the update-core.php admin page
		if ( empty( $screen->id ) || ( 'update-core' !== $screen->id && 'update-core-network' !== $screen->id ) ) {
			return $res;
		}

		$res         = new stdClass();
		$plugin_info = $this->get_upgrade_data();

		if ( isset( $plugin_info[ $this->licences->plugin->slug ]['tested'] ) ) {
			$res->tested = $plugin_info[ $this->licences->plugin->slug ]['tested'];
		} else {
			$res->tested = false;
		}

		return $res;
	}

	/**
	 * Clear update transients when the user clicks the "Check Again" button from the update screen.
	 *
	 * @param object $current_screen
	 */
	function check_again_clear_transients( $current_screen ) {
		if ( ! isset( $current_screen->id ) || false === strpos( $current_screen->id, 'update-core' ) || ! isset( $_GET['force-check'] ) ) {
			return;
		}

		delete_site_transient( $this->licences->plugin->prefix . '_upgrade_data' );
		delete_site_transient( 'update_plugins' );
		delete_site_transient( $this->licences->plugin->prefix . '_licence_response' );
		delete_site_transient( 'dbrains_api_down' );
	}

	/**
	 * Display our custom plugin details when the user clicks "view details"
	 * on the plugin listing page.
	 */
	function plugin_update_popup() {
		$slug = sanitize_key( $_GET['plugin'] );

		if ( ! $this->is_plugin( $slug ) ) {
			return;
		}

		$error_msg = '<p>' . __( 'Could not retrieve version details. Please try again.', 'amazon-s3-and-cloudfront' ) . '</p>';

		$latest_version = $this->get_latest_version( $slug );

		if ( false === $latest_version ) {
			echo $error_msg;
			exit;
		}

		$data = $this->licences->get_changelog( $slug, $this->is_beta_version( $latest_version ) );

		if ( is_wp_error( $data ) || empty( $data ) ) {
			echo $error_msg;
		} else {
			echo $data;
		}

		exit;
	}

	/**
	 * Verify the download URL for the plugin
	 *
	 * @param array  $response
	 * @param array  $args
	 * @param string $url
	 *
	 * @return array|WP_Error Array containing 'headers', 'body', 'response', 'cookies', 'filename'. A WP_Error
	 *                        instance upon error
	 */
	function verify_download( $response, $args, $url ) {
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$download_url = $this->get_plugin_update_download_url( $this->licences->plugin->slug );

		if ( false === strpos( $url, $download_url ) || 402 != $response['response']['code'] ) {
			return $response;
		}

		// The $response['body'] is blank but output is actually saved to a file in this case
		$data = @file_get_contents( $response['filename'] );

		if ( ! $data ) {
			return new WP_Error( $this->licences->plugin->prefix . '_download_error_empty', sprintf( __( 'Error retrieving download from deliciousbrain.com. Please try again or download manually from <a href="%1$s">%2$s</a>.', 'amazon-s3-and-cloudfront' ), $this->licences->plugin->account_url, _x( 'My Account', 'Delicious Brains account', 'amazon-s3-and-cloudfront' ) ) );
		}

		$decoded_data = json_decode( $data, true );

		// Can't decode the JSON errors, so just barf it all out
		if ( ! isset( $decoded_data['errors'] ) || ! $decoded_data['errors'] ) {
			return new WP_Error( $this->licences->plugin->prefix . '_download_error_raw', $data );
		}

		foreach ( $decoded_data['errors'] as $key => $msg ) {
			return new WP_Error( $this->licences->plugin->prefix . '_' . $key, $msg );
		}
	}

	/**
	 * Is the plugin version a beta version
	 *
	 * @param string $ver
	 *
	 * @return bool
	 */
	function is_beta_version( $ver ) {
		if ( preg_match( '@b[0-9]+$@', $ver ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get the installed version of a plugin
	 *
	 * @param string $slug
	 *
	 * @return int|string
	 */
	function get_installed_version( $slug ) {
		$meta_key = $this->licences->plugin->get_global_meta_key();
		if ( isset( $GLOBALS[ $meta_key ][ $slug ]['version'] ) ) {
			// Plugin activated, use meta for version
			$installed_version = $GLOBALS[ $meta_key ][ $slug ]['version'];
		} else {
			// Not activated
			$installed_version = $this->licences->plugin->get_plugin_version( $slug );
		}

		return $installed_version;
	}

	/**
	 * Get the latest version available of a plugin
	 *
	 * @param string      $slug
	 * @param string|null $installed_version
	 *
	 * @return string|false
	 */
	function get_latest_version( $slug, $installed_version = null ) {
		$data = $this->get_upgrade_data();

		if ( ! isset( $data[ $slug ] ) ) {
			return false;
		}

		$plugin_file = $this->licences->plugin->get_plugin_basename( $slug );
		if ( $this->is_addon( $plugin_file ) ) {
			if ( ! $this->is_allowed_addon( $slug ) ) {
				return false;
			}
		}

		if ( is_null( $installed_version ) ) {
			$installed_version = $this->get_installed_version( $slug );
		}

		$required_version = $this->get_required_version( $slug );

		// Return the latest beta version if the installed version is beta
		// and the API returned a beta version and it's newer than the latest stable version
		if ( $installed_version
		     && ( $this->is_beta_version( $installed_version ) || $this->is_beta_version( $required_version ) )
		     && isset( $data[ $slug ]['beta_version'] )
		     && version_compare( $data[ $slug ]['version'], $data[ $slug ]['beta_version'], '<' )
		) {
			return $data[ $slug ]['beta_version'];
		}

		return $data[ $slug ]['version'];
	}

	/**
	 * Get the upgrade data about the plugin and its addons
	 *
	 * @return array|bool|mixed
	 */
	function get_upgrade_data() {
		$info = get_site_transient( $this->licences->plugin->prefix . '_upgrade_data' );

		if ( isset( $info['version'] ) ) {
			delete_site_transient( $this->licences->plugin->prefix . '_licence_response' );
			delete_site_transient( $this->licences->plugin->prefix . '_upgrade_data' );
			$info = false;
		}

		if ( $info ) {
			return $info;
		}

		$data = $this->licences->get_upgrade_data();

		$data = json_decode( $data, true );

		/*
		We need to set the transient even when there's an error,
		otherwise we'll end up making API requests over and over again
		and slowing things down big time.
		*/
		$default_upgrade_data = array( $this->licences->plugin->slug => array( 'version' => $this->licences->plugin->version ) );

		if ( ! $data ) {
			set_site_transient( $this->licences->plugin->prefix . '_upgrade_data', $default_upgrade_data, $this->licences->transient_retry_timeout );
			$this->licences->log_error( 'Error trying to decode JSON upgrade data.' );

			return false;
		}

		if ( isset( $data['errors'] ) ) {
			set_site_transient( $this->licences->plugin->prefix . '_upgrade_data', $default_upgrade_data, $this->licences->transient_retry_timeout );
			$this->licences->log_error( __( 'Error trying to get upgrade data.', 'amazon-s3-and-cloudfront' ), $data['errors'] );

			return false;
		}

		set_site_transient( $this->licences->plugin->prefix . '_upgrade_data', $data, $this->licences->transient_timeout );

		return $data;
	}

	/**
	 * Create the plugin update URL
	 *
	 * @param string $plugin_slug
	 * @param bool   $is_beta
	 *
	 * @return string
	 */
	function get_plugin_update_download_url( $plugin_slug, $is_beta = false ) {
		$licence    = $this->licences->get_licence_key();
		$query_args = array(
			'request'     => 'download',
			'licence_key' => $licence,
			'slug'        => $plugin_slug,
			'product'     => $this->licences->plugin->slug,
			'site_url'    => $this->licences->home_url,
		);

		if ( $is_beta ) {
			$query_args['beta'] = '1';
		}

		$url = add_query_arg( $query_args, $this->licences->api_url );

		return esc_url_raw( $url );
	}

	/**
	 * Display custom version update notices to the top of our plugin page
	 *
	 * @param string $basename
	 */
	function version_update_notice( $basename ) {
		// We don't want to show both the "Update Required" and "Update Available" messages at the same time
		if ( $this->is_addon( $basename ) && $this->is_addon_outdated( $basename ) ) {
			return;
		}

		$slug = current( explode( '/', $basename ) );

		// To reduce UI clutter we hide addon update notices if the core plugin has updates available
		if ( $this->is_addon( $basename ) && $this->core_update_available() ) {
			// Core update is available, don't show update notices for addons until core is updated
			return;
		}

		$licence          = $this->licences->get_licence_key();
		$licence_response = $this->licences->is_licence_expired();
		$licence_problem  = isset( $licence_response['errors'] );

		$update_url = wp_nonce_url( $this->licences->admin_url( 'update.php?action=upgrade-plugin&plugin=' . urlencode( $basename ) ), 'upgrade-plugin_' . $basename );

		$installed_version = $this->get_installed_version( $slug );
		$latest_version    = $this->get_latest_version( $slug, $installed_version );
		$plugin_name       = ( isset( $this->licences->addons[ $basename ] ) ) ? $this->licences->addons[ $basename ]['name'] : $this->licences->plugin->name;

		if ( version_compare( $installed_version, $latest_version, '<' ) ) { ?>
			<div style="display: block;" class="updated warning inline-message">
				<strong><?php _ex( 'Update Available', 'A new version of the plugin is available', 'amazon-s3-and-cloudfront' ); ?></strong> &mdash;
				<?php
				$message = sprintf( __( '%1$s %2$s is now available. You currently have %3$s installed.', 'amazon-s3-and-cloudfront' ), $plugin_name, $latest_version, $installed_version );
				if ( ! empty( $licence ) && ! $licence_problem ) {
					$message .= ' ' . sprintf( '<a href="%1$s">%2$s</a>', $update_url, _x( 'Update Now', 'Download and install a new version of the plugin', 'amazon-s3-and-cloudfront' ) );
				}
				echo $message;
				?>
			</div>
			<?php
		}
	}

	/**
	 * Is the plugin an addon
	 *
	 * @param string $plugin_file
	 *
	 * @return bool
	 */
	function is_addon( $plugin_file ) {
		return isset( $this->licences->addons[ $plugin_file ] );
	}

	/**
	 * Is there an update for the core plugin?
	 *
	 * @return bool
	 */
	function core_update_available() {
		$core_installed_version = $this->licences->plugin->version;
		$core_latest_version    = $this->get_latest_version( $this->licences->plugin->slug, $core_installed_version );
		$needs_update           = version_compare( $core_installed_version, $core_latest_version, '<' );

		return $needs_update;
	}

	/**
	 * Check if an addon needs to be updated
	 *
	 * @param string $addon_basename
	 *
	 * @return bool
	 */
	function is_addon_outdated( $addon_basename ) {
		$addon_slug = current( explode( '/', $addon_basename ) );

		$installed_version = $this->get_installed_version( $addon_slug );
		$required_version  = $this->licences->addons[ $addon_basename ]['required_version'];

		return version_compare( $installed_version, $required_version, '<' );
	}

	/**
	 * Is the addon allowed for the license
	 *
	 * @param string $slug
	 *
	 * @return bool
	 */
	function is_allowed_addon( $slug ) {
		$addons = get_site_transient( $this->licences->plugin->prefix . '_addons' );
		if ( isset( $addons[ $slug ] ) ) {
			// Addon allowed
			return true;
		}

		// Not an allowed addon
		return false;
	}

	/**
	 * Hook into the plugin install process and inject addon download url
	 *
	 * @param stdClass $res
	 * @param          $action
	 * @param          $args
	 *
	 * @return stdClass
	 */
	function inject_addon_install_resource( $res, $action, $args ) {
		if ( 'plugin_information' != $action || empty( $args->slug ) ) {
			return $res;
		}

		$addons = get_site_transient( $this->licences->plugin->prefix . '_addons' );

		if ( ! isset( $addons[ $args->slug ] ) ) {
			return $res;
		}

		$addon            = $addons[ $args->slug ];
		$required_version = $this->get_required_version( $args->slug );
		$is_beta          = $this->is_beta_version( $required_version ) && ! empty( $addon['beta_version'] );

		$res                = new stdClass();
		$res->name          = $this->licences->plugin->name . ' ' . $addon['name'];
		$res->version       = $is_beta ? $addon['beta_version'] : $addon['version'];
		$res->download_link = $this->get_plugin_update_download_url( $args->slug, $is_beta );
		$res->tested        = isset( $addon['tested'] ) ? $addon['tested'] : false;

		return $res;
	}

	/**
	 * Get the required version of a plugin
	 *
	 * @param string $slug
	 *
	 * @return string
	 */
	function get_required_version( $slug ) {
		$plugin_file = $this->licences->plugin->get_plugin_basename( $slug );

		if ( isset( $this->licences->addons[ $plugin_file ]['required_version'] ) ) {
			return $this->licences->addons[ $plugin_file ]['required_version'];
		} else {
			return '0';
		}
	}

	/**
	 * Check a plugin slug to see if it is the core plugin or one of its addons
	 *
	 * @param string $slug
	 *
	 * @return bool
	 */
	function is_plugin( $slug ) {
		$plugins = array( $this->licences->plugin->slug );
		if ( $this->licences->addons ) {
			foreach ( $this->licences->addons as $key => $addon ) {
				$plugins[] = dirname( $key );
			}
		}

		return in_array( $slug, $plugins );
	}

	/**
	 * Helper to get the directory of the plugin
	 *
	 * @return string
	 */
	function plugins_dir() {
		$path = untrailingslashit( $this->licences->plugin->dir_path );

		return substr( $path, 0, strrpos( $path, DIRECTORY_SEPARATOR ) ) . DIRECTORY_SEPARATOR;
	}
}
