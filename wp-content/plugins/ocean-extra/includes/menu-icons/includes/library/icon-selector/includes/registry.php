<?php
/**
 * Icon types registry
 *
 */

final class OE_Icon_Picker_Types_Registry {

	/**
	 * OE_Icon_Picker_Types_Registry singleton
	 *
	 */
	protected static $instance;

	/**
	 * Base icon type class name
	 *
	 */
	protected $base_class = 'OE_Icon_Picker_Type';

	/**
	 * All types
	 *
	 */
	protected $types = array();

	/**
	 * Get instance
	 *
	 */
	public static function instance( $args = array() ) {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self( $args );
		}

		return self::$instance;
	}

	/**
	 * Getter magic
	 *
	 */
	public function __get( $name ) {
		if ( isset( $this->$name ) ) {
			return $this->$name;
		}

		return null;
	}

	/**
	 * Setter magic
	 *
	 */
	public function __isset( $name ) {
		return isset( $this->$name );
	}

	/**
	 * Constructor
	 *
	 */
	protected function __construct() {
		/**
		 * Fires when Icon Picker types registry is ready
		 *
		 */
		do_action( 'oe_icon_picker_types_registry_init', $this );
	}

	/**
	 * Register icon type
	 *
	 */
	public function add( OE_Icon_Picker_Type $type ) {
		if ( $this->is_valid_type( $type ) ) {
			$this->types[ $type->id ] = $type;
		}
	}

	/**
	 * Get icon type
	 *
	 */
	public function get( $id ) {
		if ( isset( $this->types[ $id ] ) ) {
			return $this->types[ $id ];
		}

		return null;
	}

	/**
	 * Check if icon type is valid
	 *
	 */
	protected function is_valid_type( OE_Icon_Picker_Type $type ) {
		foreach ( array( 'id', 'controller' ) as $var ) {
			$value = $type->$var;

			if ( empty( $value ) ) {
				trigger_error( esc_html( sprintf( 'Icon Picker: "%s" cannot be empty.', $var ) ) );
				return false;
			}
		}

		if ( isset( $this->types[ $type->id ] ) ) {
			trigger_error( esc_html( sprintf( 'Icon Picker: Icon type %s is already registered. Please use a different ID.', $type->id ) ) );
			return false;
		}

		return true;
	}

	/**
	 * Get all icon types for JS
	 *
	 */
	public function get_types_for_js() {
		$types = array();
		$names = array();

		foreach ( $this->types as $type ) {
			$types[ $type->id ] = $type->get_props();
			$names[ $type->id ] = $type->name;
		}

		array_multisort( $names, SORT_ASC, $types );

		return $types;
	}
}
