<?php
if ( ! is_admin() ) {
	$in_footer = apply_filters( 'gtm4wp_vimeo', false);
//	wp_enqueue_script( "gtm4wp-vimeo-froogaloop", $gtp4wp_plugin_url . "js/froogaloop.js", array(), "2.0", $in_footer );
	wp_enqueue_script( "gtm4wp-vimeo-api", "https://player.vimeo.com/api/player.js", array(), "1.0", $in_footer );
	wp_enqueue_script( "gtm4wp-vimeo", $gtp4wp_plugin_url . "js/gtm4wp-vimeo.js", array( "jquery" ), GTM4WP_VERSION, $in_footer );
}