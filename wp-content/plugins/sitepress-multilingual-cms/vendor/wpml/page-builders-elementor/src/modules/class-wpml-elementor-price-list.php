<?php

/**
 * Class WPML_Elementor_Price_List
 */
class WPML_Elementor_Price_List extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'price_list';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return array( 'title', 'item_description', 'link' => array( 'url' ) );
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_title( $field ) {
		if ( 'title' === $field ) {
			return esc_html__( 'Price list: title', 'sitepress' );
		}

		if ( 'item_description' === $field ) {
			return esc_html__( 'Pricing list: description', 'sitepress' );
		}

		if ( 'url' === $field ) {
			return esc_html__( 'Pricing list: link', 'sitepress' );
		}

		return '';
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_editor_type( $field ) {
		if ( 'title' === $field ) {
			return 'LINE';
		}

		if ( 'url' === $field ) {
			return 'LINK';
		}

		if ( 'item_description' === $field ) {
			return 'VISUAL';
		}

		return '';
	}
}