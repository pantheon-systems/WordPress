<?php
/**
 * Post single fixed position bottom sharing buttons.
 *
 * @package Salient WordPress Theme
 * @subpackage Partials
 * @version 9.0.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$options = get_nectar_theme_options();

?>

<div class="nectar-social-sharing-fixed"> 

	<?php
	// portfolio social sharting icons
	if ( ! empty( $options['blog_social'] ) && $options['blog_social'] == 1 ) {

		echo '<a href="#"><i class="icon-default-style steadysets-icon-share"></i></a> <div class="nectar-social">';

		// facebook
		if ( ! empty( $options['blog-facebook-sharing'] ) && $options['blog-facebook-sharing'] == 1 ) {
			echo "<a class='facebook-share nectar-sharing' href='#' title='" . esc_attr__( 'Share this', 'salient' ) . "'> <i class='fa fa-facebook'></i> </a>";
		}
		// twitter
		if ( ! empty( $options['blog-twitter-sharing'] ) && $options['blog-twitter-sharing'] == 1 ) {
			echo "<a class='twitter-share nectar-sharing' href='#' title='" . esc_attr__( 'Tweet this', 'salient' ) . "'> <i class='fa fa-twitter'></i> </a>";
		}
		// google plus
		if ( ! empty( $options['blog-google-plus-sharing'] ) && $options['blog-google-plus-sharing'] == 1 ) {
			echo "<a class='google-plus-share nectar-sharing-alt' href='#' title='" . esc_attr__( 'Share this', 'salient' ) . "'> <i class='fa fa-google-plus'></i> </a>";
		}

		// linkedIn
		if ( ! empty( $options['blog-linkedin-sharing'] ) && $options['blog-linkedin-sharing'] == 1 ) {
			echo "<a class='linkedin-share nectar-sharing' href='#' title='" . esc_attr__( 'Share this', 'salient' ) . "'> <i class='fa fa-linkedin'></i> </a>";
		}
		// pinterest
		if ( ! empty( $options['blog-pinterest-sharing'] ) && $options['blog-pinterest-sharing'] == 1 ) {
			echo "<a class='pinterest-share nectar-sharing' href='#' title='" . esc_attr__( 'Pin this', 'salient' ) . "'> <i class='fa fa-pinterest'></i> </a>";
		}

		echo '</div>';

	}
	?>
</div><!--nectar social sharing fixed-->
