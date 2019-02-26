<?php

/**
 * Wrapper class for basic PHP functions
 */
class WPML_PHP_Functions {

	/**
	 * Wrapper around PHP constant defined
	 *
	 * @param string $constant_name
	 *
	 * @return bool
	 */
	public function defined( $constant_name ) {
		return defined( $constant_name );
	}

	/**
	 * Wrapper around PHP constant lookup
	 *
	 * @param string $constant_name
	 *
	 * @return string|int
	 */
	public function constant( $constant_name ) {
		return $this->defined( $constant_name ) ? constant( $constant_name ) : null;
	}

	/**
	 * @param string $function_name The function name, as a string.
	 *
	 * @return bool true if <i>function_name</i> exists and is a function, false otherwise.
	 * This function will return false for constructs, such as <b>include_once</b> and <b>echo</b>.
	 * @return bool
	 */
	public function function_exists( $function_name ) {
		return function_exists( $function_name );
	}

	/**
	 * @param string $class_name The class name. The name is matched in a case-insensitive manner.
	 * @param bool   $autoload   [optional] Whether or not to call &link.autoload; by default.
	 *
	 * @return bool true if <i>class_name</i> is a defined class, false otherwise.
	 * @return bool
	 */
	public function class_exists( $class_name, $autoload = true ) {
		return class_exists( $class_name, $autoload );
	}

	/**
	 * @param string $name The extension name
	 *
	 * @return bool true if the extension identified by <i>name</i> is loaded, false otherwise.
	 */
	public function extension_loaded( $name ) {
		return extension_loaded( $name );
	}

	/**
	 * @param $string
	 *
	 * @return string
	 */
	public function mb_strtolower( $string ) {
		if ( function_exists( 'mb_strtolower' ) ) {
			return mb_strtolower( $string );
		}

		return strtolower( $string );
	}

	/**
	 * Wrapper for \phpversion()
	 *
	 * * @param string $extension (optional)
	 *
	 * @return string
	 */
	public function phpversion( $extension = null ) {
		if ( defined( 'PHP_VERSION' ) ) {
			return PHP_VERSION;
		} else {
			return phpversion( $extension );
		}
	}

	/**
	 * Compares two "PHP-standardized" version number strings
	 * @see \WPML_WP_API::version_compare
	 *
	 * @param string $version1
	 * @param string $version2
	 * @param null   $operator
	 *
	 * @return mixed
	 */
	public function version_compare( $version1, $version2, $operator = null ) {
		return version_compare( $version1, $version2, $operator );
	}

	/**
	 * @param array $array
	 * @param int   $sort_flags
	 *
	 * @return array
	 */
	public function array_unique( $array, $sort_flags = SORT_REGULAR ) {
		return wpml_array_unique( $array, $sort_flags );
	}

	/**
	 * @param string $message
	 * @param int    $message_type
	 * @param string $destination
	 * @param string $extra_headers
	 *
	 * @return bool
	 */
	public function error_log( $message, $message_type = null, $destination = null, $extra_headers = null ) {
		return error_log( $message, $message_type, $destination, $extra_headers );
	}

	public function exit_php() {
		exit();
	}
}
