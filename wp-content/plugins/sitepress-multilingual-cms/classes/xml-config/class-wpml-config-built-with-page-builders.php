<?php

class WPML_Config_Built_With_Page_Builders extends WPML_WP_Option implements IWPML_Action, IWPML_Backend_Action_Loader, IWPML_AJAX_Action_Loader  {

	const CONFIG_KEY = 'built-with-page-builder';

	public function create() {
		return $this; // Use same instance for action
	}

	public function get_key() {
		return 'wpml_built_with_page_builder';
	}

	public function get_default() {
		return array();
	}

	public function add_hooks() {
		add_filter( 'wpml_config_array', array( $this, 'wpml_config_filter' ) );
	}

	/**
	 * @param array $config_data
	 *
	 * @return array
	 */
	public function wpml_config_filter( $config_data ) {
		if ( isset( $config_data['wpml-config'][ self::CONFIG_KEY ] ) && $config_data['wpml-config'][ self::CONFIG_KEY ] ) {
			$data_saved = $this->get();
			$data_saved = $data_saved ? $data_saved : array();

			$data_saved[] = $config_data['wpml-config'][ self::CONFIG_KEY ];
			$this->set( array_unique( $data_saved ) );
		}

		return $config_data;
	}
}