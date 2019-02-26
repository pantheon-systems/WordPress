<?php

/**
 * @author OnTheGo Systems
 *
 * The following method can be used as REST arguments validation callback
 *
 * @see    http://v2.wp-api.org/extending/adding/#arguments about the unused arguments
 */
class WPML_REST_Arguments_Validation {

	/**
	 * @param $value
	 * @param $request
	 * @param $key
	 *
	 * @return bool
	 */
	static function boolean( $value, $request, $key ) {
		return null !== filter_var( $value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );
	}

	/**
	 * @param $value
	 * @param $request
	 * @param $key
	 *
	 * @return bool
	 */
	static function integer( $value, $request, $key ) {
		return false !== filter_var( $value, FILTER_VALIDATE_INT );
	}

	/**
	 * @param $value
	 * @param $request
	 * @param $key
	 *
	 * @return bool
	 */
	static function float( $value, $request, $key ) {
		return false !== filter_var( $value, FILTER_VALIDATE_FLOAT );
	}

	/**
	 * @param $value
	 * @param $request
	 * @param $key
	 *
	 * @return bool
	 */
	static function url( $value, $request, $key ) {
		return false !== filter_var( $value, FILTER_VALIDATE_URL );
	}

	/**
	 * @param $value
	 * @param $request
	 * @param $key
	 *
	 * @return bool
	 */
	static function email( $value, $request, $key ) {
		return false !== filter_var( $value, FILTER_VALIDATE_EMAIL );
	}
}