<?php
/**
 * Auth0 Compatibility
 *
 * @link https://docs.pantheon.io/plugins-known-issues#auth0
 * @package Pantheon\Compatibility
 */

namespace Pantheon\Compatibility;

use Pantheon\Compatibility\Fixes\Auth0Fix;

/**
 * Auth0 Compatibility
 */
class Auth0 extends Base {
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
		Auth0Fix::apply();
	}

	/**
	 * @return void
	 */
	public function remove_fix() {
		Auth0Fix::remove();
	}
}
