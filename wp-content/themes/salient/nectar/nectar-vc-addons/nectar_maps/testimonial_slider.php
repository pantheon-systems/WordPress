<?php 

  $tab_id_1 = time().'-1-'.rand(0, 100);
	$tab_id_2 = time().'-2-'.rand(0, 100);

	$vc_is_wp_version_3_6_more = version_compare(preg_replace('/^([\d\.]+)(\-.*$)/', '$1', get_bloginfo('version')), '3.6') >= 0;
	
	return array(
	  "name"  => esc_html__("Testimonial Slider", "js_composer"),
	  "base" => "testimonial_slider",
	  "show_settings_on_create" => false,
	  "is_container" => true,
	  "icon" => "icon-wpb-testimonial-slider",
	  "category" => esc_html__('Nectar Elements', 'js_composer'),
	  "description" => esc_html__('An appealing testmonial slider.', 'js_composer'),
	  "params" => array(
	  	 array(
		  "type" => "dropdown",
		  "heading" => esc_html__("Style", "js_composer"),
		  "param_name" => "style",
		  "admin_label" => false,
		  "value" => array(
			 "Basic (Default)" => "default",
			 "Minimal" => "minimal",
			 "Multiple Visible" => "multiple_visible",
			 "Multiple Visible Minimal" => "multiple_visible_minimal",
		   ),
		  'save_always' => true,
		  "description" => esc_html__("Please select the style for your testimonial slider", "js_composer")
		),
	  	array(
		  "type" => "dropdown",
		  "heading" => esc_html__("Color", "js_composer"),
		  "param_name" => "color",
		  "admin_label" => false,
		  "value" => array(
			 "Inherit (from row Text Color)" => "default",
			 "Accent Color + Light Text" => "accent-color-light",
			 "Extra Color 1 + Light Text" => "extra-color-1-light",
			 "Extra Color 2 + Light Text" => "extra-color-2-light",
			 "Extra Color 3 + Light Text" => "extra-color-3-light",
			 "Accent Color + Dark Text" => "accent-color-dark",
			 "Extra Color 1 + Dark Text" => "extra-color-1-dark",
			 "Extra Color 2 + Dark Text" => "extra-color-2-dark",
			 "Extra Color 3 + Dark Text" => "extra-color-3-dark"
		   ),
		  'save_always' => true,
		  "dependency" => Array('element' => "style", 'value' => array('multiple_visible')),
		  'description' => __( 'Choose a color from your','salient') . ' <a target="_blank" href="'. esc_url(admin_url()) .'?page=Salient&tab=6"> ' . esc_html__('globally defined color scheme','salient') . '</a>',
		),
		array(
		  "type" => "dropdown",
		  "heading" => esc_html__("Star Rating Color", "js_composer"),
		  "param_name" => "star_rating_color",
		  "admin_label" => false,
		  "value" => array(
			 "Accent Color" => "accent-color",
			 "Extra Color 1" => "extra-color-1",
			 "Extra Color 2" => "extra-color-2",	
			 "Extra Color 3" => "extra-color-3",
			 "Color Gradient 1" => "extra-color-gradient-1",
			 "Color Gradient 2" => "extra-color-gradient-2"
		   ),
		  'save_always' => true,
		  "dependency" => Array('element' => "style", 'value' => array('multiple_visible_minimal','minimal','default')),
		  'description' => __( 'Choose a color from your','salient') . ' <a target="_blank" href="'. esc_url(admin_url()) .'?page=Salient&tab=6"> ' . esc_html__('globally defined color scheme','salient') . '</a>',
		),
		array(
	      "type" => "checkbox",
		  "class" => "",
		  "heading" => "Add Border To Testimonials",
		  "value" => array("Yes, please" => "true" ),
		  "param_name" => "add_border",
		   "dependency" => Array('element' => "style", 'value' => array('multiple_visible_minimal')),
		  "description" => "This will add a slight border to your testimonials"
	    ),
	    array(
	      "type" => "textfield",
	      "heading" => esc_html__("Auto rotate?", "js_composer"),
	      "param_name" => "autorotate",
	      "value" => '',
	      "description" => esc_html__("If you would like this to autorotate, enter the rotation speed in miliseconds here. i.e 5000", "js_composer")
	    ),
	    array(
	      "type" => "checkbox",
		  "class" => "",
		  "heading" => "Disable height animation?",
		  "value" => array("Yes, please" => "true" ),
		  "param_name" => "disable_height_animation",
		   "dependency" => Array('element' => "style", 'value' => array('default','minimal')),
		  "description" => "Your testimonial slider will animate the height of itself to match the height of the testimonial being shown - this will remove that and simply set the height equal to the tallest testimonial to allow your content below to remain stagnant instead of moving up/down."
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
	  [testimonial title="'.esc_html__('Testimonial','js_composer').'" id="'.$tab_id_1.'"] Click the edit button to add your testimonial. [/testimonial]
	  [testimonial title="'.esc_html__('Testimonial','js_composer').'" id="'.$tab_id_2.'"] Click the edit button to add your testimonial. [/testimonial]
	  ',
	  "js_view" => ($vc_is_wp_version_3_6_more ? 'VcTabsView' : 'VcTabsView35')
	);

?>