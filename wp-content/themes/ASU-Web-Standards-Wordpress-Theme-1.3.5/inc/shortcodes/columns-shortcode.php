<?php
/**
 * Columns Shortcode used for simplifying Bootstrap code
 *
 * @author Global Insititue of Sustainability
 * @author Ivan Montiel
 *
 * @package asu-wordpress-web-standards
 */
if ( ! function_exists( 'asu_wp_column_shortcode' ) ) :
  /**
 * Columns
 * =======
 *
 * Columns for rows
 *
 * @param $atts - associative array. You can override 'size'.
 * @param $content - content
 */
  function asu_wp_column_shortcode( $atts, $content = null ) {
    $wrapper = '<div class="%1$s">%2$s</div>';

    if ( ! isset( $atts['size'] ) ) {
      $classes = '';
    } else {
      if ( is_numeric( $atts['size'] ) ) {
        $size = $atts['size'];

        $mapper = array(
          '1'  => 'col-md-1',
          '2'  => 'col-md-2',
          '3'  => 'col-md-3',
          '4'  => 'col-sm-6 col-md-4',
          '5'  => 'col-sm-6 col-md-5 col-lg-4',
          '6'  => 'col-md-6',
          '7'  => 'col-sm-12 col-md-7 col-lg-8',
          '8'  => 'col-md-8',
          '9'  => 'col-md-9',
          '10' => 'col-md-10',
          '11' => 'col-md-11',
          '12' => 'col-md-12',
        );

        $classes = $mapper[ $size ];
      } else {
        $classes = $atts['size'];
      }
    }

    // Any additional custom classes to add
    if ( $atts != null && array_key_exists( 'class', $atts ) ) {
      $classes .= ' '.$atts['class'];
    }

    return do_shortcode( sprintf( $wrapper, $classes, $content ) );
  }
  add_shortcode( 'column', 'asu_wp_column_shortcode' );
endif;