<?php

/**
 * Front end functionalities
 *
 */
final class OE_Menu_Icons_Front_End {

	/**
	 * Icon types
	 *
	 */
	protected static $icon_types = array();

	/**
	 * Default icon style
	 *
	 */
	protected static $default_style = array(
		'font_size'      => array(
			'property' => 'font-size',
			'value'    => '1.2',
			'unit'     => 'em',
		),
		'vertical_align' => array(
			'property' => 'vertical-align',
			'value'    => 'middle',
			'unit'     => null,
		),
		'svg_width'      => array(
			'property' => 'width',
			'value'    => '1',
			'unit'     => 'em',
		),
	);

	protected static $priority = 999;
	protected static $priorities_used = array();

	/**
	 * Hidden label class
	 *
	 */
	protected static $hidden_label_class = 'hidden';

	/**
	 * Add hooks for front-end functionalities
	 *
	 */
	public static function init() {
		$active_types = OE_Menu_Icons_Settings::get( 'global', 'icon_types' );

		if ( empty( $active_types ) ) {
			return;
		}

		foreach ( OE_Menu_Icons::get( 'types' ) as $type ) {
			if ( in_array( $type->id, $active_types, true ) ) {
				self::$icon_types[ $type->id ] = $type;
			}
		}

		/**
		 * Allow themes/plugins to override the hidden label class
		 *
		 */
		self::$hidden_label_class = apply_filters( 'oe_menu_icons_hidden_label_class', self::$hidden_label_class );

		/**
		 * Allow themes/plugins to override default inline style
		 *
		 */
		self::$default_style = apply_filters( 'oe_menu_icons_default_style', self::$default_style );

		add_action( 'wp_enqueue_scripts', array( __CLASS__, '_enqueue_styles' ), 7 );
		add_filter( 'wp_nav_menu_args', array( __CLASS__, '_add_menu_item_title_filter' ) );
		add_filter( 'wp_nav_menu_items', array( __CLASS__, '_remove_menu_item_title_empty_items_filter' ), 2, 999 );
		add_filter( 'wp_nav_menu', array( __CLASS__, '_remove_menu_item_title_filter' ) );
	}

	/**
	 * Get nav menu ID based on arguments passed to wp_nav_menu()
	 *
	 */
	public static function get_nav_menu_id( $args ) {
		$args = (object) $args;
		$menu = wp_get_nav_menu_object( $args->menu );

		// Get the nav menu based on the theme_location
		if ( ! $menu
			&& $args->theme_location
			&& ( $locations = get_nav_menu_locations() )
			&& isset( $locations[ $args->theme_location ] )
		) {
			$menu = wp_get_nav_menu_object( $locations[ $args->theme_location ] );
		}

		// get the first menu that has items if we still can't find a menu
		if ( ! $menu && ! $args->theme_location ) {
			$menus = wp_get_nav_menus();
			foreach ( $menus as $menu_maybe ) {
				if ( $menu_items = wp_get_nav_menu_items( $menu_maybe->term_id, array( 'update_post_term_cache' => false ) ) ) {
					$menu = $menu_maybe;
					break;
				}
			}
		}

		if ( is_object( $menu ) && ! is_wp_error( $menu ) ) {
			return $menu->term_id;
		} else {
			return false;
		}
	}

	/**
	 * Enqueue stylesheets
	 *
	 */
	public static function _enqueue_styles() {
		foreach ( self::$icon_types as $type ) {
			if ( wp_style_is( $type->stylesheet_id, 'registered' )
				&& ( 'font-awesome' != $type->stylesheet_id
					&& 'simple-line-icons' != $type->stylesheet_id ) ) {
				wp_enqueue_style( $type->stylesheet_id );
			}
		}
	}

	/**
	 * Add filter to 'the_title' hook
	 *
	 * We need to filter the menu item title but **not** regular post titles.
	 * Thus, we're adding the filter when `wp_nav_menu()` is called.
	 *
	 */
	public static function _add_menu_item_title_filter( $args ) {

		$args['fallback_cb'] = array( __CLASS__, '_fallback_cb' );

		add_filter( 'the_title', array( __CLASS__, '_add_icon' ), self::$priority, 2 );

		self::$priorities_used[] = self::$priority;

		self::$priority++;

		return $args;
	}

	public static function _fallback_cb( $args ) {

		$priority = array_pop(self::$priorities_used);

		remove_filter( 'the_title', array( __CLASS__, '_add_icon' ), $priority, 2 );

		return wp_page_menu( $args );
	}

	public static function _remove_menu_item_title_empty_items_filter( $items, $args ) {
		if ( empty( $items ) ) {
			$priority = array_pop(self::$priorities_used);

			remove_filter( 'the_title', array( __CLASS__, '_add_icon' ), $priority, 2 );
		}
		return $items;
	}

	/**
	 * Remove filter from 'the_title' hook
	 *
	 * Because we don't want to filter post titles, we need to remove our
	 * filter when `wp_nav_menu()` exits.
	 *
	 */
	public static function _remove_menu_item_title_filter( $nav_menu ) {

		$priority = array_pop(self::$priorities_used);

		remove_filter( 'the_title', array( __CLASS__, '_add_icon' ), $priority, 2 );

		return $nav_menu;
	}

