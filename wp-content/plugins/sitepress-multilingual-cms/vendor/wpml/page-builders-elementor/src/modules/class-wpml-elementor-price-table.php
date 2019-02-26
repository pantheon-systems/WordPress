<?php

/**
 * Class WPML_Elementor_Price_Table
 */
class WPML_Elementor_Price_Table extends WPML_Elementor_Module_With_Items  {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'features_list';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return array( 'item_text' );
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_title( $field ) {
		if ( 'item_text' === $field ) {
			return esc_html__( 'Price table: text', 'sitepress' );
		}

		return '';
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_editor_type( $field ) {
		if ( 'item_text' === $field ) {
			return 'VISUAL';
		}

		return '';
	}
}