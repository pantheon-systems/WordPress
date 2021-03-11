<?php
/**
 * Icon type handler
 *
 */

/**
 * Base icon type class
 */
class OE_Icon_Picker_Type {

	/**
	 * Icon type ID
	 *
	 */
	protected $id = '';

	/**
	 * Icon type name
	 *
	 */
	protected $name = '';

	/**
	 * Icon type version
	 *
	 */
	protected $version = '';

	/**
	 * JS Controller
	 *
	 */
	protected $controller = '';

	/**
	 * Constructor
	 *
	 */
	public function __construct( $args = array() ) {
		$keys = array_keys( get_object_vars( $this ) );

		foreach ( $keys as $key ) {
			if ( isset( $args[ $key ] ) ) {
				$this->$key = $args[ $key ];
			}
		}
	}

	/**
	 * Getter magic
	 *
	 */
	public function __get( $name ) {
		$vars = get_object_vars( $this );
		if ( isset( $vars[ $name ] ) ) {
			return $vars[ $name ];
		}

		$method = "get_{$name}";
		if ( method_exists( $this, $method ) ) {
			return call_user_func( array( $this, $method ) );
		}

		return null;
	}

	/**
	 *
	 */
	public function __isset( $name ) {
		return ( isset( $this->$name ) || method_exists( $this, "get_{$name}" ) );
	}

	/**
	 * Get extra properties data
	 *
	 */
	protected function get_props_data() {
		return array();
	}

	/**
	 * Get properties
	 *
	 */
	public function get_props() {
		$props = array(
			'id'         => $this->id,
			'name'       => $this->name,
			'controller' => $this->controller,
			'templateId' => $this->template_id,
			'data'       => $this->get_props_data(),
		);

		/**
		 * Filter icon type properties
		 *
		 */
		$props = apply_filters( 'oe_icon_picker_type_props', $props, $this->id, $this );

		/**
		 * Filter icon type properties
		 *
		 */
		$props = apply_filters( "oe_icon_picker_type_props_{$this->id}", $props, $this );

		return $props;
	}
}