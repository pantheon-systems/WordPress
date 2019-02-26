<?php 

return array(
	  "name" => esc_html__("Section", "js_composer"),
	  "base" => "toggle",
	  "allowed_container_element" => 'vc_row',
	  "is_container" => true,
	  "content_element" => false,
	  "params" => array(
	    array(
	      "type" => "textfield",
	      "heading" => esc_html__("Title", "js_composer"),
	      "param_name" => "title",
	      "description" => esc_html__("Accordion section title.", "js_composer")
	    ),
	     array(
		  "type" => "dropdown",
		  "heading" => esc_html__("Color", "js_composer"),
		  "param_name" => "color",
		  "admin_label" => true,
		  "value" => array(
		     "Default" => "Default",
			 "Accent Color" => "Accent-Color",
			 "Extra Color 1" => "Extra-Color-1",
			 "Extra Color 2" => "Extra-Color-2",	
			 "Extra Color 3" => "Extra-Color-3"
		   ),
		  'save_always' => true,
		  'description' => __( 'Choose a color from your','salient') . ' <a target="_blank" href="'. esc_url(admin_url()) .'?page=Salient&tab=6"> ' . esc_html__('globally defined color scheme','salient') . '</a>',
		)
	  ),
	  'js_view' => 'VcAccordionTabView'
	);

?>