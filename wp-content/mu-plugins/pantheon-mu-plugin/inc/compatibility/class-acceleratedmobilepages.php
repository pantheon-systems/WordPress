<?php
/**
 * Accelerated Mobile Pages compatibility fix.
 *
 * @link https://docs.pantheon.io/plugins-known-issues#amp-for-wp--accelerated-mobile-pages
 * @package Pantheon\Compatibility
 */

namespace Pantheon\Compatibility;

use Pantheon\Compatibility\Fixes\AcceleratedMobilePagesFix;

/**
 * Accelerated Mobile Pages compatibility fix.
 */
class AcceleratedMobilePages extends Base {
	/**
	 * Run fix on every request.
	 *
	 * @var bool
	 */
	protected $run_fix_everytime = true;

	/**
	 * @return void
	 */
	public function apply_fix() {
		AcceleratedMobilePagesFix::apply();
	}

	/**
	 * @return void
	 */
	public function remove_fix() {
		AcceleratedMobilePagesFix::remove();
	}
}
