<?php
/**
 * API Base Class
 *
 *
 * @package     deliciousbrains
 * @subpackage  api/base
 * @copyright   Copyright (c) 2015, Delicious Brains
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Delicious_Brains_API_Base Class
 *
 * This class handles communication with the Delicious Brains WooCommerce API
 *
 * @since 0.1
 */
class Delicious_Brains_API_Base extends Delicious_Brains_API {

	/**
	 * @var Delicious_Brains_API_Plugin
	 */
	public $plugin;

	/**
	 * @var string
	 */
	public $home_url;

	/**
	 * @param Delicious_Brains_API_Plugin $plugin
	 */
	function __construct( Delicious_Brains_API_Plugin $plugin ) {
		$this->plugin   = $plugin;
		$this->home_url = $this->get_home_url();

		parent::__construct();
	}

	/**
	 * Get home URL.
	 *
	 * @return string
	 */
	private function get_home_url() {
		$home_url = get_option( 'home' );

		if ( is_multisite() && $this->plugin->is_network_activated ) {
			// Make sure always use the network URL in API communication
			$current_site = get_current_site();
			$home_url     = 'http://' . $current_site->domain;
		}

		return untrailingslashit( set_url_scheme( $home_url, 'http' ) );
	}

	/**
	 * Generate the API URL
	 *
	 * @param string $request
	 * @param array  $args
	 *
	 * @return string
	 */
	function get_url( $request, $args = array() ) {
		$url                       = $this->api_url;
		$args['request']           = $request;
		$args['product']           = $this->plugin->slug;
		$args['version']           = $this->plugin->version;
		$args['locale']            = urlencode( get_locale() );
		$args['php_version']       = urlencode( phpversion() );
		$args['wordpress_version'] = urlencode( get_bloginfo( 'version' ) );

		$args = apply_filters( $this->plugin->prefix . '_' . $request . '_request_args', $args );

		if ( false !== get_site_transient( 'dbrains_temporarily_disable_ssl' ) && 0 === strpos( $this->api_url, 'https://' ) ) {
			$url = substr_replace( $url, 'http', 0, 5 );
		}

		$url = add_query_arg( $args, $url );

		return esc_url_raw( $url );
	}

	/**
	 * Main function for communicating with the Delicious Brains API.
	 *
	 * @param string $request
	 * @param array  $args
	 *
	 * @return mixed
	 */
	function api_request( $request, $args = array() ) {
		if ( ( $check = $this->check_api_down() ) ) {
			return $check;
		}

		$url      = $this->get_url( $request, $args );
		$response = $this->get( $url );

		if ( is_wp_error( $response ) || (int) $response['response']['code'] < 200 || (int) $response['response']['code'] > 399 ) {
			// Couldn't connect successfully to the API
			$this->log_error( $response );

			if ( true === $this->is_api_down() ) {
				// API is down
				return $this->check_api_down();
			}

			return $this->connection_failed_response();
		}

		return $response['body'];
	}

	/**
	 * Return a warning notice if the API is down as a response to an API request
	 *
	 * @return bool|mixed|string|void
	 */
	function check_api_down() {
		$trans = get_site_transient( 'dbrains_api_down' );

		if ( false !== $trans ) {
			$api_down_message = sprintf( '<div class="dbrains-api-down updated warning inline-message">%s</div>', $trans );

			return json_encode( array( 'dbrains_api_down' => $api_down_message ) );
		}

		return false;
	}

