<?php

	return array(
	  "name" => esc_html__("Single Testimonial", "js_composer"),
	  "base" => "nectar_single_testimonial",
    "icon" => "icon-nectar-single-testimonial",
    "category" => esc_html__('Nectar Elements', 'js_composer'),
    "description" => esc_html__('Styled Quotes', 'js_composer'),
	  "params" => array(
      array(
      "type" => "dropdown",
      "heading" => esc_html__("Style", "js_composer"),
      "param_name" => "testimonial_style",
      "value" => array(
         "Small Modern" => "small_modern",
				 "Big Bold" => "bold",
				 "Basic" => "basic",
				 "Basic - Left Image" => "basic_left_image",
       ),
      'save_always' => true,
      'description' => __( 'Choose your desired style here.', 'js_composer' ),
      ),
      array(
       "type" => "textarea",
       "heading" => esc_html__("Quote", "js_composer"),
       "param_name" => "quote",
       "description" => esc_html__("The testimonial quote", "js_composer")
     ),
	  	array(
			"type" => "fws_image",
			"class" => "",
			"heading" => "Image",
			"value" => "",
			"param_name" => "image",
			"description" => "Add an optional image for the person/company who supplied the testimonial"
		),
		array(
	      "type" => "checkbox",
			  "class" => "",
			  "heading" => "Add Shadow To Image",
			  "value" => array("Yes, please" => "true" ),
			  "param_name" => "add_image_shadow",
			  "dependency" => Array('element' => "image", 'not_empty' => true),
			  "description" => ""
	    ),
    array(
      "type" => "textfield",
      "heading" => esc_html__("Name", "js_composer"),
      "param_name" => "name",
      "admin_label" => true,
      "description" => esc_html__("Name or source of the testimonial", "js_composer")
    ),
    array(
      "type" => "textfield",
      "heading" => esc_html__("Subtitle", "js_composer"),
      "param_name" => "subtitle",
      "admin_label" => false,
      "description" => esc_html__("The optional subtitle that will follow the testimonial name", "js_composer")
    ),
    array(
      "type" => "dropdown",
      "heading" => esc_html__("Added Color", "js_composer"),
      "param_name" => "color",
      "value" => array(
       "Default (inherit from row Text Color)" => "Default",
       "Accent Color" => "Accent-Color",
       "Extra Color-1" => "Extra-Color-1",
       "Extra Color-2" => "Extra-Color-2",	
       "Extra Color-3" => "Extra-Color-3"
       ),
      'save_always' => true,
      "dependency" => array('element' => "testimonial_style", 'value' => array('small_modern','bold')),
      'description' => esc_html__('Choose a color from your','salient') . ' <a target="_blank" href="'. esc_url(admin_url()) .'?page=Salient&tab=6"> ' . esc_html__('globally defined color scheme','salient') . '</a>',
    ),

	  )
	);

?>