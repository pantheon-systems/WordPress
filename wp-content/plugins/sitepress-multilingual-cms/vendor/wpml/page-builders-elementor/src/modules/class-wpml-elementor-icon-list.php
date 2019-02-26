<?php

/**
 * Class WPML_Elementor_Icon_List
 */
class WPML_Elementor_Icon_List extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'icon_list';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return array( 'text', 'link' => array( 'url' ) );
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_title( $field ) {
		switch( $field ) {
			case 'text':
				return esc_html__( 'Icon List: Text', 'sitepress' );

			case 'url':
				return esc_html__( 'Icon List: Link URL', 'sitepress' );

			default:
				return '';
		}
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'text':
				return 'LINE';
			case 'url':
				return 'LINK';

			default:
				return '';
		}
	}

}
