<?php

$vc_is_wp_version_3_6_more = version_compare(preg_replace('/^([\d\.]+)(\-.*$)/', '$1', get_bloginfo('version')), '3.6') >= 0;
	
	return array(
	  "name" => esc_html__("Testimonial", "js_composer"),
	  "base" => "testimonial",
	  "allowed_container_element" => 'vc_row',
	  "is_container" => false,
	  "content_element" => false,
	  "params" => array(
	  	array(
			"type" => "attach_image",
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
	      "type" => "textarea",
	      "heading" => esc_html__("Quote", "js_composer"),
	      "param_name" => "quote",
	      "description" => esc_html__("The testimonial quote", "js_composer")
	    ),
	    array(
		  "type" => "dropdown",
		  "heading" => esc_html__("Star Rating", "js_composer"),
		  "param_name" => "star_rating",
		  "admin_label" => false,
		  "value" => array(
		  	"Hidden" => "none",
			 "5 Stars" => "100%",
			 "4.5 Stars" => "91%",
			 "4 Stars" => "80%",
			 "3.5 Stars" => "701%",
			 "3 Stars" => "60%",
			 "2.5 Stars" => "51%",
			 "2 Stars" => "40%",
			 "1.5 Stars" => "31%",
			 "1 Stars" => "20%",
		   ),
		  'save_always' => true,
		  "description" => esc_html__("Please select the star raing you would like to show for your testimonial", "js_composer")
		),
	    array(
	      "type" => "tab_id",
	      "heading" => esc_html__("Testimonial ID", "js_composer"),
	      "param_name" => "id"
	    )
	  ),
	  'js_view' => ($vc_is_wp_version_3_6_more ? 'VcTabView' : 'VcTabView35')
	);

?>