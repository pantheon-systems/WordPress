<?php
/**
 * Set Server Port Fix
 *
 * This fix sets the server port based on the HTTP_USER_AGENT_HTTPS header.
 *
 * @package Pantheon\Compatibility\Fixes
 */

namespace Pantheon\Compatibility\Fixes;

use function defined;

/**
 * Class SetServerPortFix
 *
 * @package Pantheon\Compatibility\Fixes
 */
class SetServerPortFix {
	/**
	 * @return void
	 */
	public static function apply() {
		if ( ! defined( 'PANTHEON_HOSTNAME' ) ) {
			return;
		}

		$_SERVER['SERVER_NAME'] = PANTHEON_HOSTNAME;

		if ( isset( $_SERVER['HTTP_USER_AGENT_HTTPS'] ) && 'ON' === $_SERVER['HTTP_USER_AGENT_HTTPS'] ) {
			$_SERVER['SERVER_PORT'] = 443;
		} else {
			$_SERVER['SERVER_PORT'] = 80;
		}
	}

	/**
	 * @return void
	 */
	public static function remove() {
		if ( ! defined( 'PANTHEON_HOSTNAME' ) ) {
			return;
		}

		$_SERVER['SERVER_NAME'] = PANTHEON_HOSTNAME;
		$_SERVER['SERVER_PORT'] = 80;
	}
}
