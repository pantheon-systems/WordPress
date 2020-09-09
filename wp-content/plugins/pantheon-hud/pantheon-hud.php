<?php
/**
 * Plugin Name: Pantheon HUD
 * Version: 0.3.1
 * Description: A heads-up display into your Pantheon environment.
 * Author: Pantheon
 * Author URI: https://pantheon.io
 * Plugin URI: https://pantheon.io
 * Text Domain: pantheon-hud
 * Domain Path: /languages
 *
 * @package Pantheon HUD
 */

define( 'PANTHEON_HUD_ROOT_FILE', __FILE__ );

add_action(
	'init',
	function() {
		$view_pantheon_hud = apply_filters( 'pantheon_hud_current_user_can_view', current_user_can( 'manage_options' ) );
		if ( $view_pantheon_hud ) {
			Pantheon\HUD\Toolbar::get_instance();
		}
	}
);

spl_autoload_register(
	function( $class ) {
		$class = ltrim( $class, '\\' );
		if ( 0 !== stripos( $class, 'Pantheon\HUD\\' ) ) {
			return;
		}

		$parts = explode( '\\', $class );
		array_shift( $parts ); // Don't need "Pantheon".
		array_shift( $parts ); // Don't need "HUD".
		$last    = array_pop( $parts ); // File should be 'class-[...].php'.
		$last    = 'class-' . $last . '.php';
		$parts[] = $last;
		$file    = dirname( __FILE__ ) . '/inc/' . str_replace( '_', '-', strtolower( implode( '/', $parts ) ) );
		if ( file_exists( $file ) ) {
			require $file;
		}
	}
);
