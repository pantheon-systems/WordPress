<?php

return array(
	  "name" => esc_html__("Call To Action", "js_composer"),
	  "base" => "nectar_cta",
	  "icon" => "icon-cta",
	  "category" => esc_html__('Nectar Elements', 'js_composer'),
	  "description" => esc_html__('minimal & animated', 'js_composer'),
	  "params" => array(
	  	array(
			"type" => "dropdown",
			"class" => "",
			'save_always' => true,
			"heading" => "Style",
			"param_name" => "btn_style",
			"value" => array(
				"See Through Button " => "see-through",
				"Material Button" => "material",
				"Underline" => "underline",
				"Next Section Button" => "next-section"
			)),
			array(
			"type" => "dropdown",
			"class" => "",
			'save_always' => true,
			"heading" => "Heading Tag",
			"dependency" => array('element' => "btn_style", 'value' => array('see-through','material','underline')),
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
	      "type" => "textfield",
	      "heading" => esc_html__("Call to action text", "js_composer"),
	      "param_name" => "text",
	      "admin_label" => true,
				"dependency" => array('element' => "btn_style", 'value' => array('see-through','material','underline')),
	      "description" => esc_html__("The text that will appear before the actual CTA link", "js_composer")
	    ),
	     array(
	      "type" => "textfield",
	      "heading" => esc_html__("Link text", "js_composer"),
	      "param_name" => "link_text",
				"dependency" => array('element' => "btn_style", 'value' => array('see-through','material','underline')),
	      "description" => esc_html__("The text that will be used for the CTA link", "js_composer")
	    ),
	     array(
				"type" => "colorpicker",
				"class" => "",
				"heading" => "CTA Text Color",
				"param_name" => "text_color",
				"value" => "",
				"dependency" => array('element' => "btn_style", 'value' => array('see-through','material','underline')),
				"description" => ""
			),
	      array(
	      "type" => "textfield",
	      "heading" => esc_html__("Link URL", "js_composer"),
	      "param_name" => "url",
				"dependency" => array('element' => "btn_style", 'value' => array('see-through','material','underline')),
	      "description" => esc_html__("The URL that will be used for the link", "js_composer")
	    ),
	      array(
			  "type" => "dropdown",
			  "heading" => esc_html__("Link Type", "js_composer"),
			  "param_name" => "link_type",
			  "value" => array(
				    'Regular (open in same tab)' => 'regular',
				    'Open In New Tab' => 'new_tab',
				),
			  'save_always' => true,
				"dependency" => array('element' => "btn_style", 'value' => array('see-through','material','underline')),
			  "description" => esc_html__("Please select the type of link you will be using.", "js_composer")
			),
			array(
				"type" => "dropdown",
				"heading" => esc_html__("Button Type", "js_composer"),
				"dependency" => array('element' => "btn_style", 'value' => array('next-section')),
				"param_name" => "btn_type",
				"admin_label" => true,
				"value" => array(
					  'Down Arrow Bordered' => 'down-arrow-bordered',
						'Down Arrow Bounce' => 'down-arrow-bounce',
						'Mouse Wheel Scroll Animation' => 'mouse-wheel'
				),
				'save_always' => true
			),
	    array(
			  "type" => "dropdown",
			  "heading" => esc_html__("Alignment", "js_composer"),
			  "param_name" => "alignment",
			  "admin_label" => true,
			  "value" => array(
				    'Left' => 'left',
				    'Center' => 'center',
				    'Right' => 'right',
				),
			  'save_always' => true,
			  "description" => esc_html__("Please select the desired alignment for your CTA", "js_composer")
			),
			array(
				 "type" => "textfield",
				 "heading" => esc_html__("Margin", "js_composer") . "<span>" . esc_html__("Top", "js_composer") . "</span>",
				 "param_name" => "margin_top",
				 "edit_field_class" => "col-md-2",
				 "description" => ''
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
	      array(
	      "type" => "textfield",
	      "heading" => esc_html__("Extra Class Name", "js_composer"),
	      "param_name" => "class",
	      "description" => ''
	    ),
	  )
	);

?>
