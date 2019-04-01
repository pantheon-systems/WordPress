<?php

class WPML_Cookie {

	/**
	 * @param string $name
	 * @param string $value
	 * @param        $expires
	 * @param string $path
	 * @param        $domain
	 */
	public function set_cookie( $name, $value, $expires, $path, $domain ) {
		$this->handle_cache_plugins( $name );
		setcookie( $name, $value, $expires, $path, $domain );
	}

	/**
	 * @param string $name
	 *
	 * @return string
	 */
	public function get_cookie( $name ) {
		if ( isset( $_COOKIE[ $name ] ) ) {
			return $_COOKIE[ $name ];
		}
		return '';
	}

	/**
	 * simple wrapper for \headers_sent
	 *
	 * @return bool
	 */
	public function headers_sent() {
		return headers_sent();
	}

	/**
	 * @param $name
	 */
	private function handle_cache_plugins( $name ) {
		// @todo uncomment or delete when #wpmlcore-5796 is resolved
		//do_action( 'wpsc_add_cookie', $name );
	}
}
