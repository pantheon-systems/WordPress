<?php 

$vc_is_wp_version_3_6_more = version_compare(preg_replace('/^([\d\.]+)(\-.*$)/', '$1', get_bloginfo('version')), '3.6') >= 0;

$tab_id_1 = time().'-1-'.rand(0, 100);
$tab_id_2 = time().'-2-'.rand(0, 100);

	return array(
	  "name"  => esc_html__("Page Submenu", "js_composer"),
	  "base" => "page_submenu",
	  "show_settings_on_create" => true,
	  "is_container" => true,
	  "icon" => "icon-wpb-page-submenu",
	  "category" => esc_html__('Nectar Elements', 'js_composer'),
	  "description" => esc_html__('Great for animated anchors', 'js_composer'),
	  "params" => array( 
	  	array(
	      "type" => "dropdown",
	      "heading" => esc_html__("Link Alignment", "js_composer"),
	      "param_name" => "alignment",
	      "value" => array(
				"Center" => "center",
				"Left" => "left",	
				"Right" => "right"
			),
	      'save_always' => true,
	      "description" => esc_html__("Please select your desired link alignment", "js_composer")
	    ),
	    array(
	      "type" => "checkbox",
		  "class" => "",
		  "heading" => "Sticky?",
		  "value" => array("Yes, please" => "true" ),
		  "param_name" => "sticky",
		  "description" => "This will cause your submenu to stick to the top when scrolled by"
	    ),
		array(
			"type" => "colorpicker",
			"class" => "",
			"heading" => "Menu BG Color",
			"param_name" => "bg_color",
			"value" => "#f7f7f7",
			"description" => ""
		),
		array(
			"type" => "colorpicker",
			"class" => "",
			"heading" => "Link Color",
			"param_name" => "link_color",
			"value" => "#000000",
			"description" => ""
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
	  [page_link link_url="#" title="'.esc_html__('Link','js_composer').'" id="'.$tab_id_1.'"] [/page_link]
	  [page_link link_url="#" title="'.esc_html__('Link','js_composer').'" id="'.$tab_id_2.'"]  [/page_link]
	  ',
	  "js_view" => ($vc_is_wp_version_3_6_more ? 'VcTabsView' : 'VcTabsView35')
	);


?>