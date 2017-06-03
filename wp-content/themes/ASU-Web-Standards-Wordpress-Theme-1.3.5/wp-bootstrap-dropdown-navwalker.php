<?php

/**
 * Class Name: wp_bootstrap_footer_navwalker
 *
 * @package asu-wordpress-web-standards
 */

class WP_Bootstrap_Dropdown_Navwalker extends Walker_Nav_Menu {
  public function start_lvl( &$output, $depth = 0, $args = array() ) {
    return;
  }

  public function end_lvl( &$output, $depth = 0, $args = array() ) {
    if ( $args == null || empty( $args ) || ! is_object( $args ) ) {
      return;
    }
    $output .= "\n</ul>";
  }

  public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
    $classes   = array();
    $classes[] = 'menu-item-' . $item->ID;

    if ( ! empty( $item->classes ) ) {
      $classes = (array) $item->classes;
    }

    $array_filter = array_filter( $classes );
    $filters      = apply_filters( 'nav_menu_css_class', $array_filter, $item, $args );
    $class_names  = join( ' ', $filters );

    if ( $class_names ) {
      $class_names = ' class="' . esc_attr( $class_names ) . '"';
    } else {
      $class_names = '';
    }

    $id     = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
    $idFull = $id ? ' id="' . esc_attr( $id ) . '"' : '';

    // Make sure top levels are treated differently
    $atts = array();
    $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
    $atts['target'] = ! empty( $item->target )     ? $item->target     : '';
    $atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
    $atts['href']   = ! empty( $item->url )        ? $item->url        : '';

    $is_top_level = $atts['href'] === null ||
                  $atts['href'] === '' ||
                  $atts['href'] === '#';

    if ( ! $is_top_level ) {
      $output .= '<li' . $idFull . $class_names .'>';
    }

    if ( $args == null || empty( $args ) || ! is_object( $args ) ) {
      return;
    }

    /**
     * Filter the HTML attributes applied to a menu item's <a>.
     *
     * @since 3.6.0
     *
     * @see wp_nav_menu()
     *
     * @param array $atts {
     *     The HTML attributes applied to the menu item's <a>, empty strings are ignored.
     *
     *     @type string $title  Title attribute.
     *     @type string $target Target attribute.
     *     @type string $rel    The rel attribute.
     *     @type string $href   The href attribute.
     * }
     * @param object $item The current menu item.
     * @param array  $args An array of wp_nav_menu() arguments.
     */
    $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

    $attributes = '';

    foreach ( $atts as $attr => $value ) {
      if ( ! empty( $value ) ) {
        $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
        $attributes .= ' ' . $attr . '="' . $value . '"';
      }
    }
    $item_output = $args->before;

    if ( ! $is_top_level ) {
      $item_output .= '<a'. $attributes .'>';
    }
    else {
      $target = $id ? esc_attr( $id ) . '-nav' : '';
      $item_output .= '<div class="col-md-2 col-sm-3 space-bot-md"><h2 data-toggle="collapse" data-target="#' . $target . '" >';
    }

    $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;

    if ( ! $is_top_level ) {
      $item_output .= '</a>';
    }
    else {
      $item_output .= '  <span class="caret hidden-sm hidden-md hidden-lg"></span></h2>';
      $item_output .= "\n<ul class='big-foot-nav collapse' id='" . $target . "'>";
    }
    $item_output .= $args->after;
    /**
     * Filter a menu item's starting output.
     *
     * The menu item's starting output only includes $args->before, the opening <a>,
     * the menu item's title, the closing </a>, and $args->after. Currently, there is
     * no filter for modifying the opening and closing <li> for a menu item.
     *
     * @since 3.0.0
     *
     * @see wp_nav_menu()
     *
     * @param string $item_output The menu item's starting HTML output.
     * @param object $item        Menu item data object.
     * @param int    $depth       Depth of menu item. Used for padding.
     * @param array  $args        An array of wp_nav_menu() arguments.
     */
    $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
  }

  public function end_el( &$output, $item, $depth = 0, $args = array() ) {
    if ( $args == null || empty( $args ) || ! is_object( $args ) ) {
      return;
    }

    $atts['href'] = ! empty( $item->url ) ? $item->url : '';
    $isTopLevel   = $atts['href'] === null ||
                    $atts['href'] === '' ||
                    $atts['href'] === '#';

    if ( ! $isTopLevel ) {
      $output .= "</li>\n";
    }
    else {
      $output .= "</div>\n";
    }
  }
}
