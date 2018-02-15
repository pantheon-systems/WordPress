<?php
/**
 * Deprecated functions
 */

/**
 * Our version of htmlspecialchars.
 *
 *
 * @since 2.0.0
 * @deprecated 2.23.0
 *
 * @param $string
 *
 * @return string
 */
function wpmtst_htmlspecialchars( $string ) {
	return htmlspecialchars( $string, ENT_QUOTES, get_bloginfo( 'charset' ) );
}


/**
 * Truncate post content
 *
 * Find first space after char_limit (e.g. 200).
 * If not found then char_limit is in the middle of the
 * last word (e.g. string length = 203) so no need to truncate.
 *
 * @param $content
 * @param $limit
 * @deprecated
 *
 * @return string
 */
function wpmtst_truncate( $content, $limit ) {
	/**
	 * Strip tags.
	 *
	 * @since 1.15.12
	 */
	$content = strip_tags( $content );

	if ( strlen( $content ) > $limit ) {
		$space_pos = strpos( $content, ' ', $limit );
		if ( $space_pos ) {
			$content = substr( $content, 0, $space_pos );
			//$content .= '&hellip;';
		}
	}

	return $content;
}


/**
 * Read More link to the post or a page.
 *
 * @deprecated 2.10.0
 */
function wpmtst_read_more() {}


/**
 * Return a list of categories after removing any orderby filters.
 *
 * @deprecated
 *
 * @return array|int|WP_Error
 */
function wpmtst_get_category_list() {
	return get_terms( 'wpm-testimonial-category', array( 'hide_empty' => false ) );
}

