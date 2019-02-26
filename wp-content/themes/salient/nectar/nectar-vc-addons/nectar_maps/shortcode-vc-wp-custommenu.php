<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$custom_menus = array();
if ( 'vc_edit_form' === vc_post_param( 'action' ) && vc_verify_admin_nonce() ) {
	$menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );
	if ( is_array( $menus ) && ! empty( $menus ) ) {
		foreach ( $menus as $single_menu ) {
			if ( is_object( $single_menu ) && isset( $single_menu->name, $single_menu->term_id ) ) {
				$custom_menus[ $single_menu->name ] = $single_menu->term_id;
			}
		}
	}
}

return array(
	'name' => 'WP ' . __( 'Custom Menu', 'default' ),
	'base' => 'vc_wp_custommenu',
	"icon" => "icon-wpb-page-submenu",
	'category' => __( 'Content', 'js_composer' ),
	'class' => 'wpb_vc_wp_widget',
	'weight' => - 50,
	'description' => esc_html__('Add one of your custom menus as a widget', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => __( 'Widget title', 'js_composer' ),
			'param_name' => 'title',
			'description' => esc_html__('What text use as a widget title. Leave blank to use default widget title.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Menu', 'js_composer' ),
			'param_name' => 'nav_menu',
			'value' => $custom_menus,
			'description' => empty( $custom_menus ) ? esc_html__('Custom menus not found. Please visit the Appearance > Menus page to create new menu.', 'js_composer' ) : esc_html__('Select menu to display.', 'js_composer' ),
			'admin_label' => true,
			'save_always' => true,
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Extra class name', 'js_composer' ),
			'param_name' => 'el_class',
			'description' => esc_html__('Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
		),
	),
);
