<?php
/**
 * Container Shortcode used for simplifying Bootstrap code
 *
 * @author Global Insititue of Sustainability
 * @author Ivan Montiel
 *
 * @package asu-wordpress-web-standards
 */


if ( ! function_exists( 'asu_wp_container_shortcode' ) ) :
  /**
 * Containers
 * ==========
 * [container type="gray" margin="0" padding="0" class=""]
 *
 * Containers with gray option
 * - type=gray
 * - margin/padding=0, sm, md, lg, xl, top-0, top-sm, top-md, top-lg, top-xl, bot-0, bot-sm, bot-md, bot-lg, bot-xl
 *
 * @param atts - associative array.  You can override 'type' to 'gray'
 */
  function asu_wp_container_shortcode( $atts, $content = null ) {
    $margin_class_mapper = [
      'bot-xl' => 'space-bot-xl ',
      'bot-lg' => 'space-bot-lg ',
      'bot-md' => 'space-bot-md ',
      'bot-sm' => 'space-bot-sm ',
      'bot-0' => 'space-bot-0 ',
      'top-xl' => 'space-top-xl ',
      'top-lg' => 'space-top-lg ',
      'top-md' => 'space-top-md ',
      'top-sm' => 'space-top-sm ',
      'top-0' => 'space-top-0 ',
      'xl' => 'space-top-xl space-bot-xl ',
      'lg' => 'space-top-lg space-bot-lg ',
      'md' => 'space-top-md space-bot-md ',
      'sm' => 'space-top-sm space-bot-sm ',
      '0' => 'space-top-0 space-bot-0 ',
    ];

    $padding_class_mapper = [
      'bot-xl' => 'pad-bot-xl ',
      'bot-lg' => 'pad-bot-lg ',
      'bot-md' => 'pad-bot-md ',
      'bot-sm' => 'pad-bot-sm ',
      'bot-0' => 'pad-bot-0 ',
      'top-xl' => 'pad-top-xl ',
      'top-lg' => 'pad-top-lg ',
      'top-md' => 'pad-top-md ',
      'top-sm' => 'pad-top-sm ',
      'top-0' => 'pad-top-0 ',
      'xl' => 'pad-top-xl pad-bot-xl ',
      'lg' => 'pad-top-lg pad-bot-lg ',
      'md' => 'pad-top-md pad-bot-md ',
      'sm' => 'pad-top-sm pad-bot-sm ',
      '0' => 'pad-top-0 pad-bot-0 ',
    ];

    $container = '<div class="container %2$s">%1$s</div>';
    $classes   = '';

    // ==============
    // Gray Container
    // ==============
    if ( $atts != null && array_key_exists( 'type', $atts ) ) {
      if ( 'gray' == $atts['type'] ) {
        $wrap_container = '<div class="gray-back %2$s">%1$s</div>';

        // No extra classes needed
        $container = sprintf( $container, '%1$s', '' );
        // Wrap
        $container = sprintf( $wrap_container, $container, '%2$s' );
      }
    }

    // ======
    // Margin
    // ======
    if ( $atts != null && array_key_exists( 'margin', $atts ) ) {
      // Copy the spacing attributes
      $copy_spacing = (string) $atts['margin'];

      // Work backwards so that the short spacing names are not falsely added
      foreach ( $margin_class_mapper as $key => $item ) {
        // Force teh $key to be a string since '0' ==> 0
        if ( false !== strpos( $copy_spacing, (string) $key ) ) {
          $copy_spacing = str_replace( $key, '', $copy_spacing );
          $classes     .= $item;
        }
      }
    }

    // =======
    // Padding
    // =======
    if ( $atts != null && array_key_exists( 'padding', $atts ) ) {
      // Copy the spacing attributes
      $copy_spacing = (string) $atts['padding'];

      // Work backwards so that the short spacing names are not falsely added
      foreach ( $padding_class_mapper as $key => $item ) {
        // Force teh $key to be a string since '0' ==> 0
        if ( false !== strpos( $copy_spacing, (string) $key ) ) {
          $copy_spacing = str_replace( $key, '', $copy_spacing );
          $classes     .= $item;
        }
      }
    }

    // Any custom classes to add
    if ( $atts != null && array_key_exists( 'class', $atts ) ) {
      $classes .= ' '.$atts['class'];
    }

    // Finish up
    $container = sprintf( $container, '%1$s', $classes );

    return do_shortcode( sprintf( $container, $content ) );
  }
  add_shortcode( 'container', 'asu_wp_container_shortcode' );
endif;