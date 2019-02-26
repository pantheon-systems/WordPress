<?php
if ( ! is_admin() ) {
	$in_footer = apply_filters( 'gtm4wp_soundcloud', false);
	wp_enqueue_script( "gtm4wp-soundcloud-api", "https://w.soundcloud.com/player/api.js", array(), "1.0", $in_footer );
	wp_enqueue_script( "gtm4wp-soundcloud", $gtp4wp_plugin_url . "js/gtm4wp-soundcloud.js", array( "jquery" ), GTM4WP_VERSION, $in_footer );
}