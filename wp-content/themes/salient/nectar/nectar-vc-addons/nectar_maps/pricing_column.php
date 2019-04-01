<?php 

$vc_is_wp_version_3_6_more = version_compare(preg_replace('/^([\d\.]+)(\-.*$)/', '$1', get_bloginfo('version')), '3.6') >= 0;

return array(
	  "name" => esc_html__("Pricing Column", "js_composer"),
	  "base" => "pricing_column",
	  "allowed_container_element" => 'vc_row',
	  "is_container" => true,
	  "content_element" => false,
	  "params" => array(
	    array(
	      "type" => "textfield",
	      "heading" => esc_html__("Title", "js_composer"),
	      "param_name" => "title",
	      "description" => esc_html__("Please enter a title for your pricing column", "js_composer")
	    ),
	    array(
	      "type" => "textfield",
	      "heading" => esc_html__("Price", "js_composer"),
	      "param_name" => "price",
	       "admin_label" => true,
	      "description" => esc_html__("Enter the price for your column", "js_composer")
	    ),
	    array(
	      "type" => "textfield",
	      "heading" => esc_html__("Currency Symbol", "js_composer"),
	      "param_name" => "currency_symbol",
	      "description" => esc_html__("Enter the currency symbol that will display for your price", "js_composer")
	    ),
	    array(
	      "type" => "textfield",
	      "heading" => esc_html__("Interval", "js_composer"),
	      "param_name" => "interval",
	      "description" => esc_html__("Enter the interval for your pricing e.g. \"Per Month\" or \"Per Year\" ", "js_composer")
	    ),
	    array(
	      "type" => "checkbox",
		  "class" => "",
		  "heading" => "Highlight Column?",
		  "value" => array("Yes, please" => "true" ),
		  "param_name" => "highlight",
		  "description" => ""
	    ),
	    array(
	      "type" => "textfield",
	      "heading" => esc_html__("Highlight Reason", "js_composer"),
	      "param_name" => "highlight_reason",
	      "description" => esc_html__("Enter the reason for the column being highlighted e.g. \"Most Popular\"" , "js_composer"),
	      "dependency" => Array('element' => "highlight", 'not_empty' => true)
	    ),
	    array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => "Color",
			"param_name" => "color",
			"value" => array(
				"Accent Color" => "Accent-Color",
				"Extra Color 1" => "Extra-Color-1",
				"Extra Color 2" => "Extra-Color-2",	
				"Extra Color 3" => "Extra-Color-3"
			),
			'save_always' => true,
			'description' => __( 'Choose a color from your','salient') . ' <a target="_blank" href="'. esc_url(admin_url()) .'?page=Salient&tab=6"> ' . esc_html__('globally defined color scheme','salient') . '</a>',
		),
		array(
	      "type" => "textarea_html",
	      "holder" => "hidden",
	      "heading" => esc_html__("Text Content", "js_composer"),
	      "param_name" => "content",
	      "value" => ''
	    )
	  ),
	  'js_view' => ($vc_is_wp_version_3_6_more ? 'VcTabView' : 'VcTabView35')
	);


?>