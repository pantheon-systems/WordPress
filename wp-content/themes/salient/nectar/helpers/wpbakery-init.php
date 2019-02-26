<?php
/**
 * Salient WPBakery page builder initialization
 *
 * @package Salient WordPress Theme
 * @subpackage helpers
 * @version 9.0.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


$nectar_using_VC_front_end_editor = (isset($_GET['vc_editable'])) ? sanitize_text_field($_GET['vc_editable']) : '';
$nectar_using_VC_front_end_editor = ($nectar_using_VC_front_end_editor == 'true') ? true : false;

// Add Nectar Functionality to WPBakery page builder
if ( class_exists( 'WPBakeryVisualComposerAbstract' ) && defined( 'SALIENT_VC_ACTIVE' ) ) {
	function add_nectar_to_vc() {

		if ( version_compare( WPB_VC_VERSION, '4.9', '>=' ) ) {
			require_once locate_template( '/nectar/nectar-vc-addons/nectar-addons.php' );
		} else {
			require_once locate_template( '/nectar/nectar-vc-addons/nectar-addons-no-lean.php' );
		}
	}

	add_action( 'init', 'add_nectar_to_vc', 5 );
	add_action( 'admin_enqueue_scripts', 'nectar_vc_styles' );
	if($nectar_using_VC_front_end_editor) {
		add_action( 'wp_enqueue_scripts', 'nectar_frontend_vc_styles' );
	}

	function nectar_vc_styles() {
		$nectar_theme_version = nectar_get_theme_version();
		global $nectar_get_template_directory_uri;
		wp_enqueue_style( 'nectar_vc', $nectar_get_template_directory_uri . '/nectar/nectar-vc-addons/nectar-addons.css', array(), $nectar_theme_version, 'all' );
	}
	
	function nectar_frontend_vc_styles() {
		$nectar_theme_version = nectar_get_theme_version();
		global $nectar_get_template_directory_uri;
		wp_enqueue_style( 'nectar_vc_frontend', $nectar_get_template_directory_uri . '/nectar/nectar-vc-addons/nectar-addons-frontend.css', array(), $nectar_theme_version, 'all' );
	}

	function nectar_vc_library_cat_list() {
		return array(
			esc_html__( 'All', 'salient' )            => 'all',
			esc_html__( 'About', 'salient' )          => 'about',
			esc_html__( 'Blog', 'salient' )           => 'blog',
			esc_html__( 'Call To Action', 'salient' ) => 'cta',
			esc_html__( 'Counters', 'salient' )       => 'counters',
			esc_html__( 'General', 'salient' )        => 'general',
			esc_html__( 'Icons', 'salient' )          => 'icons',
			esc_html__( 'Hero Section', 'salient' )   => 'hero_section',
			esc_html__( 'Map', 'salient' )            => 'map',
			esc_html__( 'Project', 'salient' )        => 'portfolio',
			esc_html__( 'Pricing', 'salient' )        => 'pricing',
			esc_html__( 'Services', 'salient' )       => 'services',
			esc_html__( 'Team', 'salient' )           => 'team',
			esc_html__( 'Testimonials', 'salient' )   => 'testimonials',
			esc_html__( 'Shop', 'salient' )           => 'shop',
		);
	}

	if ( ! function_exists( 'add_salient_studio_to_vc' ) ) {
		function add_salient_studio_to_vc() {
			if ( is_admin() ) {
				require_once locate_template( '/nectar/nectar-vc-addons/salient-studio-templates.php' );
			}
		}
	}

	add_salient_studio_to_vc();


} elseif ( class_exists( 'WPBakeryVisualComposerAbstract' ) ) {

	function nectar_font_awesome() {
		global $nectar_get_template_directory_uri;
		wp_enqueue_style( 'font-awesome', $nectar_get_template_directory_uri . '/css/font-awesome.min.css' );
	}

	if ( ! is_admin() ) {
		add_action( 'init', 'nectar_font_awesome', 99 );
	}
}
