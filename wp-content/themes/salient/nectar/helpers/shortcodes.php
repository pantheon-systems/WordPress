<?php
/**
 * Shortcodes
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
// Shortcodes - have to load after taxonomy/post type declarations
// -----------------------------------------------------------------#

// utility function for nectar shortcode generator conditional
if ( ! function_exists( 'nectar_is_edit_page' ) ) {
	function nectar_is_edit_page( $new_edit = null ) {
		global $pagenow;
		// make sure we are on the backend
		if ( ! is_admin() ) {
			return false; }

		if ( $new_edit == 'edit' ) {
			return in_array( $pagenow, array( 'post.php' ) );
		} elseif ( $new_edit == 'new' ) { // check for new post page
			return in_array( $pagenow, array( 'post-new.php' ) );
		} else { // check for either new or edit
			return in_array( $pagenow, array( 'post.php', 'post-new.php' ) );
		}
	}
}


// load nectar shortcode button
function nectar_shortcode_init() {

	require_once get_template_directory() . '/nectar/tinymce/tinymce-class.php';

}


if ( is_admin() ) {
	if ( nectar_is_edit_page() ) {

		add_action( 'init', 'nectar_shortcode_init' );

	}
}

// Add button to page
add_action( 'media_buttons', 'nectar_buttons', 100 );

function nectar_buttons() {
	 echo "<a data-effect='mfp-zoom-in' class='button nectar-shortcode-generator' href='#nectar-sc-generator'><img src='" . get_template_directory_uri() . "/nectar/assets/img/icons/n.png' /> " . esc_html__( 'Nectar Shortcodes', 'salient' ) . '</a>';
}


// Shortcode Processing
if ( ! function_exists( 'nectar_shortcode_processing' ) ) {
	function nectar_shortcode_processing() {
		require_once get_template_directory() . '/nectar/tinymce/shortcode-processing.php';
	}
}


add_action( 'init', 'nectar_shortcode_processing' );
