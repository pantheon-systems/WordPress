<?php

/**
 * Class WPML_Beaver_Builder_Register_Strings
 */
class WPML_Beaver_Builder_Register_Strings extends WPML_Page_Builders_Register_Strings {

	/**
	 * @param array $data_array
	 * @param array $package
	 */
	protected function register_strings_for_modules( array $data_array, array $package ) {
		foreach ( $data_array as $data ) {
			if ( is_array( $data ) ) {
				$this->register_strings_for_modules( $data, $package );
			} elseif ( is_object( $data ) ) {
				if ( isset( $data->type ) && 'module' === $data->type ) {
					$this->register_strings_for_node( $data->node, $data->settings, $package );
				}
			}
		}
	}
}