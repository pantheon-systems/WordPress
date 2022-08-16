<?php
/**
 * Plugin Name: Pantheon
 * Plugin URI: https://pantheon.io/
 * Description: Building on Pantheon's and WordPress's strengths, together.
 * Version: 0.3
 * Author: Pantheon
 * Author URI: https://pantheon.io/
 *
 * @package pantheon
 */

if ( isset( $_ENV['PANTHEON_ENVIRONMENT'] ) ) {

	require_once 'pantheon/pantheon-page-cache.php';
	if ( ! defined( 'DISABLE_PANTHEON_UPDATE_NOTICES' ) || ! DISABLE_PANTHEON_UPDATE_NOTICES ) {
		require_once 'pantheon/pantheon-updates.php';
	}
	if ( ! defined('RETURN_TO_PANTHEON_BUTTON') || RETURN_TO_PANTHEON_BUTTON ) {
		require_once 'pantheon/pantheon-login-form-mods.php';
	}
	if ( 'dev' === $_ENV['PANTHEON_ENVIRONMENT'] && function_exists( 'wp_is_writable' ) ) {
		require_once 'pantheon/pantheon-plugin-install-notice.php';
	}
    if ( defined( 'WP_CLI' ) && WP_CLI ) {
        require_once 'pantheon/cli.php';
    }
	if ( ! defined( 'FS_METHOD' ) ) {
		/**
		 * When this constant is not set, WordPress writes and then deletes a
		 * temporary file to determine if it has direct access to the filesystem,
		 * which we already know to be the case.  This multiplies filesystem
		 * operations and can degrade performance of the filesystem as a whole in
		 * the case of large sites that do a lot of filesystem operations.
		 * Setting this constant to 'direct' tells WordPress to assume it has
		 * direct access and skip creating the extra temporary file.
		 */
		define( 'FS_METHOD', 'direct' );
	}
} // Ensuring that this is on Pantheon.
