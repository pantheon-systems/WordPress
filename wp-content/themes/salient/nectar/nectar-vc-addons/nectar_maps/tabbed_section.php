<?php 

	$tab_id_1 = time().'-1-'.rand(0, 100);
	$tab_id_2 = time().'-2-'.rand(0, 100);
	$vc_is_wp_version_3_6_more = version_compare(preg_replace('/^([\d\.]+)(\-.*$)/', '$1', get_bloginfo('version')), '3.6') >= 0;
	
	return array(
	  "name"  => esc_html__("Tabs", "js_composer"),
	  "base" => "tabbed_section",
	  "show_settings_on_create" => false,
	  "is_container" => true,
	  "icon" => "icon-wpb-ui-tab-content",
	  "category" => esc_html__('Nectar Elements', 'js_composer'),
	  "description" => esc_html__('Tabbed content', 'js_composer'),
	  "params" => array(
	  	 array(
		  "type" => "dropdown",
		  "heading" => esc_html__("Style", "js_composer"),
		  "param_name" => "style",
		  "admin_label" => true,
		  "value" => array(
			 "Default" => "default",
			 "Material" => "material",
			 "Minimal" => "minimal",
			 "Minimal Alt" => "minimal_alt",
			 "Vertical" => "vertical",
			 "Vertical Material" => "vertical_modern"
		   ),
		  'save_always' => true,
		  "description" => esc_html__("Please select the style you desire for your tabbed element.", "js_composer")
		),
	  array(
		  "type" => "dropdown",
		  "heading" => esc_html__("Alignment", "js_composer"),
		  "param_name" => "alignment",
		  "admin_label" => false,
		  "value" => array(
			 "Left" => "left",
			 "Center" => "center",
			 "Right" => "right"
		   ),
		  'save_always' => true,
		  "dependency" => Array('element' => "style", 'value' => array('minimal','default', 'minimal_alt', 'material')),
		  "description" => esc_html__("Please select your tabbed alignment", "js_composer")
		),
		array(
		  "type" => "dropdown",
		  "heading" => esc_html__("Spacing", "js_composer"),
		  "param_name" => "spacing",
		  "admin_label" => false,
		  "value" => array(
			 "Default" => "default",
			 "15px" => "side-15px",
			 "20px" => "side-20px",
			 "25px" => "side-25px",
			 "30px" => "side-30px",
			 "35px" => "side-35px",
			 "40px" => "side-40px",
			 "45px" => "side-45px"
		   ),
		  'save_always' => true,
		  "dependency" => Array('element' => "style", 'value' => array('minimal','default', 'minimal_alt',  'material')),
		  "description" => esc_html__("Please select your desired spacing", "js_composer")
		),
		array(
			"type" => "dropdown",
			"class" => "",
			'save_always' => true,
			"heading" => "Tab Color",
			"param_name" => "tab_color",
			"value" => array(
				"Accent Color" => "Accent-Color",
				"Extra Color 1" => "Extra-Color-1",
				"Extra Color 2" => "Extra-Color-2",	
				"Extra Color 3" => "Extra-Color-3",
				"Color Gradient 1" => "extra-color-gradient-1",
 			  "Color Gradient 2" => "extra-color-gradient-2",
			)
			),
	  	array(
	      "type" => "textfield",
	      "heading" => esc_html__("Optional CTA button", "js_composer"),
	      "param_name" => "cta_button_text",
	      "description" => esc_html__("If you wish to include an optional CTA button on your tabbed nav, enter the text here", "js_composer"),
	       "admin_label" => false,
	      "dependency" => Array('element' => "style", 'value' => array('minimal','minimal_alt'))
	    ),
	    array(
	      "type" => "textfield",
	      "heading" => esc_html__("CTA button link", "js_composer"),
	      "param_name" => "cta_button_link",
	      "description" => esc_html__("Enter a URL for your button link here", "js_composer"),
	       "admin_label" => false,
	      "dependency" => Array('element' => "style", 'value' => array('minimal','minimal_alt'))
	    ),
	     array(
		  "type" => "dropdown",
		  "heading" => esc_html__("CTA Button Color", "js_composer"),
		  "param_name" => "cta_button_style",
		  "admin_label" => false,
		  "value" => array(
			 "Accent Color" => "accent-color",
			 "Extra Color 1" => "extra-color-1",
			 "Extra Color 2" => "extra-color-2",
			 "Extra Color 3" => "extra-color-3"
		   ),
		  'save_always' => true,
		  'description' => __( 'Choose a color from your','salient') . ' <a target="_blank" href="'. esc_url(admin_url()) .'?page=Salient&tab=6"> ' . esc_html__('globally defined color scheme','salient') . '</a>',
		   "dependency" => Array('element' => "style", 'value' => array('minimal','minimal_alt'))
		),
    
		array(
				"type" => 'checkbox',
				"heading" => esc_html__("Full width divider line", "js_composer"),
				"param_name" => "full_width_line",
				"description" => esc_html__("This will cause the line that separates the tab links their content to display the full width of the screen.", "js_composer"),
				"value" => Array(esc_html__("Yes, please", "js_composer") => 'true'),
				"dependency" => Array('element' => "style", 'value' => array('material'))
			),
			
			array(
		"type" => "dropdown",
		"heading" => esc_html__("Icon Font Size", "js_composer"),
		"param_name" => "icon_size",
		"admin_label" => false,
		"value" => array(
		 "24px" => "24",
		 "26px" => "26",
		 "28px" => "28",
		 "30px" => "30",
		 "32px" => "32",
		 "34px" => "34",
		 "36px" => "36",
		 ),
		'save_always' => true,
		"dependency" => Array('element' => "style", 'value' => array('minimal','minimal_alt','material')),
		'description' => __( 'Select the size you would like the optional tab icons to display in - Thin border sets like "Iconsmind" and "Linea" are better suited to display at higher values.', 'js_composer' ),
	),

	    array(
	      "type" => "textfield",
	      "heading" => esc_html__("Extra class name", "js_composer"),
	      "param_name" => "el_class",
	      "description" => esc_html__("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "js_composer")
	    )
	  ),
	  "custom_markup" => '
	  <div class="wpb_tabs_holder wpb_holder vc_container_for_children">
	  <ul class="tabs_controls">
	  </ul>
	  %content%
	  </div>'
	  ,
	  'default_content' => '
	  [tab title="'.esc_html__('Tab','js_composer').'" id="'.$tab_id_1.'"] I am text block. Click edit button to change this text. [/tab]
	  [tab title="'.esc_html__('Tab','js_composer').'" id="'.$tab_id_2.'"] I am text block. Click edit button to change this text. [/tab]
	  ',
	  "js_view" => ($vc_is_wp_version_3_6_more ? 'VcTabsView' : 'VcTabsView35')
	);
?>