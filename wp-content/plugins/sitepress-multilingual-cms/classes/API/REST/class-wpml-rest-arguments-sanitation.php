<?php

/**
 * @author OnTheGo Systems
 *
 * The following method can be used as REST arguments sanitation callback
 *
 * @see    http://v2.wp-api.org/extending/adding/#arguments about the unused arguments
 */
class WPML_REST_Arguments_Sanitation {

	/**
	 * @param $value
	 * @param $request
	 * @param $key
	 *
	 * @return bool
	 */
	static function boolean( $value, $request, $key ) {
		/**
		 * `FILTER_VALIDATE_BOOLEAN` returns `NULL` if not valid, but in all other cases, it sanitizes the value
		 */
		return filter_var( $value, FILTER_VALIDATE_BOOLEAN );
	}

	/**
	 * @param $value
	 * @param $request
	 * @param $key
	 *
	 * @return bool
	 */
	static function integer( $value, $request, $key ) {
		return (int) self::float( $value, $request, $key );
	}

	/**
	 * @param $value
	 * @param $request
	 * @param $key
	 *
	 * @return bool
	 */
	static function float( $value, $request, $key ) {
		return (float) filter_var( $value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
	}

	/**
	 * @param $value
	 * @param $request
	 * @param $key
	 *
	 * @return bool
	 */
	static function string( $value, $request, $key ) {
		return filter_var( $value, FILTER_SANITIZE_STRING );
	}

	/**
	 * @param $value
	 * @param $request
	 * @param $key
	 *
	 * @return bool
	 */
	static function url( $value, $request, $key ) {
		return filter_var( $value, FILTER_SANITIZE_URL );
	}

	/**
	 * @param $value
	 * @param $request
	 * @param $key
	 *
	 * @return bool
	 */
	static function email( $value, $request, $key ) {
		return filter_var( $value, FILTER_SANITIZE_EMAIL );
	}
}