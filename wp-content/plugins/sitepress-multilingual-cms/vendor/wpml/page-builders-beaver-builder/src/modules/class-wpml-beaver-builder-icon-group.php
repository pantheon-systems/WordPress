<?php

class WPML_Beaver_Builder_Icon_Group extends WPML_Beaver_Builder_Module_With_Items {

	public function &get_items( $settings ) {
		return $settings->icons;
	}

	public function get_fields() {
		return array( 'link' );
	}

	protected function get_title( $field ) {
		switch ( $field ) {
			case 'link':
				return esc_html__( 'Icon link', 'sitepress' );

			default:
				return '';

		}
	}

	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'link':
				return 'LINK';

			default:
				return '';
		}
	}

}
