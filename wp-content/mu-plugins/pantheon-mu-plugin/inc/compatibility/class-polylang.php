<?php
/**
 * Compatibility fix for Polylang plugin.
 *
 * @link https://docs.pantheon.io/plugins-known-issues#polylang
 * @package Pantheon\Compatibility
 */

namespace Pantheon\Compatibility;

use Pantheon\Compatibility\Fixes\DefineConstantFix;

/**
 * Class Polylang
 */
class Polylang extends Base {
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
		DefineConstantFix::apply( 'PLL_CACHE_HOME_URL', false );
		DefineConstantFix::apply( 'PLL_COOKIE', false );
	}

	/**
	 * @return void
	 */
	public function remove_fix() {}
}
