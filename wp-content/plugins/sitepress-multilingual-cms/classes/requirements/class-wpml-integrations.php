<?php

/**
 * @author OnTheGo Systems
 */
class WPML_Integrations {
	const SCOPE_WP_CORE = 'wp-core';

	private $components = array(
		self::SCOPE_WP_CORE => array(
			'block-editor' => array(
				'name'            => 'WordPress Block Editor',
				'function'        => 'parse_blocks',
				'notices-display' => array(),
			),
		),
		'page-builders' => array(
			'js_composer'    => array(
				'name'     => 'Visual Composer',
				'constant' => 'WPB_VC_VERSION',
				'notices-display' => array(
					'wpml-translation-editor',
				),
			),
			'divi'           => array(
				'name'     => 'Divi',
				'constant' => 'ET_BUILDER_DIR',
				'notices-display' => array(
					'wpml-translation-editor',
				),
			),
			'layouts'        => array(
				'name'     => 'Toolset Layouts',
				'constant' => 'WPDDL_VERSION',
				'notices-display' => array(
					'wpml-translation-editor',
				),
			),
			'x-theme'        => array(
				'name'     => 'X Theme',
				'constant' => 'X_VERSION',
				'notices-display' => array(
					'wpml-translation-editor',
				),
			),
			'enfold'         => array(
				'name'     => 'Enfold',
				'constant' => 'AVIA_FW',
				'notices-display' => array(
					'wpml-translation-editor',
				),
			),
			'avada'          => array(
				'name'     => 'Avada',
				'function' => 'Avada',
				'notices-display' => array(
					'wpml-translation-editor',
				),
			),
			'oxygen'    => array(
				'name'     => 'Oxygen',
				'constant' => 'CT_VERSION',
				'notices-display' => array(
					'wpml-translation-editor',
				),
			),
		),
		'integrations'  => array(
			'woocommerce'  => array(
				'name'  => 'WooCommerce',
				'class' => 'WooCommerce',
				'notices-display' => array(),
			),
			'gravityforms' => array(
				'name'  => 'Gravity Forms',
				'class' => 'GFForms',
				'notices-display' => array(),
			),
			'buddypress'   => array(
				'name'  => 'BuddyPress',
				'class' => 'BuddyPress',
				'notices-display' => array(
					'wpml-translation-editor',
				),
			),
			'bb-plugin'   => array(
				'name'  => 'Beaver Builder Plugin',
				'class' => 'FLBuilderLoader',
				'notices-display' => array(
					'wpml-translation-editor',
				),
			),
			'elementor-plugin'   => array(
				'name'  => 'Elementor',
				'class' => '\Elementor\Plugin',
				'notices-display' => array(
					'wpml-translation-editor',
				),
			),
		),
	);
	private $items = array();
	private $wpml_wp_api;

	/**
	 * WPML_Integrations constructor.
	 *
	 * @param WPML_WP_API $wpml_wp_api
	 */
	function __construct( WPML_WP_API $wpml_wp_api ) {
		$this->wpml_wp_api         = $wpml_wp_api;
		$this->fetch_items();
	}

	private function fetch_items() {
		foreach ( $this->get_components() as $type => $components ) {
			foreach ( (array) $components as $slug => $data ) {
				if ( $this->component_has_constant( $data ) || $this->component_has_function( $data ) || $this->component_has_class( $data ) ) {
					$this->items[ $slug ]                    = array( 'name' => $this->get_component_name( $data ) );
					$this->items[ $slug ]['type']            = $type;
					$this->items[ $slug ]['notices-display'] = isset( $data['notices-display'] ) ? $data['notices-display'] : array();
				}
			}
		}
	}

	public function get_results() {
		return $this->items;
	}

	/**
	 * @param array $data
	 *
	 * @return bool
	 */
	private function component_has_constant( array $data ) {
		return array_key_exists( 'constant', $data ) && $data['constant'] && $this->wpml_wp_api->defined( $data['constant'] );
	}

	/**
	 * @param array $data
	 *
	 * @return bool
	 */
	private function component_has_function( array $data ) {
		return array_key_exists( 'function', $data ) && $data['function'] && function_exists( $data['function'] );
	}

	/**
	 * @param array $data
	 *
	 * @return bool
	 */
	private function component_has_class( array $data ) {
		return array_key_exists( 'class', $data ) && $data['class'] && class_exists( $data['class'] );
	}

	/**
	 * @param array $data
	 *
	 * @return mixed
	 */
	private function get_component_name( array $data ) {
		return $data['name'];
	}

	/**
	 * @return array
	 */
	private function get_components() {
		return apply_filters( 'wpml_integrations_components', $this->components );
	}
}
