<?php

/**
 * @author OnTheGo Systems
 */
class WPML_Admin_Menu_Item {
	private $capability;
	private $function;
	private $menu_slug;
	private $menu_title;
	private $order;
	private $page_title;
	private $parent_slug;

	/**
	 * WPML_Menu_Item constructor.
	 *
	 * @param array $args
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct( array $args = null ) {
		if ( $args ) {
			$required_fields = array(
				'capability',
				'menu_slug',
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
				'order',
				'parent_slug',
			);

			$fields = array_merge( $required_fields, $fields );

			foreach ( $fields as $field ) {
				if ( array_key_exists( $field, $args ) ) {
					$this->{$field} = $args[ $field ];
				}
			}
		}
	}

	/**
	 * Required by `usort` to remove duplicates, as casts array elements to string
	 * @return string
	 */
	public function __toString() {
		return $this->serialize();
	}

	public function build( $root_slug ) {
		$parent = $root_slug;
		if ( $this->get_parent_slug() ) {
			$parent = $this->get_parent_slug();
		}

		add_submenu_page( $parent,
		                  $this->get_page_title(),
		                  $this->get_menu_title(),
		                  $this->get_capability(),
		                  $this->get_menu_slug(),
		                  $this->get_function() );
	}

	/**
	 * @return mixed
	 */
	public function get_parent_slug() {
		return $this->parent_slug;
	}

	/**
	 * @param mixed $parent_slug
	 */
	public function set_parent_slug( $parent_slug ) {
		$this->parent_slug = $parent_slug;
	}

	/**
	 * @return mixed
	 */
	public function get_page_title() {
		return $this->page_title;
	}

	/**
	 * @param mixed $page_title
	 */
	public function set_page_title( $page_title ) {
		$this->page_title = $page_title;
	}

	/**
	 * @return mixed
	 */
	public function get_menu_title() {
		return $this->menu_title;
	}

	/**
	 * @param mixed $menu_title
	 */
	public function set_menu_title( $menu_title ) {
		$this->menu_title = $menu_title;
	}

	/**
	 * @return mixed
	 */
	public function get_capability() {
		return $this->capability;
	}

	/**
	 * @param mixed $capability
	 */
	public function set_capability( $capability ) {
		$this->capability = $capability;
	}

	/**
	 * @return mixed
	 */
	public function get_menu_slug() {
		return $this->menu_slug;
	}

	/**
	 * @param mixed $menu_slug
	 */
	public function set_menu_slug( $menu_slug ) {
		$this->menu_slug = $menu_slug;
	}

	/**
	 * @return mixed
	 */
	public function get_function() {
		return $this->function;
	}

	/**
	 * @param mixed $function
	 */
	public function set_function( $function ) {
		$this->function = $function;
	}

	/**
	 * @return mixed
	 */
	public function get_order() {
		return $this->order;
	}

	/**
	 * @param mixed $order
	 */
	public function set_order( $order ) {
		$this->order = $order;
	}

	/**
	 * @return string
	 */
	private function serialize() {
		$function = $this->get_function();
		if ( is_callable( $function ) ) {
			/**
			 * "Hash" is for the hash table. That's not an actual hash of the callable, but it should
			 * be good enough for the scope of this function
			 */
			$function = spl_object_hash( (object) $function );
		}

		return wp_json_encode( array(
			'capability'  => $this->get_capability(),
			'function'    => $function,
			'menu_slug'   => $this->get_menu_slug(),
			'menu_title'  => $this->get_menu_title(),
			'order'       => $this->get_order(),
			'page_title'  => $this->get_page_title(),
			'parent_slug' => $this->get_parent_slug(),
		), 0, 1 );
	}
}