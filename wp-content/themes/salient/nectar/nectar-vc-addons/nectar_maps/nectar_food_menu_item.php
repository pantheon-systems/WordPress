<?php 

return array(
	  "name" => esc_html__("Food Menu Item", "js_composer"),
	  "base" => "nectar_food_menu_item",
	  "icon" => "icon-wpb-pricing-table",
	  "category" => esc_html__('Nectar Elements', 'js_composer'),
	  "description" => esc_html__('Create restaurant menus', 'js_composer'),
	  "params" => array(
	  	 array(
			  "type" => "dropdown",
			  "heading" => esc_html__("Style", "js_composer"),
			  "param_name" => "style",
			  "value" => array(
				    'Default' => 'default',
				    'Line From Name To Price' => 'line_between'
				),
			  'save_always' => true,
			  "description" => esc_html__("Please select the desired style for your item", "js_composer")
			),
	  		array(
			"type" => "textfield",
			"class" => "",
			"description" => esc_html__("The item name", "js_composer"),
			"heading" => "Item Name",
			"admin_label" => true,
			"param_name" => "item_name"
			),
	    array(
	      "type" => "textfield",
	      "heading" => esc_html__("Item Price", "js_composer"),
	      "param_name" => "item_price",
	       "admin_label" => true,
	      "description" => esc_html__("The price of your item - include the currency symbol of your choosing i.e. \"$29\"", "js_composer")
	    ),
	     array(
			  "type" => "dropdown",
			  "heading" => esc_html__("Item Name Heading Tag", "js_composer"),
			  "param_name" => "item_name_heading_tag",
			  "value" => array(
				    'H3' => 'h3',
				    'H4' => 'h4',
				    'H5' => 'h5',
				    'H6' => 'h6'
				),
			  'save_always' => true,
			  "description" => esc_html__("Please select the desired heading tag for your item name", "js_composer")
			),
	    array(
		      "type" => "textarea",
		      "heading" => esc_html__("Item Description", "js_composer"),
		      "param_name" => "item_description",
		      "description" => esc_html__("Please enter description for your item", "js_composer")
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