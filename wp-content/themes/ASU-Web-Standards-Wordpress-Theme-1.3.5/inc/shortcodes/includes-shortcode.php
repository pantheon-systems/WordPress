<?php
/**
 * Include another page in the current page
 *
 * @author Global Institute of Sustainability
 * @author Ivan Montiel
 *
 * @package asu-wordpress-web-standards
 */
if ( ! function_exists( 'include_page' ) ) :
  /**
   * Used to include another page into the current view
   *
   * @param array $atts - expects a 'path' entry
   */
  function include_page( $atts ) {
    if ( array_key_exists( 'path', $atts ) ) {
      $content_post = get_page_by_path( $atts['path'] );
      $content      = $content_post->post_content;
      $content      = apply_filters( 'the_content', $content );
      return $content;
    }

    // Otherwise do nothing
    return '';
  }
  add_shortcode( 'include-page', 'include_page' );
endif;