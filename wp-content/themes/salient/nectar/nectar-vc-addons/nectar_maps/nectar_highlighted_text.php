<?php 

return array(
			"name" => "Highlighted Text",
			"base" => "nectar_highlighted_text",
			"icon" => "icon-wpb-nectar-gradient-text",
			"allowed_container_element" => 'vc_row',
			"category" => esc_html__('Nectar Elements', 'js_composer'),
			"description" => esc_html__('Highlight text in an animated manner', 'js_composer'),
			"params" => array(
        array(
  			 "type" => "colorpicker",
  			 "class" => "",
  			 "heading" => "Highlight Color",
  			 "param_name" => "highlight_color",
  			 "value" => "",
  			 "description" => "If left blank this will default to a desaturated variant of your defined theme accent color."
  		  ),
				array(
			      "type" => "textarea_html",
			      "holder" => "div",
			      "heading" => esc_html__("Text Content", "js_composer"),
			      "param_name" => "content",
			      "value" => '',
			      "description" => esc_html__("Any text that is wrapped in italics will get an animated highlight. Use the \"I\" button on the editor above when highlighting text to toggle italics.", "js_composer"),
			      "admin_label" => false
			    ),
          array(
    	      "type" => "dropdown",
    	      "heading" => esc_html__("Style", "js_composer"),
    	      "param_name" => "style",
    	      "value" => array(
      				"Full Text BG Cover" => "full_text",
      				"Fancy Underline" => "half_text"
      			),
    	      'save_always' => true,
    	      "description" => esc_html__("Please select the style you would like for your hightlights.", "js_composer")
    	    )

			)
	);

?>