<?php

/**
 * Class WPML_Elementor_Accordion
 */
class WPML_Elementor_Testimonial_Carousel extends WPML_Elementor_Module_With_Items  {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'slides';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return array( 'content', 'name', 'title' );
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_title( $field ) {
		switch( $field ) {
			case 'content':
				return esc_html__( 'Testimonial Carousel: Content', 'sitepress' );

			case 'name':
				return esc_html__( 'Testimonial Carousel: Name', 'sitepress' );

			case 'title':
				return esc_html__( 'Testimonial Carousel: Title', 'sitepress' );

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
			case 'name':
				return 'LINE';

			case 'title':
				return 'LINE';

			case 'content':
				return 'VISUAL';

			default:
				return '';
		}
	}

}
