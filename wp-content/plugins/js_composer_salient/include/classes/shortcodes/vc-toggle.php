<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class WPBakeryShortCode_VC_Toggle extends WPBakeryShortCode {
	public function outputTitle( $title ) {
		return '';
	}

	public function getHeading( $atts ) {
		if ( isset( $atts['use_custom_heading'] ) && 'true' === $atts['use_custom_heading'] ) {
			$custom_heading = visual_composer()->getShortCode( 'vc_custom_heading' );

			$data = vc_map_integrate_parse_atts( $this->shortcode, 'vc_custom_heading', $atts, 'custom_' );
			$data['text'] = $atts['title'];

			return $custom_heading->render( array_filter( $data ) );
		} else {
			return '<h4>' . esc_html( $atts['title'] ) . '</h4>';
		}
	}
}
