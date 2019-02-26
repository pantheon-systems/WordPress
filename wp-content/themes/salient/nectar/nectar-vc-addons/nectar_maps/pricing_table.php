<?php 

	$tab_id_1 = time().'-1-'.rand(0, 100);
	$tab_id_2 = time().'-2-'.rand(0, 100);
	$vc_is_wp_version_3_6_more = version_compare(preg_replace('/^([\d\.]+)(\-.*$)/', '$1', get_bloginfo('version')), '3.6') >= 0;

return array(
	  "name"  => esc_html__("Pricing Table", "js_composer"),
	  "base" => "pricing_table",
	  "show_settings_on_create" => false,
	  "is_container" => true,
	  "icon" => "icon-wpb-pricing-table",
	  "category" => esc_html__('Nectar Elements', 'js_composer'),
	  "description" => esc_html__('Stylish pricing tables', 'js_composer'),
	  "params" => array(
	    array(
	      "type" => "textfield",
	      "heading" => esc_html__("Extra class name", "js_composer"),
	      "param_name" => "el_class",
	      "description" => esc_html__("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "js_composer")
		),
		array(
			"type" => "dropdown",
			"holder" => "div",
			"admin_label" => false,
			"class" => "",
			"heading" => "Style",
			"param_name" => "style",
			"value" => array(
				"Default" => "default",
				"Flat Alternative" => "flat-alternative"
			),
			'save_always' => true,
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
	  [pricing_column title="'.esc_html__('Column','js_composer').'" id="'.$tab_id_1.'"]  [/pricing_column]
	  [pricing_column title="'.esc_html__('Column','js_composer').'" id="'.$tab_id_2.'"]  [/pricing_column]
	  ',
	  "js_view" => ($vc_is_wp_version_3_6_more ? 'VcTabsView' : 'VcTabsView35')
	);

?>