<?php

/**
 * Interface IWPML_Page_Builders_Data_Settings
 */
interface IWPML_Page_Builders_Data_Settings {

	/**
	 * @return string
	 */
	public function get_meta_field();

	/**
	 * @return string
	 */
	public function get_node_id_field();

	/**
	 * @return array
	 */
	public function get_fields_to_copy();

	/**
	 * @return array
	 */
	public function get_fields_to_save();

	/**
	 * @param mixed $data
	 *
	 * @return array
	 */
	public function convert_data_to_array( $data );

	/**
	 * @param array $data
	 *
	 * @return mixed
	 */
	public function prepare_data_for_saving( array $data );

	/**
	 * @return string
	 */
	public function get_pb_name();

	public function add_hooks();
}