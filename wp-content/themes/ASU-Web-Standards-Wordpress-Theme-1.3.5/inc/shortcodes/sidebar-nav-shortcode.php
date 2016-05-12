<?php
/**
 * SideBar Nav Shortcode used for simplifying Bootstrap code
 *
 * @author Global Insititue of Sustainability
 * @author Ivan Montiel
 *
 * @package asu-wordpress-web-standards
 */
if ( ! function_exists( 'asu_wp_sidebar_shortcode' ) ) :
  /**
   * Sidebar Nav
   * ===========
   *
   * Navbar with group parameters
   *
   * [sidebar (title="example") (affix=true) (spy=true)]
   *
   * @param $atts - associative array. You can override 'title'.
   * @param $content - content should be of the form 'text|#id' with one on each line.
   */
  function asu_wp_sidebar_shortcode( $atts, $content = null ) {
    $container = '<div id="%4$s" class="sidebar-nav %3$s"><h4>%1$s</h4>%2$s</div>';
    $list      = '<ul class="list-group nav nav-stacked">%s</ul>';
    $list_item = '<li><a class="list-group-item" href="%1$s">%2$s</a></li>';
    $title     = 'Navigate this Doc';
    // Unique ID will only be set if affix is true
    $unique_id = '';

    if ( $atts != null && array_key_exists( 'title', $atts ) ) {
      $title = $atts['title'];
    }

    if ( $atts != null && array_key_exists( 'affix', $atts ) ) {
      if ( 'true' === $atts['affix'] ) {
        $unique_id = 'sidebarNav';
      }
    }

    if ( $atts != null && array_key_exists( 'spy', $atts ) ) {
      if ( 'true' === $atts['affix'] ) {
        $unique_id = 'sidebarNav';

        add_action(
            'wp_footer',
            function () {
              echo '
                <script>
                  $(function () {
                    $("body").scrollspy({ target: "#sidebarNav" });
                  });
                </script>';
            },
            1000
        );
      }
    }

    $classes = '';
    // Any custom classes to add
    if ( $atts != null && array_key_exists( 'class', $atts ) ) {
      $classes .= ' '.$atts['class'];
    }

    $cleaned = str_replace( '<br />', "\n", $content );
    $cleaned = str_replace( '<br/>', "\n", $cleaned );
    $cleaned = str_replace( '<br>', "\n", $cleaned );

    $user_list_items = explode( "\n", $cleaned );
    $user_list_items_inst = '';
    foreach ( $user_list_items as $_ => $value ) {
      // [0] => Text, [1] => link
      $item_parts = explode( '|', $value );

      if ( count( $item_parts ) <= 1 ) {
        continue; }

      $user_list_item_inst   = sprintf( $list_item, trim( $item_parts[1] ), trim( $item_parts[0] ) );
      $user_list_items_inst .= $user_list_item_inst;
    }

    $list = sprintf( $list, $user_list_items_inst );

    return do_shortcode( sprintf( $container, $title, $list, $classes, $unique_id ) );
  }
  add_shortcode( 'sidebar', 'asu_wp_sidebar_shortcode' );
endif;
