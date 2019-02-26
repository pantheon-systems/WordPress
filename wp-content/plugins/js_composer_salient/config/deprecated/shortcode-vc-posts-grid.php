<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$vc_layout_sub_controls = array(
	array(
		'link_post',
		__( 'Link to post', 'js_composer' ),
	),
	array(
		'no_link',
		__( 'No link', 'js_composer' ),
	),
	array(
		'link_image',
		__( 'Link to bigger image', 'js_composer' ),
	),
);
return array(
	'name' => __( 'Old Posts Grid', 'js_composer' ),
	'base' => 'vc_posts_grid',
	'content_element' => false,
	'deprecated' => '4.4',
	'icon' => 'icon-wpb-application-icon-large',
	'description' => __( 'Posts in grid view', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => __( 'Widget title', 'js_composer' ),
			'param_name' => 'title',
			'description' => __( 'Enter text used as widget title (Note: located above content element).', 'js_composer' ),
		),
		array(
			'type' => 'loop',
			'heading' => __( 'Grids content', 'js_composer' ),
			'param_name' => 'loop',
			'value' => 'size:10|order_by:date',
			'settings' => array(
				'size' => array(
					'hidden' => false,
					'value' => 10,
				),
				'order_by' => array( 'value' => 'date' ),
			),
			'description' => __( 'Create WordPress loop, to populate content from your site.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Columns count', 'js_composer' ),
			'param_name' => 'grid_columns_count',
			'value' => array(
				6,
				4,
				3,
				2,
				1,
			),
			'std' => 3,
			'admin_label' => true,
			'description' => __( 'Select columns count.', 'js_composer' ),
		),
		array(
			'type' => 'sorted_list',
			'heading' => __( 'Teaser layout', 'js_composer' ),
			'param_name' => 'grid_layout',
			'description' => __( 'Control teasers look. Enable blocks and place them in desired order. Note: This setting can be overrriden on post to post basis.', 'js_composer' ),
			'value' => 'title,image,text',
			'options' => array(
				array(
					'image',
					__( 'Thumbnail', 'js_composer' ),
					$vc_layout_sub_controls,
				),
				array(
					'title',
					__( 'Title', 'js_composer' ),
					$vc_layout_sub_controls,
				),
				array(
					'text',
					__( 'Text', 'js_composer' ),
					array(
						array(
							'excerpt',
							__( 'Teaser/Excerpt', 'js_composer' ),
						),
						array(
							'text',
							__( 'Full content', 'js_composer' ),
						),
					),
				),
				array(
					'link',
					__( 'Read more link', 'js_composer' ),
				),
			),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Link target', 'js_composer' ),
			'param_name' => 'grid_link_target',
			'value' => vc_target_param_list(),
		),
		array(
			'type' => 'checkbox',
			'heading' => __( 'Show filter', 'js_composer' ),
			'param_name' => 'filter',
			'value' => array( __( 'Yes', 'js_composer' ) => 'yes' ),
			'description' => __( 'Select to add animated category filter to your posts grid.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Layout mode', 'js_composer' ),
			'param_name' => 'grid_layout_mode',
			'value' => array(
				__( 'Fit rows', 'js_composer' ) => 'fitRows',
				__( 'Masonry', 'js_composer' ) => 'masonry',
			),
			'description' => __( 'Teaser layout template.', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Thumbnail size', 'js_composer' ),
			'param_name' => 'grid_thumb_size',
			'value' => 'thumbnail',
			'description' => __( 'Enter thumbnail size. Example: thumbnail, medium, large, full or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height) . ', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Extra class name', 'js_composer' ),
			'param_name' => 'el_class',
			'description' => __( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
		),
	),
);
