<?php
/**
 * Update Value Fix
 *
 * @package Pantheon\Compatibility\Fixes
 */

namespace Pantheon\Compatibility\Fixes;

/**
 * Update Value Fix
 */
class UpdateValueFix {
	/**
	 * @param string $option_name
	 * @param string $option_key
	 * @param mixed $option_value
	 *
	 * @return void
	 */
	public static function apply( $option_name, $option_key, $option_value ) {
		$options = json_decode( get_option( $option_name ) );
		$options->$option_key = $option_value;
		update_option( $option_name, json_encode( $options ) );
	}

	/**
	 * @param string $option_name
	 * @param string $option_key
	 *
	 * @return void
	 */
	public static function remove( $option_name, $option_key ) {
		$options = json_decode( get_option( $option_name ) );
		unset( $options->$option_key );
		update_option( $option_name, json_encode( $options ) );
	}
}
