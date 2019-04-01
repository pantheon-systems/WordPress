<?php

class WPML_Page_Builders_Update {

	/** @var IWPML_Page_Builders_Data_Settings */
	protected $data_settings;

	public function __construct( IWPML_Page_Builders_Data_Settings $data_settings ) {
		$this->data_settings = $data_settings;
	}

	/**
	 * @param int $post_id
	 *
	 * @return array
	 */
	public function get_converted_data( $post_id ) {
		$data = get_post_meta( $post_id, $this->data_settings->get_meta_field(), true );
		return $this->data_settings->convert_data_to_array( $data );
	}

	/**
	 * @param int   $post_id
	 * @param int   $original_post_id
	 * @param array $converted_data
	 */
	public function save( $post_id, $original_post_id, $converted_data ) {
		$this->save_data( $post_id, $this->data_settings->get_fields_to_save(), $this->data_settings->prepare_data_for_saving( $converted_data ) );
		$this->copy_meta_fields( $post_id, $original_post_id, $this->data_settings->get_fields_to_copy() );
	}

	/**
	 * @param int   $post_id
	 * @param array $fields
	 * @param mixed $data
	 */
	private function save_data( $post_id, $fields, $data ) {
		foreach ( $fields as $field ) {
			update_post_meta( $post_id, $field, $data );
		}
	}

	/**
	 * @param int   $translated_post_id
	 * @param int   $original_post_id
	 * @param array $meta_fields
	 */
	private function copy_meta_fields( $translated_post_id, $original_post_id, $meta_fields ) {
		foreach ( $meta_fields as $meta_key ) {
			$value = get_post_meta( $original_post_id, $meta_key, true );

			update_post_meta(
				$translated_post_id,
				$meta_key,
				apply_filters( 'wpml_pb_copy_meta_field', $value, $translated_post_id, $original_post_id, $meta_key )
			);
		}
	}
}
