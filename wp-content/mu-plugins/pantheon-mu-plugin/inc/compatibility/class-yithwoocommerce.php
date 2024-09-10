<?php
/**
 * YITH WooCommerce Compatibility
 *
 * @link https://docs.pantheon.io/plugins-known-issues#yith-woocommerce-extensions-with-mpdf-library
 * @package Pantheon\Compatibility
 */

namespace Pantheon\Compatibility;

use Pantheon\Compatibility\Fixes\YITHChangePdfLocationFix;

/**
 * Class YITHWoocommerce
 */
class YITHWoocommerce extends Base {
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
		YITHChangePdfLocationFix::apply();
	}

	/**
	 * @return void
	 */
	public function remove_fix() {
		YITHChangePdfLocationFix::remove();
	}
}