	/**
	 * Add icon to menu item title
	 *
	 */
	public static function _add_icon( $title, $id ) {
		$meta = OE_Menu_Icons_Meta::get( $id );
		$icon = self::get_icon( $meta );

		if ( empty( $icon ) ) {
			return $title;
		}

		$title_class   = ! empty( $meta['hide_label'] ) ? self::$hidden_label_class : '';
		$title_wrapped = sprintf(
			'<span class="menu-text%s">%s</span>',
			( ! empty( $title_class ) ) ? sprintf( ' %s', esc_attr( $title_class ) ) : '',
			$title
		);

		if ( 'below' === $meta['position'] ) {
			$icon_wrap = sprintf(
				'<span class="icon-wrap">%s%s</span>',
				$icon,
				$title_wrapped
			);
		}

		if ( 'below' === $meta['position'] ) {
			$title_with_icon = "{$icon_wrap}";
		} else if ( 'after' === $meta['position'] ) {
			$title_with_icon = "{$title_wrapped}{$icon}";
		} else {
			$title_with_icon = "{$icon}{$title_wrapped}";
		}

		/**
		 * Add filter to allow to override menu item markup
		 */
		$title_with_icon = apply_filters( 'oe_menu_icons_item_title', $title_with_icon, $id, $meta, $title );

		return $title_with_icon;
	}

	/**
	 * Get icon
	 *
	 */
	public static function get_icon( $meta ) {
		$icon = '';

		// Icon type is not set.
		if ( empty( $meta['type'] ) ) {
			return $icon;
		}

		// Icon is not set.
		if ( empty( $meta['icon'] ) ) {
			return $icon;
		}

		// Icon type is not registered/enabled.
		if ( ! isset( self::$icon_types[ $meta['type'] ] ) ) {
			return $icon;
		}

		$type = self::$icon_types[ $meta['type'] ];

		$callbacks = array(
			array( $type, 'get_icon' ),
			array( __CLASS__, "get_{$type->id}_icon" ),
			array( __CLASS__, "get_{$type->template_id}_icon" ),
		);

		foreach ( $callbacks as $callback ) {
			if ( is_callable( $callback ) ) {
				$icon = call_user_func( $callback, $meta );
				break;
			}
		}

		return $icon;
	}

	/**
	 * Get icon style
	 *
	 */
	public static function get_icon_style( $meta, $keys, $as_attribute = true ) {
		$style_a = array();
		$style_s = '';

		foreach ( $keys as $key ) {
			if ( ! isset( self::$default_style[ $key ] ) ) {
				continue;
			}

			$rule = self::$default_style[ $key ];

			if ( ! isset( $meta[ $key ] ) || $meta[ $key ] === $rule['value'] ) {
				continue;
			}

			$value = $meta[ $key ];
			if ( ! empty( $rule['unit'] ) ) {
				$value .= $rule['unit'];
			}

			$style_a[ $rule['property'] ] = $value;
		}

		if ( empty( $style_a ) ) {
			return $style_s;
		}

		foreach ( $style_a as $key => $value ) {
			$style_s .= "{$key}:{$value};";
		}

		$style_s = esc_attr( $style_s );

		if ( $as_attribute  ) {
			$style_s = sprintf( ' style="%s"', $style_s );
		}

		return $style_s;
	}

	/**
	 * Get icon classes
	 *
	 */
	public static function get_icon_classes( $meta, $output = 'string' ) {
		$classes = array( 'icon' );

		if ( empty( $meta['hide_label'] ) ) {
			$classes[] = "{$meta['position']}";
		}

		if ( 'string' === $output ) {
			$classes = implode( ' ', $classes );
		}

		return $classes;
	}

	/**
	 * Get font icon
	 *
	 */
	public static function get_font_icon( $meta ) {
		$classes = sprintf( '%s %s %s', self::get_icon_classes( $meta ), $meta['type'], $meta['icon'] );
		$style   = self::get_icon_style( $meta, array( 'font_size', 'vertical_align' ) );

		return sprintf( '<i class="%s" aria-hidden="true"%s></i>', esc_attr( $classes ), $style );
	}

	/**
	 * Get image icon
	 *
	 */
	public static function get_image_icon( $meta ) {
		$args = array(
			'class'       => sprintf( '%s _image', self::get_icon_classes( $meta ) ),
			'aria-hidden' => 'true',
		);

		$style = self::get_icon_style( $meta, array( 'vertical_align' ), false );
		if ( ! empty( $style ) ) {
			$args['style'] = $style;
		}

		return wp_get_attachment_image( $meta['icon'], $meta['image_size'], false, $args );
	}

	/**
	 * Get SVG icon
	 *
	 */
	public static function get_svg_icon( $meta ) {
		$classes = sprintf( '%s svg', self::get_icon_classes( $meta ) );
		$style   = self::get_icon_style( $meta, array( 'svg_width', 'vertical_align' ) );

		return sprintf(
			'<img src="%s" class="%s" aria-hidden="true"%s />',
			esc_url( wp_get_attachment_url( $meta['icon'] ) ),
			esc_attr( $classes ),
			$style
		);
	}
}