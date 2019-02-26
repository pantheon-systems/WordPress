<?php
/**
 * Licence Class
 *
 *
 * @package     deliciousbrains
 * @subpackage  api/licences
 * @copyright   Copyright (c) 2015, Delicious Brains
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Delicious_Brains_API_Licences Class
 *
 * This class handles the licencing calls with the Delicious Brains WooCommerce API
 *
 * @since 0.1
 */
abstract class Delicious_Brains_API_Licences extends Delicious_Brains_API_Base {

	/**
	 * @var array
	 */
	public $addons;

	/**
	 * @var Delicious_Brains_API_Updates
	 */
	protected $updates;

	/**
	 * @param Delicious_Brains_API_Plugin $plugin
	 */
	function __construct( Delicious_Brains_API_Plugin $plugin ) {
		parent::__construct( $plugin );

		$this->actions();
		$this->set_addons();

		// Fire up the plugin updates
		$this->updates = new Delicious_Brains_API_Updates( $this );
	}

	/**
	 * Fire up the actions for the licenses
	 */
	function actions() {
		add_action( $this->plugin->load_hook, array( $this, 'remove_licence_when_constant_set' ) );
		add_action( $this->plugin->load_hook, array( $this, 'http_disable_ssl' ) );
		add_action( $this->plugin->load_hook, array( $this, 'http_remove_licence' ) );
		add_action( $this->plugin->load_hook, array( $this, 'http_refresh_licence' ) );

		add_action( 'wp_ajax_' . $this->plugin->prefix . '_activate_licence', array( $this, 'ajax_activate_licence' ) );
		add_action( 'wp_ajax_' . $this->plugin->prefix . '_remove_licence', array( $this, 'ajax_remove_licence' ) );
		add_action( 'wp_ajax_' . $this->plugin->prefix . '_check_licence', array( $this, 'ajax_check_licence' ) );
		add_action( 'wp_ajax_' . $this->plugin->prefix . '_reactivate_licence', array( $this, 'ajax_reactivate_licence' ) );
	}

	/**
	 * Define the addons available for the plugin
	 *
	 * @return array
	 */
	function set_addons() {
		if ( is_null( $this->addons ) ) {
			$meta_key = $this->plugin->get_global_meta_key();

			$addons = array();

			if ( isset ( $GLOBALS[ $meta_key ][ $this->plugin->slug ]['supported_addon_versions'] ) ) {
				$available_addons = get_site_transient( $this->plugin->prefix . '_addons_available' );

				foreach ( $GLOBALS[ $meta_key ][ $this->plugin->slug ]['supported_addon_versions'] as $addon => $version ) {
					$basename  = $this->plugin->get_plugin_basename( $addon );
					$name      = $this->plugin->get_plugin_name( $addon );
					$installed = file_exists( WP_PLUGIN_DIR . '/' . $basename );

					$addons[ $basename ] = array(
						'name'             => $name,
						'slug'             => $addon,
						'basename'         => $basename,
						'required_version' => $version,
						'available'        => ( false === $available_addons || isset( $available_addons[ $addon ] ) ),
						'installed'        => $installed,
					);
				}
			}

			$this->addons = $addons;
		}

		return $this->addons;
	}

	/**
	 * Get method for returning the license key from the database
	 * that must be defined by the extending class
	 *
	 * @return mixed
	 */
	abstract protected function get_plugin_licence_key();

	/**
	 * Set method for saving the license key to the database
	 * that must be defined by the extending class
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	abstract protected function set_plugin_licence_key( $key );

	/**
	 * Get the name of the license constant
	 *
	 * @return string
	 */
	protected function get_licence_constant_name() {
		return strtoupper( $this->plugin->prefix ) . '_LICENCE';
	}

	/**
	 * Is the license key defined as a constant?
	 *
	 * @return bool
	 */
	public function is_licence_constant() {
		return defined( $this->get_licence_constant_name() );
	}

	/**
	 * Get the license key either from a constant or saved by the plugin
	 *
	 * @return string
	 */
	public function get_licence_key() {
		if ( $this->is_licence_constant() ) {
			$licence = constant( $this->get_licence_constant_name() );
		} else {
			$licence = $this->get_plugin_licence_key();
		}

		return $licence;
	}

