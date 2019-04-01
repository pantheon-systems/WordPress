<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Get ACF data
 *
 * @param $value
 * @param $data
 *
 * @return string
 */
function vc_gitem_template_attribute_acf( $value, $data ) {
	$label = '';
	/**
	 * @var null|Wp_Post $post ;
	 * @var string $data ;
	 */
	extract( array_merge( array(
		'post' => null,
		'data' => '',
	), $data ) );

	if ( strstr( $data, 'field_from_group_' ) ) {
		$group_id = preg_replace( '/(^field_from_group_|_labeled$)/', '', $data );
		$fields = function_exists( 'acf_get_fields' ) ? acf_get_fields( $group_id ) : apply_filters( 'acf/field_group/get_fields', array(), $group_id );
		$field = is_array( $fields ) && isset( $fields[0] ) ? $fields[0] : false;
		if ( is_array( $field ) && isset( $field['key'] ) ) {
			$data = $field['key'] . ( strstr( $data, '_labeled' ) ? '_labeled' : '' );
		}
	}
	$label = '';
	if ( preg_match( '/_labeled$/', $data ) ) {
		$data = preg_replace( '/_labeled$/', '', $data );
		$field = get_field_object( $data );
		$label = is_array( $field ) && isset( $field['label'] ) ? '<span class="vc_gitem-acf-label">' . $field['label'] . ':</span> ' : '';
	}

	$value = '';
	if ( $data ) {
		$value = do_shortcode( '[acf field="' . $data . '" post_id="' . $post->ID . '"]' );
	}

	return $label . apply_filters( 'vc_gitem_template_attribute_acf_value', $value );
}

add_filter( 'vc_gitem_template_attribute_acf', 'vc_gitem_template_attribute_acf', 10, 2 );
