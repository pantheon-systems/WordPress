<?php

/**
 * Class WPML_Page_Builders_Defined
 */
class WPML_Page_Builders_Defined {

	private $settings;

	public function __construct() {
		$this->init_settings();
	}

	public function has( $page_builder ) {
		global $wp_version;
		if ( 'gutenberg' === $page_builder ) {
			if ( version_compare( $wp_version, '5.0-beta1', '>=' ) ) {
				return true;
			}
		}

		return defined( $this->settings[ $page_builder ]['constant'] );
	}

	/**
	 * @param array $components
	 *
	 * @return array
	 */
	public function add_components( $components ) {
		if ( isset( $components['page-builders'] ) ) {
			foreach (
				array(
					'beaver-builder' => 'Beaver Builder',
					'elementor'      => 'Elementor',
					'gutenberg'      => 'Gutenberg',
				) as $key => $name
			) {
				$components['page-builders'][ $key ] = array(
					'name'            => $name,
					'constant'        => $this->settings[ $key ]['constant'],
					'notices-display' => array(
						'wpml-translation-editor',
					),
				);
			}
		}

		return $components;
	}

	public function init_settings() {
		$this->settings = array(
			'beaver-builder' => array(
				'constant' => 'FL_BUILDER_VERSION',
				'factory' => 'WPML_Beaver_Builder_Integration_Factory',
			),
			'elementor' => array(
				'constant' => 'ELEMENTOR_VERSION',
				'factory' => 'WPML_Elementor_Integration_Factory',
			),
			'gutenberg' => array(
				'constant' => 'GUTENBERG_VERSION',
				'factory' => 'WPML_Gutenberg_Integration_Factory',
			),
		);
	}

	/**
	 * @return array
	 */
	public function get_settings() {
		return $this->settings;
	}

}