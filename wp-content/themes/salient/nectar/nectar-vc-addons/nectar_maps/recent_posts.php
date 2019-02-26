<?php 

$is_admin = is_admin();

$blog_types = ($is_admin) ? get_categories() : array('All' => 'all');

$blog_options = array("All" => "all");

if($is_admin) {
	foreach ($blog_types as $type) {
		if(isset($type->name) && isset($type->slug))
			$blog_options[htmlspecialchars($type->name)] = htmlspecialchars($type->slug);
	}
} else {
	$blog_options['All'] = 'all';
}
		
return array(
		  "name" => esc_html__("Recent Posts", "js_composer"),
		  "base" => "recent_posts",
		  "weight" => 8,
		  "icon" => "icon-wpb-recent-posts",
		  "category" => __('Nectar Elements', 'js_composer'),
		  "description" => __('Display your recent blog posts', 'js_composer'),
		  "params" => array(
		  	array(
			  "type" => "dropdown",
			  "heading" => esc_html__("Style", "js_composer"),
			  "param_name" => "style",
			  "admin_label" => true,
			  "value" => array(	
				    'Default' => 'default',
				    'Minimal' => 'minimal',
				    'Minimal - Title Only' => 'title_only',
				    'Classic Enhanced' => 'classic_enhanced',
				    'Classic Enhanced Alt' => 'classic_enhanced_alt',
						'List With Featured First Row' => 'list_featured_first_row',
						'List With Tall Featured First Row ' => 'list_featured_first_row_tall',
				    'Slider' => 'slider',
						'Slider Multiple Visible' => 'slider_multiple_visible',
						'Single Large Featured' => 'single_large_featured',
						'Multiple Large Featured' => 'multiple_large_featured'
				),
			  'save_always' => true,
			  "description" => esc_html__("Please select desired style here.", "js_composer")
			),
			array(
			  "type" => "dropdown",
			  "heading" => esc_html__("Color Scheme", "js_composer"),
			  "param_name" => "color_scheme",
			  "admin_label" => true,
			  "value" => array(	
				    'Light' => 'light',
				    'Dark' => 'dark',
				),
			  "dependency" => Array('element' => "style", 'value' => array('classic_enhanced')),
			  'save_always' => true,
			  "description" => esc_html__("Please select your desired coloring here.", "js_composer")
			),
			array(
		      "type" => "textfield",
		      "heading" => esc_html__("Slider Height", "js_composer"),
		      "param_name" => "slider_size",
		      "admin_label" => false,
		      "dependency" => Array('element' => "style", 'value' => 'slider'),
		      "description" => esc_html__("Don't include \"px\" in your string. e.g. 650", "js_composer")
		    ),
			array(
			  "type" => "dropdown_multi",
			  "heading" => esc_html__("Blog Categories", "js_composer"),
			  "param_name" => "category",
			  "admin_label" => true,
			  "value" => $blog_options,
			  'save_always' => true,
			  "description" => esc_html__("Please select the categories you would like to display in your recent posts. You can select multiple categories too (ctrl + click on PC and command + click on Mac).", "js_composer")
			),
			array(
			  "type" => "dropdown",
			  "heading" => esc_html__("Number Of Columns", "js_composer"),
			  "param_name" => "columns",
			  "admin_label" => false,
			  "value" => array(
			  	'4' => '4',
			  	'3' => '3',
			  	'2' => '2',
			  	'1' => '1'
			  ),
			  "dependency" => Array('element' => "style", 'value' => array('default','minimal','title_only','classic_enhanced', 'classic_enhanced_alt', 'list_featured_first_row', 'list_featured_first_row_tall', 'slider_multiple_visible')),
			  'save_always' => true,
			  "description" => esc_html__("Please select the number of posts you would like to display.", "js_composer")
			),
			array(
		      "type" => "textfield",
		      "heading" => esc_html__("Number Of Posts", "js_composer"),
		      "param_name" => "posts_per_page",
					"dependency" => Array('element' => "style", 'value' => array('default','minimal','title_only','classic_enhanced', 'classic_enhanced_alt','slider', 'slider_multiple_visible', 'list_featured_first_row',  'list_featured_first_row_tall')),
		      "description" => esc_html__("How many posts would you like to display? Enter as a number example \"4\"", "js_composer")
		    ),
				array(
				  "type" => "dropdown",
				  "heading" => esc_html__("Number Of Posts", "js_composer"),
				  "param_name" => "multiple_large_featured_num",
				  "admin_label" => false,
				  "value" => array(
				  	'4' => '4',
				  	'3' => '3',
				  	'2' => '2',
				  ),
				  "dependency" => Array('element' => "style", 'value' => array('multiple_large_featured')),
				  'save_always' => true,
				  "description" => esc_html__("Please select the number of posts you would like to display.", "js_composer")
				),
		    array(
		      "type" => "textfield",
		      "heading" => esc_html__("Post Offset", "js_composer"),
		      "param_name" => "post_offset",
		      "description" => esc_html__("Optioinally enter a number e.g. \"2\" to offset your posts by - useful for when you're using multiple styles of this element on the same page and would like them to no show duplicate posts", "js_composer")
		    ),
				array(
				  "type" => "dropdown",
				  "heading" => esc_html__("Auto Rotate", "js_composer"),
				  "param_name" => "auto_rotate",
				  "admin_label" => true,
				  "value" => array(	
					    'No Auto Rotate' => 'none',
							'11 Seconds' => '11000',
							'10 Seconds' => '10000',
							'9 Seconds' => '9000',
					    '8 Seconds' => '8000',
							'7 Seconds' => '7000',
							'6 Seconds' => '6000',
							'5 Seconds' => '5000',
							'4 Seconds' => '4000',
							'3 Seconds' => '3000',
					),
				  "dependency" => Array('element' => "style", 'value' => array('multiple_large_featured')),
				  'save_always' => true,
				  "description" => esc_html__("Please select your desired auto rotation timing here", "js_composer")
				),
				array(
				  "type" => "dropdown",
				  "heading" => esc_html__("Top/Bottom Padding", "js_composer"),
				  "param_name" => "large_featured_padding",
				  "admin_label" => false,
				  "value" => array(
				  	'20%' => '20%',
				  	'18%' => '18%',
				  	'16%' => '16%',
						'14%' => '14%',
						'12%' => '12%',
						'10%' => '10%',
						'8%' => '8%',
						'6%' => '6%',
				  ),
				  "dependency" => Array('element' => "style", 'value' => array('single_large_featured','multiple_large_featured')),
				  'save_always' => true,
				  "description" => esc_html__("The % value will be applied as padding to the top and bottom of your featured post(s)", "js_composer")
				),
				array(
				  "type" => "dropdown",
				  "heading" => esc_html__("Navigation Location", "js_composer"),
				  "param_name" => "mlf_navigation_location",
				  "admin_label" => false,
				  "value" => array(
				  	'On Side' => 'side',
				  	'On Bottom' => 'bottom',
				  ),
				  "dependency" => Array('element' => "style", 'value' => array('multiple_large_featured')),
				  'save_always' => true,
				  "description" => esc_html__("Please select where you would like the navigation to display", "js_composer")
				),
			array(
		      "type" => 'checkbox',
		      "heading" => esc_html__("Enable Title Labels", "js_composer"),
		      "param_name" => "title_labels",
		      "description" => esc_html__("These labels are defined by you in the \"Blog Options\" tab of your theme options panel.", "js_composer"),
		      "value" => Array(esc_html__("Yes, please", "js_composer") => 'true'),
		      "dependency" => Array('element' => "style", 'value' => 'default')
		    ),
				array(
					'type' => 'dropdown',
					'heading' => __( 'Button Color', 'js_composer' ),
					'value' => array(
						"Accent Color" => "Accent-Color",
						"Extra Color 1" => "Extra-Color-1",
						"Extra Color 2" => "Extra-Color-2",	
						"Extra Color 3" => "Extra-Color-3",
						"Color Gradient 1" => "extra-color-gradient-1",
				 		"Color Gradient 2" => "extra-color-gradient-2",
					),
					'save_always' => true,
					'param_name' => 'button_color',
					"dependency" => Array('element' => "style", 'value' => array('single_large_featured','multiple_large_featured', 'slider_multiple_visible')),
					'description' => __( 'Choose a color from your','salient') . ' <a target="_blank" href="'. esc_url(admin_url()) .'?page=Salient&tab=6"> ' . __('globally defined color scheme','salient') . '</a>',
				),
				array(
					'type' => 'dropdown',
					'heading' => __( 'Hover Shadow Type', 'js_composer' ),
					'value' => array(
						"Inherit Color From Image" => "default",
						"Regular Dark" => "dark",
					),
					'save_always' => true,
					'param_name' => 'hover_shadow_type',
					"dependency" => Array('element' => "style", 'value' => array('slider_multiple_visible') ),
					"description" => esc_html__("Please select your desired shadow color that will appear when hovering over posts.", "js_composer")
				),
				array(
				  "type" => "dropdown",
				  "heading" => esc_html__("BG Overlay", "js_composer"),
				  "param_name" => "bg_overlay",
				  "admin_label" => true,
				  "value" => array(	
					    'Solid' => 'solid_color',
					    'Diagonal Gradient' => 'diagonal_gradient',
					),
				  "dependency" => Array('element' => "style", 'value' => array('single_large_featured','multiple_large_featured')),
				  'save_always' => true,
				  "description" => esc_html__("Please select your desired BG overlay here.", "js_composer")
				),
				array(
				  "type" => "dropdown",
				  "heading" => esc_html__("Order", "js_composer"),
				  "param_name" => "order",
				  "admin_label" => false,
				  "value" => array(
				  	'Descending' => 'DESC',
				  	'Ascending' => 'ASC',
				  ),
				  'save_always' => true,
				  "description" => esc_html__("Designates the ascending or descending order - defaults to descending", "js_composer")
				),
				array(
				  "type" => "dropdown",
				  "heading" => esc_html__("Orderby", "js_composer"),
				  "param_name" => "orderby",
				  "admin_label" => false,
				  "value" => array(
				  	'Date' => 'date',
				  	'Author' => 'author',
				  	'Title' => 'title',
						'Last Modified' => 'modified',
						'Random' => 'rand',
						'Comment Count' => 'comment_count',
						'View Count' => 'view_count'
				  ),
				  'save_always' => true,
				  "description" => esc_html__("Sort retrieved posts by parameter - defaults to date", "js_composer")
				),
				array(
			      "type" => 'checkbox',
			      "heading" => esc_html__("Remove Post Date", "js_composer"),
			      "param_name" => "blog_remove_post_date",
			      "description" => esc_html__("Enable this to remove the date from displaying on your blog layout", "js_composer"),
			      "value" => Array(esc_html__("Yes, please", "js_composer") => 'true')
			    ),
				array(
			      "type" => 'checkbox',
			      "heading" => esc_html__("Remove Post Author", "js_composer"),
			      "param_name" => "blog_remove_post_author",
			      "description" => esc_html__("Enable this to remove the author name from displaying on your blog layout", "js_composer"),
			      "value" => Array(esc_html__("Yes, please", "js_composer") => 'true')
			    ),
				array(
						"type" => 'checkbox',
						"heading" => esc_html__("Remove Comment Number", "js_composer"),
						"param_name" => "blog_remove_post_comment_number",
						"description" => esc_html__("Enable this to remove the comment count from displaying on your blog layout", "js_composer"),
						"value" => Array(esc_html__("Yes, please", "js_composer") => 'true')
					),
				array(
						"type" => 'checkbox',
						"heading" => esc_html__("Remove Nectar Love Button", "js_composer"),
						"param_name" => "blog_remove_post_nectar_love",
						"description" => esc_html__("Enable this to remove the nectar love button from displaying on your blog layout", "js_composer"),
						"value" => Array(esc_html__("Yes, please", "js_composer") => 'true')
					),
					
		  )
		);

?>