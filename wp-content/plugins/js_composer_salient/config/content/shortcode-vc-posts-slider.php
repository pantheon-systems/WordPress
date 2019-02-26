<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return array(
	'name' => __( 'Posts Slider', 'js_composer' ),
	'base' => 'vc_posts_slider',
	'icon' => 'icon-wpb-slideshow',
	'category' => __( 'Content', 'js_composer' ),
	'description' => __( 'Slider with WP Posts', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => __( 'Widget title', 'js_composer' ),
			'param_name' => 'title',
			'description' => __( 'Enter text used as widget title (Note: located above content element).', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Slider type', 'js_composer' ),
			'param_name' => 'type',
			'admin_label' => true,
			'value' => array(
				__( 'Flex slider fade', 'js_composer' ) => 'flexslider_fade',
				__( 'Flex slider slide', 'js_composer' ) => 'flexslider_slide',
				__( 'Nivo slider', 'js_composer' ) => 'nivo',
			),
			'description' => __( 'Select slider type.', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Slider count', 'js_composer' ),
			'param_name' => 'count',
			'value' => 3,
			'description' => __( 'Enter number of slides to display (Note: Enter "All" to display all slides).', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Auto rotate', 'js_composer' ),
			'param_name' => 'interval',
			'value' => array(
				3,
				5,
				10,
				15,
				__( 'Disable', 'js_composer' ) => 0,
			),
			'description' => __( 'Auto rotate slides each X seconds.', 'js_composer' ),
		),
		array(
			'type' => 'posttypes',
			'heading' => __( 'Post types', 'js_composer' ),
			'param_name' => 'posttypes',
			'description' => __( 'Select source for slider.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Description', 'js_composer' ),
			'param_name' => 'slides_content',
			'value' => array(
				__( 'No description', 'js_composer' ) => '',
				__( 'Teaser (Excerpt)', 'js_composer' ) => 'teaser',
			),
			'description' => __( 'Select source to use for description (Note: some sliders do not support it).', 'js_composer' ),
			'dependency' => array(
				'element' => 'type',
				'value' => array(
					'flexslider_fade',
					'flexslider_slide',
				),
			),
		),
		array(
			'type' => 'checkbox',
			'heading' => __( 'Output post title?', 'js_composer' ),
			'param_name' => 'slides_title',
			'description' => __( 'If selected, title will be printed before the teaser text.', 'js_composer' ),
			'value' => array( __( 'Yes', 'js_composer' ) => true ),
			'dependency' => array(
				'element' => 'slides_content',
				'value' => array( 'teaser' ),
			),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Link', 'js_composer' ),
			'param_name' => 'link',
			'value' => array(
				__( 'Link to post', 'js_composer' ) => 'link_post',
				__( 'Link to bigger image', 'js_composer' ) => 'link_image',
				__( 'Open custom links', 'js_composer' ) => 'custom_link',
				__( 'No link', 'js_composer' ) => 'link_no',
			),
			'description' => __( 'Link type.', 'js_composer' ),
		),
		array(
			'type' => 'exploded_textarea_safe',
			'heading' => __( 'Custom links', 'js_composer' ),
			'param_name' => 'custom_links',
			'value' => site_url() . '/',
			'dependency' => array(
				'element' => 'link',
				'value' => 'custom_link',
			),
			'description' => __( 'Enter links for each slide here. Divide links with linebreaks (Enter).', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Thumbnail size', 'js_composer' ),
			'param_name' => 'thumb_size',
			'value' => 'medium',
			'description' => __( 'Enter thumbnail size. Example: thumbnail, medium, large, full or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height) . ', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Post/Page IDs', 'js_composer' ),
			'param_name' => 'posts_in',
			'description' => __( 'Enter page/posts IDs to display only those records (Note: separate values by commas (,)). Use this field in conjunction with "Post types" field.', 'js_composer' ),
		),
		array(
			'type' => 'exploded_textarea_safe',
			'heading' => __( 'Categories', 'js_composer' ),
			'param_name' => 'categories',
			'description' => __( 'Enter categories by names to narrow output (Note: only listed categories will be displayed, divide categories with linebreak (Enter)).', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Order by', 'js_composer' ),
			'param_name' => 'orderby',
			'value' => array(
				'',
				__( 'Date', 'js_composer' ) => 'date',
				__( 'ID', 'js_composer' ) => 'ID',
				__( 'Author', 'js_composer' ) => 'author',
				__( 'Title', 'js_composer' ) => 'title',
				__( 'Modified', 'js_composer' ) => 'modified',
				__( 'Random', 'js_composer' ) => 'rand',
				__( 'Comment count', 'js_composer' ) => 'comment_count',
				__( 'Menu order', 'js_composer' ) => 'menu_order',
			),
			'description' => sprintf( __( 'Select how to sort retrieved posts. More at %s.', 'js_composer' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Sort order', 'js_composer' ),
			'param_name' => 'order',
			'value' => array(
				__( 'Descending', 'js_composer' ) => 'DESC',
				__( 'Ascending', 'js_composer' ) => 'ASC',
			),
			'description' => sprintf( __( 'Select ascending or descending order. More at %s.', 'js_composer' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
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
		array(
			'type' => 'css_editor',
			'heading' => __( 'CSS box', 'js_composer' ),
			'param_name' => 'css',
			'group' => __( 'Design Options', 'js_composer' ),
		),
	),
);
