<?php
/**
 * API Class
 *
 * @package Pantheon HUD
 */

namespace Pantheon\HUD;

/**
 * Pull data from the Pantheon site API.
 */
class API {

	/**
	 * Base URL for all API requests.
	 *
	 * @var string
	 */
	const API_URL_BASE = 'https://api.live.getpantheon.com:8443';

	/**
	 * Holds the domains data when present.
	 *
	 * @var array
	 */
	private $domains_data;

	/**
	 * Holds the environment settings data when present.
	 *
	 * @var array
	 */
	private $environment_settings_data;

	/**
	 * Get the id of the Pantheon site.
	 *
	 * @return string
	 */
	public function get_site_id() {
		return ! empty( $_ENV['PANTHEON_SITE'] ) ? $_ENV['PANTHEON_SITE'] : '';
	}

	/**
	 * Get the name of the Pantheon site.
	 *
	 * @return string
	 */
	public function get_site_name() {
		return ! empty( $_ENV['PANTHEON_SITE_NAME'] ) ? $_ENV['PANTHEON_SITE_NAME'] : '';
	}

	/**
	 * Get the timestamp of the last code push
	 *
	 * @return int
	 */
	public function get_last_code_push_timestamp() {
		$environment_settings = $this->get_environment_settings_data();
		if ( ! empty( $environment_settings['last_code_push']['timestamp'] ) ) {
			return strtotime( $environment_settings['last_code_push']['timestamp'] );
		} else {
			return 0;
		}
	}

	/**
	 * Get the primary url for the environment
	 *
	 * @param string $env Environment to fetch the domains of.
	 * @return string
	 */
	public function get_primary_environment_url( $env ) {
		$domains = $this->get_domains_data( $env );
		if ( ! empty( $domains[0]['key'] ) ) {
			return $domains[0]['key'];
		} else {
			return '';
		}
	}

	/**
	 * Get details about this particular environments
	 *
	 * @return array
	 */
	public function get_environment_details() {
		$env                  = ! empty( $_ENV['PANTHEON_ENVIRONMENT'] ) ? $_ENV['PANTHEON_ENVIRONMENT'] : 'dev';
		$details              = array(
			'web'      => array(),
			'database' => array(),
		);
		$environment_settings = $this->get_environment_settings_data();
		if ( ! empty( $environment_settings['appserver'] ) ) {
			$details['web']['appserver_count'] = $environment_settings['appserver'];
		}
		$php_version = $this->get_php_version();
		if ( $php_version ) {
			$php_version                   = (string) $php_version;
			$details['web']['php_version'] = 'PHP ' . $php_version;
		}
		if ( ! empty( $environment_settings['dbserver'] ) ) {
			$details['database']['dbserver_count'] = $environment_settings['dbserver'];
		}
		if ( isset( $environment_settings['allow_read_slaves'] ) ) {
			$details['database']['read_replication_enabled'] = (bool) $environment_settings['allow_read_slaves'];
		}
		return $details;
	}

	/**
	 * Gets the PHP version for the site.
	 *
	 * @return string|false
	 */
	public function get_php_version() {
		return ! empty( $_ENV['php_version'] ) ? $_ENV['php_version'] : PHP_VERSION;
	}

	/**
	 * Gets the domains data.
	 *
	 * @param string $env Environment to fetch the domains of.
	 * @return array
	 */
	private function get_domains_data( $env ) {
		if ( isset( $this->domains_data[ $env ] ) ) {
			return $this->domains_data[ $env ];
		}
		if ( ! empty( $env ) ) {
			$url                        = sprintf( '%s/sites/self/environments/%s/domains', self::API_URL_BASE, $env );
			$this->domains_data[ $env ] = self::fetch_api_data( $url );
		} else {
			$this->domains_data[ $env ] = [];
		}
		return $this->domains_data[ $env ];
	}

	/**
	 * Gets the environment settings data.
	 *
	 * @return array
	 */
	private function get_environment_settings_data() {
		if ( isset( $this->environment_settings_data ) ) {
			return $this->environment_settings_data;
		}
		if ( ! empty( $_ENV['PANTHEON_ENVIRONMENT'] ) ) {
			$url                             = sprintf( '%s/sites/self/environments/%s/settings', self::API_URL_BASE, $_ENV['PANTHEON_ENVIRONMENT'] );
			$this->environment_settings_data = self::fetch_api_data( $url );
		} else {
			$this->environment_settings_data = [];
		}
		return $this->environment_settings_data;
	}

	/**
	 * Fetch data from a given Pantheon API URL.
	 *
	 * @param string $url URL from which to fetch data.
	 * @return array
	 */
	private function fetch_api_data( $url ) {

		// Function internal to Pantheon infrastructure.
		$pem_file = apply_filters( 'pantheon_hud_pem_file', null );
		if ( function_exists( 'pantheon_curl' ) ) {
			$bits     = wp_parse_url( $url );
			$response = pantheon_curl( sprintf( '%s://%s%s', $bits['scheme'], $bits['host'], $bits['path'] ), null, $bits['port'] );
			$body     = ! empty( $response['body'] ) ? $response['body'] : '';
			return json_decode( $body, true );

			// For those developing locally who know what they're doing.
		} elseif ( $pem_file || ( defined( 'PANTHEON_HUD_PHPUNIT_RUNNING' ) && PANTHEON_HUD_PHPUNIT_RUNNING ) ) {
			$require_curl = function() {
				return array( 'curl' );
			};
			add_filter( 'http_api_transports', $require_curl );
			$client_cert = function( $handle ) use ( $pem_file ) {
				curl_setopt( $handle, CURLOPT_SSLCERT, $pem_file ); // phpcs:ignore WordPress.WP.AlternativeFunctions
			};
			add_action( 'http_api_curl', $client_cert );
			$response = wp_remote_get(
				$url,
				array(
					'sslverify' => false, // yolo.
				)
			);
			if ( is_wp_error( $response ) ) {
				return array();
			}
			remove_action( 'http_api_curl', $client_cert );
			remove_filter( 'http_api_transports', $require_curl );
			if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
				return array();
			}
			$body = wp_remote_retrieve_body( $response );
			return json_decode( $body, true );
		}
		return array();
	}

}
