<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( 'vc_edit_form' === vc_post_param( 'action' ) && vc_verify_admin_nonce() ) {
	$link_category = array( __( 'All Links', 'js_composer' ) => '' );
	$link_cats = get_terms( 'link_category' );
	if ( is_array( $link_cats ) && ! empty( $link_cats ) ) {
		foreach ( $link_cats as $link_cat ) {
			if ( is_object( $link_cat ) && isset( $link_cat->name, $link_cat->term_id ) ) {
				$link_category[ $link_cat->name ] = $link_cat->term_id;
			}
		}
	}
} else {
	$link_category = array();
}

return array(
	'name' => 'WP ' . __( 'Links' ),
	'base' => 'vc_wp_links',
	'icon' => 'icon-wpb-wp',
	'category' => __( 'WordPress Widgets', 'js_composer' ),
	'class' => 'wpb_vc_wp_widget',
	'content_element' => (bool) get_option( 'link_manager_enabled' ),
	'weight' => - 50,
	'description' => __( 'Your blogroll', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'dropdown',
			'heading' => __( 'Link Category', 'js_composer' ),
			'param_name' => 'category',
			'value' => $link_category,
			'admin_label' => true,
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Order by', 'js_composer' ),
			'param_name' => 'orderby',
			'value' => array(
				__( 'Link title', 'js_composer' ) => 'name',
				__( 'Link rating', 'js_composer' ) => 'rating',
				__( 'Link ID', 'js_composer' ) => 'id',
				__( 'Random', 'js_composer' ) => 'rand',
			),
		),
		array(
			'type' => 'checkbox',
			'heading' => __( 'Options', 'js_composer' ),
			'param_name' => 'options',
			'value' => array(
				__( 'Show Link Image', 'js_composer' ) => 'images',
				__( 'Show Link Name', 'js_composer' ) => 'name',
				__( 'Show Link Description', 'js_composer' ) => 'description',
				__( 'Show Link Rating', 'js_composer' ) => 'rating',
			),
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Number of links to show', 'js_composer' ),
			'param_name' => 'limit',
			'value' => - 1,
		),
		array(
			'type' => 'el_id',
			'heading' => __( 'Element ID', 'js_composer' ),
			'param_name' => 'el_id',
			'description' => sprintf( __( 'Enter element ID (Note: make sure it is unique and valid according to <a href="%s" target="_blank">w3c specification</a>).', 'js_composer' ), 'http://www.w3schools.com/tags/att_global_id.asp' ),
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Extra class name', 'js_composer' ),
			'param_name' => 'el_class',
			'description' => __( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
		),
	),
);
