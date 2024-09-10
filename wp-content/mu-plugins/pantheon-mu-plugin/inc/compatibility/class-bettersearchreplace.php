<?php
/**
 * Better Search Replace compatibility fix.
 *
 * @link https://docs.pantheon.io/plugins-known-issues#better-search-and-replace
 * @package Pantheon\Compatibility
 */

namespace Pantheon\Compatibility;

use Pantheon\Compatibility\Fixes\AddFilterFix;

/**
 * Better Search Replace compatibility fix.
 */
class BetterSearchReplace extends Base {
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
		AddFilterFix::apply( 'bsr_capability', function () {
			return 'manage_options';
		} );
	}

	/**
	 * @return void
	 */
	public function remove_fix() {
		AddFilterFix::remove( 'bsr_capability', function () {
			return 'manage_options';
		} );
	}
}
