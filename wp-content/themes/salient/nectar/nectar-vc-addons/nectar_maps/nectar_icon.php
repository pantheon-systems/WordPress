<?php 

return array(
	  "name" => esc_html__("Icon", "js_composer"),
	  "base" => "nectar_icon",
	  "icon" => "icon-wpb-icons",
	  "category" => __('Nectar Elements', 'js_composer'),
	  "weight" => 1,
	  "description" => __('Add a icon', 'js_composer'),
	  "params" => array(
		array(
			'type' => 'dropdown',
			'heading' => __( 'Icon library', 'js_composer' ),
			'value' => array(
				__( 'Font Awesome', 'js_composer' ) => 'fontawesome',
				__( 'Iconsmind', 'js_composer' ) => 'iconsmind',
				__( 'Linea', 'js_composer' ) => 'linea',
				__( 'Steadysets', 'js_composer' ) => 'steadysets',
				__( 'Linecons', 'js_composer' ) => 'linecons',
			),
			'save_always' => true,
			'param_name' => 'icon_family',
			'description' => __( 'Select icon library.', 'js_composer' ),
		),
		array(
	      "type" => "iconpicker",
	      "heading" => esc_html__("Icon", "js_composer"),
	      "param_name" => "icon_fontawesome",
	      "settings" => array( "iconsPerPage" => 4000),
	      "dependency" => array('element' => "icon_family", 'emptyIcon' => false, 'value' => 'fontawesome'),
	      "description" => esc_html__("Select icon from library.", "js_composer")
	    ),
	    array(
	      "type" => "iconpicker",
	      "heading" => esc_html__("Icon", "js_composer"),
	      "param_name" => "icon_iconsmind",
	      "settings" => array( 'type' => 'iconsmind', 'emptyIcon' => false, "iconsPerPage" => 4000),
	      "dependency" => array('element' => "icon_family", 'value' => 'iconsmind'),
	      "description" => esc_html__("Select icon from library.", "js_composer")
	    ),
	    array(
	      "type" => "iconpicker",
	      "heading" => esc_html__("Icon", "js_composer"),
	      "param_name" => "icon_linea",
	      "settings" => array( 'type' => 'linea', "emptyIcon" => true, "iconsPerPage" => 4000),
	      "dependency" => Array('element' => "icon_family", 'value' => 'linea'),
	      "description" => esc_html__("Select icon from library.", "js_composer")
	    ),
	    array(
	      "type" => "iconpicker",
	      "heading" => esc_html__("Icon", "js_composer"),
	      "param_name" => "icon_linecons",
	      "settings" => array( 'type' => 'linecons', 'emptyIcon' => false, "iconsPerPage" => 4000),
	      "dependency" => array('element' => "icon_family", 'value' => 'linecons'),
	      "description" => esc_html__("Select icon from library.", "js_composer")
	    ),
	    array(
	      "type" => "iconpicker",
	      "heading" => esc_html__("Icon", "js_composer"),
	      "param_name" => "icon_steadysets",
	      "settings" => array( 'type' => 'steadysets', 'emptyIcon' => false, "iconsPerPage" => 4000),
	      "dependency" => array('element' => "icon_family", 'value' => 'steadysets'),
	      "description" => esc_html__("Select icon from library.", "js_composer")
	    ),
	    array(
	      "type" => "textfield",
	      "heading" => esc_html__("Icon Size", "js_composer"),
	      "param_name" => "icon_size",
	      "description" => esc_html__("Don't include \"px\" in your string. e.g. 40 - the default is 50" , "js_composer")
	    ),
	    array(
			"type" => "checkbox",
			"class" => "",
			"heading" => esc_html__("Enable Animation", "js_composer"),
	     	"param_name" => "enable_animation",
			"value" => array(esc_html__("Yes", "js_composer") => 'true'),
			 "dependency" => array('element' => "icon_family", 'value' => 'linea'),
			"description" => "This will cause the icon to appear to draw itself. <strong>Will not activate when using a gradient color.</strong>"
		),
		 array(
	      "type" => "textfield",
	      "heading" => esc_html__("Animation Delay", "js_composer"),
	      "param_name" => "animation_delay",
	      "dependency" => array('element' => "enable_animation", 'not_empty' => true),
	      "description" => esc_html__("Enter delay (in milliseconds) if needed e.g. 150. This parameter comes in handy when creating the animate in \"one by one\" effect.", "js_composer")
	    ),
		 array(
		 	'type' => 'dropdown',
			'heading' => __( 'Animation Speed', 'js_composer' ),
			'value' => array(
				__( 'Slow', 'js_composer' ) => 'slow',
				__( 'Medium', 'js_composer' ) => 'medium',
				__( 'fast', 'js_composer' ) => 'fast'
			),		
			'save_always' => true,
			'param_name' => 'animation_speed',
			 "dependency" => array('element' => "enable_animation", 'not_empty' => true),
			'description' => __( 'Select how fast you would like your icon to animate', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Icon Style', 'js_composer' ),
			'value' => array(
				__('Icon Only', 'js_composer' ) => "default",
				__('Border Basic', 'js_composer' ) => "border-basic",
				__('Border W/ Hover Animation', 'js_composer' ) => "border-animation",
				__('Soft Color Background', 'js_composer' ) => "soft-bg"
			),
			'save_always' => true,
			'param_name' => 'icon_style',
			'description' => __( 'Select your button style.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Icon Border Thickness', 'js_composer' ),
			'value' => array(
				__('1px', 'js_composer' ) => "1px",
				__('2px', 'js_composer' ) => "2px",
				__('3px', 'js_composer' ) => "3px",
				__('4px', 'js_composer' ) => "4px",
				__('5px', 'js_composer' ) => "5px"
			),
			'std' => '2px',
			 "dependency" => array('element' => "icon_style", 'value' => array('border-basic','border-animation')),
			'save_always' => true,
			'param_name' => 'icon_border_thickness',
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Icon Color', 'js_composer' ),
			'value' => array(
				"Accent Color" => "Accent-Color",
				"Extra Color 1" => "Extra-Color-1",
				"Extra Color 2" => "Extra-Color-2",	
				"Extra Color 3" => "Extra-Color-3",
				"Color Gradient 1" => "extra-color-gradient-1",
		 		"Color Gradient 2" => "extra-color-gradient-2",
		 		"Black" => "black",
		 		"Grey" => "grey",
		 		"White" => "white",
			),
			'save_always' => true,
			'param_name' => 'icon_color',
			'description' => __( 'Choose a color from your','salient') . ' <a target="_blank" href="'. esc_url(admin_url()) .'?page=Salient&tab=6"> ' . __('globally defined color scheme','salient') . '</a>',
		),
		 array(
	      "type" => "textfield",
	      "heading" => esc_html__("Link URL", "js_composer"),
	      "param_name" => "url",
	      "description" => esc_html__("The link for your button." , "js_composer")
	    ),
	    
	    array(
			"type" => "checkbox",
			"class" => "",
			"heading" => esc_html__("Open Link In New Tab?", "js_composer"),
	     	"param_name" => "open_new_tab",
			"value" => Array(esc_html__("Yes", "js_composer") => 'true'),
			"description" => ""
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Icon Padding', 'js_composer' ),
			'value' => array(
				__('10px', 'js_composer' ) => "10px",
				__('15px', 'js_composer' ) => "15px",
				__('20px', 'js_composer' ) => "20px",
				__('25px', 'js_composer' ) => "25px",
				__('30px', 'js_composer' ) => "30px",
				__('35px', 'js_composer' ) => "35px",
				__('40px', 'js_composer' ) => "40px",
				__('45px', 'js_composer' ) => "45px",
				__('50px', 'js_composer' ) => "50px",
				__('0px', 'js_composer' ) => "0px",
			),
			'std' => '20px',
			'save_always' => true,
			'param_name' => 'icon_padding',
		),
	    array(
	      "type" => "textfield",
	      "heading" => esc_html__("Margin", "js_composer") . "<span>" . esc_html__("Top", "js_composer") . "</span>",
	      "param_name" => "margin_top",
	      "edit_field_class" => "col-md-2",
	      "description" => ''
	    ),
		 array(
	      "type" => "textfield",
	      "heading" => "<span>" . esc_html__("Right", "js_composer") . "</span>",
	      "param_name" => "margin_right",
	      "edit_field_class" => "col-md-2",
	      "description" => ''
	    ),
		array(
	      "type" => "textfield",
	      "heading" => "<span>" . esc_html__("Bottom", "js_composer") . "</span>",
	      "param_name" => "margin_bottom",
	      "edit_field_class" => "col-md-2",
	      "description" => ''
	    ),
	    array(
	      "type" => "textfield",
	      "heading" => "<span>" . esc_html__("Left", "js_composer") . "</span>",
	      "param_name" => "margin_left",
	      "edit_field_class" => "col-md-2",
	      "description" => ''
	    ),
	  )
	);

?>