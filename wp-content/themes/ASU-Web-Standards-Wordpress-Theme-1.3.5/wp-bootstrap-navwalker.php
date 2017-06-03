<?php

/**
 * Class Name: WP_Bootstrap_Navwalker
 * GitHub URI: https://github.com/twittem/wp-bootstrap-navwalker
 * Description: A custom WordPress nav walker class to implement the Bootstrap 3 navigation style in a custom theme using the WordPress built in menu manager.
 * Version: 2.0.4
 * Author: Edward McIntyre - @twittem
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

class WP_Bootstrap_Navwalker extends Walker_Nav_Menu {
  private $mega_menu_flag = false;

  /**
   * @see Walker::start_lvl()
   * @since 3.0.0
   *
   * @param string $output Passed by reference. Used to append additional content.
   * @param int $depth Depth of page. Used for padding.
   */
  public function start_lvl( &$output, $depth = 0, $args = array() ) {
    $indent = str_repeat( "\t", $depth );

    if ( 0 == $depth ) {
      $output .= "$indent<ul role=\"menu\" class=\" dropdown-menu\">\n"; }

    // if the depth is not 0 and args has children, then add a row
    if ( $args->children_has_children ) {
      $columns = 'col-md-' . floor( 12.0 / $args->number_of_children );

      if ( 5 === $args->number_of_children ) {
        $columns = 'col-md-5ths';
      }

      $output .= '<li class="li-row-container">' . "\n";
      $output .= '<div class="row">' . "\n";
      $output .= '<div class="column '  . $columns . ' vertical-border-right">' . "\n";
      $output .= '<ul>';
      $this->mega_menu_flag = $depth;
    }
  }

  public function end_lvl( &$output, $depth = 0, $args = array() ) {
    if ( $depth === $this->mega_menu_flag ) {
      $output .= "\n</div>\n</div>\n</li>\n</ul>";
      $this->mega_menu_flag = false;
    } else if ( 0 == $depth ) {
      $output .= "</ul>\n";
    }
  }

  /**
   * @see Walker::start_el()
   * @since 3.0.0
   *
   * @param string $output Passed by reference. Used to append additional content.
   * @param object $item Menu item data object.
   * @param int $depth Depth of menu item. Used for padding.
   * @param int $current_page Menu item ID.
   * @param object $args
   */
  public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
    $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

    /**
     * Dividers, Headers or Disabled
     * =============================
     * Determine whether the item is a Divider, Header, Disabled or regular
     * menu item. To prevent errors we use the strcasecmp() function to so a
     * comparison that is not case sensitive. The strcasecmp() function returns
     * a 0 if the strings are equal.
     */
    if ( 0 == strcasecmp( $item->attr_title, 'divider' ) &&  1 === $depth ) {
      $output .= $indent . '<li role="presentation" class="divider">';
    } else if ( 0 == strcasecmp( $item->title, 'divider' ) &&  1 === $depth ) {
      $output .= $indent . '<li role="presentation" class="divider">';
    } else if ( 0 == strcasecmp( $item->attr_title, 'dropdown-header' ) && 1 === $depth ) {
      $output .= $indent . '<li role="presentation" class="dropdown-header">' . esc_attr( $item->title );
    } else if ( 0 == strcasecmp( $item->attr_title, 'disabled' ) ) {
      $output .= $indent . '<li role="presentation" class="disabled"><a href="#">' . esc_attr( $item->title ) . '</a>';
    } else if ( false !== strpos( $item->attr_title, 'dropdown-title' ) ) {
      $output .= $indent . '<li role="presentation" class="dropdown-title">' . esc_attr( $item->title );
    } else {
      $class_names = $value = '';

      $classes   = empty( $item->classes ) ? array() : (array) $item->classes;
      $classes[] = 'menu-item-' . $item->ID;

      // Add the mega menu class if your children have children
      if ( $args->children_has_children ) {
        $classes[] = 'mega-menu';
      }

      $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );

      if ( $args->has_children ) {
        $class_names .= ' dropdown'; }

      if ( in_array( 'current-menu-item', $classes ) ) {
        $class_names .= ' active';
      }

      $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

      $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
      $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

      /**
       * Columns for mega menu
       * =====================
       */
      if ( 1 === $depth  && $args->has_children && $args->not_first ) {
        $columns = 'col-md-' .floor( 12.0 / $args->number_of_siblings );

        if ( 5 === $args->number_of_siblings ) {
          $columns = 'col-md-5ths';
        }

        if ( $args->not_last ) {
          $line = 'vertical-border-right'; }
        else {
          $line = ''; }

        $output .= "</ul>\n</div>\n<div class='column {$columns} $line'>\n<ul>\n";
      }

      // Override classes w/the dropdown title class if we are a child that has children
      if ( 1 === $depth && $args->has_children ) {
        $class_names = ' class="dropdown-title"';
      }

      $output .= $indent . '<li ' . $id . $value . $class_names .' >';

      // if we are a child that has children
      if ( 1 === $depth && $args->has_children ) {
        $output .= apply_filters( 'the_title', $item->title, $item->ID );
        $output .= "</li>\n";
        return;
      }

      $atts = array();
      $atts['title']  = ! empty( $item->title ) ? $item->title  : '';
      $atts['target'] = ! empty( $item->target )  ? $item->target : '';
      $atts['rel']    = ! empty( $item->xfn )   ? $item->xfn  : '';

      // If item has_children add atts to a.
      if ( $args->has_children && 0 === $depth  ) {
        $atts['href']          = '#';
        $atts['data-toggle']   = 'dropdown';
        $atts['class']         = 'dropdown-toggle';
        $atts['aria-haspopup'] = 'true';
      } else {
        $atts['href'] = ! empty( $item->url ) ? $item->url : '';
      }

      $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

      $attributes = '';
      foreach ( $atts as $attr => $value ) {
        if ( ! empty( $value ) ) {
          $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
          $attributes .= ' ' . $attr . '="' . $value . '"';
        }
      }

      $item_output = $args->before;

      /*
       * Glyphicons
       * ===========
       * Since the the menu item is NOT a Divider or Header we check the see
       * if there is a value in the attr_title property. If the attr_title
       * property is NOT null we apply it as the class name for the glyphicon.
       */
      if ( ! empty( $item->attr_title ) ) {
        $item_output .= '<a'. $attributes .'><span class="glyphicon ' . esc_attr( $item->attr_title ) . '"></span>&nbsp;'; }
      else {
        $item_output .= '<a'. $attributes .'>'; }

      $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
      $item_output .= ( $args->has_children && 0 === $depth ) ? ' <span class="caret"></span></a>' : '</a>';
      $item_output .= $args->after;

      if ( $depth > 0 && $args->has_children ) {
        $item_output .= "</li>\n";
      }

      $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth + 1, $args ) . "\n";
    }
  }

  public function end_el( &$output, $item, $depth = 0, $args = array() ) {
    if ( 1 === $depth && $args->depth > 2 ) {
      return;
    }

    $output .= "</li>\n";
  }
  /**
   * Traverse elements to create list from elements.
   *
   * Display one element if the element doesn't have any children otherwise,
   * display the element and its children. Will only traverse up to the max
   * depth and no ignore elements under that depth.
   *
   * This method shouldn't be called directly, use the walk() method instead.
   *
   * @see Walker::start_el()
   * @since 2.5.0
   *
   * @param object $element Data object
   * @param array $children_elements List of elements to continue traversing.
   * @param int $max_depth Max depth to traverse.
   * @param int $depth Depth of current element.
   * @param array $args
   * @param string $output Passed by reference. Used to append additional content.
   * @return null Null on failure with no changes to parameters.
   */
  public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
    if ( ! $element ) {
      return; }

        $id_field = $this->db_fields['id'];

        // Display this element.
    if ( is_object( $args[0] ) ) {
      $args[0]->has_children = ! empty( $children_elements[ $element->$id_field ] );
      $children_has_children = false;
      $number_of_children    = 0;

      if ( $args[0]->has_children ) {
        $number_of_children = count( $children_elements[ $element->$id_field ] );

        foreach ( $children_elements[ $element->$id_field ] as $_ => $child ) {
          if ( in_array( 'menu-item-has-children', $child->classes ) ) {
            $children_has_children = true;
            break;
          }
        }
      }

      $args[0]->children_has_children = $children_has_children;
      $args[0]->number_of_children    = $number_of_children;

      // Determine if we are a child
      $parent_id = $element->menu_item_parent;
      if ( array_key_exists( $parent_id, $children_elements ) ) {
        $parent_array = $children_elements[ $parent_id ];
        $my_id = $element->ID;
        $index = -1;

        for ( $i = 0; $i < count( $parent_array ); $i++ ) {
          if ( $my_id === $parent_array[ $i ]->ID ) {
            $index = $i;
            break;
          }
        }

        $args[0]->not_first = ! ( $i == 0 );
        $args[0]->not_last  = ! ( $i === count( $parent_array ) - 1 );
        $args[0]->number_of_siblings = count( $parent_array );
      }
    }

    parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
  }

  /**
   * Menu Fallback
   * =============
   * If this function is assigned to the wp_nav_menu's fallback_cb variable
   * and a manu has not been assigned to the theme location in the WordPress
   * menu manager the function with display nothing to a non-logged in user,
   * and will add a link to the WordPress menu manager if logged in as an admin.
   *
   * @param array $args passed from the wp_nav_menu function.
   *
   */
  public static function fallback( $args ) {
    if ( current_user_can( 'manage_options' ) ) {

      /** @todo: extract() usage is highly discouraged, due to the complexity and
       * unintended issues it might cause. */
      // @codingStandardsIgnoreStart
      extract( $args );
      // @codingStandardsIgnoreEnd

      $fb_output = null;

      if ( $container ) {
        $fb_output = '<' . $container;

        if ( $container_id ) {
          $fb_output .= ' id="' . $container_id . '"'; }

        if ( $container_class ) {
          $fb_output .= ' class="' . $container_class . '"'; }

        $fb_output .= '>';
      }

      $fb_output .= '<ul';

      if ( $menu_id ) {
        $fb_output .= ' id="' . $menu_id . '"'; }

      if ( $menu_class ) {
        $fb_output .= ' class="' . $menu_class . '"'; }

      $fb_output .= '>';
      $fb_output .= '<li><a href="' . admin_url( 'nav-menus.php' ) . '">Add a menu</a></li>';
      $fb_output .= '</ul>';

      if ( $container ) {
        $fb_output .= '</' . $container . '>'; }

      // @codingStandardsIgnoreStart
      echo $fb_output ;
      // @codingStandardsIgnoreEnd
    }
  }
}

