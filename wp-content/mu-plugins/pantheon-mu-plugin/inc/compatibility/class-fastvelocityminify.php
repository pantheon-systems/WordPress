<?php
/**
 * Compatibility fix for Fast Velocity Minify plugin.
 *
 * @link https://docs.pantheon.io/plugins-known-issues#fast-velocity-minify
 * @package Pantheon\Compatibility
 */

namespace Pantheon\Compatibility;

use Pantheon\Compatibility\Fixes\DefineConstantFix;

/**
 * Class FastVelocityMinify
 */
class FastVelocityMinify extends Base {
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
		$home_url = defined( 'WP_SITEURL' ) ? WP_SITEURL : get_option( 'siteurl' );
		DefineConstantFix::apply( 'FVM_CACHE_DIR', '/code/wp-content/uploads' );
		DefineConstantFix::apply( 'FVM_CACHE_URL', sprintf( '%s/code/wp-content/uploads', $home_url ) );
	}

	/**
	 * @return void
	 */
	public function remove_fix() {}
}
