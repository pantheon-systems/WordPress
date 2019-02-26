<?php
/**
 * The template for displaying pages.
 *
 * @package Salient WordPress Theme
 * @version 9.0.2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
nectar_page_header( $post->ID );

$nectar_fp_options = nectar_get_full_page_options();

?>

<div class="container-wrap">
	<div class="<?php if ( $nectar_fp_options['page_full_screen_rows'] != 'on' ) { echo 'container';} ?> main-content">
		<div class="row">
			
			<?php

			// Yoast breadcrumbs.
			if ( function_exists( 'yoast_breadcrumb' ) && ! is_home() && ! is_front_page() ) {
				yoast_breadcrumb( '<p id="breadcrumbs">', '</p>' ); }

			 // Buddypress related.
			global $bp;
			if ( $bp && ! bp_is_blog_page() ) {
				echo '<h1>' . get_the_title() . '</h1>';
			}

			 // Fullscreen row option.
			if ( $nectar_fp_options['page_full_screen_rows'] == 'on' ) {
				echo '<div id="nectar_fullscreen_rows" data-animation="' . esc_attr( $nectar_fp_options['page_full_screen_rows_animation'] ) . '" data-row-bg-animation="' . esc_attr( $nectar_fp_options['page_full_screen_rows_bg_img_animation'] ) . '" data-animation-speed="' . esc_attr( $nectar_fp_options['page_full_screen_rows_animation_speed'] ) . '" data-content-overflow="' . esc_attr( $nectar_fp_options['page_full_screen_rows_content_overflow'] ) . '" data-mobile-disable="' . esc_attr( $nectar_fp_options['page_full_screen_rows_mobile_disable'] ) . '" data-dot-navigation="' . esc_attr( $nectar_fp_options['page_full_screen_rows_dot_navigation'] ) . '" data-footer="' . esc_attr( $nectar_fp_options['page_full_screen_rows_footer'] ) . '" data-anchors="' . esc_attr( $nectar_fp_options['page_full_screen_rows_anchors'] ) . '">';
			}

			if ( have_posts() ) :
				while ( have_posts() ) :

					the_post();

					the_content();

				 endwhile;
			 endif;

			if ( $nectar_fp_options['page_full_screen_rows'] == 'on' ) {
				echo '</div>';
			}
			?>

		</div><!--/row-->
	</div><!--/container-->
</div><!--/container-wrap-->

<?php get_footer(); ?>