	/**
	 * Return a connection failed notice as a response to an API request
	 *
	 * @return string
	 */
	protected function connection_failed_response() {
		$connection_failed_message = __( '<strong>Could not connect to deliciousbrains.com</strong> &mdash; You will not receive update notifications or be able to activate your license until this is fixed.', 'amazon-s3-and-cloudfront' );
		$connection_failed_message .= '</p><p>';

		if ( defined( 'WP_HTTP_BLOCK_EXTERNAL' ) && WP_HTTP_BLOCK_EXTERNAL ) {
			$url_parts = parse_url( $this->api_base );
			$host      = $url_parts['host'];
			if ( ! defined( 'WP_ACCESSIBLE_HOSTS' ) || strpos( WP_ACCESSIBLE_HOSTS, $host ) === false ) {
				$connection_failed_message .= sprintf( __( 'We\'ve detected that <code>WP_HTTP_BLOCK_EXTERNAL</code> is enabled and the host <strong>%1$s</strong> has not been added to <code>WP_ACCESSIBLE_HOSTS</code>. Please disable <code>WP_HTTP_BLOCK_EXTERNAL</code> or add <strong>%1$s</strong> to <code>WP_ACCESSIBLE_HOSTS</code> to continue. <a href="%2$s" target="_blank">More information</a>', 'amazon-s3-and-cloudfront' ), esc_attr( $host ), 'https://deliciousbrains.com/wp-migrate-db-pro/doc/wp_http_block_external/' );
			}
		} else {
			$disable_ssl_url = $this->admin_url( $this->plugin->settings_url_path . '&nonce=' . wp_create_nonce( $this->plugin->prefix . '-disable-ssl' ) . '&' . $this->plugin->prefix . '-disable-ssl=1' );
			$connection_failed_message .= sprintf( __( 'This issue is often caused by an improperly configured SSL server (https). We recommend <a href="%1$s" target="_blank">fixing the SSL configuration on your server</a>, but if you need a quick fix you can:%2$s', 'amazon-s3-and-cloudfront' ), 'https://deliciousbrains.com/wp-migrate-db-pro/doc/could-not-connect-deliciousbrains-com/', sprintf( '<p><a href="%1$s" class="temporarily-disable-ssl button">%2$s</a></p>', $disable_ssl_url, __( 'Temporarily disable SSL for connections to deliciousbrains.com', 'amazon-s3-and-cloudfront' ) ) );
		}

		return json_encode( array( 'errors' => array( 'connection_failed' => $connection_failed_message ) ) );
	}

	/**
	 * Is the Delicious Brains API down?
	 *
	 * If not available then a 'dbrains_api_down' transient will be set with an appropriate message.
	 *
	 * @return bool
	 */
	protected function is_api_down() {
		if ( false !== get_site_transient( 'dbrains_api_down' ) ) {
			return true;
		}

		$response = $this->get( $this->api_status_url );

		// Can't get to api status url so fall back to normal failure handling.
		if ( is_wp_error( $response ) || 200 != (int) $response['response']['code'] || empty( $response['body'] ) ) {
			return false;
		}

		$json = json_decode( $response['body'], true );

		// Can't decode json so fall back to normal failure handling.
		if ( ! $json ) {
			return false;
		}

		// Decoded JSON data doesn't seem to be the format we expect or is not down, so fall back to normal failure handling.
		if ( ! isset( $json['api']['status'] ) || 'down' != $json['api']['status'] ) {
			return false;
		}

		$message = $this->get_down_message( $json['api'] );

		set_site_transient( 'dbrains_api_down', $message, $this->transient_retry_timeout );

		return true;
	}

	/**
	 * Form the error message about the API being down
	 *
	 * @param array $response
	 *
	 * @return string
	 */
	protected function get_down_message( $response ) {
		$message = __( "<strong>Delicious Brains API is Down — </strong>Unfortunately we're experiencing some problems with our server.", 'amazon-s3-and-cloudfront' );

		if ( ! empty( $response['updated'] ) ) {
			$updated     = $response['updated'];
			$updated_ago = sprintf( _x( '%s ago', 'ex. 2 hours ago', 'amazon-s3-and-cloudfront' ), human_time_diff( strtotime( $updated ) ) );
		}

		if ( ! empty( $response['message'] ) ) {
			$message .= '<br />';
			$message .= __( "Here's the most recent update on its status", 'amazon-s3-and-cloudfront' );
			if ( ! empty( $updated_ago ) ) {
				$message .= ' (' . $updated_ago . ')';
			}
			$message .= ': <em>' . $response['message'] . '</em>';
		}

		return $message;
	}

	/**
	 * Default request arguments passed to an HTTP request
	 *
	 * @see wp_remote_request() For more information on the available arguments.
	 *
	 * @return array
	 */
	protected function get_default_request_args() {
		return array(
			'timeout'   => 30,
			'blocking'  => true,
			'sslverify' => $this->verify_ssl(),
		);
	}

	/**
	 * Retrieve the url to the admin area for the site.
	 * Handles if the plugin is a network activated plugin.
	 *
	 * @param string $path Optional path relative to the admin url
	 *
	 * @return string|void
	 */
	public function admin_url( $path ) {
		if ( $this->plugin->is_network_activated ) {
			$url = network_admin_url( $path );
		} else {
			$url = admin_url( $path );
		}

		return $url;
	}

	/**
	 * Error log method
	 *
	 * @param mixed $error
	 * @param bool  $additional_error_var
	 */
	function log_error( $error, $additional_error_var = false ) {
		error_log( print_r( $error, true ) );
	}

	/**
	 * Use SSL verification for requests
	 *
	 * @return bool
	 */
	function verify_ssl() {
		return false;
	}
}