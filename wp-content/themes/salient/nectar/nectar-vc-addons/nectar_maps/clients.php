<?php 
	
	$vc_is_wp_version_3_6_more = version_compare(preg_replace('/^([\d\.]+)(\-.*$)/', '$1', get_bloginfo('version')), '3.6') >= 0;
	
	$tab_id_1 = time().'-1-'.rand(0, 100);
	$tab_id_2 = time().'-2-'.rand(0, 100);

	return array(
	  "name"  => esc_html__("Clients Display", "js_composer"),
	  "base" => "clients",
	  "show_settings_on_create" => false,
	  "is_container" => true,
	  "icon" => "icon-wpb-clients",
	  "category" => esc_html__('Nectar Elements', 'js_composer'),
	  "description" => esc_html__('Show off your clients!', 'js_composer'),
	  "params" => array(
	    array(
	      "type" => "dropdown",
	      "heading" => esc_html__("Columns", "js_composer"),
	      "param_name" => "columns",
	      "value" => array(
				"Two" => "2",
				"Three" => "3",	
				"Four" => "4",
				"Five" => "5",
				"Six" => "6"
			),
	      'save_always' => true,
	      "description" => esc_html__("Please select how many columns you would like..", "js_composer")
	    ),
			array(
			 "type" => "dropdown",
			 "heading" => esc_html__("Hover Effect", "js_composer"),
			 "param_name" => "hover_effect",
			 "value" => array(
			 "Opacity Change" => "opacity",
			 "Greyscale to Color" => "greyscale_to_color",	
		 ),
			 'save_always' => true,
			 "description" => esc_html__("Select your desired hover effect", "js_composer")
		 ),
		 array(
			 "type" => "dropdown",
			 "heading" => esc_html__("Additional Logo Padding", "js_composer"),
			 "param_name" => "additional_padding",
			 "value" => array(
			 "None" => "none",
			 "A little" => "2",	
			 "Medium" => "3",
			 "A lot" => "4",
		 ),
			 'save_always' => true,
			 "description" => esc_html__("Please select if you would like any additional padding between your client logos", "js_composer")
		 ),
	    array(
	      "type" => "checkbox",
			  "class" => "",
			  "heading" => "Fade In One By One?",
			  "value" => array("Yes, please" => "true" ),
			  "param_name" => "fade_in_animation",
			  "description" => ""
	    ),
	    array(
	      "type" => "checkbox",
			  "class" => "",
			  "heading" => "Turn Into Carousel",
			  "value" => array("Yes, please" => "true" ),
			  "param_name" => "carousel",
			  "description" => ""
	    ),
	    array(
	      "type" => "checkbox",
		  "class" => "",
		  "heading" => "Disable Autorotate?",
		  "value" => array("Yes, please" => "true" ),
		  "param_name" => "disable_autorotate",
		  "dependency" => Array('element' => "carousel", 'not_empty' => true),
		  "description" => ""
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
	  [client title="'.esc_html__('Client','js_composer').'" id="'.$tab_id_1.'"] Click the edit button to add your testimonial. [/client]
	  [client title="'.esc_html__('Client','js_composer').'" id="'.$tab_id_2.'"] Click the edit button to add your testimonial. [/client]
	  ',
	  "js_view" => ($vc_is_wp_version_3_6_more ? 'VcTabsView' : 'VcTabsView35')
	);

?>