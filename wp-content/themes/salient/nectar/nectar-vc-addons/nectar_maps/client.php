<?php 

$vc_is_wp_version_3_6_more = version_compare(preg_replace('/^([\d\.]+)(\-.*$)/', '$1', get_bloginfo('version')), '3.6') >= 0;

return array(
	  "name" => esc_html__("Client", "js_composer"),
	  "base" => "client",
	  "allowed_container_element" => 'vc_row',
	  "is_container" => true,
	  "content_element" => false,
	  "params" => array(
	    array(
	      "type" => "fws_image",
	      "heading" => esc_html__("Image", "js_composer"),
	      "param_name" => "image",
	      "value" => "",
	      "description" => esc_html__("Select image from media library.", "js_composer")
	    ),
	    array(
	      "type" => "textfield",
	      "heading" => esc_html__("URL", "js_composer"),
	      "param_name" => "url",
	      "description" => esc_html__("Add an optional link to your client", "js_composer")
	    ),
	    array(
	      "admin_label" => true,
	      "type" => "textfield",
	      "heading" => esc_html__("Client Name", "js_composer"),
	      "param_name" => "name",
	      "description" => esc_html__("Fill this out to keep track of which client is which in your page builder interface.", "js_composer")
	    )
	  ),
	  'js_view' => ($vc_is_wp_version_3_6_more ? 'VcTabView' : 'VcTabView35')
	);

?>