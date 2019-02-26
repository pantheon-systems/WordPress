<?php

class WPML_Config_Shortcode_List extends WPML_WP_Option implements IWPML_Action, IWPML_Backend_Action_Loader, IWPML_AJAX_Action_Loader {

	public function create() {
		return $this; // Use same instance for action
	}

	public function get_key() {
		return 'wpml_shortcode_list';
	}

	public function get_default() {
		return array();
	}

	public function add_hooks() {
		add_filter( 'wpml_config_array', array( $this, 'wpml_config_filter' ) );
		add_filter( 'wpml_shortcode_list', array( $this, 'filter_shortcode_list' ) );
	}

	public function wpml_config_filter( $config_data ) {
		$shortcode_data = array();
		if ( isset( $config_data['wpml-config']['shortcode-list'] ) ) {
			$shortcode_data = $config_data['wpml-config']['shortcode-list'];
		}

		$this->set( $shortcode_data );

		return $config_data;
	}

	public function filter_shortcode_list( $shortcodes ) {
		return array_unique( array_merge( $shortcodes, $this->get() ) );
	}
}