	/**
	 * Sets the licence key
	 *
	 * @param string $key
	 */
	protected function set_licence_key( $key ) {
		$this->set_plugin_licence_key( $key );
	}

	/**
	 * Checks whether the saved licence has expired or not.
	 *
	 * @param bool $skip_transient_check
	 * @param bool $skip_expired_check
	 *
	 * @return bool
	 */
	public function is_valid_licence( $skip_transient_check = false, $skip_expired_check = true ) {
		$response = $this->is_licence_expired( $skip_transient_check );

		if ( isset( $response['dbrains_api_down'] ) ) {
			return true;
		}

		if ( $this->plugin->expired_licence_is_valid && isset( $response['errors']['subscription_expired'] ) && 1 === count( $response['errors'] ) ) {
			// Maybe don't cripple the plugin's functionality if the user's licence is expired.
			return $skip_expired_check;
		}

		return ( isset( $response['errors'] ) ) ? false : true;
	}

	/**
	 * Check to see if the license has expired
	 *
	 * @param bool $skip_transient_check
	 *
	 * @return array|mixed
	 */
	public function is_licence_expired( $skip_transient_check = false ) {
		$licence = $this->get_licence_key();

		if ( empty( $licence ) ) {
			$settings_link = sprintf( '<a href="%s" class="js-action-link as3cf-enter-licence">%s</a>', $this->admin_url( $this->plugin->settings_url_path ) . $this->plugin->settings_url_hash, __( 'enter your license key', 'amazon-s3-and-cloudfront' ) );
			$message       = sprintf( __( 'To finish activating %1$s, %2$s. If you don\'t have a license key, you may <a href="%3$s">purchase one</a>.', 'amazon-s3-and-cloudfront' ), $this->plugin->name, $settings_link, $this->plugin->purchase_url );

			return array( 'errors' => array( 'no_licence' => $message ) );
		}

		if ( ! $skip_transient_check ) {
			$trans = get_site_transient( $this->plugin->prefix . '_licence_response' );
			if ( false !== $trans ) {
				return json_decode( $trans, true );
			}
		}

		return json_decode( $this->check_licence( $licence ), true );
	}

	/**
	 * Check the license validity and fetch a response from the Delicious Brains API
	 *
	 * @param string $licence_key
	 *
	 * @return bool|mixed
	 */
	public function check_licence( $licence_key ) {
		if ( empty( $licence_key ) ) {
			return false;
		}

		$response = $this->check_support_access( $licence_key, $this->home_url );
		$response = $this->store_licence_addon_data( $response, true );

		$response = apply_filters( $this->plugin->prefix . '_check_licence_response', $response );

		set_site_transient( $this->plugin->prefix . '_licence_response', $response, $this->transient_timeout );

		return $response;
	}

	/**
	 * Process and store the Addon data after a license request
	 *
	 * @param string|array $response
	 * @param bool         $encoded
	 *
	 * @return array|mixed|string|void
	 */
	protected function store_licence_addon_data( $response, $encoded = false ) {
		$decoded_response = $encoded ? json_decode( $response, ARRAY_A ) : $response;

		if ( isset( $decoded_response['addon_list'] ) ) {
			// Save the addons list for use when installing
			// Don't really need to expire it ever, but let's clean it up after 60 days
			set_site_transient( $this->plugin->prefix . '_addons', $decoded_response['addon_list'], DAY_IN_SECONDS * 60 );
		}

		if ( isset( $decoded_response['addons_available_list'] ) ) {
			// Save the available addons list for use
			set_site_transient( $this->plugin->prefix . '_addons_available', $decoded_response['addons_available_list'], DAY_IN_SECONDS * 60 );
		}

		$response = $encoded ? json_encode( $decoded_response ) : $decoded_response;

		return $response;
	}

	/**
	 * Remove the license from the settings if defined as a constant
	 */
	public function remove_licence_when_constant_set() {
		$licence_key = $this->get_plugin_licence_key();
		// Remove licence from the database if constant is set
		if ( $this->is_licence_constant() && ! empty( $licence_key ) ) {
			$this->set_licence_key( '' );
		}
	}

