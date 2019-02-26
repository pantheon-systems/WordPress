<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class WPBakeryShortCode_VC_Hoverbox extends WPBakeryShortCode {

	public function getHeading( $tag, $atts, $align ) {
		if ( isset( $atts[ $tag ] ) && '' !== trim( $atts[ $tag ] ) ) {
			if ( isset( $atts[ 'use_custom_fonts_' . $tag ] ) && 'true' === $atts[ 'use_custom_fonts_' . $tag ] ) {
				$custom_heading = visual_composer()->getShortCode( 'vc_custom_heading' );
				$data = vc_map_integrate_parse_atts( $this->shortcode, 'vc_custom_heading', $atts, $tag . '_' );
				$data['font_container'] = implode( '|', array_filter( array(
					'tag:' . 'h2',
					'text_align:' . esc_attr( $align ),
					$data['font_container'],
				) ) );
				$data['text'] = $atts[ $tag ]; // provide text to shortcode

				return $custom_heading->render( array_filter( $data ) );
			} else {
				$inline_css = array();
				$inline_css_string = '';
				if ( isset( $atts['style'] ) && 'custom' === $atts['style'] ) {
					if ( ! empty( $atts['custom_text'] ) ) {
						$inline_css[] = vc_get_css_color( 'color', $atts['custom_text'] );
					}
				}
				if ( $align ) {
					$inline_css[] = 'text-align:' . esc_attr( $align );
				}
				if ( ! empty( $inline_css ) ) {
					$inline_css_string = ' style="' . implode( '', $inline_css ) . '"';
				}

				return '<h2' . $inline_css_string . '>' . $atts[ $tag ] . '</h2>';
			}
		}

		return '';
	}

	public function renderButton( $atts ) {
		$button_atts = vc_map_integrate_parse_atts( $this->shortcode, 'vc_btn', $atts, 'hover_btn_' );
		$button = visual_composer()->getShortCode( 'vc_btn' );

		return $button->render( array_filter( $button_atts ) );
	}

}
