<?php

/**
 * Class WPML_Elementor_Register_Strings
 */
class WPML_Elementor_Register_Strings extends WPML_Page_Builders_Register_Strings {

	/**
	 * @param array $data_array
	 * @param array $package
	 */
	protected function register_strings_for_modules( array $data_array, array $package ) {
		foreach ( $data_array as $data ) {
			if ( isset( $data['elType'] ) && 'widget' === $data['elType'] ) {
				$this->register_strings_for_node( $data[ $this->data_settings->get_node_id_field() ], $data, $package );
			}
			foreach ( $data[ 'elements' ] as $column ) {
				foreach ( $column[ 'elements' ] as $element ) {
					if ( 'widget' === $element['elType'] ) {
						$this->register_strings_for_node( $element[ $this->data_settings->get_node_id_field() ], $element, $package );
					} else {
						$this->register_strings_for_modules( array( $element ), $package );
					}
				}
			}
		}
	}
}