<?php
/**
 * Custom wp_nav_menu walker for the Custom Menu widget.
 *
 * @package Ocean_Extra
 * @category Core
 * @author OceanWP
 */

if ( ! class_exists( 'Ocean_Extra_Nav_Walker' ) ) {

	class Ocean_Extra_Nav_Walker extends Walker_Nav_Menu {

		/**
		 * Middle logo menu breaking point
		 *
		 * @access  private
		 * @var init
		 */
		private $break_point = null;

		/**
		 * Middle logo menu number of top level items displayed
		 *
		 * @access  private
		 * @var init
		 */
		private $displayed = 0;

		/**
		 * Starts the list before the elements are added.
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param int    $depth  Depth of menu item. Used for padding.
		 * @param array  $args   An array of arguments. @see wp_nav_menu()
		 */
		public function start_lvl( &$output, $depth = 0, $args = array() ) {
			$indent = str_repeat( "\t", $depth );

			$output .= "\n$indent<ul class=\"sub-menu\">\n";
		}

		/**
		 * Modified the menu output.
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param object $item   Menu item data object.
		 * @param int    $depth  Depth of menu item. Used for padding.
		 * @param array  $args   An array of arguments. @see wp_nav_menu()
		 * @param int    $id     Current item ID.
		 */
		public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
			global $wp_query;
			$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

			// Set up empty variable.
			$class_names = '';

			$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
			$classes[] = 'menu-item-' . $item->ID;

			// Nav no click
			if ( $item->nolink != '' ) {
				$classes[] = 'nav-no-click';
			}

			/**
			 * Filter the CSS class(es) applied to a menu item's <li>.
			 *
			 * @param array  $classes The CSS classes that are applied to the menu item's <li>.
			 * @param object $item    The current menu item.
			 * @param array  $args    An array of wp_nav_menu() arguments.
			 */
			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
			$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

			/**
			 * Filter the ID applied to a menu item's <li>.
			 *
			 * @param string $menu_id The ID that is applied to the menu item's <li>.
			 * @param object $item    The current menu item.
			 * @param array  $args    An array of wp_nav_menu() arguments.
			 */
			$id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args );
			$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

			// <li> output.
			$output .= $indent . '<li ' . $id . $class_names . '>';

			// link attributes
			$attributes  = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) . '"' : '';
			$attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) . '"' : '';
			$attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) . '"' : '';
			$attributes .= ! empty( $item->url ) ? ' href="' . esc_url( $item->url ) . '"' : '';

			// Icon.
			$icon = '';
			if ( $item->icon != '' ) {
				$icon = '<i class="' . $item->icon . '"></i>';
			}

			// Description
			$description = '';
			if ( $item->description != '' ) {
				$description = '<span class="nav-content">' . $item->description . '</span>';
			}

			// Text before and after
			$text_before = '';
			$text_after  = '';
			if ( $item->icon != '' ) {
				$text_before = '<span class="menu-text">';
				$text_after  = '</span>';
			}

			// Output
			$item_output = $args->before;

			$item_output .= '<a' . $attributes . ' class="menu-link">';

			$item_output .= $args->link_before . $icon . $text_before . apply_filters( 'the_title', $item->title, $item->ID ) . $text_after . $args->link_after;

			if ( $depth !== 0 ) {
				$item_output .= $description;
			}

			$item_output .= '</a>';

			$item_output .= $args->after;

			// Build html
			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );

		}

		/**
		 * Ends the list of after the elements are added.
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param int    $depth  Depth of menu item. Used for padding.
		 * @param array  $args   An array of arguments. @see wp_nav_menu()
		 */
		public function end_lvl( &$output, $depth = 0, $args = array() ) {
			$indent  = str_repeat( "\t", $depth );
			$output .= "$indent</ul>\n";
		}

		/**
		 * Icon if sub menu.
		 */
		public function display_element( $element, &$children_elements = array(), $max_depth = 0, $depth = 0, $args = array(), &$output = '' ) {

			// Define vars
			$id_field     = $this->db_fields['id'];
			$header_style = oceanwp_header_style();

			if ( is_object( $args[0] ) ) {
				$args[0]->has_children = ! empty( $children_elements[ $element->$id_field ] );
			}

			// Down Arrows
			if ( ! empty( $children_elements[ $element->$id_field ] ) && ( $depth == 0 )
				|| $element->category_post != '' && $element->object == 'category' ) {
				$element->classes[] = 'dropdown';
				if ( true == get_theme_mod( 'ocean_menu_arrow_down', true ) ) {
					$element->title .= ' <span class="nav-arrow fa fa-angle-down"></span>';
				}
			}

			// Right/Left Arrows
			if ( ! empty( $children_elements[ $element->$id_field ] ) && ( $depth > 0 ) ) {
				$element->classes[] = 'dropdown';
				if ( true == get_theme_mod( 'ocean_menu_arrow_side', true ) ) {
					if ( is_rtl() ) {
						$element->title .= '<span class="nav-arrow fa fa-angle-left"></span>';
					} else {
						$element->title .= '<span class="nav-arrow fa fa-angle-right"></span>';
					}
				}
			}

			// Define walker
			Walker_Nav_Menu::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );

		}

	}

}
