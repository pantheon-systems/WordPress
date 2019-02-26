<?php 

return array(
			"name" => "Divider",
			"base" => "divider",
			"icon" => "icon-wpb-separator",
			"allowed_container_element" => 'vc_row',
			"category" => esc_html__('Nectar Elements', 'js_composer'),
			"description" => esc_html__('Create space between your content', 'js_composer'),
			"params" => array(
				array(
					"type" => "textfield",
					"holder" => "div",
					"class" => "",
					"heading" => "Dividing Height",
					"param_name" => "custom_height",
					"description" => "If you would like to control the specifc number of pixels your divider is, enter it here. Don't enter \"px\", just the numnber e.g. \"20\""
				),
				array(
					"type" => "dropdown",
					"holder" => "div",
					"class" => "",
					"heading" => "Line Type",
					'save_always' => true,
					"param_name" => "line_type",
					"value" => array(
						"No Line" => "No Line",
						"Full Width Line" => "Full Width Line",
						"Small Line" => "Small Line"
					)
				),
				array(
					"type" => "dropdown",
					"holder" => "div",
					"class" => "",
					"heading" => "Line Alignment",
					'save_always' => true,
					"admin_label" => false,
					"param_name" => "line_alignment",
					"dependency" => Array('element' => "line_type", 'value' => array('Small Line')),
					"value" => array(
						"Default" => "default",
						"Center" => "center",
						"Right" => "right"
					)
				),
				array(
				  "type" => "dropdown",
				  "heading" => esc_html__("Line Thickness", "js_composer"),
				  "admin_label" => false,
				  "param_name" => "line_thickness",
				  "value" => array(
					    "1px" => "1",
					    "2px" => "2",
					    "3px" => "3",
					    "4px" => "4",
					    "5px" => "5",
					    "6px" => "6",
					    "7px" => "7",
					    "8px" => "8",
					    "9px" => "9",
					    "10px" => "10"
					),
				  "description" => esc_html__("Please select thickness of your line ", "js_composer"),
				  'save_always' => true,
				  "dependency" => Array('element' => "line_type", 'value' => array('Full Width Line','Small Line'))
				),
				array(
					"type" => "textfield",
					"holder" => "div",
					"admin_label" => false,
					"class" => "",
					"heading" => "Custom Line Width",
					"param_name" => "custom_line_width",
					"dependency" => Array('element' => "line_type", 'value' => array('Small Line')),
					"description" => "If you would like to control the specifc number of pixels that your divider is (widthwise), enter it here. Don't enter \"px\", just the numnber e.g. \"20\""
				),
				 array(
				  "type" => "dropdown",
				  "heading" => esc_html__("Divider Color", "js_composer"),
				  "param_name" => "divider_color",
				  "admin_label" => false,
				  "value" => array(
				     "Default (inherit from row Text Color)" => "default",
					 "Accent Color" => "accent-color",
					 "Extra Color 1" => "extra-color-1",
					 "Extra Color 2" => "extra-color-2",	
					 "Extra Color 3" => "extra-color-3",
					 "Color Gradient 1" => "extra-color-gradient-1",
					 "Color Gradient 2" => "extra-color-gradient-2"
				   ),
				  'save_always' => true,
				  "dependency" => Array('element' => "line_type", 'value' => array('Full Width Line','Small Line')),
				  'description' => __( 'Choose a color from your','salient') . ' <a target="_blank" href="'. esc_url(admin_url()) .'?page=Salient&tab=6"> ' . esc_html__('globally defined color scheme','salient') . '</a>',
				),
				 array(
			      "type" => 'checkbox',
			      "heading" => esc_html__("Animate Line", "js_composer"),
			      "param_name" => "animate",
			      "description" => esc_html__("If selected, the divider line will animate in when scrolled to", "js_composer"),
			      "value" => Array(esc_html__("Yes, please", "js_composer") => 'yes'),
			      "dependency" => Array('element' => "line_type", 'value' => array('Full Width Line','Small Line')),
			    ),
				 array(
			      "type" => "textfield",
			      "heading" => esc_html__("Animation Delay", "js_composer"),
			      "param_name" => "delay",
			      "dependency" => Array('element' => "line_type", 'value' => array('Full Width Line','Small Line')),
			      "description" => esc_html__("Enter delay (in milliseconds) if needed e.g. 150. This parameter comes in handy when creating the animate in \"one by one\" effect.", "js_composer")
			    ),

			)
	);

?>