<?php
function gtm4wp_youtube( $return, $url, $data ) {
	$site_url = site_url();
	$site_url_parts = parse_url( $site_url );

	if ( false !== strpos( $return, "youtube.com" ) ) {
		return str_replace( "feature=oembed", "feature=oembed&enablejsapi=1&origin=" . $site_url_parts["scheme"] . "://" . $site_url_parts["host"], $return );
	} else {
		return $return;
	}
}

add_filter( "oembed_result", "gtm4wp_youtube", 10, 3 );

if ( ! is_admin() ) {
	$in_footer = apply_filters( 'gtm4wp_youtube', false);
	wp_enqueue_script( "gtm4wp-youtube", $gtp4wp_plugin_url . "js/gtm4wp-youtube.js", array( "jquery" ), GTM4WP_VERSION, $in_footer );
}