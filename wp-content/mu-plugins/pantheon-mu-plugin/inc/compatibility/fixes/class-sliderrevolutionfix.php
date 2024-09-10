<?php
/**
 * Slider Revolution Fix
 *
 * @link https://docs.pantheon.io/plugins-known-issues#slider-revolution
 * @package Pantheon
 */

namespace Pantheon\Compatibility\Fixes;

/**
 * Slider Revolution Fix
 */
class SliderRevolutionFix {
	/**
	 * @return void
	 */
	public static function apply() {
		$_SERVER['SERVER_NAME'] = PANTHEON_HOSTNAME;
	}
}
