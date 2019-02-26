<?php
/**
 * Enqueue scripts
 *
 * @package Salient WordPress Theme
 * @subpackage helpers
 * @version 9.0.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


function nectar_register_js() {

	global $nectar_options;
	global $post;
	global $nectar_get_template_directory_uri;
	
	$nectar_using_VC_front_end_editor = (isset($_GET['vc_editable'])) ? sanitize_text_field($_GET['vc_editable']) : '';
	$nectar_using_VC_front_end_editor = ($nectar_using_VC_front_end_editor == 'true') ? true : false;

	$nectar_theme_version = nectar_get_theme_version();

	if ( ! is_admin() ) {

		// Register
		wp_register_script( 'nectar_priority', $nectar_get_template_directory_uri . '/js/priority.js', 'jquery', $nectar_theme_version, true );
		wp_register_script( 'modernizer', $nectar_get_template_directory_uri . '/js/modernizr.js', 'jquery', '2.6.2', true );
		wp_register_script( 'imagesLoaded', $nectar_get_template_directory_uri . '/js/imagesLoaded.min.js', 'jquery', '4.1.4', true );
		wp_register_script( 'respond', $nectar_get_template_directory_uri . '/js/respond.js', 'jquery', '1.1', true );
		wp_register_script( 'superfish', $nectar_get_template_directory_uri . '/js/superfish.js', 'jquery', '1.4.8', true );
		wp_register_script( 'respond', $nectar_get_template_directory_uri . '/js/respond.js', 'jquery', '1.1', true );
		wp_register_script( 'touchswipe', $nectar_get_template_directory_uri . '/js/touchswipe.min.js', 'jquery', '1.0', true );
		wp_register_script( 'flexslider', $nectar_get_template_directory_uri . '/js/flexslider.min.js', array( 'jquery', 'touchswipe' ), '2.1', true );
		wp_register_script( 'orbit', $nectar_get_template_directory_uri . '/js/orbit.js', 'jquery', '1.4', true );
		wp_register_script( 'flickity', $nectar_get_template_directory_uri . '/js/flickity.min.js', 'jquery', '2.1.2', true );
		wp_register_script( 'nicescroll', $nectar_get_template_directory_uri . '/js/nicescroll.js', 'jquery', '3.5.4', true );
		wp_register_script( 'magnific', $nectar_get_template_directory_uri . '/js/magnific.js', 'jquery', '7.0.1', true );
		wp_register_script( 'fancyBox', $nectar_get_template_directory_uri . '/js/jquery.fancybox.min.js', 'jquery', '7.0.1', true );
		wp_register_script( 'nectar_parallax', $nectar_get_template_directory_uri . '/js/parallax.js', 'jquery', '1.0', true );
		wp_register_script( 'isotope', $nectar_get_template_directory_uri . '/js/isotope.min.js', 'jquery', '7.6', true );
		wp_register_script( 'select2', $nectar_get_template_directory_uri . '/js/select2.min.js', 'jquery', '3.5.2', true );
		wp_register_script( 'nectarSlider', $nectar_get_template_directory_uri . '/js/nectar-slider.js', 'jquery', $nectar_theme_version, true );
		wp_register_script( 'nectar_single_product', $nectar_get_template_directory_uri . '/js/nectar-single-product.js', 'jquery', $nectar_theme_version, true );
		wp_register_script( 'fullPage', $nectar_get_template_directory_uri . '/js/jquery.fullPage.min.js', 'jquery', $nectar_theme_version, true );
		wp_register_script( 'vivus', $nectar_get_template_directory_uri . '/js/vivus.min.js', 'jquery', '6.0.1', true );
		wp_register_script( 'nectarParticles', $nectar_get_template_directory_uri . '/js/nectar-particles.js', 'jquery', $nectar_theme_version, true );
		wp_register_script( 'ajaxify', $nectar_get_template_directory_uri . '/js/ajaxify.js', 'jquery', $nectar_theme_version, true );
		wp_register_script( 'caroufredsel', $nectar_get_template_directory_uri . '/js/caroufredsel.min.js', array( 'jquery', 'touchswipe' ), '7.0.1', true );
		wp_register_script( 'owl_carousel', $nectar_get_template_directory_uri . '/js/owl.carousel.min.js', 'jquery', '2.3.4', true );
		wp_register_script( 'leaflet', $nectar_get_template_directory_uri . '/js/leaflet.js', 'jquery', '1.3.1', true );
		wp_register_script( 'nectar_leaflet_map', $nectar_get_template_directory_uri . '/js/nectar-leaflet-map.js', 'jquery', $nectar_theme_version, true );
		wp_register_script( 'twentytwenty', $nectar_get_template_directory_uri . '/js/jquery.twentytwenty.js', 'jquery', '1.0', true );
		wp_register_script( 'infinite_scroll', $nectar_get_template_directory_uri . '/js/infinitescroll.js', array( 'jquery' ), '1.1', true );
		wp_register_script( 'stickykit', $nectar_get_template_directory_uri . '/js/stickkit.js', 'jquery', '1.0', true );
		wp_register_script( 'pixi', $nectar_get_template_directory_uri . '/js/pixi.min.js', 'jquery', '4.5.1', true );

		if ( floatval( get_bloginfo( 'version' ) ) < '3.6' ) {
			wp_register_script( 'jplayer', $nectar_get_template_directory_uri . '/js/jplayer.min.js', 'jquery', '2.1', true );
		}
		wp_register_script( 'nectarFrontend', $nectar_get_template_directory_uri . '/js/init.js', array( 'jquery', 'superfish' ), $nectar_theme_version, true );

		// Dequeue
		$lightbox_script = ( ! empty( $nectar_options['lightbox_script'] ) ) ? $nectar_options['lightbox_script'] : 'magnific';
		if ( $lightbox_script == 'pretty_photo' ) {
			$lightbox_script = 'magnific'; }

		// Enqueue
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'nectar_priority' );
		wp_enqueue_script( 'modernizer' );
		wp_enqueue_script( 'imagesLoaded' );

		// only load for IE8
		if ( preg_match( '/(?i)msie [2-8]/', $_SERVER['HTTP_USER_AGENT'] ) ) {
			wp_enqueue_script( 'respond' );
		}

		$portfolio_extra_content = ( isset( $post->ID ) ) ? get_post_meta( $post->ID, '_nectar_portfolio_extra_content', true ) : '';
		$post_content            = ( isset( $post->post_content ) ) ? $post->post_content : '';

		if ( ! empty( $nectar_options['portfolio_sidebar_follow'] ) && $nectar_options['portfolio_sidebar_follow'] == '1' && is_singular( 'portfolio' ) ) {
			wp_enqueue_script( 'stickykit' ); }

		if ( $lightbox_script == 'magnific' ) {
			wp_enqueue_script( 'magnific' );
		} elseif ( $lightbox_script == 'fancybox' ) {
			wp_enqueue_script( 'fancyBox' );
		}

		if ( stripos( $post_content, 'nectar_portfolio' ) !== false || stripos( $portfolio_extra_content, 'nectar_portfolio' ) !== false ||
		   stripos( $post_content, 'vc_gallery type="image_grid"' ) !== false || stripos( $portfolio_extra_content, 'vc_gallery type="image_grid"' ) !== false ||
		   stripos( $post_content, "vc_gallery type='image_grid'" ) !== false || stripos( $portfolio_extra_content, "vc_gallery type='image_grid'" ) !== false ||
		   stripos( $post_content, 'type="image_grid"' ) !== false || stripos( $portfolio_extra_content, 'type="image_grid"' ) !== false ||
		   stripos( $post_content, "type='image_grid'" ) !== false || stripos( $portfolio_extra_content, "type='image_grid'" ) !== false ||
		   is_page_template( 'template-portfolio.php' ) || is_search() ) {

			 wp_enqueue_script( 'isotope' );
		}

		$page_full_screen_rows = ( isset( $post->ID ) ) ? get_post_meta( $post->ID, '_nectar_full_screen_rows', true ) : '';
		if ( $page_full_screen_rows == 'on' ) {
			wp_enqueue_script( 'fullPage' );
		}

		if ( stripos( $post_content, '[recent_projects' ) !== false || stripos( $portfolio_extra_content, '[recent_projects' ) !== false
		|| stripos( $post_content, '[carousel' ) !== false || stripos( $portfolio_extra_content, '[carousel' ) !== false
		|| stripos( $post_content, 'carousel="true"' ) !== false || stripos( $portfolio_extra_content, 'carousel="true"' ) !== false
		|| stripos( $post_content, 'carousel="1"' ) !== false || stripos( $portfolio_extra_content, 'carousel="1"' ) !== false
		|| is_page_template( 'template-home-1.php' ) ) {
			wp_enqueue_script( 'caroufredsel' );
		}

		if ( stripos( $post_content, 'script="owl_carousel"' ) !== false || stripos( $portfolio_extra_content, 'script="owl_carousel"' ) !== false ) {
			wp_enqueue_script( 'owl_carousel' );
		}

		$nectar_theme_skin = ( ! empty( $nectar_options['theme-skin'] ) ) ? $nectar_options['theme-skin'] : 'original';
		$header_format     = ( ! empty( $nectar_options['header_format'] ) ) ? $nectar_options['header_format'] : 'default';
		if ( $header_format == 'centered-menu-bottom-bar' ) {
			$nectar_theme_skin = 'material'; }
		
		if ( stripos( $post_content, 'bg_image_animation="displace-filter' ) !== false || stripos( $portfolio_extra_content, 'bg_image_animation="displace-filter' ) !== false ) {
			wp_enqueue_script( 'pixi' );
		}
		
		wp_enqueue_script( 'nectarFrontend' );

		$bg_type = ( isset( $post->ID ) ) ? get_post_meta( $post->ID, '_nectar_slider_bg_type', true ) : '';

		$transition_method = ( ! empty( $nectar_options['transition-method'] ) ) ? $nectar_options['transition-method'] : 'ajax';
		if ( ! empty( $nectar_options['ajax-page-loading'] ) && $nectar_options['ajax-page-loading'] == '1' && $transition_method == 'ajax' ) {
			wp_enqueue_script( 'nectarSlider' );
			wp_enqueue_script( 'fullPage' );
			wp_enqueue_script( 'pixi' );
			wp_enqueue_script( 'ajaxify' );
		}
		
		if($nectar_using_VC_front_end_editor) {
			//scripts that are possibly called through elements
			wp_enqueue_script('nectarSlider');
			wp_enqueue_script('isotope');
			wp_enqueue_script('caroufredsel');	
			wp_enqueue_script('vivus'); 
			wp_enqueue_script('touchswipe');
			wp_enqueue_script('flickity');	
			wp_enqueue_script('flexslider');
			wp_enqueue_script('stickykit');	
			wp_enqueue_script('vivus'); 
			wp_enqueue_script('twentytwenty');
			wp_enqueue_script('owl_carousel');
			wp_enqueue_script('leaflet');
	    wp_enqueue_script('nectar_leaflet_map'); 
		}
		
		
	}
}

add_action( 'wp_enqueue_scripts', 'nectar_register_js' );



function nectar_page_specific_js() {

	global $post;
	global $nectar_options;
	global $nectar_get_template_directory_uri;

	if ( ! is_object( $post ) ) {
		$post = (object) array(
			'post_content' => ' ',
			'ID'           => ' ',
		);
	}
	$template_name = get_post_meta( $post->ID, '_wp_page_template', true );

	// home
	if ( is_page_template( 'template-home-1.php' ) || $template_name == 'salient/template-home-1.php' ||
		 is_page_template( 'template-home-2.php' ) || $template_name == 'salient/template-home-2.php' ||
		 is_page_template( 'template-home-3.php' ) || $template_name == 'salient/template-home-3.php' ||
		 is_page_template( 'template-home-4.php' ) || $template_name == 'salient/template-home-4.php' ) {
		wp_enqueue_script( 'orbit' );
		wp_enqueue_script( 'touchswipe' );
	}

	$portfolio_extra_content = get_post_meta( $post->ID, '_nectar_portfolio_extra_content', true );
	$post_content            = $post->post_content;
	$posttype                = get_post_type( $post );

	/*********for page builder elements*/

	// infinite scroll
	if ( stripos( $post->post_content, 'pagination_type="infinite_scroll"' ) !== false || stripos( $portfolio_extra_content, 'pagination_type="infinite_scroll"' ) !== false ) {
		wp_enqueue_script( 'infinite_scroll' );
	}

	// gallery slider scripts
	if ( stripos( $post->post_content, '[nectar_blog' ) !== false ||
	  stripos( $portfolio_extra_content, '[nectar_blog' ) !== false ) {
			wp_enqueue_script( 'flickity' );
			wp_enqueue_script( 'flexslider' );
	}

	// stickkit
	if ( stripos( $post->post_content, '[nectar_blog' ) !== false && stripos( $post->post_content, 'enable_ss="true"' ) !== false ||
	  stripos( $portfolio_extra_content, '[nectar_blog' ) !== false && stripos( $portfolio_extra_content, 'enable_ss="true"' ) !== false ) {
		wp_enqueue_script( 'stickykit' );
	}

	// isotope
	if ( stripos( $post->post_content, '[nectar_blog' ) !== false && stripos( $post->post_content, 'layout="masonry' ) !== false ||
		stripos( $post->post_content, '[nectar_blog' ) !== false && stripos( $post->post_content, 'layout="std-blog-' ) !== false && stripos( $post->post_content, 'blog_standard_style="classic' ) !== false ||
		stripos( $post->post_content, '[nectar_blog' ) !== false && stripos( $post->post_content, 'layout="std-blog-' ) !== false && stripos( $post->post_content, 'blog_standard_style="minimal' ) !== false ||
	  stripos( $portfolio_extra_content, '[nectar_blog' ) !== false && stripos( $portfolio_extra_content, 'layout="masonry' ) !== false ) {
		wp_enqueue_script( 'isotope' );
	}

	/*********for archive pages based on theme options*/
	$nectar_on_blog_archive_check      = ( is_archive() || is_author() || is_category() || is_home() || is_tag() ) && ( 'post' == $posttype && ! is_singular() );
	$nectar_on_portfolio_archive_check = ( is_archive() || is_category() || is_home() || is_tag() ) && ( 'portfolio' == $posttype && ! is_singular() );

	// infinite scroll
	if ( ( ! empty( $nectar_options['portfolio_pagination_type'] ) && $nectar_options['portfolio_pagination_type'] == 'infinite_scroll' ) && $nectar_on_portfolio_archive_check ||
			( ! empty( $nectar_options['portfolio_pagination_type'] ) && $nectar_options['portfolio_pagination_type'] == 'infinite_scroll' ) && is_page_template( 'template-portfolio.php' ) ||
			( ! empty( $nectar_options['blog_pagination_type'] ) && $nectar_options['blog_pagination_type'] == 'infinite_scroll' ) && $nectar_on_blog_archive_check ) {
			wp_enqueue_script( 'infinite_scroll' );

		if ( class_exists( 'WPBakeryVisualComposerAbstract' ) && defined( 'SALIENT_VC_ACTIVE' ) ) {
			wp_register_script( 'progressCircle', vc_asset_url( 'lib/bower/progress-circle/ProgressCircle.min.js' ) );
			wp_register_script( 'vc_pie', vc_asset_url( 'lib/vc_chart/jquery.vc_chart.min.js' ), array( 'jquery', 'progressCircle' ) );
		}
	}

	// sticky sidebar
	if ( ! empty( $nectar_options['blog_enable_ss'] ) && $nectar_options['blog_enable_ss'] == '1' && $nectar_on_blog_archive_check ) {
		wp_enqueue_script( 'stickykit' );
	}

	// isotope
	$nectar_blog_type          = ( ! empty( $nectar_options['blog_type'] ) ) ? $nectar_options['blog_type'] : 'masonry-blog-fullwidth';
	$nectar_blog_std_style     = ( ! empty( $nectar_options['blog_standard_type'] ) ) ? $nectar_options['blog_standard_type'] : 'featured_img_left';
	$nectar_blog_masonry_style = ( ! empty( $nectar_options['blog_masonry_type'] ) ) ? $nectar_options['blog_masonry_type'] : 'auto_meta_overlaid_spaced';

	if ( $nectar_blog_type != 'std-blog-sidebar' && $nectar_blog_type != 'std-blog-fullwidth' ) {
		if ( $nectar_blog_masonry_style != 'auto_meta_overlaid_spaced' && $nectar_on_blog_archive_check ) {
			wp_enqueue_script( 'isotope' );
		}
	}

	if ( $nectar_on_portfolio_archive_check ) {
		wp_enqueue_script( 'isotope' ); }

	// gallery slider scripts
	if ( $nectar_on_blog_archive_check ) {

		if ( $nectar_blog_type == 'std-blog-sidebar' || $nectar_blog_type == 'std-blog-fullwidth' ) {
			// std styles that could contain gallery sliders
			if ( $nectar_blog_std_style == 'classic' || $nectar_blog_std_style == 'minimal' ) {
				wp_enqueue_script( 'flickity' );
				wp_enqueue_script( 'flexslider' );
				wp_enqueue_script( 'isotope' );
			}
		} else {
			// masonry styles that could contain gallery sliders
			if ( $nectar_blog_masonry_style != 'auto_meta_overlaid_spaced' ) {
				wp_enqueue_script( 'flickity' );
				wp_enqueue_script( 'flexslider' );
			}
		}
	}

	// single post sticky sidebar
	$enable_ss = ( ! empty( $nectar_options['blog_enable_ss'] ) ) ? $nectar_options['blog_enable_ss'] : 'false';

	if ( ( $enable_ss == '1' && is_single() && $posttype == 'post' ) ||
	  stripos( $post->post_content, '[vc_widget_sidebar' ) !== false || stripos( $portfolio_extra_content, '[vc_widget_sidebar' ) !== false ) {
		  wp_enqueue_script( 'stickykit' );
	}

	// nectarSlider
	if ( stripos( $post_content, '[nectar_slider' ) !== false || stripos( $portfolio_extra_content, '[nectar_slider' ) !== false
	|| stripos( $post_content, 'type="nectarslider_style"' ) !== false || stripos( $portfolio_extra_content, 'type="nectarslider_style"' ) !== false ) {

		wp_enqueue_script( 'nectarSlider' );
	}

	// touch swipe
	$box_roll = get_post_meta( $post->ID, '_nectar_header_box_roll', true );
	wp_enqueue_script( 'touchswipe' );

	// flickity
	if ( stripos( $post_content, '[vc_gallery type="flickity"' ) !== false || stripos( $portfolio_extra_content, '[vc_gallery type="flickity"' ) !== false
	|| stripos( $post_content, 'style="multiple_visible"' ) !== false || stripos( $portfolio_extra_content, 'style="multiple_visible"' ) !== false
	|| stripos( $post_content, 'style="slider_multiple_visible"' ) !== false || stripos( $portfolio_extra_content, 'style="slider_multiple_visible"' ) !== false
	|| stripos( $post_content, 'script="flickity"' ) !== false || stripos( $portfolio_extra_content, 'script="flickity"' ) !== false
	|| stripos( $post_content, 'style="multiple_visible_minimal"' ) !== false || stripos( $portfolio_extra_content, 'style="multiple_visible_minimal"' ) !== false
	|| stripos( $post_content, 'style="slider"' ) !== false || stripos( $portfolio_extra_content, 'style="slider"' ) !== false ) {

		wp_enqueue_script( 'flickity' );
	}

	// fancy select
	$fancy_rcs = ( ! empty( $nectar_options['form-fancy-select'] ) ) ? $nectar_options['form-fancy-select'] : 'default';
	if ( $fancy_rcs == '1' ) {
		wp_enqueue_script( 'select2' );
	}

	// svg icon animation
	if ( strpos( $post_content, '.svg' ) !== false || strpos( $portfolio_extra_content, '.svg' ) !== false ) {
		wp_enqueue_script( 'vivus' );
	}

	// comments
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

}

add_action( 'wp_enqueue_scripts', 'nectar_page_specific_js' );
