<?php

/**
 * Class WPML_Beaver_Builder_Update_Translation
 */
class WPML_Beaver_Builder_Update_Translation extends WPML_Page_Builders_Update_Translation  {
	
	/** @param array $data_array */
	public function update_strings_in_modules( array &$data_array ) {
		foreach ( $data_array as &$data ) {
			if ( is_array( $data ) ) {
				$this->update_strings_in_modules( $data );
			} elseif ( is_object( $data ) ) {
				if ( isset( $data->type ) && 'module' === $data->type ) {
					$data->settings = $this->update_strings_in_node( $data->node, $data->settings );
				}
			}
		}
	}

	/**
	 * @param string $node_id
	 * @param $settings
	 *
	 * @return mixed
	 */
	public function update_strings_in_node( $node_id, $settings ) {
		$strings = $this->translatable_nodes->get( $node_id, $settings );
		foreach ( $strings as $string ) {
			$translation = $this->get_translation( $string );
			$settings = $this->translatable_nodes->update( $node_id, $settings, $translation );
		}
		return $settings;
	}
}