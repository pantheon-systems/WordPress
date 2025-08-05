<?php
/**
 * DefineConstantFix
 *
 * @package Pantheon\Compatibility\Fixes
 */

namespace Pantheon\Compatibility\Fixes;

/**
 * Class DefineConstantFix
 *
 * @package Pantheon\Compatibility\Fixes
 */
class DefineConstantFix {
	/**
	 * @param string $key
	 * @param mixed $value
	 *
	 * @return void
	 */
	public static function apply( $key, $value ) {
		if ( ! defined( $key ) ) {
			define( $key, $value );
		}
	}
}
