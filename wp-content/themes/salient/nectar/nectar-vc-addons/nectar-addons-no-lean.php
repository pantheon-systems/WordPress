<?php 

global $nectar_options;
$vc_is_wp_version_3_6_more = version_compare(preg_replace('/^([\d\.]+)(\-.*$)/', '$1', get_bloginfo('version')), '3.6') >= 0;

function nectar_set_vc_as_theme() {

	vc_set_as_theme($disable_updater = true);
	$template_directory = get_template_directory();

	if(defined( 'SALIENT_VC_ACTIVE')) {
	    $child_dir = $template_directory . '/nectar/nectar-vc-addons/vc_templates';
	    $parent_dir = $template_directory . '/nectar/nectar-vc-addons/vc_templates';

	    vc_set_shortcodes_templates_dir($parent_dir);
	    vc_set_shortcodes_templates_dir($child_dir);
	} else {

	    $child_dir = $template_directory . '/nectar/nectar-vc-addons/vc_templates';
	    $parent_dir = $template_directory . '/nectar/nectar-vc-addons/vc_templates';
	    vc_set_shortcodes_templates_dir($parent_dir);
	    vc_set_shortcodes_templates_dir($child_dir);
	}


	vc_disable_frontend();

}

add_action('vc_before_init', 'nectar_set_vc_as_theme');




add_filter( 'vc_load_default_templates', 'nectar_custom_template_modify_array' ); // Hook in
function nectar_custom_template_modify_array( $data ) {
    return array(); 
}

/*
vc_remove_element("vc_row");
vc_remove_element("vc_column");
vc_remove_element("vc_column_inner");*/
vc_remove_element("vc_button");
vc_remove_element("vc_button2");
vc_remove_element("vc_posts_slider");
vc_remove_element("vc_gmaps");
vc_remove_element("vc_teaser_grid");
vc_remove_element("vc_progress_bar");
vc_remove_element("vc_facebook");
vc_remove_element("vc_tweetmeme");
vc_remove_element("vc_googleplus");
vc_remove_element("vc_facebook");
vc_remove_element("vc_pinterest");
vc_remove_element("vc_message");
vc_remove_element("vc_posts_grid");
vc_remove_element("vc_carousel");
vc_remove_element("vc_flickr");
vc_remove_element("vc_tour");
vc_remove_element("vc_separator");
vc_remove_element("vc_single_image");
vc_remove_element("vc_cta_button");
vc_remove_element("vc_cta_button2");
vc_remove_element("vc_tta_tour");
vc_remove_element("vc_btn");
vc_remove_element("vc_basic_grid");
vc_remove_element("vc_round_chart");
vc_remove_element("vc_line_chart");
vc_remove_element("vc_cta");
vc_remove_element("vc_icon");
vc_remove_element("vc_media_grid");
vc_remove_element("vc_masonry_media_grid");
vc_remove_element("vc_masonry_grid");

vc_remove_element("vc_accordion");
vc_remove_element("vc_accordion_tab");
vc_remove_element("vc_toggle");
vc_remove_element("vc_tabs");
vc_remove_element("vc_tab");
vc_remove_element("vc_tta_tabs");
vc_remove_element("vc_tta_accordion");
vc_remove_element("vc_tta_pageable");

vc_remove_element("vc_empty_space");
vc_remove_element("vc_custom_heading");
vc_remove_element("vc_images_carousel");
vc_remove_element("vc_wp_archives");
vc_remove_element("vc_wp_calendar");
vc_remove_element("vc_wp_categories");
vc_remove_element("vc_wp_custommenu");
vc_remove_element("vc_wp_links");
vc_remove_element("vc_wp_meta");
vc_remove_element("vc_wp_pages");
vc_remove_element("vc_wp_posts");
vc_remove_element("vc_wp_recentcomments");
vc_remove_element("vc_wp_rss");
vc_remove_element("vc_wp_search");
vc_remove_element("vc_wp_tagcloud");
vc_remove_element("vc_wp_text");

//remove WC elements
function your_name_vc_remove_woocommerce() {
    if ( class_exists( 'woocommerce' ) ) {
        //vc_remove_element("woocommerce_cart");
		//vc_remove_element("woocommerce_checkout");
		//vc_remove_element("woocommerce_order_tracking");
		//vc_remove_element("woocommerce_my_account");
		vc_remove_element("recent_products");
		vc_remove_element("featured_products");
		vc_remove_element("product");
		vc_remove_element("products");
		//vc_remove_element("add_to_cart");
		vc_remove_element("add_to_cart_url");
		vc_remove_element("product_page");
		//vc_remove_element("product_category");
		//vc_remove_element("product_categories");
		vc_remove_element("sale_products");
		vc_remove_element("best_selling_products");
		vc_remove_element("top_rated_products");
		vc_remove_element("product_attribute");
    }
}
// Hook for admin editor.
add_action( 'vc_build_admin_page', 'your_name_vc_remove_woocommerce', 11 );

//only load shortcode logic on front when needed
$is_admin = is_admin();


function nectar_has_shortcode( $shortcode = NULL ) {

    // false because we have to search through the post content first
    //$found = false;

    // if no short code was provided, return false
   // if ( ! $shortcode ) {
    //    return $found;
    //}
    
    // check the post content for the short code
    //if ( stripos( $post_to_check, '[' . $shortcode) !== FALSE || stripos( $portfolio_extra_content, '[' . $shortcode) !== FALSE || $is_admin) {
        // we have found the short code
   //     $found = TRUE;
   // }

    // return our final results
   // return $found;
	return true;
}

// Create multi dropdown param type
add_shortcode_param( 'dropdown_multi', 'dropdown_multi_settings_field' );
function dropdown_multi_settings_field( $param, $value ) {

	 $param_line = '';
	 $param_line .= '<select multiple name="'. esc_attr( $param['param_name'] ).'" class="wpb_vc_param_value wpb-input wpb-select '. esc_attr( $param['param_name'] ).' '. esc_attr($param['type']).'">';
                foreach ( $param['value'] as $text_val => $val ) {
                    if ( is_numeric($text_val) && (is_string($val) || is_numeric($val)) ) {
                        $text_val = $val;
                    }
                    $text_val = __($text_val, "js_composer");
                    $selected = '';

                    if(!is_array($value)) {
                    	$param_value_arr = explode(',',$value);
                    } else {
                    	$param_value_arr = $value;
                    }
					
                    if ($value!=='' && in_array($val, $param_value_arr)) {
                        $selected = ' selected="selected"';
                    }
                    $param_line .= '<option class="'.$val.'" value="'.$val.'"'.$selected.'>'.$text_val.'</option>';
                }
                $param_line .= '</select>';

   return  $param_line;
}

add_shortcode_param( 'fws_image', 'fws_image_settings_field' );
function fws_image_settings_field( $param, $value ) {
		$param_line = '';
		$param_line .= '<input type="hidden" class="wpb_vc_param_value gallery_widget_attached_images_ids '.esc_attr($param['param_name']).' '.esc_attr($param['type']).'" name="'.esc_attr($param['param_name']).'" value="'.esc_attr($value).'"/>';
        //$param_line .= '<a class="button gallery_widget_add_images" href="#" use-single="true" title="'.__('Add image', "js_composer").'">'.__('Add image', "js_composer").'</a>';
        $param_line .= '<div class="gallery_widget_attached_images">';
        $param_line .= '<ul class="gallery_widget_attached_images_list">';
	
		if(strpos($value, "http://") !== false || strpos($value, "https://") !== false) {
			//$param_value = fjarrett_get_attachment_id_by_url($param_value);
			$param_line .= '<li class="added">
				<img src="'. esc_attr($value) .'" />
				<a href="#" class="icon-remove"></a>
			</li>';
		} else {
			$param_line .= ($value != '') ? fieldAttachedImages(explode(",", esc_attr($value))) : '';
		}
		
        
        $param_line .= '</ul>';
        $param_line .= '</div>';
        $param_line .= '<div class="gallery_widget_site_images">';
        // $param_line .= siteAttachedImages(explode(",", $param_value));
        $param_line .= '</div>';
        $param_line .= '<a class="gallery_widget_add_images" href="#" use-single="true" title="'.__('Add image', "js_composer").'">'.__('Add image', "js_composer").'</a>';//class: button
        //$param_line .= '<div class="wpb_clear"></div>';

        return $param_line;
}

if(function_exists('vc_add_shortcode_param')) {
	vc_add_shortcode_param( 'hotspot_image_preview', 'hotspot_image_preview_field' );
	function hotspot_image_preview_field( $settings, $value ) {
	   
	   $image_output = null;
	   if(!empty($value)) $image_output = '<img src="'. esc_attr($value) . '" alt="preview" />';

	   return '<div id="nectar_image_with_hotspots_preview"><input name="' . esc_attr( $settings['param_name'] ) . '" type="hidden" class="wpb_vc_param_value ' . esc_attr( $settings['param_name'] ) . '" value="'.$value.'" /> '.$image_output. '</div>'; 
	}
}

// VC_Row 

add_action('vc_before_init', 'nectar_custom_maps');

function nectar_custom_maps() {

	$vc_is_wp_version_3_6_more = version_compare(preg_replace('/^([\d\.]+)(\-.*$)/', '$1', get_bloginfo('version')), '3.6') >= 0;
	global $nectar_options;
	$is_admin = is_admin();

	vc_map( array(
		'name' => __( 'Row', 'js_composer' ),
		'base' => 'vc_row',
		'is_container' => true,
		'icon' => 'icon-wpb-row',
		'show_settings_on_create' => false,
		'category' => __( 'Structure', 'js_composer' ),
		'description' => __( 'Place content elements inside the row', 'js_composer' ),
		'params' => array(
			 array(
				"type" => "dropdown",
				"class" => "",
				"heading" => "Type",
				"param_name" => "type",
				'save_always' => true,
				"value" => array(
					"In Container" => "in_container",
					"Full Width Background" => "full_width_background",
					"Full Width Content" => "full_width_content"		
					)
			),

			 array(
				"type" => "dropdown",
				"class" => "",
				"heading" => "Fullscreen Row Position",
				"param_name" => "full_screen_row_position",
				'save_always' => true,
				'description' => __( 'Select how your content will be aligned in the fullscreen row - if full height is selected, columns will be 100% of the screen height as well.', 'js_composer' ),
				"value" => array(
					"Middle" => "middle",
					"Top" => "top",
					"Bottom" => "bottom",
					"Full Height" => 'full_height'		
					)
			),

			 array(
				'type' => 'checkbox',
				'heading' => __( 'Equal height', 'js_composer' ),
				'param_name' => 'equal_height',
				'description' => __( 'If checked columns will be set to equal height.', 'js_composer' ),
				'value' => array( __( 'Yes', 'js_composer' ) => 'yes' )
			),
			array(
				'type' => 'dropdown',
				'heading' => __( 'Column Content position', 'js_composer' ),
				'param_name' => 'content_placement',
				'value' => array(
					__( 'Default', 'js_composer' ) => '',
					__( 'Top', 'js_composer' ) => 'top',
					__( 'Middle', 'js_composer' ) => 'middle',
					__( 'Bottom', 'js_composer' ) => 'bottom',
				),
				'description' => __( 'Select content position within columns.', 'js_composer' ),
				"dependency" => Array('element' => "equal_height", 'not_empty' => true)
			),
			array(
				"type" => "checkbox",
				"class" => "",
				"heading" => "Vertically Center Columns",
				"value" => array("Make all columns in this row vertically centered?" => "true" ),
				"param_name" => "vertically_center_columns",
				"description" => "",
				"dependency" => Array('element' => "type", 'value' => array('full_width_content'))
			),

			array(
				"type" => "fws_image",
				"class" => "",
				"heading" => "Background Image",
				"param_name" => "bg_image",
				"value" => "",
				"description" => "",
				"dependency" => Array('element' => "mouse_based_parallax_bg", 'is_empty' => true)
			),

			array(
				"type" => "checkbox",
				"class" => "",
				"heading" => "Background Image Mobile Hidden",
				"param_name" => "background_image_mobile_hidden",
				"value" => array("Hide Background Image on Mobile Views?" => "true" ),
				"description" => "Use this to remove your row BG image from displaying on mobile devices",
				"dependency" => Array('element' => "bg_image", 'not_empty' => true)
			),

			array(
				"type" => "dropdown",
				"class" => "",
				'save_always' => true,
				"heading" => "Background Position",
				"param_name" => "bg_position",
				"value" => array(
					 "Left Top" => "left top",
			  		 "Left Center" => "left center",
			  		 "Left Bottom" => "left bottom",
			  		 "Center Top" => "center top",
			  		 "Center Center" => "center center",
			  		 "Center Bottom" => "center bottom",
			  		 "Right Top" => "right top",
			  		 "Right Center" => "right center",
			  		 "Right Bottom" => "right bottom"
				),
				"dependency" => Array('element' => "bg_image", 'not_empty' => true)
			),


			array(
				"type" => "dropdown",
				"class" => "",
				"heading" => "Background Repeat",
				"param_name" => "bg_repeat",
				'save_always' => true,
				"value" => array(
					"No Repeat" => "no-repeat",
					"Repeat" => "repeat"
				),
				"dependency" => Array('element' => "bg_image", 'not_empty' => true)
			),

			array(
				"type" => "checkbox",
				"class" => "",
				"heading" => "Parallax Background",
				"value" => array("Enable Parallax Background?" => "true" ),
				"param_name" => "parallax_bg",
				"description" => "",
				"dependency" => Array('element' => "bg_image", 'not_empty' => true)
			),

			array(
				"type" => "dropdown",
				"class" => "",
				"description" => "The faster you choose, the closer your BG will match the users scroll speed",
				"heading" => "Parallax Background Speed",
				"param_name" => "parallax_bg_speed",
				'save_always' => true,
				"value" => array(
					 "Slow" => "slow",
			  		 "Medium" => "medium",
			  		 "Fast" => "fast",
			  		 "Fixed" => "fixed"
				),
				"dependency" => Array('element' => "parallax_bg", 'not_empty' => true)
			),

			array(
				"type" => "colorpicker",
				"class" => "",
				"heading" => "Background Color",
				"param_name" => "bg_color",
				"value" => "",
				"description" => ""
			),
			array(
				"type" => "checkbox",
				"class" => "",
				"heading" => "Mouse Based Parallax Scene",
				"value" => array("Enable Mouse Based Parallax BG?" => "true" ),
				"param_name" => "mouse_based_parallax_bg",
				"description" => ""
			),

			 array(
				  "type" => "dropdown",
				  "heading" => __("Scene Positioning", "js_composer"),
				  "param_name" => "scene_position",
				  'save_always' => true,
				  "value" => array(
			  		 "Center" => "center",
			  		 "Top" => "top",
			  		 "Bottom" => "bottom"
					),
				  "description" => __("Select your desired scene alignment within your row", "js_composer")
			),

			 array(
				"type" => "textfield",
				"class" => "",
				"heading" => "Scene Parallax Overall Strength",
				"value" => "",
				"param_name" => "mouse_sensitivity",
				"description" => "Enter a number between 1 and 25 that will effect the overall strength of the parallax movement within the entire scene - the default is 10."
			),


			array(
				"type" => "fws_image",
				"class" => "",
				"heading" => "Scene Layer One",
				"param_name" => "layer_one_image",
				"value" => "",
				"description" => "Please upload all of your layers at the same dimensions to ensure accurate placement."
			),

			array(
				"type" => "textfield",
				"class" => "",
				"heading" => "Layer One Strength",
				"value" => "",
				"param_name" => "layer_one_strength",
				"description" => "Enter a number <strong>between 0 and 1</strong> that will determine the strength this layer responds to mouse movement. <br/><br/>By default each layer will increment by .2"
			),

			array(
				"type" => "fws_image",
				"class" => "",
				"heading" => "Scene Layer Two",
				"param_name" => "layer_two_image",
				"value" => "",
				"description" => ""
			),

			array(
				"type" => "textfield",
				"class" => "",
				"heading" => "Layer Two Strength",
				"value" => "",
				"param_name" => "layer_two_strength",
				"description" => "See the description on \"Layer One Strength\" for guidelines on this property."
			),

			array(
				"type" => "fws_image",
				"class" => "",
				"heading" => "Scene Layer Three",
				"param_name" => "layer_three_image",
				"value" => "",
				"description" => ""
			),

			array(
				"type" => "textfield",
				"class" => "",
				"heading" => "Layer Three Strength",
				"value" => "",
				"param_name" => "layer_three_strength",
				"description" => "See the description on \"Layer One Strength\" for guidelines on this property."
			),

			array(
				"type" => "fws_image",
				"class" => "",
				"heading" => "Scene Layer Four",
				"param_name" => "layer_four_image",
				"value" => "",
				"description" => ""
			),

			array(
				"type" => "textfield",
				"class" => "",
				"heading" => "Layer Four Strength",
				"value" => "",
				"param_name" => "layer_four_strength",
				"description" => "See the description on \"Layer One Strength\" for guidelines on this property."
			),

			array(
				"type" => "fws_image",
				"class" => "",
				"heading" => "Scene Layer Five",
				"param_name" => "layer_five_image",
				"value" => "",
				"description" => ""
			),

			array(
				"type" => "textfield",
				"class" => "",
				"heading" => "Layer Five Strength",
				"value" => "",
				"param_name" => "layer_five_strength",
				"description" => "See the description on \"Layer One Strength\" for guidelines on this property."
			),

			array(
				"type" => "checkbox",
				"class" => "",
				"heading" => "Video Background",
				"value" => array("Enable Video Background?" => "use_video" ),
				"param_name" => "video_bg",
				"description" => ""
			),

			array(
				"type" => "checkbox",
				"class" => "",
				"heading" => "Video Color Overlay",
				"value" => array("Enable a color overlay ontop of your video?" => "true" ),
				"param_name" => "enable_video_color_overlay",
				"description" => "",
				"dependency" => Array('element' => "video_bg", 'value' => array('use_video'))
			),

			array(
				"type" => "colorpicker",
				"class" => "",
				"heading" => "Overlay Color",
				"param_name" => "video_overlay_color",
				"value" => "",
				"description" => "",
				"dependency" => Array('element' => "enable_video_color_overlay", 'value' => array('true'))
			),

			array(
				"type" => "checkbox",
				"class" => "",
				"heading" => "Mute Video",
				"value" => array("Do you want to mute the video (recommended)" => "true" ),
				"param_name" => "video_mute",
				"description" => "",
				"dependency" => Array('element' => "video_bg", 'value' => array('use_video'))
			),

			array(
				"type" => "textfield",
				"class" => "",
				"heading" => "Youtube Video URL",
				"value" => "",
				"param_name" => "video_external",
				"description" => "Can be used as an alternative to self hosted videos. Enter full video URL e.g. https://www.youtube.com/watch?v=6oTurM7gESE",
				"dependency" => Array('element' => "video_bg", 'value' => array('use_video'))
			),

			array(
				"type" => "textfield",
				"class" => "",
				"heading" => "WebM File URL",
				"value" => "",
				"param_name" => "video_webm",
				"description" => "You must include this format & the mp4 format to render your video with cross browser compatibility. OGV is optional.
			Video must be in a 16:9 aspect ratio.",
				"dependency" => Array('element' => "video_bg", 'value' => array('use_video'))
			),

			array(
				"type" => "textfield",
				"class" => "",
				"heading" => "MP4 File URL",
				"value" => "",
				"param_name" => "video_mp4",
				"description" => "Enter the URL for your mp4 video file here",
				"dependency" => Array('element' => "video_bg", 'value' => array('use_video'))
			),

			array(
				"type" => "textfield",
				"class" => "",
				"heading" => "OGV File URL",
				"value" => "",
				"param_name" => "video_ogv",
				"description" => "Enter the URL for your ogv video file here",
				"dependency" => Array('element' => "video_bg", 'value' => array('use_video'))
			),

			array(
				"type" => "attach_image",
				"class" => "",
				"heading" => "Video Preview Image",
				"value" => "",
				"param_name" => "video_image",
				"description" => "",
				"dependency" => Array('element' => "video_bg", 'value' => array('use_video'))
			),

			array(
				"type" => "dropdown",
				"class" => "",
				"heading" => "Text Color",
				"param_name" => "text_color",
				"value" => array(
					"Dark" => "dark",
					"Light" => "light",
					"Custom" => "custom"
				),
				'save_always' => true
			),

			array(
				"type" => "colorpicker",
				"class" => "",
				"heading" => "Custom Text Color",
				"param_name" => "custom_text_color",
				"value" => "",
				"description" => "",
				"dependency" => Array('element' => "text_color", 'value' => array('custom'))
			),

			array(
				"type" => "dropdown",
				"class" => "",
				'save_always' => true,
				"heading" => "Text Alignment",
				"param_name" => "text_align",
				"value" => array(
					"Left" => "left",
					"Center" => "center",
					"Right" => "right"
				)
			),

			array(
				"type" => "textfield",
				"class" => "",
				"heading" => "Padding Top",
				"value" => "",
				"param_name" => "top_padding",
				"description" => "Don't include \"px\" in your string. e.g \"40\" - However you can also use a percent value in which case a \"%\" would be needed at the end e.g. \"10%\""
			),

			array(
				"type" => "textfield",
				"class" => "",
				"heading" => "Padding Bottom",
				"value" => "",
				"param_name" => "bottom_padding",
				"description" => "Don't include \"px\" in your string. e.g \"40\" - However you can also use a percent value in which case a \"%\" would be needed at the end e.g. \"10%\""
			),

			array(
				"type" => "textfield",
				"class" => "",
				"heading" => "Extra Class Name",
				"param_name" => "class",
				"value" => ""
			),

			array(
				"type" => "textfield",
				"class" => "",
				"heading" => "Row ID",
				"param_name" => "id",
				"value" => "",
				"description" => "Use this to option to add an ID onto your row. This can then be used to target the row with CSS or as an anchor point to scroll to when the relevant link is clicked."
			),
			array(
				"type" => "textfield",
				"class" => "",
				"heading" => "Row Name",
				"param_name" => "row_name",
				"value" => "",
				"description" => "This will be shown in your dot navigation when using the Fullscreen Row option"
			),
			array(
				'type' => 'checkbox',
				'heading' => __( 'Disable Ken Burns BG effect', 'js_composer' ),
				'param_name' => 'disable_ken_burns', // Inner param name.
				'description' => __( 'If checked the ken burns background zoom effect will not occur on this row.', 'js_composer' ),
				'value' => array( __( 'Yes', 'js_composer' ) => 'yes' ),
			),
			array(
				'type' => 'checkbox',
				'heading' => __( 'Disable row', 'js_composer' ),
				'param_name' => 'disable_element', // Inner param name.
				'description' => __( 'If checked the row won\'t be visible on the public side of your website. You can switch it back any time.', 'js_composer' ),
				'value' => array( __( 'Yes', 'js_composer' ) => 'yes' ),
			),
			array(
				"type" => "checkbox",
				"class" => "",
				"group" => "Color Overlay",
				"heading" => "Enable Gradient?",
				"value" => array("Yes, please" => "true" ),
				"param_name" => "enable_gradient",
				"description" => ""
			),
			array(
				"type" => "colorpicker",
				"class" => "",
				"heading" => "Color Overlay",
				"param_name" => "color_overlay",
				"value" => "",
				"group" => "Color Overlay",
				"description" => ""
			),
			array(
				"type" => "colorpicker",
				"class" => "",
				"heading" => "Color Overlay 2",
				"param_name" => "color_overlay_2",
				"value" => "",
				"group" => "Color Overlay",
				"description" => "",
				"dependency" => Array('element' => "enable_gradient", 'not_empty' => true)
			),
			array(
				"type" => "dropdown",
				"class" => "",
				'save_always' => true,
				"heading" => "Gradient Direction",
				"param_name" => "gradient_direction",
				"group" => "Color Overlay",
				"value" => array(
					"Left to Right" => "left_to_right",
					"Left Top to Right Bottom" => "left_t_to_right_b",
					"Left Bottom to Right Top" => "left_b_to_right_t",
					"Bottom to Top" => 'top_to_bottom'
				),
				"dependency" => Array('element' => "enable_gradient", 'not_empty' => true)
			),
			array(
				"type" => "dropdown",
				"class" => "",
				'save_always' => true,
				"group" => "Color Overlay",
				"heading" => "Overlay Strength",
				"param_name" => "overlay_strength",
				"value" => array(
					"Light" => "0.3",
					"Medium" => "0.5",
					"Heavy" => "0.8",
					"Very Heavy" => "0.95",
					"Solid" => '1'
				)
			)
		
		),
		'js_view' => 'VcRowView'
	));




	if(!empty($nectar_options['header-inherit-row-color']) && $nectar_options['header-inherit-row-color'] == '1') {
		vc_add_param("vc_row", array(
			"type" => "checkbox",
			"class" => "",
			"heading" => "Exclude Row From Header Color Inheritance",
			"value" => array("Exclude this row from passing its background/text colors to the header" => "true" ),
			"param_name" => "exclude_row_header_color_inherit",
			"description" => ""
		));
	}


	vc_add_param("vc_column_text", array(
      "type" => "textfield",
      "heading" => __("Max Width", "js_composer"),
      "param_name" => "max_width",
      "admin_label" => false,
      "description" => __("Optionally enter your desired max width in pixels with the \"px\", e.g. 200", "js_composer")
    ));


	global $vc_column_width_list;
	$vc_column_width_list = array(
		__( '1 column - 1/12', 'js_composer' ) => '1/12',
		__( '2 columns - 1/6', 'js_composer' ) => '1/6',
		__( '3 columns - 1/4', 'js_composer' ) => '1/4',
		__( '4 columns - 1/3', 'js_composer' ) => '1/3',
		__( '5 columns - 5/12', 'js_composer' ) => '5/12',
		__( '6 columns - 1/2', 'js_composer' ) => '1/2',
		__( '7 columns - 7/12', 'js_composer' ) => '7/12',
		__( '8 columns - 2/3', 'js_composer' ) => '2/3',
		__( '9 columns - 3/4', 'js_composer' ) => '3/4',
		__( '10 columns - 5/6', 'js_composer' ) => '5/6',
		__( '11 columns - 11/12', 'js_composer' ) => '11/12',
		__( '12 columns - 1/1', 'js_composer' ) => '1/1'
	);

	vc_map( array(
		'name' => __( 'Column', 'js_composer' ),
		'base' => 'vc_column',
		'is_container' => true,
		'content_element' => false,
		'params' => array(
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
				"heading" => "Animation",
				"param_name" => "animation",
				'save_always' => true,
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
				"description" => __("Enter delay (in milliseconds) if needed e.g. 150. This parameter comes in handy when creating the animate in \"one by one\" effect.", "js_composer"),
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
				"type" => "checkbox",
				"class" => "",
				"heading" => "Centered Content",
				"value" => array("Centered Content Alignment" => "true" ),
				"param_name" => "centered_text",
				"description" => ""
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
					"15%" => "padding-15-percent"
				),
				"description" => "When using the full width content row type or providing a background color/image for the column, you have the option to define the amount of padding your column will receive."
			),

			array(
				"type" => "dropdown",
				"class" => "",
				"heading" => "Column Padding Position",
				"param_name" => "column_padding_position",
				'save_always' => true,
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
					'Bottom & Left' => 'bottom-left'
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
				"type" => "colorpicker",
				"class" => "",
				"heading" => "Background Color Hover",
				"param_name" => "background_color_hover",
				"value" => "",
				"description" => "",
			),

			array(
				"type" => "dropdown",
				"class" => "",
				'save_always' => true,
				"heading" => "Background Hover Color Opacity",
				"param_name" => "background_hover_color_opacity",
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
				"dependency" => Array('element' => "background_image", 'not_empty' => true)
			),

			array(
				"type" => "colorpicker",
				"class" => "",
				"heading" => "Font Color",
				"param_name" => "font_color",
				"value" => "",
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
				"heading" => "Margin Top",
				"value" => "",
				"param_name" => "top_margin",
				"description" => "Don't include \"px\" in your string. e.g \"40\" - However you can also use a percent value in which case a \"%\" would be needed at the end e.g. \"10%\". Negative Values are also accepted."
			),

			array(
				"type" => "textfield",
				"class" => "",
				"heading" => "Margin Bottom",
				"value" => "",
				"param_name" => "bottom_margin",
				"description" => "Don't include \"px\" in your string. e.g \"40\" - However you can also use a percent value in which case a \"%\" would be needed at the end e.g. \"10%\". Negative Values are also accepted."
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
				'heading' => __( 'Width', 'js_composer' ),
				'param_name' => 'width',
				'value' => $vc_column_width_list,
				'group' => __( 'Responsive Options', 'js_composer' ),
				'description' => __( 'Select column width.', 'js_composer' ),
				'std' => '1/1'
			),
			array(
				'type' => 'column_offset',
				'heading' => __( 'Responsiveness', 'js_composer' ),
				'param_name' => 'offset',
				'group' => __( 'Responsive Options', 'js_composer' ),
				'description' => __( 'Adjust column for different screen sizes. Control width, offset and visibility settings.', 'js_composer' )
			),

			array(
				"type" => "dropdown",
				"class" => "",
				'group' => __( 'Responsive Options', 'js_composer' ),
				'save_always' => true,
				"heading" => "Tablet Text Alignment",
				"param_name" => "tablet_text_alignment",
				"value" => array(
					"Default" => "default",
					"Left" => "left",
					"Center" => "center",
					"Right" => "right",
				),
				"description" => "Text alignment that will be used on tablet devices"
			),

			array(
				"type" => "dropdown",
				"class" => "",
				'group' => __( 'Responsive Options', 'js_composer' ),
				'save_always' => true,
				"heading" => "Smartphone Text Alignment",
				"param_name" => "phone_text_alignment",
				"value" => array(
					"Default" => "default",
					"Left" => "left",
					"Center" => "center",
					"Right" => "right",
				),
				"description" => "Text alignment that will be used on smartphones"
			)

		),
		'js_view' => 'VcColumnView'
	) );

	vc_map( array(
		"name" => __( "Column", "js_composer" ),
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
				"description" => __("Enter delay (in milliseconds) if needed e.g. 150. This parameter comes in handy when creating the animate in \"one by one\" effect.", "js_composer"),
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
					"15%" => "padding-15-percent"
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
				'heading' => __( 'Width', 'js_composer' ),
				'param_name' => 'width',
				'value' => $vc_column_width_list,
				'group' => __( 'Responsive Options', 'js_composer' ),
				'description' => __( 'Select column width.', 'js_composer' ),
				'std' => '1/1'
			),
			array(
				'type' => 'column_offset',
				'heading' => __( 'Responsiveness', 'js_composer' ),
				'param_name' => 'offset',
				'group' => __( 'Responsive Options', 'js_composer' ),
				'description' => __( 'Adjust column for different screen sizes. Control width, offset and visibility settings.', 'js_composer' )
			)
		),
		"js_view" => 'VcColumnView'
	) );






	//inner row class fix
	vc_remove_param("vc_row_inner", "el_class");

	//columns gap
	vc_remove_param("vc_row_inner", "gap");

	vc_add_param("vc_row_inner", array(
		"type" => "textfield",
		"class" => "",
		"heading" => "Extra Class Name",
		"param_name" => "class",
		"value" => ""
	));

	vc_add_param("vc_row_inner", array(
				"type" => "textfield",
				"class" => "",
				"heading" => "Padding Top",
				"value" => "",
				"param_name" => "top_padding",
				"description" => "Don't include \"px\" in your string. e.g \"40\" - However you can also use a percent value in which case a \"%\" would be needed at the end e.g. \"10%\""
	));

	vc_add_param("vc_row_inner", array(
				"type" => "textfield",
				"class" => "",
				"heading" => "Padding Bottom",
				"value" => "",
				"param_name" => "bottom_padding",
				"description" => "Don't include \"px\" in your string. e.g \"40\" - However you can also use a percent value in which case a \"%\" would be needed at the end e.g. \"10%\""
	));

	vc_add_param("vc_row_inner", array(
		"type" => "dropdown",
		"class" => "",
		'save_always' => true,
		"heading" => "Text Alignment",
		"param_name" => "text_align",
		"value" => array(
			"Left" => "left",
			"Center" => "center",
			"Right" => "right"
		)
	));


	if(nectar_has_shortcode('full_width_section')) {  

	require_once vc_path_dir('SHORTCODES_DIR', 'vc-row.php');

	class WPBakeryShortCode_Full_Width_Section extends WPBakeryShortCode_VC_Row {
			

	}
		vc_map( array(
				"name" => "Full Width Section",
				"base" => "full_width_section",
				"class" => "wpb_vc_row",
				"is_container" => true,
		 		"icon" => "icon-wpb-row",
		 		"show_settings_on_create" => false,
				"category" => __('Nectar Elements', 'js_composer'),
				'js_view' => 'VcRowView',
				"content_element" => false,
			    'default_content' => '[vc_column width="1/1"]%content%[/vc_column]',
			    'params' => array( 
				    	array(
						"type" => "dropdown",
						"class" => "",
						"heading" => "Type",
						"param_name" => "type",
						"value" => array(
							"Full Width Background" => "full_width_background",
							"Full Width Content" => "full_width_content",	
							"In Container" => "in_container"
						)
					)
			    )
		));





		vc_add_param("full_width_section", array(
			"type" => "checkbox",
			"class" => "",
			"heading" => "Vetical Align Columns",
			"value" => array("Make all columns in this row vertically aligned?" => "true" ),
			"param_name" => "vertically_center_columns",
			"description" => "",
			"dependency" => Array('element' => "type", 'value' => array('full_width_content'))
		));


		vc_add_param("full_width_section", array(
			"type" => "fws_image",
			"class" => "",
			"heading" => "Background Image",
			"param_name" => "image_url",
			"value" => "",
			"description" => ""
		));

		vc_add_param("full_width_section", array(
			"type" => "dropdown",
			"class" => "",
			"heading" => "Background Position",
			"param_name" => "bg_pos",
			"value" => array(
				 "Left Top" => "Left Top",
		  		 "Left Center" => "Left Center",
		  		 "Left Bottom" => "Left Bottom",
		  		 "Center Top" => "Center Top",
		  		 "Center Center" => "Center Center",
		  		 "Center Bottom" => "Center Bottom",
		  		 "Right Top" => "Right Top",
		  		 "Right Center" => "Right Center",
		  		 "Right Bottom" => "Right Bottom"
			),
			"dependency" => Array('element' => "image_url", 'not_empty' => true)
		));

		vc_add_param("full_width_section", array(
			"type" => "dropdown",
			"class" => "",
			"heading" => "Background Repeat",
			"param_name" => "bg_repeat",
			"value" => array(
				"No Repeat" => "No-Repeat",
				"Repeat" => "Repeat"
			),
			"dependency" => Array('element' => "image_url", 'not_empty' => true)
		));

		vc_add_param("full_width_section", array(
			"type" => "checkbox",
			"class" => "",
			"heading" => "Parallax Background",
			"value" => array("Enable Parallax Background?" => "true" ),
			"param_name" => "parallax_bg",
			"description" => "",
			"dependency" => Array('element' => "image_url", 'not_empty' => true)
		));

		vc_add_param("full_width_section", array(
			"type" => "colorpicker",
			"class" => "",
			"heading" => "Background Color",
			"param_name" => "background_color",
			"value" => "",
			"description" => ""
		));


		if(!empty($nectar_options['header-inherit-row-color']) && $nectar_options['header-inherit-row-color'] == '1') {
			vc_add_param("full_width_section", array(
				"type" => "checkbox",
				"class" => "",
				"heading" => "Exclude Row From Header Color Inheritance",
				"value" => array("Exclude this row from passing its background/text colors to the header" => "true" ),
				"param_name" => "exclude_row_header_color_inherit",
				"description" => ""
			));
		}

		vc_add_param("full_width_section", array(
			"type" => "checkbox",
			"class" => "",
			"heading" => "Video Background",
			"value" => array("Enable Video Background?" => "use_video" ),
			"param_name" => "video_bg",
			"description" => ""
		));

		vc_add_param("full_width_section", array(
			"type" => "checkbox",
			"class" => "",
			"heading" => "Video Color Overlay",
			"value" => array("Enable a color overlay ontop of your video?" => "true" ),
			"param_name" => "enable_video_color_overlay",
			"description" => "",
			"dependency" => Array('element' => "video_bg", 'value' => array('use_video'))
		));

		vc_add_param("full_width_section", array(
			"type" => "colorpicker",
			"class" => "",
			"heading" => "Overlay Color",
			"param_name" => "video_overlay_color",
			"value" => "",
			"description" => "",
			"dependency" => Array('element' => "enable_video_color_overlay", 'value' => array('true'))
		));

		vc_add_param("full_width_section", array(
			"type" => "textfield",
			"class" => "",
			"heading" => "WebM File URL",
			"value" => "",
			"param_name" => "video_webm",
			"description" => "You must include this format & the mp4 format to render your video with cross browser compatibility. OGV is optional.
		Video must be in a 16:9 aspect ratio.",
			"dependency" => Array('element' => "video_bg", 'value' => array('use_video'))
		));

		vc_add_param("full_width_section", array(
			"type" => "textfield",
			"class" => "",
			"heading" => "MP4 File URL",
			"value" => "",
			"param_name" => "video_mp4",
			"description" => "Enter the URL for your mp4 video file here",
			"dependency" => Array('element' => "video_bg", 'value' => array('use_video'))
		));

		vc_add_param("full_width_section", array(
			"type" => "textfield",
			"class" => "",
			"heading" => "OGV File URL",
			"value" => "",
			"param_name" => "video_ogv",
			"description" => "Enter the URL for your ogv video file here",
			"dependency" => Array('element' => "video_bg", 'value' => array('use_video'))
		));

		vc_add_param("full_width_section", array(
			"type" => "attach_image",
			"class" => "",
			"heading" => "Video Preview Image",
			"value" => "",
			"param_name" => "video_image",
			"description" => "",
			"dependency" => Array('element' => "video_bg", 'value' => array('use_video'))
		));

		vc_add_param("full_width_section", array(
			"type" => "dropdown",
			"class" => "",
			'save_always' => true,
			"heading" => "Text Color",
			"param_name" => "text_color",
			"value" => array(
				"Light" => "light",
				"Dark" => "dark",
				"Custom" => "custom"
			)
		));

		vc_add_param("full_width_section", array(
			"type" => "colorpicker",
			"class" => "",
			"heading" => "Custom Text Color",
			"param_name" => "custom_text_color",
			"value" => "",
			"description" => "",
			"dependency" => Array('element' => "text_color", 'value' => array('custom'))
		));

		vc_add_param("full_width_section", array(
			"type" => "dropdown",
			"class" => "",
			"heading" => "Text Alignment",
			"param_name" => "text_align",
			"value" => array(
				"Left" => "left",
				"Center" => "center",
				"Right" => "right"
			)
		));

		vc_add_param("full_width_section", array(
			"type" => "textfield",
			"class" => "",
			"heading" => "Padding Top",
			"value" => "",
			"param_name" => "top_padding",
			"description" => ""
		));

		vc_add_param("full_width_section", array(
			"type" => "textfield",
			"class" => "",
			"heading" => "Padding Bottom",
			"value" => "",
			"param_name" => "bottom_padding",
			"description" => ""
		));

		vc_add_param("full_width_section", array(
			"type" => "textfield",
			"class" => "",
			"heading" => "Extra Class Name",
			"param_name" => "class",
			"value" => ""
		));

	}



	// Video
	vc_remove_param("vc_video", "title");


	// Text block
	vc_remove_param("vc_column_text", "css_animation");


	$nectar_disable_nectar_slider = (!empty($nectar_options['disable_nectar_slider_pt']) && $nectar_options['disable_nectar_slider_pt'] == '1') ? true : false; 
	if($nectar_disable_nectar_slider != true) {

		if(nectar_has_shortcode('nectar_slider')) { 

			// Nectar Slider
			$slider_locations = ($is_admin) ? get_terms('slider-locations') : array('All' => 'all');
			$locations = array();

			if($is_admin) {
				foreach ($slider_locations as $location) {
					$locations[$location->name] = $location->name;
				}
			} else {
				$locations['All'] = 'all';
			}

			if (empty($locations)) {
				$location_desc = 
			      '<div class="alert">' .
				 __('You currently don\'t have any Slider Locations setup. Please create some and add assign slides to them before using this!','salient'). 
				'<br/><br/>
				<a href="' . admin_url('edit.php?post_type=nectar_slider') . '">'. __('Link to Nectar Slider', 'salient') . '</a>
				</div>';
			} else { $location_desc = ''; }

			vc_map( array(
			  "name" => __("Nectar Slider", "js_composer"),
			  "base" => "nectar_slider",
			  "icon" => "icon-wpb-nectar-slider",
			  "category" => __('Nectar Elements', 'js_composer'),
			  "description" => __('The jaw-dropping slider by ThemeNectar', 'js_composer'),
			  "weight" => 10,
			  "params" => array(
			    array(
			      "type" => "dropdown",
			      "heading" => __("Select Slider", "js_composer"),
			      "admin_label" => true,
			      "param_name" => "location",
			      "value" => $locations,
			      "description" => $location_desc,
			      'save_always' => true
			    ),
				array(
			      "type" => "textfield",
			      "heading" => __("Slider Height", "js_composer"),
			      "param_name" => "slider_height",
			      "admin_label" => true,
			      "description" => __("Don't include \"px\" in your string. e.g. 650", "js_composer")
			    ),
			    array(
			      "type" => 'checkbox',
			      "heading" => __("Flexible Slider Height", "js_composer"),
			      "param_name" => "flexible_slider_height",
			      "description" => __("Would you like the height of your slider to constantly scale in porportion to the screen size?", "js_composer"),
			      "value" => Array(__("Yes, please", "js_composer") => 'true')
			    ),
			    array(
			      "type" => "textfield",
			      "heading" => __("Minimum Slider Height", "js_composer"),
			      "param_name" => "min_slider_height",
			      "dependency" => Array('element' => "flexible_slider_height", 'not_empty' => true),
			      "description" => __("When using the flexible height option the slider can become very short on mobile devices - use this to ensure it stays tall enough for your content Don't include \"px\" in your string. e.g. 250", "js_composer")
			    ),
			    array(
			      "type" => 'checkbox',
			      "heading" => __("Display Full Width?", "js_composer"),
			      "param_name" => "full_width",
			      "description" => __("Would you like this slider to display the full width of the page?", "js_composer"),
			      "value" => Array(__("Yes, please", "js_composer") => 'true')
			    ),
			    array(
			      "type" => 'checkbox',
			      "heading" => __("Fullscreen Slider?", "js_composer"),
			      "param_name" => "fullscreen",
			      "description" => __("This will cause your slider to resize to always fill the users screen size", "js_composer"),
			      "value" => Array(__("Yes, please", "js_composer") => 'true'),
			      "dependency" => Array('element' => "full_width", 'not_empty' => true)
			    ),
			    array(
			      "type" => 'checkbox',
			      "heading" => __("Display Arrow Navigation?", "js_composer"),
			      "param_name" => "arrow_navigation",
			      "description" => __("Would you like this slider to display arrows on the right and left sides?", "js_composer"),
			      "value" => Array(__("Yes, please", "js_composer") => 'true'),
			      "dependency" => Array('element' => "overall_style", 'value' => 'classic')
			    ),
			     array(
			      "type" => "dropdown",
			      "heading" => __("Slider Next/Prev Button Styling", "js_composer"),
			      "param_name" => "slider_button_styling",
			      "dependency" => Array('element' => "arrow_navigation", 'not_empty' => true),
			      "value" => array(
						'Standard With Slide Count On Hover' => 'btn_with_count',
						'Next/Prev Slide Preview On Hover' => 'btn_with_preview'
			      ),
			      "description" => 'Please select your slider button styling here',
			    ),
			     array(
			      "type" => "dropdown",
			      "heading" => __("Overall Style", "js_composer"),
			      "param_name" => "overall_style",
			      "value" => array(
						'Classic' => 'classic',
						'Directional Based Content Movement' => 'directional'
			      ),
			      'save_always' => true,
			      "description" => 'Please select your overall style here - note that some styles will remove the possibility to control certain options.'
			    ),
			    array(
			      "type" => 'checkbox',
			      "heading" => __("Display Bullet Navigation?", "js_composer"),
			      "param_name" => "bullet_navigation",
			      "description" => __("Would you like this slider to display bullets on the bottom?", "js_composer"),
			      "value" => Array(__("Yes, please", "js_composer") => 'true'),
			      "dependency" => Array('element' => "overall_style", 'value' => 'classic')
			    ),
			     array(
			      "type" => "dropdown",
			      "heading" => __("Bullet Navigation Style", "js_composer"),
			      "param_name" => "bullet_navigation_style",
			      "value" => array(
						'See Through & Solid On Active' => 'see_through',
						'Solid & Scale On Active' => 'scale'
			      ),
			      "description" => 'Please select your overall bullet navigation style here.',
			      "dependency" => Array('element' => "bullet_navigation", 'not_empty' => true)
			    ),
			    array(
			      "type" => 'checkbox',
			      "heading" => __("Enable Swipe on Desktop?", "js_composer"),
			      "param_name" => "desktop_swipe",
			      "description" => __("Would you like this slider to have swipe interaction on desktop?", "js_composer"),
			      "value" => Array(__("Yes, please", "js_composer") => 'true'),
			      "dependency" => Array('element' => "overall_style", 'value' => 'classic')
			    ),
			    array(
			      "type" => 'checkbox',
			      "heading" => __("Parallax Slider?", "js_composer"),
			      "param_name" => "parallax",
			      "description" => __("will only activate if the slider is the <b>top level element</b> in the page", "js_composer"),
			      "value" => Array(__("Yes, please", "js_composer") => 'true')
			    ),
			    array(
			      "type" => 'checkbox',
			      "heading" => __("Loop Slider?", "js_composer"),
			      "param_name" => "loop",
			      "description" => __("Would you like your slider to loop infinitely?", "js_composer"),
			      "value" => Array(__("Yes, please", "js_composer") => 'true'),
			      "dependency" => Array('element' => "overall_style", 'value' => 'classic')
			    ),
			    array(
			      "type" => "dropdown",
			      "heading" => __("Slider Transition", "js_composer"),
			      "param_name" => "slider_transition",
			      "value" => array(
						'Slide' => 'slide',
						'Fade' => 'fade'
			      ),
			      "description" => 'Please select your slider transition here',
			      "dependency" => Array('element' => "overall_style", 'value' => 'classic'),
			      'save_always' => true
			    ),
			    array(
			      "type" => "textfield",
			      "heading" => __("Autorotate?", "js_composer"),
			      "param_name" => "autorotate",
			      "description" => __("If you would like this slider to autorotate, enter the rotation speed in miliseconds here. i.e 5000", "js_composer")
			    ),
			    array(
					"type" => "dropdown",
					"class" => "",
					"heading" => "Button Sizing",
					"param_name" => "button_sizing",
					"value" => array(
						"Regular" => "regular",
						"Large" => "large",
						"Jumbo" => "jumbo"
					),
					'save_always' => true,
					"description" => ""
				),
				array(
			  	  "type" => "textfield",
			      "heading" => __("Tablet Header Font Size", "js_composer"),
			      "param_name" => "tablet_header_font_size",
			      "admin_label" => false,
			      "description" => __("Don't include \"px\" in your string. e.g. 32", "js_composer"),
			  	  "group" => "Mobile Text Sizing Override"
			  	),
			  	array(
			  	  "type" => "textfield",
			      "heading" => __("Tablet Caption Font Size", "js_composer"),
			      "param_name" => "tablet_caption_font_size",
			      "admin_label" => false,
			      "description" => __("Don't include \"px\" in your string. e.g. 20", "js_composer"),
			  	  "group" => "Mobile Text Sizing Override"
			  	),
			  	array(
			  	  "type" => "textfield",
			      "heading" => __("Phone Header Font Size", "js_composer"),
			      "param_name" => "phone_header_font_size",
			      "admin_label" => false,
			      "description" => __("Don't include \"px\" in your string. e.g. 24", "js_composer"),
			  	  "group" => "Mobile Text Sizing Override"
			  	),
			  	array(
			  	  "type" => "textfield",
			      "heading" => __("Phone Caption Font Size", "js_composer"),
			      "param_name" => "phone_caption_font_size",
			      "admin_label" => false,
			      "description" => __("Don't include \"px\" in your string. e.g. 14", "js_composer"),
			  	  "group" => "Mobile Text Sizing Override"
			  	)
			  )
			));
		}
	}
	

	if(nectar_has_shortcode('bar')) { 
		// Horizontal progress bar shortcode
		vc_map( array(
				"name" => "Progress Bar",
				"base" => "bar",
				"icon" => "icon-wpb-progress_bar",
				"allowed_container_element" => 'vc_row',
				"category" => __('Nectar Elements', 'js_composer'),
				"description" => __('Include a horizontal progress bar', 'js_composer'),
				"params" => array(
					array(
						"type" => "textfield",
						"holder" => "div",
						"class" => "",
						"heading" => "Title",
						"param_name" => "title",
						"description" => ""
					),
					array(
						"type" => "textfield",
						"holder" => "div",
						"class" => "",
						"heading" => "Percentage",
						"param_name" => "percent",
						"description" => "Don't include \"%\" in your string - e.g \"70\""
					),
					array(
						"type" => "dropdown",
						"holder" => "div",
						"class" => "",
						'save_always' => true,
						"heading" => "Bar Color",
						"param_name" => "color",
						"value" => array(
							"Accent-Color" => "Accent-Color",
							"Extra-Color-1" => "Extra-Color-1",
							"Extra-Color-2" => "Extra-Color-2",	
							"Extra-Color-3" => "Extra-Color-3",
							"Extra-Color-Gradient-1" => "extra-color-gradient-1",
					 		"Extra-Color-Gradient-2" => "extra-color-gradient-2"
						),
						"description" => ""
					)

				)
		) );
	}




	// Split Line Heading
	class WPBakeryShortCode_Split_Line_Heading extends WPBakeryShortCode { }
	vc_map( array(
			"name" => "Split Line Heading",
			"base" => "split_line_heading",
			"icon" => "icon-wpb-split-line-heading",
			"allowed_container_element" => 'vc_row',
			"category" => __('Nectar Elements', 'js_composer'),
			"description" => __('Animated multi line heading', 'js_composer'),
			"params" => array(
				array(
			      "type" => "textarea_html",
			      "holder" => "div",
			      "heading" => __("Text Content", "js_composer"),
			      "param_name" => "content",
			      "value" => '',
			      "description" => __("Each Line of this editor will be animated separately. Separate text with the Enter or Return key on your Keyboard.", "js_composer"),
			      "admin_label" => false
			    ),

			)
	) );



	// Divider
	vc_map( array(
			"name" => "Divider",
			"base" => "divider",
			"icon" => "icon-wpb-separator",
			"allowed_container_element" => 'vc_row',
			"category" => __('Nectar Elements', 'js_composer'),
			"description" => __('Create space between your content', 'js_composer'),
			"params" => array(
				array(
					"type" => "textfield",
					"holder" => "div",
					"class" => "",
					"heading" => "Dividing Height",
					"param_name" => "custom_height",
					"description" => "If you would like to control the specifc number of pixels your divider is, enter it here. Don't enter \"px\", just the numnber e.g. \"20\""
				),
				array(
					"type" => "dropdown",
					"holder" => "div",
					"class" => "",
					"heading" => "Line Type",
					'save_always' => true,
					"param_name" => "line_type",
					"value" => array(
						"No Line" => "No Line",
						"Full Width Line" => "Full Width Line",
						"Small Line" => "Small Line"
					)
				),
				array(
				  "type" => "dropdown",
				  "heading" => __("Line Thickness", "js_composer"),
				  "admin_label" => false,
				  "param_name" => "line_thickness",
				  "value" => array(
					    "1px" => "1",
					    "2px" => "2",
					    "3px" => "3",
					    "4px" => "4",
					    "5px" => "5",
					    "6px" => "6",
					    "7px" => "7",
					    "8px" => "8",
					    "9px" => "9",
					    "10px" => "10"
					),
				  "description" => __("Please select thickness of your line ", "js_composer"),
				  'save_always' => true,
				  "dependency" => Array('element' => "line_type", 'value' => array('Full Width Line','Small Line'))
				),
				array(
					"type" => "textfield",
					"holder" => "div",
					"admin_label" => false,
					"class" => "",
					"heading" => "Custom Line Width",
					"param_name" => "custom_line_width",
					"dependency" => Array('element' => "line_type", 'value' => array('Small Line')),
					"description" => "If you would like to control the specifc number of pixels that your divider is (widthwise), enter it here. Don't enter \"px\", just the numnber e.g. \"20\""
				),
				 array(
				  "type" => "dropdown",
				  "heading" => __("Divider Color", "js_composer"),
				  "param_name" => "divider_color",
				  "admin_label" => false,
				  "value" => array(
				     "Default (inherit from row Text Color)" => "default",
					 "Accent-Color" => "accent-color",
					 "Extra-Color-1" => "extra-color-1",
					 "Extra-Color-2" => "extra-color-2",	
					 "Extra-Color-3" => "extra-color-3",
					 "Extra-Color-Gradient-1" => "extra-color-gradient-1",
					 "Extra-Color-Gradient-2" => "extra-color-gradient-2"
				   ),
				  'save_always' => true,
				  "dependency" => Array('element' => "line_type", 'value' => array('Full Width Line','Small Line')),
				  "description" => __("Please select the color for your divider line", "js_composer")
				),
				 array(
			      "type" => 'checkbox',
			      "heading" => __("Animate Line", "js_composer"),
			      "param_name" => "animate",
			      "description" => __("If selected, the divider line will animate in when scrolled to", "js_composer"),
			      "value" => Array(__("Yes, please", "js_composer") => 'yes'),
			      "dependency" => Array('element' => "line_type", 'value' => array('Full Width Line','Small Line')),
			    ),
				 array(
			      "type" => "textfield",
			      "heading" => __("Animation Delay", "js_composer"),
			      "param_name" => "delay",
			      "dependency" => Array('element' => "line_type", 'value' => array('Full Width Line','Small Line')),
			      "description" => __("Enter delay (in milliseconds) if needed e.g. 150. This parameter comes in handy when creating the animate in \"one by one\" effect.", "js_composer")
			    ),

			)
	));




	// Single image
	vc_map( array(
	  "name" => __("Single Image", "js_composer"),
	  "base" => "image_with_animation",
	  "icon" => "icon-wpb-single-image",
	  "category" => __('Nectar Elements', 'js_composer'),
	  "description" => __('Simple image with CSS animation', 'js_composer'),
	  "params" => array(
	    array(
	      "type" => "fws_image",
	      "heading" => __("Image", "js_composer"),
	      "param_name" => "image_url",
	      "value" => "",
	      "description" => __("Select image from media library.", "js_composer")
	    ),
	    array(
	      "type" => "dropdown",
	      "heading" => __("Image Alignment", "js_composer"),
	      'save_always' => true,
	      "param_name" => "alignment",
	      "value" => array(__("Align left", "js_composer") => "", __("Align right", "js_composer") => "right", __("Align center", "js_composer") => "center"),
	      "description" => __("Select image alignment.", "js_composer")
	    ),
	    array(
		  "type" => "dropdown",
		  "heading" => __("CSS Animation", "js_composer"),
		  "param_name" => "animation",
		  "admin_label" => true,
		  "value" => array(
			    __("Fade In", "js_composer") => "Fade In", 
			    __("Fade In From Left", "js_composer") => "Fade In From Left", 
			    __("Fade In From Right", "js_composer") => "Fade In From Right", 
			    __("Fade In From Bottom", "js_composer") => "Fade In From Bottom", 
			    __("Grow In", "js_composer") => "Grow In",
			    __("Flip In Horizontal", "js_composer") => "Flip In",
			    __("Flip In Vertical", "js_composer") => "flip-in-vertical",
			    __("None", "js_composer") => "None"
			),
		  'save_always' => true,
		  "description" => __("Select animation type if you want this element to be animated when it enters into the browsers viewport. Note: Works only in modern browsers.", "js_composer")
		),
		array(
	      "type" => "textfield",
	      "heading" => __("Animation Delay", "js_composer"),
	      "param_name" => "delay",
	      "description" => __("Enter delay (in milliseconds) if needed e.g. 150. This parameter comes in handy when creating the animate in \"one by one\" effect in horizontal columns.", "js_composer")
	    ),
	    array(
	      "type" => 'checkbox',
	      "heading" => __("Link to large image?", "js_composer"),
	      "param_name" => "img_link_large",
	      "description" => __("If selected, image will be linked to the bigger image.", "js_composer"),
	      "value" => Array(__("Yes, please", "js_composer") => 'yes')
	    ),
	    array(
	      "type" => "textfield",
	      "heading" => __("Image link", "js_composer"),
	      "param_name" => "img_link",
	      "description" => __("Enter url if you want this image to have link.", "js_composer"),
	      "dependency" => Array('element' => "img_link_large", 'is_empty' => true)
	    ),
	    array(
	      "type" => "dropdown",
	      "heading" => __("Link Target", "js_composer"),
	      "param_name" => "img_link_target",
	      "value" => array(__("Same window", "js_composer") => "_self", __("New window", "js_composer") => "_blank"),
	      "dependency" => Array('element' => "img_link", 'not_empty' => true)
	    ),
	    array(
	      "type" => "textfield",
	      "heading" => __("Extra class name", "js_composer"),
	      "param_name" => "el_class",
	      "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "js_composer")
	    ),
	    array(
	      "type" => "dropdown",
	      "heading" => __("Box Shadow", "js_composer"),
	      'save_always' => true,
	      "param_name" => "box_shadow",
	      "value" => array(__("None", "js_composer") => "none", __("Small Depth", "js_composer") => "small_depth", __("Medium Depth", "js_composer") => "medium_depth", __("Large Depth", "js_composer") => "large_depth", __("Very Large Depth", "js_composer") => "x_large_depth"),
	      "description" => __("Select your desired image box shadow", "js_composer")
	    ),
	    array(
	      "type" => "dropdown",
	      "heading" => __("Max Width", "js_composer"),
	      'save_always' => true,
	      "param_name" => "max_width",
	      "value" => array(
		      	__("100%", "js_composer") => "100%",
		      	__("125%", "js_composer") => "125%", 
		      	__("150%", "js_composer") => "150%",
		      	__("165%", "js_composer") => "165%",  
		      	__("175%", "js_composer") => "175%", 
		      	__("200%", "js_composer") => "200%", 
		      	__("225%", "js_composer") => "225%", 
		      	__("250%", "js_composer") => "250%"
	      ),
	      "description" => __("Select your desired max width here - by default images are not allowed to display larger than the column they're contained in. Changing this to a higher value will allow you to create designs where your image overflows out of the column partially off screen.", "js_composer")
	    )
	  )
	));


	//cascading images
class WPBakeryShortCode_Nectar_Cascading_Images extends WPBakeryShortCode {}
	
	$nectar_offset_vals_arr = array(
	     "0%" => "0%",
		 "5%" => "5%",
		 "10%" => "10%",
		 "15%" => "15%",	
		 "20%" => "20%",
		 "25%" => "25%",
		 "30%" => "30%",
		 "35%" => "35%",	
		 "40%" => "40%",
		 "45%" => "45%",	
		 "50%" => "50%",
		 "55%" => "55%",
		 "60%" => "60%",
		 "65%" => "65%",	
		 "70%" => "70%",
		 "75%" => "75%",	
		 "80%" => "80%",
		 "85%" => "85%",	
		 "90%" => "90%",
		 "95%" => "95%",	
		 "100%" => "100%"
    );
	vc_map( array(
	  "name" => __("Cascading Images", "js_composer"),
	  "base" => "nectar_cascading_images",
	  "icon" => "icon-wpb-images-stack",
	  "category" => __('Nectar Elements', 'js_composer'),
	  "description" => __('Animated overlapping images', 'js_composer'),
	  "params" => array(
	  	
	    array(
	      "type" => "fws_image",
	      "heading" => __("Image #1", "js_composer"),
	      "param_name" => "image_1_url",
	      "group" => 'Layer #1',
	      "value" => "",
	      "description" => __("Select image from media library.", "js_composer")
	    ),
	    array(
			"type" => "colorpicker",
			"class" => "",
			"group" => 'Layer #1',
			"heading" => "Layer BG Color",
			"param_name" => "image_1_bg_color",
			"value" => "",
			"description" => "Use this to set a BG color for the layer"
		),
	    array(
		  "type" => "dropdown",
		  "group" => 'Layer #1',
		  "heading" => __("Offset X", "js_composer"),
		  "param_name" => "image_1_offset_x_sign",
		  "value" => array(
			 "+" => "+",
			 "-" => "-"
		   ),
		  "edit_field_class" => "col-md-2",
		  'save_always' => true
		),
	    array(
		  "type" => "dropdown",
		  "group" => 'Layer #1',
		  "heading" => __("Offset X", "js_composer"),
		  "param_name" => "image_1_offset_x",
		  "edit_field_class" => "col-md-4",
		  "value" => $nectar_offset_vals_arr,
		  'save_always' => true
		),
		 array(
		  "type" => "dropdown",
		  "group" => 'Layer #1',
		  "heading" => __("Offset Y", "js_composer"),
		  "param_name" => "image_1_offset_y_sign",
		  'edit_field_class' => 'offset-y-sign',
		  "edit_field_class" => "col-md-2",
		  "value" => array(
			 "+" => "+",
			 "-" => "-"
		   ),
		  'save_always' => true
		),
	    array(
		  "type" => "dropdown",
		  "group" => 'Layer #1',
		  "heading" => __("Offset Y", "js_composer"),
		  "param_name" => "image_1_offset_y",
		  "value" => $nectar_offset_vals_arr,
		  'edit_field_class' => 'offset-y',
		  "edit_field_class" => "col-md-4",
		  'save_always' => true
		),
		array(
		  "type" => "dropdown",
		  "group" => 'Layer #1',
		  "heading" => __("Rotate", "js_composer"),
		  "param_name" => "image_1_rotate_sign",
		  "value" => array(
			 "+" => "+",
			 "-" => "-"
		   ),
		  "edit_field_class" => "col-md-2",
		  'save_always' => true
		),
		array(
		  "type" => "dropdown",
		  "group" => 'Layer #1',
		  "heading" => __("Rotate", "js_composer"),
		  "param_name" => "image_1_rotate",
		  "edit_field_class" => "col-md-4",
		  "value" => array(
		     "None" => "none",
			 "2.5°" => "2.5",
			 "5°" => "5",
			 "7.5°" => "7.5",	
			 "10°" => "10",
			 "12.5°" => "12.5",
			 "15°" => "15",
			 "17.5°" => "17.5",	
			 "20°" => "20"
		   ),
		  'save_always' => true
		),
		array(
		  "type" => "dropdown",
		  "heading" => __("CSS Animation", "js_composer"),
		  "group" => 'Layer #1',
		  "param_name" => "image_1_animation",
		  "admin_label" => true,
		  "value" => array(
			    __("Fade In", "js_composer") => "Fade In", 
			    __("Fade In From Left", "js_composer") => "Fade In From Left", 
			    __("Fade In From Right", "js_composer") => "Fade In From Right", 
			    __("Fade In From Bottom", "js_composer") => "Fade In From Bottom", 
			    __("Grow In", "js_composer") => "Grow In",
			    __("Flip In", "js_composer") => "Flip In",
			    __("None", "js_composer") => "None"
			),
		  'save_always' => true,
		  "description" => __("Select animation type if you want this layer to be animated when it enters into the browsers viewport.", "js_composer")
		),
	    array(
	      "type" => "dropdown",
	      "group" => 'Layer #1',
	      "heading" => __("Box Shadow", "js_composer"),
	      'save_always' => true,
	      "param_name" => "image_1_box_shadow",
	      "value" => array(__("None", "js_composer") => "none", __("Small Depth", "js_composer") => "small_depth", __("Medium Depth", "js_composer") => "medium_depth", __("Large Depth", "js_composer") => "large_depth", __("Very Large Depth", "js_composer") => "x_large_depth"),
	      "description" => __("Select your desired image box shadow", "js_composer")
	    ),

	    array(
	      "type" => "fws_image",
	      "group" => 'Layer #2',
	      "heading" => __("Image #2", "js_composer"),
	      "param_name" => "image_2_url",
	      "value" => "",
	      "description" => __("Select image from media library.", "js_composer")
	    ),
	    array(
			"type" => "colorpicker",
			"class" => "",
			"group" => 'Layer #2',
			"heading" => "Layer BG Color",
			"param_name" => "image_2_bg_color",
			"value" => "",
			"description" => "Use this to set a BG color for the layer"
		),
	    array(
		  "type" => "dropdown",
		  "group" => 'Layer #2',
		  "heading" => __("Offset X", "js_composer"),
		  "param_name" => "image_2_offset_x_sign",
		  "value" => array(
			 "+" => "+",
			 "-" => "-"
		   ),
		  "edit_field_class" => "col-md-2",
		  'save_always' => true
		),
	    array(
		  "type" => "dropdown",
		  "group" => 'Layer #2',
		  "heading" => __("Offset X", "js_composer"),
		  "param_name" => "image_2_offset_x",
		  "edit_field_class" => "col-md-4",
		  "value" => $nectar_offset_vals_arr,
		  'save_always' => true
		),
		 array(
		  "type" => "dropdown",
		  "group" => 'Layer #2',
		  "heading" => __("Offset Y", "js_composer"),
		  "param_name" => "image_2_offset_y_sign",
		  'edit_field_class' => 'offset-y-sign',
		  "edit_field_class" => "col-md-2",
		  "value" => array(
			 "+" => "+",
			 "-" => "-"
		   ),
		  'save_always' => true
		),
	    array(
		  "type" => "dropdown",
		  "group" => 'Layer #2',
		  "heading" => __("Offset Y", "js_composer"),
		  "param_name" => "image_2_offset_y",
		  "value" => $nectar_offset_vals_arr,
		  'edit_field_class' => 'offset-y',
		  "edit_field_class" => "col-md-4",
		  'save_always' => true
		),
		array(
		  "type" => "dropdown",
		  "group" => 'Layer #2',
		  "heading" => __("Rotate", "js_composer"),
		  "param_name" => "image_2_rotate_sign",
		  "value" => array(
			 "+" => "+",
			 "-" => "-"
		   ),
		  "edit_field_class" => "col-md-2",
		  'save_always' => true
		),
		array(
		  "type" => "dropdown",
		  "group" => 'Layer #2',
		  "heading" => __("Rotate", "js_composer"),
		  "param_name" => "image_2_rotate",
		  "edit_field_class" => "col-md-4",
		  "value" => array(
		     "None" => "none",
			 "2.5°" => "2.5",
			 "5°" => "5",
			 "7.5°" => "7.5",	
			 "10°" => "10",
			 "12.5°" => "12.5",
			 "15°" => "15",
			 "17.5°" => "17.5",	
			 "20°" => "20"
		   ),
		  'save_always' => true
		),
		array(
		  "type" => "dropdown",
		  "heading" => __("CSS Animation", "js_composer"),
		  "group" => 'Layer #2',
		  "param_name" => "image_2_animation",
		  "value" => array(
			    __("Fade In", "js_composer") => "Fade In", 
			    __("Fade In From Left", "js_composer") => "Fade In From Left", 
			    __("Fade In From Right", "js_composer") => "Fade In From Right", 
			    __("Fade In From Bottom", "js_composer") => "Fade In From Bottom", 
			    __("Grow In", "js_composer") => "Grow In",
			    __("Flip In", "js_composer") => "Flip In",
			    __("None", "js_composer") => "None"
			),
		  'save_always' => true,
		  "description" => __("Select animation type if you want this layer to be animated when it enters into the browsers viewport.", "js_composer")
		),
	    array(
	      "type" => "dropdown",
	      "group" => 'Layer #2',
	      "heading" => __("Box Shadow", "js_composer"),
	      'save_always' => true,
	      "param_name" => "image_2_box_shadow",
	      "value" => array(__("None", "js_composer") => "none", __("Small Depth", "js_composer") => "small_depth", __("Medium Depth", "js_composer") => "medium_depth", __("Large Depth", "js_composer") => "large_depth", __("Very Large Depth", "js_composer") => "x_large_depth"),
	      "description" => __("Select your desired image box shadow", "js_composer")
	    ),

	    array(
	      "type" => "fws_image",
	      "group" => 'Layer #3',
	      "heading" => __("Image #3", "js_composer"),
	      "param_name" => "image_3_url",
	      "value" => "",
	      "description" => __("Select image from media library.", "js_composer")
	    ),
	    array(
			"type" => "colorpicker",
			"class" => "",
			"group" => 'Layer #3',
			"heading" => "Layer BG Color",
			"param_name" => "image_3_bg_color",
			"value" => "",
			"description" => "Use this to set a BG color for the layer"
		),
	    array(
		  "type" => "dropdown",
		  "group" => 'Layer #3',
		  "heading" => __("Offset X", "js_composer"),
		  "param_name" => "image_3_offset_x_sign",
		  "value" => array(
			 "+" => "+",
			 "-" => "-"
		   ),
		  "edit_field_class" => "col-md-2",
		  'save_always' => true
		),
	    array(
		  "type" => "dropdown",
		  "group" => 'Layer #3',
		  "heading" => __("Offset X", "js_composer"),
		  "param_name" => "image_3_offset_x",
		  "edit_field_class" => "col-md-4",
		  "value" => $nectar_offset_vals_arr,
		  'save_always' => true
		),
		 array(
		  "type" => "dropdown",
		  "group" => 'Layer #3',
		  "heading" => __("Offset Y", "js_composer"),
		  "param_name" => "image_3_offset_y_sign",
		  'edit_field_class' => 'offset-y-sign',
		  "edit_field_class" => "col-md-2",
		  "value" => array(
			 "+" => "+",
			 "-" => "-"
		   ),
		  'save_always' => true
		),
	    array(
		  "type" => "dropdown",
		  "group" => 'Layer #3',
		  "heading" => __("Offset Y", "js_composer"),
		  "param_name" => "image_3_offset_y",
		  "value" => $nectar_offset_vals_arr,
		  'edit_field_class' => 'offset-y',
		  "edit_field_class" => "col-md-4",
		  'save_always' => true
		),
		array(
		  "type" => "dropdown",
		  "group" => 'Layer #3',
		  "heading" => __("Rotate", "js_composer"),
		  "param_name" => "image_3_rotate_sign",
		  "value" => array(
			 "+" => "+",
			 "-" => "-"
		   ),
		  "edit_field_class" => "col-md-2",
		  'save_always' => true
		),
		array(
		  "type" => "dropdown",
		  "group" => 'Layer #3',
		  "heading" => __("Rotate", "js_composer"),
		  "param_name" => "image_3_rotate",
		  "edit_field_class" => "col-md-4",
		  "value" => array(
		     "None" => "none",
			 "2.5°" => "2.5",
			 "5°" => "5",
			 "7.5°" => "7.5",	
			 "10°" => "10",
			 "12.5°" => "12.5",
			 "15°" => "15",
			 "17.5°" => "17.5",	
			 "20°" => "20"
		   ),
		  'save_always' => true
		),
		array(
		  "type" => "dropdown",
		  "heading" => __("CSS Animation", "js_composer"),
		  "group" => 'Layer #3',
		  "param_name" => "image_3_animation",
		  "value" => array(
			    __("Fade In", "js_composer") => "Fade In", 
			    __("Fade In From Left", "js_composer") => "Fade In From Left", 
			    __("Fade In From Right", "js_composer") => "Fade In From Right", 
			    __("Fade In From Bottom", "js_composer") => "Fade In From Bottom", 
			    __("Grow In", "js_composer") => "Grow In",
			    __("Flip In", "js_composer") => "Flip In",
			    __("None", "js_composer") => "None"
			),
		  'save_always' => true,
		  "description" => __("Select animation type if you want this layer to be animated when it enters into the browsers viewport.", "js_composer")
		),
	    array(
	      "type" => "dropdown",
	      "group" => 'Layer #3',
	      "heading" => __("Box Shadow", "js_composer"),
	      'save_always' => true,
	      "param_name" => "image_3_box_shadow",
	      "value" => array(__("None", "js_composer") => "none", __("Small Depth", "js_composer") => "small_depth", __("Medium Depth", "js_composer") => "medium_depth", __("Large Depth", "js_composer") => "large_depth", __("Very Large Depth", "js_composer") => "x_large_depth"),
	      "description" => __("Select your desired image box shadow", "js_composer")
	    ),

	    array(
	      "type" => "fws_image",
	      "group" => 'Layer #4',
	      "heading" => __("Image #4", "js_composer"),
	      "param_name" => "image_4_url",
	      "value" => "",
	      "description" => __("Select image from media library.", "js_composer")
	    ),
	    array(
			"type" => "colorpicker",
			"class" => "",
			"group" => 'Layer #4',
			"heading" => "Layer BG Color",
			"param_name" => "image_4_bg_color",
			"value" => "",
			"description" => "Use this to set a BG color for the layer"
		),
	    array(
		  "type" => "dropdown",
		  "group" => 'Layer #4',
		  "heading" => __("Offset X", "js_composer"),
		  "param_name" => "image_4_offset_x_sign",
		  "value" => array(
			 "+" => "+",
			 "-" => "-"
		   ),
		  "edit_field_class" => "col-md-2",
		  'save_always' => true
		),
	    array(
		  "type" => "dropdown",
		  "group" => 'Layer #4',
		  "heading" => __("Offset X", "js_composer"),
		  "param_name" => "image_4_offset_x",
		  "edit_field_class" => "col-md-4",
		  "value" => $nectar_offset_vals_arr,
		  'save_always' => true
		),
		 array(
		  "type" => "dropdown",
		  "group" => 'Layer #4',
		  "heading" => __("Offset Y", "js_composer"),
		  "param_name" => "image_4_offset_y_sign",
		  'edit_field_class' => 'offset-y-sign',
		  "edit_field_class" => "col-md-2",
		  "value" => array(
			 "+" => "+",
			 "-" => "-"
		   ),
		  'save_always' => true
		),
	    array(
		  "type" => "dropdown",
		  "group" => 'Layer #4',
		  "heading" => __("Offset Y", "js_composer"),
		  "param_name" => "image_4_offset_y",
		  "value" => $nectar_offset_vals_arr,
		  'edit_field_class' => 'offset-y',
		  "edit_field_class" => "col-md-4",
		  'save_always' => true
		),
		array(
		  "type" => "dropdown",
		  "group" => 'Layer #4',
		  "heading" => __("Rotate", "js_composer"),
		  "param_name" => "image_4_rotate_sign",
		  "value" => array(
			 "+" => "+",
			 "-" => "-"
		   ),
		  "edit_field_class" => "col-md-2",
		  'save_always' => true
		),
		array(
		  "type" => "dropdown",
		  "group" => 'Layer #4',
		  "heading" => __("Rotate", "js_composer"),
		  "param_name" => "image_4_rotate",
		  "edit_field_class" => "col-md-4",
		  "value" => array(
		     "None" => "none",
			 "2.5°" => "2.5",
			 "5°" => "5",
			 "7.5°" => "7.5",	
			 "10°" => "10",
			 "12.5°" => "12.5",
			 "15°" => "15",
			 "17.5°" => "17.5",	
			 "20°" => "20"
		   ),
		  'save_always' => true
		),
		array(
		  "type" => "dropdown",
		  "heading" => __("CSS Animation", "js_composer"),
		  "group" => 'Layer #4',
		  "param_name" => "image_4_animation",
		  "value" => array(
			    __("Fade In", "js_composer") => "Fade In", 
			    __("Fade In From Left", "js_composer") => "Fade In From Left", 
			    __("Fade In From Right", "js_composer") => "Fade In From Right", 
			    __("Fade In From Bottom", "js_composer") => "Fade In From Bottom", 
			    __("Grow In", "js_composer") => "Grow In",
			    __("Flip In", "js_composer") => "Flip In",
			    __("None", "js_composer") => "None"
			),
		  'save_always' => true,
		  "description" => __("Select animation type if you want this layer to be animated when it enters into the browsers viewport.", "js_composer")
		),
	    array(
	      "type" => "dropdown",
	      "group" => 'Layer #4',
	      "heading" => __("Box Shadow", "js_composer"),
	      'save_always' => true,
	      "param_name" => "image_4_box_shadow",
	      "value" => array(__("None", "js_composer") => "none", __("Small Depth", "js_composer") => "small_depth", __("Medium Depth", "js_composer") => "medium_depth", __("Large Depth", "js_composer") => "large_depth", __("Very Large Depth", "js_composer") => "x_large_depth"),
	      "description" => __("Select your desired image box shadow", "js_composer")
	    ),
	     array(
	      "type" => "textfield",
	      "heading" => __("Time Between Animations", "js_composer"),
	      "param_name" => "animation_timing",
	      "description" => __("Enter your desired time between animations in milliseconds, defaults to 200 if left blank", "js_composer")
	    )

	  )

	));




	// Image Comparision
	class WPBakeryShortCode_Nectar_Image_Comparison extends WPBakeryShortCode {}
	
	vc_map( array(
	  "name" => __("Image Comparison", "js_composer"),
	  "base" => "nectar_image_comparison",
	  "icon" => "icon-wpb-single-image",
	  "category" => __('Nectar Elements', 'js_composer'),
	  "description" => __('Shows differences in two images', 'js_composer'),
	  "params" => array(
	    array(
	      "type" => "fws_image",
	      "heading" => __("Image One", "js_composer"),
	      "param_name" => "image_url",
	      "value" => "",
	      "description" => __("Select image from media library.", "js_composer")
	    ),
	    array(
	      "type" => "fws_image",
	      "heading" => __("Image Two", "js_composer"),
	      "param_name" => "image_2_url",
	      "value" => "",
	      "description" => __("Select image from media library.", "js_composer")
	    ),
	    array(
	      "type" => "textfield",
	      "heading" => __("Extra class name", "js_composer"),
	      "param_name" => "el_class",
	      "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "js_composer")
	    )
	  )
	));



	// Portfolio

		$portfolio_types = ($is_admin) ? get_terms('project-type') : array('All' => 'all');

		$types_options = array("All" => "all");
		$types_options_2 = array("Default" => "default");

		if($is_admin) {
			foreach ($portfolio_types as $type) {
				$types_options[$type->name] = $type->slug;
				$types_options_2[$type->name] = $type->slug;
			}

		} else {
			$types_options['All'] = 'all';
			$types_options_2['All'] = 'all';
		}




		vc_map( array(
		  "name" => __("Portfolio", "js_composer"),
		  "base" => "nectar_portfolio",
		  "weight" => 8,
		  "icon" => "icon-wpb-portfolio",
		  "category" => __('Nectar Elements', 'js_composer'),
		  "description" => __('Add a portfolio element', 'js_composer'),
		  "params" => array(
			array(
			  "type" => "dropdown",
			  "heading" => __("Layout", "js_composer"),
			  "param_name" => "layout",
			  "admin_label" => true,
			  "value" => array(
				    "3 Columns" => "3",
				    "4 Columns" => "4",
				    "Fullwidth" => "fullwidth"
				),
			  "description" => __("Please select the layout you would like for your portfolio ", "js_composer"),
			  'save_always' => true
			),
			array(
		      "type" => 'checkbox',
		      "heading" => __("Constrain Max Columns to 4?", "js_composer"),
		      "param_name" => "constrain_max_cols",
		      "description" => __("This will change the max columns to 4 (default is 5 for fullwidth). Activating this will make it easier to create a grid with no empty spaces at the end of the list on all screen sizes.", "js_composer"),
		      "value" => Array(__("Yes, please", "js_composer") => 'true'),
		      "dependency" => Array('element' => "layout", 'value' => 'fullwidth')
		    ),
		    /*
		    array(
		      "type" => 'checkbox',
		      "heading" => __("Remove Column Padding?", "js_composer"),
		      "param_name" => "remove_column_padding",
		      "description" => __("This will allow your projects to sit flush against each other.", "js_composer"),
		      "value" => Array(__("Yes, please", "js_composer") => 'true'),
		      "dependency" => Array('element' => "layout", 'value' => array('3', '4'))
		    ),*/
		    array(
			  "type" => "dropdown_multi",
			  "heading" => __("Portfolio Categories", "js_composer"),
			  "param_name" => "category",
			  "admin_label" => true,
			  "value" => $types_options,
			  'save_always' => true,
			  "description" => __("Please select the categories you would like to display for your portfolio. <br/> You can select multiple categories too (ctrl + click on PC and command + click on Mac).", "js_composer")
			),
			array(
			  "type" => "dropdown",
			  "heading" => __("Starting Category", "js_composer"),
			  "param_name" => "starting_category",
			  "admin_label" => false,
			  "value" => $types_options_2,
			  'save_always' => true,
			  "description" => __("Please select the category you would like you're portfolio to start filtered on.", "js_composer"),
			  "dependency" => Array('element' => "enable_sortable", 'not_empty' => true)
			),
		    array(
			  "type" => "dropdown",
			  "heading" => __("Project Style", "js_composer"),
			  "param_name" => "project_style",
			  "admin_label" => true,
			  'save_always' => true,
			  "value" => array(
				    "Meta below thumb w/ links on hover" => "1",
				    "Meta on hover + entire thumb link" => "2",
				    "Meta on hover w/ zoom + entire thumb link" => "7",
				    "Title overlaid w/ zoom effect on hover" => "3",
				    "Title overlaid w/ zoom effect on hover alt" => "5",
				    "Meta from bottom on hover + entire thumb link" => "4",
				    "3D Parallax on hover" => "6"
				),
			  "description" => __("Please select the style you would like your projects to display in ", "js_composer")
			),
			array(
			  "type" => "dropdown",
			  "heading" => __("Item Spacing", "js_composer"),
			  "param_name" => "item_spacing",
			  'save_always' => true,
			  "value" => array(
			  		"Default" => "default",
				    "1px" => "1px",
				    "2px" => "2px",
				    "3px" => "3px",
				    "4px" => "4px",
				    "5px" => "5px",
				    "6px" => "6px",
				    "7px" => "7px",
				    "8px" => "8px",
				    "9px" => "9px",
				    "10px" => "10px",
				    "15px" => "15px",
				    "20px" => "20px"
				),
			  "dependency" => Array('element' => "layout", 'value' => array('fullwidth')),
			  "description" => __("Please select the spacing you would like between your items. ", "js_composer")
			),/*
			array(
		      "type" => 'checkbox',
		      "heading" => __("Disable Featured Image Cropping", "js_composer"),
		      "param_name" => "disable_cropping",
		      "description" => __("This will allow your portfolio items to display without any cropping - useful for photography layouts.", "js_composer"),
		      "value" => Array(__("Yes, please", "js_composer") => 'true')
		    ),*/
			array(
		      "type" => 'checkbox',
		      "heading" => __("Masonry Style", "js_composer"),
		      "param_name" => "masonry_style",
		      "description" => __("This will allow your portfolio items to display in a masonry layout as opposed to a fixed grid. You can define your masonry sizes in each project. <br/> ", "js_composer"),
		      "value" => Array(__("Yes, please", "js_composer") => 'true')
		    ),
		    array(
		      "type" => 'checkbox',
		      "heading" => __("Enable Sortable", "js_composer"),
		      "param_name" => "enable_sortable",
		      "description" => __("Checking this box will allow your portfolio to display sortable filters", "js_composer"),
		      "value" => Array(__("Yes, please", "js_composer") => 'true')
		    ),
		    array(
		      "type" => 'checkbox',
		      "heading" => __("Horizontal Filters", "js_composer"),
		      "param_name" => "horizontal_filters",
		      "description" => __("This will allow your filters to display horizontally instead of in a dropdown. (Only used if you enable sortable above.)", "js_composer"),
		      "value" => Array(__("Yes, please", "js_composer") => 'true'),
		      "dependency" => Array('element' => "enable_sortable", 'not_empty' => true)
		    ),
		    array(
			  "type" => "dropdown",
			  "heading" => __("Filter Alignment", "js_composer"),
			  "param_name" => "filter_alignment",
			  "value" => array(
			     "Default" => "default",
				 "Centered" => "center"
			   ),
			  'save_always' => true,
			  "dependency" => Array('element' => "horizontal_filters", 'not_empty' => true),
			  "description" => __("Please select the alignment you would like for your horizontal filters", "js_composer")
			),
		    array(
			  "type" => "dropdown",
			  "heading" => __("Filter Color Scheme", "js_composer"),
			  "param_name" => "filter_color",
			  "value" => array(
			     "Default" => "default",
				 "Accent-Color" => "accent-color",
				 "Extra-Color-1" => "extra-color-1",
				 "Extra-Color-2" => "extra-color-2",	
				 "Extra-Color-3" => "extra-color-3",
				 "Accent-Color Underline" => "accent-color-underline",
				 "Extra-Color-1 Underline" => "extra-color-1-underline",
				 "Extra-Color-2 Underline" => "extra-color-2-underline",	
				 "Extra-Color-3 Underline" => "extra-color-3-underline",
				 "Black" => "black"
			   ),
			  'save_always' => true,
			  "dependency" => Array('element' => "enable_sortable", 'not_empty' => true),
			  "description" => __("Please select the color scheme you would like for your filters. Only applies to full width inline filters and regular dropdown filters", "js_composer")
			),

		    array(
		      "type" => 'checkbox',
		      "heading" => __("Enable Pagination", "js_composer"),
		      "param_name" => "enable_pagination",
		      "description" => __("Would you like to enable pagination for this portfolio?", "js_composer"),
		      "value" => Array(__("Yes, please", "js_composer") => 'true')
		    ),
		    array(
			  "type" => "dropdown",
			  "heading" => __("Pagination Type", "js_composer"),
			  "param_name" => "pagination_type",
			  "admin_label" => true,
			  "value" => array(	
				    'Default' => 'default',
				    'Infinite Scroll' => 'infinite_scroll',
				),
			  'save_always' => true,
			  "description" => __("Please select your pagination type here.", "js_composer"),
			  "dependency" => Array('element' => "enable_pagination", 'not_empty' => true)
			),
		    array(
		      "type" => "textfield",
		      "heading" => __("Projects Per Page", "js_composer"),
		      "param_name" => "projects_per_page",
		      "description" => __("How many projects would you like to display per page? <br/> If pagination is not enabled, will simply show this number of projects <br/> Enter as a number example \"20\"", "js_composer")
		    ),
		    array(
		      "type" => 'checkbox',
		      "heading" => __("Lightbox Only", "js_composer"),
		      "param_name" => "lightbox_only",
		      "description" => __("This will remove the single project page from being accessible thus rendering your portfolio into only a gallery.", "js_composer"),
		      "value" => Array(__("Yes, please", "js_composer") => 'true')
		    ),
		    array(
			  "type" => "dropdown",
			  "heading" => __("Load In Animation", "js_composer"),
			  "param_name" => "load_in_animation",
			  'save_always' => true,
			  "value" => array(
				    "None" => "none",
				    "Fade In" => "fade_in",
				    "Fade In From Bottom" => "fade_in_from_bottom"
				),
			  "description" => __("Please select the style you would like your projects to display in ", "js_composer")
			)
		  )
		));





		vc_map( array(
		  "name" => __("Recent Projects", "js_composer"),
		  "base" => "recent_projects",
		  "weight" => 8,
		  "icon" => "icon-wpb-recent-projects",
		  "category" => __('Nectar Elements', 'js_composer'),
		  "description" => __('Show off some recent projects', 'js_composer'),
		  "params" => array(
		    array(
			  "type" => "dropdown_multi",
			  "heading" => __("Portfolio Categories", "js_composer"),
			  "param_name" => "category",
			  "admin_label" => true,
			  "value" => $types_options,
			  'save_always' => true,
			  "description" => __("Please select the categories you would like to display for your recent projects carousel. <br/> You can select multiple categories too (ctrl + click on PC and command + click on Mac).", "js_composer")
			),
		    array(
			  "type" => "dropdown",
			  "heading" => __("Project Style", "js_composer"),
			  "param_name" => "project_style",
			  "admin_label" => true,
			  "value" => array(
				    "Meta below thumb w/ links on hover" => "1",
				    "Meta on hover + entire thumb link" => "2",
				    "Title overlaid w/ zoom effect on hover" => "3",
				    "Meta from bottom on hover + entire thumb link" => "4"
				),
			  'save_always' => true,
			  "description" => __("Please select the style you would like your projects to display in ", "js_composer")
			),
			array(
		      "type" => 'checkbox',
		      "heading" => __("Full Width Carousel", "js_composer"),
		      "param_name" => "full_width",
		      "description" => __("This will make your carousel extend the full width of the page.", "js_composer"),
		      "value" => Array(__("Yes, please", "js_composer") => 'true')
		    ),
		    array(
		      "type" => "textfield",
		      "heading" => __("Heading Text", "js_composer"),
		      "param_name" => "heading",
		      "description" => __("Enter any text you would like for the heading of your carousel", "js_composer")
		    ),
			array(
		      "type" => "textfield",
		      "heading" => __("Page Link Text", "js_composer"),
		      "param_name" => "page_link_text",
		      "description" => __("This will be the text that is in a link leading users to your desired page (will be omitted for full width carousels and an icon will be used instead)", "js_composer")
		    ),
		    array(
		      "type" => "textfield",
		      "heading" => __("Page Link URL", "js_composer"),
		      "param_name" => "page_link_url",
		      "description" => __("Enter portfolio page URL you would like to link to. Remember to include \"http://\"!", "js_composer")
		    ),	
		    array(
			  "type" => "dropdown",
			  "heading" => __("Controls & Text Color", "js_composer"),
			  "param_name" => "control_text_color",
			  "value" => array(
				    "Dark" => "dark",
				    "Light" => "light",
				),
			  'save_always' => true,
			  "description" => __("Please select the color you desire for your carousel controls/heading text.", "js_composer")
			),
		    array(
		      "type" => 'checkbox',
		      "heading" => __("Hide Carousel Controls", "js_composer"),
		      "param_name" => "hide_controls",
		      "description" => __("Checking this box will remove the controls from your carousel", "js_composer"),
		      "value" => Array(__("Yes, please", "js_composer") => 'true')
		    ),
		    array(
		      "type" => "textfield",
		      "heading" => __("Number of Projects To Show", "js_composer"),
		      "param_name" => "number_to_display",
		      "description" => __("Enter as a number example \"6\"", "js_composer")
		    ),
		    array(
		      "type" => 'checkbox',
		      "heading" => __("Lightbox Only", "js_composer"),
		      "param_name" => "lightbox_only",
		      "description" => __("This will remove the single project page from being accessible thus rendering your portfolio into only a gallery.", "js_composer"),
		      "value" => Array(__("Yes, please", "js_composer") => 'true')
		    )
		  )
		));





	// Blog

		$blog_types = ($is_admin) ? get_categories() : array('All' => 'all');

		$blog_options = array("All" => "all");

		if($is_admin) {
			foreach ($blog_types as $type) {
				if(isset($type->name) && isset($type->slug))
					$blog_options[htmlspecialchars($type->name)] = htmlspecialchars($type->slug);
			}
		} else {
			$blog_options['All'] = 'all';
		}





		vc_map( array(
		  "name" => __("Blog", "js_composer"),
		  "base" => "nectar_blog",
		  "weight" => 8,
		  "icon" => "icon-wpb-blog",
		  "category" => __('Nectar Elements', 'js_composer'),
		  "description" => __('Display a Blog element', 'js_composer'),
		  "params" => array(
		    array(
			  "type" => "dropdown",
			  "heading" => __("Layout", "js_composer"),
			  "param_name" => "layout",
			  "admin_label" => true,
			  "value" => array(
				    'Standard Blog W/ Sidebar' => 'std-blog-sidebar',
				    'Standard Blog No Sidebar' => 'std-blog-fullwidth',
				    'Masonry Blog W/ Sidebar' => 'masonry-blog-sidebar',
				    'Masonry Blog No Sidebar' => 'masonry-blog-fullwidth',
				    'Masonry Blog Fullwidth' => 'masonry-blog-full-screen-width'
				),
			  'save_always' => true,
			  "description" => __("Please select the layout you desire for your blog", "js_composer")
			),
			array(
			  "type" => "dropdown_multi",
			  "heading" => __("Blog Categories", "js_composer"),
			  "param_name" => "category",
			  "admin_label" => true,
			  "value" => $blog_options,
			  'save_always' => true,
			  "description" => __("Please select the categories you would like to display for your blog. <br/> You can select multiple categories too (ctrl + click on PC and command + click on Mac).", "js_composer")
			),
			array(
		      "type" => 'checkbox',
		      "heading" => __("Enable Pagination", "js_composer"),
		      "param_name" => "enable_pagination",
		      "description" => __("Would you like to enable pagination?", "js_composer"),
		      "value" => Array(__("Yes, please", "js_composer") => 'true')
		    ),
		    array(
			  "type" => "dropdown",
			  "heading" => __("Pagination Type", "js_composer"),
			  "param_name" => "pagination_type",
			  "admin_label" => true,
			  "value" => array(	
				    'Default' => 'default',
				    'Infinite Scroll' => 'infinite_scroll',
				),
			  'save_always' => true,
			  "description" => __("Please select your pagination type here.", "js_composer"),
			  "dependency" => Array('element' => "enable_pagination", 'not_empty' => true)
			),
		    array(
		      "type" => "textfield",
		      "heading" => __("Posts Per Page", "js_composer"),
		      "param_name" => "posts_per_page",
		      "description" => __("How many posts would you like to display per page? <br/> If pagination is not enabled, will simply show this number of posts <br/> Enter as a number example \"10\"", "js_composer")
		    ),
		    array(
			  "type" => "dropdown",
			  "heading" => __("Load In Animation", "js_composer"),
			  "param_name" => "load_in_animation",
			  'save_always' => true,
			  "value" => array(
				    "None" => "none",
				    "Fade In" => "fade_in",
				    "Fade In From Bottom" => "fade_in_from_bottom"
				),
			  "description" => __("Please select the loading animation you would like ", "js_composer")
			)
		  )
		));



		vc_map( array(
		  "name" => __("Recent Posts", "js_composer"),
		  "base" => "recent_posts",
		  "weight" => 8,
		  "icon" => "icon-wpb-recent-posts",
		  "category" => __('Nectar Elements', 'js_composer'),
		  "description" => __('Display your recent blog posts', 'js_composer'),
		  "params" => array(
		  	array(
			  "type" => "dropdown",
			  "heading" => __("Style", "js_composer"),
			  "param_name" => "style",
			  "admin_label" => true,
			  "value" => array(	
				    'Default' => 'default',
				    'Minimal' => 'minimal',
				    'Minimal - Title Only' => 'title_only',
				    'Slider' => 'slider'
				),
			  'save_always' => true,
			  "description" => __("Please select desired style here.", "js_composer")
			),
			array(
		      "type" => "textfield",
		      "heading" => __("Slider Height", "js_composer"),
		      "param_name" => "slider_size",
		      "admin_label" => false,
		      "dependency" => Array('element' => "style", 'value' => 'slider'),
		      "description" => __("Don't include \"px\" in your string. e.g. 650", "js_composer")
		    ),
			array(
			  "type" => "dropdown_multi",
			  "heading" => __("Blog Categories", "js_composer"),
			  "param_name" => "category",
			  "admin_label" => true,
			  "value" => $blog_options,
			  'save_always' => true,
			  "description" => __("Please select the categories you would like to display in your recent posts. <br/> You can select multiple categories too (ctrl + click on PC and command + click on Mac).", "js_composer")
			),
			array(
			  "type" => "dropdown",
			  "heading" => __("Number Of Columns", "js_composer"),
			  "param_name" => "columns",
			  "admin_label" => false,
			  "value" => array(
			  	'4' => '4',
			  	'3' => '3',
			  	'2' => '2',
			  	'1' => '1'
			  ),
			  "dependency" => Array('element' => "style", 'value' => array('default','minimal','title_only')),
			  'save_always' => true,
			  "description" => __("Please select the number of posts you would like to display.", "js_composer")
			),
			array(
		      "type" => "textfield",
		      "heading" => __("Number Of Posts", "js_composer"),
		      "param_name" => "posts_per_page",
		      "description" => __("How many posts would you like to display? <br/> Enter as a number example \"4\"", "js_composer")
		    ),
		    array(
		      "type" => "textfield",
		      "heading" => __("Post Offset", "js_composer"),
		      "param_name" => "post_offset",
		      "description" => __("Optioinally enter a number e.g. \"2\" to offset your posts by - useful for when you're using multiple styles of this element on the same page and would like them to no show duplicate posts", "js_composer")
		    ),
			array(
		      "type" => 'checkbox',
		      "heading" => __("Enable Title Labels", "js_composer"),
		      "param_name" => "title_labels",
		      "description" => __("These labels are defined by you in the \"Blog Options\" tab of your theme options panel.", "js_composer"),
		      "value" => Array(__("Yes, please", "js_composer") => 'true'),
		      "dependency" => Array('element' => "style", 'value' => 'default')
		    ),
		  )
		));








	//WooCommerce Related


	global $woocommerce;

	if($woocommerce) {

		class WPBakeryShortCode_Nectar_Woo_Products extends WPBakeryShortCode {
			
			
		}


		$woo_args = array(
			'taxonomy' => 'product_cat',
		);
		$woo_types = ($is_admin) ? get_categories($woo_args) : array('All' => 'all');
		$woo_options = array("All" => "all");

		if($is_admin) {
			foreach ($woo_types as $type) {
				$woo_options[$type->name] = $type->slug;
			}
		} else {
			$woo_options['All'] = 'all';
		}

		////recent products
		vc_map( array(
		  "name" => __("WooCommerce Products", "js_composer"),
		  "base" => "nectar_woo_products",
		  "weight" => 8,
		  "icon" => "icon-wpb-recent-products",
		  "category" => __('Nectar Elements', 'js_composer'),
		  "description" => __('Display your products', 'js_composer'),
		  "params" => array(
		  	array(
			  "type" => "dropdown",
			  "heading" => __("Product Type", "js_composer"),
			  "param_name" => "product_type",
			  "value" => array(
			  	'All' => 'all',
			  	'Sale Only' => 'sale',
			  	'Featured Only' => 'featured',
			  	'Best Selling Only' => 'best_selling'
			  ),
			  'save_always' => true,
			  "description" => __("Please select the type of products you would like to display.", "js_composer")
			),
			array(
			  "type" => "dropdown_multi",
			  "heading" => __("Product Categories", "js_composer"),
			  "param_name" => "category",
			  "admin_label" => true,
			  "value" => $woo_options,
			  'save_always' => true,
			  "description" => __("Please select the categories you would like to display in your products. <br/> You can select multiple categories too (ctrl + click on PC and command + click on Mac).", "js_composer")
			),
			array(
			  "type" => "dropdown",
			  "heading" => __("Number Of Columns", "js_composer"),
			  "param_name" => "columns",
			  "value" => array(
			  	'4' => '4',
			  	'3' => '3',
			  	'2' => '2',
			  	'1' => '1'
			  ),
			  'save_always' => true,
			  "description" => __("Please select the number of columns you would like to display.", "js_composer")
			),
			array(
		      "type" => "textfield",
		      "heading" => __("Number Of Products", "js_composer"),
		      "param_name" => "per_page",
		       "admin_label" => true,
		      "description" => __("How many posts would you like to display? <br/> Enter as a number example \"4\"", "js_composer")
		    ),
		    array(
		      "type" => 'checkbox',
		      "heading" => __("Enable Carousel Display", "js_composer"),
		      "param_name" => "carousel",
		      "description" => __("This will override your column choice", "js_composer"),
		      "value" => Array(__("Yes, please", "js_composer") => true),
		    ),
		    array(
		      "type" => 'checkbox',
		      "heading" => __("Enable Controls On Hover", "js_composer"),
		      "param_name" => "controls_on_hover",
		      "dependency" => Array('element' => "carousel", 'not_empty' => true),
		      "description" => __("This will add buttons for additional user control over your product carousel", "js_composer"),
		      "value" => Array(__("Yes, please", "js_composer") => true),
		    )
		  )
		));


	}


	// Centered Heading
	vc_map( array(
	  "name" => __("Centered Heading", "js_composer"),
	  "base" => "heading",
	  "icon" => "icon-wpb-centered-heading",
	  "category" => __('Nectar Elements', 'js_composer'),
	  "description" => __('Simple heading', 'js_composer'),
	  "params" => array(
	    array(
	      "type" => "textarea_html",
	      "holder" => "div",
	      "heading" => __("Heading", "js_composer"),
	      "param_name" => "content",
	      "value" => ''
	    ), 
	    array(
	      "type" => "textfield",
	      "heading" => __("Subtitle", "js_composer"),
	      "param_name" => "subtitle",
	      "description" => __("The subtitle text under the main title", "js_composer")
	    )
	  )
	));




	// video lightbox
	class WPBakeryShortCode_Nectar_Video_Lightbox extends WPBakeryShortCode {}
	vc_map( array(
	  "name" => __("Video Lightbox", "js_composer"),
	  "base" => "nectar_video_lightbox",
	  "icon" => "icon-wpb-video-lightbox",
	  "category" => __('Nectar Elements', 'js_composer'),
	  "description" => __('Add a video lightbox link', 'js_composer'),
	  "params" => array(
	  	array(
		  "type" => "dropdown",
		  "heading" => __("Link Style", "js_composer"),
		  "param_name" => "link_style",
		  "value" => array(
		     "Play Button" => "play_button",
		     "Play Button With Preview Image" => "play_button_2",
			 "Nectar Button" => "nectar-button"
		   ),
		  'save_always' => true,
		  "admin_label" => true,
		  "description" => __("Please select your link style", "js_composer")	  
		),
		array(
	      "type" => "textfield",
	      "heading" => __("Video URL", "js_composer"),
	      "param_name" => "video_url",
	      "admin_label" => false,
	      "description" => __("The URL to your video on Youtube or Vimeo e.g. <br/> https://vimeo.com/118023315 <br/> https://www.youtube.com/watch?v=6oTurM7gESE", "js_composer")
	    ),
	    array(
		  "type" => "dropdown",
		  "heading" => __("Play Button Color", "js_composer"),
		  "param_name" => "nectar_play_button_color",
		  "value" => array(
			 "Accent-Color" => "Default-Accent-Color",
			 "Extra-Color-1" => "Extra-Color-1",
			 "Extra-Color-2" => "Extra-Color-2",	
			 "Extra-Color-3" => "Extra-Color-3"
		   ),
		  'save_always' => true,
		  "dependency" => array('element' => "link_style", 'value' => "play_button_2"),
		  "description" => __("Please select the color you desire", "js_composer")
		),
	    array(
	      "type" => "fws_image",
	      "heading" => __("Video Preview Image", "js_composer"),
	      "param_name" => "image_url",
	      "value" => "",
	      "dependency" => array('element' => "link_style", 'value' => "play_button_2"),
	      "description" => __("Select image from media library.", "js_composer")
	    ),
	    array(
	      "type" => "dropdown",
	      "dependency" => array('element' => "link_style", 'value' => "play_button_2"),
	      "heading" => __("Box Shadow", "js_composer"),
	      'save_always' => true,
	      "param_name" => "box_shadow",
	      "value" => array(__("None", "js_composer") => "none", __("Small Depth", "js_composer") => "small_depth", __("Medium Depth", "js_composer") => "medium_depth", __("Large Depth", "js_composer") => "large_depth", __("Very Large Depth", "js_composer") => "x_large_depth"),
	      "description" => __("Select your desired image box shadow", "js_composer")
	    ),
	    array(
	      "type" => "textfield",
	      "heading" => __("Link Text", "js_composer"),
	      "param_name" => "link_text",
	      "admin_label" => false,
	      "dependency" => array('element' => "link_style", 'value' => "nectar-button"),
	      "description" => __("The text that will be displayed for your link", "js_composer")
	    ),
	   

	     array(
		  "type" => "dropdown",
		  "heading" => __("Color", "js_composer"),
		  "param_name" => "nectar_button_color",
		  "value" => array(
			 "Accent-Color" => "Default-Accent-Color",
			 "Extra-Color-1" => "Default-Extra-Color-1",
			 "Extra-Color-2" => "Default-Extra-Color-2",	
			 "Extra-Color-3" => "Default-Extra-Color-3",
			 "Transparent-Accent-Color" =>  "Transparent-Accent-Color",
			 "Transparent-Extra-Color-1" => "Transparent-Extra-Color-1",
			 "Transparent-Extra-Color-2" => "Transparent-Extra-Color-2",	
			 "Transparent-Extra-Color-3" => "Transparent-Extra-Color-3"
		   ),
		  'save_always' => true,
		  "dependency" => array('element' => "link_style", 'value' => "nectar-button"),
		  "description" => __("Please select the color you desire", "js_composer")
		),

	  )
	));



		// Milestone
		vc_map( array(
		  "name" => __("Milestone", "js_composer"),
		  "base" => "milestone",
		  "icon" => "icon-wpb-milestone",
		  "category" => __('Nectar Elements', 'js_composer'),
		  "description" => __('Add an animated milestone', 'js_composer'),
		  "params" => array(
			array(
		      "type" => "textfield",
		      "heading" => __("Milestone Number", "js_composer"),
		      "param_name" => "number",
		      "admin_label" => false,
		      "description" => __("The number/count of your milestone e.g. \"13\"", "js_composer")
		    ),
		    array(
		      "type" => "textfield",
		      "heading" => __("Milestone Symbol", "js_composer"),
		      "param_name" => "symbol",
		      "admin_label" => false,
		      "description" => __("An optional symbol to place next to the number counted to. e.g. \"%\" or \"+\"", "js_composer")
		    ),
		    array(
			  "type" => "dropdown",
			  "heading" => __("Milestone Symbol Position", "js_composer"),
			  "param_name" => "symbol_position",
			  "value" => array(
			     "After Number" => "after",
				 "Before Number" => "before",
			   ),
			  'save_always' => true,
			  "description" => __("Please select the position you would like for your symbol.", "js_composer"),
			  "dependency" => Array('element' => "symbol", 'not_empty' => true)
			),
		    array(
		      "type" => "textfield",
		      "heading" => __("Milestone Subject", "js_composer"),
		      "param_name" => "subject",
		      "admin_label" => true,
		      "description" => __("The subject of your milestones e.g. \"Projects Completed\"", "js_composer")
		    ),
		     array(
			  "type" => "dropdown",
			  "heading" => __("Color", "js_composer"),
			  "param_name" => "color",
			  "value" => array(
			     "Default" => "Default",
				 "Accent-Color" => "Accent-Color",
				 "Extra-Color-1" => "Extra-Color-1",
				 "Extra-Color-2" => "Extra-Color-2",	
				 "Extra-Color-3" => "Extra-Color-3"
			   ),
			  'save_always' => true,
			  "description" => __("Please select the color you wish for your milestone to display in.", "js_composer")
			),

		     array(
			  "type" => "dropdown",
			  "heading" => __("Animation Effect", "js_composer"),
			  "param_name" => "effect",
			  "value" => array(
				 "Count To Value" => "count",
				 "Motion Blur Slide In" => "motion_blur"
			   ),
			  'save_always' => true,
			  "description" => __("Please select the animation you would like your milestone to have", "js_composer")
			),
		     array(
		      "type" => "textfield",
		      "heading" => __("Milestone Number Font Size", "js_composer"),
		      "param_name" => "number_font_size",
		      "admin_label" => false,
		      "description" => __("Enter your size in pixels, the default is 62.", "js_composer")
		    ),
		     array(
		      "type" => "textfield",
		      "heading" => __("Milestone Symbol Font Size", "js_composer"),
		      "param_name" => "symbol_font_size",
		      "admin_label" => false,
		      "description" => __("Enter your size in pixels.", "js_composer"),
		      "dependency" => Array('element' => "symbol", 'not_empty' => true)
		    ),
		     array(
			  "type" => "dropdown",
			  "heading" => __("Milestone Symbol Alignment", "js_composer"),
			  "param_name" => "symbol_alignment",
			  "value" => array(
			     "Default" => "Default",
				 "Superscript" => "Superscript",
			   ),
			  'save_always' => true,
			  "description" => __("Please select the alignment you desire for your symbol.", "js_composer"),
			  "dependency" => Array('element' => "symbol", 'not_empty' => true)
			),

		  )
		));



		// Google Map
		class WPBakeryShortCode_Nectar_Gmap extends WPBakeryShortCode {
		}

		vc_map( array(
		  "name" => __("Google Map", "js_composer"),
		  "base" => "nectar_gmap",
		  "icon" => "icon-wpb-map",
		  "category" => __('Nectar Elements', 'js_composer'),
		  "description" => __('Flexible Google Map', 'js_composer'),
		  "params" => array(
		    array(
		      "type" => "textfield",
		      "heading" => __("Map height", "js_composer"),
		      "param_name" => "size",
		      "description" => __('Enter map height in pixels. Example: 200. <span>As of June 2016, a Google map API key is needed to allow this element to display. Please See the Salient options panel > general settings > css/script related tab to enter this.</span>', "js_composer")
		    ),
		    array(
		      "type" => "textfield",
		      "heading" => __("Map Center Point Latitude", "js_composer"),
		      "param_name" => "map_center_lat",
		      "description" => __("Please enter the latitude for the maps center point.", "js_composer")
		    ),
		    array(
		      "type" => "textfield",
		      "heading" => __("Map Center Point Longitude", "js_composer"),
		      "param_name" => "map_center_lng",
		      "description" => __("Please enter the longitude for the maps center point.", "js_composer")
		    ),
		    
		  	array(
		      "type" => "dropdown",
		      "heading" => __("Map Zoom", "js_composer"),
		      "param_name" => "zoom",
		      'save_always' => true,
		      "value" => array(__("14 - Default", "js_composer") => 14, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20)
		    ),
		    array(
		      "type" => 'checkbox',
		      "heading" => __("Eanble Zoom In/Out", "js_composer"),
		      "param_name" => "enable_zoom",
		      "description" => __("Do you want users to be able to zoom in/out on the map?", "js_composer"),
		      "value" => Array(__("Yes, please", "js_composer") => true),
		    ),
		    
		    array(
		      "type" => "attach_image",
		      "heading" => __("Marker Image", "js_composer"),
		      "param_name" => "marker_image",
		      "value" => "",
		      "description" => __("Select image from media library.", "js_composer")
		    ),
		    array(
		      "type" => 'checkbox',
		      "heading" => __("Marker Animation", "js_composer"),
		      "param_name" => "marker_animation",
		      "description" => __("This will cause your markers to do a quick bounce as they load in.", "js_composer"),
		      "value" => Array(__("Yes, please", "js_composer") => true),
		    ),
		    
		    array(
		      "type" => 'checkbox',
		      "heading" => __("Greyscale Color", "js_composer"),
		      "param_name" => "map_greyscale",
		      "description" => __("Toggle a greyscale color scheme (will also unlock further custom options)", "js_composer"),
		      "value" => Array(__("Yes, please", "js_composer") => true),
		    ),
		    array(
				"type" => "colorpicker",
				"class" => "",
				"heading" => "Map Extra Color",
				"param_name" => "map_color",
				"value" => "",
				"dependency" => Array('element' => "map_greyscale", 'not_empty' => true),
				"description" => "Use this to define a main color that will be used in combination with the greyscale option for your map"
			),
			array(
		      "type" => 'checkbox',
		      "heading" => __("Ultra Flat Map", "js_composer"),
		      "param_name" => "ultra_flat",
		      "dependency" => Array('element' => "map_greyscale", 'not_empty' => true),
		      "description" => __("This removes street/landmark text & some extra details for a clean look", "js_composer"),
		      "value" => Array(__("Yes, please", "js_composer") => true),
		    ),
		    array(
		      "type" => 'checkbox',
		      "heading" => __("Dark Color Scheme", "js_composer"),
		      "param_name" => "dark_color_scheme",
		      "dependency" => Array('element' => "map_greyscale", 'not_empty' => true),
		      "description" => __("Enable this option for a dark colored map (This will override the extra color choice)", "js_composer"),
		      "value" => Array(__("Yes, please", "js_composer") => true),
		    ),
			
		    array(
		      "type" => "textarea",
		      "heading" => __("Map Marker Locations", "js_composer"),
		      "param_name" => "map_markers",
		      "description" => __("Please enter the the list of locations you would like with a latitude|longitude|description format. <br/> Divide values with linebreaks (Enter). Example: <br/> 39.949|-75.171|Our Location <br/> 40.793|-73.954|Our Location #2", "js_composer")
		    ),
		    
		  )
		));







	// Team Member
		vc_map( array(
		  "name" => __("Team Member", "js_composer"),
		  "base" => "team_member",
		  "icon" => "icon-wpb-team-member",
		  "category" => __('Nectar Elements', 'js_composer'),
		  "description" => __('Add a team member element', 'js_composer'),
		  "params" => array(
		 	 array(
		      "type" => "fws_image",
		      "heading" => __("Image", "js_composer"),
		      "param_name" => "image_url",
		      "value" => "",
		      "description" => __("Select image from media library.", "js_composer")
		    ),
		 	 array(
		      "type" => "fws_image",
		      "heading" => __("Bio Image", "js_composer"),
		      "param_name" => "bio_image_url",
		      "value" => "",
		       "dependency" => Array('element' => "team_memeber_style", 'value' => array('bio_fullscreen')),
		      "description" => __("<i>Image Size Guidelines</i>  <br/>  <strong>Bio Image:</strong> large with a portrait aspect ratio - will be shown at the full screen height at 50% of the page width. <br/> <strong>Team Small Image:</strong> Will display at 500x500 so ensure the image you're uploading is at least that size.", "js_composer")
		    ),
		    array(
			  "type" => "dropdown",
			  "heading" => __("Team Member Stlye", "js_composer"),
			  "param_name" => "team_memeber_style",
			  "value" => array(
				 "Meta below" => "meta_below",
				 "Meta overlaid" => "meta_overlaid",
				 "Meta overlaid alt" => "meta_overlaid_alt",
				 "Bio Shown Fullscreen Modal" => "bio_fullscreen"
			   ),
			  'save_always' => true,
			  "description" => __("Please select the style you desire for your team member.", "js_composer")
			),
		    array(
		      "type" => "textfield",
		      "heading" => __("Name", "js_composer"),
		      "param_name" => "name",
		      "admin_label" => true,
		      "description" => __("Please enter the name of your team member", "js_composer")
		    ),
			array(
		      "type" => "textfield",
		      "heading" => __("Job Position", "js_composer"),
		      "param_name" => "job_position",
		      "admin_label" => true,
		      "description" => __("Please enter the job position for your team member", "js_composer")
		    ),
		    array(
		      "type" => "textarea",
		      "heading" => __("Team Member Bio", "js_composer"),
		      "param_name" => "team_member_bio",
		      "description" => __("The main text portion of your team member", "js_composer"),
		      "dependency" => Array('element' => "team_memeber_style", 'value' => array('bio_fullscreen'))
		    ),
		    array(
		      "type" => "textarea",
		      "heading" => __("Description", "js_composer"),
		      "param_name" => "description",
		      "description" => __("The main text portion of your team member", "js_composer"),
		      "dependency" => Array('element' => "team_memeber_style", 'value' => array('meta_below'))
		    ),
		    array(
		      "type" => "textarea",
		      "heading" => __("Social Media", "js_composer"),
		      "param_name" => "social",
		      "dependency" => Array('element' => "team_memeber_style", 'value' => array('meta_below')),
		      "description" => __("Enter any social media links with a comma separated list. e.g. Facebook,http://facebook.com, Twitter,http://twitter.com", "js_composer")
		    ),
		    array(
			  "type" => "dropdown",
			  "heading" => __("Team Member Link Type", "js_composer"),
			  "param_name" => "link_element",
			  "value" => array(
				 "None" => "none",
				 "Image" => "image",
				 "Name" => "name",	
				 "Both" => "both"
			   ),
			  'save_always' => true,
			   "dependency" => Array('element' => "team_memeber_style", 'value' => array('meta_below')),
			  "description" => __("Please select how you wish to link your team member to an arbitrary URL", "js_composer")
			),
			array(
		      "type" => "textfield",
		      "heading" => __("Team Member Link URL", "js_composer"),
		      "param_name" => "link_url",
		      "admin_label" => false,
		      "description" => __("Please enter the URL for your team member link", "js_composer"),
		      "dependency" => Array('element' => "link_element", 'value' => array('image', 'name', 'both'))
		    ),
		    array(
		      "type" => "textfield",
		      "heading" => __("Team Member Link URL", "js_composer"),
		      "param_name" => "link_url_2",
		      "admin_label" => false,
		      "description" => __("Please enter the URL for your team member link", "js_composer"),
		      "dependency" => Array('element' => "team_memeber_style", 'value' => array('meta_overlaid','meta_overlaid_alt')),
		    ),
		     array(
			  "type" => "dropdown",
			  "heading" => __("Link Color", "js_composer"),
			  "param_name" => "color",
			  "value" => array(
				 "Accent-Color" => "Accent-Color",
				 "Extra-Color-1" => "Extra-Color-1",
				 "Extra-Color-2" => "Extra-Color-2",	
				 "Extra-Color-3" => "Extra-Color-3"
			   ),
			  'save_always' => true,
			   "dependency" => Array('element' => "team_memeber_style", 'value' => array('meta_below')),
			  "description" => __("Please select the color you wish for your social links to display in.", "js_composer")
			)
		  )
		));




	// Fancy Box
	class WPBakeryShortCode_Fancy_Box extends WPBakeryShortCode { }
		vc_map( array(
		  "name" => __("Fancy Box", "js_composer"),
		  "base" => "fancy_box",
		  "icon" => "icon-wpb-fancy-box",
		  "category" => __('Nectar Elements', 'js_composer'),
		  "description" => __('Add a fancy box element', 'js_composer'),
		  "params" => array(
		 	 array(
		      "type" => "fws_image",
		      "heading" => __("Image", "js_composer"),
		      "param_name" => "image_url",
		      "value" => "",
		      "description" => __("Select a background image from the media library.", "js_composer")
		    ),
		    array(
		      "type" => "textarea_html",
		      "heading" => __("Box Content", "js_composer"),
		      "param_name" => "content",
		      "admin_label" => true,
		      "description" => __("Please enter the text desired for your box", "js_composer")
		    ),
		    array(
		      "type" => "textfield",
		      "heading" => __("Link URL", "js_composer"),
		      "param_name" => "link_url",
		      "admin_label" => false,
		      "description" => __("Please enter the URL you would like for your box to link to", "js_composer")
		    ),
		    array(
		       "type" => "checkbox",
			  "class" => "",
			  "heading" => "Open Link In New Tab",
			  "value" => array("Yes, please" => "true" ),
			  "param_name" => "link_new_tab",
			  "description" => "",
		       "dependency" => Array('element' => "link_url", 'not_empty' => true)
		    ),
		     array(
		      "type" => "textfield",
		      "heading" => __("Link Text", "js_composer"),
		      "param_name" => "link_text",
		      "admin_label" => false,
		      "description" => __("Please enter the text that will be displayed for your box link", "js_composer")
		    ),
		     array(
		      "type" => "textfield",
		      "heading" => __("Min Height", "js_composer"),
		      "param_name" => "min_height",
		      "admin_label" => false,
		      "description" => __("Please enter the minimum height you would like for you box. Enter in number of pixels - Don't enter \"px\", default is \"300\"", "js_composer")
		    ),
		     array(
			  "type" => "dropdown",
			  "heading" => __("Link Color", "js_composer"),
			  "param_name" => "color",
			  "value" => array(
				 "Accent-Color" => "Accent-Color",
				 "Extra-Color-1" => "Extra-Color-1",
				 "Extra-Color-2" => "Extra-Color-2",	
				 "Extra-Color-3" => "Extra-Color-3"
			   ),
			  'save_always' => true,
			  "description" => __("Please select the accent color for your box", "js_composer")
			)
		  )
		));
	
	


	// Flip Box
	class WPBakeryShortCode_Nectar_Flip_Box extends WPBakeryShortCode { }
		vc_map( array(
		  "name" => __("Flip Box", "js_composer"),
		  "base" => "nectar_flip_box",
		  "icon" => "icon-wpb-nectar-flip-box",
		  "category" => __('Nectar Elements', 'js_composer'),
		  "description" => __('Add a flip box element', 'js_composer'),
		  "params" => array(
		  	array(
		      "type" => "textarea",
		      "heading" => __("Front Box Content", "js_composer"),
		      "param_name" => "front_content",
		      "description" => __("The text that will display on the front of your flip box", "js_composer"),
		      "group" => 'Front Side'
		    ),
		 	  array(
		      "type" => "fws_image",
		      "heading" => __("Background Image", "js_composer"),
		      "param_name" => "image_url_1",
		      "value" => "",
		      "group" => 'Front Side',
		      "description" => __("Select a background image from the media library.", "js_composer")
		    ),
		 	array(
				"type" => "colorpicker",
				"class" => "",
				"heading" => "Background Color",
				"group" => 'Front Side',
				"param_name" => "bg_color",
				"value" => "",
				"description" => ""
			),
			 array(
		      "type" => 'checkbox',
		      "heading" => __("BG Color overlay on BG Image", "js_composer"),
		      "param_name" => "bg_color_overlay",
		      "group" => 'Front Side',
		      "description" => __("Checking this will overlay your BG color on your BG image", "js_composer"),
		      "value" => Array(__("Yes", "js_composer") => 'true')
		    ),
			 array(
				"type" => "dropdown",
				"class" => "",
				"group" => 'Front Side',
				"heading" => "Text Color",
				"param_name" => "text_color",
				"value" => array(
					"Dark" => "dark",
					"Light" => "light"
				),
				'save_always' => true
			),	 
			 array(
				'type' => 'dropdown',
				'heading' => __( 'Icon library', 'js_composer' ),
				"group" => 'Front Side',
				'value' => array(
					__( 'Font Awesome', 'js_composer' ) => 'fontawesome',
					__( 'Iconsmind', 'js_composer' ) => 'iconsmind',
					__( 'Linea', 'js_composer' ) => 'linea',
					__( 'Steadysets', 'js_composer' ) => 'steadysets',
				),
				'param_name' => 'icon_family',
				'description' => __( 'Select icon library.', 'js_composer' ),
			),
			array(
		      "type" => "iconpicker",
		      "heading" => __("Icon Above Title", "js_composer"),
		      "param_name" => "icon_fontawesome",
		      "group" => 'Front Side',
		      "settings" => array( "emptyIcon" => true, "iconsPerPage" => 4000),
		      "dependency" => Array('element' => "icon_family", 'value' => 'fontawesome'),
		      "description" => __("Select icon from library.", "js_composer")
		    ),
		    array(
		      "type" => "iconpicker",
		      "heading" => __("Icon", "js_composer"),
		      "param_name" => "icon_iconsmind",
		      "group" => 'Front Side',
		      "settings" => array( 'type' => 'iconsmind', 'emptyIcon' => false, "iconsPerPage" => 4000),
		      "dependency" => array('element' => "icon_family", 'value' => 'iconsmind'),
		      "description" => __("Select icon from library.", "js_composer")
		    ),
		    array(
		      "type" => "iconpicker",
		      "heading" => __("Icon Above Title", "js_composer"),
		      "param_name" => "icon_linea",
		      "group" => 'Front Side',
		      "settings" => array( 'type' => 'linea', "emptyIcon" => true, "iconsPerPage" => 4000),
		      "dependency" => Array('element' => "icon_family", 'value' => 'linea'),
		      "description" => __("Select icon from library.", "js_composer")
		    ),
		    array(
		      "type" => "iconpicker",
		      "heading" => __("Icon", "js_composer"),
		      "param_name" => "icon_steadysets",
		      "group" => 'Front Side',
		      "settings" => array( 'type' => 'steadysets', 'emptyIcon' => false, "iconsPerPage" => 4000),
		      "dependency" => array('element' => "icon_family", 'value' => 'steadysets'),
		      "description" => __("Select icon from library.", "js_composer")
		    ),
		    array(
				"type" => "dropdown",
				"class" => "",
				'save_always' => true,
				"heading" => "Icon Color",
				"param_name" => "icon_color",
				"group" => 'Front Side',
				"value" => array(
					"Accent-Color" => "Accent-Color",
					"Extra-Color-1" => "Extra-Color-1",
					"Extra-Color-2" => "Extra-Color-2",	
					"Extra-Color-3" => "Extra-Color-3",
					"Extra-Color-Gradient-1" => "extra-color-gradient-1",
			 		"Extra-Color-Gradient-2" => "extra-color-gradient-2"
				),
				"description" => ""
			),
			array(
		      "type" => "textfield",
		      "group" => 'Front Side',
		      "heading" => __("Icon Size", "js_composer"),
		      "param_name" => "icon_size",
		      "description" => __("Please enter the size for your icon. Enter in number of pixels - Don't enter \"px\", default is \"60\"", "js_composer"),
		      "group" => 'Front Side'
		    ),
			array(
		      "type" => "textarea_html",
		      "heading" => __("Back Box Content", "js_composer"),
		      "param_name" => "content",
		      "admin_label" => true,
		      "group" => 'Back Side',
		      "description" => __("The content that will display on the back of your flip box", "js_composer")
		    ),	
		     array(
		      "type" => "fws_image",
		      "heading" => __("Background Image", "js_composer"),
		      "param_name" => "image_url_2",
		      "value" => "",
		      "group" => 'Back Side',
		      "description" => __("Select a background image from the media library.", "js_composer")
		    ),
		     array(
				"type" => "colorpicker",
				"class" => "",
				"heading" => "Background Color",
				"group" => 'Back Side',
				"param_name" => "bg_color_2",
				"value" => "",
				"description" => ""
			),
		     array(
		      "type" => 'checkbox',
		      "heading" => __("BG Color overlay on BG Image", "js_composer"),
		      "param_name" => "bg_color_overlay_2",
		      "group" => 'Back Side',
		      "description" => __("Checking this will overlay your BG color on your BG image", "js_composer"),
		      "value" => Array(__("Yes", "js_composer") => 'true')
		    ),
		     array(
				"type" => "dropdown",
				"class" => "",
				"group" => 'Back Side',
				"heading" => "Text Color",
				"param_name" => "text_color_2",
				"value" => array(
					"Dark" => "dark",
					"Light" => "light"
				),
				'save_always' => true
			), 
		     array(
		      "type" => "textfield",
		      "heading" => __("Min Height", "js_composer"),
		      "param_name" => "min_height",
		      "admin_label" => false,
		      "group" => 'General Settings',
		      "description" => __("Please enter the minimum height you would like for you box. Enter in number of pixels - Don't enter \"px\", default is \"300\"", "js_composer")
		    ),

			array(
				"type" => "dropdown",
				"class" => "",
				'save_always' => true,
				"heading" => "Horizontal Content Alignment",
				"param_name" => "h_text_align",
				"group" => 'General Settings',
				"value" => array(
					"Left" => "left",
					"Center" => "center",
					"Right" => "right"
				)
			),
			array(
				"type" => "dropdown",
				"class" => "",
				'save_always' => true,
				"heading" => "Vertical Content Alignment",
				"param_name" => "v_text_align",
				"group" => 'General Settings',
				"value" => array(
					"Top" => "top",
					"Center" => "center",
					"Bottom" => "bottom"
				)
			),

			array(
				"type" => "dropdown",
				"class" => "",
				'save_always' => true,
				"heading" => "Flip Direction",
				"param_name" => "flip_direction",
				"group" => 'General Settings',
				"value" => array(
					"Horizontal To Left" => "horizontal-to-left",
					"Horizontal To Right" => "horizontal-to-right",
					"Vertical To Bottom" => "vertical-to-bottom",
					"Vertical To Top" => "vertical-to-top"
				)
			),
			 /*array(
		      "type" => "dropdown",
		      "heading" => __("Box Shadow", "js_composer"),
		      'save_always' => true,
		      "param_name" => "box_shadow",
		      "group" => 'General Settings',
		      "value" => array(__("None", "js_composer") => "none", _("Light Visibility", "js_composer") => "light_visibility", _("Heavy Visibility", "js_composer") => "heavy_visibility"),
		      "description" => __("Select your desired image box shadow", "js_composer")
		    )*/
		  )
		));

	// Gradient Text
	class WPBakeryShortCode_Nectar_Gradient_Text extends WPBakeryShortCode { }
		vc_map( array(
		  "name" => __("Gradient Text", "js_composer"),
		  "base" => "nectar_gradient_text",
		  "icon" => "icon-wpb-nectar-gradient-text",
		  "category" => __('Nectar Elements', 'js_composer'),
		  "description" => __('Add text with gradient coloring', 'js_composer'),
		  "params" => array(
		  	array(
			"type" => "dropdown",
			"class" => "",
			'save_always' => true,
			"heading" => "Heading Tag",
			"param_name" => "heading_tag",
			"value" => array(
				"H1" => "h1",
				"H2" => "h2",
				"H3" => "h3",
				"H4" => "h4",
				"H5" => "h5",
				"H6" => "h6"
			)),
		    array(
				"type" => "dropdown",
				"class" => "",
				'save_always' => true,
				"heading" => "Text Color",
				"param_name" => "color",
				"admin_label" => false,
				"value" => array(
					"Extra-Color-Gradient-1" => "extra-color-gradient-1",
			 		"Extra-Color-Gradient-2" => "extra-color-gradient-2"
				),
				"description" => "Will fallback to the first color of the gardient on non webkit browsers"
			),
			array(
				"type" => "dropdown",
				"class" => "",
				'save_always' => true,
				"heading" => "Gradient Direction",
				"param_name" => "gradient_direction",
				"admin_label" => false,
				"value" => array(
					"Horizontal" => "horizontal",
			 		"Diagonal" => "diagonal"
				),
				"description" => "Select your desired gradient direction"
			),
			array(
		      "type" => "textarea",
		      "heading" => __("Text Content", "js_composer"),
		      "param_name" => "text",
		      "admin_label" => true,
		      "description" => __("The text that will display with gradient coloring", "js_composer")
		    ),
		    array(
		      "type" => "textfield",
		      "heading" => __("Margin <span>Top</span>", "js_composer"),
		      "param_name" => "margin_top",
		      "edit_field_class" => "col-md-2",
		      "description" => __("." , "js_composer")
		    ),
			 array(
		      "type" => "textfield",
		      "heading" => __("<span>Right</span>", "js_composer"),
		      "param_name" => "margin_right",
		      "edit_field_class" => "col-md-2",
		      "description" => ''
		    ),
			array(
		      "type" => "textfield",
		      "heading" => __("<span>Bottom</span>", "js_composer"),
		      "param_name" => "margin_bottom",
		      "edit_field_class" => "col-md-2",
		      "description" => ''
		    ),
		    array(
		      "type" => "textfield",
		      "heading" => __("<span>Left</span>", "js_composer"),
		      "param_name" => "margin_left",
		      "edit_field_class" => "col-md-2",
		      "description" => ''
		    ),
		 	 
		  )
		));
	
	
	// Hotspot
	class WPBakeryShortCode_Nectar_Image_With_Hotspots extends WPBakeryShortCode { }

		vc_map( array(
		  "name" => __("Image With Hotspots", "js_composer"),
		  "base" => "nectar_image_with_hotspots",
		  "weight" => 2,
		  "icon" => "icon-wpb-single-image",
		  "category" => __('Nectar Elements', 'js_composer'),
		  "description" => __('Add Hotspots On Your Image', 'js_composer'),
		  "params" => array(

		  	array(
				"type" => "attach_image",
				"class" => "",
				"heading" => "Image",
				"value" => "",
				"param_name" => "image",
				"description" => "Choose your image that will show the hotspots. <br/> You can then click on the image in the preview area to add your hotspots in the desired locations."
			),
			array(
		      "type" => "hotspot_image_preview",
		      "heading" => __("Preview", "js_composer"),
		      "param_name" => "preview",
		      "description" => __("Click to add - Drag to move - Edit content below <br/><br/> Note: this preview will not reflect hotspot style choices or show tooltips. <br/>This is only used as a visual guide for positioning. <br/><strong>Requires Salient VC 4.12 or higher</strong>", "js_composer"),
		      "value" => ''
		    ),	
			 array(
		      "type" => "textarea_html",
		      "heading" => __("Hotspots", "js_composer"),
		      "param_name" => "content",
		      "description" => '',
		    ),	 

			array(
			"type" => "dropdown",
			"class" => "",
			'save_always' => true,
			"group" => "Style",
			"heading" => "Color",
			"admin_label" => true,
			"param_name" => "color_1",
			"description" => __("Choose the color which the hotspot will use", "js_composer"),
			/*"dependency" => array('element' => "style", 'value' => 'color_pulse'),*/
			"value" => array(
				"Accent-Color" => "Accent-Color",
				"Extra-Color-1" => "Extra-Color-1",
				"Extra-Color-2" => "Extra-Color-2",	
				"Extra-Color-3" => "Extra-Color-3"
			)),
			array(
			"type" => "dropdown",
			"class" => "",
			'save_always' => true,
			"group" => "Style",
			"heading" => "Hotspot Icon",
			"description" => __("The icon that will be shown on the hotspots", "js_composer"),
			"param_name" => "hotspot_icon",
			"admin_label" => true,
			"value" => array(
				"Plus Sign" => "plus_sign",
				"Numerical" => "numerical"
			)),
			array(
			"type" => "dropdown",
			"class" => "",
			'save_always' => true,
			"group" => "Style",
			"heading" => "Tooltip Functionality",
			"param_name" => "tooltip",
			"description" => __("Select how you want your tooltips to display to the user", "js_composer"),
			"value" => array(
				"Show On Hover" => "hover",
				"Show On Click" => "click",
				"Always Show" => "always_show"
			)),
			array(
			"type" => "dropdown",
			"class" => "",
			'save_always' => true,
			"group" => "Style",
			"heading" => "Tooltip Shadow",
			"param_name" => "tooltip_shadow",
			"description" => __("Select the shadow size for your tooltip", "js_composer"),
			"value" => array(__("None", "js_composer") => "none", __("Small Depth", "js_composer") => "small_depth", __("Medium Depth", "js_composer") => "medium_depth", __("Large Depth", "js_composer") => "large_depth"),
			),
			array(
		      "type" => 'checkbox',
		      "heading" => __("Enable Animation", "js_composer"),
		      "param_name" => "animation",
		      "group" => "Style",
		      "description" => __("Turning this on will make your hotspots animate in when the user scrolls to the element", "js_composer"),
		      "value" => Array(__("Yes, please", "js_composer") => 'true')
		    )
		  )
		));
	

		class WPBakeryShortCode_Nectar_Hotspot extends WPBakeryShortCode { }

		vc_map( array(
		  "name" => __("Nectar Hotspot", "js_composer"),
		  "base" => "nectar_hotspot",
		  "allowed_container_element" => 'vc_row',
		  "content_element" => false,
		  "params" => array(
		    array(
			"type" => "dropdown",
			"class" => "",
			'save_always' => true,
			"heading" => "Position",
			"param_name" => "position",
			"value" => array(
				"top" => "top",
				"right" => "right",
				"bottom" => "bottom",
				"left" => "left",
			)),
		    array(
		      "type" => "textfield",
		      "heading" => __("Left", "js_composer"),
		      "param_name" => "left"
		    ),
		    array(
		      "type" => "textfield",
		      "heading" => __("Top", "js_composer"),
		      "param_name" => "top"
		    ),
		    array(
		      "type" => "textarea_html",
		      "heading" => __("Content", "js_composer"),
		      "param_name" => "content",
		      "description" => '',
		    )
		  )
		
		));


	// Fancy Title
	class WPBakeryShortCode_Nectar_Animated_Title extends WPBakeryShortCode { }
		vc_map( array(
		  "name" => __("Animated Title", "js_composer"),
		  "base" => "nectar_animated_title",
		  "icon" => "icon-wpb-nectar-gradient-text",
		  "category" => __('Nectar Elements', 'js_composer'),
		  "description" => __('Add a title with animation', 'js_composer'),
		  "params" => array(
		  	array(
			"type" => "dropdown",
			"class" => "",
			'save_always' => true,
			"heading" => "Heading Tag",
			"param_name" => "heading_tag",
			"value" => array(
				"H6" => "h6",
				"H5" => "h5",
				"H4" => "h4",
				"H3" => "h3",
				"H2" => "h2",
				"H1" => "h1"
			)),
			array(
				"type" => "dropdown",
				"class" => "",
				'save_always' => true,
				"heading" => "Title Style",
				"param_name" => "style",
				"admin_label" => false,
				"value" => array(
					"Color Strip Reveal" => "color-strip-reveal",
					"Hinge Drop" => "hinge-drop",
				),
				"description" => "Gradient colors are only available for compatible effects"
			),
		    array(
				"type" => "dropdown",
				"class" => "",
				'save_always' => true,
				"heading" => "Background Color",
				"param_name" => "color",
				"admin_label" => false,
				"value" => array(
					"Accent-Color" => "Accent-Color",
					"Extra-Color-1" => "Extra-Color-1",
					"Extra-Color-2" => "Extra-Color-2",	
					"Extra-Color-3" => "Extra-Color-3"
				),
				"description" => ""
			),
			array(
				"type" => "colorpicker",
				"class" => "",
				"heading" => "Text Color",
				"param_name" => "text_color",
				"value" => "#ffffff",
				"description" => "Select the color your text will display in"
			),
			array(
		      "type" => "textfield",
		      "heading" => __("Text Content", "js_composer"),
		      "param_name" => "text",
		      "admin_label" => true,
		      "description" => __("Enter your fancy title text here", "js_composer")
		    )
		 	 
		  )
		));

	require_once vc_path_dir('SHORTCODES_DIR', 'vc-accordion.php');
	require_once vc_path_dir('SHORTCODES_DIR', 'vc-accordion-tab.php');

	/* Accordion block
	---------------------------------------------------------- */
	vc_map( array(
	  "name" => __("Toggle Panels", "js_composer"),
	  "base" => "toggles",
	  "show_settings_on_create" => false,
	  "is_container" => true,
	  "icon" => "icon-wpb-ui-accordion",
	  "category" => __('Nectar Elements', 'js_composer'),
	  "description" => __('jQuery toggles/accordion', 'js_composer'),
	  "params" => array(
	  	array(
		  "type" => "dropdown",
		  "heading" => __("Style", "js_composer"),
		  "param_name" => "style",
		  "admin_label" => true,
		  "value" => array(
			 "Default" => "default",
			 "Minimal" => "minimal",
		   ),
		  'save_always' => true,
		  "description" => __("Please select the style you desire for your toggle element.", "js_composer")
		),
	    array(
	      "type" => 'checkbox',
	      "heading" => __("Allow collapsible all", "js_composer"),
	      "param_name" => "accordion",
	      "description" => __("Select checkbox to turn the toggles in an accordion.", "js_composer"),
	      "value" => Array(__("Allow", "js_composer") => 'true')
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
	  [toggle title="'.__('Section', "js_composer").'"][/toggle]
	  [toggle title="'.__('Section', "js_composer").'"][/toggle]
	  ',
	  'js_view' => 'VcAccordionView'
	));
	vc_map( array(
	  "name" => __("Section", "js_composer"),
	  "base" => "toggle",
	  "allowed_container_element" => 'vc_row',
	  "is_container" => true,
	  "content_element" => false,
	  "params" => array(
	    array(
	      "type" => "textfield",
	      "heading" => __("Title", "js_composer"),
	      "param_name" => "title",
	      "description" => __("Accordion section title.", "js_composer")
	    ),
	     array(
		  "type" => "dropdown",
		  "heading" => __("Color", "js_composer"),
		  "param_name" => "color",
		  "admin_label" => true,
		  "value" => array(
		     "Default" => "Default",
			 "Accent-Color" => "Accent-Color",
			 "Extra-Color-1" => "Extra-Color-1",
			 "Extra-Color-2" => "Extra-Color-2",	
			 "Extra-Color-3" => "Extra-Color-3"
		   ),
		  'save_always' => true,
		  "description" => __("Please select the color you wish for your toggle to display in.", "js_composer")
		)
	  ),
	  'js_view' => 'VcAccordionTabView'
	) );





	require_once vc_path_dir('SHORTCODES_DIR', 'vc-tabs.php');

	/* Tabs
	---------------------------------------------------------- */
	$tab_id_1 = time().'-1-'.rand(0, 100);
	$tab_id_2 = time().'-2-'.rand(0, 100);
	vc_map( array(
	  "name"  => __("Tabs", "js_composer"),
	  "base" => "tabbed_section",
	  "show_settings_on_create" => false,
	  "is_container" => true,
	  "icon" => "icon-wpb-ui-tab-content",
	  "category" => __('Nectar Elements', 'js_composer'),
	  "description" => __('Tabbed content', 'js_composer'),
	  "params" => array(
	  	 array(
		  "type" => "dropdown",
		  "heading" => __("Style", "js_composer"),
		  "param_name" => "style",
		  "admin_label" => true,
		  "value" => array(
			 "Default" => "default",
			 "Minimal" => "minimal",
			 "Vertical" => "vertical"
		   ),
		  'save_always' => true,
		  "description" => __("Please select the style you desire for your tabbed element.", "js_composer")
		),
	  	 array(
		  "type" => "dropdown",
		  "heading" => __("Alignment", "js_composer"),
		  "param_name" => "alignment",
		  "admin_label" => false,
		  "value" => array(
			 "Left" => "left",
			 "Center" => "center",
			 "Right" => "right"
		   ),
		  'save_always' => true,
		  "dependency" => Array('element' => "style", 'value' => array('minimal','default')),
		  "description" => __("Please select your tabbed alignment", "js_composer")
		),

	  	array(
	      "type" => "textfield",
	      "heading" => __("Optional CTA button", "js_composer"),
	      "param_name" => "cta_button_text",
	      "description" => __("If you wish to include an optional CTA button on your tabbed nav, enter the text here", "js_composer"),
	       "admin_label" => false,
	      "dependency" => Array('element' => "style", 'value' => array('minimal'))
	    ),
	    array(
	      "type" => "textfield",
	      "heading" => __("CTA button link", "js_composer"),
	      "param_name" => "cta_button_link",
	      "description" => __("Enter a URL for your button link here", "js_composer"),
	       "admin_label" => false,
	      "dependency" => Array('element' => "style", 'value' => array('minimal'))
	    ),
	     array(
		  "type" => "dropdown",
		  "heading" => __("CTA Button Color", "js_composer"),
		  "param_name" => "cta_button_style",
		  "admin_label" => false,
		  "value" => array(
			 "Accent-Color" => "accent-color",
			 "Extra-Color-1" => "extra-color-1",
			 "Extra-Color-2" => "extra-color-2",
			 "Extra-Color-3" => "extra-color-3",
		   ),
		  'save_always' => true,
		  "description" => __("Please select the style for your optional CTA button", "js_composer"),
		   "dependency" => Array('element' => "style", 'value' => array('minimal'))
		),


	    array(
	      "type" => "textfield",
	      "heading" => __("Extra class name", "js_composer"),
	      "param_name" => "el_class",
	      "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "js_composer")
	    )
	  ),
	  "custom_markup" => '
	  <div class="wpb_tabs_holder wpb_holder vc_container_for_children">
	  <ul class="tabs_controls">
	  </ul>
	  %content%
	  </div>'
	  ,
	  'default_content' => '
	  [tab title="'.__('Tab','js_composer').'" id="'.$tab_id_1.'"] I am text block. Click edit button to change this text. [/tab]
	  [tab title="'.__('Tab','js_composer').'" id="'.$tab_id_2.'"] I am text block. Click edit button to change this text. [/tab]
	  ',
	  "js_view" => ($vc_is_wp_version_3_6_more ? 'VcTabsView' : 'VcTabsView35')
	));


	vc_map( array(
	  "name" => __("Tab", "js_composer"),
	  "base" => "tab",
	  "allowed_container_element" => 'vc_row',
	  "is_container" => true,
	  "content_element" => false,
	  "params" => array(
	    array(
	      "type" => "textfield",
	      "heading" => __("Title", "js_composer"),
	      "param_name" => "title",
	      "description" => __("Tab title.", "js_composer")
	    ),
	    array(
	      "type" => "tab_id",
	      "heading" => __("Tab ID", "js_composer"),
	      "param_name" => "id"
	    )
	  ),
	  'js_view' => ($vc_is_wp_version_3_6_more ? 'VcTabView' : 'VcTabView35')
	));




	class WPBakeryShortCode_Testimonial_Slider extends WPBakeryShortCode_Tabbed_Section { }

	$tab_id_1 = time().'-1-'.rand(0, 100);
	$tab_id_2 = time().'-2-'.rand(0, 100);
	vc_map( array(
	  "name"  => __("Testiomonial Slider", "js_composer"),
	  "base" => "testimonial_slider",
	  "show_settings_on_create" => false,
	  "is_container" => true,
	  "icon" => "icon-wpb-testimonial-slider",
	  "category" => __('Nectar Elements', 'js_composer'),
	  "description" => __('An appealing testmonial slider.', 'js_composer'),
	  "params" => array(
	  	 array(
		  "type" => "dropdown",
		  "heading" => __("Style", "js_composer"),
		  "param_name" => "style",
		  "admin_label" => false,
		  "value" => array(
			 "Basic (Default)" => "default",
			 "Minimal" => "minimal",
			 "Multiple Visible" => "multiple_visible",
		   ),
		  'save_always' => true,
		  "description" => __("Please select the style for your testimonial slider", "js_composer")
		),
	  	array(
		  "type" => "dropdown",
		  "heading" => __("Color", "js_composer"),
		  "param_name" => "color",
		  "admin_label" => false,
		  "value" => array(
			 "Inherit (Default)" => "default",
			 "Accent Color + Light Text" => "accent-color-light",
			 "Extra Color 1 + Light Text" => "extra-color-1-light",
			 "Extra Color 2 + Light Text" => "extra-color-2-light",
			 "Extra Color 3 + Light Text" => "extra-color-3-light",
			 "Accent Color + Dark Text" => "accent-color-dark",
			 "Extra Color 1 + Dark Text" => "extra-color-1-dark",
			 "Extra Color 2 + Dark Text" => "extra-color-2-dark",
			 "Extra Color 3 + Dark Text" => "extra-color-3-dark"
		   ),
		  'save_always' => true,
		  "dependency" => Array('element' => "style", 'value' => array('multiple_visible')),
		  "description" => __("Please select the color you would like for your testimonial slider. <br/> The Inherit value will react based on the row Text Color when set to light or dark.", "js_composer")
		),
	    array(
	      "type" => "textfield",
	      "heading" => __("Auto rotate?", "js_composer"),
	      "param_name" => "autorotate",
	      "value" => '',
	      "description" => __("If you would like this to autorotate, enter the rotation speed in miliseconds here. i.e 5000", "js_composer")
	    ),
	    array(
	      "type" => "checkbox",
		  "class" => "",
		  "heading" => "Disable height animation?",
		  "value" => array("Yes, please" => "true" ),
		  "param_name" => "disable_height_animation",
		   "dependency" => Array('element' => "style", 'value' => array('default','minimal')),
		  "description" => "Your testimonial slider will animate the height of itself to match the height of the testimonial being shown - this will remove that and simply set the height equal to the tallest testimonial to allow your content below to remain stagnant instead of moving up/down."
	    )
	  ),
	  "custom_markup" => '
	  <div class="wpb_tabs_holder wpb_holder vc_container_for_children">
	  <ul class="tabs_controls">
	  </ul>
	  %content%
	  </div>'
	  ,
	  'default_content' => '
	  [testimonial title="'.__('Testimonial','js_composer').'" id="'.$tab_id_1.'"] Click the edit button to add your testimonial. [/testimonial]
	  [testimonial title="'.__('Testimonial','js_composer').'" id="'.$tab_id_2.'"] Click the edit button to add your testimonial. [/testimonial]
	  ',
	  "js_view" => ($vc_is_wp_version_3_6_more ? 'VcTabsView' : 'VcTabsView35')
	));


	class WPBakeryShortCode_Testimonial extends WPBakeryShortCode {
		
		public function customAdminBlockParams() {
	        return ' id="tab-'.$this->atts['id'] .'"';
	    }
		
	}



	vc_map( array(
	  "name" => __("Testimonial", "js_composer"),
	  "base" => "testimonial",
	  "allowed_container_element" => 'vc_row',
	  "is_container" => true,
	  "content_element" => false,
	  "params" => array(
	  	array(
			"type" => "attach_image",
			"class" => "",
			"heading" => "Image",
			"value" => "",
			"param_name" => "image",
			"description" => "Add an optional image for the person/company who supplied the testimonial"
		),
	    array(
	      "type" => "textfield",
	      "heading" => __("Name", "js_composer"),
	      "param_name" => "name",
	      "admin_label" => true,
	      "description" => __("Name or source of the testimonial", "js_composer")
	    ),
	    array(
	      "type" => "textfield",
	      "heading" => __("Subtitle", "js_composer"),
	      "param_name" => "subtitle",
	      "admin_label" => false,
	      "description" => __("The optional subtitle that will follow the testimonial name", "js_composer")
	    ),
	    array(
	      "type" => "textarea",
	      "heading" => __("Quote", "js_composer"),
	      "param_name" => "quote",
	      "description" => __("The testimonial quote", "js_composer")
	    ),
	    array(
	      "type" => "tab_id",
	      "heading" => __("Testimonial ID", "js_composer"),
	      "param_name" => "id"
	    )
	  ),
	  'js_view' => ($vc_is_wp_version_3_6_more ? 'VcTabView' : 'VcTabView35')
	));









	/* clients slider */
	class WPBakeryShortCode_Clients extends WPBakeryShortCode_Tabbed_Section { }

	$tab_id_1 = time().'-1-'.rand(0, 100);
	$tab_id_2 = time().'-2-'.rand(0, 100);
	vc_map( array(
	  "name"  => __("Clients Display", "js_composer"),
	  "base" => "clients",
	  "show_settings_on_create" => false,
	  "is_container" => true,
	  "icon" => "icon-wpb-clients",
	  "category" => __('Nectar Elements', 'js_composer'),
	  "description" => __('Show off your clients!', 'js_composer'),
	  "params" => array(
	    array(
	      "type" => "dropdown",
	      "heading" => __("Columns", "js_composer"),
	      "param_name" => "columns",
	      "value" => array(
				"Two" => "2",
				"Three" => "3",	
				"Four" => "4",
				"Five" => "5",
				"Six" => "6"
			),
	      'save_always' => true,
	      "description" => __("Please select how many columns you would like..", "js_composer")
	    ),
	    array(
	      "type" => "checkbox",
		  "class" => "",
		  "heading" => "Fade In One By One?",
		  "value" => array("Yes, please" => "true" ),
		  "param_name" => "fade_in_animation",
		  "description" => ""
	    ),
	    array(
	      "type" => "checkbox",
		  "class" => "",
		  "heading" => "Turn Into Carousel",
		  "value" => array("Yes, please" => "true" ),
		  "param_name" => "carousel",
		  "description" => ""
	    ),
	    array(
	      "type" => "checkbox",
		  "class" => "",
		  "heading" => "Disable Autorotate?",
		  "value" => array("Yes, please" => "true" ),
		  "param_name" => "disable_autorotate",
		  "dependency" => Array('element' => "carousel", 'not_empty' => true),
		  "description" => ""
	    )
	  ),
	  "custom_markup" => '
	  <div class="wpb_tabs_holder wpb_holder vc_container_for_children">
	  <ul class="tabs_controls">
	  </ul>
	  %content%
	  </div>'
	  ,
	  'default_content' => '
	  [client title="'.__('Client','js_composer').'" id="'.$tab_id_1.'"] Click the edit button to add your testimonial. [/client]
	  [client title="'.__('Client','js_composer').'" id="'.$tab_id_2.'"] Click the edit button to add your testimonial. [/client]
	  ',
	  "js_view" => ($vc_is_wp_version_3_6_more ? 'VcTabsView' : 'VcTabsView35')
	));


	class WPBakeryShortCode_Client extends WPBakeryShortCode {
		
		public function customAdminBlockParams() {
	        return ' id="tab-'.$this->atts['id'] .'"';
	    }
		
	}



	vc_map( array(
	  "name" => __("Client", "js_composer"),
	  "base" => "client",
	  "allowed_container_element" => 'vc_row',
	  "is_container" => true,
	  "content_element" => false,
	  "params" => array(
	    array(
	      "type" => "fws_image",
	      "heading" => __("Image", "js_composer"),
	      "param_name" => "image",
	      "value" => "",
	      "description" => __("Select image from media library.", "js_composer")
	    ),
	    array(
	      "type" => "textfield",
	      "heading" => __("URL", "js_composer"),
	      "param_name" => "url",
	      "description" => __("Add an optional link to your client", "js_composer")
	    ),
	    array(
	      "admin_label" => true,
	      "type" => "textfield",
	      "heading" => __("Client Name", "js_composer"),
	      "param_name" => "name",
	      "description" => __("Fill this out to keep track of which client is which in your page builder interface.", "js_composer")
	    )
	  ),
	  'js_view' => ($vc_is_wp_version_3_6_more ? 'VcTabView' : 'VcTabView35')
	));

	



	/* icon list */
	class WPBakeryShortCode_Nectar_Icon_List extends WPBakeryShortCode_Tabbed_Section { }

	$tab_id_1 = time().'-1-'.rand(0, 100);
	$tab_id_2 = time().'-2-'.rand(0, 100);
	vc_map( array(
	  "name"  => __("Icon List", "js_composer"),
	  "base" => "nectar_icon_list",
	  "show_settings_on_create" => false,
	  "is_container" => true,
	  "icon" => "icon-wpb-fancy-ul",
	  "category" => __('Nectar Elements', 'js_composer'),
	  "description" => __('Create an icon list', 'js_composer'),
	  "params" => array(
	   
	    array(
	      "type" => "checkbox",
		  "class" => "",
		  "heading" => "Animate Element?",
		  "value" => array("Yes, please" => "true" ),
		  "param_name" => "animate",
		  "description" => ""
	    ),
	     array(
			"type" => "dropdown",
			"class" => "",
			"heading" => "Icon Color",
			"param_name" => "color",
			"value" => array(
				"Default" => "default",
				"Accent-Color" => "Accent-Color",
				"Extra-Color-1" => "Extra-Color-1",
				"Extra-Color-2" => "Extra-Color-2",	
				"Extra-Color-3" => "Extra-Color-3",
				"Extra-Color-Gradient-1" => "extra-color-gradient-1",
				"Extra-Color-Gradient-2" => "extra-color-gradient-2"
			),
			'save_always' => true,
			"description" => ""
		),

	    array(
	      "type" => "dropdown",
	      "heading" => __("Icon Size", "js_composer"),
	      "param_name" => "icon_size",
	      "value" => array(
				"Small" => "small",
				"Medium" => "medium",
				"Large" => "large"
			),
	      'save_always' => true,
	      "description" => __("Please select the direction you would like your list items to display in", "js_composer")
	    ),

	    array(
	      "type" => "dropdown",
	      "heading" => __("Icon Style", "js_composer"),
	      "param_name" => "icon_style",
	      "value" => array(
				"Icon Colored W/ Border" => "border",
				"Icon Colored No Border" => "no-border"
			),
	      'save_always' => true,
	      "description" => __("Please select the direction you would like your list items to display in", "js_composer")
	    ),

	  ),
	  "custom_markup" => '
	  <div class="wpb_tabs_holder wpb_holder vc_container_for_children">
	  <ul class="tabs_controls">
	  </ul>
	  %content%
	  </div>'
	  ,
	  'default_content' => '
	  [nectar_icon_list_item title="'.__('List Item','js_composer').'" id="'.$tab_id_1.'"]  [/nectar_icon_list_item]
	  [nectar_icon_list_item title="'.__('List Item','js_composer').'" id="'.$tab_id_2.'"] [/nectar_icon_list_item]
	  ',
	  "js_view" => ($vc_is_wp_version_3_6_more ? 'VcTabsView' : 'VcTabsView35')
	));


	class WPBakeryShortCode_Nectar_Icon_List_Item extends WPBakeryShortCode {
		
		public function customAdminBlockParams() {
	        return ' id="tab-'.$this->atts['id'] .'"';
	    }
		
	}



	vc_map( array(
	  "name" => __("List Item", "js_composer"),
	  "base" => "nectar_icon_list_item",
	  "allowed_container_element" => 'vc_row',
	  "is_container" => true,
	  "content_element" => false,
	  "params" => array(
	  	 array(
	      "type" => "dropdown",
	      "heading" => __("List Icon Type", "js_composer"),
	      "param_name" => "icon_type",
	      "value" => array(
				"Number" => "numerical",
				"Icon" => "icon"
			),
	      'save_always' => true,
	      "admin_label" => true,
	      "description" => __("Please select how many columns you would like..", "js_composer")
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
	      "heading" => __("Icon", "js_composer"),
	      "param_name" => "icon_fontawesome",
	      "settings" => array( "emptyIcon" => true, "iconsPerPage" => 4000),
	      "dependency" => Array('element' => "icon_family", 'value' => 'fontawesome'),
	      "description" => __("Select icon from library.", "js_composer")
	    ),
	    array(
	      "type" => "iconpicker",
	      "heading" => __("Icon", "js_composer"),
	      "param_name" => "icon_iconsmind",
	      "settings" => array( 'type' => 'iconsmind', 'emptyIcon' => false, "iconsPerPage" => 4000),
	      "dependency" => array('element' => "icon_family", 'value' => 'iconsmind'),
	      "description" => __("Select icon from library.", "js_composer")
	    ),
	    array(
	      "type" => "iconpicker",
	      "heading" => __("Icon", "js_composer"),
	      "param_name" => "icon_linea",
	      "settings" => array( 'type' => 'linea', "emptyIcon" => true, "iconsPerPage" => 4000),
	      "dependency" => Array('element' => "icon_family", 'value' => 'linea'),
	      "description" => __("Select icon from library.", "js_composer")
	    ),
	    array(
	      "type" => "iconpicker",
	      "heading" => __("Icon", "js_composer"),
	      "param_name" => "icon_steadysets",
	      "settings" => array( 'type' => 'steadysets', 'emptyIcon' => false, "iconsPerPage" => 4000),
	      "dependency" => array('element' => "icon_family", 'value' => 'steadysets'),
	      "description" => __("Select icon from library.", "js_composer")
	    ),
	  	 array(
	      "admin_label" => true,
	      "type" => "textfield",
	      "heading" => __("Header", "js_composer"),
	      "param_name" => "header",
	      "description" => __("Enter the header desired for your icon list element", "js_composer")
	    ),
	    array(
	      "admin_label" => true,
	      "type" => "textarea",
	      "heading" => __("Text Content", "js_composer"),
	      "param_name" => "text",
	      "description" => __("Enter the text content desired for your icon list element", "js_composer")
	    ),
	    array(
	      "type" => "tab_id",
	      "heading" => __("Item ID", "js_composer"),
	      "param_name" => "id"
	    )

	  ),
	  'js_view' => ($vc_is_wp_version_3_6_more ? 'VcTabView' : 'VcTabView35')
	));



	/* page sub menu */
	class WPBakeryShortCode_Page_Submenu extends WPBakeryShortCode_Tabbed_Section { }

	$tab_id_1 = time().'-1-'.rand(0, 100);
	$tab_id_2 = time().'-2-'.rand(0, 100);
	vc_map( array(
	  "name"  => __("Page Submenu", "js_composer"),
	  "base" => "page_submenu",
	  "show_settings_on_create" => true,
	  "is_container" => true,
	  "icon" => "icon-wpb-page-submenu",
	  "category" => __('Nectar Elements', 'js_composer'),
	  "description" => __('Great for animated anchors', 'js_composer'),
	  "params" => array( 
	  	array(
	      "type" => "dropdown",
	      "heading" => __("Link Alignment", "js_composer"),
	      "param_name" => "alignment",
	      "value" => array(
				"Center" => "center",
				"Left" => "left",	
				"Right" => "right"
			),
	      'save_always' => true,
	      "description" => __("Please select your desired link alignment", "js_composer")
	    ),
	    array(
	      "type" => "checkbox",
		  "class" => "",
		  "heading" => "Sticky?",
		  "value" => array("Yes, please" => "true" ),
		  "param_name" => "sticky",
		  "description" => "This will cause your submenu to stick to the top when scrolled by"
	    ),
		array(
			"type" => "colorpicker",
			"class" => "",
			"heading" => "Menu BG Color",
			"param_name" => "bg_color",
			"value" => "#f7f7f7",
			"description" => ""
		),
		array(
			"type" => "colorpicker",
			"class" => "",
			"heading" => "Link Color",
			"param_name" => "link_color",
			"value" => "#000000",
			"description" => ""
		),
	  ),
	  "custom_markup" => '
	  <div class="wpb_tabs_holder wpb_holder vc_container_for_children">
	  <ul class="tabs_controls">
	  </ul>
	  %content%
	  </div>'
	  ,
	  'default_content' => '
	  [page_link link_url="#" title="'.__('Link','js_composer').'" id="'.$tab_id_1.'"] [/page_link]
	  [page_link link_url="#" title="'.__('Link','js_composer').'" id="'.$tab_id_2.'"]  [/page_link]
	  ',
	  "js_view" => ($vc_is_wp_version_3_6_more ? 'VcTabsView' : 'VcTabsView35')
	));


	class WPBakeryShortCode_Page_Link extends WPBakeryShortCode {
		
		public function customAdminBlockParams() {
	        return ' id="tab-'.$this->atts['id'] .'"';
	    }
		
	}



	vc_map( array(
	  "name" => __("Menu Link", "js_composer"),
	  "base" => "page_link",
	  "allowed_container_element" => 'vc_row',
	  "is_container" => true,
	  "content_element" => false,
	  "params" => array(
	    array(
	      "admin_label" => false,
	      "type" => "textfield",
	      "heading" => __("Link Text", "js_composer"),
	      "param_name" => "title",
	      "description" => __("Enter the text that will be displayed for your link", "js_composer")
	    ),
	    array(
	      "admin_label" => true,
	      "type" => "textfield",
	      "heading" => __("Link URL", "js_composer"),
	      "param_name" => "link_url",
	      "description" => __("Enter the URL that will be used for your link", "js_composer")
	    ),
	     array(
	      "type" => "checkbox",
		  "class" => "",
		  "heading" => "Open Link In New Tab",
		  "value" => array("Yes, please" => "true" ),
		  "param_name" => "link_new_tab",
		  "description" => ""
	    ),
	    array(
	      "type" => "tab_id",
	      "heading" => __("Page Link ID", "js_composer"),
	      "param_name" => "id"
	    )
	  ),
	  'js_view' => ($vc_is_wp_version_3_6_more ? 'VcTabView' : 'VcTabView35')
	));






	/* pricing table */
	class WPBakeryShortCode_Pricing_Table extends WPBakeryShortCode_Tabbed_Section { }

	$tab_id_1 = time().'-1-'.rand(0, 100);
	$tab_id_2 = time().'-2-'.rand(0, 100);
	vc_map( array(
	  "name"  => __("Pricing Table", "js_composer"),
	  "base" => "pricing_table",
	  "show_settings_on_create" => false,
	  "is_container" => true,
	  "icon" => "icon-wpb-pricing-table",
	  "category" => __('Nectar Elements', 'js_composer'),
	  "description" => __('Stylish pricing tables', 'js_composer'),
	  "params" => array(
	    array(
	      "type" => "textfield",
	      "heading" => __("Extra class name", "js_composer"),
	      "param_name" => "el_class",
	      "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "js_composer")
		),
		array(
			"type" => "dropdown",
			"holder" => "div",
			"admin_label" => false,
			"class" => "",
			"heading" => "Style",
			"param_name" => "style",
			"value" => array(
				"Default" => "default",
				"Flat Alternative" => "flat-alternative"
			),
			'save_always' => true,
			"description" => ""
		)
	  ),
	  "custom_markup" => '
	  <div class="wpb_tabs_holder wpb_holder vc_container_for_children">
	  <ul class="tabs_controls">
	  </ul>
	  %content%
	  </div>'
	  ,
	  'default_content' => '
	  [pricing_column title="'.__('Column','js_composer').'" id="'.$tab_id_1.'"]  [/pricing_column]
	  [pricing_column title="'.__('Column','js_composer').'" id="'.$tab_id_2.'"]  [/pricing_column]
	  ',
	  "js_view" => ($vc_is_wp_version_3_6_more ? 'VcTabsView' : 'VcTabsView35')
	));


	class WPBakeryShortCode_Pricing_Column extends WPBakeryShortCode {
		
		public function customAdminBlockParams() {
	        return ' id="tab-'.$this->atts['id'] .'"';
	    }
		
	}



	vc_map( array(
	  "name" => __("Pricing Column", "js_composer"),
	  "base" => "pricing_column",
	  "allowed_container_element" => 'vc_row',
	  "is_container" => true,
	  "content_element" => false,
	  "params" => array(
	    array(
	      "type" => "textfield",
	      "heading" => __("Title", "js_composer"),
	      "param_name" => "title",
	      "admin_label" => true,
	      "description" => __("Please enter a title for your pricing column", "js_composer")
	    ),
	    array(
	      "type" => "textfield",
	      "heading" => __("Price", "js_composer"),
	      "param_name" => "price",
	      "description" => __("Enter the price for your column", "js_composer")
	    ),
	    array(
	      "type" => "textfield",
	      "heading" => __("Currency Symbol", "js_composer"),
	      "param_name" => "currency_symbol",
	      "description" => __("Enter the currency symbol that will display for your price", "js_composer")
	    ),
	    array(
	      "type" => "textfield",
	      "heading" => __("Interval", "js_composer"),
	      "param_name" => "interval",
	      "description" => __("Enter the interval for your pricing e.g. \"Per Month\" or \"Per Year\" ", "js_composer")
	    ),
	    array(
	      "type" => "checkbox",
		  "class" => "",
		  "heading" => "Highlight Column?",
		  "value" => array("Yes, please" => "true" ),
		  "param_name" => "highlight",
		  "description" => ""
	    ),
	    array(
	      "type" => "textfield",
	      "heading" => __("Highlight Reason", "js_composer"),
	      "param_name" => "highlight_reason",
	      "description" => __("Enter the reason for the column being highlighted e.g. \"Most Popular\"" , "js_composer"),
	      "dependency" => Array('element' => "highlight", 'not_empty' => true)
	    ),
	    array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => "Color",
			"param_name" => "color",
			"value" => array(
				"Accent-Color" => "Accent-Color",
				"Extra-Color-1" => "Extra-Color-1",
				"Extra-Color-2" => "Extra-Color-2",	
				"Extra-Color-3" => "Extra-Color-3"
			),
			'save_always' => true,
			"description" => ""
		),
		array(
	      "type" => "textarea_html",
	      "holder" => "div",
	      "heading" => __("Text Content", "js_composer"),
	      "param_name" => "content",
	      "value" => ''
	    )
	  ),
	  'js_view' => ($vc_is_wp_version_3_6_more ? 'VcTabView' : 'VcTabView35')
	));





	/* carousel */
	class WPBakeryShortCode_Carousel extends WPBakeryShortCode_Tabbed_Section { }

	$tab_id_1 = time().'-1-'.rand(0, 100);
	$tab_id_2 = time().'-2-'.rand(0, 100);
	$tab_id_3 = time().'-3-'.rand(0, 100);

	vc_map( array(
	  "name"  => __("Carousel", "js_composer"),
	  "base" => "carousel",
	  "show_settings_on_create" => true,
	  "is_container" => true,
	  "icon" => "icon-wpb-carousel",
	  "category" => __('Nectar Elements', 'js_composer'),
	  "description" => __('A simple carousel for any content', 'js_composer'),
	  "params" => array(
	  array(
			"type" => "dropdown",
			"class" => "",
			"heading" => "Carousel Script",
			'save_always' => true,
			"param_name" => "script",
			"value" => array(
				"carouFredSel" => "carouFredSel",
				"Owl Carousel" => "owl_carousel"
			),
			"description" => __("Owl Carousel is the reccomended choice as there's greater control over column sizing - however carouFredSel is available for legacy users who prefer it." , "js_composer")
		),
	   array(
			"type" => "dropdown",
			"class" => "",
			"heading" => "Columns <span>Desktop</span>",
			'save_always' => true,
			"param_name" => "desktop_cols",
			"value" => array(
				"Default (4)" => "4",
				"1" => "1",
				"2" => "2",
				"3" => "3",
				"4" => "4",
				"5" => "5",
				"6" => "6",
				"7" => "7",
				"8" => "8",
			),
			"edit_field_class" => "col-md-2 vc_column",
			"dependency" => array('element' => "script", 'value' => 'owl_carousel'),
			"description" => ''
		),
	   array(
			"type" => "dropdown",
			"class" => "",
			"heading" => "<span>Desktop Small</span>",
			'save_always' => true,
			"param_name" => "desktop_small_cols",
			"value" => array(
				"Default (3)" => "3",
				"1" => "1",
				"2" => "2",
				"3" => "3",
				"4" => "4",
				"5" => "5",
				"6" => "6",
				"7" => "7",
				"8" => "8",
			),
			"edit_field_class" => "col-md-2 vc_column",
			"dependency" => array('element' => "script", 'value' => 'owl_carousel'),
			"description" => ''
		),
	    array(
			"type" => "dropdown",
			"class" => "",
			"heading" => "<span>Tablet</span>",
			'save_always' => true,
			"param_name" => "tablet_cols",
			"value" => array(
				"Default (2)" => "2",
				"1" => "1",
				"2" => "2",
				"3" => "3",
				"4" => "4",
				"5" => "5",
				"6" => "6",
			),
			"edit_field_class" => "col-md-2 vc_column",
			"dependency" => array('element' => "script", 'value' => 'owl_carousel'),
			"description" => ''
		),
	    array(
			"type" => "dropdown",
			"class" => "",
			"heading" => "<span>Mobile</span>",
			'save_always' => true,
			"param_name" => "mobile_cols",
			"value" => array(
				"Default (1)" => "1",
				"1" => "1",
				"2" => "2",
				"3" => "3",
				"4" => "4",
			),
			"dependency" => array('element' => "script", 'value' => 'owl_carousel'),
			"edit_field_class" => "col-md-2 vc_column",
			"description" => ''
		),
	   array(
	      "type" => "textfield",
	      "heading" => __("Carousel Title", "js_composer"),
	      "param_name" => "carousel_title",
	      "dependency" => array('element' => "script", 'value' => array('carouFredSel')),
	      "description" => __("Enter the title you would like at the top of your carousel (optional)" , "js_composer")
	    ),
	   array(
	     "type" => "dropdown",
			"class" => "",
			"heading" => "Column Padding",
			'save_always' => true,
			"param_name" => "column_padding",
			"value" => array(
				"None" => "0",
				"5px" => "5px",
				"10px" => "10px",
				"15px" => "15px",
				"20px" => "20px",
				"30px" => "30px",
				"40px" => "40px",
				"50px" => "50px"
			),
			"dependency" => array('element' => "script", 'value' => 'owl_carousel'),
			"description" => __("Please select your desired column padding " , "js_composer")
	    ),
	    array(
	      "type" => "textfield",
	      "heading" => __("Transition Scroll Speed", "js_composer"),
	      "param_name" => "scroll_speed",
	      "dependency" => array('element' => "script", 'value' => array('carouFredSel')),
	      "description" => __("Enter in milliseconds (default is 700)" , "js_composer")
	    ),
	    array(
			"type" => "checkbox",
			"class" => "",
			"heading" => __("Autorotate?", "js_composer"),
	     	"param_name" => "autorotate",
			"value" => Array(__("Yes", "js_composer") => 'true'),
			"description" => ""
		),
		array(
	      "type" => "textfield",
	      "heading" => __("Autorotation Speed", "js_composer"),
	      "param_name" => "autorotation_speed",
	      "dependency" => array('element' => "script", 'value' => array('owl_carousel')),
	      "description" => __("Enter in milliseconds (default is 5000)" , "js_composer")
	    ),
	    array(
			"type" => "checkbox",
			"class" => "",
			"heading" => "Enable Animation",
			"value" => array("Enable Animation?" => "true" ),
			"param_name" => "enable_animation",
			"dependency" => array('element' => "script", 'value' => array('owl_carousel')),
			"description" => "This will cause your list items to animate in one by one"
		),

		array(
			"type" => "textfield",
			"class" => "",
			"heading" => "Animation Delay",
			"param_name" => "delay",
			"admin_label" => false,
			"description" => "",
			"dependency" => Array('element' => "enable_animation", 'not_empty' => true)
		),

	    array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"admin_label" => false,
			"heading" => "Easing",
			"param_name" => "easing",
			'save_always' => true,
			"dependency" => array('element' => "script", 'value' => array('carouFredSel')),
			"value" => array(
				'linear'=>'linear',
				'swing'=>'swing',
				'easeInQuad'=>'easeInQuad',
				'easeOutQuad' => 'easeOutQuad',
				'easeInOutQuad'=>'easeInOutQuad',
				'easeInCubic'=>'easeInCubic',
				'easeOutCubic'=>'easeOutCubic',
				'easeInOutCubic'=>'easeInOutCubic',
				'easeInQuart'=>'easeInQuart',
				'easeOutQuart'=>'easeOutQuart',
				'easeInOutQuart'=>'easeInOutQuart',
				'easeInQuint'=>'easeInQuint',
				'easeOutQuint'=>'easeOutQuint',
				'easeInOutQuint'=>'easeInOutQuint',
				'easeInExpo'=>'easeInExpo',
				'easeOutExpo'=>'easeOutExpo',
				'easeInOutExpo'=>'easeInOutExpo',
				'easeInSine'=>'easeInSine',
				'easeOutSine'=>'easeOutSine',
				'easeInOutSine'=>'easeInOutSine',
				'easeInCirc'=>'easeInCirc',
				'easeOutCirc'=>'easeOutCirc',
				'easeInOutCirc'=>'easeInOutCirc',
				'easeInElastic'=>'easeInElastic',
				'easeOutElastic'=>'easeOutElastic',
				'easeInOutElastic'=>'easeInOutElastic',
				'easeInBack'=>'easeInBack',
				'easeOutBack'=>'easeOutBack',
				'easeInOutBack'=>'easeInOutBack',
				'easeInBounce'=>'easeInBounce',
				'easeOutBounce'=>'easeOutBounce',
				'easeInOutBounce'=>'easeInOutBounce',
			),
			"description" => "Select the animation easing you would like for slide transitions <a href=\"http://jqueryui.com/resources/demos/effect/easing.html\" target=\"_blank\"> Click here </a> to see examples of these."
		)
	  ),
	  "custom_markup" => '
	  <div class="wpb_tabs_holder wpb_holder vc_container_for_children">
	  <ul class="tabs_controls">
	  </ul>
	  %content%
	  </div>'
	  ,
	  'default_content' => '
	  [item id="'.$tab_id_1.'"] Add Content Here [/item]
	  [item id="'.$tab_id_2.'"] Add Content Here [/item]
	  [item id="'.$tab_id_3.'"] Add Content Here [/item]
	  ',
	  "js_view" => ($vc_is_wp_version_3_6_more ? 'VcTabsView' : 'VcTabsView35')
	));



	vc_map( array(
	  "name" => __("Carousel Item", "js_composer"),
	  "base" => "item",
	  "allowed_container_element" => 'vc_row',
	  "is_container" => true,
	  "content_element" => false,
	  "params" => array(
	    array(
	      "type" => "tab_id",
	      "heading" => __("Tab ID", "js_composer"),
	      "param_name" => "id"
	    )
	  ),
	  'js_view' => ($vc_is_wp_version_3_6_more ? 'VcTabView' : 'VcTabView35')
	));




	// Social Buttons
	vc_map( array(
	  "name" => __("Social Buttons", "js_composer"),
	  "base" => "social_buttons",
	  "icon" => "icon-wpb-social-buttons",
	  "category" => __('Nectar Elements', 'js_composer'),
	  "description" => __('Add social buttons to any page', 'js_composer'),
	  "params" => array(
	     array(
	      "type" => 'checkbox',
	      "heading" => __("Display full width?", "js_composer"),
	      "param_name" => "full_width_icons",
	      "description" => __("This will make your social icons expand to fit edge to edge in whatever space they're placed." , "js_composer"),
	      "value" => Array(__("Yes", "js_composer") => 'true')
	    ),
	   /* array(
	      "type" => 'checkbox',
	      "heading" => __("Hide share counts?", "js_composer"),
	      "param_name" => "hide_share_count",
	      "description" => __("This will remove your share counts from displaying to the user" , "js_composer"),
	      "value" => Array(__("Yes", "js_composer") => 'true')
	    ), */
	 	 array(
	      "type" => 'checkbox',
	      "heading" => __("Nectar Love", "js_composer"),
	      "param_name" => "nectar_love",
	      "value" => Array(__("Yes", "js_composer") => 'true')
	    ),
	    array(
	      "type" => 'checkbox',
	      "heading" => __("Facebook", "js_composer"),
	      "param_name" => "facebook",
	      "value" => Array(__("Yes", "js_composer") => 'true')
	    ),
	    array(
	      "type" => 'checkbox',
	      "heading" => __("Twitter", "js_composer"),
	      "param_name" => "twitter",
	      "value" => Array(__("Yes", "js_composer") => 'true')
	    ),
	    array(
	      "type" => 'checkbox',
	      "heading" => __("Google+", "js_composer"),
	      "param_name" => "google_plus",
	      "value" => Array(__("Yes", "js_composer") => 'true')
	    ),
	    array(
	      "type" => 'checkbox',
	      "heading" => __("LinkedIn", "js_composer"),
	      "param_name" => "linkedin",
	      "value" => Array(__("Yes", "js_composer") => 'true')
	    ),
	    array(
	      "type" => 'checkbox',
	      "heading" => __("Pinterest", "js_composer"),
	      "param_name" => "pinterest",
	      "description" => '',
	      "value" => Array(__("Yes", "js_composer") => 'true')
	    )
	  )
	));




	//gallery 
	vc_remove_param("vc_gallery", "type");
	vc_remove_param("vc_gallery", "title");
	vc_remove_param("vc_gallery", "interval");
	vc_remove_param("vc_gallery", "images");
	vc_remove_param("vc_gallery", "img_size");
	vc_remove_param("vc_gallery", "onclick");
	vc_remove_param("vc_gallery", "custom_links");
	vc_remove_param("vc_gallery", "custom_links_target");
	vc_remove_param("vc_gallery", "el_class");


	if(nectar_has_shortcode('vc_gallery')) { 
		vc_add_param("vc_gallery",array(
		      "type" => "dropdown",
		      "heading" => __("Gallery type", "js_composer"),
		      "param_name" => "type",
		      "value" => array(
		         __("Basic Slider Style", "js_composer") => "flexslider_style", 
		         __("Nectar Slider Style", "js_composer") => "nectarslider_style",
		         __("Touch Enabled & Spaced", "js_composer") => "flickity_style",
		         __("Image Grid Style", "js_composer") => "image_grid"
		       ),
		      'save_always' => true,
		      "description" => __("Select gallery type.", "js_composer")
		));
		vc_add_param("vc_gallery",array(
		      "type" => "dropdown",
		      "heading" => __("Auto rotate slides", "js_composer"),
		      "param_name" => "interval",
		      "value" => array(3, 5, 10, 15, __("Disable", "js_composer") => 0),
		      "description" => __("Auto rotate slides each X seconds.", "js_composer"),
		      'save_always' => true,
		      "dependency" => Array('element' => "type", 'value' => array('flexslider_fade', 'flexslider_slide', 'nivo'))
		));
		vc_add_param("vc_gallery",array(
		      "type" => "attach_images",
		      "heading" => __("Images", "js_composer"),
		      "param_name" => "images",
		      "value" => "",
		      "description" => __("Select images from media library.", "js_composer"),
		      "dependency" => Array('element' => "source", 'value' => array('media_library'))
		));
		vc_add_param("vc_gallery",array(
		      "type" => "textfield",
		      "heading" => __("Image size", "js_composer"),
		      "param_name" => "img_size",
		      "description" => __("Enter image size in pixels - e.g 600x400 (Width x Height) <br/> Or use WordPress image size names such as \"full\"", "js_composer"),
		      "dependency" => Array('element' => "source", 'value' => array('media_library'))
		));

		vc_add_param("vc_gallery",array(
			  "type" => "dropdown",
			  "heading" => __("Controls", "js_composer"),
			  "param_name" => "flickity_controls",
			  "value" => array(
				    "Pagination" => "pagination",
				    "Next/Prev Arrows" => "next_prev_arrows"
				),
			  'save_always' => true,
			  "description" => __("Please select the controls you would like for your gallery ", "js_composer"),
			  "dependency" => Array('element' => "type", 'value' => array('flickity_style'))
		));
		vc_add_param("vc_gallery",array(
	      "type" => "dropdown",
	      "heading" => __("Box Shadow", "js_composer"),
	      'save_always' => true,
	      "param_name" => "flickity_box_shadow",
	      "value" => array(__("None", "js_composer") => "none", __("Small Depth", "js_composer") => "small_depth", __("Medium Depth", "js_composer") => "medium_depth", __("Large Depth", "js_composer") => "large_depth", __("Very Large Depth", "js_composer") => "x_large_depth"),
	      "description" => __("Select your desired image box shadow", "js_composer"),
	      "dependency" => Array('element' => "type", 'value' => array('flickity_style'))
	    ));

		 vc_add_param("vc_gallery",array(
		      "type" => 'checkbox',
		      "heading" => __("Flexible Slider Height", "js_composer"),
		      "param_name" => "flexible_slider_height",
		      "description" => __("Would you like the height of your slider to constantly scale in porportion to the screen size?", "js_composer"),
		      "value" => Array(__("Yes, please", "js_composer") => 'true'),
		      "dependency" => Array('element' => "type", 'value' => array('nectarslider_style'))
		  ));
		  vc_add_param("vc_gallery",array(
		      "type" => 'checkbox',
		      "heading" => __("Hide Arrow Navigation?", "js_composer"),
		      "param_name" => "hide_arrow_navigation",
		      "description" => __("Would you like this slider to hide the arrows on the right and left sides?", "js_composer"),
		      "value" => Array(__("Yes, please", "js_composer") => 'true'),
		      "dependency" => Array('element' => "type", 'value' => array('nectarslider_style'))
		  ));
		  vc_add_param("vc_gallery",array(
		      "type" => 'checkbox',
		      "heading" => __("Display Bullet Navigation?", "js_composer"),
		      "param_name" => "bullet_navigation",
		      "description" => __("Would you like this slider to display bullets on the bottom?", "js_composer"),
		      "value" => Array(__("Yes, please", "js_composer") => 'true'),
		      "dependency" => Array('element' => "type", 'value' => array('nectarslider_style'))
		  ));
		  vc_add_param("vc_gallery",array(
		      "type" => "dropdown",
		      "heading" => __("Bullet Navigation Style", "js_composer"),
		      "param_name" => "bullet_navigation_style",
		      "value" => array(
					'See Through & Solid On Active' => 'see_through',
					'Solid & Scale On Active' => 'scale'
		      ),
		      'save_always' => true,
		      "description" => 'Please select your overall bullet navigation style here.',
		      "dependency" => Array('element' => "type", 'value' => array('nectarslider_style'))
		  ));


		vc_add_param("vc_gallery",array(
		      "type" => 'checkbox',
		      "heading" => __("Display Title/Caption?", "js_composer"),
		      "param_name" => "display_title_caption",
		      "value" => Array(__("Yes", "js_composer") => 'true'),
		      "dependency" => Array('element' => "type", 'value' => array('image_grid'))
		));

		vc_add_param("vc_gallery",array(
			  "type" => "dropdown",
			  "heading" => __("Layout", "js_composer"),
			  "param_name" => "layout",
			  "admin_label" => true,
			  "value" => array(
				    "3 Columns" => "3",
				    "4 Columns" => "4",
				    "Fullwidth" => "fullwidth"
				),
			  'save_always' => true,
			  "description" => __("Please select the layout you would like for your gallery ", "js_composer"),
			  "dependency" => Array('element' => "type", 'value' => array('image_grid'))
		));
		vc_add_param("vc_gallery",array(
		      "type" => 'checkbox',
		      "heading" => __("Masonry Style", "js_composer"),
		      "param_name" => "masonry_style",
		      "description" => __("This will allow your gallery items to display in a masonry layout as opposed to a fixed grid. You can define your desired masonry size for each image when editing/adding them in the right hand side \"Attachment Details\" sidebar. Enabling this will override the \"Image Size\" field above.<br/> ", "js_composer"),
		      "value" => Array(__("Yes, please", "js_composer") => 'true'),
		      "dependency" => Array('element' => "type", 'value' => array('image_grid'))
		));
		vc_add_param("vc_gallery",array(
			  "type" => "dropdown",
			  "heading" => __("Item Spacing", "js_composer"),
			  "param_name" => "item_spacing",
			  'save_always' => true,
			  "value" => array(
			  		"Default" => "default",
				    "1px" => "1px",
				    "2px" => "2px",
				    "3px" => "3px",
				    "4px" => "4px",
				    "5px" => "5px",
				    "6px" => "6px",
				    "7px" => "7px",
				    "8px" => "8px",
				    "9px" => "9px",
				    "10px" => "10px",
				    "15px" => "15px",
				    "20px" => "20px"
				),
			  "dependency" => Array('element' => "layout", 'value' => array('fullwidth')),
			  "description" => __("Please select the spacing you would like between your items. ", "js_composer")
		));
		vc_add_param("vc_gallery",array(
		      "type" => 'checkbox',
		      "heading" => __("Constrain Max Columns to 4?", "js_composer"),
		      "param_name" => "constrain_max_cols",
		      "description" => __("This will change the max columns to 4 (default is 5 for fullwidth). Activating this will make it easier to create a grid with no empty spaces at the end of the list on all screen sizes. (Won't be used if masonry layout is active)", "js_composer"),
		      "value" => Array(__("Yes, please", "js_composer") => 'true'),
		      "dependency" => Array('element' => "layout", 'value' => 'fullwidth')
		));
		vc_add_param("vc_gallery",array(
			  "type" => "dropdown",
			  "heading" => __("Gallery Style", "js_composer"),
			  "param_name" => "gallery_style",
			  "admin_label" => true,
			  "value" => array(
				    "Meta below thumb w/ links on hover" => "1",
				    "Meta on hover + entire thumb link" => "2",
				    "Meta on hover w/ zoom + entire thumb link" => "7",
				    "Title overlaid w/ zoom effect on hover" => "3",
				    'Title overlaid w/ zoom effect on hover alt' => '5',
				    "Meta from bottom on hover + entire thumb link" => "4"
				),
			  'save_always' => true,
			  "description" => __("Please select the style you would like your gallery to display in ", "js_composer"),
			  "dependency" => Array('element' => "type", 'value' => array('image_grid'))
		));

		vc_add_param("vc_gallery",array(
			  "type" => "dropdown",
			  "heading" => __("Load In Animation", "js_composer"),
			  "param_name" => "load_in_animation",
			  'save_always' => true,
			  "value" => array(
				    "None" => "none",
				    "Fade In" => "fade_in",
				    "Fade In From Bottom" => "fade_in_from_bottom"
				),
			  "description" => __("Please select the style you would like your projects to display in ", "js_composer"),
			  "dependency" => Array('element' => "type", 'value' => array('image_grid'))
		));

		vc_add_param("vc_gallery",array(
		      "type" => "dropdown",
		      "heading" => __("On click", "js_composer"),
		      "param_name" => "onclick",
		      "value" => array( __("Do nothing", "js_composer") => "link_no", __("Open prettyPhoto", "js_composer") => "link_image",  __("Open custom link", "js_composer") => "custom_link"),
		      "description" => __("What to do when slide is clicked?", "js_composer"),
		      'save_always' => true,
		      "dependency" => Array('element' => "type", 'value' => array('nectarslider_style', 'flexslider_style', 'flickity_style'))
		));
		vc_add_param("vc_gallery",array(
		      "type" => "exploded_textarea",
		      "heading" => __("Custom links", "js_composer"),
		      "param_name" => "custom_links",
		      "description" => __('Enter links for each slide here. Divide links with linebreaks (Enter).', 'js_composer'),
		      "dependency" => Array('element' => "onclick", 'value' => array('custom_link'))
		));

		vc_add_param("vc_gallery",array(
		      "type" => "dropdown",
		      "heading" => __("Custom link target", "js_composer"),
		      "param_name" => "custom_links_target",
		      "description" => __('Select where to open  custom links.', 'js_composer'),
		      "dependency" => Array('element' => "onclick", 'value' => array('custom_link')),
		      'save_always' => true,
		      'value' => array(__("Same window", "js_composer") => "_self", __("New window", "js_composer") => "_blank")
		));
		vc_add_param("vc_gallery",array(
		      "type" => "textfield",
		      "heading" => __("Extra class name", "js_composer"),
		      "param_name" => "el_class",
		      "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "js_composer")
		));
	}













	   $fa_icons = array(
		      'icon-glass' => 'icon-glass',
			  'icon-music' => 'icon-music',
			  'icon-search' => 'icon-search',
			  'icon-envelope-alt' => 'icon-envelope-alt',
			  'icon-heart' => 'icon-heart',
			  'icon-star' => 'icon-star',
			  'icon-star-empty' => 'icon-star-empty',
			  'icon-user' => 'icon-user',
			  'icon-film' => 'icon-film',
			  'icon-th-large' => 'icon-th-large',
			  'icon-th' => 'icon-th',
			  'icon-th-list' => 'icon-th-list',
			  'icon-ok' => 'icon-ok',
			  'icon-remove' => 'icon-remove',
			  'icon-zoom-in' => 'icon-zoom-in',
			  'icon-zoom-out' => 'icon-zoom-out',
			  'icon-off' => 'icon-off',
			  'icon-signal' => 'icon-signal',
			  'icon-cog' => 'icon-cog',
			  'icon-trash' => 'icon-trash',
			  'icon-home' => 'icon-home',
			  'icon-file-alt' => 'icon-file-alt',
			  'icon-time' => 'icon-time',
			  'icon-road' => 'icon-road',
			  'icon-download-alt' => 'icon-download-alt',
			  'icon-download' => 'icon-download',
			  'icon-upload' => 'icon-upload',
			  'icon-inbox' => 'icon-inbox',
			  'icon-play-circle' => 'icon-play-circle',
			  'icon-repeat' => 'icon-repeat',
			  'icon-refresh' => 'icon-refresh',
			  'icon-list-alt' => 'icon-list-alt',
			  'icon-lock' => 'icon-lock',
			  'icon-flag' => 'icon-flag',
			  'icon-headphones' => 'icon-headphones',
			  'icon-volume-off' => 'icon-volume-off',
			  'icon-volume-down' => 'icon-volume-down',
			  'icon-volume-up' => 'icon-volume-up',
			  'icon-qrcode' => 'icon-qrcode',
			  'icon-barcode' => 'icon-barcode',
			  'icon-tag' => 'icon-tag',
			  'icon-tags' => 'icon-tags',
			  'icon-book' => 'icon-book',
			  'icon-bookmark' => 'icon-bookmark',
			  'icon-print' => 'icon-print',
			  'icon-camera' => 'icon-camera',
			  'icon-font' => 'icon-font',
			  'icon-bold' => 'icon-bold',
			  'icon-italic' => 'icon-italic',
			  'icon-text-height' => 'icon-text-height',
			  'icon-text-width' => 'icon-text-width',
			  'icon-align-left' => 'icon-align-left',
			  'icon-align-center' => 'icon-align-center',
			  'icon-align-right' => 'icon-align-right',
			  'icon-align-justify' => 'icon-align-justify',
			  'icon-list' => 'icon-list',
			  'icon-indent-left' => 'icon-indent-left',
			  'icon-indent-right' => 'icon-indent-right',
			  'icon-facetime-video' => 'icon-facetime-video',
			  'icon-picture' => 'icon-picture',
			  'icon-pencil' => 'icon-pencil',
			  'icon-map-marker' => 'icon-map-marker',
			  'icon-adjust' => 'icon-adjust',
			  'icon-tint' => 'icon-tint',
			  'icon-edit' => 'icon-edit',
			  'icon-share' => 'icon-share',
			  'icon-check' => 'icon-check',
			  'icon-move' => 'icon-move',
			  'icon-step-backward' => 'icon-step-backward',
			  'icon-fast-backward' => 'icon-fast-backward',
			  'icon-backward' => 'icon-backward',
			  'icon-play' => 'icon-play',
			  'icon-pause' => 'icon-pause',
			  'icon-stop' => 'icon-stop',
			  'icon-forward' => 'icon-forward',
			  'icon-fast-forward' => 'icon-fast-forward',
			  'icon-step-forward' => 'icon-step-forward',
			  'icon-eject' => 'icon-eject',
			  'icon-chevron-left' => 'icon-chevron-left',
			  'icon-chevron-right' => 'icon-chevron-right',
			  'icon-plus-sign' => 'icon-plus-sign',
			  'icon-minus-sign' => 'icon-minus-sign',
			  'icon-remove-sign' => 'icon-remove-sign',
			  'icon-ok-sign' => 'icon-ok-sign',
			  'icon-question-sign' => 'icon-question-sign',
			  'icon-info-sign' => 'icon-info-sign',
			  'icon-screenshot' => 'icon-screenshot',
			  'icon-remove-circle' => 'icon-remove-circle',
			  'icon-ok-circle' => 'icon-ok-circle',
			  'icon-ban-circle' => 'icon-ban-circle',
			  'icon-arrow-left' => 'icon-arrow-left',
			  'icon-arrow-right' => 'icon-arrow-right',
			  'icon-arrow-up' => 'icon-arrow-up',
			  'icon-arrow-down' => 'icon-arrow-down',
			  'icon-share-alt' => 'icon-share-alt',
			  'icon-resize-full' => 'icon-resize-full',
			  'icon-resize-small' => 'icon-resize-small',
			  'icon-plus' => 'icon-plus',
			  'icon-minus' => 'icon-minus',
			  'icon-asterisk' => 'icon-asterisk',
			  'icon-exclamation-sign' => 'icon-exclamation-sign',
			  'icon-gift' => 'icon-gift',
			  'icon-leaf' => 'icon-leaf',
			  'icon-fire' => 'icon-fire',
			  'icon-eye-open' => 'icon-eye-open',
			  'icon-eye-close' => 'icon-eye-close',
			  'icon-warning-sign' => 'icon-warning-sign',
			  'icon-plane' => 'icon-plane',
			  'icon-calendar' => 'icon-calendar',
			  'icon-random' => 'icon-random',
			  'icon-comment' => 'icon-comment',
			  'icon-magnet' => 'icon-magnet',
			  'icon-chevron-up' => 'icon-chevron-up',
			  'icon-chevron-down' => 'icon-chevron-down',
			  'icon-retweet' => 'icon-retweet',
			  'icon-shopping-cart' => 'icon-shopping-cart',
			  'icon-folder-close' => 'icon-folder-close',
			  'icon-folder-open' => 'icon-folder-open',
			  'icon-resize-vertical' => 'icon-resize-vertical',
			  'icon-resize-horizontal' => 'icon-resize-horizontal',
			  'icon-bar-chart' => 'icon-bar-chart',
			  'icon-twitter-sign' => 'icon-twitter-sign',
			  'icon-facebook-sign' => 'icon-facebook-sign',
			  'icon-camera-retro' => 'icon-camera-retro',
			  'icon-key' => 'icon-key',
			  'icon-cogs' => 'icon-cogs',
			  'icon-comments' => 'icon-comments',
			  'icon-thumbs-up-alt' => 'icon-thumbs-up-alt',
			  'icon-thumbs-down-alt' => 'icon-thumbs-down-alt',
			  'icon-star-half' => 'icon-star-half',
			  'icon-heart-empty' => 'icon-heart-empty',
			  'icon-signout' => 'icon-signout',
			  'icon-linkedin-sign' => 'icon-linkedin-sign',
			  'icon-pushpin' => 'icon-pushpin',
			  'icon-external-link' => 'icon-external-link',
			  'icon-signin' => 'icon-signin',
			  'icon-trophy' => 'icon-trophy',
			  'icon-github-sign' => 'icon-github-sign',
			  'icon-upload-alt' => 'icon-upload-alt',
			  'icon-lemon' => 'icon-lemon',
			  'icon-phone' => 'icon-phone',
			  'icon-check-empty' => 'icon-check-empty',
			  'icon-bookmark-empty' => 'icon-bookmark-empty',
			  'icon-phone-sign' => 'icon-phone-sign',
			  'icon-twitter' => 'icon-twitter',
			  'icon-facebook' => 'icon-facebook',
			  'icon-github' => 'icon-github',
			  'icon-unlock' => 'icon-unlock',
			  'icon-credit-card' => 'icon-credit-card',
			  'icon-rss' => 'icon-rss',
			  'icon-hdd' => 'icon-hdd',
			  'icon-bullhorn' => 'icon-bullhorn',
			  'icon-bell' => 'icon-bell',
			  'icon-certificate' => 'icon-certificate',
			  'icon-hand-right' => 'icon-hand-right',
			  'icon-hand-left' => 'icon-hand-left',
			  'icon-hand-up' => 'icon-hand-up',
			  'icon-hand-down' => 'icon-hand-down',
			  'icon-circle-arrow-left' => 'icon-circle-arrow-left',
			  'icon-circle-arrow-right' => 'icon-circle-arrow-right',
			  'icon-circle-arrow-up' => 'icon-circle-arrow-up',
			  'icon-circle-arrow-down' => 'icon-circle-arrow-down',
			  'icon-globe' => 'icon-globe',
			  'icon-wrench' => 'icon-wrench',
			  'icon-tasks' => 'icon-tasks',
			  'icon-filter' => 'icon-filter',
			  'icon-briefcase' => 'icon-briefcase',
			  'icon-fullscreen' => 'icon-fullscreen',
			  'icon-group' => 'icon-group',
			  'icon-link' => 'icon-link',
			  'icon-cloud' => 'icon-cloud',
			  'icon-beaker' => 'icon-beaker',
			  'icon-cut' => 'icon-cut',
			  'icon-copy' => 'icon-copy',
			  'icon-paper-clip' => 'icon-paper-clip',
			  'icon-save' => 'icon-save',
			  'icon-sign-blank' => 'icon-sign-blank',
			  'icon-reorder' => 'icon-reorder',
			  'icon-list-ul' => 'icon-list-ul',
			  'icon-list-ol' => 'icon-list-ol',
			  'icon-strikethrough' => 'icon-strikethrough',
			  'icon-underline' => 'icon-underline',
			  'icon-table' => 'icon-table',
			  'icon-magic' => 'icon-magic',
			  'icon-truck' => 'icon-truck',
			  'icon-pinterest' => 'icon-pinterest',
			  'icon-pinterest-sign' => 'icon-pinterest-sign',
			  'icon-google-plus-sign' => 'icon-google-plus-sign',
			  'icon-google-plus' => 'icon-google-plus',
			  'icon-money' => 'icon-money',
			  'icon-caret-down' => 'icon-caret-down',
			  'icon-caret-up' => 'icon-caret-up',
			  'icon-caret-left' => 'icon-caret-left',
			  'icon-caret-right' => 'icon-caret-right',
			  'icon-columns' => 'icon-columns',
			  'icon-sort' => 'icon-sort',
			  'icon-sort-down' => 'icon-sort-down',
			  'icon-sort-up' => 'icon-sort-up',
			  'icon-envelope' => 'icon-envelope',
			  'icon-linkedin' => 'icon-linkedin',
			  'icon-undo' => 'icon-undo',
			  'icon-legal' => 'icon-legal',
			  'icon-dashboard' => 'icon-dashboard',
			  'icon-comment-alt' => 'icon-comment-alt',
			  'icon-comments-alt' => 'icon-comments-alt',
			  'icon-bolt' => 'icon-bolt',
			  'icon-sitemap' => 'icon-sitemap',
			  'icon-umbrella' => 'icon-umbrella',
			  'icon-paste' => 'icon-paste',
			  'icon-lightbulb' => 'icon-lightbulb',
			  'icon-exchange' => 'icon-exchange',
			  'icon-cloud-download' => 'icon-cloud-download',
			  'icon-cloud-upload' => 'icon-cloud-upload',
			  'icon-user-md' => 'icon-user-md',
			  'icon-stethoscope' => 'icon-stethoscope',
			  'icon-suitcase' => 'icon-suitcase',
			  'icon-bell-alt' => 'icon-bell-alt',
			  'icon-coffee' => 'icon-coffee',
			  'icon-food' => 'icon-food',
			  'icon-file-text-alt' => 'icon-file-text-alt',
			  'icon-building' => 'icon-building',
			  'icon-hospital' => 'icon-hospital',
			  'icon-ambulance' => 'icon-ambulance',
			  'icon-medkit' => 'icon-medkit',
			  'icon-fighter-jet' => 'icon-fighter-jet',
			  'icon-beer' => 'icon-beer',
			  'icon-h-sign' => 'icon-h-sign',
			  'icon-plus-sign-alt' => 'icon-plus-sign-alt',
			  'icon-double-angle-left' => 'icon-double-angle-left',
			  'icon-double-angle-right' => 'icon-double-angle-right',
			  'icon-double-angle-up' => 'icon-double-angle-up',
			  'icon-double-angle-down' => 'icon-double-angle-down',
			  'icon-angle-left' => 'icon-angle-left',
			  'icon-angle-right' => 'icon-angle-right',
			  'icon-angle-up' => 'icon-angle-up',
			  'icon-angle-down' => 'icon-angle-down',
			  'icon-desktop' => 'icon-desktop',
			  'icon-laptop' => 'icon-laptop',
			  'icon-tablet' => 'icon-tablet',
			  'icon-mobile-phone' => 'icon-mobile-phone',
			  'icon-circle-blank' => 'icon-circle-blank',
			  'icon-quote-left' => 'icon-quote-left',
			  'icon-quote-right' => 'icon-quote-right',
			  'icon-spinner' => 'icon-spinner',
			  'icon-circle' => 'icon-circle',
			  'icon-reply' => 'icon-reply',
			  'icon-github-alt' => 'icon-github-alt',
			  'icon-folder-close-alt' => 'icon-folder-close-alt',
			  'icon-folder-open-alt' => 'icon-folder-open-alt',
			  'icon-expand-alt' => 'icon-expand-alt',
			  'icon-collapse-alt' => 'icon-collapse-alt',
			  'icon-smile' => 'icon-smile',
			  'icon-frown' => 'icon-frown',
			  'icon-meh' => 'icon-meh',
			  'icon-gamepad' => 'icon-gamepad',
			  'icon-keyboard' => 'icon-keyboard',
			  'icon-flag-alt' => 'icon-flag-alt',
			  'icon-flag-checkered' => 'icon-flag-checkered',
			  'icon-terminal' => 'icon-terminal',
			  'icon-code' => 'icon-code',
			  'icon-reply-all' => 'icon-reply-all',
			  'icon-mail-reply-all' => 'icon-mail-reply-all',
			  'icon-star-half-empty' => 'icon-star-half-empty',
			  'icon-location-arrow' => 'icon-location-arrow',
			  'icon-crop' => 'icon-crop',
			  'icon-code-fork' => 'icon-code-fork',
			  'icon-unlink' => 'icon-unlink',
			  'icon-question' => 'icon-question',
			  'icon-info' => 'icon-info',
			  'icon-exclamation' => 'icon-exclamation',
			  'icon-superscript' => 'icon-superscript',
			  'icon-subscript' => 'icon-subscript',
			  'icon-eraser' => 'icon-eraser',
			  'icon-puzzle-piece' => 'icon-puzzle-piece',
			  'icon-microphone' => 'icon-microphone',
			  'icon-microphone-off' => 'icon-microphone-off',
			  'icon-shield' => 'icon-shield',
			  'icon-calendar-empty' => 'icon-calendar-empty',
			  'icon-fire-extinguisher' => 'icon-fire-extinguisher',
			  'icon-rocket' => 'icon-rocket',
			  'icon-maxcdn' => 'icon-maxcdn',
			  'icon-chevron-sign-left' => 'icon-chevron-sign-left',
			  'icon-chevron-sign-right' => 'icon-chevron-sign-right',
			  'icon-chevron-sign-up' => 'icon-chevron-sign-up',
			  'icon-chevron-sign-down' => 'icon-chevron-sign-down',
			  'icon-html5' => 'icon-html5',
			  'icon-css3' => 'icon-css3',
			  'icon-anchor' => 'icon-anchor',
			  'icon-unlock-alt' => 'icon-unlock-alt',
			  'icon-bullseye' => 'icon-bullseye',
			  'icon-ellipsis-horizontal' => 'icon-ellipsis-horizontal',
			  'icon-ellipsis-vertical' => 'icon-ellipsis-vertical',
			  'icon-rss-sign' => 'icon-rss-sign',
			  'icon-play-sign' => 'icon-play-sign',
			  'icon-ticket' => 'icon-ticket',
			  'icon-minus-sign-alt' => 'icon-minus-sign-alt',
			  'icon-check-minus' => 'icon-check-minus',
			  'icon-level-up' => 'icon-level-up',
			  'icon-level-down' => 'icon-level-down',
			  'icon-check-sign' => 'icon-check-sign',
			  'icon-edit-sign' => 'icon-edit-sign',
			  'icon-external-link-sign' => 'icon-external-link-sign',
			  'icon-share-sign' => 'icon-share-sign',
			  'icon-compass' => 'icon-compass',
			  'icon-collapse' => 'icon-collapse',
			  'icon-collapse-top' => 'icon-collapse-top',
			  'icon-expand' => 'icon-expand',
			  'icon-eur' => 'icon-eur',
			  'icon-gbp' => 'icon-gbp',
			  'icon-usd' => 'icon-usd',
			  'icon-inr' => 'icon-inr',
			  'icon-jpy' => 'icon-jpy',
			  'icon-cny' => 'icon-cny',
			  'icon-krw' => 'icon-krw',
			  'icon-btc' => 'icon-btc',
			  'icon-file' => 'icon-file',
			  'icon-file-text' => 'icon-file-text',
			  'icon-sort-by-alphabet' => 'icon-sort-by-alphabet',
			  'icon-sort-by-alphabet-alt' => 'icon-sort-by-alphabet-alt',
			  'icon-sort-by-attributes' => 'icon-sort-by-attributes',
			  'icon-sort-by-attributes-alt' => 'icon-sort-by-attributes-alt',
			  'icon-sort-by-order' => 'icon-sort-by-order',
			  'icon-sort-by-order-alt' => 'icon-sort-by-order-alt',
			  'icon-thumbs-up' => 'icon-thumbs-up',
			  'icon-thumbs-down' => 'icon-thumbs-down',
			  'icon-youtube-sign' => 'icon-youtube-sign',
			  'icon-youtube' => 'icon-youtube',
			  'icon-xing' => 'icon-xing',
			  'icon-xing-sign' => 'icon-xing-sign',
			  'icon-youtube-play' => 'icon-youtube-play',
			  'icon-dropbox' => 'icon-dropbox',
			  'icon-stackexchange' => 'icon-stackexchange',
			  'icon-instagram' => 'icon-instagram',
			  'icon-flickr' => 'icon-flickr',
			  'icon-adn' => 'icon-adn',
			  'icon-bitbucket' => 'icon-bitbucket',
			  'icon-bitbucket-sign' => 'icon-bitbucket-sign',
			  'icon-tumblr' => 'icon-tumblr',
			  'icon-tumblr-sign' => 'icon-tumblr-sign',
			  'icon-long-arrow-down' => 'icon-long-arrow-down',
			  'icon-long-arrow-up' => 'icon-long-arrow-up',
			  'icon-long-arrow-left' => 'icon-long-arrow-left',
			  'icon-long-arrow-right' => 'icon-long-arrow-right',
			  'icon-apple' => 'icon-apple',
			  'icon-windows' => 'icon-windows',
			  'icon-android' => 'icon-android',
			  'icon-linux' => 'icon-linux',
			  'icon-dribbble' => 'icon-dribbble',
			  'icon-skype' => 'icon-skype',
			  'icon-foursquare' => 'icon-foursquare',
			  'icon-trello' => 'icon-trello',
			  'icon-female' => 'icon-female',
			  'icon-male' => 'icon-male',
			  'icon-gittip' => 'icon-gittip',
			  'icon-sun' => 'icon-sun',
			  'icon-moon' => 'icon-moon',
			  'icon-archive' => 'icon-archive',
			  'icon-bug' => 'icon-bug',
			  'icon-vk' => 'icon-vk',
			  'icon-weibo' => 'icon-weibo',
			  'icon-renren' => 'icon-renren',
			  'fa-pagelines' => 'fa fa-pagelines',
			  'fa-stack-exchange' => 'fa fa-stack-exchange',
			  'fa-arrow-circle-o-right' => 'fa fa-arrow-circle-o-right',
			  'fa-arrow-circle-o-left' => 'fa fa-arrow-circle-o-left',
			  'fa-caret-square-o-left' => 'fa fa-caret-square-o-left',
			  'fa-dot-circle-o' => 'fa fa-dot-circle-o',
			  'fa-wheelchair' => 'fa fa-wheelchair',
			  'fa-vimeo-square' => 'fa fa-vimeo-square',
			  'fa-try' => 'fa fa-try',
			  'fa-plus-square-o' => 'fa fa-plus-square-o',
			  'fa-space-shuttle' => 'fa fa-space-shuttle',
			  'fa-slack' => 'fa fa-slack',
			  'fa-envelope-square' => 'fa fa-envelope-square',
			  'fa-wordpress' => 'fa fa-wordpress',
			  'fa-openid' => 'fa fa-openid',
			  'fa-university' => 'fa fa-university',
			  'fa-graduation-cap' => 'fa fa-graduation-cap',
			  'fa-yahoo' => 'fa fa-yahoo',
			  'fa-google' => 'fa fa-google',
			  'fa-reddit' => 'fa fa-reddit',
			  'fa-reddit-square' => 'fa fa-reddit-square',
			  'fa-stumbleupon-circle' => 'fa fa-stumbleupon-circle',
			  'fa-stumbleupon' => 'fa fa-stumbleupon',
			  'fa-delicious' => 'fa fa-delicious',
			  'fa-digg' => 'fa fa-digg',
			  'fa-pied-piper-pp' => 'fa fa-pied-piper-pp',
			  'fa-pied-piper-alt' => 'fa fa-pied-piper-alt',
			  'fa-drupal' => 'fa fa-drupal',
			  'fa-joomla' => 'fa fa-joomla',
			  'fa-language' => 'fa fa-language',
			  'fa-fax' => 'fa fa-fax',
			  'fa-building' => 'fa fa-building',
			  'fa-child' => 'fa fa-child',
			  'fa-paw' => 'fa fa-paw',
			  'fa-spoon' => 'fa fa-spoon',
			  'fa-cube' => 'fa fa-cube',
			  'fa-cubes' => 'fa fa-cubes',
			  'fa-behance' => 'fa fa-behance',
			  'fa-behance-square' => 'fa fa-behance-square',
			  'fa-steam' => 'fa fa-steam',
			  'fa-steam-square' => 'fa fa-steam-square',
			  'fa-recycle' => 'fa fa-recycle',
			  'fa-car' => 'fa fa-car',
			  'fa-taxi' => 'fa fa-taxi',
			  'fa-tree' => 'fa fa-tree',
			  'fa-spotify' => 'fa fa-spotify',
			  'fa-deviantart' => 'fa fa-deviantart',
			  'fa-soundcloud' => 'fa fa-soundcloud',
			  'fa-database' => 'fa fa-database',
			  'fa-file-pdf-o' => 'fa fa-file-pdf-o',
			  'fa-file-word-o' => 'fa fa-file-word-o',
			  'fa-file-excel-o' => 'fa fa-file-excel-o',
			  'fa-file-powerpoint-o' => 'fa fa-file-powerpoint-o',
			  'fa-file-image-o' => 'fa fa-file-image-o',
			  'fa-file-archive-o' => 'fa fa-file-archive-o',
			  'fa-file-audio-o' => 'fa fa-file-audio-o',
			  'fa-file-video-o' => 'fa fa-file-video-o',
			  'fa-file-code-o' => 'fa fa-file-code-o',
			  'fa-vine' => 'fa fa-vine',
			  'fa-codepen' => 'fa fa-codepen',
			  'fa-jsfiddle' => 'fa fa-jsfiddle',
			  'fa-life-ring' => 'fa fa-life-ring',
			  'fa-circle-o-notch' => 'fa fa-circle-o-notch',
			  'fa-rebel' => 'fa fa-rebel',
			  'fa-empire' => 'fa fa-empire',
			  'fa-git-square' => 'fa fa-git-square',
			  'fa-git' => 'fa fa-git',
			  'fa-hacker-news' => 'fa fa-hacker-news',
			  'fa-tencent-weibo' => 'fa fa-tencent-weibo',
			  'fa-qq' => 'fa fa-qq',
			  'fa-weixin' => 'fa fa-weixin',
			  'fa-paper-plane' => 'fa fa-paper-plane',
			  'fa-paper-plane-o' => 'fa fa-paper-plane-o',
			  'fa-history' => 'fa fa-history',
			  'fa-circle-thin' => 'fa fa-circle-thin',
			  'fa-header' => 'fa fa-header',
			  'fa-paragraph' => 'fa fa-paragraph',
			  'fa-sliders' => 'fa fa-sliders',
			  'fa-share-alt' => 'fa fa-share-alt',
			  'fa-share-alt-square' => 'fa fa-share-alt-square',
			  'fa-bomb' => 'fa fa-bomb',
			  'fa-futbol-o' => 'fa fa-futbol-o',
			  'fa-tty' => 'fa fa-tty',
			  'fa-binoculars' => 'fa fa-binoculars',
			  'fa-plug' => 'fa fa-plug',
			  'fa-slideshare' => 'fa fa-slideshare',
			  'fa-twitch' => 'fa fa-twitch',
			  'fa-yelp' => 'fa fa-yelp',
			  'fa-newspaper-o' => 'fa fa-newspaper-o',
			  'fa-wifi' => 'fa fa-wifi',
			  'fa-calculator' => 'fa fa-calculator',
			  'fa-paypal' => 'fa fa-paypal',
			  'fa-google-wallet' => 'fa fa-google-wallet',
			  'fa-cc-visa' => 'fa fa-cc-visa',
			  'fa-cc-mastercard' => 'fa fa-cc-mastercard',
			  'fa-cc-discover' => 'fa fa-cc-discover',
			  'fa-cc-amex' => 'fa fa-cc-amex',
			  'fa-cc-paypal' => 'fa fa-cc-paypal',
			  'fa-cc-stripe' => 'fa fa-cc-stripe',
			  'fa-bell-slash' => 'fa fa-bell-slash',
			  'fa-bell-slash-o' => 'fa fa-bell-slash-o',
			  'fa-trash' => 'fa fa-trash',
			  'fa-copyright' => 'fa fa-copyright',
			  'fa-at' => 'fa fa-at',
			  'fa-eyedropper' => 'fa fa-eyedropper',
			  'fa-paint-brush' => 'fa fa-paint-brush',
			  'fa-birthday-cake' => 'fa fa-birthday-cake',
			  'fa-area-chart' => 'fa fa-area-chart',
			  'fa-pie-chart' => 'fa fa-pie-chart',
			  'fa-line-chart' => 'fa fa-line-chart',
			  'fa-lastfm' => 'fa fa-lastfm',
			  'fa-lastfm-square' => 'fa fa-lastfm-square',
			  'fa-toggle-off' => 'fa fa-toggle-off',
			  'fa-toggle-on' => 'fa fa-toggle-on',
			  'fa-bicycle' => 'fa fa-bicycle',
			  'fa-bus' => 'fa fa-bus',
			  'fa-ioxhost' => 'fa fa-ioxhost',
			  'fa-angellist' => 'fa fa-angellist',
			  'fa-cc' => 'fa fa-cc',
			  'fa-ils' => 'fa fa-ils',
			  'fa-meanpath' => 'fa fa-meanpath',
			  'fa-buysellads' => 'fa fa-buysellads',
			  'fa-connectdevelop' => 'fa fa-connectdevelop',
			  'fa-dashcube' => 'fa fa-dashcube',
			  'fa-forumbee' => 'fa fa-forumbee',
			  'fa-leanpub' => 'fa fa-leanpub',
			  'fa-sellsy' => 'fa fa-sellsy',
			  'fa-shirtsinbulk' => 'fa fa-shirtsinbulk',
			  'fa-simplybuilt' => 'fa fa-simplybuilt',
			  'fa-skyatlas' => 'fa fa-skyatlas',
			  'fa-cart-plus' => 'fa fa-cart-plus',
			  'fa-cart-arrow-down' => 'fa fa-cart-arrow-down',
			  'fa-diamond' => 'fa fa-diamond',
			  'fa-ship' => 'fa fa-ship',
			  'fa-user-secret' => 'fa fa-user-secret',
			  'fa-motorcycle' => 'fa fa-motorcycle',
			  'fa-street-view' => 'fa fa-street-view',
			  'fa-heartbeat' => 'fa fa-heartbeat',
			  'fa-venus' => 'fa fa-venus',
			  'fa-mars' => 'fa fa-mars',
			  'fa-mercury' => 'fa fa-mercury',
			  'fa-transgender' => 'fa fa-transgender',
			  'fa-transgender-alt' => 'fa fa-transgender-alt',
			  'fa-venus-double' => 'fa fa-venus-double',
			  'fa-mars-double' => 'fa fa-mars-double',
			  'fa-venus-mars' => 'fa fa-venus-mars',
			  'fa-mars-stroke' => 'fa fa-mars-stroke',
			  'fa-mars-stroke-v' => 'fa fa-mars-stroke-v',
			  'fa-mars-stroke-h' => 'fa fa-mars-stroke-h',
			  'fa-neuter' => 'fa fa-neuter',
			  'fa-genderless' => 'fa fa-genderless',
			  'fa-facebook-official' => 'fa fa-facebook-official',
			  'fa-pinterest-p' => 'fa fa-pinterest-p',
			  'fa-whatsapp' => 'fa fa-whatsapp',
			  'fa-server' => 'fa fa-server',
			  'fa-user-plus' => 'fa fa-user-plus',
			  'fa-user-times' => 'fa fa-user-times',
			  'fa-bed' => 'fa fa-bed',
			  'fa-viacoin' => 'fa fa-viacoin',
			  'fa-train' => 'fa fa-train',
			  'fa-subway' => 'fa fa-subway',
			  'fa-medium' => 'fa fa-medium',
			  'fa-y-combinator' => 'fa fa-y-combinator',
			  'fa-optin-monster' => 'fa fa-optin-monster',
			  'fa-opencart' => 'fa fa-opencart',
			  'fa-expeditedssl' => 'fa fa-expeditedssl',
			  'fa-battery-full' => 'fa fa-battery-full',
			  'fa-battery-three-quarters' => 'fa fa-battery-three-quarters',
			  'fa-battery-half' => 'fa fa-battery-half',
			  'fa-battery-quarter' => 'fa fa-battery-quarter',
			  'fa-battery-empty' => 'fa fa-battery-empty',
			  'fa-mouse-pointer' => 'fa fa-mouse-pointer',
			  'fa-i-cursor' => 'fa fa-i-cursor',
			  'fa-object-group' => 'fa fa-object-group',
			  'fa-object-ungroup' => 'fa fa-object-ungroup',
			  'fa-sticky-note' => 'fa fa-sticky-note',
			  'fa-sticky-note-o' => 'fa fa-sticky-note-o',
			  'fa-cc-jcb' => 'fa fa-cc-jcb',
			  'fa-cc-diners-club' => 'fa fa-cc-diners-club',
			  'fa-clone' => 'fa fa-clone',
			  'fa-balance-scale' => 'fa fa-balance-scale',
			  'fa-hourglass-o' => 'fa fa-hourglass-o',
			  'fa-hourglass-start' => 'fa fa-hourglass-start',
			  'fa-hourglass-half' => 'fa fa-hourglass-half',
			  'fa-hourglass-end' => 'fa fa-hourglass-end',
			  'fa-hourglass' => 'fa fa-hourglass',
			  'fa-hand-rock-o' => 'fa fa-hand-rock-o',
			  'fa-hand-paper-o' => 'fa fa-hand-paper-o',
			  'fa-hand-scissors-o' => 'fa fa-hand-scissors-o',
			  'fa-hand-lizard-o' => 'fa fa-hand-lizard-o',
			  'fa-hand-spock-o' => 'fa fa-hand-spock-o',
			  'fa-hand-pointer-o' => 'fa fa-hand-pointer-o',
			  'fa-hand-peace-o' => 'fa fa-hand-peace-o',
			  'fa-trademark' => 'fa fa-trademark',
			  'fa-registered' => 'fa fa-registered',
			  'fa-creative-commons' => 'fa fa-creative-commons',
			  'fa-gg' => 'fa fa-gg',
			  'fa-gg-circle' => 'fa fa-gg-circle',
			  'fa-tripadvisor' => 'fa fa-tripadvisor',
			  'fa-odnoklassniki' => 'fa fa-odnoklassniki',
			  'fa-odnoklassniki-square' => 'fa fa-odnoklassniki-square',
			  'fa-get-pocket' => 'fa fa-get-pocket',
			  'fa-wikipedia-w' => 'fa fa-wikipedia-w',
			  'fa-safari' => 'fa fa-safari',
			  'fa-chrome' => 'fa fa-chrome',
			  'fa-firefox' => 'fa fa-firefox',
			  'fa-opera' => 'fa fa-opera',
			  'fa-internet-explorer' => 'fa fa-internet-explorer',
			  'fa-television' => 'fa fa-television',
			  'fa-contao' => 'fa fa-contao',
			  'fa-500px' => 'fa fa-500px',
			  'fa-amazon' => 'fa fa-amazon',
			  'fa-calendar-plus-o' => 'fa fa-calendar-plus-o',
			  'fa-calendar-minus-o' => 'fa fa-calendar-minus-o',
			  'fa-calendar-times-o' => 'fa fa-calendar-times-o',
			  'fa-calendar-check-o' => 'fa fa-calendar-check-o',
			  'fa-industry' => 'fa fa-industry',
			  'fa-map-pin' => 'fa fa-map-pin',
			  'fa-map-signs' => 'fa fa-map-signs',
			  'fa-map-o' => 'fa fa-map-o',
			  'fa-map' => 'fa fa-map',
			  'fa-commenting' => 'fa fa-commenting',
			  'fa-commenting-o' => 'fa fa-commenting-o',
			  'fa-houzz' => 'fa fa-houzz',
			  'fa-vimeo' => 'fa fa-vimeo',
			  'fa-black-tie' => 'fa fa-black-tie',
			  'fa-fonticons' => 'fa fa-fonticons',
			  'fa-reddit-alien' => 'fa fa-reddit-alien',
			  'fa-edge' => 'fa fa-edge',
			  'fa-credit-card-alt' => 'fa fa-credit-card-alt',
			  'fa-codiepie' => 'fa fa-codiepie',
			  'fa-modx' => 'fa fa-modx',
			  'fa-fort-awesome' => 'fa fa-fort-awesome',
			  'fa-usb' => 'fa fa-usb',
			  'fa-product-hunt' => 'fa fa-product-hunt',
			  'fa-mixcloud' => 'fa fa-mixcloud',
			  'fa-scribd' => 'fa fa-scribd',
			  'fa-pause-circle' => 'fa fa-pause-circle',
			  'fa-pause-circle-o' => 'fa fa-pause-circle-o',
			  'fa-stop-circle' => 'fa fa-stop-circle',
			  'fa-stop-circle-o' => 'fa fa-stop-circle-o',
			  'fa-shopping-bag' => 'fa fa-shopping-bag',
			  'fa-shopping-basket' => 'fa fa-shopping-basket',
			  'fa-hashtag' => 'fa fa-hashtag',
			  'fa-bluetooth' => 'fa fa-bluetooth',
			  'fa-bluetooth-b' => 'fa fa-bluetooth-b',
			  'fa-percent' => 'fa fa-percent',
			  'fa-gitlab' => 'fa fa-gitlab',
			  'fa-wpbeginner' => 'fa fa-wpbeginner',
			  'fa-wpforms' => 'fa fa-wpforms',
			  'fa-envira' => 'fa fa-envira',
			  'fa-universal-access' => 'fa fa-universal-access',
			  'fa-wheelchair-alt' => 'fa fa-wheelchair-alt',
			  'fa-question-circle-o' => 'fa fa-question-circle-o',
			  'fa-blind' => 'fa fa-blind',
			  'fa-audio-description' => 'fa fa-audio-description',
			  'fa-volume-control-phone' => 'fa fa-volume-control-phone',
			  'fa-braille' => 'fa fa-braille',
			  'fa-assistive-listening-systems' => 'fa fa-assistive-listening-systems',
			  'fa-american-sign-language-interpreting' => 'fa fa-american-sign-language-interpreting',
			  'fa-deaf' => 'fa fa-deaf',
			  'fa-glide' => 'fa fa-glide',
			  'fa-glide-g' => 'fa fa-glide-g',
			  'fa-sign-language' => 'fa fa-sign-language',
			  'fa-low-vision' => 'fa fa-low-vision',
			  'fa-viadeo' => 'fa fa-viadeo',
			  'fa-viadeo-square' => 'fa fa-viadeo-square',
			  'fa-snapchat' => 'fa fa-snapchat',
			  'fa-snapchat-ghost' => 'fa fa-snapchat-ghost',
			  'fa-snapchat-square' => 'fa fa-snapchat-square',
			  'fa-pied-piper' => 'fa fa-pied-piper',
			  'fa-first-order' => 'fa fa-first-order',
			  'fa-yoast' => 'fa fa-yoast',
			  'fa-themeisle' => 'fa fa-themeisle',
			  'fa-google-plus-official' => 'fa fa-google-plus-official',
			  'fa-font-awesome' => 'fa fa-font-awesome'

		);
			
	$steadysets = array(
			  'steadysets-icon-type' => 'steadysets-icon-type',
			  'steadysets-icon-box' => 'steadysets-icon-box',
			  'steadysets-icon-archive' => 'steadysets-icon-archive',
			  'steadysets-icon-envelope' => 'steadysets-icon-envelope',
			  'steadysets-icon-email' => 'steadysets-icon-email',
			  'steadysets-icon-files' => 'steadysets-icon-files',
			  'steadysets-icon-uniE606' => 'steadysets-icon-uniE606',
			  'steadysets-icon-connection-empty' => 'steadysets-icon-connection-empty',
			  'steadysets-icon-connection-25' => 'steadysets-icon-connection-25',
			  'steadysets-icon-connection-50' => 'steadysets-icon-connection-50',
			  'steadysets-icon-connection-75' => 'steadysets-icon-connection-75',
			  'steadysets-icon-connection-full' => 'steadysets-icon-connection-full',
			  'steadysets-icon-microphone' => 'steadysets-icon-microphone',
			  'steadysets-icon-microphone-off' => 'steadysets-icon-microphone-off',
			  'steadysets-icon-book' => 'steadysets-icon-book',
			  'steadysets-icon-cloud' => 'steadysets-icon-cloud',
			  'steadysets-icon-book2' => 'steadysets-icon-book2',
			  'steadysets-icon-star' => 'steadysets-icon-star',
			  'steadysets-icon-phone-portrait' => 'steadysets-icon-phone-portrait',
			  'steadysets-icon-phone-landscape' => 'steadysets-icon-phone-landscape',
			  'steadysets-icon-tablet' => 'steadysets-icon-tablet',
			  'steadysets-icon-tablet-landscape' => 'steadysets-icon-tablet-landscape',
			  'steadysets-icon-laptop' => 'steadysets-icon-laptop',
			  'steadysets-icon-uniE617' => 'steadysets-icon-uniE617',
			  'steadysets-icon-barbell' => 'steadysets-icon-barbell',
			  'steadysets-icon-stopwatch' => 'steadysets-icon-stopwatch',
			  'steadysets-icon-atom' => 'steadysets-icon-atom',
			  'steadysets-icon-syringe' => 'steadysets-icon-syringe',
			  'steadysets-icon-pencil' => 'steadysets-icon-pencil',
			  'steadysets-icon-chart' => 'steadysets-icon-chart',
			  'steadysets-icon-bars' => 'steadysets-icon-bars',
			  'steadysets-icon-cube' => 'steadysets-icon-cube',
			  'steadysets-icon-image' => 'steadysets-icon-image',
			  'steadysets-icon-crop' => 'steadysets-icon-crop',
			  'steadysets-icon-graph' => 'steadysets-icon-graph',
			  'steadysets-icon-select' => 'steadysets-icon-select',
			  'steadysets-icon-bucket' => 'steadysets-icon-bucket',
			  'steadysets-icon-mug' => 'steadysets-icon-mug',
			  'steadysets-icon-clipboard' => 'steadysets-icon-clipboard',
			  'steadysets-icon-lab' => 'steadysets-icon-lab',
			  'steadysets-icon-bones' => 'steadysets-icon-bones',
			  'steadysets-icon-pill' => 'steadysets-icon-pill',
			  'steadysets-icon-bolt' => 'steadysets-icon-bolt',
			  'steadysets-icon-health' => 'steadysets-icon-health',
			  'steadysets-icon-map-marker' => 'steadysets-icon-map-marker',
			  'steadysets-icon-stack' => 'steadysets-icon-stack',
			  'steadysets-icon-newspaper' => 'steadysets-icon-newspaper',
			  'steadysets-icon-uniE62F' => 'steadysets-icon-uniE62F',
			  'steadysets-icon-coffee' => 'steadysets-icon-coffee',
			  'steadysets-icon-bill' => 'steadysets-icon-bill',
			  'steadysets-icon-sun' => 'steadysets-icon-sun',
			  'steadysets-icon-vcard' => 'steadysets-icon-vcard',
			  'steadysets-icon-shorts' => 'steadysets-icon-shorts',
			  'steadysets-icon-drink' => 'steadysets-icon-drink',
			  'steadysets-icon-diamond' => 'steadysets-icon-diamond',
			  'steadysets-icon-bag' => 'steadysets-icon-bag',
			  'steadysets-icon-calculator' => 'steadysets-icon-calculator',
			  'steadysets-icon-credit-cards' => 'steadysets-icon-credit-cards',
			  'steadysets-icon-microwave-oven' => 'steadysets-icon-microwave-oven',
			  'steadysets-icon-camera' => 'steadysets-icon-camera',
			  'steadysets-icon-share' => 'steadysets-icon-share',
			  'steadysets-icon-bullhorn' => 'steadysets-icon-bullhorn',
			  'steadysets-icon-user' => 'steadysets-icon-user',
			  'steadysets-icon-users' => 'steadysets-icon-users',
			  'steadysets-icon-user2' => 'steadysets-icon-user2',
			  'steadysets-icon-users2' => 'steadysets-icon-users2',
			  'steadysets-icon-unlocked' => 'steadysets-icon-unlocked',
			  'steadysets-icon-unlocked2' => 'steadysets-icon-unlocked2',
			  'steadysets-icon-lock' => 'steadysets-icon-lock',
			  'steadysets-icon-forbidden' => 'steadysets-icon-forbidden',
			  'steadysets-icon-switch' => 'steadysets-icon-switch',
			  'steadysets-icon-meter' => 'steadysets-icon-meter',
			  'steadysets-icon-flag' => 'steadysets-icon-flag',
			  'steadysets-icon-home' => 'steadysets-icon-home',
			  'steadysets-icon-printer' => 'steadysets-icon-printer',
			  'steadysets-icon-clock' => 'steadysets-icon-clock',
			  'steadysets-icon-calendar' => 'steadysets-icon-calendar',
			  'steadysets-icon-comment' => 'steadysets-icon-comment',
			  'steadysets-icon-chat-3' => 'steadysets-icon-chat-3',
			  'steadysets-icon-chat-2' => 'steadysets-icon-chat-2',
			  'steadysets-icon-chat-1' => 'steadysets-icon-chat-1',
			  'steadysets-icon-chat' => 'steadysets-icon-chat',
			  'steadysets-icon-zoom-out' => 'steadysets-icon-zoom-out',
			  'steadysets-icon-zoom-in' => 'steadysets-icon-zoom-in',
			  'steadysets-icon-search' => 'steadysets-icon-search',
			  'steadysets-icon-trashcan' => 'steadysets-icon-trashcan',
			  'steadysets-icon-tag' => 'steadysets-icon-tag',
			  'steadysets-icon-download' => 'steadysets-icon-download',
			  'steadysets-icon-paperclip' => 'steadysets-icon-paperclip',
			  'steadysets-icon-checkbox' => 'steadysets-icon-checkbox',
			  'steadysets-icon-checkbox-checked' => 'steadysets-icon-checkbox-checked',
			  'steadysets-icon-checkmark' => 'steadysets-icon-checkmark',
			  'steadysets-icon-refresh' => 'steadysets-icon-refresh',
			  'steadysets-icon-reload' => 'steadysets-icon-reload',
			  'steadysets-icon-arrow-right' => 'steadysets-icon-arrow-right',
			  'steadysets-icon-arrow-down' => 'steadysets-icon-arrow-down',
			  'steadysets-icon-arrow-up' => 'steadysets-icon-arrow-up',
			  'steadysets-icon-arrow-left' => 'steadysets-icon-arrow-left',
			  'steadysets-icon-settings' => 'steadysets-icon-settings',
			  'steadysets-icon-battery-full' => 'steadysets-icon-battery-full',
			  'steadysets-icon-battery-75' => 'steadysets-icon-battery-75',
			  'steadysets-icon-battery-50' => 'steadysets-icon-battery-50',
			  'steadysets-icon-battery-25' => 'steadysets-icon-battery-25',
			  'steadysets-icon-battery-empty' => 'steadysets-icon-battery-empty',
			  'steadysets-icon-battery-charging' => 'steadysets-icon-battery-charging',
			  'steadysets-icon-uniE669' => 'steadysets-icon-uniE669',
			  'steadysets-icon-grid' => 'steadysets-icon-grid',
			  'steadysets-icon-list' => 'steadysets-icon-list',
			  'steadysets-icon-wifi-low' => 'steadysets-icon-wifi-low',
			  'steadysets-icon-folder-check' => 'steadysets-icon-folder-check',
			  'steadysets-icon-folder-settings' => 'steadysets-icon-folder-settings',
			  'steadysets-icon-folder-add' => 'steadysets-icon-folder-add',
			  'steadysets-icon-folder' => 'steadysets-icon-folder',
			  'steadysets-icon-window' => 'steadysets-icon-window',
			  'steadysets-icon-windows' => 'steadysets-icon-windows',
			  'steadysets-icon-browser' => 'steadysets-icon-browser',
			  'steadysets-icon-file-broken' => 'steadysets-icon-file-broken',
			  'steadysets-icon-align-justify' => 'steadysets-icon-align-justify',
			  'steadysets-icon-align-center' => 'steadysets-icon-align-center',
			  'steadysets-icon-align-right' => 'steadysets-icon-align-right',
			  'steadysets-icon-align-left' => 'steadysets-icon-align-left',
			  'steadysets-icon-file' => 'steadysets-icon-file',
			  'steadysets-icon-file-add' => 'steadysets-icon-file-add',
			  'steadysets-icon-file-settings' => 'steadysets-icon-file-settings',
			  'steadysets-icon-mute' => 'steadysets-icon-mute',
			  'steadysets-icon-heart' => 'steadysets-icon-heart',
			  'steadysets-icon-enter' => 'steadysets-icon-enter',
			  'steadysets-icon-volume-decrease' => 'steadysets-icon-volume-decrease',
			  'steadysets-icon-wifi-mid' => 'steadysets-icon-wifi-mid',
			  'steadysets-icon-volume' => 'steadysets-icon-volume',
			  'steadysets-icon-bookmark' => 'steadysets-icon-bookmark',
			  'steadysets-icon-screen' => 'steadysets-icon-screen',
			  'steadysets-icon-map' => 'steadysets-icon-map',
			  'steadysets-icon-measure' => 'steadysets-icon-measure',
			  'steadysets-icon-eyedropper' => 'steadysets-icon-eyedropper',
			  'steadysets-icon-support' => 'steadysets-icon-support',
			  'steadysets-icon-phone' => 'steadysets-icon-phone',
			  'steadysets-icon-email2' => 'steadysets-icon-email2',
			  'steadysets-icon-volume-increase' => 'steadysets-icon-volume-increase',
			  'steadysets-icon-wifi-full' => 'steadysets-icon-wifi-full'
		);

	$linecons = array(
			  'linecon-icon-heart' => 'linecon-icon-heart',
			  'linecon-icon-cloud' => 'linecon-icon-cloud',
			  'linecon-icon-star' => 'linecon-icon-star',
			  'linecon-icon-tv' => 'linecon-icon-tv',
			  'linecon-icon-sound' => 'linecon-icon-sound',
			  'linecon-icon-video' => 'linecon-icon-video',
			  'linecon-icon-trash' => 'linecon-icon-trash',
			  'linecon-icon-user' => 'linecon-icon-user',
			  'linecon-icon-key' => 'linecon-icon-key',
			  'linecon-icon-search' => 'linecon-icon-search',
			  'linecon-icon-eye' => 'linecon-icon-eye',
			  'linecon-icon-bubble' => 'linecon-icon-bubble',
			  'linecon-icon-stack' => 'linecon-icon-stack',
			  'linecon-icon-cup' => 'linecon-icon-cup',
			  'linecon-icon-phone' => 'linecon-icon-phone',
			  'linecon-icon-news' => 'linecon-icon-news',
			  'linecon-icon-mail' => 'linecon-icon-mail',
			  'linecon-icon-like' => 'linecon-icon-like',
			  'linecon-icon-photo' => 'linecon-icon-photo',
			  'linecon-icon-note' => 'linecon-icon-note',
			  'linecon-icon-food' => 'linecon-icon-food',
			  'linecon-icon-t-shirt' => 'linecon-icon-t-shirt',
			  'linecon-icon-fire' => 'linecon-icon-fire',
			  'linecon-icon-clip' => 'linecon-icon-clip',
			  'linecon-icon-shop' => 'linecon-icon-shop',
			  'linecon-icon-calendar' => 'linecon-icon-calendar',
			  'linecon-icon-wallet' => 'linecon-icon-wallet',
			  'linecon-icon-vynil' => 'linecon-icon-vynil',
			  'linecon-icon-truck' => 'linecon-icon-truck',
			  'linecon-icon-world' => 'linecon-icon-world',
			  'linecon-icon-clock' => 'linecon-icon-clock',
			  'linecon-icon-paperplane' => 'linecon-icon-paperplane',
			  'linecon-icon-params' => 'linecon-icon-params',
			  'linecon-icon-banknote' => 'linecon-icon-banknote',
			  'linecon-icon-data' => 'linecon-icon-data',
			  'linecon-icon-music' => 'linecon-icon-music',
			  'linecon-icon-megaphone' => 'linecon-icon-megaphone',
			  'linecon-icon-study' => 'linecon-icon-study',
			  'linecon-icon-lab' => 'linecon-icon-lab',
			  'linecon-icon-location' => 'linecon-icon-location',
			  'linecon-icon-display' => 'linecon-icon-display',
			  'linecon-icon-diamond' => 'linecon-icon-diamond',
			  'linecon-icon-pen' => 'linecon-icon-pen',
			  'linecon-icon-bulb' => 'linecon-icon-bulb',
			  'linecon-icon-lock' => 'linecon-icon-lock',
			  'linecon-icon-tag' => 'linecon-icon-tag',
			  'linecon-icon-camera' => 'linecon-icon-camera',
			  'linecon-icon-settings' => 'linecon-icon-settings'
		);
		
	// Icon list
	$icon_arr = array_merge($fa_icons, $steadysets, $linecons);

	vc_map( array(
	  "name" => __("Text With Icon", "js_composer"),
	  "base" => "text-with-icon",
	  "icon" => "icon-wpb-text-with-icon",
	  "category" => __('Nectar Elements', 'js_composer'),
	  "weight" => 1,
	  "description" => __('Add a text block with stylish icon', 'js_composer'),
	  "params" => array(
	    array(
		  "type" => "dropdown",
		  "heading" => __("Icon Type", "js_composer"),
		  "param_name" => "icon_type",
		  "admin_label" => true,
		  "value" => array(
			 "Font Icon" => "font_icon",
			 "Image Icon" => "image_icon",
		   ),
		  'save_always' => true,
		  "description" => __("Please select type of icon you would like for the text block", "js_composer")
		),
		array(
		  "type" => "dropdown",
		  "heading" => __("Icon", "js_composer"),
		  "param_name" => "icon",
		  "admin_label" => false,
		  "value" => $icon_arr,
		  'save_always' => true,
		  "description" => __("Please select the icon you wish to use", "js_composer"),
		  "dependency" => Array('element' => "icon_type", 'value' => array('font_icon'))
		),
	     array(
		  "type" => "dropdown",
		  "heading" => __("Color", "js_composer"),
		  "param_name" => "color",
		  "admin_label" => false,
		  'save_always' => true,
		  "value" => array(
			 "Accent-Color" => "Accent-Color",
			 "Extra-Color-1" => "Extra-Color-1",
			 "Extra-Color-2" => "Extra-Color-2",	
			 "Extra-Color-3" => "Extra-Color-3"
		   ),
		  "description" => __("Please select the color you wish for icon to display in", "js_composer"),
		  "dependency" => Array('element' => "icon_type", 'value' => array('font_icon'))
		),
		array(
			"type" => "attach_image",
			"class" => "",
			"heading" => "Icon Image",
			"param_name" => "icon_image",
			"value" => "",
			"description" => "",
			"dependency" => Array('element' => "icon_type", 'value' => array('image_icon'))
		),
		array(
	      "type" => "textarea_html",
	      "holder" => "div",
	      "heading" => __("Text Content", "js_composer"),
	      "param_name" => "content",
	      "value" => ''
	    )
	  )
	));



	vc_map( array(
	  "name" => __("Fancy Unordered List", "js_composer"),
	  "base" => "fancy-ul",
	  "icon" => "icon-wpb-fancy-ul",
	  "category" => __('Nectar Elements', 'js_composer'),
	  "weight" => 1,
	  "description" => __('Make your lists appealing', 'js_composer'),
	  "params" => array(
	    array(
		  "type" => "dropdown",
		  "heading" => __("Icon Type", "js_composer"),
		  "param_name" => "icon_type",
		  "admin_label" => false,
		  'save_always' => true,
		  "value" => array(
			 "Standard Dash" => "standard_dash",
			 "Font Icon" => "font_icon",
		   ),
		  "description" => __("Please select type of icon you would like for your fancy list", "js_composer")
		),
		array(
		  "type" => "dropdown",
		  "heading" => __("Icon", "js_composer"),
		  "param_name" => "icon",
		  "admin_label" => false,
		  "value" => $icon_arr,
		  'save_always' => true,
		  "description" => __("Please select the icon you wish to use", "js_composer"),
		  "dependency" => Array('element' => "icon_type", 'value' => array('font_icon'))
		),
	     array(
		  "type" => "dropdown",
		  "heading" => __("Color", "js_composer"),
		  "param_name" => "color",
		  "admin_label" => false,
		  'save_always' => true,
		  "value" => array(
			 "Accent-Color" => "Accent-Color",
			 "Extra-Color-1" => "Extra-Color-1",
			 "Extra-Color-2" => "Extra-Color-2",	
			 "Extra-Color-3" => "Extra-Color-3"
		   ),
		  "description" => __("Please select the color you wish for icon to display in", "js_composer"),
		),
		
		array(
			"type" => "checkbox",
			"class" => "",
			"heading" => "Enable Animation",
			"value" => array("Enable Animation?" => "true" ),
			"param_name" => "enable_animation",
			"description" => "This will cause your list items to animate in one by one"
		),

		array(
			"type" => "textfield",
			"class" => "",
			"heading" => "Animation Delay",
			"param_name" => "delay",
			"admin_label" => false,
			"description" => "",
			"dependency" => Array('element' => "enable_animation", 'not_empty' => true)
		),

		array(
	      "type" => "textarea_html",
	      "holder" => "div",
	      "heading" => __("Text Content", "js_composer"),
	      "param_name" => "content",
	      "value" => '',
	      "description" => "Please use the Unordered List button <img src='".get_template_directory_uri() ."/nectar/assets/img/icons/ul.png' alt='unordered list' /> on the editor to create the points of your fancy list."
	    )
	  )
	));

	
	// Morphing Outline
	class WPBakeryShortCode_Morphing_Outline extends WPBakeryShortCode { }
	vc_map( array(
			"name" => "Morphing Outline",
			"base" => "morphing_outline",
			"icon" => "icon-wpb-morphing-outline",
			"allowed_container_element" => 'vc_row',
			"category" => __('Nectar Elements', 'js_composer'),
			"description" => __('Wrap some text in a unqiue way to grab attention', 'js_composer'),
			"params" => array(
				array(
			      "type" => "textarea",
			      "holder" => "div",
			      "heading" => __("Text Content", "js_composer"),
			      "param_name" => "content",
			      "value" => '',
			      "description" => __("Enter the text that will be wrapped here", "js_composer"),
			      "admin_label" => false
			    ),
				array(
					"type" => "textfield",
					"holder" => "div",
					"class" => "",
					"heading" => "Border Thickness",
					"param_name" => "border_thickness",
					"description" => "Don't include \"px\" in your string - default is \"5\"",
					"admin_label" => false
				),
				array(
					"type" => "colorpicker",
					"class" => "",
					"heading" => "Starting Color",
					"param_name" => "starting_color",
					"value" => "",
					"description" => ""
				),
				array(
					"type" => "colorpicker",
					"class" => "",
					"heading" => "Hover Color",
					"param_name" => "hover_color",
					"value" => "",
					"description" => ""
				)

			)
	) );

	
	class WPBakeryShortCode_Nectar_Btn extends WPBakeryShortCode { }

	vc_map( array(
	  "name" => __("Button", "js_composer"),
	  "base" => "nectar_btn",
	  "icon" => "icon-wpb-btn",
	  "category" => __('Nectar Elements', 'js_composer'),
	  "weight" => 1,
	  "description" => __('Add a button', 'js_composer'),
	  "params" => array(

	  	array(
			'type' => 'dropdown',
			'heading' => __( 'Size', 'js_composer' ),
			'value' => array(
				__( 'Small', 'js_composer' ) => 'small',
				__( 'Medium', 'js_composer' ) => 'medium',
				__( 'Large', 'js_composer' ) => 'large',
				__( 'Jumbo', 'js_composer' ) => 'jumbo',
				__( 'Extra Jumbo', 'js_composer' ) => 'extra_jumbo',
			),
			'save_always' => true,
			'param_name' => 'size',
			'description' => __( 'Select your button size.', 'js_composer' ),
		),
		array(
	      "type" => "textfield",
	      "heading" => __("Link URL", "js_composer"),
	      "param_name" => "url",
	      "description" => __("The link for your button." , "js_composer")
	    ),
	    array(
	      "type" => "textfield",
	      "heading" => __("Text", "js_composer"),
	      "param_name" => "text",
	      "admin_label" => true,
	      "description" => __("The text for your button." , "js_composer")
	    ),
	    array(
			"type" => "checkbox",
			"class" => "",
			"heading" => __("Open Link In New Tab?", "js_composer"),
	     	"param_name" => "open_new_tab",
			"value" => Array(__("Yes", "js_composer") => 'true'),
			"description" => ""
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Style', 'js_composer' ),
			'value' => array(
				__( 'Regular', 'js_composer' ) => 'regular',
				__( 'Regular With Tilt', 'js_composer' ) => 'regular-tilt',
				__( 'See Through', 'js_composer' ) => 'see-through',
				__( 'See Through Solid On Hover', 'js_composer' ) => 'see-through-2',
				__( 'See Through Solid On Hover Alt', 'js_composer' ) => 'see-through-3',
				__( 'See Through 3D', 'js_composer' ) => 'see-through-3d',
			),		
			'save_always' => true,
			'param_name' => 'button_style',
			'description' => __( 'Select your button style.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Button Color', 'js_composer' ),
			'value' => array(
				"Accent-Color" => "Accent-Color",
				"Extra-Color-1" => "Extra-Color-1",
				"Extra-Color-2" => "Extra-Color-2",	
				"Extra-Color-3" => "Extra-Color-3"
			),
			'dependency' => array(
				'element' => 'button_style',
				'value' => array('regular-tilt'),
			),
			'save_always' => true,
			'param_name' => 'button_color',
			'description' => __( 'Select your button style.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Button Color', 'js_composer' ),
			'value' => array(
				"Accent-Color" => "Accent-Color",
				"Extra-Color-1" => "Extra-Color-1",
				"Extra-Color-2" => "Extra-Color-2",	
				"Extra-Color-3" => "Extra-Color-3",
				"Extra-Color-Gradient-1" => "extra-color-gradient-1",
		 		"Extra-Color-Gradient-2" => "extra-color-gradient-2"
			),
			'save_always' => true,
			'dependency' => array(
				'element' => 'button_style',
				'value' => array('regular','see-through'),
			),
			'param_name' => 'button_color_2',
			'description' => __( 'Select your button style.', 'js_composer' ),
		),
		array(
				"type" => "colorpicker",
				"class" => "",
				"heading" => "Color Override",
				"param_name" => "color_override",
				"value" => "",
				"description" => "won't take effect on gradient colored btns",	
			),
		array(
				"type" => "colorpicker",
				"class" => "",
				"heading" => "Hover BG Color",
				"param_name" => "hover_color_override",
				"dependency" => array('element' => "button_style", 'value' => array('see-through-2','see-through-3')),
				"value" => "",
				"description" => ""
			),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Hover Text Color', 'js_composer' ),
			'value' => array(
				__( 'Light', 'js_composer' ) => '#ffffff',
				__( 'Dark', 'js_composer' ) => '#000000'
			),
			'save_always' => true,
			'param_name' => 'hover_text_color_override',
			"dependency" => array('element' => "button_style", 'value' => array('see-through-2','see-through-3')),
			'description' => __( 'Select the color that will be used for the text on hover', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Icon library', 'js_composer' ),
			'value' => array(
				__( 'None', 'js_composer' ) => 'none',
				__( 'Font Awesome', 'js_composer' ) => 'fontawesome',
				__( 'Iconsmind', 'js_composer' ) => 'iconsmind',
				__( 'Steadysets', 'js_composer' ) => 'steadysets',
				__( 'Linecons', 'js_composer' ) => 'linecons',
			),
			'save_always' => true,
			'param_name' => 'icon_family',
			"dependency" => array('element' => "button_style", 'value' => array('regular','regular-tilt','see-through','see-through-2','see-through-3')),
			'description' => __( 'Select icon library.', 'js_composer' ),
		),
		array(
	      "type" => "iconpicker",
	      "heading" => __("Icon", "js_composer"),
	      "param_name" => "icon_fontawesome",
	      "settings" => array( "iconsPerPage" => 4000),
	      "dependency" => array('element' => "icon_family", 'emptyIcon' => false, 'value' => 'fontawesome'),
	      "description" => __("Select icon from library.", "js_composer")
	    ),
	    array(
	      "type" => "iconpicker",
	      "heading" => __("Icon", "js_composer"),
	      "param_name" => "icon_iconsmind",
	      "settings" => array( 'type' => 'iconsmind', 'emptyIcon' => false, "iconsPerPage" => 4000),
	      "dependency" => array('element' => "icon_family", 'value' => 'iconsmind'),
	      "description" => __("Select icon from library.", "js_composer")
	    ),
	    array(
	      "type" => "iconpicker",
	      "heading" => __("Icon", "js_composer"),
	      "param_name" => "icon_linecons",
	      "settings" => array( 'type' => 'linecons', 'emptyIcon' => false, "iconsPerPage" => 4000),
	      "dependency" => array('element' => "icon_family", 'value' => 'linecons'),
	      "description" => __("Select icon from library.", "js_composer")
	    ),
	    array(
	      "type" => "iconpicker",
	      "heading" => __("Icon", "js_composer"),
	      "param_name" => "icon_steadysets",
	      "settings" => array( 'type' => 'steadysets', 'emptyIcon' => false, "iconsPerPage" => 4000),
	      "dependency" => array('element' => "icon_family", 'value' => 'steadysets'),
	      "description" => __("Select icon from library.", "js_composer")
	    ),
	    array(
	      "type" => "textfield",
	      "heading" => __("Margin <span>Top</span>", "js_composer"),
	      "param_name" => "margin_top",
	      "edit_field_class" => "col-md-2",
	      "description" => __("." , "js_composer")
	    ),
		 array(
	      "type" => "textfield",
	      "heading" => __("<span>Right</span>", "js_composer"),
	      "param_name" => "margin_right",
	      "edit_field_class" => "col-md-2",
	      "description" => ''
	    ),
		array(
	      "type" => "textfield",
	      "heading" => __("<span>Bottom</span>", "js_composer"),
	      "param_name" => "margin_bottom",
	      "edit_field_class" => "col-md-2",
	      "description" => ''
	    ),
	    array(
	      "type" => "textfield",
	      "heading" => __("<span>Left</span>", "js_composer"),
	      "param_name" => "margin_left",
	      "edit_field_class" => "col-md-2",
	      "description" => ''
	    ),
	  )
	));

	
	class WPBakeryShortCode_Nectar_Icon extends WPBakeryShortCode { }

	vc_map( array(
	  "name" => __("Icon", "js_composer"),
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
	      "heading" => __("Icon", "js_composer"),
	      "param_name" => "icon_fontawesome",
	      "settings" => array( "iconsPerPage" => 4000),
	      "dependency" => array('element' => "icon_family", 'emptyIcon' => false, 'value' => 'fontawesome'),
	      "description" => __("Select icon from library.", "js_composer")
	    ),
	    array(
	      "type" => "iconpicker",
	      "heading" => __("Icon", "js_composer"),
	      "param_name" => "icon_iconsmind",
	      "settings" => array( 'type' => 'iconsmind', 'emptyIcon' => false, "iconsPerPage" => 4000),
	      "dependency" => array('element' => "icon_family", 'value' => 'iconsmind'),
	      "description" => __("Select icon from library.", "js_composer")
	    ),
	    array(
	      "type" => "iconpicker",
	      "heading" => __("Icon", "js_composer"),
	      "param_name" => "icon_linea",
	      "settings" => array( 'type' => 'linea', "emptyIcon" => true, "iconsPerPage" => 4000),
	      "dependency" => Array('element' => "icon_family", 'value' => 'linea'),
	      "description" => __("Select icon from library.", "js_composer")
	    ),
	    array(
	      "type" => "iconpicker",
	      "heading" => __("Icon", "js_composer"),
	      "param_name" => "icon_linecons",
	      "settings" => array( 'type' => 'linecons', 'emptyIcon' => false, "iconsPerPage" => 4000),
	      "dependency" => array('element' => "icon_family", 'value' => 'linecons'),
	      "description" => __("Select icon from library.", "js_composer")
	    ),
	    array(
	      "type" => "iconpicker",
	      "heading" => __("Icon", "js_composer"),
	      "param_name" => "icon_steadysets",
	      "settings" => array( 'type' => 'steadysets', 'emptyIcon' => false, "iconsPerPage" => 4000),
	      "dependency" => array('element' => "icon_family", 'value' => 'steadysets'),
	      "description" => __("Select icon from library.", "js_composer")
	    ),
	    array(
	      "type" => "textfield",
	      "heading" => __("Icon Size", "js_composer"),
	      "param_name" => "icon_size",
	      "description" => __("Don't include \"px\" in your string. e.g. 40 - the default is 50" , "js_composer")
	    ),
	    array(
			"type" => "checkbox",
			"class" => "",
			"heading" => __("Enable Animation", "js_composer"),
	     	"param_name" => "enable_animation",
			"value" => array(__("Yes", "js_composer") => 'true'),
			 "dependency" => array('element' => "icon_family", 'value' => 'linea'),
			"description" => "This will cause the icon to appear to draw itself. <strong>Will not activate when using a gradient color.</strong>"
		),
		 array(
	      "type" => "textfield",
	      "heading" => __("Animation Delay", "js_composer"),
	      "param_name" => "animation_delay",
	      "dependency" => array('element' => "enable_animation", 'not_empty' => true),
	      "description" => __("Enter delay (in milliseconds) if needed e.g. 150. This parameter comes in handy when creating the animate in \"one by one\" effect.", "js_composer")
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
				__('Border W/ Hover Animation', 'js_composer' ) => "border-animation"
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
				"Accent-Color" => "Accent-Color",
				"Extra-Color-1" => "Extra-Color-1",
				"Extra-Color-2" => "Extra-Color-2",	
				"Extra-Color-3" => "Extra-Color-3",
				"Extra-Color-Gradient-1" => "extra-color-gradient-1",
		 		"Extra-Color-Gradient-2" => "extra-color-gradient-2"
			),
			'save_always' => true,
			'param_name' => 'icon_color',
			'description' => __( 'Select your icon color.', 'js_composer' ),
		),
		 array(
	      "type" => "textfield",
	      "heading" => __("Link URL", "js_composer"),
	      "param_name" => "url",
	      "description" => __("The link for your button." , "js_composer")
	    ),
	    
	    array(
			"type" => "checkbox",
			"class" => "",
			"heading" => __("Open Link In New Tab?", "js_composer"),
	     	"param_name" => "open_new_tab",
			"value" => Array(__("Yes", "js_composer") => 'true'),
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
			),
			'std' => '20px',
			'save_always' => true,
			'param_name' => 'icon_padding',
		),
	    array(
	      "type" => "textfield",
	      "heading" => __("Margin <span>Top</span>", "js_composer"),
	      "param_name" => "margin_top",
	      "edit_field_class" => "col-md-2",
	      "description" => __("." , "js_composer")
	    ),
		 array(
	      "type" => "textfield",
	      "heading" => __("<span>Right</span>", "js_composer"),
	      "param_name" => "margin_right",
	      "edit_field_class" => "col-md-2",
	      "description" => ''
	    ),
		array(
	      "type" => "textfield",
	      "heading" => __("<span>Bottom</span>", "js_composer"),
	      "param_name" => "margin_bottom",
	      "edit_field_class" => "col-md-2",
	      "description" => ''
	    ),
	    array(
	      "type" => "textfield",
	      "heading" => __("<span>Left</span>", "js_composer"),
	      "param_name" => "margin_left",
	      "edit_field_class" => "col-md-2",
	      "description" => ''
	    ),
	  )
	));

	

}





add_filter( 'vc_iconpicker-type-iconsmind', 'vc_iconpicker_type_iconsmind' );

function vc_iconpicker_type_iconsmind( $icons ) {
	$iconsmind_icons = array(
		array("iconsmind-Aquarius" => "iconsmind-Aquarius"), 
		array("iconsmind-Aquarius-2" => "iconsmind-Aquarius-2"), 
		array("iconsmind-Aries" => "iconsmind-Aries"), 
		array("iconsmind-Aries-2" => "iconsmind-Aries-2"), 
		array("iconsmind-Cancer" => "iconsmind-Cancer"), 
		array("iconsmind-Cancer-2" => "iconsmind-Cancer-2"), 
		array("iconsmind-Capricorn" => "iconsmind-Capricorn"), 
		array("iconsmind-Capricorn-2" => "iconsmind-Capricorn-2"), 
		array("iconsmind-Gemini" => "iconsmind-Gemini"), 
		array("iconsmind-Gemini-2" => "iconsmind-Gemini-2"), 
		array("iconsmind-Leo" => "iconsmind-Leo"), 
		array("iconsmind-Leo-2" => "iconsmind-Leo-2"), 
		array("iconsmind-Libra" => "iconsmind-Libra"), 
		array("iconsmind-Libra-2" => "iconsmind-Libra-2"), 
		array("iconsmind-Pisces" => "iconsmind-Pisces"), 
		array("iconsmind-Pisces-2" => "iconsmind-Pisces-2"), 
		array("iconsmind-Sagittarus" => "iconsmind-Sagittarus"), 
		array("iconsmind-Sagittarus-2" => "iconsmind-Sagittarus-2"), 
		array("iconsmind-Scorpio" => "iconsmind-Scorpio"), 
		array("iconsmind-Scorpio-2" => "iconsmind-Scorpio-2"), 
		array("iconsmind-Taurus" => "iconsmind-Taurus"), 
		array("iconsmind-Taurus-2" => "iconsmind-Taurus-2"), 
		array("iconsmind-Virgo" => "iconsmind-Virgo"), 
		array("iconsmind-Virgo-2" => "iconsmind-Virgo-2"), 
		array("iconsmind-Add-Window" => "iconsmind-Add-Window"), 
		array("iconsmind-Approved-Window" => "iconsmind-Approved-Window"), 
		array("iconsmind-Block-Window" => "iconsmind-Block-Window"), 
		array("iconsmind-Close-Window" => "iconsmind-Close-Window"), 
		array("iconsmind-Code-Window" => "iconsmind-Code-Window"), 
		array("iconsmind-Delete-Window" => "iconsmind-Delete-Window"), 
		array("iconsmind-Download-Window" => "iconsmind-Download-Window"), 
		array("iconsmind-Duplicate-Window" => "iconsmind-Duplicate-Window"), 
		array("iconsmind-Error-404Window" => "iconsmind-Error-404Window"), 
		array("iconsmind-Favorite-Window" => "iconsmind-Favorite-Window"), 
		array("iconsmind-Font-Window" => "iconsmind-Font-Window"), 
		array("iconsmind-Full-ViewWindow" => "iconsmind-Full-ViewWindow"), 
		array("iconsmind-Height-Window" => "iconsmind-Height-Window"), 
		array("iconsmind-Home-Window" => "iconsmind-Home-Window"), 
		array("iconsmind-Info-Window" => "iconsmind-Info-Window"), 
		array("iconsmind-Loading-Window" => "iconsmind-Loading-Window"), 
		array("iconsmind-Lock-Window" => "iconsmind-Lock-Window"), 
		array("iconsmind-Love-Window" => "iconsmind-Love-Window"), 
		array("iconsmind-Maximize-Window" => "iconsmind-Maximize-Window"), 
		array("iconsmind-Minimize-Maximize-Close-Window" => "iconsmind-Minimize-Maximize-Close-Window"), 
		array("iconsmind-Minimize-Window" => "iconsmind-Minimize-Window"), 
		array("iconsmind-Navigation-LeftWindow" => "iconsmind-Navigation-LeftWindow"), 
		array("iconsmind-Navigation-RightWindow" => "iconsmind-Navigation-RightWindow"), 
		array("iconsmind-Network-Window" => "iconsmind-Network-Window"), 
		array("iconsmind-New-Tab" => "iconsmind-New-Tab"), 
		array("iconsmind-One-Window" => "iconsmind-One-Window"), 
		array("iconsmind-Refresh-Window" => "iconsmind-Refresh-Window"), 
		array("iconsmind-Remove-Window" => "iconsmind-Remove-Window"), 
		array("iconsmind-Restore-Window" => "iconsmind-Restore-Window"), 
		array("iconsmind-Save-Window" => "iconsmind-Save-Window"), 
		array("iconsmind-Settings-Window" => "iconsmind-Settings-Window"), 
		array("iconsmind-Share-Window" => "iconsmind-Share-Window"), 
		array("iconsmind-Sidebar-Window" => "iconsmind-Sidebar-Window"), 
		array("iconsmind-Split-FourSquareWindow" => "iconsmind-Split-FourSquareWindow"), 
		array("iconsmind-Split-Horizontal" => "iconsmind-Split-Horizontal"), 
		array("iconsmind-Split-Horizontal2Window" => "iconsmind-Split-Horizontal2Window"), 
		array("iconsmind-Split-Vertical" => "iconsmind-Split-Vertical"), 
		array("iconsmind-Split-Vertical2" => "iconsmind-Split-Vertical2"), 
		array("iconsmind-Split-Window" => "iconsmind-Split-Window"), 
		array("iconsmind-Time-Window" => "iconsmind-Time-Window"), 
		array("iconsmind-Touch-Window" => "iconsmind-Touch-Window"), 
		array("iconsmind-Two-Windows" => "iconsmind-Two-Windows"), 
		array("iconsmind-Upload-Window" => "iconsmind-Upload-Window"), 
		array("iconsmind-URL-Window" => "iconsmind-URL-Window"), 
		array("iconsmind-Warning-Window" => "iconsmind-Warning-Window"), 
		array("iconsmind-Width-Window" => "iconsmind-Width-Window"), 
		array("iconsmind-Window-2" => "iconsmind-Window-2"), 
		array("iconsmind-Windows-2" => "iconsmind-Windows-2"), 
		array("iconsmind-Autumn" => "iconsmind-Autumn"), 
		array("iconsmind-Celsius" => "iconsmind-Celsius"), 
		array("iconsmind-Cloud-Hail" => "iconsmind-Cloud-Hail"), 
		array("iconsmind-Cloud-Moon" => "iconsmind-Cloud-Moon"), 
		array("iconsmind-Cloud-Rain" => "iconsmind-Cloud-Rain"), 
		array("iconsmind-Cloud-Snow" => "iconsmind-Cloud-Snow"), 
		array("iconsmind-Cloud-Sun" => "iconsmind-Cloud-Sun"), 
		array("iconsmind-Clouds-Weather" => "iconsmind-Clouds-Weather"), 
		array("iconsmind-Cloud-Weather" => "iconsmind-Cloud-Weather"), 
		array("iconsmind-Drop" => "iconsmind-Drop"), 
		array("iconsmind-Dry" => "iconsmind-Dry"), 
		array("iconsmind-Fahrenheit" => "iconsmind-Fahrenheit"), 
		array("iconsmind-Fog-Day" => "iconsmind-Fog-Day"), 
		array("iconsmind-Fog-Night" => "iconsmind-Fog-Night"), 
		array("iconsmind-Full-Moon" => "iconsmind-Full-Moon"), 
		array("iconsmind-Half-Moon" => "iconsmind-Half-Moon"), 
		array("iconsmind-No-Drop" => "iconsmind-No-Drop"), 
		array("iconsmind-Rainbow" => "iconsmind-Rainbow"), 
		array("iconsmind-Rainbow-2" => "iconsmind-Rainbow-2"), 
		array("iconsmind-Rain-Drop" => "iconsmind-Rain-Drop"), 
		array("iconsmind-Sleet" => "iconsmind-Sleet"), 
		array("iconsmind-Snow" => "iconsmind-Snow"), 
		array("iconsmind-Snowflake" => "iconsmind-Snowflake"), 
		array("iconsmind-Snowflake-2" => "iconsmind-Snowflake-2"), 
		array("iconsmind-Snowflake-3" => "iconsmind-Snowflake-3"), 
		array("iconsmind-Snow-Storm" => "iconsmind-Snow-Storm"), 
		array("iconsmind-Spring" => "iconsmind-Spring"), 
		array("iconsmind-Storm" => "iconsmind-Storm"), 
		array("iconsmind-Summer" => "iconsmind-Summer"), 
		array("iconsmind-Sun" => "iconsmind-Sun"), 
		array("iconsmind-Sun-CloudyRain" => "iconsmind-Sun-CloudyRain"), 
		array("iconsmind-Sunrise" => "iconsmind-Sunrise"), 
		array("iconsmind-Sunset" => "iconsmind-Sunset"), 
		array("iconsmind-Temperature" => "iconsmind-Temperature"), 
		array("iconsmind-Temperature-2" => "iconsmind-Temperature-2"), 
		array("iconsmind-Thunder" => "iconsmind-Thunder"), 
		array("iconsmind-Thunderstorm" => "iconsmind-Thunderstorm"), 
		array("iconsmind-Twister" => "iconsmind-Twister"), 
		array("iconsmind-Umbrella-2" => "iconsmind-Umbrella-2"), 
		array("iconsmind-Umbrella-3" => "iconsmind-Umbrella-3"), 
		array("iconsmind-Wave" => "iconsmind-Wave"), 
		array("iconsmind-Wave-2" => "iconsmind-Wave-2"), 
		array("iconsmind-Windsock" => "iconsmind-Windsock"), 
		array("iconsmind-Wind-Turbine" => "iconsmind-Wind-Turbine"), 
		array("iconsmind-Windy" => "iconsmind-Windy"), 
		array("iconsmind-Winter" => "iconsmind-Winter"), 
		array("iconsmind-Winter-2" => "iconsmind-Winter-2"), 
		array("iconsmind-Cinema" => "iconsmind-Cinema"), 
		array("iconsmind-Clapperboard-Close" => "iconsmind-Clapperboard-Close"), 
		array("iconsmind-Clapperboard-Open" => "iconsmind-Clapperboard-Open"), 
		array("iconsmind-D-Eyeglasses" => "iconsmind-D-Eyeglasses"), 
		array("iconsmind-D-Eyeglasses2" => "iconsmind-D-Eyeglasses2"), 
		array("iconsmind-Director" => "iconsmind-Director"), 
		array("iconsmind-Film" => "iconsmind-Film"), 
		array("iconsmind-Film-Strip" => "iconsmind-Film-Strip"), 
		array("iconsmind-Film-Video" => "iconsmind-Film-Video"), 
		array("iconsmind-Flash-Video" => "iconsmind-Flash-Video"), 
		array("iconsmind-HD-Video" => "iconsmind-HD-Video"), 
		array("iconsmind-Movie" => "iconsmind-Movie"), 
		array("iconsmind-Old-TV" => "iconsmind-Old-TV"), 
		array("iconsmind-Reel" => "iconsmind-Reel"), 
		array("iconsmind-Tripod-andVideo" => "iconsmind-Tripod-andVideo"), 
		array("iconsmind-TV" => "iconsmind-TV"), 
		array("iconsmind-Video" => "iconsmind-Video"), 
		array("iconsmind-Video-2" => "iconsmind-Video-2"), 
		array("iconsmind-Video-3" => "iconsmind-Video-3"), 
		array("iconsmind-Video-4" => "iconsmind-Video-4"), 
		array("iconsmind-Video-5" => "iconsmind-Video-5"), 
		array("iconsmind-Video-6" => "iconsmind-Video-6"), 
		array("iconsmind-Video-Len" => "iconsmind-Video-Len"), 
		array("iconsmind-Video-Len2" => "iconsmind-Video-Len2"), 
		array("iconsmind-Video-Photographer" => "iconsmind-Video-Photographer"), 
		array("iconsmind-Video-Tripod" => "iconsmind-Video-Tripod"), 
		array("iconsmind-Affiliate" => "iconsmind-Affiliate"), 
		array("iconsmind-Background" => "iconsmind-Background"), 
		array("iconsmind-Billing" => "iconsmind-Billing"), 
		array("iconsmind-Control" => "iconsmind-Control"), 
		array("iconsmind-Control-2" => "iconsmind-Control-2"), 
		array("iconsmind-Crop-2" => "iconsmind-Crop-2"), 
		array("iconsmind-Dashboard" => "iconsmind-Dashboard"), 
		array("iconsmind-Duplicate-Layer" => "iconsmind-Duplicate-Layer"), 
		array("iconsmind-Filter-2" => "iconsmind-Filter-2"), 
		array("iconsmind-Gear" => "iconsmind-Gear"), 
		array("iconsmind-Gear-2" => "iconsmind-Gear-2"), 
		array("iconsmind-Gears" => "iconsmind-Gears"), 
		array("iconsmind-Gears-2" => "iconsmind-Gears-2"), 
		array("iconsmind-Information" => "iconsmind-Information"), 
		array("iconsmind-Layer-Backward" => "iconsmind-Layer-Backward"), 
		array("iconsmind-Layer-Forward" => "iconsmind-Layer-Forward"), 
		array("iconsmind-Library" => "iconsmind-Library"), 
		array("iconsmind-Loading" => "iconsmind-Loading"), 
		array("iconsmind-Loading-2" => "iconsmind-Loading-2"), 
		array("iconsmind-Loading-3" => "iconsmind-Loading-3"), 
		array("iconsmind-Magnifi-Glass" => "iconsmind-Magnifi-Glass"), 
		array("iconsmind-Magnifi-Glass2" => "iconsmind-Magnifi-Glass2"), 
		array("iconsmind-Magnifi-Glass22" => "iconsmind-Magnifi-Glass22"), 
		array("iconsmind-Mouse-Pointer" => "iconsmind-Mouse-Pointer"), 
		array("iconsmind-On-off" => "iconsmind-On-off"), 
		array("iconsmind-On-Off-2" => "iconsmind-On-Off-2"), 
		array("iconsmind-On-Off-3" => "iconsmind-On-Off-3"), 
		array("iconsmind-Preview" => "iconsmind-Preview"), 
		array("iconsmind-Pricing" => "iconsmind-Pricing"), 
		array("iconsmind-Profile" => "iconsmind-Profile"), 
		array("iconsmind-Project" => "iconsmind-Project"), 
		array("iconsmind-Rename" => "iconsmind-Rename"), 
		array("iconsmind-Repair" => "iconsmind-Repair"), 
		array("iconsmind-Save" => "iconsmind-Save"), 
		array("iconsmind-Scroller" => "iconsmind-Scroller"), 
		array("iconsmind-Scroller-2" => "iconsmind-Scroller-2"), 
		array("iconsmind-Share" => "iconsmind-Share"), 
		array("iconsmind-Statistic" => "iconsmind-Statistic"), 
		array("iconsmind-Support" => "iconsmind-Support"), 
		array("iconsmind-Switch" => "iconsmind-Switch"), 
		array("iconsmind-Upgrade" => "iconsmind-Upgrade"), 
		array("iconsmind-User" => "iconsmind-User"), 
		array("iconsmind-Wrench" => "iconsmind-Wrench"), 
		array("iconsmind-Air-Balloon" => "iconsmind-Air-Balloon"), 
		array("iconsmind-Airship" => "iconsmind-Airship"), 
		array("iconsmind-Bicycle" => "iconsmind-Bicycle"), 
		array("iconsmind-Bicycle-2" => "iconsmind-Bicycle-2"), 
		array("iconsmind-Bike-Helmet" => "iconsmind-Bike-Helmet"), 
		array("iconsmind-Bus" => "iconsmind-Bus"), 
		array("iconsmind-Bus-2" => "iconsmind-Bus-2"), 
		array("iconsmind-Cable-Car" => "iconsmind-Cable-Car"), 
		array("iconsmind-Car" => "iconsmind-Car"), 
		array("iconsmind-Car-2" => "iconsmind-Car-2"), 
		array("iconsmind-Car-3" => "iconsmind-Car-3"), 
		array("iconsmind-Car-Wheel" => "iconsmind-Car-Wheel"), 
		array("iconsmind-Gaugage" => "iconsmind-Gaugage"), 
		array("iconsmind-Gaugage-2" => "iconsmind-Gaugage-2"), 
		array("iconsmind-Helicopter" => "iconsmind-Helicopter"), 
		array("iconsmind-Helicopter-2" => "iconsmind-Helicopter-2"), 
		array("iconsmind-Helmet" => "iconsmind-Helmet"), 
		array("iconsmind-Jeep" => "iconsmind-Jeep"), 
		array("iconsmind-Jeep-2" => "iconsmind-Jeep-2"), 
		array("iconsmind-Jet" => "iconsmind-Jet"), 
		array("iconsmind-Motorcycle" => "iconsmind-Motorcycle"), 
		array("iconsmind-Plane" => "iconsmind-Plane"), 
		array("iconsmind-Plane-2" => "iconsmind-Plane-2"), 
		array("iconsmind-Road" => "iconsmind-Road"), 
		array("iconsmind-Road-2" => "iconsmind-Road-2"), 
		array("iconsmind-Rocket" => "iconsmind-Rocket"), 
		array("iconsmind-Sailing-Ship" => "iconsmind-Sailing-Ship"), 
		array("iconsmind-Scooter" => "iconsmind-Scooter"), 
		array("iconsmind-Scooter-Front" => "iconsmind-Scooter-Front"), 
		array("iconsmind-Ship" => "iconsmind-Ship"), 
		array("iconsmind-Ship-2" => "iconsmind-Ship-2"), 
		array("iconsmind-Skateboard" => "iconsmind-Skateboard"), 
		array("iconsmind-Skateboard-2" => "iconsmind-Skateboard-2"), 
		array("iconsmind-Taxi" => "iconsmind-Taxi"), 
		array("iconsmind-Taxi-2" => "iconsmind-Taxi-2"), 
		array("iconsmind-Taxi-Sign" => "iconsmind-Taxi-Sign"), 
		array("iconsmind-Tractor" => "iconsmind-Tractor"), 
		array("iconsmind-traffic-Light" => "iconsmind-traffic-Light"), 
		array("iconsmind-Traffic-Light2" => "iconsmind-Traffic-Light2"), 
		array("iconsmind-Train" => "iconsmind-Train"), 
		array("iconsmind-Train-2" => "iconsmind-Train-2"), 
		array("iconsmind-Tram" => "iconsmind-Tram"), 
		array("iconsmind-Truck" => "iconsmind-Truck"), 
		array("iconsmind-Yacht" => "iconsmind-Yacht"), 
		array("iconsmind-Double-Tap" => "iconsmind-Double-Tap"), 
		array("iconsmind-Drag" => "iconsmind-Drag"), 
		array("iconsmind-Drag-Down" => "iconsmind-Drag-Down"), 
		array("iconsmind-Drag-Left" => "iconsmind-Drag-Left"), 
		array("iconsmind-Drag-Right" => "iconsmind-Drag-Right"), 
		array("iconsmind-Drag-Up" => "iconsmind-Drag-Up"), 
		array("iconsmind-Finger-DragFourSides" => "iconsmind-Finger-DragFourSides"), 
		array("iconsmind-Finger-DragTwoSides" => "iconsmind-Finger-DragTwoSides"), 
		array("iconsmind-Five-Fingers" => "iconsmind-Five-Fingers"), 
		array("iconsmind-Five-FingersDrag" => "iconsmind-Five-FingersDrag"), 
		array("iconsmind-Five-FingersDrag2" => "iconsmind-Five-FingersDrag2"), 
		array("iconsmind-Five-FingersTouch" => "iconsmind-Five-FingersTouch"), 
		array("iconsmind-Flick" => "iconsmind-Flick"), 
		array("iconsmind-Four-Fingers" => "iconsmind-Four-Fingers"), 
		array("iconsmind-Four-FingersDrag" => "iconsmind-Four-FingersDrag"), 
		array("iconsmind-Four-FingersDrag2" => "iconsmind-Four-FingersDrag2"), 
		array("iconsmind-Four-FingersTouch" => "iconsmind-Four-FingersTouch"), 
		array("iconsmind-Hand-Touch" => "iconsmind-Hand-Touch"), 
		array("iconsmind-Hand-Touch2" => "iconsmind-Hand-Touch2"), 
		array("iconsmind-Hand-TouchSmartphone" => "iconsmind-Hand-TouchSmartphone"), 
		array("iconsmind-One-Finger" => "iconsmind-One-Finger"), 
		array("iconsmind-One-FingerTouch" => "iconsmind-One-FingerTouch"), 
		array("iconsmind-Pinch" => "iconsmind-Pinch"), 
		array("iconsmind-Press" => "iconsmind-Press"), 
		array("iconsmind-Rotate-Gesture" => "iconsmind-Rotate-Gesture"), 
		array("iconsmind-Rotate-Gesture2" => "iconsmind-Rotate-Gesture2"), 
		array("iconsmind-Rotate-Gesture3" => "iconsmind-Rotate-Gesture3"), 
		array("iconsmind-Scroll" => "iconsmind-Scroll"), 
		array("iconsmind-Scroll-Fast" => "iconsmind-Scroll-Fast"), 
		array("iconsmind-Spread" => "iconsmind-Spread"), 
		array("iconsmind-Star-Track" => "iconsmind-Star-Track"), 
		array("iconsmind-Tap" => "iconsmind-Tap"), 
		array("iconsmind-Three-Fingers" => "iconsmind-Three-Fingers"), 
		array("iconsmind-Three-FingersDrag" => "iconsmind-Three-FingersDrag"), 
		array("iconsmind-Three-FingersDrag2" => "iconsmind-Three-FingersDrag2"), 
		array("iconsmind-Three-FingersTouch" => "iconsmind-Three-FingersTouch"), 
		array("iconsmind-Thumb" => "iconsmind-Thumb"), 
		array("iconsmind-Two-Fingers" => "iconsmind-Two-Fingers"), 
		array("iconsmind-Two-FingersDrag" => "iconsmind-Two-FingersDrag"), 
		array("iconsmind-Two-FingersDrag2" => "iconsmind-Two-FingersDrag2"), 
		array("iconsmind-Two-FingersScroll" => "iconsmind-Two-FingersScroll"), 
		array("iconsmind-Two-FingersTouch" => "iconsmind-Two-FingersTouch"), 
		array("iconsmind-Zoom-Gesture" => "iconsmind-Zoom-Gesture"), 
		array("iconsmind-Alarm-Clock" => "iconsmind-Alarm-Clock"), 
		array("iconsmind-Alarm-Clock2" => "iconsmind-Alarm-Clock2"), 
		array("iconsmind-Calendar-Clock" => "iconsmind-Calendar-Clock"), 
		array("iconsmind-Clock" => "iconsmind-Clock"), 
		array("iconsmind-Clock-2" => "iconsmind-Clock-2"), 
		array("iconsmind-Clock-3" => "iconsmind-Clock-3"), 
		array("iconsmind-Clock-4" => "iconsmind-Clock-4"), 
		array("iconsmind-Clock-Back" => "iconsmind-Clock-Back"), 
		array("iconsmind-Clock-Forward" => "iconsmind-Clock-Forward"), 
		array("iconsmind-Hour" => "iconsmind-Hour"), 
		array("iconsmind-Old-Clock" => "iconsmind-Old-Clock"), 
		array("iconsmind-Over-Time" => "iconsmind-Over-Time"), 
		array("iconsmind-Over-Time2" => "iconsmind-Over-Time2"), 
		array("iconsmind-Sand-watch" => "iconsmind-Sand-watch"), 
		array("iconsmind-Sand-watch2" => "iconsmind-Sand-watch2"), 
		array("iconsmind-Stopwatch" => "iconsmind-Stopwatch"), 
		array("iconsmind-Stopwatch-2" => "iconsmind-Stopwatch-2"), 
		array("iconsmind-Time-Backup" => "iconsmind-Time-Backup"), 
		array("iconsmind-Time-Fire" => "iconsmind-Time-Fire"), 
		array("iconsmind-Time-Machine" => "iconsmind-Time-Machine"), 
		array("iconsmind-Timer" => "iconsmind-Timer"), 
		array("iconsmind-Watch" => "iconsmind-Watch"), 
		array("iconsmind-Watch-2" => "iconsmind-Watch-2"), 
		array("iconsmind-Watch-3" => "iconsmind-Watch-3"), 
		array("iconsmind-A-Z" => "iconsmind-A-Z"), 
		array("iconsmind-Bold-Text" => "iconsmind-Bold-Text"), 
		array("iconsmind-Bulleted-List" => "iconsmind-Bulleted-List"), 
		array("iconsmind-Font-Color" => "iconsmind-Font-Color"), 
		array("iconsmind-Font-Name" => "iconsmind-Font-Name"), 
		array("iconsmind-Font-Size" => "iconsmind-Font-Size"), 
		array("iconsmind-Font-Style" => "iconsmind-Font-Style"), 
		array("iconsmind-Font-StyleSubscript" => "iconsmind-Font-StyleSubscript"), 
		array("iconsmind-Font-StyleSuperscript" => "iconsmind-Font-StyleSuperscript"), 
		array("iconsmind-Function" => "iconsmind-Function"), 
		array("iconsmind-Italic-Text" => "iconsmind-Italic-Text"), 
		array("iconsmind-Line-SpacingText" => "iconsmind-Line-SpacingText"), 
		array("iconsmind-Lowercase-Text" => "iconsmind-Lowercase-Text"), 
		array("iconsmind-Normal-Text" => "iconsmind-Normal-Text"), 
		array("iconsmind-Numbering-List" => "iconsmind-Numbering-List"), 
		array("iconsmind-Strikethrough-Text" => "iconsmind-Strikethrough-Text"), 
		array("iconsmind-Sum" => "iconsmind-Sum"), 
		array("iconsmind-Text-Box" => "iconsmind-Text-Box"), 
		array("iconsmind-Text-Effect" => "iconsmind-Text-Effect"), 
		array("iconsmind-Text-HighlightColor" => "iconsmind-Text-HighlightColor"), 
		array("iconsmind-Text-Paragraph" => "iconsmind-Text-Paragraph"), 
		array("iconsmind-Under-LineText" => "iconsmind-Under-LineText"), 
		array("iconsmind-Uppercase-Text" => "iconsmind-Uppercase-Text"), 
		array("iconsmind-Wrap-Text" => "iconsmind-Wrap-Text"), 
		array("iconsmind-Z-A" => "iconsmind-Z-A"), 
		array("iconsmind-Aerobics" => "iconsmind-Aerobics"), 
		array("iconsmind-Aerobics-2" => "iconsmind-Aerobics-2"), 
		array("iconsmind-Aerobics-3" => "iconsmind-Aerobics-3"), 
		array("iconsmind-Archery" => "iconsmind-Archery"), 
		array("iconsmind-Archery-2" => "iconsmind-Archery-2"), 
		array("iconsmind-Ballet-Shoes" => "iconsmind-Ballet-Shoes"), 
		array("iconsmind-Baseball" => "iconsmind-Baseball"), 
		array("iconsmind-Basket-Ball" => "iconsmind-Basket-Ball"), 
		array("iconsmind-Bodybuilding" => "iconsmind-Bodybuilding"), 
		array("iconsmind-Bowling" => "iconsmind-Bowling"), 
		array("iconsmind-Bowling-2" => "iconsmind-Bowling-2"), 
		array("iconsmind-Box" => "iconsmind-Box"), 
		array("iconsmind-Chess" => "iconsmind-Chess"), 
		array("iconsmind-Cricket" => "iconsmind-Cricket"), 
		array("iconsmind-Dumbbell" => "iconsmind-Dumbbell"), 
		array("iconsmind-Football" => "iconsmind-Football"), 
		array("iconsmind-Football-2" => "iconsmind-Football-2"), 
		array("iconsmind-Footprint" => "iconsmind-Footprint"), 
		array("iconsmind-Footprint-2" => "iconsmind-Footprint-2"), 
		array("iconsmind-Goggles" => "iconsmind-Goggles"), 
		array("iconsmind-Golf" => "iconsmind-Golf"), 
		array("iconsmind-Golf-2" => "iconsmind-Golf-2"), 
		array("iconsmind-Gymnastics" => "iconsmind-Gymnastics"), 
		array("iconsmind-Hokey" => "iconsmind-Hokey"), 
		array("iconsmind-Jump-Rope" => "iconsmind-Jump-Rope"), 
		array("iconsmind-Life-Jacket" => "iconsmind-Life-Jacket"), 
		array("iconsmind-Medal" => "iconsmind-Medal"), 
		array("iconsmind-Medal-2" => "iconsmind-Medal-2"), 
		array("iconsmind-Medal-3" => "iconsmind-Medal-3"), 
		array("iconsmind-Parasailing" => "iconsmind-Parasailing"), 
		array("iconsmind-Pilates" => "iconsmind-Pilates"), 
		array("iconsmind-Pilates-2" => "iconsmind-Pilates-2"), 
		array("iconsmind-Pilates-3" => "iconsmind-Pilates-3"), 
		array("iconsmind-Ping-Pong" => "iconsmind-Ping-Pong"), 
		array("iconsmind-Rafting" => "iconsmind-Rafting"), 
		array("iconsmind-Running" => "iconsmind-Running"), 
		array("iconsmind-Running-Shoes" => "iconsmind-Running-Shoes"), 
		array("iconsmind-Skate-Shoes" => "iconsmind-Skate-Shoes"), 
		array("iconsmind-Ski" => "iconsmind-Ski"), 
		array("iconsmind-Skydiving" => "iconsmind-Skydiving"), 
		array("iconsmind-Snorkel" => "iconsmind-Snorkel"), 
		array("iconsmind-Soccer-Ball" => "iconsmind-Soccer-Ball"), 
		array("iconsmind-Soccer-Shoes" => "iconsmind-Soccer-Shoes"), 
		array("iconsmind-Swimming" => "iconsmind-Swimming"), 
		array("iconsmind-Tennis" => "iconsmind-Tennis"), 
		array("iconsmind-Tennis-Ball" => "iconsmind-Tennis-Ball"), 
		array("iconsmind-Trekking" => "iconsmind-Trekking"), 
		array("iconsmind-Trophy" => "iconsmind-Trophy"), 
		array("iconsmind-Trophy-2" => "iconsmind-Trophy-2"), 
		array("iconsmind-Volleyball" => "iconsmind-Volleyball"), 
		array("iconsmind-weight-Lift" => "iconsmind-weight-Lift"), 
		array("iconsmind-Speach-Bubble" => "iconsmind-Speach-Bubble"), 
		array("iconsmind-Speach-Bubble2" => "iconsmind-Speach-Bubble2"), 
		array("iconsmind-Speach-Bubble3" => "iconsmind-Speach-Bubble3"), 
		array("iconsmind-Speach-Bubble4" => "iconsmind-Speach-Bubble4"), 
		array("iconsmind-Speach-Bubble5" => "iconsmind-Speach-Bubble5"), 
		array("iconsmind-Speach-Bubble6" => "iconsmind-Speach-Bubble6"), 
		array("iconsmind-Speach-Bubble7" => "iconsmind-Speach-Bubble7"), 
		array("iconsmind-Speach-Bubble8" => "iconsmind-Speach-Bubble8"), 
		array("iconsmind-Speach-Bubble9" => "iconsmind-Speach-Bubble9"), 
		array("iconsmind-Speach-Bubble10" => "iconsmind-Speach-Bubble10"), 
		array("iconsmind-Speach-Bubble11" => "iconsmind-Speach-Bubble11"), 
		array("iconsmind-Speach-Bubble12" => "iconsmind-Speach-Bubble12"), 
		array("iconsmind-Speach-Bubble13" => "iconsmind-Speach-Bubble13"), 
		array("iconsmind-Speach-BubbleAsking" => "iconsmind-Speach-BubbleAsking"), 
		array("iconsmind-Speach-BubbleComic" => "iconsmind-Speach-BubbleComic"), 
		array("iconsmind-Speach-BubbleComic2" => "iconsmind-Speach-BubbleComic2"), 
		array("iconsmind-Speach-BubbleComic3" => "iconsmind-Speach-BubbleComic3"), 
		array("iconsmind-Speach-BubbleComic4" => "iconsmind-Speach-BubbleComic4"), 
		array("iconsmind-Speach-BubbleDialog" => "iconsmind-Speach-BubbleDialog"), 
		array("iconsmind-Speach-Bubbles" => "iconsmind-Speach-Bubbles"), 
		array("iconsmind-Aim" => "iconsmind-Aim"), 
		array("iconsmind-Ask" => "iconsmind-Ask"), 
		array("iconsmind-Bebo" => "iconsmind-Bebo"), 
		array("iconsmind-Behance" => "iconsmind-Behance"), 
		array("iconsmind-Betvibes" => "iconsmind-Betvibes"), 
		array("iconsmind-Bing" => "iconsmind-Bing"), 
		array("iconsmind-Blinklist" => "iconsmind-Blinklist"), 
		array("iconsmind-Blogger" => "iconsmind-Blogger"), 
		array("iconsmind-Brightkite" => "iconsmind-Brightkite"), 
		array("iconsmind-Delicious" => "iconsmind-Delicious"), 
		array("iconsmind-Deviantart" => "iconsmind-Deviantart"), 
		array("iconsmind-Digg" => "iconsmind-Digg"), 
		array("iconsmind-Diigo" => "iconsmind-Diigo"), 
		array("iconsmind-Doplr" => "iconsmind-Doplr"), 
		array("iconsmind-Dribble" => "iconsmind-Dribble"), 
		array("iconsmind-Email" => "iconsmind-Email"), 
		array("iconsmind-Evernote" => "iconsmind-Evernote"), 
		array("iconsmind-Facebook" => "iconsmind-Facebook"), 
		array("iconsmind-Facebook-2" => "iconsmind-Facebook-2"), 
		array("iconsmind-Feedburner" => "iconsmind-Feedburner"), 
		array("iconsmind-Flickr" => "iconsmind-Flickr"), 
		array("iconsmind-Formspring" => "iconsmind-Formspring"), 
		array("iconsmind-Forsquare" => "iconsmind-Forsquare"), 
		array("iconsmind-Friendfeed" => "iconsmind-Friendfeed"), 
		array("iconsmind-Friendster" => "iconsmind-Friendster"), 
		array("iconsmind-Furl" => "iconsmind-Furl"), 
		array("iconsmind-Google" => "iconsmind-Google"), 
		array("iconsmind-Google-Buzz" => "iconsmind-Google-Buzz"), 
		array("iconsmind-Google-Plus" => "iconsmind-Google-Plus"), 
		array("iconsmind-Gowalla" => "iconsmind-Gowalla"), 
		array("iconsmind-ICQ" => "iconsmind-ICQ"), 
		array("iconsmind-ImDB" => "iconsmind-ImDB"), 
		array("iconsmind-Instagram" => "iconsmind-Instagram"), 
		array("iconsmind-Last-FM" => "iconsmind-Last-FM"), 
		array("iconsmind-Like" => "iconsmind-Like"), 
		array("iconsmind-Like-2" => "iconsmind-Like-2"), 
		array("iconsmind-Linkedin" => "iconsmind-Linkedin"), 
		array("iconsmind-Linkedin-2" => "iconsmind-Linkedin-2"), 
		array("iconsmind-Livejournal" => "iconsmind-Livejournal"), 
		array("iconsmind-Metacafe" => "iconsmind-Metacafe"), 
		array("iconsmind-Mixx" => "iconsmind-Mixx"), 
		array("iconsmind-Myspace" => "iconsmind-Myspace"), 
		array("iconsmind-Newsvine" => "iconsmind-Newsvine"), 
		array("iconsmind-Orkut" => "iconsmind-Orkut"), 
		array("iconsmind-Picasa" => "iconsmind-Picasa"), 
		array("iconsmind-Pinterest" => "iconsmind-Pinterest"), 
		array("iconsmind-Plaxo" => "iconsmind-Plaxo"), 
		array("iconsmind-Plurk" => "iconsmind-Plurk"), 
		array("iconsmind-Posterous" => "iconsmind-Posterous"), 
		array("iconsmind-QIK" => "iconsmind-QIK"), 
		array("iconsmind-Reddit" => "iconsmind-Reddit"), 
		array("iconsmind-Reverbnation" => "iconsmind-Reverbnation"), 
		array("iconsmind-RSS" => "iconsmind-RSS"), 
		array("iconsmind-Sharethis" => "iconsmind-Sharethis"), 
		array("iconsmind-Shoutwire" => "iconsmind-Shoutwire"), 
		array("iconsmind-Skype" => "iconsmind-Skype"), 
		array("iconsmind-Soundcloud" => "iconsmind-Soundcloud"), 
		array("iconsmind-Spurl" => "iconsmind-Spurl"), 
		array("iconsmind-Stumbleupon" => "iconsmind-Stumbleupon"), 
		array("iconsmind-Technorati" => "iconsmind-Technorati"), 
		array("iconsmind-Tumblr" => "iconsmind-Tumblr"), 
		array("iconsmind-Twitter" => "iconsmind-Twitter"), 
		array("iconsmind-Twitter-2" => "iconsmind-Twitter-2"), 
		array("iconsmind-Unlike" => "iconsmind-Unlike"), 
		array("iconsmind-Unlike-2" => "iconsmind-Unlike-2"), 
		array("iconsmind-Ustream" => "iconsmind-Ustream"), 
		array("iconsmind-Viddler" => "iconsmind-Viddler"), 
		array("iconsmind-Vimeo" => "iconsmind-Vimeo"), 
		array("iconsmind-Wordpress" => "iconsmind-Wordpress"), 
		array("iconsmind-Xanga" => "iconsmind-Xanga"), 
		array("iconsmind-Xing" => "iconsmind-Xing"), 
		array("iconsmind-Yahoo" => "iconsmind-Yahoo"), 
		array("iconsmind-Yahoo-Buzz" => "iconsmind-Yahoo-Buzz"), 
		array("iconsmind-Yelp" => "iconsmind-Yelp"), 
		array("iconsmind-Youtube" => "iconsmind-Youtube"), 
		array("iconsmind-Zootool" => "iconsmind-Zootool"), 
		array("iconsmind-Bisexual" => "iconsmind-Bisexual"), 
		array("iconsmind-Cancer2" => "iconsmind-Cancer2"), 
		array("iconsmind-Couple-Sign" => "iconsmind-Couple-Sign"), 
		array("iconsmind-David-Star" => "iconsmind-David-Star"), 
		array("iconsmind-Family-Sign" => "iconsmind-Family-Sign"), 
		array("iconsmind-Female-2" => "iconsmind-Female-2"), 
		array("iconsmind-Gey" => "iconsmind-Gey"), 
		array("iconsmind-Heart" => "iconsmind-Heart"), 
		array("iconsmind-Homosexual" => "iconsmind-Homosexual"), 
		array("iconsmind-Inifity" => "iconsmind-Inifity"), 
		array("iconsmind-Lesbian" => "iconsmind-Lesbian"), 
		array("iconsmind-Lesbians" => "iconsmind-Lesbians"), 
		array("iconsmind-Love" => "iconsmind-Love"), 
		array("iconsmind-Male-2" => "iconsmind-Male-2"), 
		array("iconsmind-Men" => "iconsmind-Men"), 
		array("iconsmind-No-Smoking" => "iconsmind-No-Smoking"), 
		array("iconsmind-Paw" => "iconsmind-Paw"), 
		array("iconsmind-Quotes" => "iconsmind-Quotes"), 
		array("iconsmind-Quotes-2" => "iconsmind-Quotes-2"), 
		array("iconsmind-Redirect" => "iconsmind-Redirect"), 
		array("iconsmind-Retweet" => "iconsmind-Retweet"), 
		array("iconsmind-Ribbon" => "iconsmind-Ribbon"), 
		array("iconsmind-Ribbon-2" => "iconsmind-Ribbon-2"), 
		array("iconsmind-Ribbon-3" => "iconsmind-Ribbon-3"), 
		array("iconsmind-Sexual" => "iconsmind-Sexual"), 
		array("iconsmind-Smoking-Area" => "iconsmind-Smoking-Area"), 
		array("iconsmind-Trace" => "iconsmind-Trace"), 
		array("iconsmind-Venn-Diagram" => "iconsmind-Venn-Diagram"), 
		array("iconsmind-Wheelchair" => "iconsmind-Wheelchair"), 
		array("iconsmind-Women" => "iconsmind-Women"), 
		array("iconsmind-Ying-Yang" => "iconsmind-Ying-Yang"), 
		array("iconsmind-Add-Bag" => "iconsmind-Add-Bag"), 
		array("iconsmind-Add-Basket" => "iconsmind-Add-Basket"), 
		array("iconsmind-Add-Cart" => "iconsmind-Add-Cart"), 
		array("iconsmind-Bag-Coins" => "iconsmind-Bag-Coins"), 
		array("iconsmind-Bag-Items" => "iconsmind-Bag-Items"), 
		array("iconsmind-Bag-Quantity" => "iconsmind-Bag-Quantity"), 
		array("iconsmind-Bar-Code" => "iconsmind-Bar-Code"), 
		array("iconsmind-Basket-Coins" => "iconsmind-Basket-Coins"), 
		array("iconsmind-Basket-Items" => "iconsmind-Basket-Items"), 
		array("iconsmind-Basket-Quantity" => "iconsmind-Basket-Quantity"), 
		array("iconsmind-Bitcoin" => "iconsmind-Bitcoin"), 
		array("iconsmind-Car-Coins" => "iconsmind-Car-Coins"), 
		array("iconsmind-Car-Items" => "iconsmind-Car-Items"), 
		array("iconsmind-CartQuantity" => "iconsmind-CartQuantity"), 
		array("iconsmind-Cash-Register" => "iconsmind-Cash-Register"), 
		array("iconsmind-Cash-register2" => "iconsmind-Cash-register2"), 
		array("iconsmind-Checkout" => "iconsmind-Checkout"), 
		array("iconsmind-Checkout-Bag" => "iconsmind-Checkout-Bag"), 
		array("iconsmind-Checkout-Basket" => "iconsmind-Checkout-Basket"), 
		array("iconsmind-Full-Basket" => "iconsmind-Full-Basket"), 
		array("iconsmind-Full-Cart" => "iconsmind-Full-Cart"), 
		array("iconsmind-Fyll-Bag" => "iconsmind-Fyll-Bag"), 
		array("iconsmind-Home" => "iconsmind-Home"), 
		array("iconsmind-Password-2shopping" => "iconsmind-Password-2shopping"), 
		array("iconsmind-Password-shopping" => "iconsmind-Password-shopping"), 
		array("iconsmind-QR-Code" => "iconsmind-QR-Code"), 
		array("iconsmind-Receipt" => "iconsmind-Receipt"), 
		array("iconsmind-Receipt-2" => "iconsmind-Receipt-2"), 
		array("iconsmind-Receipt-3" => "iconsmind-Receipt-3"), 
		array("iconsmind-Receipt-4" => "iconsmind-Receipt-4"), 
		array("iconsmind-Remove-Bag" => "iconsmind-Remove-Bag"), 
		array("iconsmind-Remove-Basket" => "iconsmind-Remove-Basket"), 
		array("iconsmind-Remove-Cart" => "iconsmind-Remove-Cart"), 
		array("iconsmind-Shop" => "iconsmind-Shop"), 
		array("iconsmind-Shop-2" => "iconsmind-Shop-2"), 
		array("iconsmind-Shop-3" => "iconsmind-Shop-3"), 
		array("iconsmind-Shop-4" => "iconsmind-Shop-4"), 
		array("iconsmind-Shopping-Bag" => "iconsmind-Shopping-Bag"), 
		array("iconsmind-Shopping-Basket" => "iconsmind-Shopping-Basket"), 
		array("iconsmind-Shopping-Cart" => "iconsmind-Shopping-Cart"), 
		array("iconsmind-Tag-2" => "iconsmind-Tag-2"), 
		array("iconsmind-Tag-3" => "iconsmind-Tag-3"), 
		array("iconsmind-Tag-4" => "iconsmind-Tag-4"), 
		array("iconsmind-Tag-5" => "iconsmind-Tag-5"), 
		array("iconsmind-This-SideUp" => "iconsmind-This-SideUp"), 
		array("iconsmind-Broke-Link2" => "iconsmind-Broke-Link2"), 
		array("iconsmind-Coding" => "iconsmind-Coding"), 
		array("iconsmind-Consulting" => "iconsmind-Consulting"), 
		array("iconsmind-Copyright" => "iconsmind-Copyright"), 
		array("iconsmind-Idea-2" => "iconsmind-Idea-2"), 
		array("iconsmind-Idea-3" => "iconsmind-Idea-3"), 
		array("iconsmind-Idea-4" => "iconsmind-Idea-4"), 
		array("iconsmind-Idea-5" => "iconsmind-Idea-5"), 
		array("iconsmind-Internet" => "iconsmind-Internet"), 
		array("iconsmind-Internet-2" => "iconsmind-Internet-2"), 
		array("iconsmind-Link-2" => "iconsmind-Link-2"), 
		array("iconsmind-Management" => "iconsmind-Management"), 
		array("iconsmind-Monitor-Analytics" => "iconsmind-Monitor-Analytics"), 
		array("iconsmind-Monitoring" => "iconsmind-Monitoring"), 
		array("iconsmind-Optimization" => "iconsmind-Optimization"), 
		array("iconsmind-Search-People" => "iconsmind-Search-People"), 
		array("iconsmind-Tag" => "iconsmind-Tag"), 
		array("iconsmind-Target" => "iconsmind-Target"), 
		array("iconsmind-Target-Market" => "iconsmind-Target-Market"), 
		array("iconsmind-Testimonal" => "iconsmind-Testimonal"), 
		array("iconsmind-Computer-Secure" => "iconsmind-Computer-Secure"), 
		array("iconsmind-Eye-Scan" => "iconsmind-Eye-Scan"), 
		array("iconsmind-Finger-Print" => "iconsmind-Finger-Print"), 
		array("iconsmind-Firewall" => "iconsmind-Firewall"), 
		array("iconsmind-Key-Lock" => "iconsmind-Key-Lock"), 
		array("iconsmind-Laptop-Secure" => "iconsmind-Laptop-Secure"), 
		array("iconsmind-Layer-1532" => "iconsmind-Layer-1532"), 
		array("iconsmind-Lock" => "iconsmind-Lock"), 
		array("iconsmind-Lock-2" => "iconsmind-Lock-2"), 
		array("iconsmind-Lock-3" => "iconsmind-Lock-3"), 
		array("iconsmind-Password" => "iconsmind-Password"), 
		array("iconsmind-Password-Field" => "iconsmind-Password-Field"), 
		array("iconsmind-Police" => "iconsmind-Police"), 
		array("iconsmind-Safe-Box" => "iconsmind-Safe-Box"), 
		array("iconsmind-Security-Block" => "iconsmind-Security-Block"), 
		array("iconsmind-Security-Bug" => "iconsmind-Security-Bug"), 
		array("iconsmind-Security-Camera" => "iconsmind-Security-Camera"), 
		array("iconsmind-Security-Check" => "iconsmind-Security-Check"), 
		array("iconsmind-Security-Settings" => "iconsmind-Security-Settings"), 
		array("iconsmind-Securiy-Remove" => "iconsmind-Securiy-Remove"), 
		array("iconsmind-Shield" => "iconsmind-Shield"), 
		array("iconsmind-Smartphone-Secure" => "iconsmind-Smartphone-Secure"), 
		array("iconsmind-SSL" => "iconsmind-SSL"), 
		array("iconsmind-Tablet-Secure" => "iconsmind-Tablet-Secure"), 
		array("iconsmind-Type-Pass" => "iconsmind-Type-Pass"), 
		array("iconsmind-Unlock" => "iconsmind-Unlock"), 
		array("iconsmind-Unlock-2" => "iconsmind-Unlock-2"), 
		array("iconsmind-Unlock-3" => "iconsmind-Unlock-3"), 
		array("iconsmind-Ambulance" => "iconsmind-Ambulance"), 
		array("iconsmind-Astronaut" => "iconsmind-Astronaut"), 
		array("iconsmind-Atom" => "iconsmind-Atom"), 
		array("iconsmind-Bacteria" => "iconsmind-Bacteria"), 
		array("iconsmind-Band-Aid" => "iconsmind-Band-Aid"), 
		array("iconsmind-Bio-Hazard" => "iconsmind-Bio-Hazard"), 
		array("iconsmind-Biotech" => "iconsmind-Biotech"), 
		array("iconsmind-Brain" => "iconsmind-Brain"), 
		array("iconsmind-Chemical" => "iconsmind-Chemical"), 
		array("iconsmind-Chemical-2" => "iconsmind-Chemical-2"), 
		array("iconsmind-Chemical-3" => "iconsmind-Chemical-3"), 
		array("iconsmind-Chemical-4" => "iconsmind-Chemical-4"), 
		array("iconsmind-Chemical-5" => "iconsmind-Chemical-5"), 
		array("iconsmind-Clinic" => "iconsmind-Clinic"), 
		array("iconsmind-Cube-Molecule" => "iconsmind-Cube-Molecule"), 
		array("iconsmind-Cube-Molecule2" => "iconsmind-Cube-Molecule2"), 
		array("iconsmind-Danger" => "iconsmind-Danger"), 
		array("iconsmind-Danger-2" => "iconsmind-Danger-2"), 
		array("iconsmind-DNA" => "iconsmind-DNA"), 
		array("iconsmind-DNA-2" => "iconsmind-DNA-2"), 
		array("iconsmind-DNA-Helix" => "iconsmind-DNA-Helix"), 
		array("iconsmind-First-Aid" => "iconsmind-First-Aid"), 
		array("iconsmind-Flask" => "iconsmind-Flask"), 
		array("iconsmind-Flask-2" => "iconsmind-Flask-2"), 
		array("iconsmind-Helix-2" => "iconsmind-Helix-2"), 
		array("iconsmind-Hospital" => "iconsmind-Hospital"), 
		array("iconsmind-Hurt" => "iconsmind-Hurt"), 
		array("iconsmind-Medical-Sign" => "iconsmind-Medical-Sign"), 
		array("iconsmind-Medicine" => "iconsmind-Medicine"), 
		array("iconsmind-Medicine-2" => "iconsmind-Medicine-2"), 
		array("iconsmind-Medicine-3" => "iconsmind-Medicine-3"), 
		array("iconsmind-Microscope" => "iconsmind-Microscope"), 
		array("iconsmind-Neutron" => "iconsmind-Neutron"), 
		array("iconsmind-Nuclear" => "iconsmind-Nuclear"), 
		array("iconsmind-Physics" => "iconsmind-Physics"), 
		array("iconsmind-Plasmid" => "iconsmind-Plasmid"), 
		array("iconsmind-Plaster" => "iconsmind-Plaster"), 
		array("iconsmind-Pulse" => "iconsmind-Pulse"), 
		array("iconsmind-Radioactive" => "iconsmind-Radioactive"), 
		array("iconsmind-Safety-PinClose" => "iconsmind-Safety-PinClose"), 
		array("iconsmind-Safety-PinOpen" => "iconsmind-Safety-PinOpen"), 
		array("iconsmind-Spermium" => "iconsmind-Spermium"), 
		array("iconsmind-Stethoscope" => "iconsmind-Stethoscope"), 
		array("iconsmind-Temperature2" => "iconsmind-Temperature2"), 
		array("iconsmind-Test-Tube" => "iconsmind-Test-Tube"), 
		array("iconsmind-Test-Tube2" => "iconsmind-Test-Tube2"), 
		array("iconsmind-Virus" => "iconsmind-Virus"), 
		array("iconsmind-Virus-2" => "iconsmind-Virus-2"), 
		array("iconsmind-Virus-3" => "iconsmind-Virus-3"), 
		array("iconsmind-X-ray" => "iconsmind-X-ray"), 
		array("iconsmind-Auto-Flash" => "iconsmind-Auto-Flash"), 
		array("iconsmind-Camera" => "iconsmind-Camera"), 
		array("iconsmind-Camera-2" => "iconsmind-Camera-2"), 
		array("iconsmind-Camera-3" => "iconsmind-Camera-3"), 
		array("iconsmind-Camera-4" => "iconsmind-Camera-4"), 
		array("iconsmind-Camera-5" => "iconsmind-Camera-5"), 
		array("iconsmind-Camera-Back" => "iconsmind-Camera-Back"), 
		array("iconsmind-Crop" => "iconsmind-Crop"), 
		array("iconsmind-Daylight" => "iconsmind-Daylight"), 
		array("iconsmind-Edit" => "iconsmind-Edit"), 
		array("iconsmind-Eye" => "iconsmind-Eye"), 
		array("iconsmind-Film2" => "iconsmind-Film2"), 
		array("iconsmind-Film-Cartridge" => "iconsmind-Film-Cartridge"), 
		array("iconsmind-Filter" => "iconsmind-Filter"), 
		array("iconsmind-Flash" => "iconsmind-Flash"), 
		array("iconsmind-Flash-2" => "iconsmind-Flash-2"), 
		array("iconsmind-Fluorescent" => "iconsmind-Fluorescent"), 
		array("iconsmind-Gopro" => "iconsmind-Gopro"), 
		array("iconsmind-Landscape" => "iconsmind-Landscape"), 
		array("iconsmind-Len" => "iconsmind-Len"), 
		array("iconsmind-Len-2" => "iconsmind-Len-2"), 
		array("iconsmind-Len-3" => "iconsmind-Len-3"), 
		array("iconsmind-Macro" => "iconsmind-Macro"), 
		array("iconsmind-Memory-Card" => "iconsmind-Memory-Card"), 
		array("iconsmind-Memory-Card2" => "iconsmind-Memory-Card2"), 
		array("iconsmind-Memory-Card3" => "iconsmind-Memory-Card3"), 
		array("iconsmind-No-Flash" => "iconsmind-No-Flash"), 
		array("iconsmind-Panorama" => "iconsmind-Panorama"), 
		array("iconsmind-Photo" => "iconsmind-Photo"), 
		array("iconsmind-Photo-2" => "iconsmind-Photo-2"), 
		array("iconsmind-Photo-3" => "iconsmind-Photo-3"), 
		array("iconsmind-Photo-Album" => "iconsmind-Photo-Album"), 
		array("iconsmind-Photo-Album2" => "iconsmind-Photo-Album2"), 
		array("iconsmind-Photo-Album3" => "iconsmind-Photo-Album3"), 
		array("iconsmind-Photos" => "iconsmind-Photos"), 
		array("iconsmind-Portrait" => "iconsmind-Portrait"), 
		array("iconsmind-Retouching" => "iconsmind-Retouching"), 
		array("iconsmind-Retro-Camera" => "iconsmind-Retro-Camera"), 
		array("iconsmind-secound" => "iconsmind-secound"), 
		array("iconsmind-secound2" => "iconsmind-secound2"), 
		array("iconsmind-Selfie" => "iconsmind-Selfie"), 
		array("iconsmind-Shutter" => "iconsmind-Shutter"), 
		array("iconsmind-Signal" => "iconsmind-Signal"), 
		array("iconsmind-Snow2" => "iconsmind-Snow2"), 
		array("iconsmind-Sport-Mode" => "iconsmind-Sport-Mode"), 
		array("iconsmind-Studio-Flash" => "iconsmind-Studio-Flash"), 
		array("iconsmind-Studio-Lightbox" => "iconsmind-Studio-Lightbox"), 
		array("iconsmind-Timer2" => "iconsmind-Timer2"), 
		array("iconsmind-Tripod-2" => "iconsmind-Tripod-2"), 
		array("iconsmind-Tripod-withCamera" => "iconsmind-Tripod-withCamera"), 
		array("iconsmind-Tripod-withGopro" => "iconsmind-Tripod-withGopro"), 
		array("iconsmind-Add-User" => "iconsmind-Add-User"), 
		array("iconsmind-Add-UserStar" => "iconsmind-Add-UserStar"), 
		array("iconsmind-Administrator" => "iconsmind-Administrator"), 
		array("iconsmind-Alien" => "iconsmind-Alien"), 
		array("iconsmind-Alien-2" => "iconsmind-Alien-2"), 
		array("iconsmind-Assistant" => "iconsmind-Assistant"), 
		array("iconsmind-Baby" => "iconsmind-Baby"), 
		array("iconsmind-Baby-Cry" => "iconsmind-Baby-Cry"), 
		array("iconsmind-Boy" => "iconsmind-Boy"), 
		array("iconsmind-Business-Man" => "iconsmind-Business-Man"), 
		array("iconsmind-Business-ManWoman" => "iconsmind-Business-ManWoman"), 
		array("iconsmind-Business-Mens" => "iconsmind-Business-Mens"), 
		array("iconsmind-Business-Woman" => "iconsmind-Business-Woman"), 
		array("iconsmind-Checked-User" => "iconsmind-Checked-User"), 
		array("iconsmind-Chef" => "iconsmind-Chef"), 
		array("iconsmind-Conference" => "iconsmind-Conference"), 
		array("iconsmind-Cool-Guy" => "iconsmind-Cool-Guy"), 
		array("iconsmind-Criminal" => "iconsmind-Criminal"), 
		array("iconsmind-Dj" => "iconsmind-Dj"), 
		array("iconsmind-Doctor" => "iconsmind-Doctor"), 
		array("iconsmind-Engineering" => "iconsmind-Engineering"), 
		array("iconsmind-Farmer" => "iconsmind-Farmer"), 
		array("iconsmind-Female" => "iconsmind-Female"), 
		array("iconsmind-Female-22" => "iconsmind-Female-22"), 
		array("iconsmind-Find-User" => "iconsmind-Find-User"), 
		array("iconsmind-Geek" => "iconsmind-Geek"), 
		array("iconsmind-Genius" => "iconsmind-Genius"), 
		array("iconsmind-Girl" => "iconsmind-Girl"), 
		array("iconsmind-Headphone" => "iconsmind-Headphone"), 
		array("iconsmind-Headset" => "iconsmind-Headset"), 
		array("iconsmind-ID-2" => "iconsmind-ID-2"), 
		array("iconsmind-ID-3" => "iconsmind-ID-3"), 
		array("iconsmind-ID-Card" => "iconsmind-ID-Card"), 
		array("iconsmind-King-2" => "iconsmind-King-2"), 
		array("iconsmind-Lock-User" => "iconsmind-Lock-User"), 
		array("iconsmind-Love-User" => "iconsmind-Love-User"), 
		array("iconsmind-Male" => "iconsmind-Male"), 
		array("iconsmind-Male-22" => "iconsmind-Male-22"), 
		array("iconsmind-MaleFemale" => "iconsmind-MaleFemale"), 
		array("iconsmind-Man-Sign" => "iconsmind-Man-Sign"), 
		array("iconsmind-Mens" => "iconsmind-Mens"), 
		array("iconsmind-Network" => "iconsmind-Network"), 
		array("iconsmind-Nurse" => "iconsmind-Nurse"), 
		array("iconsmind-Pac-Man" => "iconsmind-Pac-Man"), 
		array("iconsmind-Pilot" => "iconsmind-Pilot"), 
		array("iconsmind-Police-Man" => "iconsmind-Police-Man"), 
		array("iconsmind-Police-Woman" => "iconsmind-Police-Woman"), 
		array("iconsmind-Professor" => "iconsmind-Professor"), 
		array("iconsmind-Punker" => "iconsmind-Punker"), 
		array("iconsmind-Queen-2" => "iconsmind-Queen-2"), 
		array("iconsmind-Remove-User" => "iconsmind-Remove-User"), 
		array("iconsmind-Robot" => "iconsmind-Robot"), 
		array("iconsmind-Speak" => "iconsmind-Speak"), 
		array("iconsmind-Speak-2" => "iconsmind-Speak-2"), 
		array("iconsmind-Spy" => "iconsmind-Spy"), 
		array("iconsmind-Student-Female" => "iconsmind-Student-Female"), 
		array("iconsmind-Student-Male" => "iconsmind-Student-Male"), 
		array("iconsmind-Student-MaleFemale" => "iconsmind-Student-MaleFemale"), 
		array("iconsmind-Students" => "iconsmind-Students"), 
		array("iconsmind-Superman" => "iconsmind-Superman"), 
		array("iconsmind-Talk-Man" => "iconsmind-Talk-Man"), 
		array("iconsmind-Teacher" => "iconsmind-Teacher"), 
		array("iconsmind-Waiter" => "iconsmind-Waiter"), 
		array("iconsmind-WomanMan" => "iconsmind-WomanMan"), 
		array("iconsmind-Woman-Sign" => "iconsmind-Woman-Sign"), 
		array("iconsmind-Wonder-Woman" => "iconsmind-Wonder-Woman"), 
		array("iconsmind-Worker" => "iconsmind-Worker"), 
		array("iconsmind-Anchor" => "iconsmind-Anchor"), 
		array("iconsmind-Army-Key" => "iconsmind-Army-Key"), 
		array("iconsmind-Balloon" => "iconsmind-Balloon"), 
		array("iconsmind-Barricade" => "iconsmind-Barricade"), 
		array("iconsmind-Batman-Mask" => "iconsmind-Batman-Mask"), 
		array("iconsmind-Binocular" => "iconsmind-Binocular"), 
		array("iconsmind-Boom" => "iconsmind-Boom"), 
		array("iconsmind-Bucket" => "iconsmind-Bucket"), 
		array("iconsmind-Button" => "iconsmind-Button"), 
		array("iconsmind-Cannon" => "iconsmind-Cannon"), 
		array("iconsmind-Chacked-Flag" => "iconsmind-Chacked-Flag"), 
		array("iconsmind-Chair" => "iconsmind-Chair"), 
		array("iconsmind-Coffee-Machine" => "iconsmind-Coffee-Machine"), 
		array("iconsmind-Crown" => "iconsmind-Crown"), 
		array("iconsmind-Crown-2" => "iconsmind-Crown-2"), 
		array("iconsmind-Dice" => "iconsmind-Dice"), 
		array("iconsmind-Dice-2" => "iconsmind-Dice-2"), 
		array("iconsmind-Domino" => "iconsmind-Domino"), 
		array("iconsmind-Door-Hanger" => "iconsmind-Door-Hanger"), 
		array("iconsmind-Drill" => "iconsmind-Drill"), 
		array("iconsmind-Feather" => "iconsmind-Feather"), 
		array("iconsmind-Fire-Hydrant" => "iconsmind-Fire-Hydrant"), 
		array("iconsmind-Flag" => "iconsmind-Flag"), 
		array("iconsmind-Flag-2" => "iconsmind-Flag-2"), 
		array("iconsmind-Flashlight" => "iconsmind-Flashlight"), 
		array("iconsmind-Footprint2" => "iconsmind-Footprint2"), 
		array("iconsmind-Gas-Pump" => "iconsmind-Gas-Pump"), 
		array("iconsmind-Gift-Box" => "iconsmind-Gift-Box"), 
		array("iconsmind-Gun" => "iconsmind-Gun"), 
		array("iconsmind-Gun-2" => "iconsmind-Gun-2"), 
		array("iconsmind-Gun-3" => "iconsmind-Gun-3"), 
		array("iconsmind-Hammer" => "iconsmind-Hammer"), 
		array("iconsmind-Identification-Badge" => "iconsmind-Identification-Badge"), 
		array("iconsmind-Key" => "iconsmind-Key"), 
		array("iconsmind-Key-2" => "iconsmind-Key-2"), 
		array("iconsmind-Key-3" => "iconsmind-Key-3"), 
		array("iconsmind-Lamp" => "iconsmind-Lamp"), 
		array("iconsmind-Lego" => "iconsmind-Lego"), 
		array("iconsmind-Life-Safer" => "iconsmind-Life-Safer"), 
		array("iconsmind-Light-Bulb" => "iconsmind-Light-Bulb"), 
		array("iconsmind-Light-Bulb2" => "iconsmind-Light-Bulb2"), 
		array("iconsmind-Luggafe-Front" => "iconsmind-Luggafe-Front"), 
		array("iconsmind-Luggage-2" => "iconsmind-Luggage-2"), 
		array("iconsmind-Magic-Wand" => "iconsmind-Magic-Wand"), 
		array("iconsmind-Magnet" => "iconsmind-Magnet"), 
		array("iconsmind-Mask" => "iconsmind-Mask"), 
		array("iconsmind-Menorah" => "iconsmind-Menorah"), 
		array("iconsmind-Mirror" => "iconsmind-Mirror"), 
		array("iconsmind-Movie-Ticket" => "iconsmind-Movie-Ticket"), 
		array("iconsmind-Office-Lamp" => "iconsmind-Office-Lamp"), 
		array("iconsmind-Paint-Brush" => "iconsmind-Paint-Brush"), 
		array("iconsmind-Paint-Bucket" => "iconsmind-Paint-Bucket"), 
		array("iconsmind-Paper-Plane" => "iconsmind-Paper-Plane"), 
		array("iconsmind-Post-Sign" => "iconsmind-Post-Sign"), 
		array("iconsmind-Post-Sign2ways" => "iconsmind-Post-Sign2ways"), 
		array("iconsmind-Puzzle" => "iconsmind-Puzzle"), 
		array("iconsmind-Razzor-Blade" => "iconsmind-Razzor-Blade"), 
		array("iconsmind-Scale" => "iconsmind-Scale"), 
		array("iconsmind-Screwdriver" => "iconsmind-Screwdriver"), 
		array("iconsmind-Sewing-Machine" => "iconsmind-Sewing-Machine"), 
		array("iconsmind-Sheriff-Badge" => "iconsmind-Sheriff-Badge"), 
		array("iconsmind-Stroller" => "iconsmind-Stroller"), 
		array("iconsmind-Suitcase" => "iconsmind-Suitcase"), 
		array("iconsmind-Teddy-Bear" => "iconsmind-Teddy-Bear"), 
		array("iconsmind-Telescope" => "iconsmind-Telescope"), 
		array("iconsmind-Tent" => "iconsmind-Tent"), 
		array("iconsmind-Thread" => "iconsmind-Thread"), 
		array("iconsmind-Ticket" => "iconsmind-Ticket"), 
		array("iconsmind-Time-Bomb" => "iconsmind-Time-Bomb"), 
		array("iconsmind-Tourch" => "iconsmind-Tourch"), 
		array("iconsmind-Vase" => "iconsmind-Vase"), 
		array("iconsmind-Video-GameController" => "iconsmind-Video-GameController"), 
		array("iconsmind-Conservation" => "iconsmind-Conservation"), 
		array("iconsmind-Eci-Icon" => "iconsmind-Eci-Icon"), 
		array("iconsmind-Environmental" => "iconsmind-Environmental"), 
		array("iconsmind-Environmental-2" => "iconsmind-Environmental-2"), 
		array("iconsmind-Environmental-3" => "iconsmind-Environmental-3"), 
		array("iconsmind-Fire-Flame" => "iconsmind-Fire-Flame"), 
		array("iconsmind-Fire-Flame2" => "iconsmind-Fire-Flame2"), 
		array("iconsmind-Flowerpot" => "iconsmind-Flowerpot"), 
		array("iconsmind-Forest" => "iconsmind-Forest"), 
		array("iconsmind-Green-Energy" => "iconsmind-Green-Energy"), 
		array("iconsmind-Green-House" => "iconsmind-Green-House"), 
		array("iconsmind-Landscape2" => "iconsmind-Landscape2"), 
		array("iconsmind-Leafs" => "iconsmind-Leafs"), 
		array("iconsmind-Leafs-2" => "iconsmind-Leafs-2"), 
		array("iconsmind-Light-BulbLeaf" => "iconsmind-Light-BulbLeaf"), 
		array("iconsmind-Palm-Tree" => "iconsmind-Palm-Tree"), 
		array("iconsmind-Plant" => "iconsmind-Plant"), 
		array("iconsmind-Recycling" => "iconsmind-Recycling"), 
		array("iconsmind-Recycling-2" => "iconsmind-Recycling-2"), 
		array("iconsmind-Seed" => "iconsmind-Seed"), 
		array("iconsmind-Trash-withMen" => "iconsmind-Trash-withMen"), 
		array("iconsmind-Tree" => "iconsmind-Tree"), 
		array("iconsmind-Tree-2" => "iconsmind-Tree-2"), 
		array("iconsmind-Tree-3" => "iconsmind-Tree-3"), 
		array("iconsmind-Audio" => "iconsmind-Audio"), 
		array("iconsmind-Back-Music" => "iconsmind-Back-Music"), 
		array("iconsmind-Bell" => "iconsmind-Bell"), 
		array("iconsmind-Casette-Tape" => "iconsmind-Casette-Tape"), 
		array("iconsmind-CD-2" => "iconsmind-CD-2"), 
		array("iconsmind-CD-Cover" => "iconsmind-CD-Cover"), 
		array("iconsmind-Cello" => "iconsmind-Cello"), 
		array("iconsmind-Clef" => "iconsmind-Clef"), 
		array("iconsmind-Drum" => "iconsmind-Drum"), 
		array("iconsmind-Earphones" => "iconsmind-Earphones"), 
		array("iconsmind-Earphones-2" => "iconsmind-Earphones-2"), 
		array("iconsmind-Electric-Guitar" => "iconsmind-Electric-Guitar"), 
		array("iconsmind-Equalizer" => "iconsmind-Equalizer"), 
		array("iconsmind-First" => "iconsmind-First"), 
		array("iconsmind-Guitar" => "iconsmind-Guitar"), 
		array("iconsmind-Headphones" => "iconsmind-Headphones"), 
		array("iconsmind-Keyboard3" => "iconsmind-Keyboard3"), 
		array("iconsmind-Last" => "iconsmind-Last"), 
		array("iconsmind-Loud" => "iconsmind-Loud"), 
		array("iconsmind-Loudspeaker" => "iconsmind-Loudspeaker"), 
		array("iconsmind-Mic" => "iconsmind-Mic"), 
		array("iconsmind-Microphone" => "iconsmind-Microphone"), 
		array("iconsmind-Microphone-2" => "iconsmind-Microphone-2"), 
		array("iconsmind-Microphone-3" => "iconsmind-Microphone-3"), 
		array("iconsmind-Microphone-4" => "iconsmind-Microphone-4"), 
		array("iconsmind-Microphone-5" => "iconsmind-Microphone-5"), 
		array("iconsmind-Microphone-6" => "iconsmind-Microphone-6"), 
		array("iconsmind-Microphone-7" => "iconsmind-Microphone-7"), 
		array("iconsmind-Mixer" => "iconsmind-Mixer"), 
		array("iconsmind-Mp3-File" => "iconsmind-Mp3-File"), 
		array("iconsmind-Music-Note" => "iconsmind-Music-Note"), 
		array("iconsmind-Music-Note2" => "iconsmind-Music-Note2"), 
		array("iconsmind-Music-Note3" => "iconsmind-Music-Note3"), 
		array("iconsmind-Music-Note4" => "iconsmind-Music-Note4"), 
		array("iconsmind-Music-Player" => "iconsmind-Music-Player"), 
		array("iconsmind-Mute" => "iconsmind-Mute"), 
		array("iconsmind-Next-Music" => "iconsmind-Next-Music"), 
		array("iconsmind-Old-Radio" => "iconsmind-Old-Radio"), 
		array("iconsmind-On-Air" => "iconsmind-On-Air"), 
		array("iconsmind-Piano" => "iconsmind-Piano"), 
		array("iconsmind-Play-Music" => "iconsmind-Play-Music"), 
		array("iconsmind-Radio" => "iconsmind-Radio"), 
		array("iconsmind-Record" => "iconsmind-Record"), 
		array("iconsmind-Record-Music" => "iconsmind-Record-Music"), 
		array("iconsmind-Rock-andRoll" => "iconsmind-Rock-andRoll"), 
		array("iconsmind-Saxophone" => "iconsmind-Saxophone"), 
		array("iconsmind-Sound" => "iconsmind-Sound"), 
		array("iconsmind-Sound-Wave" => "iconsmind-Sound-Wave"), 
		array("iconsmind-Speaker" => "iconsmind-Speaker"), 
		array("iconsmind-Stop-Music" => "iconsmind-Stop-Music"), 
		array("iconsmind-Trumpet" => "iconsmind-Trumpet"), 
		array("iconsmind-Voice" => "iconsmind-Voice"), 
		array("iconsmind-Volume-Down" => "iconsmind-Volume-Down"), 
		array("iconsmind-Volume-Up" => "iconsmind-Volume-Up"), 
		array("iconsmind-Back" => "iconsmind-Back"), 
		array("iconsmind-Back-2" => "iconsmind-Back-2"), 
		array("iconsmind-Eject" => "iconsmind-Eject"), 
		array("iconsmind-Eject-2" => "iconsmind-Eject-2"), 
		array("iconsmind-End" => "iconsmind-End"), 
		array("iconsmind-End-2" => "iconsmind-End-2"), 
		array("iconsmind-Next" => "iconsmind-Next"), 
		array("iconsmind-Next-2" => "iconsmind-Next-2"), 
		array("iconsmind-Pause" => "iconsmind-Pause"), 
		array("iconsmind-Pause-2" => "iconsmind-Pause-2"), 
		array("iconsmind-Power-2" => "iconsmind-Power-2"), 
		array("iconsmind-Power-3" => "iconsmind-Power-3"), 
		array("iconsmind-Record2" => "iconsmind-Record2"), 
		array("iconsmind-Record-2" => "iconsmind-Record-2"), 
		array("iconsmind-Repeat" => "iconsmind-Repeat"), 
		array("iconsmind-Repeat-2" => "iconsmind-Repeat-2"), 
		array("iconsmind-Shuffle" => "iconsmind-Shuffle"), 
		array("iconsmind-Shuffle-2" => "iconsmind-Shuffle-2"), 
		array("iconsmind-Start" => "iconsmind-Start"), 
		array("iconsmind-Start-2" => "iconsmind-Start-2"), 
		array("iconsmind-Stop" => "iconsmind-Stop"), 
		array("iconsmind-Stop-2" => "iconsmind-Stop-2"), 
		array("iconsmind-Compass" => "iconsmind-Compass"), 
		array("iconsmind-Compass-2" => "iconsmind-Compass-2"), 
		array("iconsmind-Compass-Rose" => "iconsmind-Compass-Rose"), 
		array("iconsmind-Direction-East" => "iconsmind-Direction-East"), 
		array("iconsmind-Direction-North" => "iconsmind-Direction-North"), 
		array("iconsmind-Direction-South" => "iconsmind-Direction-South"), 
		array("iconsmind-Direction-West" => "iconsmind-Direction-West"), 
		array("iconsmind-Edit-Map" => "iconsmind-Edit-Map"), 
		array("iconsmind-Geo" => "iconsmind-Geo"), 
		array("iconsmind-Geo2" => "iconsmind-Geo2"), 
		array("iconsmind-Geo3" => "iconsmind-Geo3"), 
		array("iconsmind-Geo22" => "iconsmind-Geo22"), 
		array("iconsmind-Geo23" => "iconsmind-Geo23"), 
		array("iconsmind-Geo24" => "iconsmind-Geo24"), 
		array("iconsmind-Geo2-Close" => "iconsmind-Geo2-Close"), 
		array("iconsmind-Geo2-Love" => "iconsmind-Geo2-Love"), 
		array("iconsmind-Geo2-Number" => "iconsmind-Geo2-Number"), 
		array("iconsmind-Geo2-Star" => "iconsmind-Geo2-Star"), 
		array("iconsmind-Geo32" => "iconsmind-Geo32"), 
		array("iconsmind-Geo33" => "iconsmind-Geo33"), 
		array("iconsmind-Geo34" => "iconsmind-Geo34"), 
		array("iconsmind-Geo3-Close" => "iconsmind-Geo3-Close"), 
		array("iconsmind-Geo3-Love" => "iconsmind-Geo3-Love"), 
		array("iconsmind-Geo3-Number" => "iconsmind-Geo3-Number"), 
		array("iconsmind-Geo3-Star" => "iconsmind-Geo3-Star"), 
		array("iconsmind-Geo-Close" => "iconsmind-Geo-Close"), 
		array("iconsmind-Geo-Love" => "iconsmind-Geo-Love"), 
		array("iconsmind-Geo-Number" => "iconsmind-Geo-Number"), 
		array("iconsmind-Geo-Star" => "iconsmind-Geo-Star"), 
		array("iconsmind-Global-Position" => "iconsmind-Global-Position"), 
		array("iconsmind-Globe" => "iconsmind-Globe"), 
		array("iconsmind-Globe-2" => "iconsmind-Globe-2"), 
		array("iconsmind-Location" => "iconsmind-Location"), 
		array("iconsmind-Location-2" => "iconsmind-Location-2"), 
		array("iconsmind-Map" => "iconsmind-Map"), 
		array("iconsmind-Map2" => "iconsmind-Map2"), 
		array("iconsmind-Map-Marker" => "iconsmind-Map-Marker"), 
		array("iconsmind-Map-Marker2" => "iconsmind-Map-Marker2"), 
		array("iconsmind-Map-Marker3" => "iconsmind-Map-Marker3"), 
		array("iconsmind-Road2" => "iconsmind-Road2"), 
		array("iconsmind-Satelite" => "iconsmind-Satelite"), 
		array("iconsmind-Satelite-2" => "iconsmind-Satelite-2"), 
		array("iconsmind-Street-View" => "iconsmind-Street-View"), 
		array("iconsmind-Street-View2" => "iconsmind-Street-View2"), 
		array("iconsmind-Android-Store" => "iconsmind-Android-Store"), 
		array("iconsmind-Apple-Store" => "iconsmind-Apple-Store"), 
		array("iconsmind-Box2" => "iconsmind-Box2"), 
		array("iconsmind-Dropbox" => "iconsmind-Dropbox"), 
		array("iconsmind-Google-Drive" => "iconsmind-Google-Drive"), 
		array("iconsmind-Google-Play" => "iconsmind-Google-Play"), 
		array("iconsmind-Paypal" => "iconsmind-Paypal"), 
		array("iconsmind-Skrill" => "iconsmind-Skrill"), 
		array("iconsmind-X-Box" => "iconsmind-X-Box"), 
		array("iconsmind-Add" => "iconsmind-Add"), 
		array("iconsmind-Back2" => "iconsmind-Back2"), 
		array("iconsmind-Broken-Link" => "iconsmind-Broken-Link"), 
		array("iconsmind-Check" => "iconsmind-Check"), 
		array("iconsmind-Check-2" => "iconsmind-Check-2"), 
		array("iconsmind-Circular-Point" => "iconsmind-Circular-Point"), 
		array("iconsmind-Close" => "iconsmind-Close"), 
		array("iconsmind-Cursor" => "iconsmind-Cursor"), 
		array("iconsmind-Cursor-Click" => "iconsmind-Cursor-Click"), 
		array("iconsmind-Cursor-Click2" => "iconsmind-Cursor-Click2"), 
		array("iconsmind-Cursor-Move" => "iconsmind-Cursor-Move"), 
		array("iconsmind-Cursor-Move2" => "iconsmind-Cursor-Move2"), 
		array("iconsmind-Cursor-Select" => "iconsmind-Cursor-Select"), 
		array("iconsmind-Down" => "iconsmind-Down"), 
		array("iconsmind-Download" => "iconsmind-Download"), 
		array("iconsmind-Downward" => "iconsmind-Downward"), 
		array("iconsmind-Endways" => "iconsmind-Endways"), 
		array("iconsmind-Forward" => "iconsmind-Forward"), 
		array("iconsmind-Left" => "iconsmind-Left"), 
		array("iconsmind-Link" => "iconsmind-Link"), 
		array("iconsmind-Next2" => "iconsmind-Next2"), 
		array("iconsmind-Orientation" => "iconsmind-Orientation"), 
		array("iconsmind-Pointer" => "iconsmind-Pointer"), 
		array("iconsmind-Previous" => "iconsmind-Previous"), 
		array("iconsmind-Redo" => "iconsmind-Redo"), 
		array("iconsmind-Refresh" => "iconsmind-Refresh"), 
		array("iconsmind-Reload" => "iconsmind-Reload"), 
		array("iconsmind-Remove" => "iconsmind-Remove"), 
		array("iconsmind-Repeat2" => "iconsmind-Repeat2"), 
		array("iconsmind-Reset" => "iconsmind-Reset"), 
		array("iconsmind-Rewind" => "iconsmind-Rewind"), 
		array("iconsmind-Right" => "iconsmind-Right"), 
		array("iconsmind-Rotation" => "iconsmind-Rotation"), 
		array("iconsmind-Rotation-390" => "iconsmind-Rotation-390"), 
		array("iconsmind-Spot" => "iconsmind-Spot"), 
		array("iconsmind-Start-ways" => "iconsmind-Start-ways"), 
		array("iconsmind-Synchronize" => "iconsmind-Synchronize"), 
		array("iconsmind-Synchronize-2" => "iconsmind-Synchronize-2"), 
		array("iconsmind-Undo" => "iconsmind-Undo"), 
		array("iconsmind-Up" => "iconsmind-Up"), 
		array("iconsmind-Upload" => "iconsmind-Upload"), 
		array("iconsmind-Upward" => "iconsmind-Upward"), 
		array("iconsmind-Yes" => "iconsmind-Yes"), 
		array("iconsmind-Barricade2" => "iconsmind-Barricade2"), 
		array("iconsmind-Crane" => "iconsmind-Crane"), 
		array("iconsmind-Dam" => "iconsmind-Dam"), 
		array("iconsmind-Drill2" => "iconsmind-Drill2"), 
		array("iconsmind-Electricity" => "iconsmind-Electricity"), 
		array("iconsmind-Explode" => "iconsmind-Explode"), 
		array("iconsmind-Factory" => "iconsmind-Factory"), 
		array("iconsmind-Fuel" => "iconsmind-Fuel"), 
		array("iconsmind-Helmet2" => "iconsmind-Helmet2"), 
		array("iconsmind-Helmet-2" => "iconsmind-Helmet-2"), 
		array("iconsmind-Laser" => "iconsmind-Laser"), 
		array("iconsmind-Mine" => "iconsmind-Mine"), 
		array("iconsmind-Oil" => "iconsmind-Oil"), 
		array("iconsmind-Petrol" => "iconsmind-Petrol"), 
		array("iconsmind-Pipe" => "iconsmind-Pipe"), 
		array("iconsmind-Power-Station" => "iconsmind-Power-Station"), 
		array("iconsmind-Refinery" => "iconsmind-Refinery"), 
		array("iconsmind-Saw" => "iconsmind-Saw"), 
		array("iconsmind-Shovel" => "iconsmind-Shovel"), 
		array("iconsmind-Solar" => "iconsmind-Solar"), 
		array("iconsmind-Wheelbarrow" => "iconsmind-Wheelbarrow"), 
		array("iconsmind-Windmill" => "iconsmind-Windmill"), 
		array("iconsmind-Aa" => "iconsmind-Aa"), 
		array("iconsmind-Add-File" => "iconsmind-Add-File"), 
		array("iconsmind-Address-Book" => "iconsmind-Address-Book"), 
		array("iconsmind-Address-Book2" => "iconsmind-Address-Book2"), 
		array("iconsmind-Add-SpaceAfterParagraph" => "iconsmind-Add-SpaceAfterParagraph"), 
		array("iconsmind-Add-SpaceBeforeParagraph" => "iconsmind-Add-SpaceBeforeParagraph"), 
		array("iconsmind-Airbrush" => "iconsmind-Airbrush"), 
		array("iconsmind-Aligator" => "iconsmind-Aligator"), 
		array("iconsmind-Align-Center" => "iconsmind-Align-Center"), 
		array("iconsmind-Align-JustifyAll" => "iconsmind-Align-JustifyAll"), 
		array("iconsmind-Align-JustifyCenter" => "iconsmind-Align-JustifyCenter"), 
		array("iconsmind-Align-JustifyLeft" => "iconsmind-Align-JustifyLeft"), 
		array("iconsmind-Align-JustifyRight" => "iconsmind-Align-JustifyRight"), 
		array("iconsmind-Align-Left" => "iconsmind-Align-Left"), 
		array("iconsmind-Align-Right" => "iconsmind-Align-Right"), 
		array("iconsmind-Alpha" => "iconsmind-Alpha"), 
		array("iconsmind-AMX" => "iconsmind-AMX"), 
		array("iconsmind-Anchor2" => "iconsmind-Anchor2"), 
		array("iconsmind-Android" => "iconsmind-Android"), 
		array("iconsmind-Angel" => "iconsmind-Angel"), 
		array("iconsmind-Angel-Smiley" => "iconsmind-Angel-Smiley"), 
		array("iconsmind-Angry" => "iconsmind-Angry"), 
		array("iconsmind-Apple" => "iconsmind-Apple"), 
		array("iconsmind-Apple-Bite" => "iconsmind-Apple-Bite"), 
		array("iconsmind-Argentina" => "iconsmind-Argentina"), 
		array("iconsmind-Arrow-Around" => "iconsmind-Arrow-Around"), 
		array("iconsmind-Arrow-Back" => "iconsmind-Arrow-Back"), 
		array("iconsmind-Arrow-Back2" => "iconsmind-Arrow-Back2"), 
		array("iconsmind-Arrow-Back3" => "iconsmind-Arrow-Back3"), 
		array("iconsmind-Arrow-Barrier" => "iconsmind-Arrow-Barrier"), 
		array("iconsmind-Arrow-Circle" => "iconsmind-Arrow-Circle"), 
		array("iconsmind-Arrow-Cross" => "iconsmind-Arrow-Cross"), 
		array("iconsmind-Arrow-Down" => "iconsmind-Arrow-Down"), 
		array("iconsmind-Arrow-Down2" => "iconsmind-Arrow-Down2"), 
		array("iconsmind-Arrow-Down3" => "iconsmind-Arrow-Down3"), 
		array("iconsmind-Arrow-DowninCircle" => "iconsmind-Arrow-DowninCircle"), 
		array("iconsmind-Arrow-Fork" => "iconsmind-Arrow-Fork"), 
		array("iconsmind-Arrow-Forward" => "iconsmind-Arrow-Forward"), 
		array("iconsmind-Arrow-Forward2" => "iconsmind-Arrow-Forward2"), 
		array("iconsmind-Arrow-From" => "iconsmind-Arrow-From"), 
		array("iconsmind-Arrow-Inside" => "iconsmind-Arrow-Inside"), 
		array("iconsmind-Arrow-Inside45" => "iconsmind-Arrow-Inside45"), 
		array("iconsmind-Arrow-InsideGap" => "iconsmind-Arrow-InsideGap"), 
		array("iconsmind-Arrow-InsideGap45" => "iconsmind-Arrow-InsideGap45"), 
		array("iconsmind-Arrow-Into" => "iconsmind-Arrow-Into"), 
		array("iconsmind-Arrow-Join" => "iconsmind-Arrow-Join"), 
		array("iconsmind-Arrow-Junction" => "iconsmind-Arrow-Junction"), 
		array("iconsmind-Arrow-Left" => "iconsmind-Arrow-Left"), 
		array("iconsmind-Arrow-Left2" => "iconsmind-Arrow-Left2"), 
		array("iconsmind-Arrow-LeftinCircle" => "iconsmind-Arrow-LeftinCircle"), 
		array("iconsmind-Arrow-Loop" => "iconsmind-Arrow-Loop"), 
		array("iconsmind-Arrow-Merge" => "iconsmind-Arrow-Merge"), 
		array("iconsmind-Arrow-Mix" => "iconsmind-Arrow-Mix"), 
		array("iconsmind-Arrow-Next" => "iconsmind-Arrow-Next"), 
		array("iconsmind-Arrow-OutLeft" => "iconsmind-Arrow-OutLeft"), 
		array("iconsmind-Arrow-OutRight" => "iconsmind-Arrow-OutRight"), 
		array("iconsmind-Arrow-Outside" => "iconsmind-Arrow-Outside"), 
		array("iconsmind-Arrow-Outside45" => "iconsmind-Arrow-Outside45"), 
		array("iconsmind-Arrow-OutsideGap" => "iconsmind-Arrow-OutsideGap"), 
		array("iconsmind-Arrow-OutsideGap45" => "iconsmind-Arrow-OutsideGap45"), 
		array("iconsmind-Arrow-Over" => "iconsmind-Arrow-Over"), 
		array("iconsmind-Arrow-Refresh" => "iconsmind-Arrow-Refresh"), 
		array("iconsmind-Arrow-Refresh2" => "iconsmind-Arrow-Refresh2"), 
		array("iconsmind-Arrow-Right" => "iconsmind-Arrow-Right"), 
		array("iconsmind-Arrow-Right2" => "iconsmind-Arrow-Right2"), 
		array("iconsmind-Arrow-RightinCircle" => "iconsmind-Arrow-RightinCircle"), 
		array("iconsmind-Arrow-Shuffle" => "iconsmind-Arrow-Shuffle"), 
		array("iconsmind-Arrow-Squiggly" => "iconsmind-Arrow-Squiggly"), 
		array("iconsmind-Arrow-Through" => "iconsmind-Arrow-Through"), 
		array("iconsmind-Arrow-To" => "iconsmind-Arrow-To"), 
		array("iconsmind-Arrow-TurnLeft" => "iconsmind-Arrow-TurnLeft"), 
		array("iconsmind-Arrow-TurnRight" => "iconsmind-Arrow-TurnRight"), 
		array("iconsmind-Arrow-Up" => "iconsmind-Arrow-Up"), 
		array("iconsmind-Arrow-Up2" => "iconsmind-Arrow-Up2"), 
		array("iconsmind-Arrow-Up3" => "iconsmind-Arrow-Up3"), 
		array("iconsmind-Arrow-UpinCircle" => "iconsmind-Arrow-UpinCircle"), 
		array("iconsmind-Arrow-XLeft" => "iconsmind-Arrow-XLeft"), 
		array("iconsmind-Arrow-XRight" => "iconsmind-Arrow-XRight"), 
		array("iconsmind-ATM" => "iconsmind-ATM"), 
		array("iconsmind-At-Sign" => "iconsmind-At-Sign"), 
		array("iconsmind-Baby-Clothes" => "iconsmind-Baby-Clothes"), 
		array("iconsmind-Baby-Clothes2" => "iconsmind-Baby-Clothes2"), 
		array("iconsmind-Bag" => "iconsmind-Bag"), 
		array("iconsmind-Bakelite" => "iconsmind-Bakelite"), 
		array("iconsmind-Banana" => "iconsmind-Banana"), 
		array("iconsmind-Bank" => "iconsmind-Bank"), 
		array("iconsmind-Bar-Chart" => "iconsmind-Bar-Chart"), 
		array("iconsmind-Bar-Chart2" => "iconsmind-Bar-Chart2"), 
		array("iconsmind-Bar-Chart3" => "iconsmind-Bar-Chart3"), 
		array("iconsmind-Bar-Chart4" => "iconsmind-Bar-Chart4"), 
		array("iconsmind-Bar-Chart5" => "iconsmind-Bar-Chart5"), 
		array("iconsmind-Bat" => "iconsmind-Bat"), 
		array("iconsmind-Bathrobe" => "iconsmind-Bathrobe"), 
		array("iconsmind-Battery-0" => "iconsmind-Battery-0"), 
		array("iconsmind-Battery-25" => "iconsmind-Battery-25"), 
		array("iconsmind-Battery-50" => "iconsmind-Battery-50"), 
		array("iconsmind-Battery-75" => "iconsmind-Battery-75"), 
		array("iconsmind-Battery-100" => "iconsmind-Battery-100"), 
		array("iconsmind-Battery-Charge" => "iconsmind-Battery-Charge"), 
		array("iconsmind-Bear" => "iconsmind-Bear"), 
		array("iconsmind-Beard" => "iconsmind-Beard"), 
		array("iconsmind-Beard-2" => "iconsmind-Beard-2"), 
		array("iconsmind-Beard-3" => "iconsmind-Beard-3"), 
		array("iconsmind-Bee" => "iconsmind-Bee"), 
		array("iconsmind-Beer" => "iconsmind-Beer"), 
		array("iconsmind-Beer-Glass" => "iconsmind-Beer-Glass"), 
		array("iconsmind-Bell2" => "iconsmind-Bell2"), 
		array("iconsmind-Belt" => "iconsmind-Belt"), 
		array("iconsmind-Belt-2" => "iconsmind-Belt-2"), 
		array("iconsmind-Belt-3" => "iconsmind-Belt-3"), 
		array("iconsmind-Berlin-Tower" => "iconsmind-Berlin-Tower"), 
		array("iconsmind-Beta" => "iconsmind-Beta"), 
		array("iconsmind-Big-Bang" => "iconsmind-Big-Bang"), 
		array("iconsmind-Big-Data" => "iconsmind-Big-Data"), 
		array("iconsmind-Bikini" => "iconsmind-Bikini"), 
		array("iconsmind-Bilk-Bottle2" => "iconsmind-Bilk-Bottle2"), 
		array("iconsmind-Bird" => "iconsmind-Bird"), 
		array("iconsmind-Bird-DeliveringLetter" => "iconsmind-Bird-DeliveringLetter"), 
		array("iconsmind-Birthday-Cake" => "iconsmind-Birthday-Cake"), 
		array("iconsmind-Bishop" => "iconsmind-Bishop"), 
		array("iconsmind-Blackboard" => "iconsmind-Blackboard"), 
		array("iconsmind-Black-Cat" => "iconsmind-Black-Cat"), 
		array("iconsmind-Block-Cloud" => "iconsmind-Block-Cloud"), 
		array("iconsmind-Blood" => "iconsmind-Blood"), 
		array("iconsmind-Blouse" => "iconsmind-Blouse"), 
		array("iconsmind-Blueprint" => "iconsmind-Blueprint"), 
		array("iconsmind-Board" => "iconsmind-Board"), 
		array("iconsmind-Bone" => "iconsmind-Bone"), 
		array("iconsmind-Bones" => "iconsmind-Bones"), 
		array("iconsmind-Book" => "iconsmind-Book"), 
		array("iconsmind-Bookmark" => "iconsmind-Bookmark"), 
		array("iconsmind-Books" => "iconsmind-Books"), 
		array("iconsmind-Books-2" => "iconsmind-Books-2"), 
		array("iconsmind-Boot" => "iconsmind-Boot"), 
		array("iconsmind-Boot-2" => "iconsmind-Boot-2"), 
		array("iconsmind-Bottom-ToTop" => "iconsmind-Bottom-ToTop"), 
		array("iconsmind-Bow" => "iconsmind-Bow"), 
		array("iconsmind-Bow-2" => "iconsmind-Bow-2"), 
		array("iconsmind-Bow-3" => "iconsmind-Bow-3"), 
		array("iconsmind-Box-Close" => "iconsmind-Box-Close"), 
		array("iconsmind-Box-Full" => "iconsmind-Box-Full"), 
		array("iconsmind-Box-Open" => "iconsmind-Box-Open"), 
		array("iconsmind-Box-withFolders" => "iconsmind-Box-withFolders"), 
		array("iconsmind-Bra" => "iconsmind-Bra"), 
		array("iconsmind-Brain2" => "iconsmind-Brain2"), 
		array("iconsmind-Brain-2" => "iconsmind-Brain-2"), 
		array("iconsmind-Brazil" => "iconsmind-Brazil"), 
		array("iconsmind-Bread" => "iconsmind-Bread"), 
		array("iconsmind-Bread-2" => "iconsmind-Bread-2"), 
		array("iconsmind-Bridge" => "iconsmind-Bridge"), 
		array("iconsmind-Broom" => "iconsmind-Broom"), 
		array("iconsmind-Brush" => "iconsmind-Brush"), 
		array("iconsmind-Bug" => "iconsmind-Bug"), 
		array("iconsmind-Building" => "iconsmind-Building"), 
		array("iconsmind-Butterfly" => "iconsmind-Butterfly"), 
		array("iconsmind-Cake" => "iconsmind-Cake"), 
		array("iconsmind-Calculator" => "iconsmind-Calculator"), 
		array("iconsmind-Calculator-2" => "iconsmind-Calculator-2"), 
		array("iconsmind-Calculator-3" => "iconsmind-Calculator-3"), 
		array("iconsmind-Calendar" => "iconsmind-Calendar"), 
		array("iconsmind-Calendar-2" => "iconsmind-Calendar-2"), 
		array("iconsmind-Calendar-3" => "iconsmind-Calendar-3"), 
		array("iconsmind-Calendar-4" => "iconsmind-Calendar-4"), 
		array("iconsmind-Camel" => "iconsmind-Camel"), 
		array("iconsmind-Can" => "iconsmind-Can"), 
		array("iconsmind-Can-2" => "iconsmind-Can-2"), 
		array("iconsmind-Canada" => "iconsmind-Canada"), 
		array("iconsmind-Candle" => "iconsmind-Candle"), 
		array("iconsmind-Candy" => "iconsmind-Candy"), 
		array("iconsmind-Candy-Cane" => "iconsmind-Candy-Cane"), 
		array("iconsmind-Cap" => "iconsmind-Cap"), 
		array("iconsmind-Cap-2" => "iconsmind-Cap-2"), 
		array("iconsmind-Cap-3" => "iconsmind-Cap-3"), 
		array("iconsmind-Cardigan" => "iconsmind-Cardigan"), 
		array("iconsmind-Cardiovascular" => "iconsmind-Cardiovascular"), 
		array("iconsmind-Castle" => "iconsmind-Castle"), 
		array("iconsmind-Cat" => "iconsmind-Cat"), 
		array("iconsmind-Cathedral" => "iconsmind-Cathedral"), 
		array("iconsmind-Cauldron" => "iconsmind-Cauldron"), 
		array("iconsmind-CD" => "iconsmind-CD"), 
		array("iconsmind-Charger" => "iconsmind-Charger"), 
		array("iconsmind-Checkmate" => "iconsmind-Checkmate"), 
		array("iconsmind-Cheese" => "iconsmind-Cheese"), 
		array("iconsmind-Cheetah" => "iconsmind-Cheetah"), 
		array("iconsmind-Chef-Hat" => "iconsmind-Chef-Hat"), 
		array("iconsmind-Chef-Hat2" => "iconsmind-Chef-Hat2"), 
		array("iconsmind-Chess-Board" => "iconsmind-Chess-Board"), 
		array("iconsmind-Chicken" => "iconsmind-Chicken"), 
		array("iconsmind-Chile" => "iconsmind-Chile"), 
		array("iconsmind-Chimney" => "iconsmind-Chimney"), 
		array("iconsmind-China" => "iconsmind-China"), 
		array("iconsmind-Chinese-Temple" => "iconsmind-Chinese-Temple"), 
		array("iconsmind-Chip" => "iconsmind-Chip"), 
		array("iconsmind-Chopsticks" => "iconsmind-Chopsticks"), 
		array("iconsmind-Chopsticks-2" => "iconsmind-Chopsticks-2"), 
		array("iconsmind-Christmas" => "iconsmind-Christmas"), 
		array("iconsmind-Christmas-Ball" => "iconsmind-Christmas-Ball"), 
		array("iconsmind-Christmas-Bell" => "iconsmind-Christmas-Bell"), 
		array("iconsmind-Christmas-Candle" => "iconsmind-Christmas-Candle"), 
		array("iconsmind-Christmas-Hat" => "iconsmind-Christmas-Hat"), 
		array("iconsmind-Christmas-Sleigh" => "iconsmind-Christmas-Sleigh"), 
		array("iconsmind-Christmas-Snowman" => "iconsmind-Christmas-Snowman"), 
		array("iconsmind-Christmas-Sock" => "iconsmind-Christmas-Sock"), 
		array("iconsmind-Christmas-Tree" => "iconsmind-Christmas-Tree"), 
		array("iconsmind-Chrome" => "iconsmind-Chrome"), 
		array("iconsmind-Chrysler-Building" => "iconsmind-Chrysler-Building"), 
		array("iconsmind-City-Hall" => "iconsmind-City-Hall"), 
		array("iconsmind-Clamp" => "iconsmind-Clamp"), 
		array("iconsmind-Claps" => "iconsmind-Claps"), 
		array("iconsmind-Clothing-Store" => "iconsmind-Clothing-Store"), 
		array("iconsmind-Cloud" => "iconsmind-Cloud"), 
		array("iconsmind-Cloud2" => "iconsmind-Cloud2"), 
		array("iconsmind-Cloud3" => "iconsmind-Cloud3"), 
		array("iconsmind-Cloud-Camera" => "iconsmind-Cloud-Camera"), 
		array("iconsmind-Cloud-Computer" => "iconsmind-Cloud-Computer"), 
		array("iconsmind-Cloud-Email" => "iconsmind-Cloud-Email"), 
		array("iconsmind-Cloud-Laptop" => "iconsmind-Cloud-Laptop"), 
		array("iconsmind-Cloud-Lock" => "iconsmind-Cloud-Lock"), 
		array("iconsmind-Cloud-Music" => "iconsmind-Cloud-Music"), 
		array("iconsmind-Cloud-Picture" => "iconsmind-Cloud-Picture"), 
		array("iconsmind-Cloud-Remove" => "iconsmind-Cloud-Remove"), 
		array("iconsmind-Clouds" => "iconsmind-Clouds"), 
		array("iconsmind-Cloud-Secure" => "iconsmind-Cloud-Secure"), 
		array("iconsmind-Cloud-Settings" => "iconsmind-Cloud-Settings"), 
		array("iconsmind-Cloud-Smartphone" => "iconsmind-Cloud-Smartphone"), 
		array("iconsmind-Cloud-Tablet" => "iconsmind-Cloud-Tablet"), 
		array("iconsmind-Cloud-Video" => "iconsmind-Cloud-Video"), 
		array("iconsmind-Clown" => "iconsmind-Clown"), 
		array("iconsmind-CMYK" => "iconsmind-CMYK"), 
		array("iconsmind-Coat" => "iconsmind-Coat"), 
		array("iconsmind-Cocktail" => "iconsmind-Cocktail"), 
		array("iconsmind-Coconut" => "iconsmind-Coconut"), 
		array("iconsmind-Coffee" => "iconsmind-Coffee"), 
		array("iconsmind-Coffee-2" => "iconsmind-Coffee-2"), 
		array("iconsmind-Coffee-Bean" => "iconsmind-Coffee-Bean"), 
		array("iconsmind-Coffee-toGo" => "iconsmind-Coffee-toGo"), 
		array("iconsmind-Coffin" => "iconsmind-Coffin"), 
		array("iconsmind-Coin" => "iconsmind-Coin"), 
		array("iconsmind-Coins" => "iconsmind-Coins"), 
		array("iconsmind-Coins-2" => "iconsmind-Coins-2"), 
		array("iconsmind-Coins-3" => "iconsmind-Coins-3"), 
		array("iconsmind-Colombia" => "iconsmind-Colombia"), 
		array("iconsmind-Colosseum" => "iconsmind-Colosseum"), 
		array("iconsmind-Column" => "iconsmind-Column"), 
		array("iconsmind-Column-2" => "iconsmind-Column-2"), 
		array("iconsmind-Column-3" => "iconsmind-Column-3"), 
		array("iconsmind-Comb" => "iconsmind-Comb"), 
		array("iconsmind-Comb-2" => "iconsmind-Comb-2"), 
		array("iconsmind-Communication-Tower" => "iconsmind-Communication-Tower"), 
		array("iconsmind-Communication-Tower2" => "iconsmind-Communication-Tower2"), 
		array("iconsmind-Compass2" => "iconsmind-Compass2"), 
		array("iconsmind-Compass-22" => "iconsmind-Compass-22"), 
		array("iconsmind-Computer" => "iconsmind-Computer"), 
		array("iconsmind-Computer-2" => "iconsmind-Computer-2"), 
		array("iconsmind-Computer-3" => "iconsmind-Computer-3"), 
		array("iconsmind-Confused" => "iconsmind-Confused"), 
		array("iconsmind-Contrast" => "iconsmind-Contrast"), 
		array("iconsmind-Cookie-Man" => "iconsmind-Cookie-Man"), 
		array("iconsmind-Cookies" => "iconsmind-Cookies"), 
		array("iconsmind-Cool" => "iconsmind-Cool"), 
		array("iconsmind-Costume" => "iconsmind-Costume"), 
		array("iconsmind-Cow" => "iconsmind-Cow"), 
		array("iconsmind-CPU" => "iconsmind-CPU"), 
		array("iconsmind-Cranium" => "iconsmind-Cranium"), 
		array("iconsmind-Credit-Card" => "iconsmind-Credit-Card"), 
		array("iconsmind-Credit-Card2" => "iconsmind-Credit-Card2"), 
		array("iconsmind-Credit-Card3" => "iconsmind-Credit-Card3"), 
		array("iconsmind-Croissant" => "iconsmind-Croissant"), 
		array("iconsmind-Crying" => "iconsmind-Crying"), 
		array("iconsmind-Cupcake" => "iconsmind-Cupcake"), 
		array("iconsmind-Danemark" => "iconsmind-Danemark"), 
		array("iconsmind-Data" => "iconsmind-Data"), 
		array("iconsmind-Data-Backup" => "iconsmind-Data-Backup"), 
		array("iconsmind-Data-Block" => "iconsmind-Data-Block"), 
		array("iconsmind-Data-Center" => "iconsmind-Data-Center"), 
		array("iconsmind-Data-Clock" => "iconsmind-Data-Clock"), 
		array("iconsmind-Data-Cloud" => "iconsmind-Data-Cloud"), 
		array("iconsmind-Data-Compress" => "iconsmind-Data-Compress"), 
		array("iconsmind-Data-Copy" => "iconsmind-Data-Copy"), 
		array("iconsmind-Data-Download" => "iconsmind-Data-Download"), 
		array("iconsmind-Data-Financial" => "iconsmind-Data-Financial"), 
		array("iconsmind-Data-Key" => "iconsmind-Data-Key"), 
		array("iconsmind-Data-Lock" => "iconsmind-Data-Lock"), 
		array("iconsmind-Data-Network" => "iconsmind-Data-Network"), 
		array("iconsmind-Data-Password" => "iconsmind-Data-Password"), 
		array("iconsmind-Data-Power" => "iconsmind-Data-Power"), 
		array("iconsmind-Data-Refresh" => "iconsmind-Data-Refresh"), 
		array("iconsmind-Data-Save" => "iconsmind-Data-Save"), 
		array("iconsmind-Data-Search" => "iconsmind-Data-Search"), 
		array("iconsmind-Data-Security" => "iconsmind-Data-Security"), 
		array("iconsmind-Data-Settings" => "iconsmind-Data-Settings"), 
		array("iconsmind-Data-Sharing" => "iconsmind-Data-Sharing"), 
		array("iconsmind-Data-Shield" => "iconsmind-Data-Shield"), 
		array("iconsmind-Data-Signal" => "iconsmind-Data-Signal"), 
		array("iconsmind-Data-Storage" => "iconsmind-Data-Storage"), 
		array("iconsmind-Data-Stream" => "iconsmind-Data-Stream"), 
		array("iconsmind-Data-Transfer" => "iconsmind-Data-Transfer"), 
		array("iconsmind-Data-Unlock" => "iconsmind-Data-Unlock"), 
		array("iconsmind-Data-Upload" => "iconsmind-Data-Upload"), 
		array("iconsmind-Data-Yes" => "iconsmind-Data-Yes"), 
		array("iconsmind-Death" => "iconsmind-Death"), 
		array("iconsmind-Debian" => "iconsmind-Debian"), 
		array("iconsmind-Dec" => "iconsmind-Dec"), 
		array("iconsmind-Decrase-Inedit" => "iconsmind-Decrase-Inedit"), 
		array("iconsmind-Deer" => "iconsmind-Deer"), 
		array("iconsmind-Deer-2" => "iconsmind-Deer-2"), 
		array("iconsmind-Delete-File" => "iconsmind-Delete-File"), 
		array("iconsmind-Depression" => "iconsmind-Depression"), 
		array("iconsmind-Device-SyncwithCloud" => "iconsmind-Device-SyncwithCloud"), 
		array("iconsmind-Diamond" => "iconsmind-Diamond"), 
		array("iconsmind-Digital-Drawing" => "iconsmind-Digital-Drawing"), 
		array("iconsmind-Dinosaur" => "iconsmind-Dinosaur"), 
		array("iconsmind-Diploma" => "iconsmind-Diploma"), 
		array("iconsmind-Diploma-2" => "iconsmind-Diploma-2"), 
		array("iconsmind-Disk" => "iconsmind-Disk"), 
		array("iconsmind-Dog" => "iconsmind-Dog"), 
		array("iconsmind-Dollar" => "iconsmind-Dollar"), 
		array("iconsmind-Dollar-Sign" => "iconsmind-Dollar-Sign"), 
		array("iconsmind-Dollar-Sign2" => "iconsmind-Dollar-Sign2"), 
		array("iconsmind-Dolphin" => "iconsmind-Dolphin"), 
		array("iconsmind-Door" => "iconsmind-Door"), 
		array("iconsmind-Double-Circle" => "iconsmind-Double-Circle"), 
		array("iconsmind-Doughnut" => "iconsmind-Doughnut"), 
		array("iconsmind-Dove" => "iconsmind-Dove"), 
		array("iconsmind-Down2" => "iconsmind-Down2"), 
		array("iconsmind-Down-2" => "iconsmind-Down-2"), 
		array("iconsmind-Down-3" => "iconsmind-Down-3"), 
		array("iconsmind-Download2" => "iconsmind-Download2"), 
		array("iconsmind-Download-fromCloud" => "iconsmind-Download-fromCloud"), 
		array("iconsmind-Dress" => "iconsmind-Dress"), 
		array("iconsmind-Duck" => "iconsmind-Duck"), 
		array("iconsmind-DVD" => "iconsmind-DVD"), 
		array("iconsmind-Eagle" => "iconsmind-Eagle"), 
		array("iconsmind-Ear" => "iconsmind-Ear"), 
		array("iconsmind-Eggs" => "iconsmind-Eggs"), 
		array("iconsmind-Egypt" => "iconsmind-Egypt"), 
		array("iconsmind-Eifel-Tower" => "iconsmind-Eifel-Tower"), 
		array("iconsmind-Elbow" => "iconsmind-Elbow"), 
		array("iconsmind-El-Castillo" => "iconsmind-El-Castillo"), 
		array("iconsmind-Elephant" => "iconsmind-Elephant"), 
		array("iconsmind-Embassy" => "iconsmind-Embassy"), 
		array("iconsmind-Empire-StateBuilding" => "iconsmind-Empire-StateBuilding"), 
		array("iconsmind-Empty-Box" => "iconsmind-Empty-Box"), 
		array("iconsmind-End2" => "iconsmind-End2"), 
		array("iconsmind-Envelope" => "iconsmind-Envelope"), 
		array("iconsmind-Envelope-2" => "iconsmind-Envelope-2"), 
		array("iconsmind-Eraser" => "iconsmind-Eraser"), 
		array("iconsmind-Eraser-2" => "iconsmind-Eraser-2"), 
		array("iconsmind-Eraser-3" => "iconsmind-Eraser-3"), 
		array("iconsmind-Euro" => "iconsmind-Euro"), 
		array("iconsmind-Euro-Sign" => "iconsmind-Euro-Sign"), 
		array("iconsmind-Euro-Sign2" => "iconsmind-Euro-Sign2"), 
		array("iconsmind-Evil" => "iconsmind-Evil"), 
		array("iconsmind-Eye2" => "iconsmind-Eye2"), 
		array("iconsmind-Eye-Blind" => "iconsmind-Eye-Blind"), 
		array("iconsmind-Eyebrow" => "iconsmind-Eyebrow"), 
		array("iconsmind-Eyebrow-2" => "iconsmind-Eyebrow-2"), 
		array("iconsmind-Eyebrow-3" => "iconsmind-Eyebrow-3"), 
		array("iconsmind-Eyeglasses-Smiley" => "iconsmind-Eyeglasses-Smiley"), 
		array("iconsmind-Eyeglasses-Smiley2" => "iconsmind-Eyeglasses-Smiley2"), 
		array("iconsmind-Eye-Invisible" => "iconsmind-Eye-Invisible"), 
		array("iconsmind-Eye-Visible" => "iconsmind-Eye-Visible"), 
		array("iconsmind-Face-Style" => "iconsmind-Face-Style"), 
		array("iconsmind-Face-Style2" => "iconsmind-Face-Style2"), 
		array("iconsmind-Face-Style3" => "iconsmind-Face-Style3"), 
		array("iconsmind-Face-Style4" => "iconsmind-Face-Style4"), 
		array("iconsmind-Face-Style5" => "iconsmind-Face-Style5"), 
		array("iconsmind-Face-Style6" => "iconsmind-Face-Style6"), 
		array("iconsmind-Factory2" => "iconsmind-Factory2"), 
		array("iconsmind-Fan" => "iconsmind-Fan"), 
		array("iconsmind-Fashion" => "iconsmind-Fashion"), 
		array("iconsmind-Fax" => "iconsmind-Fax"), 
		array("iconsmind-File" => "iconsmind-File"), 
		array("iconsmind-File-Block" => "iconsmind-File-Block"), 
		array("iconsmind-File-Bookmark" => "iconsmind-File-Bookmark"), 
		array("iconsmind-File-Chart" => "iconsmind-File-Chart"), 
		array("iconsmind-File-Clipboard" => "iconsmind-File-Clipboard"), 
		array("iconsmind-File-ClipboardFileText" => "iconsmind-File-ClipboardFileText"), 
		array("iconsmind-File-ClipboardTextImage" => "iconsmind-File-ClipboardTextImage"), 
		array("iconsmind-File-Cloud" => "iconsmind-File-Cloud"), 
		array("iconsmind-File-Copy" => "iconsmind-File-Copy"), 
		array("iconsmind-File-Copy2" => "iconsmind-File-Copy2"), 
		array("iconsmind-File-CSV" => "iconsmind-File-CSV"), 
		array("iconsmind-File-Download" => "iconsmind-File-Download"), 
		array("iconsmind-File-Edit" => "iconsmind-File-Edit"), 
		array("iconsmind-File-Excel" => "iconsmind-File-Excel"), 
		array("iconsmind-File-Favorite" => "iconsmind-File-Favorite"), 
		array("iconsmind-File-Fire" => "iconsmind-File-Fire"), 
		array("iconsmind-File-Graph" => "iconsmind-File-Graph"), 
		array("iconsmind-File-Hide" => "iconsmind-File-Hide"), 
		array("iconsmind-File-Horizontal" => "iconsmind-File-Horizontal"), 
		array("iconsmind-File-HorizontalText" => "iconsmind-File-HorizontalText"), 
		array("iconsmind-File-HTML" => "iconsmind-File-HTML"), 
		array("iconsmind-File-JPG" => "iconsmind-File-JPG"), 
		array("iconsmind-File-Link" => "iconsmind-File-Link"), 
		array("iconsmind-File-Loading" => "iconsmind-File-Loading"), 
		array("iconsmind-File-Lock" => "iconsmind-File-Lock"), 
		array("iconsmind-File-Love" => "iconsmind-File-Love"), 
		array("iconsmind-File-Music" => "iconsmind-File-Music"), 
		array("iconsmind-File-Network" => "iconsmind-File-Network"), 
		array("iconsmind-File-Pictures" => "iconsmind-File-Pictures"), 
		array("iconsmind-File-Pie" => "iconsmind-File-Pie"), 
		array("iconsmind-File-Presentation" => "iconsmind-File-Presentation"), 
		array("iconsmind-File-Refresh" => "iconsmind-File-Refresh"), 
		array("iconsmind-Files" => "iconsmind-Files"), 
		array("iconsmind-File-Search" => "iconsmind-File-Search"), 
		array("iconsmind-File-Settings" => "iconsmind-File-Settings"), 
		array("iconsmind-File-Share" => "iconsmind-File-Share"), 
		array("iconsmind-File-TextImage" => "iconsmind-File-TextImage"), 
		array("iconsmind-File-Trash" => "iconsmind-File-Trash"), 
		array("iconsmind-File-TXT" => "iconsmind-File-TXT"), 
		array("iconsmind-File-Upload" => "iconsmind-File-Upload"), 
		array("iconsmind-File-Video" => "iconsmind-File-Video"), 
		array("iconsmind-File-Word" => "iconsmind-File-Word"), 
		array("iconsmind-File-Zip" => "iconsmind-File-Zip"), 
		array("iconsmind-Financial" => "iconsmind-Financial"), 
		array("iconsmind-Finger" => "iconsmind-Finger"), 
		array("iconsmind-Fingerprint" => "iconsmind-Fingerprint"), 
		array("iconsmind-Fingerprint-2" => "iconsmind-Fingerprint-2"), 
		array("iconsmind-Firefox" => "iconsmind-Firefox"), 
		array("iconsmind-Fire-Staion" => "iconsmind-Fire-Staion"), 
		array("iconsmind-Fish" => "iconsmind-Fish"), 
		array("iconsmind-Fit-To" => "iconsmind-Fit-To"), 
		array("iconsmind-Fit-To2" => "iconsmind-Fit-To2"), 
		array("iconsmind-Flag2" => "iconsmind-Flag2"), 
		array("iconsmind-Flag-22" => "iconsmind-Flag-22"), 
		array("iconsmind-Flag-3" => "iconsmind-Flag-3"), 
		array("iconsmind-Flag-4" => "iconsmind-Flag-4"), 
		array("iconsmind-Flamingo" => "iconsmind-Flamingo"), 
		array("iconsmind-Folder" => "iconsmind-Folder"), 
		array("iconsmind-Folder-Add" => "iconsmind-Folder-Add"), 
		array("iconsmind-Folder-Archive" => "iconsmind-Folder-Archive"), 
		array("iconsmind-Folder-Binder" => "iconsmind-Folder-Binder"), 
		array("iconsmind-Folder-Binder2" => "iconsmind-Folder-Binder2"), 
		array("iconsmind-Folder-Block" => "iconsmind-Folder-Block"), 
		array("iconsmind-Folder-Bookmark" => "iconsmind-Folder-Bookmark"), 
		array("iconsmind-Folder-Close" => "iconsmind-Folder-Close"), 
		array("iconsmind-Folder-Cloud" => "iconsmind-Folder-Cloud"), 
		array("iconsmind-Folder-Delete" => "iconsmind-Folder-Delete"), 
		array("iconsmind-Folder-Download" => "iconsmind-Folder-Download"), 
		array("iconsmind-Folder-Edit" => "iconsmind-Folder-Edit"), 
		array("iconsmind-Folder-Favorite" => "iconsmind-Folder-Favorite"), 
		array("iconsmind-Folder-Fire" => "iconsmind-Folder-Fire"), 
		array("iconsmind-Folder-Hide" => "iconsmind-Folder-Hide"), 
		array("iconsmind-Folder-Link" => "iconsmind-Folder-Link"), 
		array("iconsmind-Folder-Loading" => "iconsmind-Folder-Loading"), 
		array("iconsmind-Folder-Lock" => "iconsmind-Folder-Lock"), 
		array("iconsmind-Folder-Love" => "iconsmind-Folder-Love"), 
		array("iconsmind-Folder-Music" => "iconsmind-Folder-Music"), 
		array("iconsmind-Folder-Network" => "iconsmind-Folder-Network"), 
		array("iconsmind-Folder-Open" => "iconsmind-Folder-Open"), 
		array("iconsmind-Folder-Open2" => "iconsmind-Folder-Open2"), 
		array("iconsmind-Folder-Organizing" => "iconsmind-Folder-Organizing"), 
		array("iconsmind-Folder-Pictures" => "iconsmind-Folder-Pictures"), 
		array("iconsmind-Folder-Refresh" => "iconsmind-Folder-Refresh"), 
		array("iconsmind-Folder-Remove" => "iconsmind-Folder-Remove"), 
		array("iconsmind-Folders" => "iconsmind-Folders"), 
		array("iconsmind-Folder-Search" => "iconsmind-Folder-Search"), 
		array("iconsmind-Folder-Settings" => "iconsmind-Folder-Settings"), 
		array("iconsmind-Folder-Share" => "iconsmind-Folder-Share"), 
		array("iconsmind-Folder-Trash" => "iconsmind-Folder-Trash"), 
		array("iconsmind-Folder-Upload" => "iconsmind-Folder-Upload"), 
		array("iconsmind-Folder-Video" => "iconsmind-Folder-Video"), 
		array("iconsmind-Folder-WithDocument" => "iconsmind-Folder-WithDocument"), 
		array("iconsmind-Folder-Zip" => "iconsmind-Folder-Zip"), 
		array("iconsmind-Foot" => "iconsmind-Foot"), 
		array("iconsmind-Foot-2" => "iconsmind-Foot-2"), 
		array("iconsmind-Fork" => "iconsmind-Fork"), 
		array("iconsmind-Formula" => "iconsmind-Formula"), 
		array("iconsmind-Fountain-Pen" => "iconsmind-Fountain-Pen"), 
		array("iconsmind-Fox" => "iconsmind-Fox"), 
		array("iconsmind-Frankenstein" => "iconsmind-Frankenstein"), 
		array("iconsmind-French-Fries" => "iconsmind-French-Fries"), 
		array("iconsmind-Frog" => "iconsmind-Frog"), 
		array("iconsmind-Fruits" => "iconsmind-Fruits"), 
		array("iconsmind-Full-Screen" => "iconsmind-Full-Screen"), 
		array("iconsmind-Full-Screen2" => "iconsmind-Full-Screen2"), 
		array("iconsmind-Full-View" => "iconsmind-Full-View"), 
		array("iconsmind-Full-View2" => "iconsmind-Full-View2"), 
		array("iconsmind-Funky" => "iconsmind-Funky"), 
		array("iconsmind-Funny-Bicycle" => "iconsmind-Funny-Bicycle"), 
		array("iconsmind-Gamepad" => "iconsmind-Gamepad"), 
		array("iconsmind-Gamepad-2" => "iconsmind-Gamepad-2"), 
		array("iconsmind-Gay" => "iconsmind-Gay"), 
		array("iconsmind-Geek2" => "iconsmind-Geek2"), 
		array("iconsmind-Gentleman" => "iconsmind-Gentleman"), 
		array("iconsmind-Giraffe" => "iconsmind-Giraffe"), 
		array("iconsmind-Glasses" => "iconsmind-Glasses"), 
		array("iconsmind-Glasses-2" => "iconsmind-Glasses-2"), 
		array("iconsmind-Glasses-3" => "iconsmind-Glasses-3"), 
		array("iconsmind-Glass-Water" => "iconsmind-Glass-Water"), 
		array("iconsmind-Gloves" => "iconsmind-Gloves"), 
		array("iconsmind-Go-Bottom" => "iconsmind-Go-Bottom"), 
		array("iconsmind-Gorilla" => "iconsmind-Gorilla"), 
		array("iconsmind-Go-Top" => "iconsmind-Go-Top"), 
		array("iconsmind-Grave" => "iconsmind-Grave"), 
		array("iconsmind-Graveyard" => "iconsmind-Graveyard"), 
		array("iconsmind-Greece" => "iconsmind-Greece"), 
		array("iconsmind-Hair" => "iconsmind-Hair"), 
		array("iconsmind-Hair-2" => "iconsmind-Hair-2"), 
		array("iconsmind-Hair-3" => "iconsmind-Hair-3"), 
		array("iconsmind-Halloween-HalfMoon" => "iconsmind-Halloween-HalfMoon"), 
		array("iconsmind-Halloween-Moon" => "iconsmind-Halloween-Moon"), 
		array("iconsmind-Hamburger" => "iconsmind-Hamburger"), 
		array("iconsmind-Hand" => "iconsmind-Hand"), 
		array("iconsmind-Hands" => "iconsmind-Hands"), 
		array("iconsmind-Handshake" => "iconsmind-Handshake"), 
		array("iconsmind-Hanger" => "iconsmind-Hanger"), 
		array("iconsmind-Happy" => "iconsmind-Happy"), 
		array("iconsmind-Hat" => "iconsmind-Hat"), 
		array("iconsmind-Hat-2" => "iconsmind-Hat-2"), 
		array("iconsmind-Haunted-House" => "iconsmind-Haunted-House"), 
		array("iconsmind-HD" => "iconsmind-HD"), 
		array("iconsmind-HDD" => "iconsmind-HDD"), 
		array("iconsmind-Heart2" => "iconsmind-Heart2"), 
		array("iconsmind-Heels" => "iconsmind-Heels"), 
		array("iconsmind-Heels-2" => "iconsmind-Heels-2"), 
		array("iconsmind-Hello" => "iconsmind-Hello"), 
		array("iconsmind-Hipo" => "iconsmind-Hipo"), 
		array("iconsmind-Hipster-Glasses" => "iconsmind-Hipster-Glasses"), 
		array("iconsmind-Hipster-Glasses2" => "iconsmind-Hipster-Glasses2"), 
		array("iconsmind-Hipster-Glasses3" => "iconsmind-Hipster-Glasses3"), 
		array("iconsmind-Hipster-Headphones" => "iconsmind-Hipster-Headphones"), 
		array("iconsmind-Hipster-Men" => "iconsmind-Hipster-Men"), 
		array("iconsmind-Hipster-Men2" => "iconsmind-Hipster-Men2"), 
		array("iconsmind-Hipster-Men3" => "iconsmind-Hipster-Men3"), 
		array("iconsmind-Hipster-Sunglasses" => "iconsmind-Hipster-Sunglasses"), 
		array("iconsmind-Hipster-Sunglasses2" => "iconsmind-Hipster-Sunglasses2"), 
		array("iconsmind-Hipster-Sunglasses3" => "iconsmind-Hipster-Sunglasses3"), 
		array("iconsmind-Holly" => "iconsmind-Holly"), 
		array("iconsmind-Home2" => "iconsmind-Home2"), 
		array("iconsmind-Home-2" => "iconsmind-Home-2"), 
		array("iconsmind-Home-3" => "iconsmind-Home-3"), 
		array("iconsmind-Home-4" => "iconsmind-Home-4"), 
		array("iconsmind-Honey" => "iconsmind-Honey"), 
		array("iconsmind-Hong-Kong" => "iconsmind-Hong-Kong"), 
		array("iconsmind-Hoodie" => "iconsmind-Hoodie"), 
		array("iconsmind-Horror" => "iconsmind-Horror"), 
		array("iconsmind-Horse" => "iconsmind-Horse"), 
		array("iconsmind-Hospital2" => "iconsmind-Hospital2"), 
		array("iconsmind-Host" => "iconsmind-Host"), 
		array("iconsmind-Hot-Dog" => "iconsmind-Hot-Dog"), 
		array("iconsmind-Hotel" => "iconsmind-Hotel"), 
		array("iconsmind-Hub" => "iconsmind-Hub"), 
		array("iconsmind-Humor" => "iconsmind-Humor"), 
		array("iconsmind-Ice-Cream" => "iconsmind-Ice-Cream"), 
		array("iconsmind-Idea" => "iconsmind-Idea"), 
		array("iconsmind-Inbox" => "iconsmind-Inbox"), 
		array("iconsmind-Inbox-Empty" => "iconsmind-Inbox-Empty"), 
		array("iconsmind-Inbox-Forward" => "iconsmind-Inbox-Forward"), 
		array("iconsmind-Inbox-Full" => "iconsmind-Inbox-Full"), 
		array("iconsmind-Inbox-Into" => "iconsmind-Inbox-Into"), 
		array("iconsmind-Inbox-Out" => "iconsmind-Inbox-Out"), 
		array("iconsmind-Inbox-Reply" => "iconsmind-Inbox-Reply"), 
		array("iconsmind-Increase-Inedit" => "iconsmind-Increase-Inedit"), 
		array("iconsmind-Indent-FirstLine" => "iconsmind-Indent-FirstLine"), 
		array("iconsmind-Indent-LeftMargin" => "iconsmind-Indent-LeftMargin"), 
		array("iconsmind-Indent-RightMargin" => "iconsmind-Indent-RightMargin"), 
		array("iconsmind-India" => "iconsmind-India"), 
		array("iconsmind-Internet-Explorer" => "iconsmind-Internet-Explorer"), 
		array("iconsmind-Internet-Smiley" => "iconsmind-Internet-Smiley"), 
		array("iconsmind-iOS-Apple" => "iconsmind-iOS-Apple"), 
		array("iconsmind-Israel" => "iconsmind-Israel"), 
		array("iconsmind-Jacket" => "iconsmind-Jacket"), 
		array("iconsmind-Jamaica" => "iconsmind-Jamaica"), 
		array("iconsmind-Japan" => "iconsmind-Japan"), 
		array("iconsmind-Japanese-Gate" => "iconsmind-Japanese-Gate"), 
		array("iconsmind-Jeans" => "iconsmind-Jeans"), 
		array("iconsmind-Joystick" => "iconsmind-Joystick"), 
		array("iconsmind-Juice" => "iconsmind-Juice"), 
		array("iconsmind-Kangoroo" => "iconsmind-Kangoroo"), 
		array("iconsmind-Kenya" => "iconsmind-Kenya"), 
		array("iconsmind-Keyboard" => "iconsmind-Keyboard"), 
		array("iconsmind-Keypad" => "iconsmind-Keypad"), 
		array("iconsmind-King" => "iconsmind-King"), 
		array("iconsmind-Kiss" => "iconsmind-Kiss"), 
		array("iconsmind-Knee" => "iconsmind-Knee"), 
		array("iconsmind-Knife" => "iconsmind-Knife"), 
		array("iconsmind-Knight" => "iconsmind-Knight"), 
		array("iconsmind-Koala" => "iconsmind-Koala"), 
		array("iconsmind-Korea" => "iconsmind-Korea"), 
		array("iconsmind-Lantern" => "iconsmind-Lantern"), 
		array("iconsmind-Laptop" => "iconsmind-Laptop"), 
		array("iconsmind-Laptop-2" => "iconsmind-Laptop-2"), 
		array("iconsmind-Laptop-3" => "iconsmind-Laptop-3"), 
		array("iconsmind-Laptop-Phone" => "iconsmind-Laptop-Phone"), 
		array("iconsmind-Laptop-Tablet" => "iconsmind-Laptop-Tablet"), 
		array("iconsmind-Laughing" => "iconsmind-Laughing"), 
		array("iconsmind-Leaning-Tower" => "iconsmind-Leaning-Tower"), 
		array("iconsmind-Left2" => "iconsmind-Left2"), 
		array("iconsmind-Left-2" => "iconsmind-Left-2"), 
		array("iconsmind-Left-3" => "iconsmind-Left-3"), 
		array("iconsmind-Left-ToRight" => "iconsmind-Left-ToRight"), 
		array("iconsmind-Leg" => "iconsmind-Leg"), 
		array("iconsmind-Leg-2" => "iconsmind-Leg-2"), 
		array("iconsmind-Lemon" => "iconsmind-Lemon"), 
		array("iconsmind-Leopard" => "iconsmind-Leopard"), 
		array("iconsmind-Letter-Close" => "iconsmind-Letter-Close"), 
		array("iconsmind-Letter-Open" => "iconsmind-Letter-Open"), 
		array("iconsmind-Letter-Sent" => "iconsmind-Letter-Sent"), 
		array("iconsmind-Library2" => "iconsmind-Library2"), 
		array("iconsmind-Lighthouse" => "iconsmind-Lighthouse"), 
		array("iconsmind-Line-Chart" => "iconsmind-Line-Chart"), 
		array("iconsmind-Line-Chart2" => "iconsmind-Line-Chart2"), 
		array("iconsmind-Line-Chart3" => "iconsmind-Line-Chart3"), 
		array("iconsmind-Line-Chart4" => "iconsmind-Line-Chart4"), 
		array("iconsmind-Line-Spacing" => "iconsmind-Line-Spacing"), 
		array("iconsmind-Linux" => "iconsmind-Linux"), 
		array("iconsmind-Lion" => "iconsmind-Lion"), 
		array("iconsmind-Lollipop" => "iconsmind-Lollipop"), 
		array("iconsmind-Lollipop-2" => "iconsmind-Lollipop-2"), 
		array("iconsmind-Loop" => "iconsmind-Loop"), 
		array("iconsmind-Love2" => "iconsmind-Love2"), 
		array("iconsmind-Mail" => "iconsmind-Mail"), 
		array("iconsmind-Mail-2" => "iconsmind-Mail-2"), 
		array("iconsmind-Mail-3" => "iconsmind-Mail-3"), 
		array("iconsmind-Mail-Add" => "iconsmind-Mail-Add"), 
		array("iconsmind-Mail-Attachement" => "iconsmind-Mail-Attachement"), 
		array("iconsmind-Mail-Block" => "iconsmind-Mail-Block"), 
		array("iconsmind-Mailbox-Empty" => "iconsmind-Mailbox-Empty"), 
		array("iconsmind-Mailbox-Full" => "iconsmind-Mailbox-Full"), 
		array("iconsmind-Mail-Delete" => "iconsmind-Mail-Delete"), 
		array("iconsmind-Mail-Favorite" => "iconsmind-Mail-Favorite"), 
		array("iconsmind-Mail-Forward" => "iconsmind-Mail-Forward"), 
		array("iconsmind-Mail-Gallery" => "iconsmind-Mail-Gallery"), 
		array("iconsmind-Mail-Inbox" => "iconsmind-Mail-Inbox"), 
		array("iconsmind-Mail-Link" => "iconsmind-Mail-Link"), 
		array("iconsmind-Mail-Lock" => "iconsmind-Mail-Lock"), 
		array("iconsmind-Mail-Love" => "iconsmind-Mail-Love"), 
		array("iconsmind-Mail-Money" => "iconsmind-Mail-Money"), 
		array("iconsmind-Mail-Open" => "iconsmind-Mail-Open"), 
		array("iconsmind-Mail-Outbox" => "iconsmind-Mail-Outbox"), 
		array("iconsmind-Mail-Password" => "iconsmind-Mail-Password"), 
		array("iconsmind-Mail-Photo" => "iconsmind-Mail-Photo"), 
		array("iconsmind-Mail-Read" => "iconsmind-Mail-Read"), 
		array("iconsmind-Mail-Removex" => "iconsmind-Mail-Removex"), 
		array("iconsmind-Mail-Reply" => "iconsmind-Mail-Reply"), 
		array("iconsmind-Mail-ReplyAll" => "iconsmind-Mail-ReplyAll"), 
		array("iconsmind-Mail-Search" => "iconsmind-Mail-Search"), 
		array("iconsmind-Mail-Send" => "iconsmind-Mail-Send"), 
		array("iconsmind-Mail-Settings" => "iconsmind-Mail-Settings"), 
		array("iconsmind-Mail-Unread" => "iconsmind-Mail-Unread"), 
		array("iconsmind-Mail-Video" => "iconsmind-Mail-Video"), 
		array("iconsmind-Mail-withAtSign" => "iconsmind-Mail-withAtSign"), 
		array("iconsmind-Mail-WithCursors" => "iconsmind-Mail-WithCursors"), 
		array("iconsmind-Mans-Underwear" => "iconsmind-Mans-Underwear"), 
		array("iconsmind-Mans-Underwear2" => "iconsmind-Mans-Underwear2"), 
		array("iconsmind-Marker" => "iconsmind-Marker"), 
		array("iconsmind-Marker-2" => "iconsmind-Marker-2"), 
		array("iconsmind-Marker-3" => "iconsmind-Marker-3"), 
		array("iconsmind-Martini-Glass" => "iconsmind-Martini-Glass"), 
		array("iconsmind-Master-Card" => "iconsmind-Master-Card"), 
		array("iconsmind-Maximize" => "iconsmind-Maximize"), 
		array("iconsmind-Megaphone" => "iconsmind-Megaphone"), 
		array("iconsmind-Mexico" => "iconsmind-Mexico"), 
		array("iconsmind-Milk-Bottle" => "iconsmind-Milk-Bottle"), 
		array("iconsmind-Minimize" => "iconsmind-Minimize"), 
		array("iconsmind-Money" => "iconsmind-Money"), 
		array("iconsmind-Money-2" => "iconsmind-Money-2"), 
		array("iconsmind-Money-Bag" => "iconsmind-Money-Bag"), 
		array("iconsmind-Monitor" => "iconsmind-Monitor"), 
		array("iconsmind-Monitor-2" => "iconsmind-Monitor-2"), 
		array("iconsmind-Monitor-3" => "iconsmind-Monitor-3"), 
		array("iconsmind-Monitor-4" => "iconsmind-Monitor-4"), 
		array("iconsmind-Monitor-5" => "iconsmind-Monitor-5"), 
		array("iconsmind-Monitor-Laptop" => "iconsmind-Monitor-Laptop"), 
		array("iconsmind-Monitor-phone" => "iconsmind-Monitor-phone"), 
		array("iconsmind-Monitor-Tablet" => "iconsmind-Monitor-Tablet"), 
		array("iconsmind-Monitor-Vertical" => "iconsmind-Monitor-Vertical"), 
		array("iconsmind-Monkey" => "iconsmind-Monkey"), 
		array("iconsmind-Monster" => "iconsmind-Monster"), 
		array("iconsmind-Morocco" => "iconsmind-Morocco"), 
		array("iconsmind-Mouse" => "iconsmind-Mouse"), 
		array("iconsmind-Mouse-2" => "iconsmind-Mouse-2"), 
		array("iconsmind-Mouse-3" => "iconsmind-Mouse-3"), 
		array("iconsmind-Moustache-Smiley" => "iconsmind-Moustache-Smiley"), 
		array("iconsmind-Museum" => "iconsmind-Museum"), 
		array("iconsmind-Mushroom" => "iconsmind-Mushroom"), 
		array("iconsmind-Mustache" => "iconsmind-Mustache"), 
		array("iconsmind-Mustache-2" => "iconsmind-Mustache-2"), 
		array("iconsmind-Mustache-3" => "iconsmind-Mustache-3"), 
		array("iconsmind-Mustache-4" => "iconsmind-Mustache-4"), 
		array("iconsmind-Mustache-5" => "iconsmind-Mustache-5"), 
		array("iconsmind-Navigate-End" => "iconsmind-Navigate-End"), 
		array("iconsmind-Navigat-Start" => "iconsmind-Navigat-Start"), 
		array("iconsmind-Nepal" => "iconsmind-Nepal"), 
		array("iconsmind-Netscape" => "iconsmind-Netscape"), 
		array("iconsmind-New-Mail" => "iconsmind-New-Mail"), 
		array("iconsmind-Newspaper" => "iconsmind-Newspaper"), 
		array("iconsmind-Newspaper-2" => "iconsmind-Newspaper-2"), 
		array("iconsmind-No-Battery" => "iconsmind-No-Battery"), 
		array("iconsmind-Noose" => "iconsmind-Noose"), 
		array("iconsmind-Note" => "iconsmind-Note"), 
		array("iconsmind-Notepad" => "iconsmind-Notepad"), 
		array("iconsmind-Notepad-2" => "iconsmind-Notepad-2"), 
		array("iconsmind-Office" => "iconsmind-Office"), 
		array("iconsmind-Old-Camera" => "iconsmind-Old-Camera"), 
		array("iconsmind-Old-Cassette" => "iconsmind-Old-Cassette"), 
		array("iconsmind-Old-Sticky" => "iconsmind-Old-Sticky"), 
		array("iconsmind-Old-Sticky2" => "iconsmind-Old-Sticky2"), 
		array("iconsmind-Old-Telephone" => "iconsmind-Old-Telephone"), 
		array("iconsmind-Open-Banana" => "iconsmind-Open-Banana"), 
		array("iconsmind-Open-Book" => "iconsmind-Open-Book"), 
		array("iconsmind-Opera" => "iconsmind-Opera"), 
		array("iconsmind-Opera-House" => "iconsmind-Opera-House"), 
		array("iconsmind-Orientation2" => "iconsmind-Orientation2"), 
		array("iconsmind-Orientation-2" => "iconsmind-Orientation-2"), 
		array("iconsmind-Ornament" => "iconsmind-Ornament"), 
		array("iconsmind-Owl" => "iconsmind-Owl"), 
		array("iconsmind-Paintbrush" => "iconsmind-Paintbrush"), 
		array("iconsmind-Palette" => "iconsmind-Palette"), 
		array("iconsmind-Panda" => "iconsmind-Panda"), 
		array("iconsmind-Pantheon" => "iconsmind-Pantheon"), 
		array("iconsmind-Pantone" => "iconsmind-Pantone"), 
		array("iconsmind-Pants" => "iconsmind-Pants"), 
		array("iconsmind-Paper" => "iconsmind-Paper"), 
		array("iconsmind-Parrot" => "iconsmind-Parrot"), 
		array("iconsmind-Pawn" => "iconsmind-Pawn"), 
		array("iconsmind-Pen" => "iconsmind-Pen"), 
		array("iconsmind-Pen-2" => "iconsmind-Pen-2"), 
		array("iconsmind-Pen-3" => "iconsmind-Pen-3"), 
		array("iconsmind-Pen-4" => "iconsmind-Pen-4"), 
		array("iconsmind-Pen-5" => "iconsmind-Pen-5"), 
		array("iconsmind-Pen-6" => "iconsmind-Pen-6"), 
		array("iconsmind-Pencil" => "iconsmind-Pencil"), 
		array("iconsmind-Pencil-Ruler" => "iconsmind-Pencil-Ruler"), 
		array("iconsmind-Penguin" => "iconsmind-Penguin"), 
		array("iconsmind-Pentagon" => "iconsmind-Pentagon"), 
		array("iconsmind-People-onCloud" => "iconsmind-People-onCloud"), 
		array("iconsmind-Pepper" => "iconsmind-Pepper"), 
		array("iconsmind-Pepper-withFire" => "iconsmind-Pepper-withFire"), 
		array("iconsmind-Petronas-Tower" => "iconsmind-Petronas-Tower"), 
		array("iconsmind-Philipines" => "iconsmind-Philipines"), 
		array("iconsmind-Phone" => "iconsmind-Phone"), 
		array("iconsmind-Phone-2" => "iconsmind-Phone-2"), 
		array("iconsmind-Phone-3" => "iconsmind-Phone-3"), 
		array("iconsmind-Phone-3G" => "iconsmind-Phone-3G"), 
		array("iconsmind-Phone-4G" => "iconsmind-Phone-4G"), 
		array("iconsmind-Phone-Simcard" => "iconsmind-Phone-Simcard"), 
		array("iconsmind-Phone-SMS" => "iconsmind-Phone-SMS"), 
		array("iconsmind-Phone-Wifi" => "iconsmind-Phone-Wifi"), 
		array("iconsmind-Pi" => "iconsmind-Pi"), 
		array("iconsmind-Pie-Chart" => "iconsmind-Pie-Chart"), 
		array("iconsmind-Pie-Chart2" => "iconsmind-Pie-Chart2"), 
		array("iconsmind-Pie-Chart3" => "iconsmind-Pie-Chart3"), 
		array("iconsmind-Pipette" => "iconsmind-Pipette"), 
		array("iconsmind-Piramids" => "iconsmind-Piramids"), 
		array("iconsmind-Pizza" => "iconsmind-Pizza"), 
		array("iconsmind-Pizza-Slice" => "iconsmind-Pizza-Slice"), 
		array("iconsmind-Plastic-CupPhone" => "iconsmind-Plastic-CupPhone"), 
		array("iconsmind-Plastic-CupPhone2" => "iconsmind-Plastic-CupPhone2"), 
		array("iconsmind-Plate" => "iconsmind-Plate"), 
		array("iconsmind-Plates" => "iconsmind-Plates"), 
		array("iconsmind-Plug-In" => "iconsmind-Plug-In"), 
		array("iconsmind-Plug-In2" => "iconsmind-Plug-In2"), 
		array("iconsmind-Poland" => "iconsmind-Poland"), 
		array("iconsmind-Police-Station" => "iconsmind-Police-Station"), 
		array("iconsmind-Polo-Shirt" => "iconsmind-Polo-Shirt"), 
		array("iconsmind-Portugal" => "iconsmind-Portugal"), 
		array("iconsmind-Post-Mail" => "iconsmind-Post-Mail"), 
		array("iconsmind-Post-Mail2" => "iconsmind-Post-Mail2"), 
		array("iconsmind-Post-Office" => "iconsmind-Post-Office"), 
		array("iconsmind-Pound" => "iconsmind-Pound"), 
		array("iconsmind-Pound-Sign" => "iconsmind-Pound-Sign"), 
		array("iconsmind-Pound-Sign2" => "iconsmind-Pound-Sign2"), 
		array("iconsmind-Power" => "iconsmind-Power"), 
		array("iconsmind-Power-Cable" => "iconsmind-Power-Cable"), 
		array("iconsmind-Prater" => "iconsmind-Prater"), 
		array("iconsmind-Present" => "iconsmind-Present"), 
		array("iconsmind-Presents" => "iconsmind-Presents"), 
		array("iconsmind-Printer" => "iconsmind-Printer"), 
		array("iconsmind-Projector" => "iconsmind-Projector"), 
		array("iconsmind-Projector-2" => "iconsmind-Projector-2"), 
		array("iconsmind-Pumpkin" => "iconsmind-Pumpkin"), 
		array("iconsmind-Punk" => "iconsmind-Punk"), 
		array("iconsmind-Queen" => "iconsmind-Queen"), 
		array("iconsmind-Quill" => "iconsmind-Quill"), 
		array("iconsmind-Quill-2" => "iconsmind-Quill-2"), 
		array("iconsmind-Quill-3" => "iconsmind-Quill-3"), 
		array("iconsmind-Ram" => "iconsmind-Ram"), 
		array("iconsmind-Redhat" => "iconsmind-Redhat"), 
		array("iconsmind-Reload2" => "iconsmind-Reload2"), 
		array("iconsmind-Reload-2" => "iconsmind-Reload-2"), 
		array("iconsmind-Remote-Controll" => "iconsmind-Remote-Controll"), 
		array("iconsmind-Remote-Controll2" => "iconsmind-Remote-Controll2"), 
		array("iconsmind-Remove-File" => "iconsmind-Remove-File"), 
		array("iconsmind-Repeat3" => "iconsmind-Repeat3"), 
		array("iconsmind-Repeat-22" => "iconsmind-Repeat-22"), 
		array("iconsmind-Repeat-3" => "iconsmind-Repeat-3"), 
		array("iconsmind-Repeat-4" => "iconsmind-Repeat-4"), 
		array("iconsmind-Resize" => "iconsmind-Resize"), 
		array("iconsmind-Retro" => "iconsmind-Retro"), 
		array("iconsmind-RGB" => "iconsmind-RGB"), 
		array("iconsmind-Right2" => "iconsmind-Right2"), 
		array("iconsmind-Right-2" => "iconsmind-Right-2"), 
		array("iconsmind-Right-3" => "iconsmind-Right-3"), 
		array("iconsmind-Right-ToLeft" => "iconsmind-Right-ToLeft"), 
		array("iconsmind-Robot2" => "iconsmind-Robot2"), 
		array("iconsmind-Roller" => "iconsmind-Roller"), 
		array("iconsmind-Roof" => "iconsmind-Roof"), 
		array("iconsmind-Rook" => "iconsmind-Rook"), 
		array("iconsmind-Router" => "iconsmind-Router"), 
		array("iconsmind-Router-2" => "iconsmind-Router-2"), 
		array("iconsmind-Ruler" => "iconsmind-Ruler"), 
		array("iconsmind-Ruler-2" => "iconsmind-Ruler-2"), 
		array("iconsmind-Safari" => "iconsmind-Safari"), 
		array("iconsmind-Safe-Box2" => "iconsmind-Safe-Box2"), 
		array("iconsmind-Santa-Claus" => "iconsmind-Santa-Claus"), 
		array("iconsmind-Santa-Claus2" => "iconsmind-Santa-Claus2"), 
		array("iconsmind-Santa-onSled" => "iconsmind-Santa-onSled"), 
		array("iconsmind-Scarf" => "iconsmind-Scarf"), 
		array("iconsmind-Scissor" => "iconsmind-Scissor"), 
		array("iconsmind-Scotland" => "iconsmind-Scotland"), 
		array("iconsmind-Sea-Dog" => "iconsmind-Sea-Dog"), 
		array("iconsmind-Search-onCloud" => "iconsmind-Search-onCloud"), 
		array("iconsmind-Security-Smiley" => "iconsmind-Security-Smiley"), 
		array("iconsmind-Serbia" => "iconsmind-Serbia"), 
		array("iconsmind-Server" => "iconsmind-Server"), 
		array("iconsmind-Server-2" => "iconsmind-Server-2"), 
		array("iconsmind-Servers" => "iconsmind-Servers"), 
		array("iconsmind-Share-onCloud" => "iconsmind-Share-onCloud"), 
		array("iconsmind-Shark" => "iconsmind-Shark"), 
		array("iconsmind-Sheep" => "iconsmind-Sheep"), 
		array("iconsmind-Shirt" => "iconsmind-Shirt"), 
		array("iconsmind-Shoes" => "iconsmind-Shoes"), 
		array("iconsmind-Shoes-2" => "iconsmind-Shoes-2"), 
		array("iconsmind-Short-Pants" => "iconsmind-Short-Pants"), 
		array("iconsmind-Shuffle2" => "iconsmind-Shuffle2"), 
		array("iconsmind-Shuffle-22" => "iconsmind-Shuffle-22"), 
		array("iconsmind-Singapore" => "iconsmind-Singapore"), 
		array("iconsmind-Skeleton" => "iconsmind-Skeleton"), 
		array("iconsmind-Skirt" => "iconsmind-Skirt"), 
		array("iconsmind-Skull" => "iconsmind-Skull"), 
		array("iconsmind-Sled" => "iconsmind-Sled"), 
		array("iconsmind-Sled-withGifts" => "iconsmind-Sled-withGifts"), 
		array("iconsmind-Sleeping" => "iconsmind-Sleeping"), 
		array("iconsmind-Slippers" => "iconsmind-Slippers"), 
		array("iconsmind-Smart" => "iconsmind-Smart"), 
		array("iconsmind-Smartphone" => "iconsmind-Smartphone"), 
		array("iconsmind-Smartphone-2" => "iconsmind-Smartphone-2"), 
		array("iconsmind-Smartphone-3" => "iconsmind-Smartphone-3"), 
		array("iconsmind-Smartphone-4" => "iconsmind-Smartphone-4"), 
		array("iconsmind-Smile" => "iconsmind-Smile"), 
		array("iconsmind-Smoking-Pipe" => "iconsmind-Smoking-Pipe"), 
		array("iconsmind-Snake" => "iconsmind-Snake"), 
		array("iconsmind-Snow-Dome" => "iconsmind-Snow-Dome"), 
		array("iconsmind-Snowflake2" => "iconsmind-Snowflake2"), 
		array("iconsmind-Snowman" => "iconsmind-Snowman"), 
		array("iconsmind-Socks" => "iconsmind-Socks"), 
		array("iconsmind-Soup" => "iconsmind-Soup"), 
		array("iconsmind-South-Africa" => "iconsmind-South-Africa"), 
		array("iconsmind-Space-Needle" => "iconsmind-Space-Needle"), 
		array("iconsmind-Spain" => "iconsmind-Spain"), 
		array("iconsmind-Spam-Mail" => "iconsmind-Spam-Mail"), 
		array("iconsmind-Speaker2" => "iconsmind-Speaker2"), 
		array("iconsmind-Spell-Check" => "iconsmind-Spell-Check"), 
		array("iconsmind-Spell-CheckABC" => "iconsmind-Spell-CheckABC"), 
		array("iconsmind-Spider" => "iconsmind-Spider"), 
		array("iconsmind-Spiderweb" => "iconsmind-Spiderweb"), 
		array("iconsmind-Spoder" => "iconsmind-Spoder"), 
		array("iconsmind-Spoon" => "iconsmind-Spoon"), 
		array("iconsmind-Sports-Clothings1" => "iconsmind-Sports-Clothings1"), 
		array("iconsmind-Sports-Clothings2" => "iconsmind-Sports-Clothings2"), 
		array("iconsmind-Sports-Shirt" => "iconsmind-Sports-Shirt"), 
		array("iconsmind-Spray" => "iconsmind-Spray"), 
		array("iconsmind-Squirrel" => "iconsmind-Squirrel"), 
		array("iconsmind-Stamp" => "iconsmind-Stamp"), 
		array("iconsmind-Stamp-2" => "iconsmind-Stamp-2"), 
		array("iconsmind-Stapler" => "iconsmind-Stapler"), 
		array("iconsmind-Star" => "iconsmind-Star"), 
		array("iconsmind-Starfish" => "iconsmind-Starfish"), 
		array("iconsmind-Start2" => "iconsmind-Start2"), 
		array("iconsmind-St-BasilsCathedral" => "iconsmind-St-BasilsCathedral"), 
		array("iconsmind-St-PaulsCathedral" => "iconsmind-St-PaulsCathedral"), 
		array("iconsmind-Structure" => "iconsmind-Structure"), 
		array("iconsmind-Student-Hat" => "iconsmind-Student-Hat"), 
		array("iconsmind-Student-Hat2" => "iconsmind-Student-Hat2"), 
		array("iconsmind-Suit" => "iconsmind-Suit"), 
		array("iconsmind-Sum2" => "iconsmind-Sum2"), 
		array("iconsmind-Sunglasses" => "iconsmind-Sunglasses"), 
		array("iconsmind-Sunglasses-2" => "iconsmind-Sunglasses-2"), 
		array("iconsmind-Sunglasses-3" => "iconsmind-Sunglasses-3"), 
		array("iconsmind-Sunglasses-Smiley" => "iconsmind-Sunglasses-Smiley"), 
		array("iconsmind-Sunglasses-Smiley2" => "iconsmind-Sunglasses-Smiley2"), 
		array("iconsmind-Sunglasses-W" => "iconsmind-Sunglasses-W"), 
		array("iconsmind-Sunglasses-W2" => "iconsmind-Sunglasses-W2"), 
		array("iconsmind-Sunglasses-W3" => "iconsmind-Sunglasses-W3"), 
		array("iconsmind-Surprise" => "iconsmind-Surprise"), 
		array("iconsmind-Sushi" => "iconsmind-Sushi"), 
		array("iconsmind-Sweden" => "iconsmind-Sweden"), 
		array("iconsmind-Swimming-Short" => "iconsmind-Swimming-Short"), 
		array("iconsmind-Swimmwear" => "iconsmind-Swimmwear"), 
		array("iconsmind-Switzerland" => "iconsmind-Switzerland"), 
		array("iconsmind-Sync" => "iconsmind-Sync"), 
		array("iconsmind-Sync-Cloud" => "iconsmind-Sync-Cloud"), 
		array("iconsmind-Tablet" => "iconsmind-Tablet"), 
		array("iconsmind-Tablet-2" => "iconsmind-Tablet-2"), 
		array("iconsmind-Tablet-3" => "iconsmind-Tablet-3"), 
		array("iconsmind-Tablet-Orientation" => "iconsmind-Tablet-Orientation"), 
		array("iconsmind-Tablet-Phone" => "iconsmind-Tablet-Phone"), 
		array("iconsmind-Tablet-Vertical" => "iconsmind-Tablet-Vertical"), 
		array("iconsmind-Tactic" => "iconsmind-Tactic"), 
		array("iconsmind-Taj-Mahal" => "iconsmind-Taj-Mahal"), 
		array("iconsmind-Teapot" => "iconsmind-Teapot"), 
		array("iconsmind-Tee-Mug" => "iconsmind-Tee-Mug"), 
		array("iconsmind-Telephone" => "iconsmind-Telephone"), 
		array("iconsmind-Telephone-2" => "iconsmind-Telephone-2"), 
		array("iconsmind-Temple" => "iconsmind-Temple"), 
		array("iconsmind-Thailand" => "iconsmind-Thailand"), 
		array("iconsmind-The-WhiteHouse" => "iconsmind-The-WhiteHouse"), 
		array("iconsmind-Three-ArrowFork" => "iconsmind-Three-ArrowFork"), 
		array("iconsmind-Thumbs-DownSmiley" => "iconsmind-Thumbs-DownSmiley"), 
		array("iconsmind-Thumbs-UpSmiley" => "iconsmind-Thumbs-UpSmiley"), 
		array("iconsmind-Tie" => "iconsmind-Tie"), 
		array("iconsmind-Tie-2" => "iconsmind-Tie-2"), 
		array("iconsmind-Tie-3" => "iconsmind-Tie-3"), 
		array("iconsmind-Tiger" => "iconsmind-Tiger"), 
		array("iconsmind-Time-Clock" => "iconsmind-Time-Clock"), 
		array("iconsmind-To-Bottom" => "iconsmind-To-Bottom"), 
		array("iconsmind-To-Bottom2" => "iconsmind-To-Bottom2"), 
		array("iconsmind-Token" => "iconsmind-Token"), 
		array("iconsmind-To-Left" => "iconsmind-To-Left"), 
		array("iconsmind-Tomato" => "iconsmind-Tomato"), 
		array("iconsmind-Tongue" => "iconsmind-Tongue"), 
		array("iconsmind-Tooth" => "iconsmind-Tooth"), 
		array("iconsmind-Tooth-2" => "iconsmind-Tooth-2"), 
		array("iconsmind-Top-ToBottom" => "iconsmind-Top-ToBottom"), 
		array("iconsmind-To-Right" => "iconsmind-To-Right"), 
		array("iconsmind-To-Top" => "iconsmind-To-Top"), 
		array("iconsmind-To-Top2" => "iconsmind-To-Top2"), 
		array("iconsmind-Tower" => "iconsmind-Tower"), 
		array("iconsmind-Tower-2" => "iconsmind-Tower-2"), 
		array("iconsmind-Tower-Bridge" => "iconsmind-Tower-Bridge"), 
		array("iconsmind-Transform" => "iconsmind-Transform"), 
		array("iconsmind-Transform-2" => "iconsmind-Transform-2"), 
		array("iconsmind-Transform-3" => "iconsmind-Transform-3"), 
		array("iconsmind-Transform-4" => "iconsmind-Transform-4"), 
		array("iconsmind-Tree2" => "iconsmind-Tree2"), 
		array("iconsmind-Tree-22" => "iconsmind-Tree-22"), 
		array("iconsmind-Triangle-ArrowDown" => "iconsmind-Triangle-ArrowDown"), 
		array("iconsmind-Triangle-ArrowLeft" => "iconsmind-Triangle-ArrowLeft"), 
		array("iconsmind-Triangle-ArrowRight" => "iconsmind-Triangle-ArrowRight"), 
		array("iconsmind-Triangle-ArrowUp" => "iconsmind-Triangle-ArrowUp"), 
		array("iconsmind-T-Shirt" => "iconsmind-T-Shirt"), 
		array("iconsmind-Turkey" => "iconsmind-Turkey"), 
		array("iconsmind-Turn-Down" => "iconsmind-Turn-Down"), 
		array("iconsmind-Turn-Down2" => "iconsmind-Turn-Down2"), 
		array("iconsmind-Turn-DownFromLeft" => "iconsmind-Turn-DownFromLeft"), 
		array("iconsmind-Turn-DownFromRight" => "iconsmind-Turn-DownFromRight"), 
		array("iconsmind-Turn-Left" => "iconsmind-Turn-Left"), 
		array("iconsmind-Turn-Left3" => "iconsmind-Turn-Left3"), 
		array("iconsmind-Turn-Right" => "iconsmind-Turn-Right"), 
		array("iconsmind-Turn-Right3" => "iconsmind-Turn-Right3"), 
		array("iconsmind-Turn-Up" => "iconsmind-Turn-Up"), 
		array("iconsmind-Turn-Up2" => "iconsmind-Turn-Up2"), 
		array("iconsmind-Turtle" => "iconsmind-Turtle"), 
		array("iconsmind-Tuxedo" => "iconsmind-Tuxedo"), 
		array("iconsmind-Ukraine" => "iconsmind-Ukraine"), 
		array("iconsmind-Umbrela" => "iconsmind-Umbrela"), 
		array("iconsmind-United-Kingdom" => "iconsmind-United-Kingdom"), 
		array("iconsmind-United-States" => "iconsmind-United-States"), 
		array("iconsmind-University" => "iconsmind-University"), 
		array("iconsmind-Up2" => "iconsmind-Up2"), 
		array("iconsmind-Up-2" => "iconsmind-Up-2"), 
		array("iconsmind-Up-3" => "iconsmind-Up-3"), 
		array("iconsmind-Upload2" => "iconsmind-Upload2"), 
		array("iconsmind-Upload-toCloud" => "iconsmind-Upload-toCloud"), 
		array("iconsmind-Usb" => "iconsmind-Usb"), 
		array("iconsmind-Usb-2" => "iconsmind-Usb-2"), 
		array("iconsmind-Usb-Cable" => "iconsmind-Usb-Cable"), 
		array("iconsmind-Vector" => "iconsmind-Vector"), 
		array("iconsmind-Vector-2" => "iconsmind-Vector-2"), 
		array("iconsmind-Vector-3" => "iconsmind-Vector-3"), 
		array("iconsmind-Vector-4" => "iconsmind-Vector-4"), 
		array("iconsmind-Vector-5" => "iconsmind-Vector-5"), 
		array("iconsmind-Vest" => "iconsmind-Vest"), 
		array("iconsmind-Vietnam" => "iconsmind-Vietnam"), 
		array("iconsmind-View-Height" => "iconsmind-View-Height"), 
		array("iconsmind-View-Width" => "iconsmind-View-Width"), 
		array("iconsmind-Visa" => "iconsmind-Visa"), 
		array("iconsmind-Voicemail" => "iconsmind-Voicemail"), 
		array("iconsmind-VPN" => "iconsmind-VPN"), 
		array("iconsmind-Wacom-Tablet" => "iconsmind-Wacom-Tablet"), 
		array("iconsmind-Walkie-Talkie" => "iconsmind-Walkie-Talkie"), 
		array("iconsmind-Wallet" => "iconsmind-Wallet"), 
		array("iconsmind-Wallet-2" => "iconsmind-Wallet-2"), 
		array("iconsmind-Warehouse" => "iconsmind-Warehouse"), 
		array("iconsmind-Webcam" => "iconsmind-Webcam"), 
		array("iconsmind-Wifi" => "iconsmind-Wifi"), 
		array("iconsmind-Wifi-2" => "iconsmind-Wifi-2"), 
		array("iconsmind-Wifi-Keyboard" => "iconsmind-Wifi-Keyboard"), 
		array("iconsmind-Window" => "iconsmind-Window"), 
		array("iconsmind-Windows" => "iconsmind-Windows"), 
		array("iconsmind-Windows-Microsoft" => "iconsmind-Windows-Microsoft"), 
		array("iconsmind-Wine-Bottle" => "iconsmind-Wine-Bottle"), 
		array("iconsmind-Wine-Glass" => "iconsmind-Wine-Glass"), 
		array("iconsmind-Wink" => "iconsmind-Wink"), 
		array("iconsmind-Wireless" => "iconsmind-Wireless"), 
		array("iconsmind-Witch" => "iconsmind-Witch"), 
		array("iconsmind-Witch-Hat" => "iconsmind-Witch-Hat"), 
		array("iconsmind-Wizard" => "iconsmind-Wizard"), 
		array("iconsmind-Wolf" => "iconsmind-Wolf"), 
		array("iconsmind-Womans-Underwear" => "iconsmind-Womans-Underwear"), 
		array("iconsmind-Womans-Underwear2" => "iconsmind-Womans-Underwear2"), 
		array("iconsmind-Worker-Clothes" => "iconsmind-Worker-Clothes"), 
		array("iconsmind-Wreath" => "iconsmind-Wreath"), 
		array("iconsmind-Zebra" => "iconsmind-Zebra"), 
		array("iconsmind-Zombie" => "iconsmind-Zombie")
	);
	return array_merge( $icons, $iconsmind_icons );
}






add_filter( 'vc_iconpicker-type-steadysets', 'vc_iconpicker_type_steadysets' );

function vc_iconpicker_type_steadysets( $icons ) {
	$steadysets_icons = array(
	  array('steadysets-icon-type' => 'steadysets-icon-type'),
	  array('steadysets-icon-box' => 'steadysets-icon-box'),
	  array('steadysets-icon-archive' => 'steadysets-icon-archive'),
	  array('steadysets-icon-envelope' => 'steadysets-icon-envelope'),
	  array('steadysets-icon-email' => 'steadysets-icon-email'),
	  array('steadysets-icon-files' => 'steadysets-icon-files'),
	  array('steadysets-icon-uniE606' => 'steadysets-icon-uniE606'),
	  array('steadysets-icon-connection-empty' => 'steadysets-icon-connection-empty'),
	  array('steadysets-icon-connection-25' => 'steadysets-icon-connection-25'),
	  array('steadysets-icon-connection-50' => 'steadysets-icon-connection-50'),
	  array('steadysets-icon-connection-75' => 'steadysets-icon-connection-75'),
	  array('steadysets-icon-connection-full' => 'steadysets-icon-connection-full'),
	  array('steadysets-icon-microphone' => 'steadysets-icon-microphone'),
	  array('steadysets-icon-microphone-off' => 'steadysets-icon-microphone-off'),
	  array('steadysets-icon-book' => 'steadysets-icon-book'),
	  array('steadysets-icon-cloud' => 'steadysets-icon-cloud'),
	  array('steadysets-icon-book2' => 'steadysets-icon-book2'),
	  array('steadysets-icon-star' => 'steadysets-icon-star'),
	  array('steadysets-icon-phone-portrait' => 'steadysets-icon-phone-portrait'),
	  array('steadysets-icon-phone-landscape' => 'steadysets-icon-phone-landscape'),
	  array('steadysets-icon-tablet' => 'steadysets-icon-tablet'),
	  array('steadysets-icon-tablet-landscape' => 'steadysets-icon-tablet-landscape'),
	  array('steadysets-icon-laptop' => 'steadysets-icon-laptop'),
	  array('steadysets-icon-uniE617' => 'steadysets-icon-uniE617'),
	  array('steadysets-icon-barbell' => 'steadysets-icon-barbell'),
	  array('steadysets-icon-stopwatch' => 'steadysets-icon-stopwatch'),
	  array('steadysets-icon-atom' => 'steadysets-icon-atom'),
	  array('steadysets-icon-syringe' => 'steadysets-icon-syringe'),
	  array('steadysets-icon-pencil' => 'steadysets-icon-pencil'),
	  array('steadysets-icon-chart' => 'steadysets-icon-chart'),
	  array('steadysets-icon-bars' => 'steadysets-icon-bars'),
	  array('steadysets-icon-cube' => 'steadysets-icon-cube'),
	  array('steadysets-icon-image' => 'steadysets-icon-image'),
	  array('steadysets-icon-crop' => 'steadysets-icon-crop'),
	  array('steadysets-icon-graph' => 'steadysets-icon-graph'),
	  array('steadysets-icon-select' => 'steadysets-icon-select'),
	  array('steadysets-icon-bucket' => 'steadysets-icon-bucket'),
	  array('steadysets-icon-mug' => 'steadysets-icon-mug'),
	  array('steadysets-icon-clipboard' => 'steadysets-icon-clipboard'),
	  array('steadysets-icon-lab' => 'steadysets-icon-lab'),
	  array('steadysets-icon-bones' => 'steadysets-icon-bones'),
	  array('steadysets-icon-pill' => 'steadysets-icon-pill'),
	  array('steadysets-icon-bolt' => 'steadysets-icon-bolt'),
	  array('steadysets-icon-health' => 'steadysets-icon-health'),
	  array('steadysets-icon-map-marker' => 'steadysets-icon-map-marker'),
	  array('steadysets-icon-stack' => 'steadysets-icon-stack'),
	  array('steadysets-icon-newspaper' => 'steadysets-icon-newspaper'),
	  array('steadysets-icon-uniE62F' => 'steadysets-icon-uniE62F'),
	  array('steadysets-icon-coffee' => 'steadysets-icon-coffee'),
	  array('steadysets-icon-bill' => 'steadysets-icon-bill'),
	  array('steadysets-icon-sun' => 'steadysets-icon-sun'),
	  array('steadysets-icon-vcard' => 'steadysets-icon-vcard'),
	  array('steadysets-icon-shorts' => 'steadysets-icon-shorts'),
	  array('steadysets-icon-drink' => 'steadysets-icon-drink'),
	  array('steadysets-icon-diamond' => 'steadysets-icon-diamond'),
	  array('steadysets-icon-bag' => 'steadysets-icon-bag'),
	  array('steadysets-icon-calculator' => 'steadysets-icon-calculator'),
	  array('steadysets-icon-credit-cards' => 'steadysets-icon-credit-cards'),
	  array('steadysets-icon-microwave-oven' => 'steadysets-icon-microwave-oven'),
	  array('steadysets-icon-camera' => 'steadysets-icon-camera'),
	  array('steadysets-icon-share' => 'steadysets-icon-share'),
	  array('steadysets-icon-bullhorn' => 'steadysets-icon-bullhorn'),
	  array('steadysets-icon-user' => 'steadysets-icon-user'),
	  array('steadysets-icon-users' => 'steadysets-icon-users'),
	  array('steadysets-icon-user2' => 'steadysets-icon-user2'),
	  array('steadysets-icon-users2' => 'steadysets-icon-users2'),
	  array('steadysets-icon-unlocked' => 'steadysets-icon-unlocked'),
	  array('steadysets-icon-unlocked2' => 'steadysets-icon-unlocked2'),
	  array('steadysets-icon-lock' => 'steadysets-icon-lock'),
	  array('steadysets-icon-forbidden' => 'steadysets-icon-forbidden'),
	  array('steadysets-icon-switch' => 'steadysets-icon-switch'),
	  array('steadysets-icon-meter' => 'steadysets-icon-meter'),
	  array('steadysets-icon-flag' => 'steadysets-icon-flag'),
	  array('steadysets-icon-home' => 'steadysets-icon-home'),
	  array('steadysets-icon-printer' => 'steadysets-icon-printer'),
	  array('steadysets-icon-clock' => 'steadysets-icon-clock'),
	  array('steadysets-icon-calendar' => 'steadysets-icon-calendar'),
	  array('steadysets-icon-comment' => 'steadysets-icon-comment'),
	  array('steadysets-icon-chat-3' => 'steadysets-icon-chat-3'),
	  array('steadysets-icon-chat-2' => 'steadysets-icon-chat-2'),
	  array('steadysets-icon-chat-1' => 'steadysets-icon-chat-1'),
	  array('steadysets-icon-chat' => 'steadysets-icon-chat'),
	  array('steadysets-icon-zoom-out' => 'steadysets-icon-zoom-out'),
	  array('steadysets-icon-zoom-in' => 'steadysets-icon-zoom-in'),
	  array('steadysets-icon-search' => 'steadysets-icon-search'),
	  array('steadysets-icon-trashcan' => 'steadysets-icon-trashcan'),
	  array('steadysets-icon-tag' => 'steadysets-icon-tag'),
	  array('steadysets-icon-download' => 'steadysets-icon-download'),
	  array('steadysets-icon-paperclip' => 'steadysets-icon-paperclip'),
	  array('steadysets-icon-checkbox' => 'steadysets-icon-checkbox'),
	  array('steadysets-icon-checkbox-checked' => 'steadysets-icon-checkbox-checked'),
	  array('steadysets-icon-checkmark' => 'steadysets-icon-checkmark'),
	  array('steadysets-icon-refresh' => 'steadysets-icon-refresh'),
	  array('steadysets-icon-reload' => 'steadysets-icon-reload'),
	  array('steadysets-icon-arrow-right' => 'steadysets-icon-arrow-right'),
	  array('steadysets-icon-arrow-down' => 'steadysets-icon-arrow-down'),
	  array('steadysets-icon-arrow-up' => 'steadysets-icon-arrow-up'),
	  array('steadysets-icon-arrow-left' => 'steadysets-icon-arrow-left'),
	  array('steadysets-icon-settings' => 'steadysets-icon-settings'),
	  array('steadysets-icon-battery-full' => 'steadysets-icon-battery-full'),
	  array('steadysets-icon-battery-75' => 'steadysets-icon-battery-75'),
	  array('steadysets-icon-battery-50' => 'steadysets-icon-battery-50'),
	  array('steadysets-icon-battery-25' => 'steadysets-icon-battery-25'),
	  array('steadysets-icon-battery-empty' => 'steadysets-icon-battery-empty'),
	  array('steadysets-icon-battery-charging' => 'steadysets-icon-battery-charging'),
	  array('steadysets-icon-uniE669' => 'steadysets-icon-uniE669'),
	  array('steadysets-icon-grid' => 'steadysets-icon-grid'),
	  array('steadysets-icon-list' => 'steadysets-icon-list'),
	  array('steadysets-icon-wifi-low' => 'steadysets-icon-wifi-low'),
	  array('steadysets-icon-folder-check' => 'steadysets-icon-folder-check'),
	  array('steadysets-icon-folder-settings' => 'steadysets-icon-folder-settings'),
	  array('steadysets-icon-folder-add' => 'steadysets-icon-folder-add'),
	  array('steadysets-icon-folder' => 'steadysets-icon-folder'),
	  array('steadysets-icon-window' => 'steadysets-icon-window'),
	  array('steadysets-icon-windows' => 'steadysets-icon-windows'),
	  array('steadysets-icon-browser' => 'steadysets-icon-browser'),
	  array('steadysets-icon-file-broken' => 'steadysets-icon-file-broken'),
	  array('steadysets-icon-align-justify' => 'steadysets-icon-align-justify'),
	  array('steadysets-icon-align-center' => 'steadysets-icon-align-center'),
	  array('steadysets-icon-align-right' => 'steadysets-icon-align-right'),
	  array('steadysets-icon-align-left' => 'steadysets-icon-align-left'),
	  array('steadysets-icon-file' => 'steadysets-icon-file'),
	  array('steadysets-icon-file-add' => 'steadysets-icon-file-add'),
	  array('steadysets-icon-file-settings' => 'steadysets-icon-file-settings'),
	  array('steadysets-icon-mute' => 'steadysets-icon-mute'),
	  array('steadysets-icon-heart' => 'steadysets-icon-heart'),
	  array('steadysets-icon-enter' => 'steadysets-icon-enter'),
	  array('steadysets-icon-volume-decrease' => 'steadysets-icon-volume-decrease'),
	  array('steadysets-icon-wifi-mid' => 'steadysets-icon-wifi-mid'),
	  array('steadysets-icon-volume' => 'steadysets-icon-volume'),
	  array('steadysets-icon-bookmark' => 'steadysets-icon-bookmark'),
	  array('steadysets-icon-screen' => 'steadysets-icon-screen'),
	  array('steadysets-icon-map' => 'steadysets-icon-map'),
	  array('steadysets-icon-measure' => 'steadysets-icon-measure'),
	  array('steadysets-icon-eyedropper' => 'steadysets-icon-eyedropper'),
	  array('steadysets-icon-support' => 'steadysets-icon-support'),
	  array('steadysets-icon-phone' => 'steadysets-icon-phone'),
	  array('steadysets-icon-email2' => 'steadysets-icon-email2'),
	  array('steadysets-icon-volume-increase' => 'steadysets-icon-volume-increase'),
	  array('steadysets-icon-wifi-full' => 'steadysets-icon-wifi-full'),
	);

	return array_merge( $icons, $steadysets_icons );
}

add_filter( 'vc_iconpicker-type-linea', 'vc_iconpicker_type_linea' );

function vc_iconpicker_type_linea( $icons ) {
	$linea_icons = array(
	array( 'icon-arrows-anticlockwise' => 'icon-arrows-anticlockwise'),
	array('icon-arrows-anticlockwise-dashed' => 'icon-arrows-anticlockwise-dashed'),
	array('icon-arrows-button-down' => 'icon-arrows-button-down'),
	array('icon-arrows-button-off' => 'icon-arrows-button-off'),
	array('icon-arrows-button-on' => 'icon-arrows-button-on'),
	array('icon-arrows-button-up' => 'icon-arrows-button-up'),
	array('icon-arrows-check' => 'icon-arrows-check'),
	array('icon-arrows-circle-check' => 'icon-arrows-circle-check'),
	array('icon-arrows-circle-down' => 'icon-arrows-circle-down'),
	array('icon-arrows-circle-downleft' => 'icon-arrows-circle-downleft'),
	array('icon-arrows-circle-downright' => 'icon-arrows-circle-downright'),
	array('icon-arrows-circle-left' => 'icon-arrows-circle-left'),
	array('icon-arrows-circle-minus' => 'icon-arrows-circle-minus'),
	array('icon-arrows-circle-plus' => 'icon-arrows-circle-plus'),
	array('icon-arrows-circle-remove' => 'icon-arrows-circle-remove'),
	array('icon-arrows-circle-right' => 'icon-arrows-circle-right'),
	array('icon-arrows-circle-up' => 'icon-arrows-circle-up'),
	array('icon-arrows-circle-upleft' => 'icon-arrows-circle-upleft'),
	array('icon-arrows-circle-upright' => 'icon-arrows-circle-upright'),
	array('icon-arrows-clockwise' => 'icon-arrows-clockwise'),
	array('icon-arrows-clockwise-dashed' => 'icon-arrows-clockwise-dashed'),
	array('icon-arrows-compress' => 'icon-arrows-compress'),
	array('icon-arrows-deny' => 'icon-arrows-deny'),
	array('icon-arrows-diagonal' => 'icon-arrows-diagonal'),
	array('icon-arrows-diagonal2' => 'icon-arrows-diagonal2'),
	array('icon-arrows-down' => 'icon-arrows-down'),
	array('icon-arrows-downleft' => 'icon-arrows-down-double'),
	array('icon-arrows-downright' => 'icon-arrows-downleft'),
	array('icon-arrows-drag-down' => 'icon-arrows-drag-down'),
	array('icon-arrows-drag-down-dashed' => 'icon-arrows-drag-down-dashed'),
	array('icon-arrows-drag-horiz' => 'icon-arrows-drag-horiz'),
	array('icon-arrows-drag-left' => 'icon-arrows-drag-left'),
	array('icon-arrows-drag-left-dashed' => 'icon-arrows-drag-left-dashed'),
	array('icon-arrows-drag-right' => 'icon-arrows-drag-right'),
	array('icon-arrows-drag-right-dashed' => 'icon-arrows-drag-right-dashed'),
	array('icon-arrows-drag-up' => 'icon-arrows-drag-up'),
	array('icon-arrows-drag-up-dashed' => 'icon-arrows-drag-up-dashed'),
	array('icon-arrows-exclamation' => 'icon-arrows-exclamation'),
	array('icon-arrows-expand' => 'icon-arrows-expand'),
	array('icon-arrows-expand-diagonal1' => 'icon-arrows-expand-diagonal1'),
	array('icon-arrows-expand-horizontal1' => 'icon-arrows-expand-horizontal1'),
	array('icon-arrows-expand-vertical1' => 'icon-arrows-expand-vertical1'),
	array('icon-arrows-fit-horizontal' => 'icon-arrows-fit-horizontal'),
	array('icon-arrows-fit-vertical' => 'icon-arrows-fit-vertical'),
	array('icon-arrows-glide' => 'icon-arrows-glide'),
	array('icon-arrows-glide-horizontal' => 'icon-arrows-glide-horizontal'),
	array('icon-arrows-glide-vertical' => 'icon-arrows-glide-vertical'),
	array('icon-arrows-hamburger1' => 'icon-arrows-hamburger1'),
	array('icon-arrows-hamburger-2' => 'icon-arrows-hamburger-2'),
	array('icon-arrows-horizontal' => 'icon-arrows-horizontal'),
	array('icon-arrows-info' => 'icon-arrows-info'),
	array('icon-arrows-keyboard-alt' => 'icon-arrows-keyboard-alt'),
	array('icon-arrows-keyboard-cmd' => 'icon-arrows-keyboard-cmd'),
	array('icon-arrows-keyboard-delete' => 'icon-arrows-keyboard-delete'),
	array('icon-arrows-keyboard-down' => 'icon-arrows-keyboard-down'),
	array('icon-arrows-keyboard-left' => 'icon-arrows-keyboard-left'),
	array('icon-arrows-keyboard-return' => 'icon-arrows-keyboard-return'),
	array('icon-arrows-keyboard-right' => 'icon-arrows-keyboard-right'),
	array('icon-arrows-keyboard-shift' => 'icon-arrows-keyboard-shift'),
	array('icon-arrows-keyboard-tab' => 'icon-arrows-keyboard-tab'),
	array('icon-arrows-keyboard-up' => 'icon-arrows-keyboard-up'),
	array('icon-arrows-left' => 'icon-arrows-left'),
	array('icon-arrows-left-double-32' => 'icon-arrows-left-double-32'),
	array('icon-arrows-minus' => 'icon-arrows-minus'),
	array('icon-arrows-move' => 'icon-arrows-move'),
	array('icon-arrows-move2' => 'icon-arrows-move2'),
	array('icon-arrows-move-bottom' => 'icon-arrows-move-bottom'),
	array('icon-arrows-move-left' => 'icon-arrows-move-left'),
	array('icon-arrows-move-right' => 'icon-arrows-move-right'),
	array('icon-arrows-move-top' => 'icon-arrows-move-top'),
	array('icon-arrows-plus' => 'icon-arrows-plus'),
	array('icon-arrows-question' => 'icon-arrows-question'),
	array('icon-arrows-remove' => 'icon-arrows-remove'),
	array('icon-arrows-right' => 'icon-arrows-right'),
	array('icon-arrows-right-double' => 'icon-arrows-right-double'),
	array('icon-arrows-rotate' => 'icon-arrows-rotate'),
	array('icon-arrows-rotate-anti' => 'icon-arrows-rotate-anti'),
	array('icon-arrows-rotate-anti-dashed' => 'icon-arrows-rotate-anti-dashed'),
	array('icon-arrows-rotate-dashed' => 'icon-arrows-rotate-dashed'),
	array('icon-arrows-shrink' => 'icon-arrows-shrink'),
	array('icon-arrows-shrink-diagonal1' => 'icon-arrows-shrink-diagonal1'),
	array('icon-arrows-shrink-diagonal2' => 'icon-arrows-shrink-diagonal2'),
	array('icon-arrows-shrink-horizonal2' => 'icon-arrows-shrink-horizonal2'),
	array('icon-arrows-shrink-horizontal1' => 'icon-arrows-shrink-horizontal1'),
	array('icon-arrows-shrink-vertical1' => 'icon-arrows-shrink-vertical1'),
	array('icon-arrows-shrink-vertical2' => 'icon-arrows-shrink-vertical2'),
	array('icon-arrows-sign-down' => 'icon-arrows-sign-down'),
	array('icon-arrows-sign-left' => 'icon-arrows-sign-left'),
	array('icon-arrows-sign-right' => 'icon-arrows-sign-right'),
	array('icon-arrows-sign-up' => 'icon-arrows-sign-up'),
	array('icon-arrows-slide-down1' => 'icon-arrows-slide-down1'),
	array('icon-arrows-slide-down2' => 'icon-arrows-slide-down2'),
	array('icon-arrows-slide-left1' => 'icon-arrows-slide-left1'),
	array('icon-arrows-slide-left2' => 'icon-arrows-slide-left2'),
	array('icon-arrows-slide-right1' => 'icon-arrows-slide-right1'),
	array('icon-arrows-slide-right2' => 'icon-arrows-slide-right2'),
	array('icon-arrows-slide-up1' => 'icon-arrows-slide-up1'),
	array('icon-arrows-slide-up2' => 'icon-arrows-slide-up2'),
	array('icon-arrows-slim-down' => 'icon-arrows-slim-down'),
	array('icon-arrows-slim-down-dashed' => 'icon-arrows-slim-down-dashed'),
	array('icon-arrows-slim-left' => 'icon-arrows-slim-left'),
	array('icon-arrows-slim-left-dashed' => 'icon-arrows-slim-left-dashed'),
	array('icon-arrows-slim-right' => 'icon-arrows-slim-right'),
	array('icon-arrows-slim-right-dashed' => 'icon-arrows-slim-right-dashed'),
	array('icon-arrows-slim-up' => 'icon-arrows-slim-up'),
	array('icon-arrows-slim-up-dashed' => 'icon-arrows-slim-up-dashed'),
	array('icon-arrows-squares' => 'icon-arrows-squares'),
	array('icon-arrows-square-check' => 'icon-arrows-square-check'),
	array('icon-arrows-square-down' => 'icon-arrows-square-down'),
	array('icon-arrows-square-downleft' => 'icon-arrows-square-downleft'),
	array('icon-arrows-square-downright' => 'icon-arrows-square-downright'),
	array('icon-arrows-square-left' => 'icon-arrows-square-left'),
	array('icon-arrows-square-minus' => 'icon-arrows-square-minus'),
	array('icon-arrows-square-plus' => 'icon-arrows-square-plus'),
	array('icon-arrows-square-remove' => 'icon-arrows-square-remove'),
	array('icon-arrows-square-right' => 'icon-arrows-square-right'),
	array('icon-arrows-square-up' => 'icon-arrows-square-up'),
	array('icon-arrows-square-upleft' => 'icon-arrows-square-upleft'),
	array('icon-arrows-square-upright' => 'icon-arrows-square-upright'),
	array('icon-arrows-stretch-diagonal1' => 'icon-arrows-stretch-diagonal1'),
	array('icon-arrows-stretch-diagonal2' => 'icon-arrows-stretch-diagonal2'),
	array('icon-arrows-stretch-diagonal3' => 'icon-arrows-stretch-diagonal3'),
	array('icon-arrows-stretch-diagonal4' => 'icon-arrows-stretch-diagonal4'),
	array('icon-arrows-stretch-horizontal1' => 'icon-arrows-stretch-horizontal1'),
	array('icon-arrows-stretch-horizontal2' => 'icon-arrows-stretch-horizontal2'),
	array('icon-arrows-stretch-vertical1' => 'icon-arrows-stretch-vertical1'),
	array('icon-arrows-stretch-vertical2' => 'icon-arrows-stretch-vertical2'),
	array('icon-arrows-switch-horizontal' => 'icon-arrows-switch-horizontal'),
	array('icon-arrows-switch-vertical' => 'icon-arrows-switch-vertical'),
	array('icon-arrows-up' => 'icon-arrows-up'),
	array('icon-arrows-upright' => 'icon-arrows-upright'),
	array('icon-arrows-vertical' => 'icon-arrows-vertical'),
	array('icon-basic-accelerator' => 'icon-basic-accelerator'),
	array('icon-basic-alarm' => 'icon-basic-alarm'),
	array('icon-basic-anchor' => 'icon-basic-anchor'),
	array('icon-basic-anticlockwise' => 'icon-basic-anticlockwise'),
	array('icon-basic-archive' => 'icon-basic-archive'),
	array('icon-basic-archive-full' => 'icon-basic-archive-full'),
	array('icon-basic-ban' => 'icon-basic-ban'),
	array('icon-basic-battery-charge' => 'icon-basic-battery-charge'),
	array('icon-basic-battery-empty' => 'icon-basic-battery-empty'),
	array('icon-basic-battery-full' => 'icon-basic-battery-full'),
	array('icon-basic-battery-half' => 'icon-basic-battery-half'),
	array('icon-basic-bolt' => 'icon-basic-bolt'),
	array('icon-basic-book' => 'icon-basic-book'),
	array('icon-basic-bookmark' => 'icon-basic-book-pen'),
	array('icon-basic-book-pen' => 'icon-basic-book-pencil'),
	array('icon-basic-book-pencil' => 'icon-basic-bookmark'),
	array('icon-basic-calculator' => 'icon-basic-calculator'),
	array('icon-basic-calendar' => 'icon-basic-calendar'),
	array('icon-basic-cards-diamonds' => 'icon-basic-cards-diamonds'),
	array('icon-basic-cards-hearts' => 'icon-basic-cards-hearts'),
	array('icon-basic-case' => 'icon-basic-case'),
	array('icon-basic-chronometer' => 'icon-basic-chronometer'),
	array('icon-basic-clessidre' => 'icon-basic-clessidre'),
	array('icon-basic-clock' => 'icon-basic-clock'),
	array('icon-basic-clockwise' => 'icon-basic-clockwise'),
	array('icon-basic-cloud' => 'icon-basic-cloud'),
	array('icon-basic-clubs' => 'icon-basic-clubs'),
	array('icon-basic-compass' => 'icon-basic-compass'),
	array('icon-basic-cup' => 'icon-basic-cup'),
	array('icon-basic-diamonds' => 'icon-basic-diamonds'),
	array('icon-basic-display' => 'icon-basic-display'),
	array('icon-basic-download' => 'icon-basic-download'),
	array('icon-basic-elaboration-bookmark-checck' => 'icon-basic-elaboration-bookmark-checck'),
	array('icon-basic-elaboration-bookmark-minus' => 'icon-basic-elaboration-bookmark-minus'),
	array('icon-basic-elaboration-bookmark-plus' => 'icon-basic-elaboration-bookmark-plus'),
	array('icon-basic-elaboration-bookmark-remove' => 'icon-basic-elaboration-bookmark-remove'),
	array('icon-basic-elaboration-briefcase-check' => 'icon-basic-elaboration-briefcase-check'),
	array('icon-basic-elaboration-briefcase-download' => 'icon-basic-elaboration-briefcase-download'),
	array('icon-basic-elaboration-briefcase-flagged' => 'icon-basic-elaboration-briefcase-flagged'),
	array('icon-basic-elaboration-briefcase-minus' => 'icon-basic-elaboration-briefcase-minus'),
	array('icon-basic-elaboration-briefcase-plus' => 'icon-basic-elaboration-briefcase-plus'),
	array('icon-basic-elaboration-briefcase-refresh' => 'icon-basic-elaboration-briefcase-refresh'),
	array('icon-basic-elaboration-briefcase-remove' => 'icon-basic-elaboration-briefcase-remove'),
	array('icon-basic-elaboration-briefcase-search' => 'icon-basic-elaboration-briefcase-search'),
	array('icon-basic-elaboration-briefcase-star' => 'icon-basic-elaboration-briefcase-star'),
	array('icon-basic-elaboration-briefcase-upload' => 'icon-basic-elaboration-briefcase-upload'),
	array('icon-basic-elaboration-browser-check' => 'icon-basic-elaboration-browser-check'),
	array('icon-basic-elaboration-browser-download' => 'icon-basic-elaboration-browser-download'),
	array('icon-basic-elaboration-browser-minus' => 'icon-basic-elaboration-browser-minus'),
	array('icon-basic-elaboration-browser-plus' => 'icon-basic-elaboration-browser-plus'),
	array('icon-basic-elaboration-browser-refresh' => 'icon-basic-elaboration-browser-refresh'),
	array('icon-basic-elaboration-browser-remove' => 'icon-basic-elaboration-browser-remove'),
	array('icon-basic-elaboration-browser-search' => 'icon-basic-elaboration-browser-search'),
	array('icon-basic-elaboration-browser-star' => 'icon-basic-elaboration-browser-star'),
	array('icon-basic-elaboration-browser-upload' => 'icon-basic-elaboration-browser-upload'),
	array('icon-basic-elaboration-calendar-check' => 'icon-basic-elaboration-calendar-check'),
	array('icon-basic-elaboration-calendar-cloud' => 'icon-basic-elaboration-calendar-cloud'),
	array('icon-basic-elaboration-calendar-download' => 'icon-basic-elaboration-calendar-download'),
	array('icon-basic-elaboration-calendar-empty' => 'icon-basic-elaboration-calendar-empty'),
	array('icon-basic-elaboration-calendar-flagged' => 'icon-basic-elaboration-calendar-flagged'),
	array('icon-basic-elaboration-calendar-heart' => 'icon-basic-elaboration-calendar-heart'),
	array('icon-basic-elaboration-calendar-minus' => 'icon-basic-elaboration-calendar-minus'),
	array('icon-basic-elaboration-calendar-next' => 'icon-basic-elaboration-calendar-next'),
	array('icon-basic-elaboration-calendar-noaccess' => 'icon-basic-elaboration-calendar-noaccess'),
	array('icon-basic-elaboration-calendar-pencil' => 'icon-basic-elaboration-calendar-pencil'),
	array('icon-basic-elaboration-calendar-plus' => 'icon-basic-elaboration-calendar-plus'),
	array('icon-basic-elaboration-calendar-previous' => 'icon-basic-elaboration-calendar-previous'),
	array('icon-basic-elaboration-calendar-refresh' => 'icon-basic-elaboration-calendar-refresh'),
	array('icon-basic-elaboration-calendar-remove' => 'icon-basic-elaboration-calendar-remove'),
	array('icon-basic-elaboration-calendar-search' => 'icon-basic-elaboration-calendar-search'),
	array('icon-basic-elaboration-calendar-star' => 'icon-basic-elaboration-calendar-star'),
	array('icon-basic-elaboration-calendar-upload' => 'icon-basic-elaboration-calendar-upload'),
	array('icon-basic-elaboration-cloud-check' => 'icon-basic-elaboration-cloud-check'),
	array('icon-basic-elaboration-cloud-download' => 'icon-basic-elaboration-cloud-download'),
	array('icon-basic-elaboration-cloud-minus' => 'icon-basic-elaboration-cloud-minus'),
	array('icon-basic-elaboration-cloud-noaccess' => 'icon-basic-elaboration-cloud-noaccess'),
	array('icon-basic-elaboration-cloud-plus' => 'icon-basic-elaboration-cloud-plus'),
	array('icon-basic-elaboration-cloud-refresh' => 'icon-basic-elaboration-cloud-refresh'),
	array('icon-basic-elaboration-cloud-remove' => 'icon-basic-elaboration-cloud-remove'),
	array('icon-basic-elaboration-cloud-search' => 'icon-basic-elaboration-cloud-search'),
	array('icon-basic-elaboration-cloud-upload' => 'icon-basic-elaboration-cloud-upload'),
	array('icon-basic-elaboration-document-check' => 'icon-basic-elaboration-document-check'),
	array('icon-basic-elaboration-document-cloud' => 'icon-basic-elaboration-document-cloud'),
	array('icon-basic-elaboration-document-download' => 'icon-basic-elaboration-document-download'),
	array('icon-basic-elaboration-document-flagged' => 'icon-basic-elaboration-document-flagged'),
	array('icon-basic-elaboration-document-graph' => 'icon-basic-elaboration-document-graph'),
	array('icon-basic-elaboration-document-heart' => 'icon-basic-elaboration-document-heart'),
	array('icon-basic-elaboration-document-minus' => 'icon-basic-elaboration-document-minus'),
	array('icon-basic-elaboration-document-next' => 'icon-basic-elaboration-document-next'),
	array('icon-basic-elaboration-document-noaccess' => 'icon-basic-elaboration-document-noaccess'),
	array('icon-basic-elaboration-document-note' => 'icon-basic-elaboration-document-note'),
	array('icon-basic-elaboration-document-pencil' => 'icon-basic-elaboration-document-pencil'),
	array('icon-basic-elaboration-document-picture' => 'icon-basic-elaboration-document-picture'),
	array('icon-basic-elaboration-document-plus' => 'icon-basic-elaboration-document-plus'),
	array('icon-basic-elaboration-document-previous' => 'icon-basic-elaboration-document-previous'),
	array('icon-basic-elaboration-document-refresh' => 'icon-basic-elaboration-document-refresh'),
	array('icon-basic-elaboration-document-remove' => 'icon-basic-elaboration-document-remove'),
	array('icon-basic-elaboration-document-search' => 'icon-basic-elaboration-document-search'),
	array('icon-basic-elaboration-document-star' => 'icon-basic-elaboration-document-star'),
	array('icon-basic-elaboration-document-upload' => 'icon-basic-elaboration-document-upload'),
	array('icon-basic-elaboration-folder-check' => 'icon-basic-elaboration-folder-check'),
	array('icon-basic-elaboration-folder-cloud' => 'icon-basic-elaboration-folder-cloud'),
	array('icon-basic-elaboration-folder-document' => 'icon-basic-elaboration-folder-document'),
	array('icon-basic-elaboration-folder-download' => 'icon-basic-elaboration-folder-download'),
	array('icon-basic-elaboration-folder-flagged' => 'icon-basic-elaboration-folder-flagged'),
	array('icon-basic-elaboration-folder-graph' => 'icon-basic-elaboration-folder-graph'),
	array('icon-basic-elaboration-folder-heart' => 'icon-basic-elaboration-folder-heart'),
	array('icon-basic-elaboration-folder-minus' => 'icon-basic-elaboration-folder-minus'),
	array('icon-basic-elaboration-folder-next' => 'icon-basic-elaboration-folder-next'),
	array('icon-basic-elaboration-folder-noaccess' => 'icon-basic-elaboration-folder-noaccess'),
	array('icon-basic-elaboration-folder-note' => 'icon-basic-elaboration-folder-note'),
	array('icon-basic-elaboration-folder-pencil' => 'icon-basic-elaboration-folder-pencil'),
	array('icon-basic-elaboration-folder-picture' => 'icon-basic-elaboration-folder-picture'),
	array('icon-basic-elaboration-folder-plus' => 'icon-basic-elaboration-folder-plus'),
	array('icon-basic-elaboration-folder-previous' => 'icon-basic-elaboration-folder-previous'),
	array('icon-basic-elaboration-folder-refresh' => 'icon-basic-elaboration-folder-refresh'),
	array('icon-basic-elaboration-folder-remove' => 'icon-basic-elaboration-folder-remove'),
	array('icon-basic-elaboration-folder-search' => 'icon-basic-elaboration-folder-search'),
	array('icon-basic-elaboration-folder-star' => 'icon-basic-elaboration-folder-star'),
	array('icon-basic-elaboration-folder-upload' => 'icon-basic-elaboration-folder-upload'),
	array('icon-basic-elaboration-mail-check' => 'icon-basic-elaboration-mail-check'),
	array('icon-basic-elaboration-mail-cloud' => 'icon-basic-elaboration-mail-cloud'),
	array('icon-basic-elaboration-mail-document' => 'icon-basic-elaboration-mail-document'),
	array('icon-basic-elaboration-mail-download' => 'icon-basic-elaboration-mail-download'),
	array('icon-basic-elaboration-mail-flagged' => 'icon-basic-elaboration-mail-flagged'),
	array('icon-basic-elaboration-mail-heart' => 'icon-basic-elaboration-mail-heart'),
	array('icon-basic-elaboration-mail-next' => 'icon-basic-elaboration-mail-next'),
	array('icon-basic-elaboration-mail-noaccess' => 'icon-basic-elaboration-mail-noaccess'),
	array('icon-basic-elaboration-mail-note' => 'icon-basic-elaboration-mail-note'),
	array('icon-basic-elaboration-mail-pencil' => 'icon-basic-elaboration-mail-pencil'),
	array('icon-basic-elaboration-mail-picture' => 'icon-basic-elaboration-mail-picture'),
	array('icon-basic-elaboration-mail-previous' => 'icon-basic-elaboration-mail-previous'),
	array('icon-basic-elaboration-mail-refresh' => 'icon-basic-elaboration-mail-refresh'),
	array('icon-basic-elaboration-mail-remove' => 'icon-basic-elaboration-mail-remove'),
	array('icon-basic-elaboration-mail-search' => 'icon-basic-elaboration-mail-search'),
	array('icon-basic-elaboration-mail-star' => 'icon-basic-elaboration-mail-star'),
	array('icon-basic-elaboration-mail-upload' => 'icon-basic-elaboration-mail-upload'),
	array('icon-basic-elaboration-message-check' => 'icon-basic-elaboration-message-check'),
	array('icon-basic-elaboration-message-dots' => 'icon-basic-elaboration-message-dots'),
	array('icon-basic-elaboration-message-happy' => 'icon-basic-elaboration-message-happy'),
	array('icon-basic-elaboration-message-heart' => 'icon-basic-elaboration-message-heart'),
	array('icon-basic-elaboration-message-minus' => 'icon-basic-elaboration-message-minus'),
	array('icon-basic-elaboration-message-note' => 'icon-basic-elaboration-message-note'),
	array('icon-basic-elaboration-message-plus' => 'icon-basic-elaboration-message-plus'),
	array('icon-basic-elaboration-message-refresh' => 'icon-basic-elaboration-message-refresh'),
	array('icon-basic-elaboration-message-remove' => 'icon-basic-elaboration-message-remove'),
	array('icon-basic-elaboration-message-sad' => 'icon-basic-elaboration-message-sad'),
	array('icon-basic-elaboration-smartphone-cloud' => 'icon-basic-elaboration-smartphone-cloud'),
	array('icon-basic-elaboration-smartphone-heart' => 'icon-basic-elaboration-smartphone-heart'),
	array('icon-basic-elaboration-smartphone-noaccess' => 'icon-basic-elaboration-smartphone-noaccess'),
	array('icon-basic-elaboration-smartphone-note' => 'icon-basic-elaboration-smartphone-note'),
	array('icon-basic-elaboration-smartphone-pencil' => 'icon-basic-elaboration-smartphone-pencil'),
	array('icon-basic-elaboration-smartphone-picture' => 'icon-basic-elaboration-smartphone-picture'),
	array('icon-basic-elaboration-smartphone-refresh' => 'icon-basic-elaboration-smartphone-refresh'),
	array('icon-basic-elaboration-smartphone-search' => 'icon-basic-elaboration-smartphone-search'),
	array('icon-basic-elaboration-tablet-cloud' => 'icon-basic-elaboration-tablet-cloud'),
	array('icon-basic-elaboration-tablet-heart' => 'icon-basic-elaboration-tablet-heart'),
	array('icon-basic-elaboration-tablet-noaccess' => 'icon-basic-elaboration-tablet-noaccess'),
	array('icon-basic-elaboration-tablet-note' => 'icon-basic-elaboration-tablet-note'),
	array('icon-basic-elaboration-tablet-pencil' => 'icon-basic-elaboration-tablet-pencil'),
	array('icon-basic-elaboration-tablet-picture' => 'icon-basic-elaboration-tablet-picture'),
	array('icon-basic-elaboration-tablet-refresh' => 'icon-basic-elaboration-tablet-refresh'),
	array('icon-basic-elaboration-tablet-search' => 'icon-basic-elaboration-tablet-search'),
	array('icon-basic-elaboration-todolist-2' => 'icon-basic-elaboration-todolist-2'),
	array('icon-basic-elaboration-todolist-check' => 'icon-basic-elaboration-todolist-check'),
	array('icon-basic-elaboration-todolist-cloud' => 'icon-basic-elaboration-todolist-cloud'),
	array('icon-basic-elaboration-todolist-download' => 'icon-basic-elaboration-todolist-download'),
	array('icon-basic-elaboration-todolist-flagged' => 'icon-basic-elaboration-todolist-flagged'),
	array('icon-basic-elaboration-todolist-minus' => 'icon-basic-elaboration-todolist-minus'),
	array('icon-basic-elaboration-todolist-noaccess' => 'icon-basic-elaboration-todolist-noaccess'),
	array('icon-basic-elaboration-todolist-pencil' => 'icon-basic-elaboration-todolist-pencil'),
	array('icon-basic-elaboration-todolist-plus' => 'icon-basic-elaboration-todolist-plus'),
	array('icon-basic-elaboration-todolist-refresh' => 'icon-basic-elaboration-todolist-refresh'),
	array('icon-basic-elaboration-todolist-remove' => 'icon-basic-elaboration-todolist-remove'),
	array('icon-basic-elaboration-todolist-search' => 'icon-basic-elaboration-todolist-search'),
	array('icon-basic-elaboration-todolist-star' => 'icon-basic-elaboration-todolist-star'),
	array('icon-basic-elaboration-todolist-upload' => 'icon-basic-elaboration-todolist-upload'),
	array('icon-basic-exclamation' => 'icon-basic-exclamation'),
	array('icon-basic-eye' => 'icon-basic-eye'),
	array('icon-basic-eye-closed' => 'icon-basic-eye-closed'),
	array('icon-basic-female' => 'icon-basic-female'),
	array('icon-basic-flag1' => 'icon-basic-flag1'),
	array('icon-basic-flag2' => 'icon-basic-flag2'),
	array('icon-basic-floppydisk' => 'icon-basic-floppydisk'),
	array('icon-basic-folder' => 'icon-basic-folder'),
	array('icon-basic-folder-multiple' => 'icon-basic-folder-multiple'),
	array('icon-basic-gear' => 'icon-basic-gear'),
	array('icon-basic-geolocalize-01' => 'icon-basic-geolocalize-01'),
	array('icon-basic-geolocalize-05' => 'icon-basic-geolocalize-05'),
	array('icon-basic-globe' => 'icon-basic-globe'),
	array('icon-basic-gunsight' => 'icon-basic-gunsight'),
	array('icon-basic-hammer' => 'icon-basic-hammer'),
	array('icon-basic-headset' => 'icon-basic-headset'),
	array('icon-basic-heart' => 'icon-basic-heart'),
	array('icon-basic-heart-broken' => 'icon-basic-heart-broken'),
	array('icon-basic-helm' => 'icon-basic-helm'),
	array('icon-basic-home' => 'icon-basic-home'),
	array('icon-basic-info' => 'icon-basic-info'),
	array('icon-basic-ipod' => 'icon-basic-ipod'),
	array('icon-basic-joypad' => 'icon-basic-joypad'),
	array('icon-basic-key' => 'icon-basic-key'),
	array('icon-basic-keyboard' => 'icon-basic-keyboard'),
	array('icon-basic-laptop' => 'icon-basic-laptop'),
	array('icon-basic-life-buoy' => 'icon-basic-life-buoy'),
	array('icon-basic-lightbulb' => 'icon-basic-lightbulb'),
	array('icon-basic-link' => 'icon-basic-link'),
	array('icon-basic-lock' => 'icon-basic-lock'),
	array('icon-basic-lock-open' => 'icon-basic-lock-open'),
	array('icon-basic-magic-mouse' => 'icon-basic-magic-mouse'),
	array('icon-basic-magnifier' => 'icon-basic-magnifier'),
	array('icon-basic-magnifier-minus' => 'icon-basic-magnifier-minus'),
	array('icon-basic-magnifier-plus' => 'icon-basic-magnifier-plus'),
	array('icon-basic-mail' => 'icon-basic-mail'),
	array('icon-basic-mail-multiple' => 'icon-basic-mail-multiple'),
	array('icon-basic-mail-open' => 'icon-basic-mail-open'),
	array('icon-basic-mail-open-text' => 'icon-basic-mail-open-text'),
	array('icon-basic-male' => 'icon-basic-male'),
	array('icon-basic-map' => 'icon-basic-map'),
	array('icon-basic-message' => 'icon-basic-message'),
	array('icon-basic-message-multiple' => 'icon-basic-message-multiple'),
	array('icon-basic-message-txt' => 'icon-basic-message-txt'),
	array('icon-basic-mixer2' => 'icon-basic-mixer2'),
	array('icon-basic-mouse' => 'icon-basic-mouse'),
	array('icon-basic-notebook' => 'icon-basic-notebook'),
	array('icon-basic-notebook-pen' => 'icon-basic-notebook-pen'),
	array('icon-basic-notebook-pencil' => 'icon-basic-notebook-pencil'),
	array('icon-basic-paperplane' => 'icon-basic-paperplane'),
	array('icon-basic-pencil-ruler' => 'icon-basic-pencil-ruler'),
	array('icon-basic-pencil-ruler-pen ' => 'icon-basic-pencil-ruler-pen'),
	array('icon-basic-photo' => 'icon-basic-photo'),
	array('icon-basic-picture' => 'icon-basic-picture'),
	array('icon-basic-picture-multiple' => 'icon-basic-picture-multiple'),
	array('icon-basic-pin1' => 'icon-basic-pin1'),
	array('icon-basic-pin2' => 'icon-basic-pin2'),
	array('icon-basic-postcard' => 'icon-basic-postcard'),
	array('icon-basic-postcard-multiple' => 'icon-basic-postcard-multiple'),
	array('icon-basic-printer' => 'icon-basic-printer'),
	array('icon-basic-question' => 'icon-basic-question'),
	array('icon-basic-rss' => 'icon-basic-rss'),
	array('icon-basic-server' => 'icon-basic-server'),
	array('icon-basic-server2' => 'icon-basic-server2'),
	array('icon-basic-server-cloud' => 'icon-basic-server-cloud'),
	array('icon-basic-server-download' => 'icon-basic-server-download'),
	array('icon-basic-server-upload' => 'icon-basic-server-upload'),
	array('icon-basic-settings' => 'icon-basic-settings'),
	array('icon-basic-share' => 'icon-basic-share'),
	array('icon-basic-sheet' => 'icon-basic-sheet'),
	array('icon-basic-sheet-multiple ' => 'icon-basic-sheet-multiple'),
	array('icon-basic-sheet-pen' => 'icon-basic-sheet-pen'),
	array('icon-basic-sheet-pencil' => 'icon-basic-sheet-pencil'),
	array('icon-basic-sheet-txt ' => 'icon-basic-sheet-txt'),
	array('icon-basic-signs' => 'icon-basic-signs'),
	array('icon-basic-smartphone' => 'icon-basic-smartphone'),
	array('icon-basic-spades' => 'icon-basic-spades'),
	array('icon-basic-spread' => 'icon-basic-spread'),
	array('icon-basic-spread-bookmark' => 'icon-basic-spread-bookmark'),
	array('icon-basic-spread-text' => 'icon-basic-spread-text'),
	array('icon-basic-spread-text-bookmark' => 'icon-basic-spread-text-bookmark'),
	array('icon-basic-star' => 'icon-basic-star'),
	array('icon-basic-tablet' => 'icon-basic-tablet'),
	array('icon-basic-target' => 'icon-basic-target'),
	array('icon-basic-todo' => 'icon-basic-todo'),
	array('icon-basic-todolist-pen' => 'icon-basic-todo-pen'),
	array('icon-basic-todolist-pencil' => 'icon-basic-todo-pencil'),
	array('icon-basic-todo-pen ' => 'icon-basic-todo-txt'),
	array('icon-basic-todo-pencil' => 'icon-basic-todolist-pen'),
	array('icon-basic-todo-txt' => 'icon-basic-todolist-pencil'),
	array('icon-basic-trashcan' => 'icon-basic-trashcan'),
	array('icon-basic-trashcan-full' => 'icon-basic-trashcan-full'),
	array('icon-basic-trashcan-refresh' => 'icon-basic-trashcan-refresh'),
	array('icon-basic-trashcan-remove' => 'icon-basic-trashcan-remove'),
	array('icon-basic-upload' => 'icon-basic-upload'),
	array('icon-basic-usb' => 'icon-basic-usb'),
	array('icon-basic-video' => 'icon-basic-video'),
	array('icon-basic-watch' => 'icon-basic-watch'),
	array('icon-basic-webpage' => 'icon-basic-webpage'),
	array('icon-basic-webpage-img-txt' => 'icon-basic-webpage-img-txt'),
	array('icon-basic-webpage-multiple' => 'icon-basic-webpage-multiple'),
	array('icon-basic-webpage-txt' => 'icon-basic-webpage-txt'),
	array('icon-basic-world' => 'icon-basic-world'),
	array('icon-ecommerce-bag' => 'icon-ecommerce-bag'),
	array('icon-ecommerce-bag-check' => 'icon-ecommerce-bag-check'),
	array('icon-ecommerce-bag-cloud' => 'icon-ecommerce-bag-cloud'),
	array('icon-ecommerce-bag-download' => 'icon-ecommerce-bag-download'),
	array('icon-ecommerce-bag-minus' => 'icon-ecommerce-bag-minus'),
	array('icon-ecommerce-bag-plus' => 'icon-ecommerce-bag-plus'),
	array('icon-ecommerce-bag-refresh' => 'icon-ecommerce-bag-refresh'),
	array('icon-ecommerce-bag-remove' => 'icon-ecommerce-bag-remove'),
	array('icon-ecommerce-bag-search' => 'icon-ecommerce-bag-search'),
	array('icon-ecommerce-bag-upload' => 'icon-ecommerce-bag-upload'),
	array('icon-ecommerce-banknote' => 'icon-ecommerce-banknote'),
	array('icon-ecommerce-banknotes' => 'icon-ecommerce-banknotes'),
	array('icon-ecommerce-basket' => 'icon-ecommerce-basket'),
	array('icon-ecommerce-basket-check' => 'icon-ecommerce-basket-check'),
	array('icon-ecommerce-basket-cloud' => 'icon-ecommerce-basket-cloud'),
	array('icon-ecommerce-basket-download' => 'icon-ecommerce-basket-download'),
	array('icon-ecommerce-basket-minus' => 'icon-ecommerce-basket-minus'),
	array('icon-ecommerce-basket-plus' => 'icon-ecommerce-basket-plus'),
	array('icon-ecommerce-basket-refresh' => 'icon-ecommerce-basket-refresh'),
	array('icon-ecommerce-basket-remove' => 'icon-ecommerce-basket-remove'),
	array('icon-ecommerce-basket-search' => 'icon-ecommerce-basket-search'),
	array('icon-ecommerce-basket-upload' => 'icon-ecommerce-basket-upload'),
	array('icon-ecommerce-bath' => 'icon-ecommerce-bath'),
	array('icon-ecommerce-cart' => 'icon-ecommerce-cart'),
	array('icon-ecommerce-cart-check' => 'icon-ecommerce-cart-check'),
	array('icon-ecommerce-cart-cloud' => 'icon-ecommerce-cart-cloud'),
	array('icon-ecommerce-cart-content' => 'icon-ecommerce-cart-content'),
	array('icon-ecommerce-cart-download' => 'icon-ecommerce-cart-download'),
	array('icon-ecommerce-cart-minus' => 'icon-ecommerce-cart-minus'),
	array('icon-ecommerce-cart-plus' => 'icon-ecommerce-cart-plus'),
	array('icon-ecommerce-cart-refresh' => 'icon-ecommerce-cart-refresh'),
	array('icon-ecommerce-cart-remove' => 'icon-ecommerce-cart-remove'),
	array('icon-ecommerce-cart-search' => 'icon-ecommerce-cart-search'),
	array('icon-ecommerce-cart-upload' => 'icon-ecommerce-cart-upload'),
	array('icon-ecommerce-cent' => 'icon-ecommerce-cent'),
	array('icon-ecommerce-colon' => 'icon-ecommerce-colon'),
	array('icon-ecommerce-creditcard' => 'icon-ecommerce-creditcard'),
	array('icon-ecommerce-diamond' => 'icon-ecommerce-diamond'),
	array('icon-ecommerce-dollar' => 'icon-ecommerce-dollar'),
	array('icon-ecommerce-euro' => 'icon-ecommerce-euro'),
	array('icon-ecommerce-franc' => 'icon-ecommerce-franc'),
	array('icon-ecommerce-gift' => 'icon-ecommerce-gift'),
	array('icon-ecommerce-graph1' => 'icon-ecommerce-graph1'),
	array('icon-ecommerce-graph2' => 'icon-ecommerce-graph2'),
	array('icon-ecommerce-graph3' => 'icon-ecommerce-graph3'),
	array('icon-ecommerce-graph-decrease' => 'icon-ecommerce-graph-decrease'),
	array('icon-ecommerce-graph-increase' => 'icon-ecommerce-graph-increase'),
	array('icon-ecommerce-guarani' => 'icon-ecommerce-guarani'),
	array('icon-ecommerce-kips' => 'icon-ecommerce-kips'),
	array('icon-ecommerce-lira' => 'icon-ecommerce-lira'),
	array('icon-ecommerce-megaphone' => 'icon-ecommerce-megaphone'),
	array('icon-ecommerce-money' => 'icon-ecommerce-money'),
	array('icon-ecommerce-naira' => 'icon-ecommerce-naira'),
	array('icon-ecommerce-pesos' => 'icon-ecommerce-pesos'),
	array('icon-ecommerce-pound' => 'icon-ecommerce-pound'),
	array('icon-ecommerce-receipt' => 'icon-ecommerce-receipt'),
	array('icon-ecommerce-receipt-bath' => 'icon-ecommerce-receipt-bath'),
	array('icon-ecommerce-receipt-cent' => 'icon-ecommerce-receipt-cent'),
	array('icon-ecommerce-receipt-dollar' => 'icon-ecommerce-receipt-dollar'),
	array('icon-ecommerce-receipt-euro' => 'icon-ecommerce-receipt-euro'),
	array('icon-ecommerce-receipt-franc' => 'icon-ecommerce-receipt-franc'),
	array('icon-ecommerce-receipt-guarani' => 'icon-ecommerce-receipt-guarani'),
	array('icon-ecommerce-receipt-kips' => 'icon-ecommerce-receipt-kips'),
	array('icon-ecommerce-receipt-lira' => 'icon-ecommerce-receipt-lira'),
	array('icon-ecommerce-receipt-naira' => 'icon-ecommerce-receipt-naira'),
	array('icon-ecommerce-receipt-pesos' => 'icon-ecommerce-receipt-pesos'),
	array('icon-ecommerce-receipt-pound' => 'icon-ecommerce-receipt-pound'),
	array('icon-ecommerce-receipt-rublo' => 'icon-ecommerce-receipt-rublo'),
	array('icon-ecommerce-receipt-rupee' => 'icon-ecommerce-receipt-rupee'),
	array('icon-ecommerce-receipt-tugrik' => 'icon-ecommerce-receipt-tugrik'),
	array('icon-ecommerce-receipt-won' => 'icon-ecommerce-receipt-won'),
	array('icon-ecommerce-receipt-yen' => 'icon-ecommerce-receipt-yen'),
	array('icon-ecommerce-receipt-yen2' => 'icon-ecommerce-receipt-yen2'),
	array('icon-ecommerce-recept-colon' => 'icon-ecommerce-recept-colon'),
	array('icon-ecommerce-rublo' => 'icon-ecommerce-rublo'),
	array('icon-ecommerce-rupee' => 'icon-ecommerce-rupee'),
	array('icon-ecommerce-safe' => 'icon-ecommerce-safe'),
	array('icon-ecommerce-sale' => 'icon-ecommerce-sale'),
	array('icon-ecommerce-sales' => 'icon-ecommerce-sales'),
	array('icon-ecommerce-ticket' => 'icon-ecommerce-ticket'),
	array('icon-ecommerce-tugriks' => 'icon-ecommerce-tugriks'),
	array('icon-ecommerce-wallet' => 'icon-ecommerce-wallet'),
	array('icon-ecommerce-won' => 'icon-ecommerce-won'),
	array('icon-ecommerce-yen' => 'icon-ecommerce-yen'),
	array('icon-ecommerce-yen2' => 'icon-ecommerce-yen2'),
	array('icon-music-beginning-button' => 'icon-music-beginning-button'),
	array('icon-music-bell' => 'icon-music-bell'),
	array('icon-music-cd' => 'icon-music-cd'),
	array('icon-music-diapason' => 'icon-music-diapason'),
	array('icon-music-eject-button' => 'icon-music-eject-button'),
	array('icon-music-end-button' => 'icon-music-end-button'),
	array('icon-music-fastforward-button' => 'icon-music-fastforward-button'),
	array('icon-music-headphones' => 'icon-music-headphones'),
	array('icon-music-ipod' => 'icon-music-ipod'),
	array('icon-music-loudspeaker' => 'icon-music-loudspeaker'),
	array('icon-music-microphone' => 'icon-music-microphone'),
	array('icon-music-microphone-old' => 'icon-music-microphone-old'),
	array('icon-music-mixer' => 'icon-music-mixer'),
	array('icon-music-mute' => 'icon-music-mute'),
	array('icon-music-note-multiple' => 'icon-music-note-multiple'),
	array('icon-music-note-single' => 'icon-music-note-single'),
	array('icon-music-pause-button' => 'icon-music-pause-button'),
	array('icon-music-playlist' => 'icon-music-play-button'),
	array('icon-music-play-button' => 'icon-music-playlist'),
	array('icon-music-radio-ghettoblaster' => 'icon-music-radio-ghettoblaster'),
	array('icon-music-radio-portable' => 'icon-music-radio-portable'),
	array('icon-music-record' => 'icon-music-record'),
	array('icon-music-recordplayer' => 'icon-music-recordplayer'),
	array('icon-music-repeat-button' => 'icon-music-repeat-button'),
	array('icon-music-rewind-button' => 'icon-music-rewind-button'),
	array('icon-music-shuffle-button' => 'icon-music-shuffle-button'),
	array('icon-music-stop-button' => 'icon-music-stop-button'),
	array('icon-music-tape' => 'icon-music-tape'),
	array('icon-music-volume-down' => 'icon-music-volume-down'),
	array('icon-music-volume-up' => 'icon-music-volume-up'),
	array('icon-software-add-vectorpoint' => 'icon-software-add-vectorpoint'),
	array('icon-software-box-oval' => 'icon-software-box-oval'),
	array('icon-software-box-polygon' => 'icon-software-box-polygon'),
	array('icon-software-box-rectangle' => 'icon-software-box-rectangle'),
	array('icon-software-box-roundedrectangle' => 'icon-software-box-roundedrectangle'),
	array('icon-software-character' => 'icon-software-character'),
	array('icon-software-crop' => 'icon-software-crop'),
	array('icon-software-eyedropper' => 'icon-software-eyedropper'),
	array('icon-software-font-allcaps' => 'icon-software-font-allcaps'),
	array('icon-software-font-baseline-shift' => 'icon-software-font-baseline-shift'),
	array('icon-software-font-horizontal-scale' => 'icon-software-font-horizontal-scale'),
	array('icon-software-font-kerning' => 'icon-software-font-kerning'),
	array('icon-software-font-leading' => 'icon-software-font-leading'),
	array('icon-software-font-size' => 'icon-software-font-size'),
	array('icon-software-font-smallcapital' => 'icon-software-font-smallcapital'),
	array('icon-software-font-smallcaps' => 'icon-software-font-smallcaps'),
	array('icon-software-font-strikethrough' => 'icon-software-font-strikethrough'),
	array('icon-software-font-tracking' => 'icon-software-font-tracking'),
	array('icon-software-font-underline' => 'icon-software-font-underline'),
	array('icon-software-font-vertical-scale' => 'icon-software-font-vertical-scale'),
	array('icon-software-horizontal-align-center' => 'icon-software-horizontal-align-center'),
	array('icon-software-horizontal-align-left' => 'icon-software-horizontal-align-left'),
	array('icon-software-horizontal-align-right' => 'icon-software-horizontal-align-right'),
	array('icon-software-horizontal-distribute-center' => 'icon-software-horizontal-distribute-center'),
	array('icon-software-horizontal-distribute-left' => 'icon-software-horizontal-distribute-left'),
	array('icon-software-horizontal-distribute-right' => 'icon-software-horizontal-distribute-right'),
	array('icon-software-indent-firstline' => 'icon-software-indent-firstline'),
	array('icon-software-indent-left' => 'icon-software-indent-left'),
	array('icon-software-indent-right' => 'icon-software-indent-right'),
	array('icon-software-lasso' => 'icon-software-lasso'),
	array('icon-software-layers1' => 'icon-software-layers1'),
	array('icon-software-layers2' => 'icon-software-layers2'),
	array('icon-software-layout-8boxes' => 'icon-software-layout'),
	array('icon-software-layout' => 'icon-software-layout-2columns'),
	array('icon-software-layout-2columns' => 'icon-software-layout-3columns'),
	array('icon-software-layout-3columns' => 'icon-software-layout-4boxes'),
	array('icon-software-layout-4boxes' => 'icon-software-layout-4columns'),
	array('icon-software-layout-4columns' => 'icon-software-layout-4lines'),
	array('icon-software-layout-4lines' => 'icon-software-layout-8boxes'),
	array('icon-software-layout-header' => 'icon-software-layout-header'),
	array('icon-software-layout-header-2columns' => 'icon-software-layout-header-2columns'),
	array('icon-software-layout-header-3columns' => 'icon-software-layout-header-3columns'),
	array('icon-software-layout-header-4boxes' => 'icon-software-layout-header-4boxes'),
	array('icon-software-layout-header-4columns' => 'icon-software-layout-header-4columns'),
	array('icon-software-layout-header-complex' => 'icon-software-layout-header-complex'),
	array('icon-software-layout-header-complex2' => 'icon-software-layout-header-complex2'),
	array('icon-software-layout-header-complex3' => 'icon-software-layout-header-complex3'),
	array('icon-software-layout-header-complex4' => 'icon-software-layout-header-complex4'),
	array('icon-software-layout-header-sideleft' => 'icon-software-layout-header-sideleft'),
	array('icon-software-layout-header-sideright' => 'icon-software-layout-header-sideright'),
	array('icon-software-layout-sidebar-left' => 'icon-software-layout-sidebar-left'),
	array('icon-software-layout-sidebar-right' => 'icon-software-layout-sidebar-right'),
	array('icon-software-magnete' => 'icon-software-magnete'),
	array('icon-software-pages' => 'icon-software-pages'),
	array('icon-software-paintbrush' => 'icon-software-paintbrush'),
	array('icon-software-paintbucket' => 'icon-software-paintbucket'),
	array('icon-software-paintroller' => 'icon-software-paintroller'),
	array('icon-software-paragraph' => 'icon-software-paragraph'),
	array('icon-software-paragraph-align-left' => 'icon-software-paragraph-align-left'),
	array('icon-software-paragraph-align-right' => 'icon-software-paragraph-align-right'),
	array('icon-software-paragraph-center' => 'icon-software-paragraph-center'),
	array('icon-software-paragraph-justify-all' => 'icon-software-paragraph-justify-all'),
	array('icon-software-paragraph-justify-center' => 'icon-software-paragraph-justify-center'),
	array('icon-software-paragraph-justify-left' => 'icon-software-paragraph-justify-left'),
	array('icon-software-paragraph-justify-right' => 'icon-software-paragraph-justify-right'),
	array('icon-software-paragraph-space-after' => 'icon-software-paragraph-space-after'),
	array('icon-software-paragraph-space-before' => 'icon-software-paragraph-space-before'),
	array('icon-software-pathfinder-exclude' => 'icon-software-pathfinder-exclude'),
	array('icon-software-pathfinder-intersect' => 'icon-software-pathfinder-intersect'),
	array('icon-software-pathfinder-subtract' => 'icon-software-pathfinder-subtract'),
	array('icon-software-pathfinder-unite' => 'icon-software-pathfinder-unite'),
	array('icon-software-pen' => 'icon-software-pen'),
	array('icon-software-pencil' => 'icon-software-pen-add'),
	array('icon-software-pen-add' => 'icon-software-pen-remove'),
	array('icon-software-pen-remove' => 'icon-software-pencil'),
	array('icon-software-polygonallasso' => 'icon-software-polygonallasso'),
	array('icon-software-reflect-horizontal' => 'icon-software-reflect-horizontal'),
	array('icon-software-reflect-vertical' => 'icon-software-reflect-vertical'),
	array('icon-software-remove-vectorpoint' => 'icon-software-remove-vectorpoint'),
	array('icon-software-scale-expand' => 'icon-software-scale-expand'),
	array('icon-software-scale-reduce' => 'icon-software-scale-reduce'),
	array('icon-software-selection-oval' => 'icon-software-selection-oval'),
	array('icon-software-selection-polygon' => 'icon-software-selection-polygon'),
	array('icon-software-selection-rectangle' => 'icon-software-selection-rectangle'),
	array('icon-software-selection-roundedrectangle' => 'icon-software-selection-roundedrectangle'),
	array('icon-software-shape-oval' => 'icon-software-shape-oval'),
	array('icon-software-shape-polygon' => 'icon-software-shape-polygon'),
	array('icon-software-shape-rectangle' => 'icon-software-shape-rectangle'),
	array('icon-software-shape-roundedrectangle' => 'icon-software-shape-roundedrectangle'),
	array('icon-software-slice' => 'icon-software-slice'),
	array('icon-software-transform-bezier' => 'icon-software-transform-bezier'),
	array('icon-software-vector-box' => 'icon-software-vector-box'),
	array('icon-software-vector-composite' => 'icon-software-vector-composite'),
	array('icon-software-vector-line' => 'icon-software-vector-line'),
	array('icon-software-vertical-align-bottom' => 'icon-software-vertical-align-bottom'),
	array('icon-software-vertical-align-center' => 'icon-software-vertical-align-center'),
	array('icon-software-vertical-align-top' => 'icon-software-vertical-align-top'),
	array('icon-software-vertical-distribute-bottom' => 'icon-software-vertical-distribute-bottom'),
	array('icon-software-vertical-distribute-center' => 'icon-software-vertical-distribute-center'),
	array('icon-software-vertical-distribute-top' => 'icon-software-vertical-distribute-top'),
	array('icon-weather-aquarius' => 'icon-weather-aquarius'),
	array('icon-weather-aries' => 'icon-weather-aries'),
	array('icon-weather-cancer' => 'icon-weather-cancer'),
	array('icon-weather-capricorn' => 'icon-weather-capricorn'),
	array('icon-weather-cloud' => 'icon-weather-cloud'),
	array('icon-weather-cloud-drop' => 'icon-weather-cloud-drop'),
	array('icon-weather-cloud-lightning' => 'icon-weather-cloud-lightning'),
	array('icon-weather-cloud-snowflake' => 'icon-weather-cloud-snowflake'),
	array('icon-weather-downpour-fullmoon' => 'icon-weather-downpour-fullmoon'),
	array('icon-weather-downpour-halfmoon' => 'icon-weather-downpour-halfmoon'),
	array('icon-weather-downpour-sun' => 'icon-weather-downpour-sun'),
	array('icon-weather-drop' => 'icon-weather-drop'),
	array('icon-weather-first-quarter ' => 'icon-weather-first-quarter'),
	array('icon-weather-fog' => 'icon-weather-fog'),
	array('icon-weather-fog-fullmoon' => 'icon-weather-fog-fullmoon'),
	array('icon-weather-fog-halfmoon' => 'icon-weather-fog-halfmoon'),
	array('icon-weather-fog-sun' => 'icon-weather-fog-sun'),
	array('icon-weather-fullmoon' => 'icon-weather-fullmoon'),
	array('icon-weather-gemini' => 'icon-weather-gemini'),
	array('icon-weather-hail' => 'icon-weather-hail'),
	array('icon-weather-hail-fullmoon' => 'icon-weather-hail-fullmoon'),
	array('icon-weather-hail-halfmoon' => 'icon-weather-hail-halfmoon'),
	array('icon-weather-hail-sun' => 'icon-weather-hail-sun'),
	array('icon-weather-last-quarter' => 'icon-weather-last-quarter'),
	array('icon-weather-leo' => 'icon-weather-leo'),
	array('icon-weather-libra' => 'icon-weather-libra'),
	array('icon-weather-lightning' => 'icon-weather-lightning'),
	array('icon-weather-mistyrain' => 'icon-weather-mistyrain'),
	array('icon-weather-mistyrain-fullmoon' => 'icon-weather-mistyrain-fullmoon'),
	array('icon-weather-mistyrain-halfmoon' => 'icon-weather-mistyrain-halfmoon'),
	array('icon-weather-mistyrain-sun' => 'icon-weather-mistyrain-sun'),
	array('icon-weather-moon' => 'icon-weather-moon'),
	array('icon-weather-moondown-full' => 'icon-weather-moondown-full'),
	array('icon-weather-moondown-half' => 'icon-weather-moondown-half'),
	array('icon-weather-moonset-full' => 'icon-weather-moonset-full'),
	array('icon-weather-moonset-half' => 'icon-weather-moonset-half'),
	array('icon-weather-move2' => 'icon-weather-move2'),
	array('icon-weather-newmoon' => 'icon-weather-newmoon'),
	array('icon-weather-pisces' => 'icon-weather-pisces'),
	array('icon-weather-rain' => 'icon-weather-rain'),
	array('icon-weather-rain-fullmoon' => 'icon-weather-rain-fullmoon'),
	array('icon-weather-rain-halfmoon' => 'icon-weather-rain-halfmoon'),
	array('icon-weather-rain-sun' => 'icon-weather-rain-sun'),
	array('icon-weather-sagittarius' => 'icon-weather-sagittarius'),
	array('icon-weather-scorpio' => 'icon-weather-scorpio'),
	array('icon-weather-snow' => 'icon-weather-snow'),
	array('icon-weather-snowflake' => 'icon-weather-snowflake'),
	array('icon-weather-snow-fullmoon' => 'icon-weather-snow-fullmoon'),
	array('icon-weather-snow-halfmoon' => 'icon-weather-snow-halfmoon'),
	array('icon-weather-snow-sun' => 'icon-weather-snow-sun'),
	array('icon-weather-star' => 'icon-weather-star'),
	array('icon-weather-storm-11' => 'icon-weather-storm-11'),
	array('icon-weather-storm-32' => 'icon-weather-storm-32'),
	array('icon-weather-storm-fullmoon' => 'icon-weather-storm-fullmoon'),
	array('icon-weather-storm-halfmoon' => 'icon-weather-storm-halfmoon'),
	array('icon-weather-storm-sun' => 'icon-weather-storm-sun'),
	array('icon-weather-sun' => 'icon-weather-sun'),
	array('icon-weather-sundown' => 'icon-weather-sundown'),
	array('icon-weather-sunset' => 'icon-weather-sunset'),
	array('icon-weather-taurus' => 'icon-weather-taurus'),
	array('icon-weather-tempest' => 'icon-weather-tempest'),
	array('icon-weather-tempest-fullmoon' => 'icon-weather-tempest-fullmoon'),
	array('icon-weather-tempest-halfmoon' => 'icon-weather-tempest-halfmoon'),
	array('icon-weather-tempest-sun' => 'icon-weather-tempest-sun'),
	array('icon-weather-variable-fullmoon' => 'icon-weather-variable-fullmoon'),
	array('icon-weather-variable-halfmoon' => 'icon-weather-variable-halfmoon'),
	array('icon-weather-variable-sun' => 'icon-weather-variable-sun'),
	array('icon-weather-virgo' => 'icon-weather-virgo'),
	array('icon-weather-waning-cresent' => 'icon-weather-waning-cresent'),
	array('icon-weather-waning-gibbous' => 'icon-weather-waning-gibbous'),
	array('icon-weather-waxing-cresent' => 'icon-weather-waxing-cresent'),
	array('icon-weather-waxing-gibbous' => 'icon-weather-waxing-gibbous'),
	array('icon-weather-wind' => 'icon-weather-wind'),
	array('icon-weather-windgust' => 'icon-weather-windgust'),
	array('icon-weather-wind-e' => 'icon-weather-wind-e'),
	array('icon-weather-wind-fullmoon' => 'icon-weather-wind-fullmoon'),
	array('icon-weather-wind-halfmoon' => 'icon-weather-wind-halfmoon'),
	array('icon-weather-wind-n' => 'icon-weather-wind-n'),
	array('icon-weather-wind-ne' => 'icon-weather-wind-ne'),
	array('icon-weather-wind-nw' => 'icon-weather-wind-nw'),
	array('icon-weather-wind-s' => 'icon-weather-wind-s'),
	array('icon-weather-wind-se' => 'icon-weather-wind-se'),
	array('icon-weather-wind-sun' => 'icon-weather-wind-sun'),
	array('icon-weather-wind-sw' => 'icon-weather-wind-sw'),
	array('icon-weather-wind-w' => 'icon-weather-wind-w'),
	);

	return array_merge( $icons, $linea_icons );
}

?>