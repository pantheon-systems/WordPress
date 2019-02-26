<?php 

return array(
		  "name" => esc_html__("Flip Box", "js_composer"),
		  "base" => "nectar_flip_box",
		  "icon" => "icon-wpb-nectar-flip-box",
		  "category" => esc_html__('Nectar Elements', 'js_composer'),
		  "description" => esc_html__('Add a flip box element', 'js_composer'),
		  "params" => array(
		  	array(
		      "type" => "textarea",
		      "heading" => esc_html__("Front Box Content", "js_composer"),
		      "param_name" => "front_content",
		      "description" => esc_html__("The text that will display on the front of your flip box", "js_composer"),
		      "group" => 'Front Side'
		    ),
		 	  array(
		      "type" => "fws_image",
		      "heading" => esc_html__("Background Image", "js_composer"),
		      "param_name" => "image_url_1",
		      "value" => "",
		      "group" => 'Front Side',
		      "description" => esc_html__("Select a background image from the media library.", "js_composer")
		    ),
		 	array(
				"type" => "colorpicker",
				"class" => "",
				"heading" => "Background Color",
				"group" => 'Front Side',
				"param_name" => "bg_color",
				"value" => "",
				"description" => ""
			),
			 array(
		      "type" => 'checkbox',
		      "heading" => esc_html__("BG Color overlay on BG Image", "js_composer"),
		      "param_name" => "bg_color_overlay",
		      "group" => 'Front Side',
		      "description" => esc_html__("Checking this will overlay your BG color on your BG image", "js_composer"),
		      "value" => Array(esc_html__("Yes", "js_composer") => 'true')
		    ),
			 array(
				"type" => "dropdown",
				"class" => "",
				"group" => 'Front Side',
				"heading" => "Text Color",
				"param_name" => "text_color",
				"value" => array(
					"Dark" => "dark",
					"Light" => "light"
				),
				'save_always' => true
			),	 
			 array(
				'type' => 'dropdown',
				'heading' => __( 'Icon library', 'js_composer' ),
				"group" => 'Front Side',
				'value' => array(
					__( 'Font Awesome', 'js_composer' ) => 'fontawesome',
					__( 'Iconsmind', 'js_composer' ) => 'iconsmind',
					__( 'Linea', 'js_composer' ) => 'linea',
					__( 'Steadysets', 'js_composer' ) => 'steadysets',
				),
				'param_name' => 'icon_family',
				'description' => __( 'Select icon library.', 'js_composer' ),
			),
			array(
		      "type" => "iconpicker",
		      "heading" => esc_html__("Icon Above Title", "js_composer"),
		      "param_name" => "icon_fontawesome",
		      "group" => 'Front Side',
		      "settings" => array( "emptyIcon" => true, "iconsPerPage" => 4000),
		      "dependency" => Array('element' => "icon_family", 'value' => 'fontawesome'),
		      "description" => esc_html__("Select icon from library.", "js_composer")
		    ),
		    array(
		      "type" => "iconpicker",
		      "heading" => esc_html__("Icon", "js_composer"),
		      "param_name" => "icon_iconsmind",
		      "group" => 'Front Side',
		      "settings" => array( 'type' => 'iconsmind', 'emptyIcon' => false, "iconsPerPage" => 4000),
		      "dependency" => array('element' => "icon_family", 'value' => 'iconsmind'),
		      "description" => esc_html__("Select icon from library.", "js_composer")
		    ),
		    array(
		      "type" => "iconpicker",
		      "heading" => esc_html__("Icon Above Title", "js_composer"),
		      "param_name" => "icon_linea",
		      "group" => 'Front Side',
		      "settings" => array( 'type' => 'linea', "emptyIcon" => true, "iconsPerPage" => 4000),
		      "dependency" => Array('element' => "icon_family", 'value' => 'linea'),
		      "description" => esc_html__("Select icon from library.", "js_composer")
		    ),
		    array(
		      "type" => "iconpicker",
		      "heading" => esc_html__("Icon", "js_composer"),
		      "param_name" => "icon_steadysets",
		      "group" => 'Front Side',
		      "settings" => array( 'type' => 'steadysets', 'emptyIcon' => false, "iconsPerPage" => 4000),
		      "dependency" => array('element' => "icon_family", 'value' => 'steadysets'),
		      "description" => esc_html__("Select icon from library.", "js_composer")
		    ),
		    array(
				"type" => "dropdown",
				"class" => "",
				'save_always' => true,
				"heading" => "Icon Color",
				"param_name" => "icon_color",
				"group" => 'Front Side',
				"value" => array(
					"Accent Color" => "Accent-Color",
					"Extra Color 1" => "Extra-Color-1",
					"Extra Color 2" => "Extra-Color-2",	
					"Extra Color 3" => "Extra-Color-3",
					"Color Gradient 1" => "extra-color-gradient-1",
			 		"Color Gradient 2" => "extra-color-gradient-2"
				),
				'description' => __( 'Choose a color from your','salient') . ' <a target="_blank" href="'. esc_url(admin_url()) .'?page=Salient&tab=6"> ' . esc_html__('globally defined color scheme','salient') . '</a>',
			),
			array(
		      "type" => "textfield",
		      "group" => 'Front Side',
		      "heading" => esc_html__("Icon Size", "js_composer"),
		      "param_name" => "icon_size",
		      "description" => esc_html__("Please enter the size for your icon. Enter in number of pixels - Don't enter \"px\", default is \"60\"", "js_composer"),
		      "group" => 'Front Side'
		    ),
			array(
		      "type" => "textarea_html",
		      "heading" => esc_html__("Back Box Content", "js_composer"),
		      "param_name" => "content",
		      "admin_label" => true,
		      "group" => 'Back Side',
		      "description" => esc_html__("The content that will display on the back of your flip box", "js_composer")
		    ),	
		     array(
		      "type" => "fws_image",
		      "heading" => esc_html__("Background Image", "js_composer"),
		      "param_name" => "image_url_2",
		      "value" => "",
		      "group" => 'Back Side',
		      "description" => esc_html__("Select a background image from the media library.", "js_composer")
		    ),
		     array(
				"type" => "colorpicker",
				"class" => "",
				"heading" => "Background Color",
				"group" => 'Back Side',
				"param_name" => "bg_color_2",
				"value" => "",
				"description" => ""
			),
		     array(
		      "type" => 'checkbox',
		      "heading" => esc_html__("BG Color overlay on BG Image", "js_composer"),
		      "param_name" => "bg_color_overlay_2",
		      "group" => 'Back Side',
		      "description" => esc_html__("Checking this will overlay your BG color on your BG image", "js_composer"),
		      "value" => Array(esc_html__("Yes", "js_composer") => 'true')
		    ),
		     array(
				"type" => "dropdown",
				"class" => "",
				"group" => 'Back Side',
				"heading" => "Text Color",
				"param_name" => "text_color_2",
				"value" => array(
					"Dark" => "dark",
					"Light" => "light"
				),
				'save_always' => true
			), 
		     array(
		      "type" => "textfield",
		      "heading" => esc_html__("Min Height", "js_composer"),
		      "param_name" => "min_height",
		      "admin_label" => false,
		      "group" => 'General Settings',
		      "description" => esc_html__("Please enter the minimum height you would like for you box. Enter in number of pixels - Don't enter \"px\", default is \"300\"", "js_composer")
		    ),

			array(
				"type" => "dropdown",
				"class" => "",
				'save_always' => true,
				"heading" => "Horizontal Content Alignment",
				"param_name" => "h_text_align",
				"group" => 'General Settings',
				"value" => array(
					"Left" => "left",
					"Center" => "center",
					"Right" => "right"
				)
			),
			array(
				"type" => "dropdown",
				"class" => "",
				'save_always' => true,
				"heading" => "Vertical Content Alignment",
				"param_name" => "v_text_align",
				"group" => 'General Settings',
				"value" => array(
					"Top" => "top",
					"Center" => "center",
					"Bottom" => "bottom"
				)
			),

			array(
				"type" => "dropdown",
				"class" => "",
				'save_always' => true,
				"heading" => "Flip Direction",
				"param_name" => "flip_direction",
				"group" => 'General Settings',
				"value" => array(
					"Horizontal To Left" => "horizontal-to-left",
					"Horizontal To Right" => "horizontal-to-right",
					"Vertical To Bottom" => "vertical-to-bottom",
					"Vertical To Top" => "vertical-to-top"
				)
			)
		  )
		);

?>