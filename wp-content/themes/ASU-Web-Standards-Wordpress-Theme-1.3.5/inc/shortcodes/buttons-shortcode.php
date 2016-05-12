<?php
/**
 * Buttons Shortcode used for simplifying Bootstrap code
 *
 * @author Global Insititue of Sustainability
 * @author Ivan Montiel
 *
 * @package asu-wordpress-web-standards
 */
if ( ! function_exists( 'asu_button_shortcode' ) ) :
  /**
 * Buttons.
 *
 * Attributes:
 * - Link (optional)
 * - Color (optional, defaults "default")
 * - Extra (block)
 * - id (optional)
 * - class (optional)
 *
 * @param $atts - associative array.
 * @param $content - content
 */
  function asu_button_shortcode( $atts, $content = null ) {
    $button = '<button %1$s>%2$s</button>';
    $ahref  = '<a href="%4$s" %1$s %3$s>%2$s</a>';
    $result = '';

    // Check if the attributes contain a link
    if ( array_key_exists( 'link', $atts ) ) {
      $result = sprintf( $ahref, '%1$s', '%2$s', '%3$s', $atts['link'] );
    } else {
      $result = $button;
    }

    $class = 'class="btn ';

    $colorMap = array(
      'default'   => 'btn-default',
      'primary'   => 'btn-primary',
      'secondary' => 'btn-secondary',
      'gold'      => 'btn-gold',
      'blue'      => 'btn-blue',
      'success'   => 'btn-success',
      'info'      => 'btn-info',
      'warning'   => 'btn-warning',
      'danger'    => 'btn-danger',
      'link'      => 'btn-link',
    );

    // Check if the attributes contain a color
    if ( array_key_exists( 'color', $atts ) ) {
      if ( array_key_exists( $atts['color'], $colorMap ) ) {
        $class .= ' ' . $colorMap[ $atts['color'] ];
      }
    }

    $sizeMap = array(
      'large'       => 'btn-lg',
      'medium'      => '',
      'small'       => 'btn-sm',
      'extra-small' => 'btn-xs',
    );

    // Check if the attributes contain a size
    if ( array_key_exists( 'size', $atts ) ) {
      if ( array_key_exists( $atts['size'], $sizeMap ) ) {
        $class .= ' ' . $sizeMap[ $atts['size'] ]; }
    }

    $extraMap = array( 'block' => 'btn-block' );

    // Check if we have extras
    if ( array_key_exists( 'extra', $atts ) ) {
      if ( array_key_exists( $atts['extra'], $extraMap ) ) {
        $class .= ' ' . $extraMap[ $atts['extra'] ];
      }
    }

    // Any custom classes to add
    if ( $atts != null && array_key_exists( 'class', $atts ) ) {
      $class .= ' '.$atts['class'];
    }

    $class .= '"';

    $id = '';

    if ( array_key_exists( 'id', $atts ) ) {
      $id = ' id="'.$atts['id'].'"';
    }

    return do_shortcode( sprintf( $result, $class, $content, $id ) );
  }
  add_shortcode( 'button', 'asu_button_shortcode' );
endif;