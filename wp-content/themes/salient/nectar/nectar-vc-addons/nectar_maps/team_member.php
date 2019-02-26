<?php 

return array(
		  "name" => esc_html__("Team Member", "js_composer"),
		  "base" => "team_member",
		  "icon" => "icon-wpb-team-member",
		  "category" => esc_html__('Nectar Elements', 'js_composer'),
		  "description" => esc_html__('Add a team member element', 'js_composer'),
		  "params" => array(
		 	 array(
		      "type" => "fws_image",
		      "heading" => esc_html__("Image", "js_composer"),
		      "param_name" => "image_url",
		      "value" => "",
		      "description" => esc_html__("Select image from media library.", "js_composer")
		    ),
		 	 array(
		      "type" => "fws_image",
		      "heading" => esc_html__("Bio Image", "js_composer"),
		      "param_name" => "bio_image_url",
		      "value" => "",
		       "dependency" => Array('element' => "team_memeber_style", 'value' => array('bio_fullscreen')),
		      "description" => esc_html__("Image Size Guidelines", "js_composer") . "<br/><strong>" . esc_html__("Bio Image:", "js_composer") . "</strong>" . esc_html__("large with a portrait aspect ratio - will be shown at the full screen height at 50% of the page width.", "js_composer") . "<br/> <strong>" . esc_html__("Team Small Image:", "js_composer") . "</strong> " . esc_html__("Will display at 500x500 so ensure the image you're uploading is at least that size.", "js_composer")
		    ),
		    array(
			  "type" => "dropdown",
			  "heading" => esc_html__("Team Member Stlye", "js_composer"),
			  "param_name" => "team_memeber_style",
			  "value" => array(
				 "Meta below" => "meta_below",
				 "Meta overlaid" => "meta_overlaid",
				 "Meta overlaid alt" => "meta_overlaid_alt",
				 "Bio Shown Fullscreen Modal" => "bio_fullscreen"
			   ),
			  'save_always' => true,
			  "description" => esc_html__("Please select the style you desire for your team member.", "js_composer")
			),
		    array(
		      "type" => "textfield",
		      "heading" => esc_html__("Name", "js_composer"),
		      "param_name" => "name",
		      "admin_label" => true,
		      "description" => esc_html__("Please enter the name of your team member", "js_composer")
		    ),
			array(
		      "type" => "textfield",
		      "heading" => esc_html__("Job Position", "js_composer"),
		      "param_name" => "job_position",
		      "admin_label" => true,
		      "description" => esc_html__("Please enter the job position for your team member", "js_composer")
		    ),
		    array(
		      "type" => "textarea",
		      "heading" => esc_html__("Team Member Bio", "js_composer"),
		      "param_name" => "team_member_bio",
		      "description" => esc_html__("The main text portion of your team member", "js_composer"),
		      "dependency" => Array('element' => "team_memeber_style", 'value' => array('bio_fullscreen'))
		    ),
		    array(
		      "type" => "textarea",
		      "heading" => esc_html__("Description", "js_composer"),
		      "param_name" => "description",
		      "description" => esc_html__("The main text portion of your team member", "js_composer"),
		      "dependency" => Array('element' => "team_memeber_style", 'value' => array('meta_below'))
		    ),
				array(
		      "type" => "iconpicker",
		      "heading" => esc_html__("Social Icon", "js_composer"),
		      "param_name" => "social_icon_1",
		      "settings" => array( "emptyIcon" => true, "iconsPerPage" => 4000 ),
					"dependency" => Array('element' => "team_memeber_style", 'value' => array('bio_fullscreen')),
		      "description" => ''
		    ),
				array(
		      "type" => "textfield",
		      "heading" => esc_html__("Social Link", "js_composer"),
		      "param_name" => "social_link_1",
					"dependency" => Array('element' => "team_memeber_style", 'value' => array('bio_fullscreen')),
		      "description" => esc_html__("Please enter the URL here", "js_composer")
		    ),
				array(
		      "type" => "iconpicker",
		      "heading" => esc_html__("Social Icon 2", "js_composer"),
		      "param_name" => "social_icon_2",
		      "settings" => array( "emptyIcon" => true, "iconsPerPage" => 4000),
					"dependency" => Array('element' => "team_memeber_style", 'value' => array('bio_fullscreen')),
		      "description" => ''
		    ),
				array(
		      "type" => "textfield",
		      "heading" => esc_html__("Social Link 2", "js_composer"),
		      "param_name" => "social_link_2",
					"dependency" => Array('element' => "team_memeber_style", 'value' => array('bio_fullscreen')),
		      "description" => esc_html__("Please enter the URL here", "js_composer")
		    ),
				
				array(
		      "type" => "iconpicker",
		      "heading" => esc_html__("Social Icon 3", "js_composer"),
		      "param_name" => "social_icon_3",
		      "settings" => array( "emptyIcon" => true, "iconsPerPage" => 4000),
					"dependency" => Array('element' => "team_memeber_style", 'value' => array('bio_fullscreen')),
		      "description" => ''
		    ),
				array(
		      "type" => "textfield",
		      "heading" => esc_html__("Social Link 3", "js_composer"),
		      "param_name" => "social_link_3",
					"dependency" => Array('element' => "team_memeber_style", 'value' => array('bio_fullscreen')),
		      "description" => esc_html__("Please enter the URL here", "js_composer")
		    ),
				
				array(
		      "type" => "iconpicker",
		      "heading" => esc_html__("Social Icon 4", "js_composer"),
		      "param_name" => "social_icon_4",
		      "settings" => array( "emptyIcon" => true, "iconsPerPage" => 4000),
					"dependency" => Array('element' => "team_memeber_style", 'value' => array('bio_fullscreen')),
		      "description" => ''
		    ),
				array(
		      "type" => "textfield",
		      "heading" => esc_html__("Social Link 4", "js_composer"),
		      "param_name" => "social_link_4",
					"dependency" => Array('element' => "team_memeber_style", 'value' => array('bio_fullscreen')),
		      "description" => esc_html__("Please enter the URL here", "js_composer")
		    ),
				
		    array(
		      "type" => "textarea",
		      "heading" => esc_html__("Social Media", "js_composer"),
		      "param_name" => "social",
		      "dependency" => Array('element' => "team_memeber_style", 'value' => array('meta_below')),
		      "description" => esc_html__("Enter any social media links with a comma separated list. e.g. Facebook,http://facebook.com, Twitter,http://twitter.com", "js_composer")
		    ),
		    array(
			  "type" => "dropdown",
			  "heading" => esc_html__("Team Member Link Type", "js_composer"),
			  "param_name" => "link_element",
			  "value" => array(
				 "None" => "none",
				 "Image" => "image",
				 "Name" => "name",	
				 "Both" => "both"
			   ),
			  'save_always' => true,
			   "dependency" => Array('element' => "team_memeber_style", 'value' => array('meta_below')),
			  "description" => esc_html__("Please select how you wish to link your team member to an arbitrary URL", "js_composer")
			),
			array(
		      "type" => "textfield",
		      "heading" => esc_html__("Team Member Link URL", "js_composer"),
		      "param_name" => "link_url",
		      "admin_label" => false,
		      "description" => esc_html__("Please enter the URL for your team member link", "js_composer"),
		      "dependency" => Array('element' => "link_element", 'value' => array('image', 'name', 'both'))
		    ),
		    array(
		      "type" => "textfield",
		      "heading" => esc_html__("Team Member Link URL", "js_composer"),
		      "param_name" => "link_url_2",
		      "admin_label" => false,
		      "description" => esc_html__("Please enter the URL for your team member link", "js_composer"),
		      "dependency" => Array('element' => "team_memeber_style", 'value' => array('meta_overlaid','meta_overlaid_alt')),
		    ),
				array(
				 "type" => "checkbox",
				 "class" => "",
				 "heading" => "Open Team Member Link In New Tab",
				 "value" => array("Yes, please" => "true" ),
				 "param_name" => "team_member_link_new_tab",
				 "description" => "",
				 "dependency" => Array('element' => "team_memeber_style", 'value' => array('meta_below','meta_overlaid','meta_overlaid_alt')),
				 ),
		     array(
			  "type" => "dropdown",
			  "heading" => esc_html__("Link Color", "js_composer"),
			  "param_name" => "color",
			  "value" => array(
				 "Accent Color" => "Accent-Color",
				 "Extra Color-1" => "Extra-Color-1",
				 "Extra Color-2" => "Extra-Color-2",	
				 "Extra Color-3" => "Extra-Color-3"
			   ),
			  'save_always' => true,
			   "dependency" => Array('element' => "team_memeber_style", 'value' => array('meta_below')),
			  'description' => __( 'Choose a color from your','salient') . ' <a target="_blank" href="'. esc_url(admin_url()) .'?page=Salient&tab=6"> ' . esc_html__('globally defined color scheme','salient') . '</a>',
			)
		  )
		);

?>