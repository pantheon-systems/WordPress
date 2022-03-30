<?php
/**
 * Plugin Name: Pantheon
 * Plugin URI: https://pantheon.io/
 * Description: Building on Pantheon's and WordPress's strengths, together.
 * Version: 0.2
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
	if ( ! defined( 'FS_METHOD' ) ) {
		define( 'FS_METHOD', 'direct' ); // Resolves valhalla performance issue
	}
} // Ensuring that this is on Pantheon.
