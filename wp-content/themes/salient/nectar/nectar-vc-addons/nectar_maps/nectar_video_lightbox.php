<?php 

return array(
	  "name" => esc_html__("Video Lightbox", "js_composer"),
	  "base" => "nectar_video_lightbox",
	  "icon" => "icon-wpb-video-lightbox",
	  "category" => esc_html__('Nectar Elements', 'js_composer'),
	  "description" => esc_html__('Add a video lightbox link', 'js_composer'),
	  "params" => array(
	  	array(
		  "type" => "dropdown",
		  "heading" => esc_html__("Link Style", "js_composer"),
		  "param_name" => "link_style",
		  "value" => array(
		     "Play Button" => "play_button",
		     "Play Button With text" => "play_button_with_text",
		     "Play Button With Preview Image" => "play_button_2",
			   "Nectar Button" => "nectar-button"
		   ),
		  'save_always' => true,
		  "admin_label" => true,
		  "description" => esc_html__("Please select your link style", "js_composer")	  
		),
		array(
	      "type" => "textfield",
	      "heading" => esc_html__("Video URL", "js_composer"),
	      "param_name" => "video_url",
	      "admin_label" => false,
	      "description" => esc_html__("The URL to your video on Youtube or Vimeo e.g. https://vimeo.com/118023315 or https://www.youtube.com/watch?v=6oTurM7gESE etc.", "js_composer")
	    ),
	    array(
		  "type" => "dropdown",
		  "heading" => esc_html__("Play Button Color", "js_composer"),
		  "param_name" => "nectar_play_button_color",
		  "value" => array(
			 "Accent Color" => "Default-Accent-Color",
			 "Extra Color 1" => "Extra-Color-1",
			 "Extra Color 2" => "Extra-Color-2",	
			 "Extra Color 3" => "Extra-Color-3"
		   ),
		  'save_always' => true,
		  "dependency" => array('element' => "link_style", 'value' => array("play_button_2","play_button_with_text")),
		  'description' => __( 'Choose a color from your','salient') . ' <a target="_blank" href="'. esc_url(admin_url()) .'?page=Salient&tab=6"> ' . esc_html__('globally defined color scheme','salient') . '</a>',
		),
	    array(
	      "type" => "fws_image",
	      "heading" => esc_html__("Video Preview Image", "js_composer"),
	      "param_name" => "image_url",
	      "value" => "",
	      "dependency" => array('element' => "link_style", 'value' => "play_button_2"),
	      "description" => esc_html__("Select image from media library.", "js_composer")
	    ),
	    array(
	      "type" => "dropdown",
	      "dependency" => array('element' => "link_style", 'value' => "play_button_2"),
	      "heading" => esc_html__("Hover Effect", "js_composer"),
	      'save_always' => true,
	      "param_name" => "hover_effect",
	      "value" => array(esc_html__("Zoom BG Image", "js_composer") => "defaut", esc_html__("Zoom Button", "js_composer") => "zoom_button"),
	      "description" => esc_html__("Select your desired hover effect", "js_composer")
	    ),
	    array(
	      "type" => "dropdown",
	      "dependency" => array('element' => "link_style", 'value' => "play_button_2"),
	      "heading" => esc_html__("Box Shadow", "js_composer"),
	      'save_always' => true,
	      "param_name" => "box_shadow",
	      "value" => array(esc_html__("None", "js_composer") => "none", esc_html__("Small Depth", "js_composer") => "small_depth", esc_html__("Medium Depth", "js_composer") => "medium_depth", esc_html__("Large Depth", "js_composer") => "large_depth", esc_html__("Very Large Depth", "js_composer") => "x_large_depth"),
	      "description" => esc_html__("Select your desired image box shadow", "js_composer")
	    ),
			array(
					"type" => "dropdown",
					"heading" => esc_html__("Border Radius", "js_composer"),
					'save_always' => true,
					"dependency" => array('element' => "link_style", 'value' => "play_button_2"),
					"param_name" => "border_radius",
					"value" => array(
						esc_html__("0px", "js_composer") => "none",
						esc_html__("3px", "js_composer") => "3px",
						esc_html__("5px", "js_composer") => "5px", 
						esc_html__("10px", "js_composer") => "10px", 
						esc_html__("15px", "js_composer") => "15px", 
						esc_html__("20px", "js_composer") => "20px"),
				),	
				array(
						"type" => "dropdown",
						"heading" => esc_html__("Play Button Size", "js_composer"),
						'save_always' => true,
						"dependency" => array('element' => "link_style", 'value' => "play_button_2"),
						"param_name" => "play_button_size",
						"value" => array(
							esc_html__("Default", "js_composer") => "default",
							esc_html__("Larger", "js_composer") => "larger")
					),	
	    array(
	      "type" => "textfield",
	      "heading" => esc_html__("Link Text", "js_composer"),
	      "param_name" => "link_text",
	      "admin_label" => false,
	      "dependency" => array('element' => "link_style", 'value' => array("nectar-button","play_button_with_text")),
	      "description" => esc_html__("The text that will be displayed for your link", "js_composer")
	    ),
	   	array(
			"type" => "dropdown",
			"class" => "",
			'save_always' => true,
			"heading" => "Text Font Style",
			"dependency" => array('element' => "link_style", 'value' => array("play_button_with_text")),
			"description" => esc_html__("Choose what element your link text will inherit styling from", "js_composer"),
			"param_name" => "font_style",
			"value" => array(
				"Paragraph" => "p",
				"H6" => "h6",
				"H5" => "h5",
				"H4" => "h4",
				"H3" => "h3",
				"H2" => "h2",
				"H1" => "h1"
			)),

	     array(
		  "type" => "dropdown",
		  "heading" => esc_html__("Color", "js_composer"),
		  "param_name" => "nectar_button_color",
		  "value" => array(
			 "Accent Color" => "Default-Accent-Color",
			 "Extra Color 1" => "Default-Extra-Color-1",
			 "Extra Color 2" => "Default-Extra-Color-2",	
			 "Extra Color 3" => "Default-Extra-Color-3",
			 "Transparent Accent Color" =>  "Transparent-Accent-Color",
			 "Transparent Extra Color 1" => "Transparent-Extra-Color-1",
			 "Transparent Extra Color 2" => "Transparent-Extra-Color-2",	
			 "Transparent Extra Color 3" => "Transparent-Extra-Color-3"
		   ),
		  'save_always' => true,
		  "dependency" => array('element' => "link_style", 'value' => "nectar-button"),
		  'description' => __( 'Choose a color from your','salient') . ' <a target="_blank" href="'. esc_url(admin_url()) .'?page=Salient&tab=6"> ' . esc_html__('globally defined color scheme','salient') . '</a>',
		),

	  )
	);

?>