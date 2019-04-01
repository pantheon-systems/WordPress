<?php

class WPML_Admin_Menu_Root {
	private $capability;
	private $function;
	private $icon_url;
	private $items = array();
	private $menu_id;
	private $menu_title;
	private $page_title;
	private $position;

	/**
	 * WPML_Menu_Root constructor.
	 *
	 * @param array|null $args
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct( array $args = null ) {
		if ( $args ) {
			$required_fields = array(
				'capability',
				'menu_id',
				'menu_title',
				'page_title',
			);

			foreach ( $required_fields as $required_field ) {
				if ( ! array_key_exists( $required_field, $args ) ) {
					throw new InvalidArgumentException( $required_field . ' is missing.' );
				}
			}

			$fields = array(
				'function',
				'icon_url',
				'position',
			);

			$fields = array_merge( $required_fields, $fields );

			foreach ( $fields as $field ) {
				if ( array_key_exists( $field, $args ) ) {
					$this->{$field} = $args[ $field ];
				}
			}
		}
	}

	public function build() {
		do_action( 'wpml_admin_menu_configure', $this->get_menu_id() );

		$this->adjust_items();

		$root_slug = $this->get_menu_slug();

		add_menu_page( $this->get_page_title(),
		               $this->get_menu_title(),
		               $this->get_capability(),
		               $root_slug,
		               $this->get_function(),
		               $this->get_icon_url(),
		               $this->get_position() );

		do_action( 'wpml_admin_menu_root_configured', $this->get_menu_id(), $root_slug );

		/** @var WPML_Admin_Menu_Item $menu_item */
		foreach ( $this->items as $menu_item ) {
			$menu_item = apply_filters( 'wpml_menu_item_before_build', $menu_item, $root_slug );
			$menu_item->build( $root_slug );
		}
	}

	private function adjust_items() {
		if ( $this->items && count( $this->items ) > 1 ) {
			$menu_items = $this->items;
			$menu_items = array_unique( $menu_items );
			$menu_items = array_map( array( $this, 'menu_order_fixer' ), $menu_items );
			/**
			 * Error suppression is required because of https://bugs.php.net/bug.php?id=50688
			 * which makes PHPUnit (or every case where xDebug is involved) to cause a
			 * `PHP Warning: usort(): Array was modified by the user comparison function`
			 */
			@usort( $menu_items, array( $this, 'menu_order_sorter' ) );
			$this->items = $menu_items;
		}
	}

	/**
	 * @return string
	 */
	public function get_menu_slug() {
		$top_menu = null;

		if ( $this->items ) {
			$this->adjust_items();
			$top_menu = $this->items[0]->get_menu_slug();
		}

		return $top_menu;
	}

	/**
	 * @return string
	 */
	public function get_page_title() {
		return $this->page_title;
	}

	/**
	 * @param string $page_title
	 */
	public function set_page_title( $page_title ) {
		$this->page_title = $page_title;
	}

	/**
	 * @return string
	 */
	public function get_menu_id() {
		return $this->menu_id;
	}

	/**
	 * @return string
	 */
	public function get_menu_title() {
		return $this->menu_title;
	}

	/**
	 * @param string $menu_title
	 */
	public function set_menu_title( $menu_title ) {
		$this->menu_title = $menu_title;
	}

	/**
	 * @return string
	 */
	public function get_capability() {
		return $this->capability;
	}

	/**
	 * @param string $capability
	 */
	public function set_capability( $capability ) {
		$this->capability = $capability;
	}

	/**
	 * @return null|callable
	 */
	public function get_function() {
		return $this->function;
	}

	/**
	 * @param null|callable $function
	 */
	public function set_function( $function ) {
		$this->function = $function;
	}

	/**
	 * @return string
	 */
	public function get_icon_url() {
		return $this->icon_url;
	}

	/**
	 * @param string $icon_url
	 */
	public function set_icon_url( $icon_url ) {
		$this->icon_url = $icon_url;
	}

	/**
	 * @return array
	 */
	public function get_items() {
		return $this->items;
	}

	/**
	 * @return int
	 */
	public function get_position() {
		return $this->position;
	}

	/**
	 * @param int $position
	 */
	public function set_position( $position ) {
		$this->position = $position;
	}

	public function init_hooks() {
		add_action( 'wpml_admin_menu_register_item', array( $this, 'register_menu_item' ) );
		add_action( 'admin_menu', array( $this, 'build' ) );
	}

	/**
	 * @param WPML_Admin_Menu_Item $item
	 *
	 * @return WPML_Admin_Menu_Item
	 */
	public function menu_order_fixer( WPML_Admin_Menu_Item $item ) {
		static $last_order = WPML_Main_Admin_Menu::MENU_ORDER_MAX;
		if ( $item->get_order() === null ) {
			$item->set_order( $last_order+1 );
		}
		if ( $last_order < PHP_INT_MAX ) {
			$last_order = $item->get_order();
		}

		return $item;
	}

	/**
	 * @param WPML_Admin_Menu_Item $a
	 * @param WPML_Admin_Menu_Item $b
	 *
	 * @return int
	 */
	public function menu_order_sorter( WPML_Admin_Menu_Item $a, WPML_Admin_Menu_Item $b ) {
		$order_a = $a->get_order() === null ? 0 : $a->get_order();
		$order_b = $b->get_order() === null ? 0 : $b->get_order();

		if ( $order_a < $order_b ) {
			return - 1;
		}
		if ( $order_a === $order_b ) {
			return 0;
		}

		return 1;
	}

	/**
	 * @param WPML_Admin_Menu_Item|array $item
	 *
	 * @throws \InvalidArgumentException
	 */
	public function register_menu_item( $item ) {
		if ( is_array( $item ) ) {
			$item = new WPML_Admin_Menu_Item( $item );
		}
		$this->add_item( $item );
	}

	public function add_item( WPML_Admin_Menu_Item $item ) {
		$this->items[] = $item;

		return $this;
	}

}