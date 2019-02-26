<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return array(
	'name' => __( 'Image Carousel', 'js_composer' ),
	'base' => 'vc_images_carousel',
	'icon' => 'icon-wpb-images-carousel',
	'category' => __( 'Content', 'js_composer' ),
	'description' => __( 'Animated carousel with images', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => __( 'Widget title', 'js_composer' ),
			'param_name' => 'title',
			'description' => __( 'Enter text used as widget title (Note: located above content element).', 'js_composer' ),
		),
		array(
			'type' => 'attach_images',
			'heading' => __( 'Images', 'js_composer' ),
			'param_name' => 'images',
			'value' => '',
			'description' => __( 'Select images from media library.', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Carousel size', 'js_composer' ),
			'param_name' => 'img_size',
			'value' => 'thumbnail',
			'description' => __( 'Enter image size. Example: thumbnail, medium, large, full or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height). Leave empty to use "thumbnail" size. If used slides per view, this will be used to define carousel wrapper size.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'On click action', 'js_composer' ),
			'param_name' => 'onclick',
			'value' => array(
				__( 'Open prettyPhoto', 'js_composer' ) => 'link_image',
				__( 'None', 'js_composer' ) => 'link_no',
				__( 'Open custom links', 'js_composer' ) => 'custom_link',
			),
			'description' => __( 'Select action for click event.', 'js_composer' ),
		),
		array(
			'type' => 'exploded_textarea_safe',
			'heading' => __( 'Custom links', 'js_composer' ),
			'param_name' => 'custom_links',
			'description' => __( 'Enter links for each slide (Note: divide links with linebreaks (Enter)).', 'js_composer' ),
			'dependency' => array(
				'element' => 'onclick',
				'value' => array( 'custom_link' ),
			),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Custom link target', 'js_composer' ),
			'param_name' => 'custom_links_target',
			'description' => __( 'Select how to open custom links.', 'js_composer' ),
			'dependency' => array(
				'element' => 'onclick',
				'value' => array( 'custom_link' ),
			),
			'value' => vc_target_param_list(),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Slider orientation', 'js_composer' ),
			'param_name' => 'mode',
			'value' => array(
				__( 'Horizontal', 'js_composer' ) => 'horizontal',
				__( 'Vertical', 'js_composer' ) => 'vertical',
			),
			'description' => __( 'Select slider position (Note: this affects swiping orientation).', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Slider speed', 'js_composer' ),
			'param_name' => 'speed',
			'value' => '5000',
			'description' => __( 'Duration of animation between slides (in ms).', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Slides per view', 'js_composer' ),
			'param_name' => 'slides_per_view',
			'value' => '1',
			'description' => __( 'Enter number of slides to display at the same time.', 'js_composer' ),
		),
		array(
			'type' => 'checkbox',
			'heading' => __( 'Slider autoplay', 'js_composer' ),
			'param_name' => 'autoplay',
			'description' => __( 'Enable autoplay mode.', 'js_composer' ),
			'value' => array( __( 'Yes', 'js_composer' ) => 'yes' ),
		),
		array(
			'type' => 'checkbox',
			'heading' => __( 'Hide pagination control', 'js_composer' ),
			'param_name' => 'hide_pagination_control',
			'description' => __( 'If checked, pagination controls will be hidden.', 'js_composer' ),
			'value' => array( __( 'Yes', 'js_composer' ) => 'yes' ),
		),
		array(
			'type' => 'checkbox',
			'heading' => __( 'Hide prev/next buttons', 'js_composer' ),
			'param_name' => 'hide_prev_next_buttons',
			'description' => __( 'If checked, prev/next buttons will be hidden.', 'js_composer' ),
			'value' => array( __( 'Yes', 'js_composer' ) => 'yes' ),
		),
		array(
			'type' => 'checkbox',
			'heading' => __( 'Partial view', 'js_composer' ),
			'param_name' => 'partial_view',
			'description' => __( 'If checked, part of the next slide will be visible.', 'js_composer' ),
			'value' => array( __( 'Yes', 'js_composer' ) => 'yes' ),
		),
		array(
			'type' => 'checkbox',
			'heading' => __( 'Slider loop', 'js_composer' ),
			'param_name' => 'wrap',
			'description' => __( 'Enable slider loop mode.', 'js_composer' ),
			'value' => array( __( 'Yes', 'js_composer' ) => 'yes' ),
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
