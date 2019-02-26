<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

function vc_custom_heading_element_params() {
	return array(
		'name' => __( 'Custom Heading', 'js_composer' ),
		'base' => 'vc_custom_heading',
		'icon' => 'icon-wpb-ui-custom_heading',
		'show_settings_on_create' => true,
		'category' => __( 'Content', 'js_composer' ),
		'description' => __( 'Text with Google fonts', 'js_composer' ),
		'params' => array(
			array(
				'type' => 'dropdown',
				'heading' => __( 'Text source', 'js_composer' ),
				'param_name' => 'source',
				'value' => array(
					__( 'Custom text', 'js_composer' ) => '',
					__( 'Post or Page Title', 'js_composer' ) => 'post_title',
				),
				'std' => '',
				'description' => __( 'Select text source.', 'js_composer' ),
			),
			array(
				'type' => 'textarea',
				'heading' => __( 'Text', 'js_composer' ),
				'param_name' => 'text',
				'admin_label' => true,
				'value' => __( 'This is custom heading element', 'js_composer' ),
				'description' => __( 'Note: If you are using non-latin characters be sure to activate them under Settings/WPBakery Page Builder/General Settings.', 'js_composer' ),
				'dependency' => array(
					'element' => 'source',
					'is_empty' => true,
				),
			),
			array(
				'type' => 'vc_link',
				'heading' => __( 'URL (Link)', 'js_composer' ),
				'param_name' => 'link',
				'description' => __( 'Add link to custom heading.', 'js_composer' ),
				// compatible with btn2 and converted from href{btn1}
			),
			array(
				'type' => 'font_container',
				'param_name' => 'font_container',
				'value' => 'tag:h2|text_align:left',
				'settings' => array(
					'fields' => array(
						'tag' => 'h2',
						// default value h2
						'text_align',
						'font_size',
						'line_height',
						'color',
						'tag_description' => __( 'Select element tag.', 'js_composer' ),
						'text_align_description' => __( 'Select text alignment.', 'js_composer' ),
						'font_size_description' => __( 'Enter font size.', 'js_composer' ),
						'line_height_description' => __( 'Enter line height.', 'js_composer' ),
						'color_description' => __( 'Select heading color.', 'js_composer' ),
					),
				),
			),
			array(
				'type' => 'checkbox',
				'heading' => __( 'Use theme default font family?', 'js_composer' ),
				'param_name' => 'use_theme_fonts',
				'value' => array( __( 'Yes', 'js_composer' ) => 'yes' ),
				'description' => __( 'Use font family from the theme.', 'js_composer' ),
			),
			array(
				'type' => 'google_fonts',
				'param_name' => 'google_fonts',
				'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
				'settings' => array(
					'fields' => array(
						'font_family_description' => __( 'Select font family.', 'js_composer' ),
						'font_style_description' => __( 'Select font styling.', 'js_composer' ),
					),
				),
				'dependency' => array(
					'element' => 'use_theme_fonts',
					'value_not_equal_to' => 'yes',
				),
			),
			vc_map_add_css_animation(),
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
				'type' => 'css_editor',
				'heading' => __( 'CSS box', 'js_composer' ),
				'param_name' => 'css',
				'group' => __( 'Design Options', 'js_composer' ),
			),
		),
	);
}
