<?php
/**
 * Buttons Shortcode used for simplifying Bootstrap code
 *
 * @author Global Institute of Sustainability
 * @author Ivan Montiel
 *
 * @package asu-wordpress-web-standards
 */
if ( ! function_exists( 'asu_breadcrumbs' ) ) :
  /**
   * Used for internal purposes
   */
  function asu_breadcrumbs() {
    $markup = '';

    // The home page is considered index.php which is used to render the blog.
    // Sometimes the home page is NOT the front page, which is the case with
    // most of the sites that will use this theme.  Since we only want breadcrumbs
    // to not show up on the front page, we will not check for is_home as most
    // online discussions would suggest.
    if ( function_exists( 'yoast_breadcrumb' ) /* && !is_home() */ && ! is_front_page() ) {
      $markup  = '<div class="asu-breadcrumbs">';
      $markup .= '  <div class="container">';
      $markup .= '    <div class="row">';
      $markup .= '      <div class="col-md-12">';
      ob_start();
      yoast_breadcrumb( '<div id="breadcrumbs" class="breadcrumb">', '</div>' );
      $markup .= ob_get_contents();
      ob_end_clean();
      $markup .= '      </div>';
      $markup .= '    </div>';
      $markup .= '  </div>';
      $markup .= '</div>';
    }

    return $markup;
  }
  add_shortcode( 'asu_breadcrumbs', 'asu_breadcrumbs' );
endif;