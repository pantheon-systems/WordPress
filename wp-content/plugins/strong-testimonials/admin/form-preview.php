<?php

function wpmtst_get_form_preview() {
	if ( ! isset( $_POST['fields'] ) ) exit;

	// parse_str decodes too; no need to use urldecode
	parse_str( stripslashes_deep( $_POST['fields'] ), $preview );

	$new_fields = array();
	$fields = apply_filters( 'wpmtst_fields', get_option( 'wpmtst_fields' ) );

	foreach ( $preview['fields'] as $key => $field ) {
		/*
		 * Before merging onto base field, catch fields that are "off"
		 * which the form does not submit. Otherwise, the default "on"
		 * would override the requested (but not submitted) "off".
		 */
		$field['show_label']         = isset( $field['show_label'] ) ? 1 : 0;
		$field['required']           = isset( $field['required'] ) ? 1 : 0;

		$field = array_merge( $fields['field_base'], $field );

		if ( 'none' == $field['input_type'] ) {
			$field['input_type'] = 'text';
		}

		$field['name']               = sanitize_text_field( $field['name'] );
		$field['label']              = sanitize_text_field( $field['label'] );
		// TODO Replace this special handling
		if ( 'checkbox' == $field['input_type'] ) {
			$field['default_form_value'] = wpmtst_sanitize_checkbox( $field, 'default_form_value' );
		} else {
			$field['default_form_value'] = sanitize_text_field( $field['default_form_value'] );
		}
		$field['placeholder']        = sanitize_text_field( $field['placeholder'] );
		$field['before']             = sanitize_text_field( $field['before'] );
		$field['after']              = sanitize_text_field( $field['after'] );

		// add to fields array in display order
		$new_fields[] = $field;
	}

	ob_start();
	include WPMTST_ADMIN . 'partials/templates/form-preview-template.php';
	$html = ob_get_contents();
	ob_end_clean();
	echo $html;

	exit;
}
add_action( 'wp_ajax_wpmtst_get_form_preview', 'wpmtst_get_form_preview' );
