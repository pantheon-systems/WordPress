<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * New button implementation
 * array_merge is needed due to merging other shortcode data into params.
 * @since 4.5
 */

$pixel_icons = vc_pixel_icons();
require_once vc_path_dir( 'CONFIG_DIR', 'content/vc-icon-element.php' );

$icons_params = vc_map_integrate_shortcode( vc_icon_element_params(), 'i_', '', array(
	'include_only_regex' => '/^(type|icon_\w*)/',
	// we need only type, icon_fontawesome, icon_blabla..., NOT color and etc
), array(
	'element' => 'add_icon',
	'value' => 'true',
) );
// populate integrated vc_icons params.
if ( is_array( $icons_params ) && ! empty( $icons_params ) ) {
	foreach ( $icons_params as $key => $param ) {
		if ( is_array( $param ) && ! empty( $param ) ) {
			if ( 'i_type' === $param['param_name'] ) {
				// append pixelicons to dropdown
				$icons_params[ $key ]['value'][ __( 'Pixel', 'js_composer' ) ] = 'pixelicons';
			}
			if ( isset( $param['admin_label'] ) ) {
				// remove admin label
				unset( $icons_params[ $key ]['admin_label'] );
			}
		}
	}
}
$params = array_merge( array(
	array(
		'type' => 'textfield',
		'heading' => __( 'Text', 'js_composer' ),
		'param_name' => 'title',
		// fully compatible to btn1 and btn2
		'value' => __( 'Text on the button', 'js_composer' ),
	),
	array(
		'type' => 'vc_link',
		'heading' => __( 'URL (Link)', 'js_composer' ),
		'param_name' => 'link',
		'description' => __( 'Add link to button.', 'js_composer' ),
		// compatible with btn2 and converted from href{btn1}
	),
	array(
		'type' => 'dropdown',
		'heading' => __( 'Style', 'js_composer' ),
		'description' => __( 'Select button display style.', 'js_composer' ),
		'param_name' => 'style',
		// partly compatible with btn2, need to be converted shape+style from btn2 and btn1
		'value' => array(
			__( 'Modern', 'js_composer' ) => 'modern',
			__( 'Classic', 'js_composer' ) => 'classic',
			__( 'Flat', 'js_composer' ) => 'flat',
			__( 'Outline', 'js_composer' ) => 'outline',
			__( '3d', 'js_composer' ) => '3d',
			__( 'Custom', 'js_composer' ) => 'custom',
			__( 'Outline custom', 'js_composer' ) => 'outline-custom',
			__( 'Gradient', 'js_composer' ) => 'gradient',
			__( 'Gradient Custom', 'js_composer' ) => 'gradient-custom',
		),
	),
	array(
		'type' => 'dropdown',
		'heading' => __( 'Gradient Color 1', 'js_composer' ),
		'param_name' => 'gradient_color_1',
		'description' => __( 'Select first color for gradient.', 'js_composer' ),
		'param_holder_class' => 'vc_colored-dropdown vc_btn3-colored-dropdown',
		'value' => getVcShared( 'colors-dashed' ),
		'std' => 'turquoise',
		'dependency' => array(
			'element' => 'style',
			'value' => array( 'gradient' ),
		),
		'edit_field_class' => 'vc_col-sm-6',
	),
	array(
		'type' => 'dropdown',
		'heading' => __( 'Gradient Color 2', 'js_composer' ),
		'param_name' => 'gradient_color_2',
		'description' => __( 'Select second color for gradient.', 'js_composer' ),
		'param_holder_class' => 'vc_colored-dropdown vc_btn3-colored-dropdown',
		'value' => getVcShared( 'colors-dashed' ),
		'std' => 'blue',
		// must have default color grey
		'dependency' => array(
			'element' => 'style',
			'value' => array( 'gradient' ),
		),
		'edit_field_class' => 'vc_col-sm-6',
	),
	array(
		'type' => 'colorpicker',
		'heading' => __( 'Gradient Color 1', 'js_composer' ),
		'param_name' => 'gradient_custom_color_1',
		'description' => __( 'Select first color for gradient.', 'js_composer' ),
		'param_holder_class' => 'vc_colored-dropdown vc_btn3-colored-dropdown',
		'value' => '#dd3333',
		'dependency' => array(
			'element' => 'style',
			'value' => array( 'gradient-custom' ),
		),
		'edit_field_class' => 'vc_col-sm-4',
	),
	array(
		'type' => 'colorpicker',
		'heading' => __( 'Gradient Color 2', 'js_composer' ),
		'param_name' => 'gradient_custom_color_2',
		'description' => __( 'Select second color for gradient.', 'js_composer' ),
		'param_holder_class' => 'vc_colored-dropdown vc_btn3-colored-dropdown',
		'value' => '#eeee22',
		'dependency' => array(
			'element' => 'style',
			'value' => array( 'gradient-custom' ),
		),
		'edit_field_class' => 'vc_col-sm-4',
	),
	array(
		'type' => 'colorpicker',
		'heading' => __( 'Button Text Color', 'js_composer' ),
		'param_name' => 'gradient_text_color',
		'description' => __( 'Select button text color.', 'js_composer' ),
		'param_holder_class' => 'vc_colored-dropdown vc_btn3-colored-dropdown',
		'value' => '#ffffff',
		// must have default color grey
		'dependency' => array(
			'element' => 'style',
			'value' => array( 'gradient-custom' ),
		),
		'edit_field_class' => 'vc_col-sm-4',
	),
	array(
		'type' => 'colorpicker',
		'heading' => __( 'Background', 'js_composer' ),
		'param_name' => 'custom_background',
		'description' => __( 'Select custom background color for your element.', 'js_composer' ),
		'dependency' => array(
			'element' => 'style',
			'value' => array( 'custom' ),
		),
		'edit_field_class' => 'vc_col-sm-6',
		'std' => '#ededed',
	),
	array(
		'type' => 'colorpicker',
		'heading' => __( 'Text', 'js_composer' ),
		'param_name' => 'custom_text',
		'description' => __( 'Select custom text color for your element.', 'js_composer' ),
		'dependency' => array(
			'element' => 'style',
			'value' => array( 'custom' ),
		),
		'edit_field_class' => 'vc_col-sm-6',
		'std' => '#666',
	),
	array(
		'type' => 'colorpicker',
		'heading' => __( 'Outline and Text', 'js_composer' ),
		'param_name' => 'outline_custom_color',
		'description' => __( 'Select outline and text color for your element.', 'js_composer' ),
		'dependency' => array(
			'element' => 'style',
			'value' => array( 'outline-custom' ),
		),
		'edit_field_class' => 'vc_col-sm-4',
		'std' => '#666',
	),
	array(
		'type' => 'colorpicker',
		'heading' => __( 'Hover background', 'js_composer' ),
		'param_name' => 'outline_custom_hover_background',
		'description' => __( 'Select hover background color for your element.', 'js_composer' ),
		'dependency' => array(
			'element' => 'style',
			'value' => array( 'outline-custom' ),
		),
		'edit_field_class' => 'vc_col-sm-4',
		'std' => '#666',
	),
	array(
		'type' => 'colorpicker',
		'heading' => __( 'Hover text', 'js_composer' ),
		'param_name' => 'outline_custom_hover_text',
		'description' => __( 'Select hover text color for your element.', 'js_composer' ),
		'dependency' => array(
			'element' => 'style',
			'value' => array( 'outline-custom' ),
		),
		'edit_field_class' => 'vc_col-sm-4',
		'std' => '#fff',
	),
	array(
		'type' => 'dropdown',
		'heading' => __( 'Shape', 'js_composer' ),
		'description' => __( 'Select button shape.', 'js_composer' ),
		'param_name' => 'shape',
		// need to be converted
		'value' => array(
			__( 'Rounded', 'js_composer' ) => 'rounded',
			__( 'Square', 'js_composer' ) => 'square',
			__( 'Round', 'js_composer' ) => 'round',
		),
	),
	array(
		'type' => 'dropdown',
		'heading' => __( 'Color', 'js_composer' ),
		'param_name' => 'color',
		'description' => __( 'Select button color.', 'js_composer' ),
		// compatible with btn2, need to be converted from btn1
		'param_holder_class' => 'vc_colored-dropdown vc_btn3-colored-dropdown',
		'value' => array(
				// Btn1 Colors
				__( 'Classic Grey', 'js_composer' ) => 'default',
				__( 'Classic Blue', 'js_composer' ) => 'primary',
				__( 'Classic Turquoise', 'js_composer' ) => 'info',
				__( 'Classic Green', 'js_composer' ) => 'success',
				__( 'Classic Orange', 'js_composer' ) => 'warning',
				__( 'Classic Red', 'js_composer' ) => 'danger',
				__( 'Classic Black', 'js_composer' ) => 'inverse',
				// + Btn2 Colors (default color set)
			) + getVcShared( 'colors-dashed' ),
		'std' => 'grey',
		// must have default color grey
		'dependency' => array(
			'element' => 'style',
			'value_not_equal_to' => array(
				'custom',
				'outline-custom',
				'gradient',
				'gradient-custom',
			),
		),
	),
	array(
		'type' => 'dropdown',
		'heading' => __( 'Size', 'js_composer' ),
		'param_name' => 'size',
		'description' => __( 'Select button display size.', 'js_composer' ),
		// compatible with btn2, default md, but need to be converted from btn1 to btn2
		'std' => 'md',
		'value' => getVcShared( 'sizes' ),
	),
	array(
		'type' => 'dropdown',
		'heading' => __( 'Alignment', 'js_composer' ),
		'param_name' => 'align',
		'description' => __( 'Select button alignment.', 'js_composer' ),
		// compatible with btn2, default left to be compatible with btn1
		'value' => array(
			__( 'Inline', 'js_composer' ) => 'inline',
			// default as well
			__( 'Left', 'js_composer' ) => 'left',
			// default as well
			__( 'Right', 'js_composer' ) => 'right',
			__( 'Center', 'js_composer' ) => 'center',
		),
	),
	array(
		'type' => 'checkbox',
		'heading' => __( 'Set full width button?', 'js_composer' ),
		'param_name' => 'button_block',
		'dependency' => array(
			'element' => 'align',
			'value_not_equal_to' => 'inline',
		),
	),
	array(
		'type' => 'checkbox',
		'heading' => __( 'Add icon?', 'js_composer' ),
		'param_name' => 'add_icon',
	),
	array(
		'type' => 'dropdown',
		'heading' => __( 'Icon Alignment', 'js_composer' ),
		'description' => __( 'Select icon alignment.', 'js_composer' ),
		'param_name' => 'i_align',
		'value' => array(
			__( 'Left', 'js_composer' ) => 'left',
			// default as well
			__( 'Right', 'js_composer' ) => 'right',
		),
		'dependency' => array(
			'element' => 'add_icon',
			'value' => 'true',
		),
	),
), $icons_params, array(
		array(
			'type' => 'iconpicker',
			'heading' => __( 'Icon', 'js_composer' ),
			'param_name' => 'i_icon_pixelicons',
			'value' => 'vc_pixel_icon vc_pixel_icon-alert',
			'settings' => array(
				'emptyIcon' => false,
				// default true, display an "EMPTY" icon?
				'type' => 'pixelicons',
				'source' => $pixel_icons,
			),
			'dependency' => array(
				'element' => 'i_type',
				'value' => 'pixelicons',
			),
			'description' => __( 'Select icon from library.', 'js_composer' ),
		),
	), array(
		vc_map_add_css_animation( true ),
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
		array(
			'type' => 'checkbox',
			'heading' => __( 'Advanced on click action', 'js_composer' ),
			'param_name' => 'custom_onclick',
			'description' => __( 'Insert inline onclick javascript action.', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'On click code', 'js_composer' ),
			'param_name' => 'custom_onclick_code',
			'description' => __( 'Enter onclick action code.', 'js_composer' ),
			'dependency' => array(
				'element' => 'custom_onclick',
				'not_empty' => true,
			),
		),
		array(
			'type' => 'css_editor',
			'heading' => __( 'CSS box', 'js_composer' ),
			'param_name' => 'css',
			'group' => __( 'Design Options', 'js_composer' ),
		),
	)
);
/**
 * @class WPBakeryShortCode_VC_Btn
 */
return array(
	'name' => __( 'Button', 'js_composer' ),
	'base' => 'vc_btn',
	'icon' => 'icon-wpb-ui-button',
	'category' => array(
		__( 'Content', 'js_composer' ),
	),
	'description' => __( 'Eye catching button', 'js_composer' ),
	'params' => $params,
	'js_view' => 'VcButton3View',
	'custom_markup' => '{{title}}<div class="vc_btn3-container"><button class="vc_general vc_btn3 vc_btn3-size-sm vc_btn3-shape-{{ params.shape }} vc_btn3-style-{{ params.style }} vc_btn3-color-{{ params.color }}">{{{ params.title }}}</button></div>',
);
