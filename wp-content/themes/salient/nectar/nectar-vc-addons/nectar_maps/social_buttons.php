<?php 

return array(
	  "name" => esc_html__("Social Buttons", "js_composer"),
	  "base" => "social_buttons",
	  "icon" => "icon-wpb-social-buttons",
	  "category" => esc_html__('Nectar Elements', 'js_composer'),
	  "description" => esc_html__('Add social buttons to any page', 'js_composer'),
	  "params" => array(
	     array(
	      "type" => 'checkbox',
	      "heading" => esc_html__("Display full width?", "js_composer"),
	      "param_name" => "full_width_icons",
	      "description" => esc_html__("This will make your social icons expand to fit edge to edge in whatever space they're placed." , "js_composer"),
	      "value" => Array(esc_html__("Yes", "js_composer") => 'true')
	    ),
	 	 array(
	      "type" => 'checkbox',
	      "heading" => esc_html__("Nectar Love", "js_composer"),
	      "param_name" => "nectar_love",
	      "value" => Array(esc_html__("Yes", "js_composer") => 'true')
	    ),
	    array(
	      "type" => 'checkbox',
	      "heading" => esc_html__("Facebook", "js_composer"),
	      "param_name" => "facebook",
	      "value" => Array(esc_html__("Yes", "js_composer") => 'true')
	    ),
	    array(
	      "type" => 'checkbox',
	      "heading" => esc_html__("Twitter", "js_composer"),
	      "param_name" => "twitter",
	      "value" => Array(esc_html__("Yes", "js_composer") => 'true')
	    ),
	    array(
	      "type" => 'checkbox',
	      "heading" => esc_html__("Google+", "js_composer"),
	      "param_name" => "google_plus",
	      "value" => Array(esc_html__("Yes", "js_composer") => 'true')
	    ),
	    array(
	      "type" => 'checkbox',
	      "heading" => esc_html__("LinkedIn", "js_composer"),
	      "param_name" => "linkedin",
	      "value" => Array(esc_html__("Yes", "js_composer") => 'true')
	    ),
	    array(
	      "type" => 'checkbox',
	      "heading" => esc_html__("Pinterest", "js_composer"),
	      "param_name" => "pinterest",
	      "description" => '',
	      "value" => Array(esc_html__("Yes", "js_composer") => 'true')
	    )
	  )
	);

?>