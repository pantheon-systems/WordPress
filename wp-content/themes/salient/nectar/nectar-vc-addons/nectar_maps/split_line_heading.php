<?php 

return array(
			"name" => "Split Line Heading",
			"base" => "split_line_heading",
			"icon" => "icon-wpb-split-line-heading",
			"allowed_container_element" => 'vc_row',
			"category" => esc_html__('Nectar Elements', 'js_composer'),
			"description" => esc_html__('Animated multi line heading', 'js_composer'),
			"params" => array(
				array(
			      "type" => "textarea_html",
			      "holder" => "div",
			      "heading" => esc_html__("Text Content", "js_composer"),
			      "param_name" => "content",
			      "value" => '',
			      "description" => esc_html__("Each Line of this editor will be animated separately. Separate text with the Enter or Return key on your Keyboard.", "js_composer"),
			      "admin_label" => false
			    ),

			)
	);

?>