<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return array(
	'name' => __( 'Old Call to Action Button', 'js_composer' ) . ' 2',
	'base' => 'vc_cta_button2',
	'icon' => 'icon-wpb-call-to-action',
	'deprecated' => '4.5',
	'content_element' => false,
	'category' => array( __( 'Content', 'js_composer' ) ),
	'description' => __( 'Catch visitors attention with CTA block', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => __( 'Heading', 'js_composer' ),
			'admin_label' => true,
			'param_name' => 'h2',
			'value' => __( 'Hey! I am first heading line feel free to change me', 'js_composer' ),
			'description' => __( 'Enter text for heading line.', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Subheading', 'js_composer' ),
			'param_name' => 'h4',
			'value' => '',
			'description' => __( 'Enter text for subheading line.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Shape', 'js_composer' ),
			'param_name' => 'style',
			'value' => getVcShared( 'cta styles' ),
			'description' => __( 'Select display shape and style.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Width', 'js_composer' ),
			'param_name' => 'el_width',
			'value' => getVcShared( 'cta widths' ),
			'description' => __( 'Select element width (percentage).', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Text alignment', 'js_composer' ),
			'param_name' => 'txt_align',
			'value' => getVcShared( 'text align' ),
			'description' => __( 'Select text alignment in "Call to Action" block.', 'js_composer' ),
		),
		array(
			'type' => 'colorpicker',
			'heading' => __( 'Background color', 'js_composer' ),
			'param_name' => 'accent_color',
			'description' => __( 'Select background color.', 'js_composer' ),
		),
		array(
			'type' => 'textarea_html',
			'heading' => __( 'Text', 'js_composer' ),
			'param_name' => 'content',
			'value' => __( 'I am promo text. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'js_composer' ),
		),
		array(
			'type' => 'vc_link',
			'heading' => __( 'URL (Link)', 'js_composer' ),
			'param_name' => 'link',
			'description' => __( 'Add link to button (Important: adding link automatically adds button).', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Text on the button', 'js_composer' ),
			'param_name' => 'title',
			'value' => __( 'Text on the button', 'js_composer' ),
			'description' => __( 'Add text on the button.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Shape', 'js_composer' ),
			'param_name' => 'btn_style',
			'value' => getVcShared( 'button styles' ),
			'description' => __( 'Select button display style and shape.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Color', 'js_composer' ),
			'param_name' => 'color',
			'value' => getVcShared( 'colors' ),
			'description' => __( 'Select button color.', 'js_composer' ),
			'param_holder_class' => 'vc_colored-dropdown',
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Size', 'js_composer' ),
			'param_name' => 'size',
			'value' => getVcShared( 'sizes' ),
			'std' => 'md',
			'description' => __( 'Select button size.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Button position', 'js_composer' ),
			'param_name' => 'position',
			'value' => array(
				__( 'Right', 'js_composer' ) => 'right',
				__( 'Left', 'js_composer' ) => 'left',
				__( 'Bottom', 'js_composer' ) => 'bottom',
			),
			'description' => __( 'Select button alignment.', 'js_composer' ),
		),
		vc_map_add_css_animation(),
		array(
			'type' => 'textfield',
			'heading' => __( 'Extra class name', 'js_composer' ),
			'param_name' => 'el_class',
			'description' => __( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
		),
	),
);
