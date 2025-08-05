<?php
/**
 * Class AddFilterFix
 *
 * @package Pantheon\Compatibility\Fixes
 */

namespace Pantheon\Compatibility\Fixes;

/**
 * Class AddFilterFix
 *
 * @package Pantheon\Compatibility\Fixes
 */
class AddFilterFix {
	/**
	 * @param string $filter_name
	 * @param callable $callback
	 *
	 * @return void
	 */
	public static function apply( $filter_name, callable $callback ) {
		add_filter( $filter_name, $callback );
	}

	/**
	 * @param string $filter_name
	 * @param callable $callback
	 *
	 * @return void
	 */
	public static function remove( $filter_name, callable $callback ) {
		remove_filter( $filter_name, $callback );
	}
}
