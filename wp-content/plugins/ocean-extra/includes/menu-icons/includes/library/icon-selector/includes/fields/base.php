<?php
if ( ! function_exists( 'wp_get_attachment_image_url' ) ) {
	/**
	 * Get the URL of an image attachment.
	 *
	 */
	function wp_get_attachment_image_url( $attachment_id, $size = 'thumbnail', $icon = false ) {
		$image = wp_get_attachment_image_src( $attachment_id, $size, $icon );
		return isset( $image['0'] ) ? $image['0'] : false;
	}
}

/**
 * Get Icon URL
 *
 */
function oe_icon_picker_get_icon_url( $type, $id, $size = 'thumbnail' ) {
	$url = '';

	if ( ! in_array( $type, array( 'image', 'svg' ), true ) ) {
		return $url;
	}

	if ( empty( $id ) ) {
		return $url;
	}

	return wp_get_attachment_image_url( $id, $size, false );
}

/**
 * The Icon Picker Field
 *
 */
function oe_icon_picker_field( $args, $echo = true ) {
	$defaults = array(
		'id'    => '',
		'name'  => '',
		'value' => array(
			'type' => '',
			'icon' => '',
		),
		'select' => sprintf( '<a class="ipf-select">%s</a>', esc_html__( 'Select Icon', 'ocean-extra' ) ),
		'remove' => sprintf( '<a class="ipf-remove button hidden">%s</a>', esc_html__( 'Remove', 'ocean-extra' ) ),
	);

	$args          = wp_parse_args( $args, $defaults );
	$args['value'] = wp_parse_args( $args['value'], $defaults['value'] );

	$field  = sprintf( '<div id="%s" class="ipf">', $args['id'] );
	$field .= $args['select'];
	$field .= $args['remove'];

	foreach ( $args['value'] as $key => $value ) {
		$field .= sprintf(
			'<input type="hidden" id="%s" name="%s" class="%s" value="%s" />',
			esc_attr( "{$args['id']}-{$key}" ),
			esc_attr( "{$args['name']}[{$key}]" ),
			esc_attr( "ipf-{$key}" ),
			esc_attr( $value )
		);
	}

	// This won't be saved. It's here for the preview.
	$field .= sprintf(
		'<input type="hidden" class="url" value="%s" />',
		esc_attr( oe_icon_picker_get_icon_url( $args['value']['type'], $args['value']['icon'] ) )
	);
	$field .= '</div>';

	if ( $echo ) {
		echo $field; // xss ok
	} else {
		return $field;
	}
}
