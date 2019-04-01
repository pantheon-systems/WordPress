<?php 

return array(
			"name" => "Horizontal List Item",
			"base" => "nectar_horizontal_list_item",
			"icon" => "icon-wpb-nectar-horizontal-list-item",
			"allowed_container_element" => 'vc_row',
			"category" => esc_html__('Nectar Elements', 'js_composer'),
			"description" => esc_html__('Organize data into a clean list', 'js_composer'),
			"params" => array(
				array(
					"type" => "dropdown",
					"class" => "",
					"heading" => "Columns",
					'save_always' => true,
					"param_name" => "columns",
					"value" => array(
						"1" => "1",
						"2" => "2",
						"3" => "3",
						"4" => "4"
					)
				),
				
				array(
					"type" => "dropdown",
					"class" => "",
					"heading" => "Column Layout",
					'save_always' => true,
					"param_name" => "column_layout_using_2_columns",
					"value" => array(
						"Even Widths" => "even",
						"70% | 30%" => "large_first",
						"80% | 20%" => "xlarge_first",
						"30% | 70%" => "small_first",
						"20% | 80%" => "xsmall_first",
					),
					"dependency" => Array('element' => "columns", 'value' => array('2')),
				),

				array(
					"type" => "dropdown",
					"class" => "",
					"heading" => "Column Layout",
					'save_always' => true,
					"param_name" => "column_layout_using_3_columns",
					"value" => array(
						"Even Widths" => "even",
						"20% | 40% | 40%" => "small_first",
						"50% | 25% | 25%" => "large_first",
						"25% | 50% | 25%" => "large_middle",
						"25% | 25% | 50%" => "large_last",	
					),
					"dependency" => Array('element' => "columns", 'value' => array('3')),
				),

				array(
					"type" => "dropdown",
					"class" => "",
					"heading" => "Column Layout",
					'save_always' => true,
					"param_name" => "column_layout_using_4_columns",
					"value" => array(
						"Even Widths" => "even",
						"15% | 35% | 35% | 15%" => "small_first_last",
						"35% | 35% | 15% | 15%" => "large_first",
						"35% | 15% | 35% | 15%" => "large_nth",
						"15% | 35% | 15% | 35%" => "small_nth",
					),
					"dependency" => Array('element' => "columns", 'value' => array('4')),
				),

				array(
					"type" => "dropdown",
					"edit_field_class" => "col-md-2",
					'save_always' => true,
					"heading" => "Text Alignment <span class='row-heading'>Column One</span>",
					"param_name" => "col_1_text_align",
					"value" => array(
						"Left" => "left",
						"Center" => "center",
						"Right" => "right"
					)
				),
				array(
					"type" => "dropdown",
					"edit_field_class" => "col-md-2",
					'save_always' => true,
					"heading" => "<span class='row-heading'>Column Two</span>",
					"param_name" => "col_2_text_align",
					"value" => array(
						"Left" => "left",
						"Center" => "center",
						"Right" => "right"
					),
					"dependency" => Array('element' => "columns", 'value' => array('2','3','4')),
				),
				array(
					"type" => "dropdown",
					"edit_field_class" => "col-md-2",
					'save_always' => true,
					"heading" => "<span class='row-heading'>Column Three</span>",
					"param_name" => "col_3_text_align",
					"value" => array(
						"Left" => "left",
						"Center" => "center",
						"Right" => "right"
					),
					"dependency" => Array('element' => "columns", 'value' => array('3','4')),
				),
				array(
					"type" => "dropdown",
					"edit_field_class" => "col-md-2",
					'save_always' => true,
					"heading" => "<span class='row-heading'>Column Four</span>",
					"param_name" => "col_4_text_align",
					"value" => array(
						"Left" => "left",
						"Center" => "center",
						"Right" => "right"
					),
					"dependency" => Array('element' => "columns", 'value' => array('4')),
				),


				array(
			      "type" => "textfield",
			      "heading" => esc_html__("Column One Content", "js_composer"),
			      "param_name" => "col_1_content",
			      "admin_label" => true,
			      "description" => esc_html__("Enter your column text here", "js_composer")
			    ),

				 array(
			      "type" => "textfield",
			      "heading" => esc_html__("Column Two Content", "js_composer"),
			      "param_name" => "col_2_content",
			       "admin_label" => true,
			      "description" => esc_html__("Enter your column text here", "js_composer"),
			      "dependency" => Array('element' => "columns", 'value' => array('2','3','4')),
			    ),

				  array(
			      "type" => "textfield",
			      "heading" => esc_html__("Column Three Content", "js_composer"),
			      "param_name" => "col_3_content",
			       "admin_label" => true,
			      "description" => esc_html__("Enter your column text here", "js_composer"),
			      "dependency" => Array('element' => "columns", 'value' => array('3','4')),
			    ),

				 array(
			      "type" => "textfield",
			      "heading" => esc_html__("Column Four Content", "js_composer"),
			      "param_name" => "col_4_content",
			       "admin_label" => true,
			      "description" => esc_html__("Enter your column text here", "js_composer"),
			      "dependency" => Array('element' => "columns", 'value' => array('4')),
			    ),

				 array(
			      "type" => "textfield",
			      "heading" => esc_html__("CTA Text", "js_composer"),
			      "param_name" => "cta_1_text",
			      "description" => esc_html__("Enter your CTA text here" , "js_composer"),
			      "group" => "Call To Action Button"
			    ),

				array(
			      "type" => "textfield",
			      "heading" => esc_html__("CTA Link URL", "js_composer"),
			      "param_name" => "cta_1_url",
			      "description" => esc_html__("Enter your URL here" , "js_composer"),
			      "group" => "Call To Action Button"
			    ),

				 array(
					"type" => "checkbox",
					"class" => "",
					"heading" => esc_html__("CTA Open in New Tab", "js_composer"),
			     	"param_name" => "cta_1_open_new_tab",
					"value" => Array(esc_html__("Yes", "js_composer") => 'true'),
					"group" => "Call To Action Button",
					"description" => ""
				),

				 array(
			      "type" => "textfield",
			      "heading" => esc_html__("CTA 2 Text", "js_composer"),
			      "param_name" => "cta_2_text",
			      "description" => esc_html__("Enter your CTA text here" , "js_composer"),
			      "group" => "Call To Action Button 2"
			    ),

				 array(
			      "type" => "textfield",
			      "heading" => esc_html__("CTA 2 Link URL", "js_composer"),
			      "param_name" => "cta_2_url",
			      "description" => esc_html__("Enter your URL here" , "js_composer"),
			      "group" => "Call To Action Button 2"
			    ),

				  array(
					"type" => "checkbox",
					"class" => "",
					"heading" => esc_html__("CTA 2 Open in New Tab", "js_composer"),
			     	"param_name" => "cta_2_open_new_tab",
					"value" => Array(esc_html__("Yes", "js_composer") => 'true'),
					"description" => "",
					"group" => "Call To Action Button 2"
				),


				array(
					"type" => "dropdown",
					"class" => "",
					"heading" => "Inherit Font From",
					'save_always' => true,
					"param_name" => "font_family",
					"value" => array(
						"p" => "p",
						"h6" => "h6",
						"h5" => "h5",
						"h4" => "h4",
						"h3" => "h3"
					)
				),

				array(
			      "type" => "textfield",
			      "heading" => esc_html__("Full Item Link URL", "js_composer"),
			      "param_name" => "url",
			      "description" => esc_html__("Adding a URL for this will link your entire list item" , "js_composer")
			    ),

			    array(
					"type" => "checkbox",
					"class" => "",
					"heading" => esc_html__("Open Full Link In New Tab", "js_composer"),
			     	"param_name" => "open_new_tab",
					"value" => Array(esc_html__("Yes", "js_composer") => 'true'),
					"description" => ""
				),

				 array(
				  "type" => "dropdown",
				  "heading" => esc_html__("Hover Color", "js_composer"),
				  "param_name" => "hover_color",
				  "admin_label" => false,
				  "value" => array(
					 "Accent-Color" => "accent-color",
					 "Extra-Color-1" => "extra-color-1",
					 "Extra-Color-2" => "extra-color-2",	
					 "Extra-Color-3" => "extra-color-3",
					 "Black" => "black",
					 "White" => "white"
				   ),
				  'save_always' => true,
				  'description' => __( 'Choose a color from your','salient') . ' <a target="_blank" href="'. esc_url(admin_url()) .'?page=Salient&tab=6"> ' . esc_html__('globally defined color scheme','salient') . '</a>',
				),
				

			)
	);

?>