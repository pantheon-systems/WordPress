<?php
/**
 * Slider Revolution Compatibility
 *
 * @link https://docs.pantheon.io/plugins-known-issues#slider-revolution
 * @package Pantheon\Compatibility
 */

namespace Pantheon\Compatibility;

use Pantheon\Compatibility\Fixes\SliderRevolutionFix;

/**
 * Slider Revolution Compatibility
 */
class SliderRevolution extends Base {
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
		SliderRevolutionFix::apply();
	}

	/**
	 * @return void
	 */
	public function remove_fix() {}
}
