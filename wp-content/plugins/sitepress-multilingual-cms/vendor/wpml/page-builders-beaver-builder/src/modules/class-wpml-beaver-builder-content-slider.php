<?php

class WPML_Beaver_Builder_Content_Slider extends WPML_Beaver_Builder_Module_With_Items {

	public function &get_items( $settings ) {
		return $settings->slides;
	}

	public function get_fields() {
		return array( 'title', 'text', 'cta_text', 'link' );
	}

	protected function get_title( $field ) {
		switch( $field ) {
			case 'title':
				return esc_html__( 'Content Slider: Slide heading', 'sitepress' );

			case 'text':
				return esc_html__( 'Content Slider: Slide content', 'sitepress' );

			case 'cta_text':
				return esc_html__( 'Content Slider: Slide call to action text', 'sitepress' );

			case 'link':
				return esc_html__( 'Content Slider: Slide call to action link', 'sitepress' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'title':
			case 'cta_text':
				return 'LINE';

			case 'link':
				return 'LINK';

			case 'text':
				return 'VISUAL';

			default:
				return '';
		}
	}

}
