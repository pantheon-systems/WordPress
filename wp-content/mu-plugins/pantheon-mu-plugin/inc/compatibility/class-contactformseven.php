<?php
/**
 * Compatibility class for Contact Form 7 plugin.
 *
 * @link https://docs.pantheon.io/plugins-known-issues#contact-form-7
 * @package Pantheon\Compatibility
 */

namespace Pantheon\Compatibility;

use Pantheon\Compatibility\Fixes\DefineConstantFix;
use Pantheon\Compatibility\Fixes\SetServerPortFix;

/**
 * Class ContactFormSeven
 */
class ContactFormSeven extends Base {
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
		SetServerPortFix::apply();
		DefineConstantFix::apply( 'WPCF7_UPLOADS_TMP_DIR', ( WP_CONTENT_DIR . '/uploads/wpcf7_uploads' ) );
	}

	/**
	 * @return void
	 */
	public function remove_fix() {
		SetServerPortFix::remove();
	}
}