	/**
	 * Returns a formatted message dependant on the status of the licence.
	 *
	 * @param bool $trans
	 *
	 * @return string
	 */
	function get_licence_status_message( $trans = false ) {
		$licence               = $this->get_licence_key();
		$api_response_provided = true;

		if ( empty( $licence ) && ! $trans ) {
			$message = sprintf( __( '<strong>Activate Your License</strong> &mdash; Please <a href="#" class="%s">enter your license key</a>.', 'amazon-s3-and-cloudfront' ), 'js-action-link enter-licence' );

			return $message;
		}

		if ( ! $trans ) {
			$trans = get_site_transient( $this->plugin->prefix . '_licence_response' );

			if ( false === $trans ) {
				$trans = $this->check_licence( $licence );
				$trans = $this->store_licence_addon_data( $trans, true );
			}

			$trans                 = json_decode( $trans, true );
			$api_response_provided = false;
		}

		if ( isset( $trans['dbrains_api_down'] ) ) {
			return __( "<strong>We've temporarily activated your license and will complete the activation once the Delicious Brains API is available again.</strong>", 'amazon-s3-and-cloudfront' );
		}

		$errors = $trans['errors'];

		$check_licence_again_url = $this->admin_url( $this->plugin->settings_url_path . '&nonce=' . wp_create_nonce( $this->plugin->prefix . '-check-licence' ) . '&' . $this->plugin->prefix . '-check-licence=1' . $this->plugin->settings_url_hash );

		if ( isset( $errors['connection_failed'] ) ) {
			$message = $errors['connection_failed'];
		} elseif ( isset( $errors['subscription_cancelled'] ) ) {
			$message = sprintf( __( '<strong>Your License Was Cancelled</strong> &mdash; Please visit <a href="%s" target="_blank">My Account</a> to renew or upgrade your license.', 'amazon-s3-and-cloudfront' ), $this->plugin->account_url );
			$message .= sprintf( '<br /><a href="%s">%s</a>', $check_licence_again_url, __( 'Check my license again', 'amazon-s3-and-cloudfront' ) );
		} elseif ( isset( $errors['subscription_expired'] ) ) {
			$message = sprintf( __( '<strong>Your License Has Expired</strong> &mdash; Your expired license has been added to this install. Please visit <a href="%s" target="_blank">My Account</a> to renew your license and continue receiving plugin updates and access to email support.', 'amazon-s3-and-cloudfront' ), $this->plugin->account_url );
			$message .= sprintf( '<br /><a href="%s">%s</a>', $check_licence_again_url, __( 'Check my license again', 'amazon-s3-and-cloudfront' ) );
		} elseif ( isset( $errors['no_activations_left'] ) ) {
			$message = sprintf( __( '<strong>No Activations Left</strong> &mdash; Please visit <a href="%s" target="_blank">My Account</a> to upgrade your license or deactivate a previous activation.', 'amazon-s3-and-cloudfront' ), $this->plugin->account_url );
			$message .= sprintf( ' <a href="%s">%s</a>', $check_licence_again_url, __( 'Check my license again', 'amazon-s3-and-cloudfront' ) );
		} elseif ( isset( $errors['licence_not_found'] ) ) {
			if ( ! $api_response_provided ) {
				$message = sprintf( __( '<strong>Your License Was Not Found</strong> &mdash; Make sure you have copied your licence key exactly as it appears in your email or from <a href="%s" target="_blank">My Account</a>.', 'amazon-s3-and-cloudfront' ), $this->plugin->account_url );
				if ( $this->is_licence_constant() ) {
					$message = sprintf( __( '<strong>Your License Was Not Found</strong> &mdash; Perhaps you made a typo when defining your %s constant in your wp-config.php? Please visit <a href="%s" target="_blank">My Account</a> to double check your license key.', 'amazon-s3-and-cloudfront' ), $this->get_licence_constant_name(), $this->plugin->account_url );
				}
				$message .= sprintf( ' <a href="%s">%s</a>', $check_licence_again_url, __( 'Check my license again', 'amazon-s3-and-cloudfront' ) );
			} else {
				$error   = reset( $errors );
				$message = __( '<strong>Your License Was Not Found</strong> &mdash; ', 'amazon-s3-and-cloudfront' );
				$message .= $error;
			}
		} elseif ( isset( $errors['activation_deactivated'] ) ) {
			$message = sprintf( '<strong>%s</strong> &mdash; ', __( 'Your License Is Inactive', 'amazon-s3-and-cloudfront' ) );
			$message .= sprintf( '%s <a href="#" class="js-action-link reactivate-licence">%s</a>', __( 'Your license has been deactivated for this install.', 'amazon-s3-and-cloudfront' ), __( 'Reactivate License', 'amazon-s3-and-cloudfront' ) );
		} else {
			$error   = reset( $errors );
			$message = sprintf( __( '<strong>An Unexpected Error Occurred</strong> &mdash; Please contact us at <a href="%1$s">%2$s</a> and quote the following:', 'amazon-s3-and-cloudfront' ), 'mailto:nom@deliciousbrains.com', 'nom@deliciousbrains.com' );
			$message .= sprintf( '<p>%s</p>', $error );
		}

		return apply_filters( $this->plugin->prefix . '_licence_status_message', $message, $errors );
	}

