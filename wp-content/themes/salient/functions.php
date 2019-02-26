<?php

// -----------------------------------------------------------------#
// Default theme constants
// -----------------------------------------------------------------#
define( 'NECTAR_THEME_DIRECTORY', get_template_directory() );
define( 'NECTAR_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/nectar/' );
define( 'NECTAR_THEME_NAME', 'salient' );


if ( ! function_exists( 'get_nectar_theme_version' ) ) {
	function nectar_get_theme_version() {
		return '10.0.1';
	}
}


// -----------------------------------------------------------------#
// Load text domain
// -----------------------------------------------------------------#
add_action( 'after_setup_theme', 'nectar_lang_setup' );

if ( ! function_exists( 'nectar_lang_setup' ) ) {
	function nectar_lang_setup() {

		load_theme_textdomain( NECTAR_THEME_NAME, get_template_directory() . '/lang' );

	}
}


// -----------------------------------------------------------------#
// Helper to grab Salient theme options
// -----------------------------------------------------------------#
function get_nectar_theme_options() {

	$legacy_options  = get_option( 'salient' );
	$current_options = get_option( 'salient_redux' );

	if ( ! empty( $current_options ) ) {
		return $current_options;
	} elseif ( ! empty( $legacy_options ) ) {
		return $legacy_options;
	} else {
		return $current_options;
	}
}

$nectar_options                    = get_nectar_theme_options();
$nectar_get_template_directory_uri = get_template_directory_uri();


// Default WP video size.
$content_width = 1080;


// -----------------------------------------------------------------#
// Register/Enqueue JS
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/enqueue-scripts.php';


// -----------------------------------------------------------------#
// Register/Enqueue CSS
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/enqueue-styles.php';


// -----------------------------------------------------------------#
// Dynamic Styles
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/dynamic-styles.php';


// Dynamic CSS to be loadded in head.
$nectar_external_dynamic = ( ! empty( $nectar_options['external-dynamic-css'] ) && $nectar_options['external-dynamic-css'] == 1 ) ? 'on' : 'off';
if ( $nectar_external_dynamic != 'on' ) {

	add_action( 'wp_head', 'nectar_colors_css_output' );
	add_action( 'wp_head', 'nectar_custom_css_output' );
	add_action( 'wp_head', 'nectar_fonts_output' );

}

// Dynamic CSS to be enqueued in a file.
else {
	add_action( 'wp_enqueue_scripts', 'nectar_enqueue_dynamic_css' );
}


// -----------------------------------------------------------------#
// Category Custom Meta
// -----------------------------------------------------------------#
require 'nectar/meta/category-meta.php';


// -----------------------------------------------------------------#
// Image sizes
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/media.php';


// -----------------------------------------------------------------#
// Navigation menu locations and custom fields
// -----------------------------------------------------------------#
require_once 'nectar/assets/functions/wp-menu-custom-items/menu-item-custom-fields.php';

require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/nav-menus.php';


// -----------------------------------------------------------------#
// TGM
// -----------------------------------------------------------------#
$nectar_disable_tgm = ( ! empty( $nectar_options['disable_tgm'] ) && $nectar_options['disable_tgm'] == '1' ) ? true : false;

if ( ! $nectar_disable_tgm ) {
	require_once 'nectar/tgm-plugin-activation/class-tgm-plugin-activation.php';
	require_once 'nectar/tgm-plugin-activation/required_plugins.php';
}


// -----------------------------------------------------------------#
// Nectar WPBakery Page Builder
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/wpbakery-init.php';


// -----------------------------------------------------------------#
// Theme Skin
// -----------------------------------------------------------------#
$nectar_theme_skin    = ( ! empty( $nectar_options['theme-skin'] ) ) ? $nectar_options['theme-skin'] : 'original';
$nectar_header_format = ( ! empty( $nectar_options['header_format'] ) ) ? $nectar_options['header_format'] : 'default';

if ( $nectar_header_format == 'centered-menu-bottom-bar' ) {
	$nectar_theme_skin = 'material';
}

add_filter( 'body_class', 'nectar_theme_skin_class' );

function nectar_theme_skin_class( $classes ) {
	global $nectar_theme_skin;
	$classes[] = $nectar_theme_skin;
	return $classes;
}


function nectar_theme_skin_css() {
	global $nectar_theme_skin;
	wp_enqueue_style( 'skin-' . $nectar_theme_skin );
}

