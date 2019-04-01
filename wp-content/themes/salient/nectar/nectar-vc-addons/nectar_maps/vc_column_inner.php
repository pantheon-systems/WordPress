<?php 

return array(
		"name" => esc_html__("Column", "js_composer" ),
		"base" => "vc_column_inner",
		"class" => "",
		"icon" => "",
		"wrapper_class" => "",
		"controls" => "full",
		"allowed_container_element" => false,
		"content_element" => false,
		"is_container" => true,
		"params" => array(
			array(
				"type" => "checkbox",
				"class" => "",
				"heading" => "Enable Animation",
				"value" => array("Enable Column Animation?" => "true" ),
				"param_name" => "enable_animation",
				"description" => ""
			),

			array(
				"type" => "dropdown",
				"class" => "",
				'save_always' => true,
				"heading" => "Animation",
				"param_name" => "animation",
				"value" => array(
					 "None" => "none",
				     "Fade In" => "fade-in",
			  		 "Fade In From Left" => "fade-in-from-left",
			  		 "Fade In Right" => "fade-in-from-right",
			  		 "Fade In From Bottom" => "fade-in-from-bottom",
			  		 "Grow In" => "grow-in",
			  		 "Flip In Horizontal" => "flip-in",
			  		 "Flip In Vertical" => "flip-in-vertical",
			  		 "Reveal From Right" => "reveal-from-right",
			  		 "Reveal From Bottom" => "reveal-from-bottom",
			  		 "Reveal From Left" => "reveal-from-left",
			  		 "Reveal From Top" => "reveal-from-top"		
				),
				"dependency" => Array('element' => "enable_animation", 'not_empty' => true)
			),

			array(
				"type" => "textfield",
				"class" => "",
				"heading" => "Animation Delay",
				"param_name" => "delay",
				"admin_label" => false,
				"description" => esc_html__("Enter delay (in milliseconds) if needed e.g. 150. This parameter comes in handy when creating the animate in \"one by one\" effect.", "js_composer"),
				"dependency" => Array('element' => "enable_animation", 'not_empty' => true)
			),

			array(
				"type" => "checkbox",
				"class" => "",
				"heading" => "Boxed Column",
				"value" => array("Boxed Style" => "true" ),
				"param_name" => "boxed",
				"description" => ""
			),

			array(
				"type" => "fws_image",
				"class" => "",
				"heading" => "Background Image",
				"param_name" => "background_image",
				"value" => "",
				"description" => "",
			),

			array(
				"type" => "checkbox",
				"class" => "",
				"heading" => "Scale Background Image To Column",
				"value" => array("Enable" => "true" ),
				"param_name" => "enable_bg_scale",
				"description" => "",
				"dependency" => array('element' => "background_image", 'not_empty' => true)
			),

			array(
				"type" => "dropdown",
				"class" => "",
				'save_always' => true,
				"heading" => "Column Padding",
				"param_name" => "column_padding",
				"value" => array(
					"None" => "no-extra-padding",
					"1%" => "padding-1-percent",
					"2%" => "padding-2-percent",
					"3%" => "padding-3-percent",
					"4%" => "padding-4-percent",
					"5%" => "padding-5-percent",
					"6%" => "padding-6-percent",
					"7%" => "padding-7-percent",
					"8%" => "padding-8-percent",
					"9%" => "padding-9-percent",
					"10%" => "padding-10-percent",
					"11%" => "padding-11-percent",
					"12%" => "padding-12-percent",
					"13%" => "padding-13-percent",
					"14%" => "padding-14-percent",
					"15%" => "padding-15-percent",
					"16%" => "padding-16-percent",
					"17%" => "padding-17-percent"
				),
				"description" => "When using the full width content row type or providing a background color/image for the column, you have the option to define the amount of padding your column will receive."
			),

			array(
				"type" => "dropdown",
				"class" => "",
				'save_always' => true,
				"heading" => "Column Padding Position",
				"param_name" => "column_padding_position",
				"value" => array(
					"All Sides" => 'all',
					'Top' => "top",
					'Right' => 'right',
					'Left' => 'left',
					'Bottom' => 'bottom',
					'Left & Right' => 'left-right',
					'Top & Right' => 'top-right',
					'Top & Left' => 'top-left',
					'Top & Bottom' => 'top-bottom',
					'Bottom & Right' => 'bottom-right',
					'Bottom & Left' => 'bottom-left',
				),
				"description" => "Use this to fine tune where the column padding will take effect"
			),

			array(
				"type" => "colorpicker",
				"class" => "",
				"heading" => "Background Color",
				"param_name" => "background_color",
				"value" => "",
				"description" => "",
			),

			array(
				"type" => "dropdown",
				"class" => "",
				'save_always' => true,
				"heading" => "Background Color Opacity",
				"param_name" => "background_color_opacity",
				"value" => array(
					"1" => "1",
					"0.9" => "0.9",
					"0.8" => "0.8",
					"0.7" => "0.7",
					"0.6" => "0.6",
					"0.5" => "0.5",
					"0.4" => "0.4",
					"0.3" => "0.3",
					"0.2" => "0.2",
					"0.1" => "0.1",
				)
				
			),


			array(
				"type" => "checkbox",
				"class" => "",
				"heading" => "Centered Content",
				"value" => array("Centered Content Alignment" => "true" ),
				"param_name" => "centered_text",
				"description" => ""
			),

			array(
				"type" => "textfield",
				"class" => "",
				"heading" => "Column Link",
				"param_name" => "column_link",
				"admin_label" => false,
				"description" => "If you wish for this column to link somewhere, enter the URL in here",
			),

			array(
				"type" => "textfield",
				"class" => "",
				"heading" => "Extra Class Name",
				"param_name" => "el_class",
				"value" => ""
			),

			array(
				'type' => 'dropdown',
				'save_always' => true,
				'heading' => esc_html__('Width', 'js_composer' ),
				'param_name' => 'width',
				'value' => $vc_column_width_list,
				'group' => esc_html__('Responsive Options', 'js_composer' ),
				'description' => esc_html__('Select column width.', 'js_composer' ),
				'std' => '1/1'
			),
			array(
				'type' => 'column_offset',
				'heading' => esc_html__('Responsiveness', 'js_composer' ),
				'param_name' => 'offset',
				'group' => esc_html__('Responsive Options', 'js_composer' ),
				'description' => esc_html__('Adjust column for different screen sizes. Control width, offset and visibility settings.', 'js_composer' )
			)
		),
		"js_view" => 'VcColumnView'
	);

?>