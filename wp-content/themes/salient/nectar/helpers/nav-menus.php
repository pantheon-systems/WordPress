<?php
/**
 * Navigation menu related helper functions
 *
 * @package Salient WordPress Theme
 * @subpackage helpers
 * @version 9.0.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



if ( function_exists( 'register_nav_menus' ) ) {

	function nectar_add_theme_menu_locations() {

		global $nectar_options;

		$sideWidgetArea                = ( ! empty( $nectar_options['header-slide-out-widget-area'] ) ) ? $nectar_options['header-slide-out-widget-area'] : 'off';
		$usingPRCompatLayout           = false;
		$usingTopLeftRightCompatLayout = false;

		if ( ! empty( $nectar_options['header_format'] ) && $nectar_options['header_format'] == 'menu-left-aligned' || $nectar_options['header_format'] == 'centered-menu' ) {
			$usingPRCompatLayout = true;
		}

		if ( ! empty( $nectar_options['header_format'] ) && $nectar_options['header_format'] == 'centered-menu-bottom-bar' ) {
			$usingTopLeftRightCompatLayout = true;
		}

		if ( $sideWidgetArea == '1' ) {

			if ( $usingPRCompatLayout == true ) {

				$nectar_menu_arr = array(
					'top_nav'            => 'Top Navigation Menu',
					'top_nav_pull_right' => 'Top Navigation Menu Pull Right',
					'secondary_nav'      => 'Secondary Navigation Menu <br /> <small>Will only display if applicable header layout is selected.</small>',
					'off_canvas_nav'     => 'Off Canvas Navigation Menu',
				);

			} elseif ( $usingTopLeftRightCompatLayout == true ) {

				$nectar_menu_arr = array(
					'top_nav'           => 'Top Navigation Menu',
					'top_nav_pull_left' => 'Top Navigation Menu Pull Left',
					'off_canvas_nav'    => 'Off Canvas Navigation Menu',
				);

			} else {
				$nectar_menu_arr = array(
					'top_nav'        => 'Top Navigation Menu',
					'secondary_nav'  => 'Secondary Navigation Menu <br /> <small>Will only display if applicable header layout is selected.</small>',
					'off_canvas_nav' => 'Off Canvas Navigation Menu',
				);
			}
		} else {

			if ( $usingPRCompatLayout == true ) {

				$nectar_menu_arr = array(
					'top_nav'            => 'Top Navigation Menu',
					'top_nav_pull_right' => 'Top Navigation Menu Pull Right',
					'secondary_nav'      => 'Secondary Navigation Menu <br /> <small>Will only display if applicable header layout is selected.</small>',
				);

			} else {
				$nectar_menu_arr = array(
					'top_nav'       => 'Top Navigation Menu',
					'secondary_nav' => 'Secondary Navigation Menu <br /> <small>Will only display if applicable header layout is selected.</small>',
				);
			}
		}

		register_nav_menus( $nectar_menu_arr );

	}

	add_action( 'after_setup_theme', 'nectar_add_theme_menu_locations' );

}






// dropdown arrows
if ( ! function_exists( 'nectar_walker_nav_menu' ) ) {
	function nectar_walker_nav_menu() {

		class Nectar_Arrow_Walker_Nav_Menu extends Walker_Nav_Menu {
			function display_element( $element, &$children_elements, $max_depth, $depth = 0, $args, &$output ) {
				$id_field = $this->db_fields['id'];
				global $nectar_options;

						$theme_skin     = ( ! empty( $nectar_options['theme-skin'] ) ) ? $nectar_options['theme-skin'] : 'original';
						$header_format  = ( ! empty( $nectar_options['header_format'] ) ) ? $nectar_options['header_format'] : 'default';
						$dropdownArrows = ( ! empty( $nectar_options['header-dropdown-arrows'] ) && $header_format != 'left-header' ) ? $nectar_options['header-dropdown-arrows'] : 'inherit';

				if ( $header_format == 'centered-menu-bottom-bar' ) {
					$theme_skin = 'material';
				}

				if ( $theme_skin == 'material' ) {
					$theme_skin = 'ascend';
				}

				$header_format = ( ! empty( $nectar_options['header_format'] ) ) ? $nectar_options['header_format'] : 'default';

				// button styling
				$button_style = get_post_meta( $element->$id_field, 'menu-item-nectar-button-style', true );
				if ( ! empty( $button_style ) ) {
					$element->classes[] = $button_style;
				}

				if ( ! empty( $children_elements[ $element->$id_field ] ) && $element->menu_item_parent == 0 && $theme_skin != 'ascend' && $header_format != 'left-header' && $dropdownArrows != 'dont_show' ||
								! empty( $children_elements[ $element->$id_field ] ) && $element->menu_item_parent == 0 && $dropdownArrows == 'show' ) {
					$element->title     = $element->title . '<span class="sf-sub-indicator"><i class="icon-angle-down"></i></span>';
					$element->classes[] = 'sf-with-ul';
				}

				if ( ! empty( $children_elements[ $element->$id_field ] ) && $element->menu_item_parent != 0 && $header_format != 'left-header' ) {
					$element->title = $element->title . '<span class="sf-sub-indicator"><i class="icon-angle-right"></i></span>';
				}

				if ( empty( $button_style ) && $header_format == 'left-header' ) {
					$element->title = '<span>' . $element->title . '</span>';
				}

				Walker_Nav_Menu::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
			}
		}

	}
}

nectar_walker_nav_menu();









if ( ! function_exists( 'nectar_description_walker_nav_menu' ) ) {
	function nectar_description_walker_nav_menu( $item_output, $item, $depth, $args ) {
		if ( 'off_canvas_nav' == $args->theme_location && $item->description ) {
			$item_output = str_replace( $args->link_after . '</a>', $args->link_after . '</a><small class="nav_desc">' . $item->description . '</small>', $item_output );
		}

		return $item_output;
	}
}

add_filter( 'walker_nav_menu_start_el', 'nectar_description_walker_nav_menu', 10, 4 );










if ( ! function_exists( 'nectar_nav_button_style' ) ) {

	function nectar_nav_button_style( $output, $item, $depth, $args ) {

		$item_id = $item->ID;
		$name    = 'menu-item-nectar-button-style';
		$value   = get_post_meta( $item_id, $name, true );

		?>

	  <p class="description description-wide">
			<label for="<?php echo esc_attr( $name ) . '-' . esc_attr( $item_id ); ?>">
				<?php echo __( 'Menu Item Style', 'salient' ); ?> <br />
				<select id="<?php echo esc_attr( $name ) . '-' . esc_attr( $item_id ); ?>" class="widefat edit-menu-item-target" name="<?php echo esc_attr( $name ) . '[' . esc_attr( $item_id ) . ']'; ?>">
					<option value="" <?php selected( $value, '' ); ?>><?php echo esc_html__( 'Standard', 'salient' ); ?> </option>
					<option value="button_solid_color" <?php selected( $value, 'button_solid_color' ); ?>><?php echo esc_html__( 'Button Accent Color', 'salient' ); ?> </option>
					<option value="button_solid_color_2" <?php selected( $value, 'button_solid_color_2' ); ?>><?php echo esc_html__( 'Button Extra Color #1', 'salient' ); ?> </option>
					<option value="button_bordered" <?php selected( $value, 'button_bordered' ); ?>><?php echo esc_html__( 'Button Bordered Accent Color', 'salient' ); ?> </option>
					<option value="button_bordered_2" <?php selected( $value, 'button_bordered_2' ); ?>><?php echo esc_html__( 'Button Bordered Extra Color #1', 'salient' ); ?> </option>
				</select>
			</label>
		</p>
			
		<?php
	}
}

	add_action( 'wp_nav_menu_item_custom_fields', 'nectar_nav_button_style', 10, 4 );






	$nectar_custom_menu_fields = array(
		'menu-item-nectar-button-style' => '',
	);

	function nectar_nav_button_style_update( $menu_id, $menu_item_db_id, $menu_item_args ) {

		$current_screen = get_current_screen();

		// fix auto add new pages to top nav
		$on_post_type = ( $current_screen && isset( $current_screen->post_type ) && ! empty( $current_screen->post_type ) ) ? true : false;

		global $nectar_custom_menu_fields;

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX || $on_post_type ) {
			return;
		}
		check_admin_referer( 'update-nav_menu', 'update-nav-menu-nonce' );

		foreach ( $nectar_custom_menu_fields as $key => $label ) {

			// Sanitize
			if ( ! empty( $_POST[ $key ][ $menu_item_db_id ] ) ) {
				// Do some checks here...
				$value = sanitize_text_field( $_POST[ $key ][ $menu_item_db_id ] );
			} else {
				$value = null;
			}

			// Update
			if ( ! is_null( $value ) ) {
				update_post_meta( $menu_item_db_id, $key, $value );
			} else {
				delete_post_meta( $menu_item_db_id, $key );
			}
		}
	}

	add_action( 'wp_update_nav_menu_item', 'nectar_nav_button_style_update', 10, 3 );