add_action( 'wp_enqueue_scripts', 'nectar_theme_skin_css' );


// -----------------------------------------------------------------#
// Search
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/search.php';


// -----------------------------------------------------------------#
// General WP
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/wp-general.php';


// -----------------------------------------------------------------#
// Widget areas and custom widgets
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/widgets.php';


// -----------------------------------------------------------------#
// Header
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/header.php';


// -----------------------------------------------------------------#
// Blog
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/blog.php';


// -----------------------------------------------------------------#
// Portfolio
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/portfolio.php';


// -----------------------------------------------------------------#
// Page
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/page.php';


// -----------------------------------------------------------------#
// Options panel
// -----------------------------------------------------------------#
define( 'CNKT_INSTALLER_PATH', NECTAR_FRAMEWORK_DIRECTORY . 'redux-framework/extensions/wbc_importer/wbc_importer/connekt-plugin-installer/' );

$using_nectar_redux_framework = false;

if ( ! class_exists( 'ReduxFramework' ) && file_exists( dirname( __FILE__ ) . '/nectar/redux-framework/ReduxCore/framework.php' ) ) {
	require_once dirname( __FILE__ ) . '/nectar/redux-framework/ReduxCore/framework.php';
	$using_nectar_redux_framework = true;
}
if ( ! isset( $redux_demo ) && file_exists( dirname( __FILE__ ) . '/nectar/redux-framework/options-config.php' ) ) {
	require_once dirname( __FILE__ ) . '/nectar/redux-framework/options-config.php';
}


require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/redux-salient.php';



// -----------------------------------------------------------------#
// Nectar love
// -----------------------------------------------------------------#
require_once 'nectar/love/nectar-love.php';


// -----------------------------------------------------------------#
// Page meta
// -----------------------------------------------------------------#
require 'nectar/meta/page-meta.php';

$nectar_disable_home_slider   = ( ! empty( $nectar_options['disable_home_slider_pt'] ) && $nectar_options['disable_home_slider_pt'] == '1' ) ? true : false;
$nectar_disable_nectar_slider = ( ! empty( $nectar_options['disable_nectar_slider_pt'] ) && $nectar_options['disable_nectar_slider_pt'] == '1' ) ? true : false;


// -----------------------------------------------------------------#
// Home slider
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/home-slider.php';


if ( $nectar_disable_home_slider != true ) {
	include 'nectar/meta/home-slider-meta.php';
}


// -----------------------------------------------------------------#
// Nectar Slider
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/nectar-slider.php';


if ( $nectar_disable_nectar_slider != true ) {
	include 'nectar/meta/nectar-slider-meta.php';
}


// -----------------------------------------------------------------#
// WPML
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/wpml.php';

// -----------------------------------------------------------------#
// Gutenberg
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/gutenberg.php';


// -----------------------------------------------------------------#
// Shortcodes
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/shortcodes.php';


// -----------------------------------------------------------------#
// Portfolio Meta
// -----------------------------------------------------------------#
require 'nectar/meta/portfolio-meta.php';


// -----------------------------------------------------------------#
// Post meta
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/admin-enqueue.php';


// Post meta core functions.
require 'nectar/meta/meta-config.php';
require 'nectar/meta/post-meta.php';


// -----------------------------------------------------------------#
// Pagination
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/pagination.php';


// -----------------------------------------------------------------#
// Page header
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/page-header.php';


// -----------------------------------------------------------------#
// Woocommerce
// -----------------------------------------------------------------#
global $woocommerce;

// admin notice for left over uneeded template files.
if ( $woocommerce && is_admin() && file_exists( dirname( __FILE__ ) . '/woocommerce/cart/cart.php' ) ) {
	include 'nectar/woo/admin-notices.php';
}

// load product quickview.
$nectar_quick_view_in_use = 'false';
if ( $woocommerce ) {
	$nectar_quick_view = ( ! empty( $nectar_options['product_quick_view'] ) && $nectar_options['product_quick_view'] == '1' ) ? true : false;
	if ( $nectar_quick_view ) {
		$nectar_quick_view_in_use = 'true';
		require_once 'nectar/woo/quick-view.php';
	}
}

require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/woocommerce.php';


// -----------------------------------------------------------------#
// Open Graph
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/open-graph.php';

