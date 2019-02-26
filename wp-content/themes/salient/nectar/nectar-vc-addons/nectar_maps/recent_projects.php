<?php 

$is_admin = is_admin();

$portfolio_types = ($is_admin) ? get_terms('project-type') : array('All' => 'all');

$types_options = array("All" => "all");
$types_options_2 = array("Default" => "default");

if($is_admin) {
	foreach ($portfolio_types as $type) {
		$types_options[$type->name] = $type->slug;
		$types_options_2[$type->name] = $type->slug;
	}

} else {
	$types_options['All'] = 'all';
	$types_options_2['All'] = 'all';
}
	
return array(
		  "name" => esc_html__("Recent Projects", "js_composer"),
		  "base" => "recent_projects",
		  "weight" => 8,
		  "icon" => "icon-wpb-recent-projects",
		  "category" => esc_html__('Nectar Elements', 'js_composer'),
		  "description" => esc_html__('Show off some recent projects', 'js_composer'),
		  "params" => array(
		    array(
			  "type" => "dropdown_multi",
			  "heading" => esc_html__("Portfolio Categories", "js_composer"),
			  "param_name" => "category",
			  "admin_label" => true,
			  "value" => $types_options,
			  'save_always' => true,
			  "description" => esc_html__("Please select the categories you would like to display for your recent projects carousel. You can select multiple categories too (ctrl + click on PC and command + click on Mac).", "js_composer")
			),
		    array(
			  "type" => "dropdown",
			  "heading" => esc_html__("Project Style", "js_composer"),
			  "param_name" => "project_style",
			  "admin_label" => true,
			  "value" => array(
				    "Meta below thumb w/ links on hover" => "1",
				    "Meta on hover + entire thumb link" => "2",
				    "Title overlaid w/ zoom effect on hover" => "3",
				    "Meta from bottom on hover + entire thumb link" => "4",
				    "Fullscreen Zoom Slider" => 'fullscreen_zoom_slider'
				),
			  'save_always' => true,
			  "description" => esc_html__("Please select the style you would like your projects to display in ", "js_composer")
			),
			array(
			  "type" => "dropdown",
			  "heading" => esc_html__("Slider Controls", "js_composer"),
			  "param_name" => "slider_controls",
			  "admin_label" => true,
			   "dependency" => Array('element' => "project_style", 'value' => array('fullscreen_zoom_slider')),
			  "value" => array(
				    "Prev/Nect Arrows" => "arrows",
				    "Pagination Lines" => "pagination",
				    "Both" => "both",
				),
			  'save_always' => true,
			  "description" => esc_html__("Please select the controls you would like your slider to use ", "js_composer")
			),
			array(
			  "type" => "dropdown",
			  "heading" => esc_html__("Slider Text Color", "js_composer"),
			  "param_name" => "slider_text_color",
			   "dependency" => Array('element' => "project_style", 'value' => array('fullscreen_zoom_slider')),
			  "admin_label" => true,
			  "value" => array(
				    "Light" => "light",
				    "Dark" => "dark"
				),
			  'save_always' => true,
			  "description" => esc_html__("Please select the color scheme that will be used for your slider text/controls ", "js_composer")
			),
			array(
			  "type" => "dropdown",
			  "heading" => esc_html__("Overlay Strength", "js_composer"),
			  "param_name" => "overlay_strength",
			  "admin_label" => true,
			  "value" => array(
				    "0" => "0",
				    "0.1" => "0.1",
				    "0.2" => "0.2",
				    "0.3" => "0.3",
				    "0.4" => '0.4',
				    "0.5" => '0.5',
				    "0.6" => '0.6',
				    "0.7" => '0.7',
				    "0.8" => '0.8',
				    "0.9" => '0.9',
				    "1" => '1'
				),
			  'save_always' => true,
			  "dependency" => Array('element' => "project_style", 'value' => array('fullscreen_zoom_slider')),
			  "description" => esc_html__("Please select the strength you would like for the image color overlay on your projects ", "js_composer")
			),
			array(
		      "type" => "textfield",
		      "heading" => esc_html__("Custom Link Text", "js_composer"),
		      "param_name" => "custom_link_text",
		      "value" => '',
		      "dependency" => Array('element' => "project_style", 'value' => array('fullscreen_zoom_slider')),
		      "description" => esc_html__("The default text is \"View Project\". If you would like to use alternate text, enter it here.", "js_composer")
		  ),
			array(
		      "type" => 'checkbox',
		      "heading" => esc_html__("Display Project Excerpt", "js_composer"),
		      "param_name" => "display_project_excerpt",
		      "description" => esc_html__("This will add the project excerpt below the project title on your slider", "js_composer"),
		      "value" => Array(esc_html__("Yes, please", "js_composer") => 'true'),
		      "dependency" => Array('element' => "project_style", 'value' => array('fullscreen_zoom_slider')),
		    ),
			array(
		      "type" => "textfield",
		      "heading" => esc_html__("Auto rotate", "js_composer"),
		      "param_name" => "autorotate",
		      "value" => '',
		      "dependency" => Array('element' => "project_style", 'value' => array('fullscreen_zoom_slider')),
		      "description" => esc_html__("If you would like this to auto rotate, enter the rotation speed in miliseconds here. i.e 5000", "js_composer")
		    ),
			array(
		      "type" => 'checkbox',
		      "heading" => esc_html__("Full Width Carousel", "js_composer"),
		      "param_name" => "full_width",
		      "description" => esc_html__("This will make your carousel extend the full width of the page.", "js_composer"),
		      "value" => Array(esc_html__("Yes, please", "js_composer") => 'true'),
		      "dependency" => Array('element' => "project_style", 'value' => array('1','2','3','4')),
		    ),
		    array(
		      "type" => "textfield",
		      "heading" => esc_html__("Heading Text", "js_composer"),
		      "param_name" => "heading",
		      "description" => esc_html__("Enter any text you would like for the heading of your carousel", "js_composer"),
		      "dependency" => Array('element' => "project_style", 'value' => array('1','2','3','4'))
		    ),
			array(
		      "type" => "textfield",
		      "heading" => esc_html__("Page Link Text", "js_composer"),
		      "param_name" => "page_link_text",
		      "description" => esc_html__("This will be the text that is in a link leading users to your desired page (will be omitted for full width carousels and an icon will be used instead)", "js_composer"),
		      "dependency" => Array('element' => "project_style", 'value' => array('1','2','3','4'))
		    ),
		    array(
		      "type" => "textfield",
		      "heading" => esc_html__("Page Link URL", "js_composer"),
		      "param_name" => "page_link_url",
		      "description" => esc_html__("Enter portfolio page URL you would like to link to. Remember to include \"http://\"!", "js_composer"),
		      "dependency" => Array('element' => "project_style", 'value' => array('1','2','3','4'))
		    ),	
		    array(
			  "type" => "dropdown",
			  "heading" => esc_html__("Controls & Text Color", "js_composer"),
			  "param_name" => "control_text_color",
			  "value" => array(
				    "Dark" => "dark",
				    "Light" => "light",
				),
			  'save_always' => true,
			  "description" => esc_html__("Please select the color you desire for your carousel controls/heading text.", "js_composer"),
			  "dependency" => Array('element' => "project_style", 'value' => array('1','2','3','4'))
			),
		    array(
		      "type" => 'checkbox',
		      "heading" => esc_html__("Hide Carousel Controls", "js_composer"),
		      "param_name" => "hide_controls",
		      "description" => esc_html__("Checking this box will remove the controls from your carousel", "js_composer"),
		      "value" => Array(esc_html__("Yes, please", "js_composer") => 'true'),
		      "dependency" => Array('element' => "project_style", 'value' => array('1','2','3','4'))
		    ),
		    array(
		      "type" => "textfield",
		      "heading" => esc_html__("Number of Projects To Show", "js_composer"),
		      "param_name" => "number_to_display",
		      "description" => esc_html__("Enter as a number example \"6\"", "js_composer")
		    ),
				array(
		      "type" => "textfield",
		      "heading" => esc_html__("Project Offset", "js_composer"),
		      "param_name" => "project_offset",
		      "description" => esc_html__("Optioinally enter a number e.g. \"2\" to offset your projects by", "js_composer")
		    ),
		    array(
		      "type" => 'checkbox',
		      "heading" => esc_html__("Lightbox Only", "js_composer"),
		      "param_name" => "lightbox_only",
		      "description" => esc_html__("This will remove the single project page from being accessible thus rendering your portfolio into only a gallery.", "js_composer"),
		      "value" => Array(esc_html__("Yes, please", "js_composer") => 'true'),
		      "dependency" => Array('element' => "project_style", 'value' => array('1','2','3','4'))
		    )
		  )
		);

?>