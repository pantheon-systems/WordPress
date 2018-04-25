<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Strong_Plugin_Updater
 */
class Strong_Plugin_Updater {

	public function __construct() {

		add_action( 'admin_init', array( $this, 'plugin_updater' ), 0 ); // Don't change this priority

		add_action( 'wp_ajax_wpmtst_activate_license', array( $this, 'activate_license' ) );

		add_action( 'wp_ajax_wpmtst_deactivate_license', array( $this, 'deactivate_license' ) );

	}

	function plugin_updater() {

		// retrieve our license key from the DB
		$addons = get_option( 'wpmtst_addons' );
		if ( !$addons ) return;

		foreach ( $addons as $addon => $addon_info ) {

			/**
			 * Repair missing file name due to bug in license activation process.
			 *
			 * @since 2.21.2
			 */
			if ( ! isset( $addon_info['file'] ) ) {
				switch ( $addon ) {
					case 'review-markup':
						$addon_info['file'] = WP_PLUGIN_DIR . '/strong-testimonials-review-markup/strong-testimonials-review-markup.php';
						break;
					case 'multiple-forms':
						$addon_info['file'] = WP_PLUGIN_DIR . '/strong-testimonials-multiple-forms/strong-testimonials-multiple-forms.php';
						break;
					case 'properties':
						$addon_info['file'] = WP_PLUGIN_DIR . '/strong-testimonials-properties/strong-testimonials-properties.php';
						break;
					default:
				}

				$addons[ $addon ] = $addon_info;
				update_option( 'wpmtst_addons', $addons );
			}

			$license_key = trim( $addon_info['license']['key'] );
			$version     = $addon_info['version'];

			if ( ! $license_key || ! $version ) {
				return;
			}

			// setup the updater
			$args = array(
				'version'   => $version,         // current installed version number
				'license'   => $license_key,     // license key
				'item_name' => $addon_info['name'],   // name of this plugin
				'author'    => 'Chris Dillon',    // author of this plugin
				'url'       => home_url()
			);
			$edd_updater = new EDD_SL_Plugin_Updater( STRONGPLUGINS_STORE_URL, $addon_info['file'], $args );
		}

	}

	/**
	 * Activate a license key.
	 */
	function activate_license() {

		if ( !check_ajax_referer( 'wpmtst-admin', 'security', false ) ) {
			wp_send_json_error();
		}

		if ( isset( $_GET['action'] ) && 'wpmtst_activate_license' == $_GET['action'] ) {

			$plugin = $_GET['plugin'];

			// retrieve the license from the database
			$addons  = get_option( 'wpmtst_addons' );
			$addon   = $addons[ $plugin ];
			$license = trim( $addon['license']['key'] );

			// data to send in our API request
			$api_params = array(
				'edd_action' => 'activate_license',
				'license'    => $license,
				'item_name'  => $addon['name'], // the name of our product in EDD
				'url'        => home_url()
			);

			$license_data = new stdClass();
			$license_data->license = '';

			// Call the custom API.
			$response = wp_remote_post( STRONGPLUGINS_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

			// make sure the response came back okay
			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

				if ( is_wp_error( $response ) ) {
					$message = $response->get_error_message();
				} else {
					$message = __( 'An error occurred, please try again.' );
				}

			} else {

				$license_data = json_decode( wp_remote_retrieve_body( $response ) );

				if ( !isset( $license_data->success ) ) {

					$message = __( 'An error occurred, please contact support.' );

				} elseif ( false === $license_data->success ) {

					switch( $license_data->error ) {

						case 'expired' :

							$message = sprintf(
								__( 'Your license key expired on %s.' ),
								date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
							);
							break;

						case 'revoked' :

							$message = __( 'Your license key has been disabled.' );
							break;

						case 'missing' :

							$message = __( 'Invalid license.' );
							break;

						case 'invalid' :
						case 'site_inactive' :

							$message = __( 'Your license is not active for this URL.' );
							break;

						case 'item_name_mismatch' :

							$message = sprintf( __( 'This appears to be an invalid license key for %s.' ), $addon['name'] );
							break;

						case 'no_activations_left':

							$message = __( 'Your license key has reached its activation limit.' );
							break;

						default :

							$message = __( 'An error occurred, please try again.' );
							break;
					}

				}

			}

			// Check if anything passed on a message constituting a failure
			if ( !empty( $message ) ) {
				wp_send_json_error( $message );
			}

			// $license_data->license will be either "valid" or "invalid"
			$addon['license']['status'] = $license_data->license;
			$addons[ $plugin ] = $addon;
			update_option( 'wpmtst_addons', $addons );
			wp_send_json_success( $license_data->license );
		}
	}

	/**
	 * Deactivate a license key. This will also decrease the site count.
	 */
	function deactivate_license() {

		if ( ! check_ajax_referer( 'wpmtst-admin', 'security', false ) ) {
			wp_send_json_error();
		}

		if ( isset( $_GET['action'] ) && 'wpmtst_deactivate_license' == $_GET['action'] ) {

			$plugin = $_GET['plugin'];

			// retrieve the license from the database
			$addons  = get_option( 'wpmtst_addons' );
			$addon   = $addons[ $plugin ];
			$license = trim( $addon['license']['key'] );

			// data to send in our API request
			$api_params = array(
				'edd_action' => 'deactivate_license',
				'license'    => $license,
				'item_name'  => $addon['name'], // the name of our product in EDD
				'url'        => home_url()
			);

			// Call the custom API.
			$response = wp_remote_post( STRONGPLUGINS_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

			// make sure the response came back okay
			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

				if ( is_wp_error( $response ) ) {
					$message = $response->get_error_message();
				} else {
					$message = __( 'An error occurred, please try again.' );
				}

				wp_send_json_error( $message );
			}

			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			// $license_data->license will be either "deactivated" or "failed"
			if( $license_data->license == 'deactivated' ) {
				unset( $addons[ $plugin ]['license']['status'] );
				update_option( 'wpmtst_addons', $addons );
				wp_send_json_success( $license_data->license );
			}

			wp_send_json_error( $license_data->license );
		}
	}

}

new Strong_Plugin_Updater();