	/**
	 * Return a license key formatted with HTML to mask all but the last part
	 *
	 * @param string $licence
	 *
	 * @return string
	 */
	public function mask_licence( $licence ) {
		$licence_parts  = explode( '-', $licence );
		$i              = count( $licence_parts ) - 1;
		$masked_licence = '';

		foreach ( $licence_parts as $licence_part ) {
			if ( $i == 0 ) {
				$masked_licence .= $licence_part;
				continue;
			}

			$masked_licence .= '<span class="bull">';
			$masked_licence .= str_repeat( '&bull;', strlen( $licence_part ) ) . '</span>&ndash;';
			--$i;
		}

		return $masked_licence;
	}

	/**
	 * Helper method to display markup of the masked license
	 *
	 * @return string
	 */
	public function get_formatted_masked_licence() {
		return sprintf(
			'<p class="masked-licence">%s <a href="%s">%s</a></p>',
			$this->mask_licence( $this->get_plugin_licence_key() ),
			$this->admin_url( $this->plugin->settings_url_path . '&nonce=' . wp_create_nonce( $this->plugin->prefix . '-remove-licence' ) . '&' . $this->plugin->prefix . '-remove-licence=1' . $this->plugin->settings_url_hash ),
			_x( 'Remove', 'Delete license', 'amazon-s3-and-cloudfront' )
		);
	}

	/**
	 * Check for {prefix}-disable-ssl and related nonce
	 * if found temporarily disable ssl via transient
	 *
	 * @return void
	 */
	function http_disable_ssl() {
		if ( isset( $_GET[ $this->plugin->prefix . '-disable-ssl' ] ) && wp_verify_nonce( $_GET['nonce'], $this->plugin->prefix . '-disable-ssl' ) ) { // input var okay
			set_site_transient( $this->plugin->prefix . '_temporarily_disable_ssl', '1', DAY_IN_SECONDS * 30 ); // 30 days
			$hash = ( isset( $_GET['hash'] ) ) ? '#' . sanitize_title( $_GET['hash'] ) : ''; // input var okay
			// delete the licence transient as we want to attempt to fetch the licence details again
			delete_site_transient( $this->plugin->prefix . '_licence_response' );
			// redirecting here because we don't want to keep the query string in the web browsers address bar
			wp_redirect( $this->admin_url( $this->plugin->settings_url_path . $hash ) );
			exit;
		}
	}

	/**
	 * Check for {prefix}-remove-licence and related nonce
	 * if found cleanup routines related to licenced product
	 *
	 * @return void
	 */
	function http_remove_licence() {
		if ( isset( $_GET[ $this->plugin->prefix . '-remove-licence' ] ) && wp_verify_nonce( $_GET['nonce'], $this->plugin->prefix . '-remove-licence' ) ) { // input var okay
			$this->remove_licence();

			// redirecting here because we don't want to keep the query string in the web browsers address bar
			wp_redirect( $this->admin_url( $this->plugin->settings_url_path . $this->plugin->settings_url_hash ) );
			exit;
		}
	}

