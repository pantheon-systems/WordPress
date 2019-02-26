<?php
/**
 * Enqueue styles
 *
 * @package Salient WordPress Theme
 * @subpackage helpers
 * @version 9.0.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


function nectar_main_styles() {

		 global $nectar_get_template_directory_uri;
		 
		 $nectar_using_VC_front_end_editor = (isset($_GET['vc_editable'])) ? sanitize_text_field($_GET['vc_editable']) : '';
		 $nectar_using_VC_front_end_editor = ($nectar_using_VC_front_end_editor == 'true') ? true : false;
		
		 $nectar_theme_version = nectar_get_theme_version();

		 // Register
		 wp_register_style( 'rgs', $nectar_get_template_directory_uri . '/css/rgs.css', '', $nectar_theme_version );
		 wp_register_style( 'orbit', $nectar_get_template_directory_uri . '/css/orbit.css' );
		 wp_register_style( 'twentytwenty', $nectar_get_template_directory_uri . '/css/twentytwenty.css' );
		 wp_register_style( 'woocommerce', $nectar_get_template_directory_uri . '/css/woocommerce.css', '', $nectar_theme_version );
		 wp_register_style( 'font-awesome', $nectar_get_template_directory_uri . '/css/font-awesome.min.css', '', '4.6.4' );
		 wp_register_style( 'iconsmind', $nectar_get_template_directory_uri . '/css/iconsmind.css', '', '7.6' );
		 wp_register_style( 'linea', $nectar_get_template_directory_uri . '/css/fonts/svg/font/arrows_styles.css' );
		 wp_register_style( 'fullpage', $nectar_get_template_directory_uri . '/css/fullpage.css', '', $nectar_theme_version );
		 wp_register_style( 'nectarslider', $nectar_get_template_directory_uri . '/css/nectar-slider.css', '', $nectar_theme_version );
		 wp_register_style( 'main-styles', get_stylesheet_directory_uri() . '/style.css', '', $nectar_theme_version );
		 wp_register_style( 'nectar-portfolio', $nectar_get_template_directory_uri . '/css/portfolio.css', '', $nectar_theme_version );
		 wp_register_style( 'magnific', $nectar_get_template_directory_uri . '/css/magnific.css', '', '8.6.0' );
		 wp_register_style( 'fancyBox', $nectar_get_template_directory_uri . '/css/jquery.fancybox.css', '', '9.0' );
		 wp_register_style( 'responsive', $nectar_get_template_directory_uri . '/css/responsive.css', '', $nectar_theme_version );
		 wp_register_style( 'select2', $nectar_get_template_directory_uri . '/css/select2.css', '', '6.2' );
		 wp_register_style( 'non-responsive', $nectar_get_template_directory_uri . '/css/non-responsive.css' );
		 wp_register_style( 'skin-original', $nectar_get_template_directory_uri . '/css/skin-original.css', '', $nectar_theme_version );
		 wp_register_style( 'skin-ascend', $nectar_get_template_directory_uri . '/css/ascend.css', '', $nectar_theme_version );
		 wp_register_style( 'skin-material', $nectar_get_template_directory_uri . '/css/skin-material.css', '', $nectar_theme_version );
		 wp_register_style( 'box-roll', $nectar_get_template_directory_uri . '/css/box-roll.css' );
		 wp_register_style( 'leaflet', $nectar_get_template_directory_uri . '/css/leaflet.css', '1.3.1' );
		 wp_register_style( 'nectar-ie8', $nectar_get_template_directory_uri . '/css/ie8.css' );

		 global $nectar_options;

		 $lightbox_script = ( ! empty( $nectar_options['lightbox_script'] ) ) ? $nectar_options['lightbox_script'] : 'magnific';
	if ( $lightbox_script == 'pretty_photo' ) {
		$lightbox_script = 'magnific'; }

		 // Enqueue
		 wp_enqueue_style( 'rgs' );
		 wp_enqueue_style( 'font-awesome' );
		 wp_enqueue_style( 'main-styles' );

	if ( $lightbox_script == 'magnific' ) {
		wp_enqueue_style( 'magnific' );
	} elseif ( $lightbox_script == 'fancybox' ) {
		wp_enqueue_style( 'fancyBox' );
	}
		 wp_enqueue_style( 'nectar-ie8' );

		 // responsive
	if ( ! empty( $nectar_options['responsive'] ) && $nectar_options['responsive'] == 1 ) {
		wp_enqueue_style( 'responsive' );
	} else {
		wp_enqueue_style( 'non-responsive' );

		add_filter( 'body_class', 'salient_non_responsive' );
		function salient_non_responsive( $classes ) {
				// add 'class-name' to the $classes array
				$classes[] = 'salient_non_responsive';
				// return the $classes array
				return $classes;
		}
	}

		 // Default fonts with extended chars
		 global $nectar_options;
	if ( ! empty( $nectar_options['extended-theme-font'] ) && $nectar_options['extended-theme-font'] != '0' ) {
		wp_enqueue_style( 'options_typography_OpenSans_ext', 'https://fonts.googleapis.com/css?family=Open+Sans%3A300%2C400%2C600%2C700&subset=latin%2Clatin-ext', false, null, 'all' );

	}

		 // IE
		 global $wp_styles;
		 $wp_styles->add_data( 'nectar-ie8', 'conditional', 'lt IE 9' );

		// ajaxify needed
		$transition_method = ( ! empty( $nectar_options['transition-method'] ) ) ? $nectar_options['transition-method'] : 'ajax';
	if ( ! empty( $nectar_options['ajax-page-loading'] ) && $nectar_options['ajax-page-loading'] == '1' && $transition_method == 'ajax' || $nectar_using_VC_front_end_editor ) {
		wp_enqueue_style( 'wp-mediaelement' );
		wp_enqueue_style( 'fullpage' );
		wp_enqueue_style( 'nectarslider' );
		wp_enqueue_style( 'nectar-portfolio' );
		wp_enqueue_style( 'twentytwenty' ); 
		wp_enqueue_style( 'iconsmind' ); 
		wp_enqueue_style( 'linea' ); 
		wp_enqueue_style('leaflet'); 
	}

}

add_action( 'wp_enqueue_scripts', 'nectar_main_styles' );



function nectar_page_sepcific_styles() {
	global $post;
	global $nectar_options;

	if ( ! is_object( $post ) ) {
		$post = (object) array(
			'post_content' => ' ',
			'ID'           => ' ',
		);
	}
	$portfolio_extra_content = get_post_meta( $post->ID, '_nectar_portfolio_extra_content', true );
	$post_content            = $post->post_content;

	// home
	if ( is_page_template( 'template-home-1.php' ) || is_page_template( 'template-home-2.php' ) || is_page_template( 'template-home-3.php' ) || is_page_template( 'template-home-4.php' ) ) {
		wp_enqueue_style( 'orbit' );
	}

	// full page
	$page_full_screen_rows = ( isset( $post->ID ) ) ? get_post_meta( $post->ID, '_nectar_full_screen_rows', true ) : '';
	if ( $page_full_screen_rows == 'on' ) {
		wp_enqueue_style( 'fullpage' );
	}

	// nectar slider
	if ( stripos( $post_content, '[nectar_slider' ) !== false || stripos( $portfolio_extra_content, '[nectar_slider' ) !== false
	|| stripos( $post_content, 'type="nectarslider_style"' ) !== false || stripos( $portfolio_extra_content, 'type="nectarslider_style"' ) !== false ) {

		wp_enqueue_style( 'nectarslider' );
	}

	// portfolio
	if ( stripos( $post_content, 'nectar_portfolio' ) !== false || stripos( $portfolio_extra_content, 'nectar_portfolio' ) !== false ||
	   stripos( $post_content, 'recent_projects' ) !== false || stripos( $portfolio_extra_content, 'recent_projects' ) !== false ||
	   stripos( $post_content, 'type="image_grid"' ) !== false || stripos( $portfolio_extra_content, 'type="image_grid"' ) !== false ||
	   stripos( $post_content, "type='image_grid'" ) !== false || stripos( $portfolio_extra_content, "type='image_grid'" ) !== false ||
	   is_page_template( 'template-portfolio.php' ) || is_post_type_archive( 'portfolio' ) || is_singular( 'portfolio' ) || is_tax( 'project-attributes' ) || is_tax( 'project-type' ) ) {
			 wp_enqueue_style( 'nectar-portfolio' );
	}

	// blog std style containing image gallery grid - non archive
	if ( stripos( $post->post_content, '[nectar_blog' ) !== false && stripos( $post->post_content, 'layout="std-blog-' ) !== false && stripos( $post->post_content, 'blog_standard_style="classic' ) !== false ||
		 stripos( $post->post_content, '[nectar_blog' ) !== false && stripos( $post->post_content, 'layout="std-blog-' ) !== false && stripos( $post->post_content, 'blog_standard_style="minimal' ) !== false ) {
		wp_enqueue_style( 'nectar-portfolio' );
	}

	// blog std style containing image gallery grid - archive
	$posttype                     = get_post_type( $post );
	$nectar_on_blog_archive_check = ( is_archive() || is_author() || is_category() || is_home() || is_tag() ) && ( 'post' == $posttype && ! is_singular() );
	$nectar_blog_type             = ( ! empty( $nectar_options['blog_type'] ) ) ? $nectar_options['blog_type'] : 'masonry-blog-fullwidth';
	$nectar_blog_std_style        = ( ! empty( $nectar_options['blog_standard_type'] ) ) ? $nectar_options['blog_standard_type'] : 'featured_img_left';

	if ( $nectar_on_blog_archive_check ) {
		if ( $nectar_blog_type == 'std-blog-sidebar' || $nectar_blog_type == 'std-blog-fullwidth' ) {
			// std styles that could contain gallery sliders
			if ( $nectar_blog_std_style == 'classic' || $nectar_blog_std_style == 'minimal' ) {
				 wp_enqueue_style( 'nectar-portfolio' );
			}
		}
	}

	// WooCommerce
	if ( function_exists( 'is_woocommerce' ) ) {
		wp_enqueue_style( 'woocommerce' );
	}

	if ( strpos( $post_content, '.svg' ) !== false && strpos( $post_content, 'icon color="Extra-Color-Gradient-1"' ) !== false ||
	   strpos( $post_content, '.svg' ) !== false && strpos( $post_content, 'icon color="Extra-Color-Gradient-2"' ) !== false ||
	   strpos( $post_content, '.svg' ) !== false && strpos( $post_content, "icon color='Extra-Color-Gradient-1'" ) !== false ||
	   strpos( $post_content, '.svg' ) !== false && strpos( $post_content, "icon color='Extra-Color-Gradient-2'" ) !== false ||
	   strpos( $portfolio_extra_content, '.svg' ) !== false && strpos( $portfolio_extra_content, 'icon color="Extra-Color-Gradient-1"' ) !== false ||
	   strpos( $portfolio_extra_content, '.svg' ) !== false && strpos( $portfolio_extra_content, 'icon color="Extra-Color-Gradient-2"' ) !== false ||
	   strpos( $portfolio_extra_content, '.svg' ) !== false && strpos( $portfolio_extra_content, "icon color='Extra-Color-Gradient-1'" ) !== false ||
	   strpos( $portfolio_extra_content, '.svg' ) !== false && strpos( $portfolio_extra_content, "icon color='Extra-Color-Gradient-2'" ) !== false ) {
		wp_enqueue_style( 'linea' );
	}

	if ( strpos( $post_content, 'iconsmind-' ) !== false ||
	   strpos( $portfolio_extra_content, 'iconsmind-' ) !== false ) {
		wp_enqueue_style( 'iconsmind' );
	}

	$fancy_rcs = ( ! empty( $nectar_options['form-fancy-select'] ) ) ? $nectar_options['form-fancy-select'] : 'default';
	if ( $fancy_rcs == '1' ) {
		wp_enqueue_style( 'select2' );
	}

}

add_action( 'wp_enqueue_scripts', 'nectar_page_sepcific_styles' );

