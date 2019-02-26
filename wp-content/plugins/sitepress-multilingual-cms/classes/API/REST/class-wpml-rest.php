<?php

/**
 * @author OnTheGo Systems
 */
class WPML_Rest {
	private $http;

	/**
	 * WPML_Rest constructor.
	 *
	 * @param WP_Http $http
	 */
	public function __construct( WP_Http $http ) {
		$this->http = $http;
	}

	public function is_available() {
		return function_exists( 'rest_get_server' );
	}

	public function is_rest_request() {
		return defined( 'REST_REQUEST' ) && REST_REQUEST;
	}

	public function has_registered_routes() {
		return (bool) rest_get_server()->get_routes();
	}

	public function has_discovered_routes() {
		return (bool) $this->get_discovered_routes();
	}

	private function get_discovered_routes() {
		$url      = $this->get_discovery_url();
		$response = $this->http->get( $url );
		$body     = json_decode( $response['body'], true );

		return array_key_exists( 'routes', $body ) ? $body['routes'] : array();
	}

	public function get_discovery_url() {
		$url_prefix = 'wp-json';
		if ( function_exists( 'rest_get_url_prefix' ) ) {
			$url_prefix = rest_get_url_prefix();
		}

		return untrailingslashit( trailingslashit( get_site_url() ) . $url_prefix );
	}
}