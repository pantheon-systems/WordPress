<?php

class WPML_Beaver_Builder_Data_Settings implements IWPML_Page_Builders_Data_Settings {

	/**
	 * @return string
	 */
	public function get_meta_field() {
		return '_fl_builder_data';
	}

	/**
	 * @return string
	 */
	public function get_node_id_field() {
		return 'node';
	}

	/**
	 * @return array
	 */
	public function get_fields_to_copy() {
		return array( '_fl_builder_draft_settings', '_fl_builder_data_settings', '_fl_builder_enabled' );
	}

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function convert_data_to_array( $data ) {
		return $data;
	}

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function prepare_data_for_saving( array $data ) {
		return $data;
	}

	/**
	 * @return string
	 */
	public function get_pb_name(){
		return 'Beaver builder';
	}

	/**
	 * @return array
	 */
	public function get_fields_to_save() {
		return array( '_fl_builder_data', '_fl_builder_draft' );
	}

	public function add_hooks(){}
}