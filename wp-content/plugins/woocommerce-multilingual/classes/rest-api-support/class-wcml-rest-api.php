<?php

class WCML_REST_API {
	
	/**
	 * Check if is request to the WooCommerce REST API.
	 *
	 * @return bool
	 */
	public function is_rest_api_request(){

		if ( empty( $_SERVER['REQUEST_URI'] ) ) {
			return false;
		}

		$rest_prefix = trailingslashit( rest_get_url_prefix() );
		// Check if WooCommerce endpoint.
		$woocommerce = ( false !== strpos( $_SERVER['REQUEST_URI'], $rest_prefix . 'wc/' ) );

		return apply_filters( 'woocommerce_rest_is_request_to_rest_api', $woocommerce );

	}

	/**
	 * @return int
	 * Returns the version number of the API used for the current request
	 */
	public function get_api_request_version(){
		$version = 0;
		$rest_prefix = trailingslashit( rest_get_url_prefix() );
		if( preg_match( "@" . $rest_prefix . "wc/v([0-9]+)/@", $_SERVER['REQUEST_URI'], $matches ) ){
			$version = intval($matches[1]);
		}
		return $version;
	}

	/**
	 * Use url without the language parameter. Needed for the signature match.
	 */
	public function remove_wpml_global_url_filters(){
		/** var WPML_URL_Filters */
		global $wpml_url_filters;
		remove_filter( 'home_url', array( $wpml_url_filters, 'home_url_filter' ), - 10 );
	}

}
