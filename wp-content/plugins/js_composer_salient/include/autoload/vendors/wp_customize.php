<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * @since 4.4 vendors initialization moved to hooks in autoload/vendors.
 */
// Remove scripts from the WPBakery Page Builder while in the Customizer = Temp Fix
// Actually we need to check if this is really needed in 4.4 uncomment if you have customizer issues
// But this actually will break any VC js in Customizer preview.
// removed by fixing vcTabsBevahiour in js_composer_front.js
/*
if ( ! function_exists( 'vc_wpex_remove_vc_scripts' ) ) {
	function vc_wpex_remove_vc_scripts() {
		if ( is_customize_preview() ) {
			wp_deregister_script( 'wpb_composer_front_js' );
			wp_dequeue_script( 'wpb_composer_front_js' );
		}
	}
}*/
//add_action( 'wp_enqueue_scripts', 'vc_wpex_remove_vc_scripts' );
