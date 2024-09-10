<?php
/**
 * Compatibility class for Broken Link Checker plugin.
 *
 * @link https://docs.pantheon.io/plugins-known-issues#broken-link-checker
 * @package Pantheon\Compatibility
 */

namespace Pantheon\Compatibility;

use Pantheon\Compatibility\Fixes\UpdateValueFix;

/**
 * Class BrokenLinkChecker
 */
class BrokenLinkChecker extends Base {
	/**
	 * The default threshold value.
	 *
	 * @var int
	 */
	private $default_threshold_value = 72;

	/**
	 * @return void
	 */
	public function apply_fix() {
		UpdateValueFix::apply( 'wsblc_options', 'check_threshold', 72 );
	}

	/**
	 * @return void
	 */
	public function remove_fix() {
		UpdateValueFix::remove( 'wsblc_options', 'check_threshold' );
	}

	/**
	 * Check if the plugin is installed.
	 *
	 * @return bool
	 */
	private function check_threshold_has_default_value() {
		$options = json_decode( get_option( 'wsblc_options' ) ?: '{}' );
		// bail if the option is not set.
		if ( ! $options ) {
			return true;
		}

		// bail if the check_threshold is not set.
		if ( ! isset( $options->check_threshold ) ) {
			return true;
		}

		// bail if the check_threshold is not equal to the default value.
		if ( $this->default_threshold_value !== (int) $options->check_threshold ) {
			return false;
		}

		return true;
	}
}
