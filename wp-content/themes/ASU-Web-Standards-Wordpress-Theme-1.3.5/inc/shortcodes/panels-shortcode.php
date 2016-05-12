<?php
/**
 * Panel Shortcode used for simplifying Bootstrap code
 *
 * @author Global Insititue of Sustainability
 * @author Ivan Montiel
 *
 * @package asu-wordpress-web-standards
 */
if ( ! function_exists( 'asu_wp_panel_shortcode' ) ) :
  /**
 * Panel
 * =====
 *
 * @param $atts - associative array. You can override 'type'.
 * @param $content - content
 */
  function asu_wp_panel_shortcode( $atts, $content = null ) {
    $wrapper = '<div class="%1$s"><div class="panel-body">%2$s</div></div>';

    if ( ! isset( $atts['type'] ) ) {
      $type = '';
      $prefix_content = '';
    } else {
      $type = $atts['type'];
      $prefix_content = '<h3>Explore Our Programs</h3>';
    }

    $mapper = array(
      ''  => '',
      'explore-programs'  => 'explore-programs',
    );

    $classes = 'panel ' . $mapper[ $type ];

    // Any custom classes to add
    if ( $atts != null && array_key_exists( 'class', $atts ) ) {
      $classes .= ' '.$atts['class'];
    }

    return do_shortcode( sprintf( $wrapper, $classes, $prefix_content . $content ) );
  }
  add_shortcode( 'panel', 'asu_wp_panel_shortcode' );
endif;