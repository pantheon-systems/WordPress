<?php 

return array(
	  "name" => esc_html__("Image Comparison", "js_composer"),
	  "base" => "nectar_image_comparison",
	  "icon" => "icon-wpb-single-image",
	  "category" => esc_html__('Nectar Elements', 'js_composer'),
	  "description" => esc_html__('Shows differences in two images', 'js_composer'),
	  "params" => array(
	    array(
	      "type" => "fws_image",
	      "heading" => esc_html__("Image One", "js_composer"),
	      "param_name" => "image_url",
	      "value" => "",
	      "description" => esc_html__("Select image from media library.", "js_composer")
	    ),
	    array(
	      "type" => "fws_image",
	      "heading" => esc_html__("Image Two", "js_composer"),
	      "param_name" => "image_2_url",
	      "value" => "",
	      "description" => esc_html__("Select image from media library.", "js_composer")
	    ),
	    array(
	      "type" => "textfield",
	      "heading" => esc_html__("Extra class name", "js_composer"),
	      "param_name" => "el_class",
	      "description" => esc_html__("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "js_composer")
	    )
	  )
	);

?>