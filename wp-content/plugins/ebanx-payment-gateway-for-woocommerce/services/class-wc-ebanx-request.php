<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_EBANX_Request
 */
class WC_EBANX_Request {
	const DEFAULT_VALUE = 'WC_EBANX_Request::DEFAULT_VALUE';

	/**
	 * Reads _REQUEST and gets the value from '$param'
	 *
	 * @param  string $param   The key from _REQUEST.
	 * @param  mixed  $default What you want to return if there's no $_REQUEST[$param].
	 * @return mixed          $_REQUEST[$param] value OR default OR
	 * @throws Exception Throws exception if there's no $_REQUEST[$param] and no $default.
	 */
	public static function read( $param, $default = self::DEFAULT_VALUE ) {
		if ( self::has( $param ) ) {
			if ( isset( $_REQUEST[ $param ] ) ) {
				// @codingStandardsIgnoreLine
				return $_REQUEST[ $param ];
			}
		}
		if ( self::DEFAULT_VALUE == $default ) {
			throw new Exception( 'Missing argument "' . $param . '".' );
		}
		return $default;
	}

	/**
	 * Sets $value in $_REQUEST[$param]
	 *
	 * @param string $param The key from _REQUEST.
	 * @param mixed  $value The value you want to set.
	 * @return void
	 */
	public static function set( $param, $value ) {
		$_REQUEST[ $param ] = $value;
	}

	/**
	 * Reads all keys in array $params from $_REQUEST
	 *
	 * @param  array $params  Array of strings, the values you want to read.
	 * @param  mixed $default What you want to return if there's no value in each $_REQUEST[$params[]].
	 * @return array          An array with results OR
	 * @throws Exception Throws exception if there's no $_REQUEST[$param] and no $default.
	 */
	public static function read_values( $params, $default = self::DEFAULT_VALUE ) {
		return array_map(
			function( $param ) use ( $default ) {
				return self::read( $param, $default[ $param ] );
			}, $params
		);
	}

	/**
	 * Check if a key is set on $_REQUEST
	 *
	 * @param string $key The key you want to check.
	 *
	 * @return boolean        True if $_REQUEST has $key key.
	 */
	public static function has( $key ) {
		// @codingStandardsIgnoreLine
		return array_key_exists( $key, $_REQUEST );
	}

	/**
	 * Checks if $_GET is empty.
	 * Necessary for routers.
	 *
	 * @return boolean True if $_GET is empty
	 */
	public static function is_get_empty() {
		// @codingStandardsIgnoreLine
		return empty( $_GET );
	}

	/**
	 * Checks if $_POST is empty.
	 * Necessary for routers.
	 *
	 * @return boolean True if $_POST is empty
	 */
	public static function is_post_empty() {
		// @codingStandardsIgnoreLine
		return empty( $_POST );
	}
}
