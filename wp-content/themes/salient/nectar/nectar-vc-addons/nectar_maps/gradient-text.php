<?php 

return array(
		  "name" => esc_html__("Gradient Text", "js_composer"),
		  "base" => "nectar_gradient_text",
		  "icon" => "icon-wpb-nectar-gradient-text",
		  "category" => esc_html__('Nectar Elements', 'js_composer'),
		  "description" => esc_html__('Add text with gradient coloring', 'js_composer'),
		  "params" => array(
		  	array(
			"type" => "dropdown",
			"class" => "",
			'save_always' => true,
			"heading" => "Heading Tag",
			"param_name" => "heading_tag",
			"value" => array(
				"H1" => "h1",
				"H2" => "h2",
				"H3" => "h3",
				"H4" => "h4",
				"H5" => "h5",
				"H6" => "h6"
			)),
		    array(
				"type" => "dropdown",
				"class" => "",
				'save_always' => true,
				"heading" => "Text Color",
				"param_name" => "color",
				"admin_label" => false,
				"value" => array(
					"Color Gradient 1" => "extra-color-gradient-1",
			 		"Color Gradient 2" => "extra-color-gradient-2"
				),
				'description' => __( 'Choose a color from your','salient') . ' <a target="_blank" href="'. esc_url(admin_url()) .'?page=Salient&tab=6"> ' . __('globally defined color scheme','salient') . '</a> <br/> Will fallback to the first color of the gardient on non webkit browsers.',
			),
			array(
				"type" => "dropdown",
				"class" => "",
				'save_always' => true,
				"heading" => "Gradient Direction",
				"param_name" => "gradient_direction",
				"admin_label" => false,
				"value" => array(
					"Horizontal" => "horizontal",
			 		"Diagonal" => "diagonal"
				),
				"description" => "Select your desired gradient direction"
			),
			array(
		      "type" => "textarea",
		      "heading" => esc_html__("Text Content", "js_composer"),
		      "param_name" => "text",
		      "admin_label" => true,
		      "description" => esc_html__("The text that will display with gradient coloring", "js_composer")
		    ),
		    array(
		      "type" => "textfield",
		      "heading" => esc_html__("Margin", "js_composer") . "<span>" . esc_html__("Top", "js_composer") . "</span>",
		      "param_name" => "margin_top",
		      "edit_field_class" => "col-md-2",
		      "description" => esc_html__("." , "js_composer")
		    ),
			 array(
		      "type" => "textfield",
		      "heading" => "<span>" . esc_html__("Right", "js_composer") . "</span>",
		      "param_name" => "margin_right",
		      "edit_field_class" => "col-md-2",
		      "description" => ''
		    ),
			array(
		      "type" => "textfield",
		      "heading" => "<span>" . esc_html__("Bottom", "js_composer") . "</span>",
		      "param_name" => "margin_bottom",
		      "edit_field_class" => "col-md-2",
		      "description" => ''
		    ),
		    array(
		      "type" => "textfield",
		      "heading" => "<span>" . esc_html__("Left", "js_composer") . "</span>",
		      "param_name" => "margin_left",
		      "edit_field_class" => "col-md-2",
		      "description" => ''
		    ),
		 	 
		  )
		);
?>