<?php 

return array(
		  "name" => esc_html__("Fancy Box", "js_composer"),
		  "base" => "fancy_box",
		  "icon" => "icon-wpb-fancy-box",
		  "category" => esc_html__('Nectar Elements', 'js_composer'),
		  "description" => esc_html__('Add a fancy box element', 'js_composer'),
		  "params" => array(
				array(
			  "type" => "dropdown",
			  "heading" => esc_html__("Style", "js_composer"),
			  "param_name" => "box_style",
			  "value" => array(
					 "Bottom Color Bar Hover Effect" => "default",
					 "Color Box Hover Effect" => "color_box_hover",
					 "Color Box Basic" => "color_box_basic",
					 "Parallax Hover Effect" => "parallax_hover",
			   ),
			  'save_always' => true,
			  'description' => __( 'Choose your desired style here.', 'js_composer' ),
			  ),
				array(
					'type' => 'dropdown',
					'heading' => __( 'Icon library', 'js_composer' ),
					'value' => array(
						__( 'None', 'js_composer' ) => 'none',
						__( 'Font Awesome', 'js_composer' ) => 'fontawesome',
						__( 'Iconsmind', 'js_composer' ) => 'iconsmind',
						__( 'Linea', 'js_composer' ) => 'linea',
						__( 'Steadysets', 'js_composer' ) => 'steadysets',
						__( 'Linecons', 'js_composer' ) => 'linecons',
					),
					'save_always' => true,
					'param_name' => 'icon_family',
					'description' => __( 'Select icon library.', 'js_composer' ),
				),
				array(
			      "type" => "iconpicker",
			      "heading" => esc_html__("Icon", "js_composer"),
			      "param_name" => "icon_fontawesome",
			      "settings" => array( "iconsPerPage" => 4000),
			      "dependency" => array('element' => "icon_family", 'emptyIcon' => false, 'value' => 'fontawesome'),
			      "description" => esc_html__("Select icon from library.", "js_composer")
			    ),
			    array(
			      "type" => "iconpicker",
			      "heading" => esc_html__("Icon", "js_composer"),
			      "param_name" => "icon_iconsmind",
			      "settings" => array( 'type' => 'iconsmind', 'emptyIcon' => false, "iconsPerPage" => 4000),
			      "dependency" => array('element' => "icon_family", 'value' => 'iconsmind'),
			      "description" => esc_html__("Select icon from library.", "js_composer")
			    ),
			    array(
			      "type" => "iconpicker",
			      "heading" => esc_html__("Icon", "js_composer"),
			      "param_name" => "icon_linea",
			      "settings" => array( 'type' => 'linea', "emptyIcon" => true, "iconsPerPage" => 4000),
			      "dependency" => Array('element' => "icon_family", 'value' => 'linea'),
			      "description" => esc_html__("Select icon from library.", "js_composer")
			    ),
			    array(
			      "type" => "iconpicker",
			      "heading" => esc_html__("Icon", "js_composer"),
			      "param_name" => "icon_linecons",
			      "settings" => array( 'type' => 'linecons', 'emptyIcon' => false, "iconsPerPage" => 4000),
			      "dependency" => array('element' => "icon_family", 'value' => 'linecons'),
			      "description" => esc_html__("Select icon from library.", "js_composer")
			    ),
			    array(
			      "type" => "iconpicker",
			      "heading" => esc_html__("Icon", "js_composer"),
			      "param_name" => "icon_steadysets",
			      "settings" => array( 'type' => 'steadysets', 'emptyIcon' => false, "iconsPerPage" => 4000),
			      "dependency" => array('element' => "icon_family", 'value' => 'steadysets'),
			      "description" => esc_html__("Select icon from library.", "js_composer")
			    ),
			    array(
			      "type" => "textfield",
			      "heading" => esc_html__("Icon Size", "js_composer"),
			      "param_name" => "icon_size",
						"dependency" => array('element' => "icon_family", 'value' => array('fontawesome','iconsmind', 'linea', 'steadysets', 'linecons')),
			      "description" => esc_html__("Don't include \"px\" in your string. e.g. 40 - the default is 50" , "js_composer")
			    ),
		 	 array(
		      "type" => "fws_image",
		      "heading" => esc_html__("Image", "js_composer"),
		      "param_name" => "image_url",
		      "value" => "",
		      "description" => esc_html__("Select a background image from the media library.", "js_composer")
		    ),
		    array(
		      "type" => "textarea_html",
		      "heading" => esc_html__("Box Content", "js_composer"),
		      "param_name" => "content",
		      "admin_label" => true,
		      "description" => esc_html__("Please enter the text desired for your box", "js_composer")
		    ),
		    array(
		      "type" => "textfield",
		      "heading" => esc_html__("Link URL", "js_composer"),
		      "param_name" => "link_url",
		      "admin_label" => false,
		      "description" => esc_html__("Please enter the URL you would like for your box to link to", "js_composer")
		    ),
		    array(
		       "type" => "checkbox",
			  "class" => "",
			  "heading" => "Open Link In New Tab",
			  "value" => array("Yes, please" => "true" ),
			  "param_name" => "link_new_tab",
			  "description" => "",
		       "dependency" => Array('element' => "link_url", 'not_empty' => true)
		    ),
		     array(
		      "type" => "textfield",
		      "heading" => esc_html__("Link Text", "js_composer"),
		      "param_name" => "link_text",
		      "admin_label" => false,
					"dependency" => array('element' => "box_style", 'value' => 'default'),
		      "description" => esc_html__("Please enter the text that will be displayed for your box link", "js_composer")
		    ),
		     array(
		      "type" => "textfield",
		      "heading" => esc_html__("Min Height", "js_composer"),
		      "param_name" => "min_height",
		      "admin_label" => false,
		      "description" => esc_html__("Please enter the minimum height you would like for you box. Enter in number of pixels - Don't enter \"px\", default is \"300\"", "js_composer")
		    ),
		    array(
			  "type" => "dropdown",
			  "heading" => esc_html__("Box Color", "js_composer"),
			  "param_name" => "color",
			  "value" => array(
				 "Accent Color" => "Accent-Color",
				 "Extra Color-1" => "Extra-Color-1",
				 "Extra Color-2" => "Extra-Color-2",	
				 "Extra Color-3" => "Extra-Color-3",
				 "Color Gradient 1" => "extra-color-gradient-1",
				 "Color Gradient 2" => "extra-color-gradient-2"
			   ),
			  'save_always' => true,
				"dependency" => array('element' => "box_style", 'value' => array('default','color_box_hover')),
			  'description' => __( 'Choose a color from your','salient') . ' <a target="_blank" href="'. esc_url(admin_url()) .'?page=Salient&tab=6"> ' . esc_html__('globally defined color scheme','salient') . '</a>',
			),
			array(
			 "type" => "colorpicker",
			 "class" => "",
			 "heading" => "Box Color",
			 "param_name" => "box_color",
			 "value" => "",
			 "dependency" => array('element' => "box_style", 'value' => array('color_box_basic')),
			 "description" => "If left blank this will default to your theme accent color."
		  ),
			array(
			 "type" => "colorpicker",
			 "class" => "",
			 "heading" => "Content Color",
			 "param_name" => "content_color",
			 "dependency" => array('element' => "box_style", 'value' => array('color_box_basic')),
			 "value" => "",
			 "description" => "If left blank this will default to white."
		  ),
			array(
			"type" => "dropdown",
			"heading" => esc_html__("Box Color Opacity", "js_composer"),
			"param_name" => "box_color_opacity",
			"value" => array(
				 "1" => "1",
				 "0.9" => "0.9",
				 "0.8" => "0.8",
				 "0.7" => "0.7",
				 "0.6" => "0.6",
				 "0.5" => "0.5",
				 "0.4" => "0.4",
				 "0.3" => "0.3",
				 "0.2" => "0.2",
				 "0.1" => "0.1",
				 "0" => "0",
			 ),
			 "dependency" => array('element' => "box_style", 'value' => array('color_box_basic')),
			 "description" => "Lowering this will allow the color to be overlaid on top of the image background (if supplied).",
			'save_always' => true,
			),
			array(
			"type" => "dropdown",
			"heading" => esc_html__("Content Alignment", "js_composer"),
			"param_name" => "box_alignment",
			"value" => array(
				 "Left" => "left",
				 "Center" => "center",
				 "Right" => "right",
			 ),
			 "dependency" => array('element' => "box_style", 'value' => array('color_box_basic','color_box_hover')),
			'save_always' => true,
			),
			
			array(
				"type" => "checkbox",
				"class" => "",
				"dependency" => array('element' => "box_style", 'value' => array('color_box_hover')),
				"heading" => "Add Border",
				"value" => array("Enable Fancy Box Border?" => "true" ),
				"param_name" => "enable_border",
				"description" => "This will add a minimal border to show the fancy box area before hovering"
			),
			
			array(
				"type" => "checkbox",
				"class" => "",
				"heading" => "Enable Animation",
				"value" => array("Enable Box Animation?" => "true" ),
				"param_name" => "enable_animation",
				"description" => ""
			),

			array(
				"type" => "dropdown",
				"class" => "",
				'save_always' => true,
				"heading" => "Animation",
				"param_name" => "animation",
				"value" => array(
					 "None" => "none",
				     "Fade In" => "fade-in",
			  		 "Fade In From Left" => "fade-in-from-left",
			  		 "Fade In Right" => "fade-in-from-right",
			  		 "Fade In From Bottom" => "fade-in-from-bottom",
			  		 "Grow In" => "grow-in",
			  		 "Flip In Horizontal" => "flip-in",
			  		 "Flip In Vertical" => "flip-in-vertical"
				),
				"dependency" => Array('element' => "enable_animation", 'not_empty' => true)
			),

			array(
				"type" => "textfield",
				"class" => "",
				"heading" => "Animation Delay",
				"param_name" => "delay",
				"admin_label" => false,
				"description" => esc_html__("Enter delay (in milliseconds) if needed e.g. 150. This parameter comes in handy when creating the animate in \"one by one\" effect.", "js_composer"),
				"dependency" => Array('element' => "enable_animation", 'not_empty' => true)
			),
			
			 array(
				'type' => 'css_editor',
				'heading' => 'Css' ,
				'param_name' => 'css',
				'group' => 'Advanced Spacing',
			)
			
		  )
		);

?>