<?php 

return array(
			"name" => "Morphing Outline",
			"base" => "morphing_outline",
			"icon" => "icon-wpb-morphing-outline",
			"allowed_container_element" => 'vc_row',
			"category" => esc_html__('Nectar Elements', 'js_composer'),
			"description" => esc_html__('Wrap some text in a unqiue way to grab attention', 'js_composer'),
			"params" => array(
				array(
			      "type" => "textarea",
			      "holder" => "div",
			      "heading" => esc_html__("Text Content", "js_composer"),
			      "param_name" => "content",
			      "value" => '',
			      "description" => esc_html__("Enter the text that will be wrapped here", "js_composer"),
			      "admin_label" => false
			    ),
				array(
					"type" => "textfield",
					"holder" => "div",
					"class" => "",
					"heading" => "Border Thickness",
					"param_name" => "border_thickness",
					"description" => "Don't include \"px\" in your string - default is \"5\"",
					"admin_label" => false
				),
				array(
					"type" => "colorpicker",
					"class" => "",
					"heading" => "Starting Color",
					"param_name" => "starting_color",
					"value" => "",
					"description" => ""
				),
				array(
					"type" => "colorpicker",
					"class" => "",
					"heading" => "Hover Color",
					"param_name" => "hover_color",
					"value" => "",
					"description" => ""
				)

			)
	);

?>