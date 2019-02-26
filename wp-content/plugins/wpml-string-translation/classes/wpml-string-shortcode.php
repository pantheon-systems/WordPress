<?php

class WPML_String_Shortcode {
	private $context;
	private $name;

	public function __construct( wpdb $wpdb ) {
		$this->wpdb = $wpdb;
	}

	function init_hooks() {
		add_shortcode( 'wpml-string', array( $this, 'shortcode' ) );
	}

	/**
	 * @param array  $attributes
	 * @param string $value
	 *
	 * @return string
	 */
	function shortcode( $attributes, $value ) {
		$this->parse_attributes( $attributes, $value );
		$this->maybe_register_string( $value );

		return do_shortcode( icl_t( $this->context, $this->name, $value ) );
	}

	/**
	 * @param string $value
	 */
	private function maybe_register_string( $value ) {
		$string = $this->get_registered_string();
		if ( ! $string || $string->value !== $value ) {
			icl_register_string( $this->context, $this->name, $value );
		}
	}

	/**
	 * @param array  $attributes
	 * @param string $value
	 */
	private function parse_attributes( $attributes, $value ) {
		$pairs = array(
			'context' => 'wpml-shortcode',
			'name'    => 'wpml-shortcode-' . md5( $value ),
		);

		$attributes = shortcode_atts( $pairs, $attributes );

		$this->context = $attributes['context'];
		$this->name    = $attributes['name'];
	}

	/**
	 * @return stdClass
	 */
	private function get_registered_string() {
		$strings = $this->get_strings_registered_in_context();
		if ( $strings && array_key_exists( $this->name , $strings ) ) {
			return $strings[ $this->name ];
		}

		return null;
	}

	/**
	 * @return stdClass[]
	 */
	private function get_strings_registered_in_context() {
		$cache_key   = $this->context;
		$cache_group = 'wpml-string-shortcode';

		$cache_found = false;
		$string      = wp_cache_get( $cache_key, $cache_group, false, $cache_found );
		if ( ! $cache_found ) {
			$query  = 'SELECT name, id, value, status FROM ' . $this->wpdb->prefix . 'icl_strings WHERE context=%s';
			$sql    = $this->wpdb->prepare( $query, $this->context );
			$string = $this->wpdb->get_results( $sql, OBJECT_K );
			wp_cache_set( $cache_key, $string, $cache_group );
		}

		return $string;
	}
}
