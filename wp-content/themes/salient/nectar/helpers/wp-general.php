<?php
/**
 * General setup functions
 *
 * @package Salient WordPress Theme
 * @subpackage helpers
 * @version 9.0.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}




// -----------------------------------------------------------------#
// Add Theme Support
// -----------------------------------------------------------------#
function nectar_add_theme_support() {
	add_theme_support( 'post-formats', array( 'quote', 'video', 'audio', 'gallery', 'link' ) );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
}

add_action( 'after_setup_theme', 'nectar_add_theme_support' );




// -----------------------------------------------------------------#
// Site Title
// -----------------------------------------------------------------#
if ( ! function_exists( '_wp_render_title_tag' ) ) {
	function nectar_theme_slug_render_title() { ?>
			<title><?php wp_title( '|', true, 'right' ); ?></title> 
			<?php
	}
		add_action( 'wp_head', 'nectar_theme_slug_render_title' );
}



// -----------------------------------------------------------------#
// Nectar Hooks
// -----------------------------------------------------------------#
function nectar_hook_after_body_open() {
	do_action( 'nectar_hook_after_body_open' );
}

function nectar_hook_before_body_close() {
	do_action( 'nectar_hook_before_body_close' );
}

function nectar_hook_pull_right_menu_items() {
	do_action( 'nectar_hook_pull_right_menu_items' );
}

function nectar_hook_secondary_header_menu_items() {
	do_action( 'nectar_hook_secondary_header_menu_items' );
}

function nectar_hook_before_footer_widget_area() {
	do_action( 'nectar_hook_before_footer_widget_area' );
}

function nectar_hook_after_footer_widget_area() {
	do_action( 'nectar_hook_after_footer_widget_area' );
}

function nectar_hook_ocm_bottom_meta() {
	do_action( 'nectar_hook_ocm_bottom_meta' );
}




/**
 * Add iFrame to allowed wp_kses_post tags
 *
 * @param string $tags Allowed tags, attributes, and/or entities.
 * @param string $context Context to judge allowed tags by. Allowed values are 'post',
 *
 * @return mixed
 */
function nectar_custom_wpkses_post_tags( $tags, $context ) {
	if ( 'post' === $context ) {
		$tags['iframe'] = array(
			'src'             => true,
			'height'          => true,
			'width'           => true,
			'frameborder'     => true,
			'allowfullscreen' => true,
		);
	}
	return $tags;
}
add_filter( 'wp_kses_allowed_html', 'nectar_custom_wpkses_post_tags', 10, 2 );




// -----------------------------------------------------------------#
// Remove Lazy Load Helper
// -----------------------------------------------------------------#
if ( ! function_exists( 'nectar_remove_lazy_load_functionality' ) ) {
	function nectar_remove_lazy_load_functionality( $attr ) {
		$attr['class'] .= ' skip-lazy';
		return $attr;
	}
}



// -----------------------------------------------------------------#
// Check for HTTPS
// -----------------------------------------------------------------#
$nectar_is_ssl = is_ssl();

function nectar_ssl_check( $src ) {

	global $nectar_is_ssl;

	if ( strpos( $src, 'http://' ) !== false && $nectar_is_ssl == true ) {
		$converted_start = str_replace( 'http://', 'https://', $src );
		return $converted_start;
	} else {
		return $src;
	}
}





// -----------------------------------------------------------------#
// If Using Ajaxify
// -----------------------------------------------------------------#
function nectar_ajaxify_non_cached_scripts( $url ) {

	if ( false !== strpos( $url, 'vc_chart.js' ) ) {
		return "$url' class='always";
	}

	if ( false !== strpos( $url, 'ProgressCircle.js' ) ) {
		return "$url' class='always";
	}

	// not our file
	return $url;

}

global $nectar_options;
if ( ! empty( $nectar_options['ajax-page-loading'] ) && $nectar_options['ajax-page-loading'] == '1' ) {
	add_filter( 'clean_url', 'nectar_ajaxify_non_cached_scripts', 11, 1 );
}
