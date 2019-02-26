<?php
/**
 * Open graph default tags
 *
 * @package Salient WordPress Theme
 * @subpackage helpers
 * @version 9.0.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// Open Graph
if ( ! function_exists( 'nectar_add_opengraph' ) ) {
	function nectar_add_opengraph() {
		global $post; // Ensures we can use post variables outside the loop

		// Start with some values that don't change.
		echo "<meta property='og:site_name' content='" . get_bloginfo( 'name' ) . "'/>"; // Sets the site name to the one in your WordPress settings
		echo "<meta property='og:url' content='" . esc_url( get_permalink() ) . "'/>"; // Gets the permalink to the post/page

		if ( is_singular() ) { // If we are on a blog post/page
			echo "<meta property='og:title' content='" . get_the_title() . "'/>"; // Gets the page title
			echo "<meta property='og:type' content='article'/>"; // Sets the content type to be article.
		} elseif ( is_front_page() or is_home() ) { // If it is the front page or home page
			echo "<meta property='og:title' content='" . get_bloginfo( 'name' ) . "'/>"; // Get the site title
			echo "<meta property='og:type' content='website'/>"; // Sets the content type to be website.
		}

		if ( has_post_thumbnail( $post->ID ) ) { // If the post has a featured image.
			$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'large' );
			echo "<meta property='og:image' content='" . esc_attr( $thumbnail[0] ) . "'/>"; // If it has a featured image, then display this for Facebook
		}

	}
}

$using_jetpack_publicize = ( class_exists( 'Jetpack' ) && in_array( 'publicize', Jetpack::get_active_modules() ) ) ? true : false;

if ( ! defined( 'WPSEO_VERSION' ) && ! class_exists( 'NY_OG_Admin' ) && ! class_exists( 'Wpsso' ) && $using_jetpack_publicize == false ) {
	add_action( 'wp_head', 'nectar_add_opengraph', 5 );
}