	/**
	 * Ajax licence removal handler.
	 */
	public function ajax_remove_licence() {
		check_ajax_referer( 'remove-licence' );

		if ( $this->is_licence_constant() ) {
			wp_send_json_error( array(
				'message' => __( 'Your licence key is currently defined via a constant and must be removed manually.', 'amazon-s3-and-cloudfront' ),
			) );
		}

		$this->remove_licence();

		wp_send_json_success( array(
			'message' => __( 'Licence key removed successfully.', 'amazon-s3-and-cloudfront' ),
		) );
	}

	/**
	 * Remove the license key.
	 */
	protected function remove_licence() {
		$this->set_licence_key( '' );

		// delete these transients as they contain information only valid for authenticated licence holders
		delete_site_transient( 'update_plugins' );
		delete_site_transient( $this->plugin->prefix . '_upgrade_data' );
		delete_site_transient( $this->plugin->prefix . '_licence_response' );

		do_action( $this->plugin->prefix . '_http_remove_licence' );
	}

	/**
	 * Check for {prefix}-check-licence and related nonce
	 * if found refresh licence details
	 *
	 * @return void
	 */
	function http_refresh_licence() {
		if ( isset( $_GET[ $this->plugin->prefix . '-check-licence' ] ) && wp_verify_nonce( $_GET['nonce'], $this->plugin->prefix . '-check-licence' ) ) { // input var okay
			$hash = ( isset( $_GET['hash'] ) ) ? '#' . sanitize_title( $_GET['hash'] ) : ''; // input var okay
			// delete the licence transient as we want to attempt to fetch the licence details again
			delete_site_transient( $this->plugin->prefix . '_licence_response' );
			do_action( $this->plugin->prefix . '_http_refresh_licence' );

			$sendback = $this->admin_url( $this->plugin->settings_url_path . $hash );
			if ( isset( $_GET['sendback'] ) ) {
				$sendback = $_GET['sendback'];
			}

			// redirecting because we don't want to keep the query string in the web browsers address bar
			wp_redirect( esc_url_raw( $sendback ) );
			exit;
		}
	}

	/**
	 * AJAX handler for activating a licence.
	 */
	public function ajax_activate_licence() {
		$this->check_ajax_referer( 'activate-licence' );

		$licence_key       = filter_input( INPUT_POST, 'licence_key' );
		$api_response_json = $this->activate_licence( $licence_key, $this->home_url );
		$api_response      = json_decode( $api_response_json, true );
		$api_down          = ! empty( $api_response['dbrains_api_down'] );
		$response          = array(
			'message' => __( 'License activated successfully.', 'amazon-s3-and-cloudfront' ),
		);

		$errors = isset( $api_response['errors'] ) ? $api_response['errors'] : array();

		// Remove insignificant errors
		unset( $errors['activation_deactivated'], $errors['subscription_expired'] );

		if ( ! $errors && ! $api_down ) {
			$this->set_licence_key( $licence_key );
			$response['masked_licence'] = $this->get_masked_licence();
		} else {
			set_site_transient( $this->plugin->prefix . '_licence_response', $api_response_json, $this->transient_timeout );

			$response['message'] = $this->get_licence_status_message( $api_response );

			if ( $api_down ) {
				$errors[] = $api_response['dbrains_api_down'];
			}

			$response['errors'] = $errors;
			$response           = apply_filters( $this->plugin->prefix . '_activate_licence_response', $response );
		}

		if ( $errors ) {
			wp_send_json_error( $response );
		}

		wp_send_json_success( $response );
	}

