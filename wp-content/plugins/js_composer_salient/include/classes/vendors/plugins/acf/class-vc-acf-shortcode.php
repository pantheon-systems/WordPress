<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class WPBakeryShortCode_Vc_Acf extends WPBakeryShortCode {
	/**
	 * @param $atts
	 * @param null $content
	 *
	 * @return mixed|void
	 */
	protected function content( $atts, $content = null ) {
		$atts = $atts + vc_map_get_attributes( $this->getShortcode(), $atts );

		$field_group = $atts['field_group'];
		$field_key = '';
		if ( 0 === strlen( $atts['field_group'] ) ) {
			$groups = function_exists( 'acf_get_field_groups' ) ? acf_get_field_groups() : apply_filters( 'acf/get_field_groups', array() );
			if ( is_array( $groups ) && isset( $groups[0] ) ) {
				$key = isset( $groups[0]['id'] ) ? 'id' : ( isset( $groups[0]['ID'] ) ? 'ID' : 'id' );
				$field_group = $groups[0][ $key ];
			}
		}
		if ( $field_group ) {
			$field_key = ! empty( $atts[ 'field_from_' . $field_group ] ) ? $atts[ 'field_from_' . $field_group ] : 'field_from_group_' . $field_group;
		}

		$css_class = array();
		$css_class[] = 'vc_acf';
		if ( $atts['el_class'] ) {
			$css_class[] = $atts['el_class'];
		}
		if ( $atts['align'] ) {
			$css_class[] = 'vc_txt_align_' . $atts['align'];
		}

		$value = '';
		if ( $field_key ) {
			$css_class[] = $field_key;

			$value = do_shortcode( '[acf field="' . $field_key . '" post_id="' . get_the_ID() . '"]' );

			if ( $atts['show_label'] ) {
				$field = get_field_object( $field_key );
				$label = is_array( $field ) && isset( $field['label'] ) ? '<span class="vc_acf-label">' . $field['label'] . ':</span> ' : '';

				$value = $label . $value;
			}
		}

		$css_string = implode( ' ', $css_class );

		$output = '<div class="' . esc_attr( $css_string ) . '">' . $value . '</div>';

		return $output;
	}

}
