<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( 'vc_edit_form' === vc_post_param( 'action' ) ) {
	add_filter( 'vc_edit_form_fields_attributes_vc_single_image', 'vc_single_image_convert_old_link_to_new' );
}
/**
 * Backward compatibility
 *
 * @since 4.6
 * @param $atts
 * @return mixed
 */
function vc_single_image_convert_old_link_to_new( $atts ) {
	if ( empty( $atts['onclick'] ) && isset( $atts['img_link_large'] ) && 'yes' === $atts['img_link_large'] ) {
		$atts['onclick'] = 'img_link_large';
		unset( $atts['img_link_large'] );
	} elseif ( empty( $atts['onclick'] ) && ( ! isset( $atts['img_link_large'] ) || 'yes' !== $atts['img_link_large'] ) ) {
		unset( $atts['img_link_large'] );
	}

	if ( empty( $atts['onclick'] ) && ! empty( $atts['link'] ) ) {
		$atts['onclick'] = 'custom_link';
	}

	return $atts;
}
