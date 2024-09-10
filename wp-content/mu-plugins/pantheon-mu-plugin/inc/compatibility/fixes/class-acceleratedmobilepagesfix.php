<?php
/**
 * Accelerated Mobile Pages Fix
 *
 * @link https://docs.pantheon.io/plugins-known-issues#amp-for-wp--accelerated-mobile-pages
 * @package Pantheon\Compatibility\Fixes
 */

namespace Pantheon\Compatibility\Fixes;

use function defined;
use function extension_loaded;

/**
 * Accelerated Mobile Pages Fix
 *
 * @package Pantheon\Compatibility\Fixes
 */
class AcceleratedMobilePagesFix {
	/**
	 * @return void
	 */
	public static function apply() {
		SelfUpdatingThemesFix::apply();
		global $redux_builder_amp;
		// Force disabling AMP mobile redirection.
		$redux_builder_amp['amp-mobile-redirection'] = 0;

		if ( ! self::globals_exist() || ! self::is_mobile() ) {
			return;
		}
		http_response_code( 301 );
		$protocol = ( 0 === stripos( $_SERVER['SERVER_PROTOCOL'], 'https' ) ) ? 'https://' : 'http://';
		$redirect_header_value = $protocol . PANTHEON_HOSTNAME . $_SERVER['REQUEST_URI'] . '/amp';
		header( 'Location: ' . $redirect_header_value );

		// Name transaction "redirect" in New Relic for improved reporting.
		if ( extension_loaded( 'newrelic' ) ) {
			newrelic_name_transaction( 'redirect' );
		}
		exit();
	}

	/**
	 * Check if the required globals exist
	 *
	 * @return bool
	 */
	private static function globals_exist() {
		return isset( $_SERVER['REQUEST_URI'], $_SERVER['HTTP_USER_AGENT'], $_SERVER['SERVER_PROTOCOL'] ) && defined( 'PANTHEON_HOSTNAME' );
	}

	/**
	 * Check if the request is from a mobile device
	 *
	 * @see https://github.com/serbanghita/Mobile-Detect for a more robust solution
	 * @return bool
	 */
	private static function is_mobile() {
		if ( empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
			return false;
		}
		if ( str_contains( $_SERVER['HTTP_USER_AGENT'], 'Mobile' )
			|| str_contains( $_SERVER['HTTP_USER_AGENT'], 'Android' )
			|| str_contains( $_SERVER['HTTP_USER_AGENT'], 'Silk/' )
			|| str_contains( $_SERVER['HTTP_USER_AGENT'], 'Kindle' )
			|| str_contains( $_SERVER['HTTP_USER_AGENT'], 'BlackBerry' )
			|| str_contains( $_SERVER['HTTP_USER_AGENT'], 'Opera Mini' )
			|| str_contains( $_SERVER['HTTP_USER_AGENT'], 'Opera Mobi' ) ) {
			return true;
		}

		return false;
	}
}
