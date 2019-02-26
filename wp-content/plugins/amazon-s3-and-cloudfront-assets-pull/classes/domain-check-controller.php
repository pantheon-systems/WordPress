<?php

namespace DeliciousBrains\WP_Offload_Media_Assets_Pull;

use WP_REST_Controller;
use WP_REST_Request;

class Domain_Check_Controller extends WP_REST_Controller {

	protected $namespace = 'deliciousbrains/v1';
	protected $rest_base = 'assets-pull/domain-check';

	/**
	 * Register all REST routes for this controller.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, $this->rest_base . '/(?P<key>[\w\d=]+)', array(
			'methods'  => 'GET',
			'callback' => array( $this, 'show' ),
		) );
	}

	/**
	 * Respond to a GET request to the domain check route, with the given key.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return Domain_Check_Response
	 */
	public function show( WP_REST_Request $request ) {
		$response = new Domain_Check_Response( array(
			'key' => $request->get_param( 'key' ),
			'ver' => filter_input( INPUT_GET, 'ver' ),
		) );
		$response->header( 'X-As3cf-Signature', $response->hashed_signature() );

		return $response;
	}

	/**
	 * Get a URL to the show route, with the given key.
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	public function show_url( $key ) {
		return rest_url( "$this->namespace/$this->rest_base/$key" );
	}
}