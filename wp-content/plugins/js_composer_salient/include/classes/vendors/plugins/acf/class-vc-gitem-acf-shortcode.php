<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class Vc_Gitem_Acf_Shortcode extends WPBakeryShortCode {
	/**
	 * @param $atts
	 * @param null $content
	 *
	 * @return mixed|void
	 */
	protected function content( $atts, $content = null ) {
		$field_key = $label = '';
		/**
		 * @var string $el_class
		 * @var string $show_label
		 * @var string $align
		 * @var string $field_group
		 */
		extract( shortcode_atts( array(
			'el_class' => '',
			'field_group' => '',
			'show_label' => '',
			'align' => '',
		), $atts ) );
		if ( 0 === strlen( $field_group ) ) {
			$groups = function_exists( 'acf_get_field_groups' ) ? acf_get_field_groups() : apply_filters( 'acf/get_field_groups', array() );
			if ( is_array( $groups ) && isset( $groups[0] ) ) {
				$key = isset( $groups[0]['id'] ) ? 'id' : ( isset( $groups[0]['ID'] ) ? 'ID' : 'id' );
				$field_group = $groups[0][ $key ];
			}
		}
		if ( ! empty( $field_group ) ) {
			$field_key = ! empty( $atts[ 'field_from_' . $field_group ] ) ? $atts[ 'field_from_' . $field_group ] : 'field_from_group_' . $field_group;
		}
		if ( 'yes' === $show_label && $field_key ) {
			$field_key .= '_labeled';
		}
		$css_class = 'vc_gitem-acf'
		             . ( strlen( $el_class ) ? ' ' . $el_class : '' )
		             . ( strlen( $align ) ? ' vc_gitem-align-' . $align : '' )
		             . ( strlen( $field_key ) ? ' ' . $field_key : '' );

		return '<div ' . $field_key . ' class="' . esc_attr( $css_class ) . '">'
		       . '{{ acf' . ( ! empty( $field_key ) ? ':' . $field_key : '' ) . ' }}'
		       . '</div>';
	}
}
