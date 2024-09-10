<?php
/**
 * Auth0 Fix
 *
 * @link https://docs.pantheon.io/plugins-known-issues#auth0
 * @package Pantheon\Compatibility\Fixes
 */

namespace Pantheon\Compatibility\Fixes;

use Auth0\SDK\Store\CookieStore;

use function class_exists;
use function function_exists;

/**
 * Auth0 Fix
 *
 * @package Pantheon\Compatibility\Fixes
 */
class Auth0Fix {
	/**
	 * Apply the fix
	 *
	 * @return void
	 */
	public static function apply() {
		add_action( 'plugins_loaded', [ __CLASS__, 'fix' ] );
	}

	/**
	 * @return void
	 */
	public static function remove() {}

	/**
	 * @return void
	 */
	public static function fix() {
		if ( ! function_exists( 'wpAuth0' ) || ! class_exists( 'Auth0\SDK\Store\CookieStore' ) ) {
			return;
		}

		$sdk = wpAuth0()->getSdk();
		$config = $sdk->configuration();
		$storage_id = 'STYXKEY_' . $config->getSessionStorageId();
		$transient_id = 'STYXKEY_' . $config->getTransientStorageId();
		$config->setSessionStorageId( $storage_id );
		$config->setTransientStorageId( $transient_id );
		$config->setSessionStorage( new CookieStore( $config, $config->getSessionStorageId() ) );
		$config->setTransientStorage( new CookieStore( $config, $config->getTransientStorageId() ) );
		$sdk->setConfiguration( $config );
	}
}
