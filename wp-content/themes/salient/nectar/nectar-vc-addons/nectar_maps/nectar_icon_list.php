<?php 
	
	$vc_is_wp_version_3_6_more = version_compare(preg_replace('/^([\d\.]+)(\-.*$)/', '$1', get_bloginfo('version')), '3.6') >= 0;
	
	$tab_id_1 = time().'-1-'.rand(0, 100);
	$tab_id_2 = time().'-2-'.rand(0, 100);

	return array(
	  "name"  => esc_html__("Icon List", "js_composer"),
	  "base" => "nectar_icon_list",
	  "show_settings_on_create" => false,
	  "is_container" => true,
	  "icon" => "icon-wpb-fancy-ul",
	  "category" => esc_html__('Nectar Elements', 'js_composer'),
	  "description" => esc_html__('Create an icon list', 'js_composer'),
	  "params" => array(
	   
	    array(
	      "type" => "checkbox",
		  "class" => "",
		  "heading" => "Animate Element?",
		  "value" => array("Yes, please" => "true" ),
		  "param_name" => "animate",
		  "description" => ""
	    ),
	     array(
			"type" => "dropdown",
			"class" => "",
			"heading" => "Icon Color",
			"param_name" => "color",
			"value" => array(
				"Default (inherit from row Text Color)" => "default",
				"Accent Color" => "Accent-Color",
				"Extra Color 1" => "Extra-Color-1",
				"Extra Color 2" => "Extra-Color-2",	
				"Extra Color 3" => "Extra-Color-3",
				"Color Gradient 1" => "extra-color-gradient-1",
				"Color Gradient 2" => "extra-color-gradient-2"
			),
			'save_always' => true,
			'description' => __( 'Choose a color from your','salient') . ' <a target="_blank" href="'. esc_url(admin_url()) .'?page=Salient&tab=6"> ' . esc_html__('globally defined color scheme','salient') . '</a>',
		),
		array(
			"type" => "dropdown",
			"heading" => esc_html__("Direction", "js_composer"),
			"param_name" => "direction",
			"value" => array(
				"Vertical" => "vertical",
				"Horizontal" => "horizontal"
			),
			'save_always' => true,
			"description" => esc_html__("Please select the direction you would like your list items to display in", "js_composer")
		),
		array(
			"type" => "dropdown",
			"heading" => esc_html__("Columns", "js_composer"),
			"param_name" => "columns",
			"value" => array(
				"Default (3)" => "default",
				"1" => "1",
				"2" => "2",
				"3" => "3",
				"4" => "4",
				"5" => "5",
			),
			"dependency" => array('element' => "direction", 'value' => 'horizontal'),
			'save_always' => true,
			"description" => esc_html__("Please select the column number you desire for your icon list items", "js_composer")
		),
		
	    array(
	      "type" => "dropdown",
	      "heading" => esc_html__("Icon Size", "js_composer"),
	      "param_name" => "icon_size",
	      "value" => array(
				"Small" => "small",
				"Medium" => "medium",
				"Large" => "large"
			),
	      'save_always' => true,
	      "description" => esc_html__("Please select the size you would like your list item icons to display in", "js_composer")
	    ),

	    array(
	      "type" => "dropdown",
	      "heading" => esc_html__("Icon Style", "js_composer"),
	      "param_name" => "icon_style",
	      "value" => array(
				"Icon Colored W/ BG" => "border",
				"Icon Colored No BG" => "no-border"
			),
	      'save_always' => true,
	      "description" => esc_html__("Please select the style you would like your list item icons to display in", "js_composer")
	    ),

	  ),
	  "custom_markup" => '
	  <div class="wpb_tabs_holder wpb_holder vc_container_for_children">
	  <ul class="tabs_controls">
	  </ul>
	  %content%
	  </div>'
	  ,
	  'default_content' => '
	  [nectar_icon_list_item title="'.esc_html__('List Item','js_composer').'" id="'.$tab_id_1.'"]  [/nectar_icon_list_item]
	  [nectar_icon_list_item title="'.esc_html__('List Item','js_composer').'" id="'.$tab_id_2.'"] [/nectar_icon_list_item]
	  ',
	  "js_view" => ($vc_is_wp_version_3_6_more ? 'VcTabsView' : 'VcTabsView35')
	);

?>