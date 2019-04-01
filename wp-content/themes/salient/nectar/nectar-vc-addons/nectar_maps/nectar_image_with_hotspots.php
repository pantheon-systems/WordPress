<?php 

return array(
		  "name" => esc_html__("Image With Hotspots", "js_composer"),
		  "base" => "nectar_image_with_hotspots",
		  "weight" => 2,
		  "icon" => "icon-wpb-nectar-image-withhotspots",
		  "category" => esc_html__('Nectar Elements', 'js_composer'),
		  "description" => esc_html__('Add Hotspots On Your Image', 'js_composer'),
		  "params" => array(

		  	array(
				"type" => "attach_image",
				"class" => "",
				"heading" => "Image",
				"value" => "",
				"param_name" => "image",
				"description" => "Choose your image that will show the hotspots. <br/> You can then click on the image in the preview area to add your hotspots in the desired locations."
			),
			array(
		      "type" => "hotspot_image_preview",
		      "heading" => esc_html__("Preview", "js_composer"),
		      "param_name" => "preview",
		      "description" => esc_html__("Click to add - Drag to move - Edit content below. Note: this preview will not reflect hotspot style choices or show tooltips. This is only used as a visual guide for positioning. Requires Salient VC 4.12 or higher.", "js_composer"),
		      "value" => ''
		    ),	
			 array(
		      "type" => "textarea_html",
		      "heading" => esc_html__("Hotspots", "js_composer"),
		      "param_name" => "content",
		      "description" => '',
		    ),	 

			array(
			"type" => "dropdown",
			"class" => "",
			'save_always' => true,
			"group" => "Style",
			"heading" => "Color",
			"admin_label" => true,
			"param_name" => "color_1",
			'description' => __( 'Choose a color from your','salient') . ' <a target="_blank" href="'. esc_url(admin_url()) .'?page=Salient&tab=6"> ' . esc_html__('globally defined color scheme','salient') . '</a>',
			/*"dependency" => array('element' => "style", 'value' => 'color_pulse'),*/
			"value" => array(
				"Accent Color" => "Accent-Color",
				"Extra Color 1" => "Extra-Color-1",
				"Extra Color 2" => "Extra-Color-2",	
				"Extra Color 3" => "Extra-Color-3"
			)),
			/*array(
			"type" => "dropdown",
			"class" => "",
			'save_always' => true,
			"group" => "Style",
			"heading" => "Color",
			"param_name" => "color_2",
			"description" => __("Choose the color which the hotspot will use", "js_composer"),
			"dependency" => array('element' => "style", 'value' => 'border'),
			"value" => array(
				"Light" => "light",
				"Dark" => "dark",
			)),*/
			array(
			"type" => "dropdown",
			"class" => "",
			'save_always' => true,
			"group" => "Style",
			"heading" => "Hotspot Icon",
			"description" => esc_html__("The icon that will be shown on the hotspots", "js_composer"),
			"param_name" => "hotspot_icon",
			"admin_label" => true,
			"value" => array(
				"Plus Sign" => "plus_sign",
				"Numerical" => "numerical"
			)),
			/*array(
			"type" => "dropdown",
			"class" => "",
			'save_always' => true,
			"group" => "Style",
			"heading" => "Hotspot Size",
			"param_name" => "size",
			"description" => esc_html__("Select the size of your hotspots here", "js_composer"),
			"value" => array(
				"Small" => "small",
				"Medium" => "medium",
				"Large" => "large"
			)),*/
			array(
			"type" => "dropdown",
			"class" => "",
			'save_always' => true,
			"group" => "Style",
			"heading" => "Tooltip Functionality",
			"param_name" => "tooltip",
			"description" => esc_html__("Select how you want your tooltips to display to the user", "js_composer"),
			"value" => array(
				"Show On Hover" => "hover",
				"Show On Click" => "click",
				"Always Show" => "always_show"
			)),
			array(
			"type" => "dropdown",
			"class" => "",
			'save_always' => true,
			"group" => "Style",
			"heading" => "Tooltip Shadow",
			"param_name" => "tooltip_shadow",
			"description" => esc_html__("Select the shadow size for your tooltip", "js_composer"),
			"value" => array(esc_html__("None", "js_composer") => "none", esc_html__("Small Depth", "js_composer") => "small_depth", esc_html__("Medium Depth", "js_composer") => "medium_depth", esc_html__("Large Depth", "js_composer") => "large_depth"),
			),
			array(
		      "type" => 'checkbox',
		      "heading" => esc_html__("Enable Animation", "js_composer"),
		      "param_name" => "animation",
		      "group" => "Style",
		      "description" => esc_html__("Turning this on will make your hotspots animate in when the user scrolls to the element", "js_composer"),
		      "value" => Array(esc_html__("Yes, please", "js_composer") => 'true')
		    )
		  )
		);

?>