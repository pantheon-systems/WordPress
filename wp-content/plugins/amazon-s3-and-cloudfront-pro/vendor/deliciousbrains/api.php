<?php
/**
 * API Wrapper Class
 *
 *
 * @package     deliciousbrains
 * @subpackage  api
 * @copyright   Copyright (c) 2015, Delicious Brains
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Delicious_Brains_API Class
 *
 * This class is a wrapper for the Delicious Brains WooCommerce API
 *
 * @since 0.1
 */
class Delicious_Brains_API {

	/**
	 * @var string
	 */
	public $api_url;

	/**
	 * @var string
	 */
	public $api_base = 'https://api.deliciousbrains.com';

	/**
	 * @var string
	 */
	public $api_status_url = 'http://s3.amazonaws.com/cdn.deliciousbrains.com/status.json';

	/**
	 * @var int
	 */
	public $transient_timeout;

	/**
	 * @var int
	 */
	public $transient_retry_timeout;

	function __construct() {
		$this->transient_timeout       = HOUR_IN_SECONDS * 12;
		$this->transient_retry_timeout = HOUR_IN_SECONDS * 2;

		if ( defined( 'DBRAINS_API_BASE' ) ) {
			$this->api_base = DBRAINS_API_BASE;
		}

		$this->api_url = $this->api_base . '/?wc-api=delicious-brains';
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
			'timeout' => 30,
		);
	}

	/**
	 * Wrapper for wp_remote_get
	 *
	 * @param string $url
	 * @param array  $args
	 *
	 * @return array|WP_Error
	 */
	public function get( $url, $args = array() ) {
		$defaults = $this->get_default_request_args();

		$args = array_merge( $defaults, $args );

		$response = wp_remote_get( $url, $args );

		return $response;
	}

	/**
	 * Generate the API URL
	 *
	 * @param string $request
	 * @param array  $args
	 *
	 * @return string
	 */
	protected function get_url( $request, $args = array() ) {
		return $this->api_url;
	}

	/**
	 * Main function for communicating with the Delicious Brains API.
	 *
	 * @param string $request
	 * @param array  $args
	 *
	 * @return string|bool
	 */
	public function api_request( $request, $args = array() ) {
		$url      = $this->get_url( $request, $args );
		$response = $this->get( $url );

		if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) ) {
			return false;
		}

		$response = wp_remote_retrieve_body( $response );

		return $response;
	}

	/**
	 * Check the support access for a license key
	 *
	 * @param string $licence_key
	 * @param string $site_url
	 *
	 * @return mixed
	 */
	public function check_support_access( $licence_key = '', $site_url = '' ) {
		$args = array(
			'licence_key' => $licence_key,
			'site_url'    => $site_url,
		);

		$response = $this->api_request( 'check_support_access', $args );

		return $response;
	}

	/**
	 * Activate a license key for an install
	 *
	 * @param string $licence_key
	 * @param string $site_url
	 *
	 * @return mixed
	 */
	public function activate_licence( $licence_key = '', $site_url = '' ) {
		$args = array(
			'licence_key' => $licence_key,
			'site_url'    => $site_url,
		);

		$response = $this->api_request( 'activate_licence', $args );

		return $response;
	}

	/**
	 * Reactivate an install for a license key
	 *
	 * @param string $licence_key
	 * @param string $site_url
	 *
	 * @return mixed
	 */
	public function reactivate_licence( $licence_key = '', $site_url = '' ) {
		$args = array(
			'licence_key' => $licence_key,
			'site_url'    => $site_url,
		);

		$response = $this->api_request( 'reactivate_licence', $args );

		return $response;
	}

	/**
	 * Get the upgrade data for plugin and addons
	 *
	 * @return mixed
	 */
	public function get_upgrade_data() {
		$response = $this->api_request( 'upgrade_data' );

		return $response;
	}

	/**
	 * Get changelog contents for the given plugin slug.
	 *
	 * @param string $slug
	 * @param bool   $beta
	 *
	 * @return bool|string
	 */
	public function get_changelog( $slug, $beta = false ) {
		if ( true === $beta ) {
			$slug .= '-beta';
		}

		$args = array(
			'slug' => $slug,
		);

		$response = $this->api_request( 'changelog', $args );

		return $response;
	}
}
