<?php 
return array(
	  "name" => esc_html__("Toggle Panels", "js_composer"),
	  "base" => "toggles",
	  "show_settings_on_create" => false,
	  "is_container" => true,
	  "icon" => "icon-wpb-ui-accordion",
	  "category" => esc_html__('Nectar Elements', 'js_composer'),
	  "description" => esc_html__('jQuery toggles/accordion', 'js_composer'),
	  "params" => array(
	  	array(
			  "type" => "dropdown",
			  "heading" => esc_html__("Style", "js_composer"),
			  "param_name" => "style",
			  "admin_label" => true,
			  "value" => array(
				 "Default" => "default",
				 "Minimal" => "minimal",
				 "Minimal Small" => "minimal_small",
		   ),
		  'save_always' => true,
		  "description" => esc_html__("Please select the style you desire for your toggle element.", "js_composer")
		  ),
	    array(
	      "type" => 'checkbox',
	      "heading" => esc_html__("Accordion Toggles", "js_composer"),
	      "param_name" => "accordion",
	      "description" => esc_html__("Selecting this will make it so that only one toggle can be opened at a time.", "js_composer"),
	      "value" => Array(esc_html__("Allow", "js_composer") => 'true')
	    )
	  ),
	  "custom_markup" => '
	  <div class="wpb_accordion_holder wpb_holder clearfix vc_container_for_children">
	  %content%
	  </div>
	  <div class="tab_controls">
	 <a class="add_tab" title="' . __( 'Add section', 'js_composer' ) . '"><span class="vc_icon"></span> <span class="tab-label">' . __( 'Add section', 'js_composer' ) . '</span></a>
	  </div>
	  ',
	  'default_content' => '
	  [toggle title="'.esc_html__('Section', "js_composer").'"][/toggle]
	  [toggle title="'.esc_html__('Section', "js_composer").'"][/toggle]
	  ',
	  'js_view' => 'VcAccordionView'
	);
?>