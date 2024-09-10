<?php
/**
 * Compatibility fix for WooZone.
 *
 * @link https://docs.pantheon.io/plugins-known-issues#woozone
 * @package Pantheon\Compatibility
 */

namespace Pantheon\Compatibility;

use Pantheon\Compatibility\Fixes\DefineConstantFix;

/**
 * Class WooZone
 */
class WooZone extends Base {
	/**
	 * Run fix on each request.
	 *
	 * @var bool
	 */
	protected $run_fix_everytime = true;

	/**
	 * @return void
	 */
	public function apply_fix() {
		DefineConstantFix::apply( 'WP_MEMORY_LIMIT', '256M' );
	}

	/**
	 * @return void
	 */
	public function remove_fix() {}
}
