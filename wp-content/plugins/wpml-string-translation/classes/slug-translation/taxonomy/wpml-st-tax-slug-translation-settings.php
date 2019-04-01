<?php

class WPML_ST_Tax_Slug_Translation_Settings extends WPML_ST_Slug_Translation_Settings {

	const OPTION_NAME = "wpml_tax_slug_translation_settings";

	/** @var array $types */
	private $types = array();

	public function __construct() {
		$this->init();
	}

	/** @param array $types */
	public function set_types( array $types ) {
		$this->types = $types;
	}

	/** @return array */
	public function get_types() {
		return $this->types;
	}

	/**
	 * @param string $taxonomy_name
	 *
	 * @return bool
	 */
	public function is_translated( $taxonomy_name ) {
		return array_key_exists( $taxonomy_name, $this->types ) && (bool) $this->types[ $taxonomy_name ];
	}

	/**
	 * @param string $taxonomy_name
	 * @param bool   $is_enabled
	 */
	public function set_type( $taxonomy_name, $is_enabled ) {
		$this->types[ $taxonomy_name ] = (int) $is_enabled;
	}

	/** @return array */
	private function get_properties() {
		return get_object_vars( $this );
	}

	public function init() {
		$options = get_option( self::OPTION_NAME, array() );

		foreach ( $this->get_properties() as $name => $value ) {
			if ( array_key_exists( $name, $options ) ) {
				call_user_func( array( $this, 'set_' . $name ), $options[ $name ] );
			}
		}
	}

	public function save() {
		update_option( self::OPTION_NAME, $this->get_properties() );
	}
}
