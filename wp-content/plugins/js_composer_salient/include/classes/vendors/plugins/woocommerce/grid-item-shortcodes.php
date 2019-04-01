<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return array(
	'vc_gitem_wocommerce' => array(
		'name' => __( 'WooCommerce field', 'js_composer' ),
		'base' => 'vc_gitem_wocommerce',
		'icon' => 'icon-wpb-woocommerce',
		'category' => __( 'Content', 'js_composer' ),
		'description' => __( 'Woocommerce', 'js_composer' ),
		'php_class_name' => 'Vc_Gitem_Woocommerce_Shortcode',
		'params' => array(

			array(
				'type' => 'dropdown',
				'heading' => __( 'Content type', 'js_composer' ),
				'param_name' => 'post_type',
				'value' => array(
					__( 'Product', 'js_composer' ) => 'product',
					__( 'Order', 'js_composer' ) => 'order',
				),
				'save_always' => true,
				'description' => __( 'Select Woo Commerce post type.', 'js_composer' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => __( 'Product field name', 'js_composer' ),
				'param_name' => 'product_field_key',
				'value' => Vc_Vendor_Woocommerce::getProductsFieldsList(),
				'dependency' => array(
					'element' => 'post_type',
					'value' => array( 'product' ),
				),
				'save_always' => true,
				'description' => __( 'Select field from product.', 'js_composer' ),
			),
			array(
				'type' => 'textfield',
				'heading' => __( 'Product custom key', 'js_composer' ),
				'param_name' => 'product_custom_key',
				'description' => __( 'Enter custom key.', 'js_composer' ),
				'dependency' => array(
					'element' => 'product_field_key',
					'value' => array( '_custom_' ),
				),
			),
			array(
				'type' => 'dropdown',
				'heading' => __( 'Order fields', 'js_composer' ),
				'param_name' => 'order_field_key',
				'value' => Vc_Vendor_Woocommerce::getOrderFieldsList(),
				'dependency' => array(
					'element' => 'post_type',
					'value' => array( 'order' ),
				),
				'save_always' => true,
				'description' => __( 'Select field from order.', 'js_composer' ),
			),
			array(
				'type' => 'textfield',
				'heading' => __( 'Order custom key', 'js_composer' ),
				'param_name' => 'order_custom_key',
				'dependency' => array(
					'element' => 'order_field_key',
					'value' => array( '_custom_' ),
				),
				'description' => __( 'Enter custom key.', 'js_composer' ),
			),
			array(
				'type' => 'checkbox',
				'heading' => __( 'Show label', 'js_composer' ),
				'param_name' => 'show_label',
				'value' => array( __( 'Yes', 'js_composer' ) => 'yes' ),
				'save_always' => true,
				'description' => __( 'Enter label to display before key value.', 'js_composer' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => __( 'Align', 'js_composer' ),
				'param_name' => 'align',
				'value' => array(
					__( 'left', 'js_composer' ) => 'left',
					__( 'right', 'js_composer' ) => 'right',
					__( 'center', 'js_composer' ) => 'center',
					__( 'justify', 'js_composer' ) => 'justify',
				),
				'save_always' => true,
				'description' => __( 'Select alignment.', 'js_composer' ),
			),
			array(
				'type' => 'textfield',
				'heading' => __( 'Extra class name', 'js_composer' ),
				'param_name' => 'el_class',
				'description' => __( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
			),
		),
		'post_type' => Vc_Grid_Item_Editor::postType(),
	),
);
