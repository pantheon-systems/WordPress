<?php 

$vc_is_wp_version_3_6_more = version_compare(preg_replace('/^([\d\.]+)(\-.*$)/', '$1', get_bloginfo('version')), '3.6') >= 0;

return array(
	  "name" => esc_html__("List Item", "js_composer"),
	  "base" => "nectar_icon_list_item",
	  "allowed_container_element" => 'vc_row',
	  "is_container" => true,
	  "content_element" => false,
	  "params" => array(
	  	 array(
	      "type" => "dropdown",
	      "heading" => esc_html__("List Icon Type", "js_composer"),
	      "param_name" => "icon_type",
	      "value" => array(
				"Number" => "numerical",
				"Icon" => "icon"
			),
	      'save_always' => true,
	      "admin_label" => true,
	      "description" => esc_html__("Please select how many columns you would like..", "js_composer")
	    ),

	  	 array(
			'type' => 'dropdown',
			'heading' => __( 'Icon library', 'js_composer' ),
			'value' => array(
				__( 'Font Awesome', 'js_composer' ) => 'fontawesome',
				__( 'Iconsmind', 'js_composer' ) => 'iconsmind',
				__( 'Linea', 'js_composer' ) => 'linea',
				__( 'Steadysets', 'js_composer' ) => 'steadysets',
			),
			"dependency" => array('element' => "icon_type", 'value' => 'icon'),
			'param_name' => 'icon_family',
			'description' => __( 'Select icon library.', 'js_composer' ),
		),
		array(
	      "type" => "iconpicker",
	      "heading" => esc_html__("Icon", "js_composer"),
	      "param_name" => "icon_fontawesome",
	      "settings" => array( "emptyIcon" => true, "iconsPerPage" => 4000),
	      "dependency" => Array('element' => "icon_family", 'value' => 'fontawesome'),
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
	      "param_name" => "icon_steadysets",
	      "settings" => array( 'type' => 'steadysets', 'emptyIcon' => false, "iconsPerPage" => 4000),
	      "dependency" => array('element' => "icon_family", 'value' => 'steadysets'),
	      "description" => esc_html__("Select icon from library.", "js_composer")
	    ),
	  	 array(
	      "admin_label" => true,
	      "type" => "textfield",
	      "heading" => esc_html__("Header", "js_composer"),
	      "param_name" => "header",
	      "description" => esc_html__("Enter the header desired for your icon list element", "js_composer")
	    ),
	    array(
	      "admin_label" => true,
	      "type" => "textarea",
	      "heading" => esc_html__("Text Content", "js_composer"),
	      "param_name" => "text",
	      "description" => esc_html__("Enter the text content desired for your icon list element", "js_composer")
	    ),
	    array(
	      "type" => "tab_id",
	      "heading" => esc_html__("Item ID", "js_composer"),
	      "param_name" => "id"
	    )

	  ),
	  'js_view' => ($vc_is_wp_version_3_6_more ? 'VcTabView' : 'VcTabView35')
	);

?>