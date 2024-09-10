<?php
/**
 * Compatibility class for WP Rocket plugin.
 *
 * @link https://docs.pantheon.io/plugins-known-issues#wp-rocket
 * @package Pantheon\Compatibility
 */

namespace Pantheon\Compatibility;

use Pantheon\Compatibility\Fixes\WPRocketFix;

/**
 * Class WPRocket
 *
 * @package Pantheon\Compatibility
 */
class WPRocket extends Base {
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
		WPRocketFix::apply();
	}

	/**
	 * @return void
	 */
	public function remove_fix() {
		WPRocketFix::remove();
	}
}
