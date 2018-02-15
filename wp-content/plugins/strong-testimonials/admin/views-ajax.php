<?php
/**
 * Views Ajax Functions
 */


/**
 * Check for forced options.
 *
 * @since 1.25.0
 */
function wpmtst_force_check() {
	$atts = array( 'template' => $_REQUEST['template'] );
	$force = WPMST()->templates->get_template_config( $atts, 'force', false );
	echo $force;
	wp_die();
}
add_action( 'wp_ajax_wpmtst_force_check', 'wpmtst_force_check' );


/**
 * [Add New Field] Ajax receiver
 *
 * @since 1.21.0
 */
function wpmtst_view_add_field_function() {
	$new_key = (int) $_REQUEST['key'];
	$empty_field = array( 'field' => '', 'type' => 'text', 'class' => '' );
	wpmtst_view_field_inputs( $new_key, $empty_field, true );
	wp_die();
}
add_action( 'wp_ajax_wpmtst_view_add_field', 'wpmtst_view_add_field_function' );


/**
 * [Field Type: Link] Ajax receiver
 *
 * @since 1.21.0
 */
function wpmtst_view_add_field_link_function() {
	$key         = (int) $_REQUEST['key'];
	$field_name  = $_REQUEST['fieldName'];
	$type        = $_REQUEST['fieldType'];
	$empty_field = array( 'url' => '', 'link_text' => '', 'new_tab' => true );
	wpmtst_view_field_link( $key, $field_name, $type, $empty_field );
	wp_die();
}
add_action( 'wp_ajax_wpmtst_view_add_field_link', 'wpmtst_view_add_field_link_function' );


/**
 * [Field name change] Ajax receiver
 *
 * @since 1.24.0
 */
function wpmtst_view_get_label_function() {
	$field = array( 'field' => $_REQUEST['name'] );
	$label = wpmtst_get_field_label( $field );
	echo $label;
	wp_die();
}
add_action( 'wp_ajax_wpmtst_view_get_label', 'wpmtst_view_get_label_function' );


/**
 * [Field Type: Date] Ajax receiver
 *
 * @since 1.21.0
 */
function wpmtst_view_add_field_date_function() {
	$key = (int) $_REQUEST['key'];
	$empty_field = array( 'format' => '' );
	wpmtst_view_field_date( $key, $empty_field );
	wp_die();
}
add_action( 'wp_ajax_wpmtst_view_add_field_date', 'wpmtst_view_add_field_date_function' );


/**
 * Fetch the view mode description.
 *
 * @since 2.22.0
 */
function wpmtst_view_get_mode_description() {
	$mode = $_REQUEST['mode'];
	$options = get_option( 'wpmtst_view_options' );
	if ( isset( $options['mode'][ $mode ]['description'] ) ) {
		echo $options['mode'][ $mode ]['description'];
	}
	wp_die();
}
add_action( 'wp_ajax_wpmtst_view_get_mode_description', 'wpmtst_view_get_mode_description' );


/**
 * Get background color presets in View editor.
 */
function wpmtst_get_background_preset_colors() {
	$preset = wpmtst_get_background_presets( $_REQUEST['key'] );
	echo json_encode( $preset );
	wp_die();
}
add_action( 'wp_ajax_wpmtst_get_background_preset_colors', 'wpmtst_get_background_preset_colors' );
