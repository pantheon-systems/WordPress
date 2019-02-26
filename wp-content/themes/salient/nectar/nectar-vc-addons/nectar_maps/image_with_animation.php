<?php 

return array(
	  "name" => esc_html__("Single Image", "js_composer"),
	  "base" => "image_with_animation",
	  "icon" => "icon-wpb-single-image",
	  "category" => esc_html__('Nectar Elements', 'js_composer'),
	  "description" => esc_html__('Simple image with CSS animation', 'js_composer'),
	  "params" => array(
	    array(
	      "type" => "fws_image",
	      "heading" => esc_html__("Image", "js_composer"),
	      "param_name" => "image_url",
	      "value" => "",
	      "description" => esc_html__("Select image from media library.", "js_composer")
	    ),
	    array(
	      "type" => "dropdown",
	      "heading" => esc_html__("Image Alignment", "js_composer"),
	      'save_always' => true,
	      "param_name" => "alignment",
	      "value" => array(esc_html__("Align left", "js_composer") => "", esc_html__("Align right", "js_composer") => "right", esc_html__("Align center", "js_composer") => "center"),
	      "description" => esc_html__("Select image alignment.", "js_composer")
	    ),
	    array(
		  "type" => "dropdown",
		  "heading" => esc_html__("CSS Animation", "js_composer"),
		  "param_name" => "animation",
		  "admin_label" => true,
		  "value" => array(
			    esc_html__("Fade In", "js_composer") => "Fade In", 
			    esc_html__("Fade In From Left", "js_composer") => "Fade In From Left", 
			    esc_html__("Fade In From Right", "js_composer") => "Fade In From Right", 
			    esc_html__("Fade In From Bottom", "js_composer") => "Fade In From Bottom", 
			    esc_html__("Grow In", "js_composer") => "Grow In",
			    esc_html__("Flip In Horizontal", "js_composer") => "Flip In",
			    esc_html__("Flip In Vertical", "js_composer") => "flip-in-vertical",
			    esc_html__("None", "js_composer") => "None"
			),
		  'save_always' => true,
		  "description" => esc_html__("Select animation type if you want this element to be animated when it enters into the browsers viewport. Note: Works only in modern browsers.", "js_composer")
		),
		array(
	      "type" => "textfield",
	      "heading" => esc_html__("Animation Delay", "js_composer"),
	      "param_name" => "delay",
	      "description" => esc_html__("Enter delay (in milliseconds) if needed e.g. 150. This parameter comes in handy when creating the animate in \"one by one\" effect in horizontal columns.", "js_composer")
	    ),
	    array(
	      "type" => 'checkbox',
	      "heading" => esc_html__("Link to large image?", "js_composer"),
	      "param_name" => "img_link_large",
	      "description" => esc_html__("If selected, image will be linked to the bigger image.", "js_composer"),
	      "value" => Array(esc_html__("Yes, please", "js_composer") => 'yes')
	    ),
	    array(
	      "type" => "textfield",
	      "heading" => esc_html__("Image link", "js_composer"),
	      "param_name" => "img_link",
	      "description" => esc_html__("Enter url if you want this image to have link.", "js_composer"),
	      "dependency" => Array('element' => "img_link_large", 'is_empty' => true)
	    ),
	    array(
	      "type" => "dropdown",
	      "heading" => esc_html__("Link Target", "js_composer"),
	      "param_name" => "img_link_target",
	      "value" => array(esc_html__("Same window", "js_composer") => "_self", esc_html__("New window", "js_composer") => "_blank"),
	      "dependency" => Array('element' => "img_link", 'not_empty' => true)
	    ),
	    array(
	      "type" => "textfield",
	      "heading" => esc_html__("Extra class name", "js_composer"),
	      "param_name" => "el_class",
	      "description" => esc_html__("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "js_composer")
	    ),
			array(
					"type" => "dropdown",
					"heading" => esc_html__("Border Radius", "js_composer"),
					'save_always' => true,
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
	      "heading" => esc_html__("Box Shadow", "js_composer"),
	      'save_always' => true,
	      "param_name" => "box_shadow",
	      "value" => array(esc_html__("None", "js_composer") => "none", esc_html__("Small Depth", "js_composer") => "small_depth", esc_html__("Medium Depth", "js_composer") => "medium_depth", esc_html__("Large Depth", "js_composer") => "large_depth", esc_html__("Very Large Depth", "js_composer") => "x_large_depth"),
	      "description" => esc_html__("Select your desired image box shadow", "js_composer")
	    ),
	    array(
	      "type" => "dropdown",
	      "heading" => esc_html__("Max Width", "js_composer"),
	      'save_always' => true,
	      "param_name" => "max_width",
	      "value" => array(
		      	esc_html__("100%", "js_composer") => "100%",
						esc_html__("110%", "js_composer") => "110%", 
		      	esc_html__("125%", "js_composer") => "125%", 
		      	esc_html__("150%", "js_composer") => "150%",
		      	esc_html__("165%", "js_composer") => "165%",  
		      	esc_html__("175%", "js_composer") => "175%", 
		      	esc_html__("200%", "js_composer") => "200%", 
		      	esc_html__("225%", "js_composer") => "225%", 
		      	esc_html__("250%", "js_composer") => "250%",
						esc_html__("75%", "js_composer") => "75%",
						esc_html__("50%", "js_composer") => "50%"
	      ),
	      "description" => esc_html__("Select your desired max width here - by default images are not allowed to display larger than the column they're contained in. Changing this to a higher value will allow you to create designs where your image overflows out of the column partially off screen.", "js_composer")
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
	    )
			
	  )
	);

?>