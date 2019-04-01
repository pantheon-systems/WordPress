<?php
/**
 * Post single default minimal header style bottom social sharing bar.
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

<div class="bottom-meta">	
	<?php

	$using_post_pag      = ( ! empty( $options['blog_next_post_link'] ) && $options['blog_next_post_link'] == '1' ) ? true : false;
	$using_related_posts = ( ! empty( $options['blog_related_posts'] ) && ! empty( $options['blog_related_posts'] ) == '1' ) ? true : false;
	$extra_bottom_space  = ( $using_related_posts && ! $using_post_pag ) ? 'false' : 'true';

	echo '<div class="sharing-default-minimal" data-bottom-space="' . $extra_bottom_space . '">'; // WPCS: XSS ok.
	  nectar_blog_social_sharing();
	echo '</div>';
	?>
</div>
