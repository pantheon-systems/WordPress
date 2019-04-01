<?php 

return array(
		  "name" => esc_html__("Animated Title", "js_composer"),
		  "base" => "nectar_animated_title",
		  "icon" => "icon-wpb-nectar-gradient-text",
		  "category" => esc_html__('Nectar Elements', 'js_composer'),
		  "description" => esc_html__('Add a title with animation', 'js_composer'),
		  "params" => array(
		  	array(
			"type" => "dropdown",
			"class" => "",
			'save_always' => true,
			"heading" => "Heading Tag",
			"param_name" => "heading_tag",
			"value" => array(
				"H6" => "h6",
				"H5" => "h5",
				"H4" => "h4",
				"H3" => "h3",
				"H2" => "h2",
				"H1" => "h1"
			)),
			array(
				"type" => "dropdown",
				"class" => "",
				'save_always' => true,
				"heading" => "Title Style",
				"param_name" => "style",
				"admin_label" => false,
				"value" => array(
					"Color Strip Reveal" => "color-strip-reveal",
					"Hinge Drop" => "hinge-drop",
				),
				"description" => "Gradient colors are only available for compatible effects"
			),
		    array(
				"type" => "dropdown",
				"class" => "",
				'save_always' => true,
				"heading" => "Background Color",
				"param_name" => "color",
				"admin_label" => false,
				"value" => array(
					"Accent Color" => "Accent-Color",
					"Extra Color 1" => "Extra-Color-1",
					"Extra Color 2" => "Extra-Color-2",	
					"Extra Color 3" => "Extra-Color-3"
				),
				'description' => __( 'Choose a color from your','salient') . ' <a target="_blank" href="'. esc_url(admin_url()) .'?page=Salient&tab=6"> ' . esc_html__('globally defined color scheme','salient') . '</a>',
			),
			array(
				"type" => "colorpicker",
				"class" => "",
				"heading" => "Text Color",
				"param_name" => "text_color",
				"value" => "#ffffff",
				"description" => "Select the color your text will display in"
			),
			array(
		      "type" => "textfield",
		      "heading" => esc_html__("Text Content", "js_composer"),
		      "param_name" => "text",
		      "admin_label" => true,
		      "description" => esc_html__("Enter your fancy title text here", "js_composer")
		    )
		 	 
		  )
		);
?>