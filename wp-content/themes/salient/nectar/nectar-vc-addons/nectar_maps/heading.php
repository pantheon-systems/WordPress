<?php 

return array(
	  "name" => esc_html__("Centered Heading", "js_composer"),
	  "base" => "heading",
	  "icon" => "icon-wpb-centered-heading",
	  "category" => esc_html__('Nectar Elements', 'js_composer'),
	  "description" => esc_html__('Simple heading', 'js_composer'),
	  "params" => array(
	    array(
	      "type" => "textarea_html",
	      "holder" => "div",
	      "heading" => esc_html__("Heading", "js_composer"),
	      "param_name" => "content",
	      "value" => ''
	    ), 
	    array(
	      "type" => "textfield",
	      "heading" => esc_html__("Subtitle", "js_composer"),
	      "param_name" => "subtitle",
	      "description" => esc_html__("The subtitle text under the main title", "js_composer")
	    )
	  )
	);

?>