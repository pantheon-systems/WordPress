<?php
/**
 * WP-CLI commands for the Pantheon mu-plugin.
 *
 * @package pantheon
 */

namespace Pantheon\CLI;

use Pantheon_Cache;
use WP_CLI;

// Support the old pantheon-cache command but return a deprecation notice.
WP_CLI::add_command( 'pantheon-cache set-maintenance-mode', '\\Pantheon\\CLI\\__deprecated_maintenance_mode_output' );
WP_CLI::add_command( 'pantheon cache set-maintenance-mode', '\\Pantheon\\CLI\\set_maintenance_mode_command' );
WP_CLI::add_command( 'pantheon set-maintenance-mode', '\\Pantheon\\CLI\\set_maintenance_mode_command' );

/**
 * Sets maintenance mode status.
 *
 * Enable maintenance mode to work on your site while serving cached pages
 * to visitors and bots, or everyone except administators.
 *
 * ## DEPRECATION NOTICE
 *
 * This command is deprecated and will be removed in a future release.
 * Use `pantheon set-maintenance-mode` instead.
 *
 * ## USAGE
 *
 * wp pantheon-cache set-maintenance-mode <status> (deprecated) or
 * wp pantheon cache set-maintenance-mode <status>
 *
 * ## OPTIONS
 *
 * <status>
 * : Maintenance mode status.
 * ---
 * options:
 *   - disabled
 *   - anonymous
 *   - everyone
 * ---
 *
 * @subcommand set-maintenance-mode
 *
 * @deprecated 1.0.0
 */
function __deprecated_maintenance_mode_output( $args ) {
	$allowed_args = [ 'disabled', 'anonymous', 'everyone' ];
	$replacement_command = ( ! empty( $args && count( $args ) === 1 ) && in_array( $args[0], $allowed_args, true ) ) ? 'set-maintenance-mode ' . $args[0] : false;

	// translators: %s is the replacement command.
	WP_CLI::warning( WP_CLI::colorize( '%y' . sprintf( __( 'This command is deprecated and will be removed in a future release. Use `wp pantheon %s` instead.', 'pantheon-systems' ), $replacement_command ) . '%n' ) );
	WP_CLI::line( __( 'Run `wp pantheon set-maintenance-mode --help` for more information.', 'pantheon-systems' ) );

	// The command should fail before we get here, but in case it doesn't, display an error.
	if ( false === $replacement_command ) {
		WP_CLI::error( __( 'Invalid arguments. Run `wp pantheon set-maintenance-mode --help` for more infomation.', 'pantheon-systems' ) );
	}

	set_maintenance_mode_command( $args );
}

/**
 * Sets maintenance mode status.
 *
 * Enable maintenance mode to work on your site while serving cached pages
 * to visitors and bots, or everyone except administators.
 *
 * ## OPTIONS
 *
 * <status>
 * : Maintenance mode status.
 * ---
 * options:
 *   - disabled
 *   - anonymous
 *   - everyone
 * ---
 *
 * @subcommand set-maintenance-mode
 */
function set_maintenance_mode_command( $args ) {

	list( $status ) = $args;

	$out = Pantheon_Cache()->default_options;
	if ( ! empty( $status )
		&& in_array( $status, [ 'anonymous', 'everyone' ], true ) ) {
		$out['maintenance_mode'] = $status;
	} else {
		$out['maintenance_mode'] = 'disabled';
	}
	update_option( Pantheon_Cache::SLUG, $out );
	WP_CLI::success( sprintf( 'Maintenance mode set to: %s', $out['maintenance_mode'] ) );
}

/**
 * Force HTTPS scheme for home and siteurl options during ElasticPress CLI syncs.
 *
 * When WP-CLI runs via Terminus, $_SERVER['HTTP_HOST'] is not set, so
 * wp-config-pantheon.php skips defining WP_HOME/WP_SITEURL. WordPress
 * falls back to database values which may use http:// scheme. This causes
 * ElasticPress to index content with http:// URLs, leading to mixed content
 * and broken images on the HTTPS frontend.
 *
 * All Pantheon environments enforce HTTPS, so http:// is never correct.
 *
 * Use the 'pantheon_elasticpress_force_https_in_cli' filter to disable
 * this behavior:
 *
 *     add_filter( 'pantheon_elasticpress_force_https_in_cli', '__return_false' );
 *
 * @see https://getpantheon.atlassian.net/browse/SITE-5401
 */
WP_CLI::add_hook( 'before_invoke:elasticpress', function () {
	/**
	 * Filter whether to force HTTPS for home/siteurl during ElasticPress CLI commands.
	 *
	 * @param bool $force_https Whether to force HTTPS. Default true.
	 */
	if ( ! apply_filters( 'pantheon_elasticpress_force_https_in_cli', true ) ) {
		return;
	}

	if ( ! defined( 'WP_HOME' ) || strpos( WP_HOME, 'http://' ) === 0 ) {
		add_filter( 'option_home', '\\Pantheon\\CLI\\_pantheon_ep_force_https_url' );
	}
	if ( ! defined( 'WP_SITEURL' ) || strpos( WP_SITEURL, 'http://' ) === 0 ) {
		add_filter( 'option_siteurl', '\\Pantheon\\CLI\\_pantheon_ep_force_https_url' );
	}
} );

/**
 * Replace http:// with https:// in a URL string.
 *
 * @param string $url The option value.
 * @return string The URL with https:// scheme.
 */
function _pantheon_ep_force_https_url( $url ) {
	if ( is_string( $url ) && strpos( $url, 'http://' ) === 0 ) {
		return 'https://' . substr( $url, 7 );
	}
	return $url;
}
