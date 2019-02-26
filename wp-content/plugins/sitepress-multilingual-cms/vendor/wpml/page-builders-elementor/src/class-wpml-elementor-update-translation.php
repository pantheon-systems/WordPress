<?php

class WPML_Elementor_Update_Translation extends WPML_Page_Builders_Update_Translation {

	/** @param array $data_array */
	protected function update_strings_in_modules( array &$data_array ) {
		foreach ( $data_array as &$element ) {
			if ( $element['elements'] ) {
				$this->update_strings_in_modules( $element['elements'] );
			} else if ( 'widget' === $element['elType'] ) {
				$element = $this->update_strings_in_node( $element[ $this->data_settings->get_node_id_field() ], $element );
			}
		}
	}

	/**
	 * @param int $node_id
	 * @param array $settings
	 *
	 * @return array
	 */
	protected function update_strings_in_node( $node_id, $settings ) {
		$strings = $this->translatable_nodes->get( $node_id, $settings );
		foreach ( $strings as $string ) {
			$translation = $this->get_translation( $string );

			if ( 'VISUAL' === $string->get_editor_type() ) {
				$translation->set_value( wpautop( $translation->get_value() ) );
			}

			$settings = $this->translatable_nodes->update( $node_id, $settings, $translation );
		}
		return $settings;
	}
}