	/**
	 * AJAX handler for checking a licence.
	 */
	public function ajax_check_licence() {
		$this->check_ajax_referer( 'check-licence' );

		$licence          = ( empty( $_POST['licence_key'] ) ? $this->get_licence_key() : sanitize_key( $_POST['licence_key'] ) );
		$response         = $this->check_licence( $licence );
		$decoded_response = json_decode( $response, ARRAY_A );

		if ( ! empty( $decoded_response['dbrains_api_down'] ) ) {
			$help_message = get_site_transient( $this->plugin->prefix . '_help_message' );

			if ( ! $help_message ) {
				ob_start();
				?>
				<p><?php _e( 'If you have an <strong>active license</strong>, you may send an email to the following address.', 'amazon-s3-and-cloudfront' ); ?></p>
				<p>
					<strong><?php _e( 'Please copy the Diagnostic Info &amp; Error Log info below into a text file and attach it to your email.', 'amazon-s3-and-cloudfront' ); ?></strong>
				</p>
				<p class="email">
					<a class="button" href="mailto:<?php echo $this->plugin->get_email_address_name(); ?>@deliciousbrains.com">
						<?php echo $this->plugin->get_email_address_name(); ?>@deliciousbrains.com
					</a>
				</p>
				<?php
				$help_message = ob_get_clean();
			}

			$decoded_response['message'] = $help_message;
		} elseif ( ! empty( $decoded_response['errors'] ) ) {
			$decoded_response['htmlErrors'] = array( sprintf( '<div class="notification-message warning-notice inline-message invalid-licence">%s</div>', $this->get_licence_status_message() ) );
		} elseif ( ! empty( $decoded_response['message'] ) && ! get_site_transient( $this->plugin->prefix . '_help_message' ) ) {
			set_site_transient( $this->plugin->prefix . '_help_message', $decoded_response['message'], $this->transient_timeout );
		}

		$decoded_response = $this->store_licence_addon_data( $decoded_response );

		$decoded_response = apply_filters( $this->plugin->prefix . '_ajax_check_licence_response', $decoded_response );

		wp_send_json( $decoded_response );
	}

	/**
	 * AJAX handler for reactivating this instance for the activated license
	 *
	 * @return array Empty array or an array containing an error message.
	 */
	public function ajax_reactivate_licence() {
		$this->check_ajax_referer( 'reactivate-licence' );

		$return = array();

		$response = $this->reactivate_licence( $this->get_licence_key(), $this->home_url );

		$decoded_response = json_decode( $response, true );

		if ( isset( $decoded_response['dbrains_api_down'] ) ) {
			$return['dbrains_api_down'] = 1;

			$return['body'] = $decoded_response['dbrains_api_down'];
			$result         = $this->end_ajax( json_encode( $return ) );

			return $result;
		}

		if ( isset( $decoded_response['errors'] ) ) {
			$return[ $this->plugin->prefix . '_error' ] = 1;

			$return['body'] = reset( $decoded_response['errors'] );
			$this->log_error( $return['body'], $decoded_response );
			$result = $this->end_ajax( json_encode( $return ) );

			return $result;
		}

		delete_site_transient( $this->plugin->prefix . '_upgrade_data' );
		delete_site_transient( $this->plugin->prefix . '_licence_response' );

		$result = $this->end_ajax( json_encode( array() ) );

		return $result;
	}

	/**
	 * Helper to end an AJAX request
	 *
	 * @param bool|string $return
	 */
	protected function end_ajax( $return = false ) {
		echo ( false === $return ) ? '' : $return;
		wp_die();
	}

	/**
	 * Custom verification of the AJAX request to prevent processing requests
	 *
	 * @param string $action Action nonce name
	 */
	protected function check_ajax_referer( $action ) {
		$result = check_ajax_referer( $action, 'nonce', false );

		if ( false === $result ) {
			$return = array(
				$this->plugin->prefix . '_error' => 1,
				'body'                           => sprintf( __( 'Invalid nonce for: %s', 'amazon-s3-and-cloudfront' ), $action ),
			);
			$this->end_ajax( json_encode( $return ) );
		}
	}

	/**
	 * Get the raw licence masked with bullets except for the last segment.
	 *
	 * @return bool|string false if no licence, masked string otherwise.
	 */
	public function get_masked_licence() {
		$licence_key = $this->get_licence_key();

		if ( ! $licence_key ) {
			return false;
		}

		$licence_segments  = explode( '-', $licence_key );
		$visible_segment   = array_pop( $licence_segments );
		$masked_segments   = array_map( function ( $segment ) {
			return str_repeat( '•', strlen( $segment ) );
		}, $licence_segments );
		$masked_segments[] = $visible_segment;

		return join( '-', $masked_segments );
	}
}
