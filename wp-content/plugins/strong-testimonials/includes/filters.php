<?php

/**
 * Remove whitespace between tags. Helps prevent double wpautop in plugins
 * like Posts For Pages and Custom Content Shortcode.
 *
 * @param $html
 *
 * @since 2.3
 *
 * @return mixed
 */
function wpmtst_remove_whitespace( $html ) {
	$options = get_option( 'wpmtst_options' );
	if ( $options['remove_whitespace'] ) {
		$html = preg_replace( '~>\s+<~', '><', $html );
	}

	return $html;
}

add_filter( 'strong_view_html', 'wpmtst_remove_whitespace' );
add_filter( 'strong_view_form_html', 'wpmtst_remove_whitespace' );
