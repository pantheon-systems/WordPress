<?php
/**
 * AutoptimizeFix
 *
 * @link https://docs.pantheon.io/plugins-known-issues#autoptimize
 * @package Pantheon\Compatibility\Fixes
 */

namespace Pantheon\Compatibility\Fixes;

/**
 * AutoptimizeFix
 *
 * @package Pantheon\Compatibility\Fixes
 */
class AutoptimizeFix {
	/**
	 * Apply the fix
	 *
	 * @return void
	 */
	public static function apply() {
		DefineConstantFix::apply( 'AUTOPTIMIZE_CACHE_CHILD_DIR', '/uploads/autoptimize/' );
		// this is for setting the value of Enable 404 fallbacks to false.
		update_option( 'autoptimize_cache_fallback', '' );
	}

	/**
	 * @return void
	 */
	public static function remove() {
		delete_option( 'autoptimize_cache_fallback' );
	}
}
