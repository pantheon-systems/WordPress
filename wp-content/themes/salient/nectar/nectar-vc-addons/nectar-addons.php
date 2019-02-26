<?php 

global $nectar_options;

if(!function_exists('nectar_set_vc_as_theme')) {
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
		
		//only allow frontend editor when using Salient WPBakery that supports it
		if ( version_compare( WPB_VC_VERSION, '5.5.4', '<=' ) ) {
			vc_disable_frontend();
		} 

	}
}

add_action('vc_before_init', 'nectar_set_vc_as_theme');




add_filter( 'vc_load_default_templates', 'nectar_custom_template_modify_array' ); // Hook in
function nectar_custom_template_modify_array( $data ) {
    return array(); 
}

vc_remove_element("vc_section");
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
vc_remove_element("vc_images_carousel");
vc_remove_element("vc_wp_archives");
vc_remove_element("vc_wp_calendar");
vc_remove_element("vc_wp_categories");
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
function salient_vc_remove_woocommerce() {
    if ( class_exists( 'woocommerce' ) ) {
		vc_remove_element("recent_products");
		vc_remove_element("featured_products");
		vc_remove_element("product");
		vc_remove_element("products");
		vc_remove_element("add_to_cart_url");
		vc_remove_element("product_page");
		vc_remove_element("sale_products");
		vc_remove_element("best_selling_products");
		vc_remove_element("top_rated_products");
		vc_remove_element("product_attribute");
    }
}
// Hook for admin editor.
add_action( 'vc_build_admin_page', 'salient_vc_remove_woocommerce', 11 );




if ( !function_exists( 'nectar_vc_navbar_mod' ) ) {
	
	function nectar_vc_navbar_mod($list) {

			if ( is_array( $list ) ) {
				
				//remove default template button
				foreach($list as $key => $button) {
						if(isset($button[0]) && $button[0] == 'templates') {
							 unset($list[$key]);
						}
						
				}
				
				//add new template buttons
				$list[] = array( 'salient_studio', nectar_generate_salient_studio_button() );
				$list[] = array( 'user_templates', nectar_generate_user_template_button() );
			}
				
			return $list;
	}
	
}

if ( !function_exists( 'nectar_generate_salient_studio_button' ) ) {
	function nectar_generate_salient_studio_button() {
		return '<li><a href="javascript:;" class="vc_icon-btn vc_templates-button salient-studio-templates"  id="vc_templates-editor-button" title="'
					 . esc_html__('Salient studio template library', 'salient' ) . '"><i class="vc-composer-icon vc-c-icon-add_template"></i> <span>'. esc_html__('Salient Templates','salient'). '</span></a></li>';
	}
}

if ( !function_exists( 'nectar_generate_user_template_button' ) ) {
	function nectar_generate_user_template_button() {
		return '<li><a href="javascript:;" class="vc_icon-btn vc_templates-button user-templates"  id="vc_templates-editor-button" title="'
					 . esc_html__('My templates', 'salient' ) . '"><i class="vc-composer-icon vc-c-icon-add_template"></i> <span>'. esc_html__('My Templates','salient'). '</span></a></li>';
	}
}

add_filter('vc_nav_controls','nectar_vc_navbar_mod');
add_filter('vc_nav_front_controls','nectar_vc_navbar_mod');






//only load shortcode logic on front when needed
$is_admin = is_admin();


global $nectar_get_template_directory_uri;


function nectar_select_color_styles() {
	global $nectar_options;

	$nectar_accent_color = (!empty($nectar_options["accent-color"])) ? $nectar_options["accent-color"] : 'transparent';
	$nectar_extra_color_1 = (!empty($nectar_options["extra-color-1"])) ? $nectar_options["extra-color-1"] : 'transparent';
	$nectar_extra_color_2 = (!empty($nectar_options["extra-color-2"])) ? $nectar_options["extra-color-2"] : 'transparent';
	$nectar_extra_color_3 = (!empty($nectar_options["extra-color-3"])) ? $nectar_options["extra-color-3"] : 'transparent';

	$nectar_color_css = '.vc_edit-form-tab .chosen-container .chosen-results li.Default:before, .vc_edit-form-tab .chosen-container .chosen-results li.default:before, .vc_edit-form-tab .vc_shortcode-param[data-param_type="dropdown"] select[name*="color"].Default + .chosen-container > a:before, .vc_edit-form-tab .vc_shortcode-param[data-param_type="dropdown"] select[name*="color"].default + .chosen-container > a:before { background: linear-gradient(to right, #444 49%, #fff 51%); } 
	.vc_edit-form-tab .chosen-container .chosen-results li[class*="Accent-Color"]:before, .vc_edit-form-tab .chosen-container .chosen-results li.Default-Accent-Color:before, .vc_edit-form-tab .chosen-container .chosen-results li[class*="accent-color"]:before, .vc_edit-form-tab .vc_shortcode-param[data-param_type="dropdown"] select[name*="color"].Default-Accent-Color + .chosen-container > a:before, .vc_edit-form-tab .vc_shortcode-param[data-param_type="dropdown"] select[name*="color"][class*="Accent-Color"] + .chosen-container > a:before, .vc_edit-form-tab .vc_shortcode-param[data-param_type="dropdown"] select[name*="color"][class*="accent-color"] + .chosen-container > a:before, .vc_edit-form-tab .vc_shortcode-param[data-param_type="dropdown"] select[name*="cta_button_style"].accent-color + .chosen-container > a:before { background-color: '.$nectar_accent_color.'; } 
    .vc_edit-form-tab .chosen-container .chosen-results li[class*="Extra-Color-1"]:before, .vc_edit-form-tab .chosen-container .chosen-results li[class*="extra-color-1"]:before, .vc_edit-form-tab .vc_shortcode-param[data-param_type="dropdown"] select[name*="color"][class*="Extra-Color-1"] + .chosen-container > a:before, .vc_edit-form-tab .vc_shortcode-param[data-param_type="dropdown"] select[name*="color"][class*="extra-color-1"] + .chosen-container > a:before, .vc_edit-form-tab .vc_shortcode-param[data-param_type="dropdown"] select[name*="cta_button_style"].extra-color-1 + .chosen-container > a:before { background-color: '.$nectar_extra_color_1.'; }
    .vc_edit-form-tab .chosen-container .chosen-results li[class*="Extra-Color-2"]:before, .vc_edit-form-tab .chosen-container .chosen-results li[class*="extra-color-2"]:before, .vc_edit-form-tab .vc_shortcode-param[data-param_type="dropdown"] select[name*="color"][class*="Extra-Color-2"] + .chosen-container > a:before, .vc_edit-form-tab .vc_shortcode-param[data-param_type="dropdown"] select[name*="color"][class*="extra-color-2"] + .chosen-container > a:before, .vc_edit-form-tab .vc_shortcode-param[data-param_type="dropdown"] select[name*="cta_button_style"].extra-color-2 + .chosen-container > a:before { background-color: '.$nectar_extra_color_2.'; }
    .vc_edit-form-tab .chosen-container .chosen-results li[class*="Extra-Color-3"]:before, .vc_edit-form-tab .chosen-container .chosen-results li[class*="extra-color-3"]:before, .vc_edit-form-tab .vc_shortcode-param[data-param_type="dropdown"] select[name*="color"][class*="Extra-Color-3"] + .chosen-container > a:before, .vc_edit-form-tab .vc_shortcode-param[data-param_type="dropdown"] select[name*="color"][class*="extra-color-3"] + .chosen-container > a:before, .vc_edit-form-tab .vc_shortcode-param[data-param_type="dropdown"] select[name*="cta_button_style"].extra-color-3 + .chosen-container > a:before { background-color: '.$nectar_extra_color_3.'; }';

	if( !empty($nectar_options["extra-color-gradient"]) && $nectar_options["extra-color-gradient"]['to'] && $nectar_options["extra-color-gradient"]['from']) {
		$nectar_gradient_1_from = $nectar_options["extra-color-gradient"]['from'];
		$nectar_gradient_1_to = $nectar_options["extra-color-gradient"]['to'];

		$nectar_color_css .= '.vc_edit-form-tab .chosen-container .chosen-results li.extra-color-gradient-1:before, .vc_edit-form-tab .vc_shortcode-param[data-param_type="dropdown"] select[name*="color"].extra-color-gradient-1 + .chosen-container > a:before, .vc_edit-form-tab .vc_shortcode-param[data-param_type="dropdown"] select[name*="button_color"].extra-color-gradient-1 + .chosen-container > a:before, .vc_edit-form-tab .vc_shortcode-param[data-param_type="dropdown"] select[name="icon_color"].extra-color-gradient-1 + .chosen-container > a:before {  background: linear-gradient(to right, '.$nectar_gradient_1_from.', '.$nectar_gradient_1_to.'); }';
	}

	if( !empty($nectar_options["extra-color-gradient-2"]) && $nectar_options["extra-color-gradient-2"]['to'] && $nectar_options["extra-color-gradient-2"]['from']) {
		$nectar_gradient_2_from = $nectar_options["extra-color-gradient-2"]['from'];
		$nectar_gradient_2_to = $nectar_options["extra-color-gradient-2"]['to'];

		$nectar_color_css .= '.vc_edit-form-tab .chosen-container .chosen-results li.extra-color-gradient-2:before, .vc_edit-form-tab .vc_shortcode-param[data-param_type="dropdown"] select[name*="color"].extra-color-gradient-2 + .chosen-container > a:before, .vc_edit-form-tab .vc_shortcode-param[data-param_type="dropdown"] select[name*="button_color"].extra-color-gradient-2 + .chosen-container > a:before, .vc_edit-form-tab .vc_shortcode-param[data-param_type="dropdown"] select[name="icon_color"].extra-color-gradient-2 + .chosen-container > a:before {  background: linear-gradient(to right, '.$nectar_gradient_2_from.', '.$nectar_gradient_2_to.'); }';
	}

   
    wp_add_inline_style( 'nectar_vc', $nectar_color_css );
}
add_action( 'admin_enqueue_scripts', 'nectar_select_color_styles' );




if(function_exists('vc_add_shortcode_param')) {

	// Create multi dropdown param type
	vc_add_shortcode_param( 'dropdown_multi', 'dropdown_multi_settings_field', get_template_directory_uri().'/nectar/nectar-vc-addons/js/backend-edit-form-bulk.js' );
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

	vc_add_shortcode_param( 'fws_image', 'fws_image_settings_field' );
	function fws_image_settings_field( $param, $value ) {
			$param_line = '';
			$param_line .= '<input type="hidden" class="wpb_vc_param_value gallery_widget_attached_images_ids '.esc_attr($param['param_name']).' '.esc_attr($param['type']).'" name="'.esc_attr($param['param_name']).'" value="'.esc_attr($value).'"/>';
	        //$param_line .= '<a class="button gallery_widget_add_images" href="#" use-single="true" title="'.esc_html__('Add image', "js_composer").'">'.esc_html__('Add image', "js_composer").'</a>';
	        $param_line .= '<div class="gallery_widget_attached_images">';
	        $param_line .= '<ul class="gallery_widget_attached_images_list">';
		
			if(strpos($value, "http://") !== false || strpos($value, "https://") !== false) {
				//$param_value = fjarrett_get_attachment_id_by_url($param_value);
				$param_line .= '<li class="added">
					<img src="'. esc_attr($value) .'" />
					<a href="#" class="vc_icon-remove"><i class="vc-composer-icon vc-c-icon-close"></i></a>
				</li>';
			} else {
				$param_line .= ($value != '') ? fieldAttachedImages(explode(",", esc_attr($value))) : '';
			}
			
	        
	        $param_line .= '</ul>';
	        $param_line .= '</div>';
	        $param_line .= '<div class="gallery_widget_site_images">';
	        // $param_line .= siteAttachedImages(explode(",", $param_value));
	        $param_line .= '</div>';
	        $param_line .= '<a class="gallery_widget_add_images" href="#" use-single="true" title="'.esc_html__('Add image', "js_composer").'">'.esc_html__('Add image', "js_composer").'</a>';//class: button
	        //$param_line .= '<div class="wpb_clear"></div>';

	        return $param_line;
	}


	vc_add_shortcode_param( 'nectar_radio_image', 'nectar_radio_images_settings_field' );
	function nectar_radio_images_settings_field( $param, $value ) {
			$rnd_id = uniqid();
			$options = isset($param['options']) ? $param['options'] : '';

			$param_line = '';
			$param_line .= '<input type="hidden" id="nectar-radio-image-'.$rnd_id.'" class="wpb_vc_param_value '.esc_attr($param['param_name']).' '.esc_attr($param['type']).'" name="'.esc_attr($param['param_name']).'" value="'.esc_attr($value).'"/>';
	        $param_line .= '<div class="nectar-radio-image" data-grp-id="' . $rnd_id . '">';
	        $param_line .= '<ul class="nectar_radio_images_list">';
		
			foreach($options as $k => $v) {

				foreach($v as $name => $image_src) {

					if($value == $k) {
						$checked = 'checked';
					}
					else {
						$checked = '';
					}

					$param_line .= '<li><label>
						<input type="radio" class="n_radio_image_val" value="'. $k .'" name="n_radio_image_' . $rnd_id . '" ' . $checked . ' />
						<span class="n_radio_image_src"><img src="'. $image_src .'" alt="'. $name.'" /></span>
						<span class="n_radio_image_title">'.$name.'</span>
					</label></li>';
				}

				
			}
			
	        
	        $param_line .= '</ul>';
	        $param_line .= '</div>';

	        return $param_line;
	}


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
	$nectar_template_dir = get_template_directory();
	$nectar_get_template_directory_uri = get_template_directory_uri();

	vc_map( array(
		'name' => __( 'Row', 'js_composer' ),
		'base' => 'vc_row',
		'is_container' => true,
		'weight' => 11,
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
				'type' => 'checkbox',
				'heading' => __( 'Full height row?', 'js_composer' ),
				'param_name' => 'full_height',
				'description' => __( 'If checked row will be set to full height.', 'js_composer' ),
				'value' => array( __( 'Yes', 'js_composer' ) => 'yes' ),
			),

			array(
				'type' => 'dropdown',
				'heading' => __( 'Columns position', 'js_composer' ),
				'param_name' => 'columns_placement',
				'value' => array(
					__( 'Middle', 'js_composer' ) => 'middle',
					__( 'Top', 'js_composer' ) => 'top',
					__( 'Bottom', 'js_composer' ) => 'bottom',
					__( 'Stretch', 'js_composer' ) => 'stretch',
				),
				'description' => __( 'Select columns position within row.', 'js_composer' ),
				'dependency' => array(
					'element' => 'full_height',
					'not_empty' => true,
				),
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
				  "heading" => esc_html__("Scene Positioning", "js_composer"),
				  "param_name" => "scene_position",
				  'save_always' => true,
				  "value" => array(
			  		 "Center" => "center",
			  		 "Top" => "top",
			  		 "Bottom" => "bottom"
					),
				  "description" => esc_html__("Select your desired scene alignment within your row", "js_composer")
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
				"heading" => "Translate Y",
				"value" => "",
				"edit_field_class" => "col-md-6",
				"param_name" => "translate_y",
				"description" => ""
			),

			array(
				"type" => "textfield",
				"class" => "",
				"heading" => "Translate X",
				"value" => "",
				"edit_field_class" => "col-md-6",
				"param_name" => "translate_x",
				"description" => ""
			),
			
			array(
				"type" => "textfield",
				"class" => "",
				"heading" => "Z-Index",
				"param_name" => "zindex",
				"description" => "If you want to set a custom stacking order on this row, enter it here. Can be useful when overlapping elements from other rows with negative margins/translates.",
				"value" => ""
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
			),

			array(
				"type" => "checkbox",
				"class" => "",
				"group" => "Shape Divider",
				"heading" => "Enable Shape Divider",
				"value" => array("Yes, please" => "true" ),
				"param_name" => "enable_shape_divider",
				"description" => ""
			),
			array(
				"type" => "nectar_radio_image",
				"class" => "",
				'save_always' => true,
				"heading" => "Shape Type",
				"param_name" => "shape_type",
				"group" => "Shape Divider",
				"options" => array(
					"curve" => array( esc_html__('Curve', 'salient') => $nectar_get_template_directory_uri."/nectar/nectar-vc-addons/img/shape_dividers/curve_down.jpg"),
					'fan' => array( esc_html__('Fan', 'salient') => $nectar_get_template_directory_uri."/nectar/nectar-vc-addons/img/shape_dividers/fan.jpg"),
					'curve_opacity' => array( esc_html__('Curve Opacity', 'salient') => $nectar_get_template_directory_uri."/nectar/nectar-vc-addons/img/shape_dividers/curve_opacity.jpg"),
					"mountains" => array( esc_html__('Mountains', 'salient') => $nectar_get_template_directory_uri."/nectar/nectar-vc-addons/img/shape_dividers/mountains.jpg"),
					'curve_asym' => array( esc_html__('Curve Asym.', 'salient') => $nectar_get_template_directory_uri."/nectar/nectar-vc-addons/img/shape_dividers/curve_asym.jpg"),
					'curve_asym_2' => array( esc_html__('Curve Asym. Alt', 'salient') => $nectar_get_template_directory_uri."/nectar/nectar-vc-addons/img/shape_dividers/curve_asym_2.jpg"),
					"tilt" => array( esc_html__('Tilt', 'salient') => $nectar_get_template_directory_uri."/nectar/nectar-vc-addons/img/shape_dividers/tilt.jpg"),
					"tilt_alt" => array( esc_html__('Tilt Alt', 'salient') => $nectar_get_template_directory_uri."/nectar/nectar-vc-addons/img/shape_dividers/tilt_alt.jpg"),
					"triangle" => array( esc_html__('Triangle', 'salient') => $nectar_get_template_directory_uri."/nectar/nectar-vc-addons/img/shape_dividers/triangle.jpg"),
					'waves' => array( esc_html__('Waves', 'salient') => $nectar_get_template_directory_uri."/nectar/nectar-vc-addons/img/shape_dividers/waves_no_opacity.jpg"),
					'waves_opacity' => array( esc_html__('Waves Opacity', 'salient') => $nectar_get_template_directory_uri."/nectar/nectar-vc-addons/img/shape_dividers/waves.jpg"),
					'waves_opacity_alt' => array( esc_html__('Waves Opacity 2', 'salient') => $nectar_get_template_directory_uri."/nectar/nectar-vc-addons/img/shape_dividers/waves_opacity.jpg"),
					'clouds' => array( esc_html__('Clouds', 'salient') => $nectar_get_template_directory_uri."/nectar/nectar-vc-addons/img/shape_dividers/clouds.jpg"),
					"speech" => array( esc_html__('Speech', 'salient') => $nectar_get_template_directory_uri."/nectar/nectar-vc-addons/img/shape_dividers/speech.jpg"),
					"straight_section" => array( esc_html__('Straight Section', 'salient') => $nectar_get_template_directory_uri."/nectar/nectar-vc-addons/img/shape_dividers/straight_section.jpg")
				),
			),
			array(
				"type" => "colorpicker",
				"class" => "",
				"heading" => "Shape Divider Color",
				"param_name" => "shape_divider_color",
				"value" => "",
				"group" => "Shape Divider",
				"description" => ""
			),
			array(
				"type" => "dropdown",
				"class" => "",
				'save_always' => true,
				"heading" => "Shape Divider Position",
				"param_name" => "shape_divider_position",
				"group" => "Shape Divider",
				"value" => array(
					"Bottom" => "bottom",
					"Top" => "top",
					"Bottom & Top" => 'both'
				),
			),
			array(
				"type" => "textfield",
				"class" => "",
				"group" => "Shape Divider",
				"heading" => "Shape Divider Height",
				"param_name" => "shape_divider_height",
				"value" => "",
				"description" => "Enter an optional custom height for your shape divider in pixels without the \"px\", e.g. 50"
			),
			array(
				"type" => "checkbox",
				"class" => "",
				"group" => "Shape Divider",
				"heading" => "Bring to front?",
				"value" => array("Yes, please" => "true" ),
				"param_name" => "shape_divider_bring_to_front",
				"description" => "This will bring the shape divider to the top layer, placing it on top of any content it intersects/"
			),
			array(
				"type" => "dropdown",
				"class" => "",
				'save_always' => true,
				"heading" => "Background Image Animation",
				"param_name" => "bg_image_animation",
				"group" => "Animation",
				"value" => array(
					"None" => "none",
					"Fade In" => "fade-in",
					"Zoom Out" => 'zoom-out',
					"Zoom Out Slowly" => 'zoom-out-slow'
				),
			),
			array(
				"type" => "checkbox",
				"class" => "",
				"heading" => "Parallax Background Image Scroll",
				"value" => array("Enable Parallax Background?" => "true" ),
				"param_name" => "parallax_bg",
				"description" => "This will cause the background image on your row to scroll at a different speed than the content",
				"group" => "Animation"
			),

			array(
				"type" => "dropdown",
				"class" => "",
				"description" => "The faster you choose, the closer your BG will match the users scroll speed",
				"heading" => "Parallax Background Image Speed",
				"param_name" => "parallax_bg_speed",
				'save_always' => true,
				"value" => array(
					 "Slow" => "slow",
						 "Medium" => "medium",
						 "Fast" => "fast",
						 "Fixed" => "fixed"
				),
				"group" => "Animation",
				"dependency" => Array('element' => "parallax_bg", 'not_empty' => true)
			),

		
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
      "heading" => esc_html__("Max Width", "js_composer"),
      "param_name" => "max_width",
      "admin_label" => false,
      "description" => esc_html__("Optionally enter your desired max width in pixels without the \"px\", e.g. 200", "js_composer")
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
		__( '12 columns - 1/1', 'js_composer' ) => '1/1',
		__( '20% - 1/5', 'js_composer' ) => '1/5',
		__( '40% - 2/5', 'js_composer' ) => '2/5',
		__( '60% - 3/5', 'js_composer' ) => '3/5',
		__( '80% - 4/5', 'js_composer' ) => '4/5'
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
					"15%" => "padding-15-percent",
					"16%" => "padding-16-percent",
					"17%" => "padding-17-percent"
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
				"type" => "checkbox",
				"class" => "",
				"heading" => "Video Background",
				"value" => array("Enable Video Background?" => "use_video" ),
				"param_name" => "video_bg",
				"description" => ""
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
				"type" => "dropdown",
				"class" => "",
				"heading" => "Column Link Target",
				"param_name" => "column_link_target",
				'save_always' => true,
				'value' => array(esc_html__("Same window", "js_composer") => "_self", esc_html__("New window", "js_composer") => "_blank")
			),
			
			array(
		      "type" => "dropdown",
		      "heading" => esc_html__("Box Shadow", "js_composer"),
		      'save_always' => true,
		      "param_name" => "column_shadow",
		      "value" => array(esc_html__("None", "js_composer") => "none", esc_html__("Small Depth", "js_composer") => "small_depth", esc_html__("Medium Depth", "js_composer") => "medium_depth", esc_html__("Large Depth", "js_composer") => "large_depth", esc_html__("Very Large Depth", "js_composer") => "x_large_depth"),
		      "description" => esc_html__("Select your desired column box shadow", "js_composer")
		    ),	
				array(
			      "type" => "dropdown",
			      "heading" => esc_html__("Border Radius", "js_composer"),
			      'save_always' => true,
			      "param_name" => "column_border_radius",
			      "value" => array(
							esc_html__("0px", "js_composer") => "none",
							esc_html__("3px", "js_composer") => "3px",
							esc_html__("5px", "js_composer") => "5px", 
							esc_html__("10px", "js_composer") => "10px", 
							esc_html__("15px", "js_composer") => "15px", 
							esc_html__("20px", "js_composer") => "20px"),
			      "description" => esc_html__("This will round the edges of your column", "js_composer")
			    ),	
			array(
				"type" => "textfield",
				"class" => "",
				"heading" => "Margin Top",
				"value" => "",
				"param_name" => "top_margin",
				"description" => "Don't include \"px\" in your strings . e.g \"40\" - However you can also use a percent value in which case a \"%\" would be needed at the end e.g. \"10%\". Negative Values are also accepted."
			),

			array(
				"type" => "textfield",
				"class" => "",
				"heading" => "Margin Bottom",
				"value" => "",
				"param_name" => "bottom_margin",
				"description" => ""
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
				"heading" => "Tablet Column Width Inherits From",
				"param_name" => "tablet_width_inherit",
				"value" => array(
					"Mobile Column Width (Default)" => "default",
					"Small Desktop Colummn Width" => "small_desktop",
				),
				"description" => "This allows you to determine what your column width will inherit from when viewed on tablets in a portrait orientation."
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
			),

			array(
				"type" => "dropdown",
				"class" => "",
				'save_always' => true,
				'group' => __( 'Border', 'js_composer' ),
				"heading" => "Border Width",
				"param_name" => "column_border_width",
				"value" => array(
					"0px" => "none",
					"1px" => "1px",
					"2px" => "2px",
					"3px" => "3px",
					"4px" => "4px",
					"5px" => "5px",
					"6px" => "6px",
					"7px" => "7px",
					"8px" => "8px"
				),
				"description" => ""
			),
			array(
				"type" => "colorpicker",
				"class" => "",
				"heading" => "Border Color",
				"param_name" => "column_border_color",
				'group' => __( 'Border', 'js_composer' ),
				"value" => "",
				"description" => ""
			),
			array(
				"type" => "dropdown",
				"class" => "",
				'save_always' => true,
				'group' => __( 'Border', 'js_composer' ),
				"heading" => "Border Style",
				"param_name" => "column_border_style",
				"value" => array(
					"Solid" => "solid",
					"Dotted" => "dotted",
					"Dashed" => "dashed",
					"Double" => "double",
					"Double Offset" => "double_offset"
				),
				"description" => "",
				"dependency" => Array('element' => "column_border_radius", 'value' => 'none')
			),
			array(
				"type" => "checkbox",
				"class" => "",
				'group' => __( 'Border', 'js_composer' ),
				"heading" => "Enable Border Animation",
				"value" => array("Enable Animation?" => "true" ),
				"param_name" => "enable_border_animation",
				"description" => "",
				"dependency" => Array('element' => "column_border_radius", 'value' => 'none')
			),

			array(
				"type" => "textfield",
				"class" => "",
				"heading" => "Animation Delay",
				'group' => __( 'Border', 'js_composer' ),
				"param_name" => "border_animation_delay",
				"admin_label" => false,
				"description" => esc_html__("Enter delay (in milliseconds) if needed e.g. 150. This parameter comes in handy when creating the animate in \"one by one\" effect.", "js_composer"),
				"dependency" => Array('element' => "enable_border_animation", 'not_empty' => true)
			),
			array(
				"type" => "dropdown",
				"class" => "",
				'save_always' => true,
				"heading" => "Column Background Image Animation",
				"param_name" => "bg_image_animation",
				"group" => "Animation",
				"description" => "This will animate the optional background image layer of your column only when scrolled into view",
				"value" => array(
					"None" => "none",
					"Fade In" => "fade-in",
					"Zoom Out" => 'zoom-out',
					"Zoom Out Slowly" => 'zoom-out-slow'
				),
			),
			
			array(
				"type" => "checkbox",
				"class" => "",
				"group" => "Animation",
				"heading" => "Entire Column Animation",
				"value" => array("Enable?" => "true" ),
				"param_name" => "enable_animation",
				"description" => "This will animate the entire column and all of its contents when scrolled into view"
			),

			array(
				"type" => "dropdown",
				"class" => "",
				"group" => "Animation",
				"heading" => "Column Animation",
				"param_name" => "animation",
				'save_always' => true,
				"value" => array(
					 "None" => "none",
						 "Fade In" => "fade-in",
						 "Fade In From Left" => "fade-in-from-left",
						 "Fade In Right" => "fade-in-from-right",
						 "Fade In From Bottom" => "fade-in-from-bottom",
						 "Grow In" => "grow-in",
						 "Zoom Out" => 'zoom-out',
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
				"group" => "Animation",
				"heading" => "Column Animation Delay",
				"param_name" => "delay",
				"admin_label" => false,
				"description" => esc_html__("Enter delay (in milliseconds) if needed e.g. 150. This parameter comes in handy when creating the animate in \"one by one\" effect.", "js_composer"),
				"dependency" => Array('element' => "enable_animation", 'not_empty' => true)
			),
			

		),
		'js_view' => 'VcColumnView'
	) );
	
	
	
	vc_map( array(
		"name" => __( "Inner Column", "js_composer" ),
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
				"type" => "checkbox",
				"class" => "",
				"heading" => "Centered Content",
				"value" => array("Centered Content Alignment" => "true" ),
				"param_name" => "centered_text",
				"description" => ""
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
				"type" => "colorpicker",
				"class" => "",
				"heading" => "Font Color",
				"param_name" => "font_color",
				"value" => "",
				"description" => ""
			),
			array(
		      "type" => "dropdown",
		      "heading" => esc_html__("Box Shadow", "js_composer"),
		      'save_always' => true,
		      "param_name" => "column_shadow",
		      "value" => array(esc_html__("None", "js_composer") => "none", esc_html__("Small Depth", "js_composer") => "small_depth", esc_html__("Medium Depth", "js_composer") => "medium_depth", esc_html__("Large Depth", "js_composer") => "large_depth", esc_html__("Very Large Depth", "js_composer") => "x_large_depth"),
		      "description" => esc_html__("Select your desired column box shadow", "js_composer")
		    ),	
			array(
					"type" => "dropdown",
					"heading" => esc_html__("Border Radius", "js_composer"),
					'save_always' => true,
					"param_name" => "column_border_radius",
					"value" => array(
						esc_html__("0px", "js_composer") => "none",
						esc_html__("3px", "js_composer") => "3px",
						esc_html__("5px", "js_composer") => "5px", 
						esc_html__("10px", "js_composer") => "10px", 
						esc_html__("15px", "js_composer") => "15px", 
						esc_html__("20px", "js_composer") => "20px"),
					"description" => esc_html__("This will round the edges of your column", "js_composer")
				),	
				
				array(
					"type" => "textfield",
					"class" => "",
					"heading" => "Margin Top",
					"value" => "",
					"param_name" => "top_margin",
					"description" => "Don't include \"px\" in your strings . e.g \"40\" - However you can also use a percent value in which case a \"%\" would be needed at the end e.g. \"10%\". Negative Values are also accepted."
				),
	
				array(
					"type" => "textfield",
					"class" => "",
					"heading" => "Margin Bottom",
					"value" => "",
					"param_name" => "bottom_margin",
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
				"type" => "dropdown",
				"class" => "",
				"heading" => "Column Link Target",
				"param_name" => "column_link_target",
				'save_always' => true,
				'value' => array(esc_html__("Same window", "js_composer") => "_self", esc_html__("New window", "js_composer") => "_blank")
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
				"heading" => "Tablet Column Width Inherits From",
				"param_name" => "tablet_width_inherit",
				"value" => array(
					"Mobile Column Width (Default)" => "default",
					"Small Desktop Colummn Width" => "small_desktop",
				),
				"description" => "This allows you to determine what your column width will inherit from when viewed on tablets in a portrait orientation."
			),

			array(
				"type" => "dropdown",
				"class" => "",
				'save_always' => true,
				'group' => __( 'Border', 'js_composer' ),
				"heading" => "Border Width",
				"param_name" => "column_border_width",
				"value" => array(
					"0px" => "none",
					"1px" => "1px",
					"2px" => "2px",
					"3px" => "3px",
					"4px" => "4px",
					"5px" => "5px",
					"6px" => "6px",
					"7px" => "7px",
					"8px" => "8px",
					"9px" => "9px",
					"10px" => "10px"
				),
				"description" => ""
			),
			array(
				"type" => "colorpicker",
				"class" => "",
				"heading" => "Border Color",
				"param_name" => "column_border_color",
				'group' => __( 'Border', 'js_composer' ),
				"value" => "",
				"description" => ""
			),
			array(
				"type" => "dropdown",
				"class" => "",
				'save_always' => true,
				'group' => __( 'Border', 'js_composer' ),
				"heading" => "Border Style",
				"param_name" => "column_border_style",
				"value" => array(
					"Solid" => "solid",
					"Dotted" => "dotted",
					"Dashed" => "dashed",
					"Double" => "double",
					"Double Offset" => "double_offset"
				),
				"description" => ""
			),
			array(
				"type" => "checkbox",
				"class" => "",
				'group' => __( 'Border', 'js_composer' ),
				"heading" => "Enable Border Animation",
				"value" => array("Enable Animation?" => "true" ),
				"param_name" => "enable_border_animation",
				"description" => ""
			),

			array(
				"type" => "textfield",
				"class" => "",
				"heading" => "Animation Delay",
				'group' => __( 'Border', 'js_composer' ),
				"param_name" => "border_animation_delay",
				"admin_label" => false,
				"description" => esc_html__("Enter delay (in milliseconds) if needed e.g. 150. This parameter comes in handy when creating the animate in \"one by one\" effect.", "js_composer"),
				"dependency" => Array('element' => "enable_border_animation", 'not_empty' => true)
			),
			
			array(
				"type" => "dropdown",
				"class" => "",
				'save_always' => true,
				"heading" => "Column Background Image Animation",
				"param_name" => "bg_image_animation",
				"group" => "Animation",
				"description" => "This will animate the optional background image layer of your column only when scrolled into view",
				"value" => array(
					"None" => "none",
					"Fade In" => "fade-in",
					"Zoom Out" => 'zoom-out',
					"Zoom Out Slowly" => 'zoom-out-slow'
				),
			),
			
			array(
				"type" => "checkbox",
				"class" => "",
				"group" => "Animation",
				"heading" => "Entire Column Animation",
				"value" => array("Enable?" => "true" ),
				"param_name" => "enable_animation",
				"description" => "This will animate the entire column and all of its contents when scrolled into view"
			),

			array(
				"type" => "dropdown",
				"class" => "",
				'save_always' => true,
				"heading" => "Column Animation",
				"group" => "Animation",
				"param_name" => "animation",
				"value" => array(
					 "None" => "none",
						 "Fade In" => "fade-in",
						 "Fade In From Left" => "fade-in-from-left",
						 "Fade In Right" => "fade-in-from-right",
						 "Fade In From Bottom" => "fade-in-from-bottom",
						 "Grow In" => "grow-in",
						 "Zoom Out" => 'zoom-out',
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
				"heading" => "Column Animation Delay",
				"group" => "Animation",
				"param_name" => "delay",
				"admin_label" => false,
				"description" => esc_html__("Enter delay (in milliseconds) if needed e.g. 150. This parameter comes in handy when creating the animate in \"one by one\" effect.", "js_composer"),
				"dependency" => Array('element' => "enable_animation", 'not_empty' => true)
			),


		),
		"js_view" => 'VcColumnView'
	) );



	//inner row class fix
	vc_remove_param("vc_row_inner", "el_class");
	vc_remove_param("vc_row_inner", "el_id");

	//columns gap
	vc_remove_param("vc_row_inner", "gap");


	vc_add_param("vc_row_inner", array(
		"type" => "dropdown",
		"class" => "",
		'save_always' => true,
		"heading" => "Column Margin",
		"param_name" => "column_margin",
		"value" => array(
			"Default" => "default",
			"None" => "none"
		)
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
		"type" => "textfield",
		"class" => "",
		"heading" => "Translate Y",
		"value" => "",
		"edit_field_class" => "col-md-6",
		"param_name" => "translate_y",
		"description" => ""
	));

	vc_add_param("vc_row_inner", array(
		"type" => "textfield",
		"class" => "",
		"heading" => "Translate X",
		"value" => "",
		"edit_field_class" => "col-md-6",
		"param_name" => "translate_x",
		"description" => ""
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

	vc_add_param("vc_row_inner", array(
		"type" => "textfield",
		"class" => "",
		"heading" => "Extra Class Name",
		"param_name" => "class",
		"value" => ""
	));

	vc_add_param("vc_row_inner",  array(
        'type' => 'css_editor',
        'heading' => 'Css' ,
        'param_name' => 'css',
        'group' => 'Design options',
    ));

	vc_add_param("vc_row_inner",  array(
		"type" => "textfield",
		"class" => "",
		"heading" => "Row ID",
		"param_name" => "el_id",
		"value" => "",
		"description" => "Use this to option to add an ID onto your row. This can then be used to target the row with CSS or as an anchor point to scroll to when the relevant link is clicked."
	));



  //sidebar
	vc_add_param("vc_widget_sidebar", array(
		"type" => "checkbox",
		"class" => "",
		"heading" => "Make Sticky?",
		"value" => array("Enable" => "true" ),
		"param_name" => "enable_sticky",
		"description" => "This will cause your widgetized sidebar to stick to the screen when used in a column within a row that is taller than the widgetized sidebar."
	));


	//full width section
	require_once vc_path_dir('SHORTCODES_DIR', 'vc-row.php');

	class WPBakeryShortCode_Full_Width_Section extends WPBakeryShortCode_VC_Row {
			

	}

	vc_lean_map('full_width_section', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/full_width_section.php');




	// Video
	vc_remove_param("vc_video", "title");


	// Text block
	//vc_remove_param("vc_column_text", "css_animation");
	//inner row class fix
	vc_remove_param("vc_gallery", "css_animation");
	vc_remove_param("vc_pie", "css_animation");
	vc_remove_param("vc_video", "css_animation");
	vc_remove_param("vc_text_separator", "css_animation");

	// Nectar Slider
	$nectar_disable_nectar_slider = (!empty($nectar_options['disable_nectar_slider_pt']) && $nectar_options['disable_nectar_slider_pt'] == '1') ? true : false; 
	if($nectar_disable_nectar_slider != true) {
		vc_lean_map('nectar_slider', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/nectar_slider.php');
	}

	// Horizontal progress bar shortcode
	vc_lean_map('bar', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/bar.php');
	

	// Split Line Heading
	class WPBakeryShortCode_Split_Line_Heading extends WPBakeryShortCode { }

	vc_lean_map('split_line_heading', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/split_line_heading.php');
	
	
	
	// Split Line Heading
	class WPBakeryShortCode_Nectar_Highlighted_Text extends WPBakeryShortCode { }

	vc_lean_map('nectar_highlighted_text', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/nectar_highlighted_text.php');



	// Divider
	vc_lean_map('divider', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/divider.php');


	// Single image
	vc_lean_map('image_with_animation', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/image_with_animation.php');


	//cascading images
	class WPBakeryShortCode_Nectar_Cascading_Images extends WPBakeryShortCode {}
	
	vc_lean_map('nectar_cascading_images', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/nectar_cascading_images.php');



	// Image Comparision
	class WPBakeryShortCode_Nectar_Image_Comparison extends WPBakeryShortCode {}

	vc_lean_map('nectar_image_comparison', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/nectar_image_comparison.php');


	// Portfolio
	vc_lean_map('nectar_portfolio', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/nectar_portfolio.php');

	vc_lean_map('recent_projects', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/recent_projects.php');


	//Horizontal List Item
	class WPBakeryShortCode_Nectar_Horizontal_List_Item extends WPBakeryShortCode {}
	vc_lean_map('nectar_horizontal_list_item', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/nectar_horizontal_list_item.php');


	// Blog
	vc_lean_map('nectar_blog', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/nectar_blog.php');
		
	vc_lean_map('recent_posts', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/recent_posts.php');


	//WooCommerce Related
	global $woocommerce;

	if($woocommerce) {

		class WPBakeryShortCode_Nectar_Woo_Products extends WPBakeryShortCode {
			
		}

		vc_lean_map('nectar_woo_products', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/nectar_woo_products.php');
		

	}
	
	
	//category grid
	class WPBakeryShortCode_Nectar_Category_Grid extends WPBakeryShortCode {
		
	}
	
	/*helper function*/
	if(!function_exists('nectar_grid_item_markup')) {
		
		function nectar_grid_item_markup($temp_cat_obj_holder,$atts) {
		    
		    $markup = '';
		    
		    if($temp_cat_obj_holder) {
		        $temp_cat_obj_holder->term_id;
		        $temp_cat_obj_holder->name;
		        
		        //grab cat image
		        $bg_style_markup = '';
		        
		        if($atts['post_type'] == 'posts') {
		          $thumbnail_id = get_post_thumbnail_id( $temp_cat_obj_holder->term_id );
		          $terms =  get_option( "taxonomy_$temp_cat_obj_holder->term_id" );
		          $image_bg = $terms['category_thumbnail_image'];
		          if(!empty($image_bg)) {
		            $image_id = fjarrett_get_attachment_id_from_url($image_bg);
		            $image_bg = wp_get_attachment_image_src( $image_id, 'large');
		            
		            $bg_style_markup = (!empty($image_bg)) ? 'style="background-image:url('. $image_bg[0] .');"' : '';
		          }
		        
		          
		        } else if( $atts['post_type'] == 'products') {
		          $thumbnail_id = get_woocommerce_term_meta( $temp_cat_obj_holder->term_id, 'thumbnail_id', true );
		          $image_bg = wp_get_attachment_image_src( $thumbnail_id, 'large');
		          $bg_style_markup = (!empty($image_bg)) ? 'style="background-image:url('. $image_bg[0] .');"' : '';
		          
		        }
		        
		        $bg_overlay_markup = (!empty($atts['color_overlay'])) ? 'style=" background-color: '.$atts['color_overlay'].';"' : '';
		        
		        $markup .= '<div class="nectar-category-grid-item"> <div class="inner"> <a class="nectar-category-grid-link" href="'. get_term_link($temp_cat_obj_holder->term_id) .'"></a>';
		        $markup .= '<div class="nectar-category-grid-item-bg" '.$bg_style_markup.'></div>';
		        $markup .= '<div class="bg-overlay" '.$bg_overlay_markup.' data-opacity="'. $atts['color_overlay_opacity'] .'" data-hover-opacity="'. $atts['color_overlay_hover_opacity'] .'"></div>';
		        $markup .= '<div class="content" data-subtext-vis="'. $atts['subtext_visibility'] .'" data-subtext="'. $atts['subtext'] .'" ><h3>'. $temp_cat_obj_holder->name .'</h3>';
		
		        if($atts['subtext'] == 'cat_item_count') {
		          
		          $subtext_count_markup = '';
		          
		          if($atts['post_type'] == 'posts') {
		            if($temp_cat_obj_holder->count == 1) { $subtext_count_markup = '<span class="subtext">' . $temp_cat_obj_holder->count .  ' ' . esc_html__('post', 'salient') . '</span>'; } 
		            else { $subtext_count_markup = '<span class="subtext">' . $temp_cat_obj_holder->count .  ' ' . esc_html__('posts', 'salient') . '</span>'; }
		          } else if($atts['post_type'] == 'products') {
		            if($temp_cat_obj_holder->count == 1) {  $subtext_count_markup = '<span class="subtext">' . $temp_cat_obj_holder->count .  ' ' . esc_html__('product', 'salient') . '</span>'; } 
		            else { $subtext_count_markup = '<span class="subtext">' . $temp_cat_obj_holder->count .  ' ' . esc_html__('products', 'salient') . '</span>';  }  
		          }
		          
		          $markup .= $subtext_count_markup;
		
		        } else if($atts['subtext'] == 'custom') {
		          $markup .= '<span class="subtext">' . $atts['custom_subtext'] . '</span>';
		        }
		        $markup .= '</div>';
		        $markup .= '</div></div>';
		    }
		    
		    return $markup;
		    
		}
 }

	vc_lean_map('nectar_category_grid', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/nectar_category_grid.php');
	


	// Centered Heading
	vc_lean_map('heading', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/heading.php');

	// Call to action
	class WPBakeryShortCode_Nectar_Cta extends WPBakeryShortCode {}
	vc_lean_map('nectar_cta', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/nectar_cta.php');

	// video lightbox
	class WPBakeryShortCode_Nectar_Video_Lightbox extends WPBakeryShortCode {}
	vc_lean_map('nectar_video_lightbox', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/nectar_video_lightbox.php');


	// Milestone
	vc_lean_map('milestone', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/milestone.php');

	// Google Map
	class WPBakeryShortCode_Nectar_Gmap extends WPBakeryShortCode {
	}
	vc_lean_map('nectar_gmap', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/nectar_gmap.php');

	// Team Member
	vc_lean_map('team_member', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/team_member.php');
		

	// Fancy Box
	class WPBakeryShortCode_Fancy_Box extends WPBakeryShortCode { }
	vc_lean_map('fancy_box', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/fancy_box.php');

	// Flip Box
	class WPBakeryShortCode_Nectar_Flip_Box extends WPBakeryShortCode { }
	vc_lean_map('nectar_flip_box', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/flip-box.php');


	// Gradient Text
	class WPBakeryShortCode_Nectar_Gradient_Text extends WPBakeryShortCode { }
	vc_lean_map('nectar_gradient_text', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/gradient-text.php');
	
	
	// Hotspot
	class WPBakeryShortCode_Nectar_Image_With_Hotspots extends WPBakeryShortCode { }
	vc_lean_map('nectar_image_with_hotspots', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/nectar_image_with_hotspots.php');
		
	
	class WPBakeryShortCode_Nectar_Hotspot extends WPBakeryShortCode { }
	vc_lean_map('nectar_hotspot', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/nectar_hotspot.php');


	// Fancy Title
	class WPBakeryShortCode_Nectar_Animated_Title extends WPBakeryShortCode { }
	vc_lean_map('nectar_animated_title', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/fancy-title.php');
	
	
	// Single Testimonial
	class WPBakeryShortCode_Nectar_Single_Testimonial extends WPBakeryShortCode { }
	vc_lean_map('nectar_single_testimonial', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/nectar_single_testimonial.php');
	

	require_once vc_path_dir('SHORTCODES_DIR', 'vc-accordion.php');
	require_once vc_path_dir('SHORTCODES_DIR', 'vc-accordion-tab.php');

	/* Accordion block
	---------------------------------------------------------- */
	vc_lean_map('toggles', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/toggles.php');
	vc_lean_map('toggle', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/toggle.php');
	



	require_once vc_path_dir('SHORTCODES_DIR', 'vc-tabs.php');

	/* Tabs
	---------------------------------------------------------- */
	vc_lean_map('tabbed_section', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/tabbed_section.php');
	vc_lean_map('tab', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/tab.php');



	class WPBakeryShortCode_Testimonial_Slider extends WPBakeryShortCode_Tabbed_Section { }

	vc_lean_map('testimonial_slider', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/testimonial_slider.php');


	class WPBakeryShortCode_Testimonial extends WPBakeryShortCode {
		
		public function customAdminBlockParams() {
	        return ' id="tab-'.$this->atts['id'] .'"';
	    }
		
	}

	vc_lean_map('testimonial', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/testimonial.php');

	
	//adding back in default VC elements
	$vc_config_path = vc_path_dir( 'CONFIG_DIR' );
	if(version_compare(WPB_VC_VERSION,'5.0','>=')) {
    	vc_lean_map( 'vc_widget_sidebar', null, $vc_config_path . '/structure/shortcode-vc-widget-sidebar.php' );
    }

    vc_lean_map( 'vc_wp_custommenu', null,  $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/shortcode-vc-wp-custommenu.php' );
	

	/* clients slider */
	class WPBakeryShortCode_Clients extends WPBakeryShortCode_Tabbed_Section { }

	vc_lean_map('clients', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/clients.php');

	

	class WPBakeryShortCode_Client extends WPBakeryShortCode {
		
		public function customAdminBlockParams() {
	        return ' id="tab-'.$this->atts['id'] .'"';
	    }
		
	}

	vc_lean_map('client', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/client.php');

	


	/* icon list */
	class WPBakeryShortCode_Nectar_Icon_List extends WPBakeryShortCode_Tabbed_Section { }

	vc_lean_map('nectar_icon_list', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/nectar_icon_list.php');

	

	class WPBakeryShortCode_Nectar_Icon_List_Item extends WPBakeryShortCode {
		
		public function customAdminBlockParams() {
	        return ' id="tab-'.$this->atts['id'] .'"';
	    }
		
	}

	vc_lean_map('nectar_icon_list_item', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/nectar_icon_list_item.php');

	

	/* page sub menu */
	class WPBakeryShortCode_Page_Submenu extends WPBakeryShortCode_Tabbed_Section { }

	vc_lean_map('page_submenu', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/page_submenu.php');

	

	class WPBakeryShortCode_Page_Link extends WPBakeryShortCode {
		
		public function customAdminBlockParams() {
	        return ' id="tab-'.$this->atts['id'] .'"';
	    }
		
	}


	vc_lean_map('page_link', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/page_link.php');

	


	/* pricing table */
	class WPBakeryShortCode_Pricing_Table extends WPBakeryShortCode_Tabbed_Section { }

	vc_lean_map('pricing_table', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/pricing_table.php');

	


	class WPBakeryShortCode_Pricing_Column extends WPBakeryShortCode {
		
		public function customAdminBlockParams() {
	        return ' id="tab-'.$this->atts['id'] .'"';
	    }
		
	}


	vc_lean_map('pricing_column', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/pricing_column.php');



	/* carousel */
	class WPBakeryShortCode_Carousel extends WPBakeryShortCode_Tabbed_Section { }

	vc_lean_map('carousel', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/carousel.php');


	vc_map( array(
	  "name" => esc_html__("Carousel Item", "js_composer"),
	  "base" => "item",
	  "allowed_container_element" => 'vc_row',
	  "is_container" => true,
	  "content_element" => false,
	  "params" => array(
	    array(
	      "type" => "tab_id",
	      "heading" => esc_html__("Tab ID", "js_composer"),
	      "param_name" => "id"
	    )
	  ),
	  'js_view' => ($vc_is_wp_version_3_6_more ? 'VcTabView' : 'VcTabView35')
	));


	// Social Buttons
	vc_lean_map('social_buttons', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/social_buttons.php');



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



	vc_add_param("vc_gallery",array(
	      "type" => "dropdown",
	      "heading" => esc_html__("Gallery type", "js_composer"),
	      "param_name" => "type",
	      "value" => array(
	         esc_html__("Basic Slider Style", "js_composer") => "flexslider_style", 
	         esc_html__("Nectar Slider Style", "js_composer") => "nectarslider_style",
	         esc_html__("Touch Enabled and Spaced (Flickity)", "js_composer") => "flickity_style",
	         esc_html__("Image Grid Style", "js_composer") => "image_grid",
	         esc_html__("Parallax Image Grid", "js_composer") => "parallax_image_grid"
	       ),
	      'save_always' => true,
	      "description" => esc_html__("Select gallery type.", "js_composer")
	));
	vc_add_param("vc_gallery",array(
	      "type" => "dropdown",
	      "heading" => esc_html__("Auto rotate slides", "js_composer"),
	      "param_name" => "interval",
	      "value" => array(3, 5, 10, 15, esc_html__("Disable", "js_composer") => 0),
	      "description" => esc_html__("Auto rotate slides each X seconds.", "js_composer"),
	      'save_always' => true,
	      "dependency" => Array('element' => "type", 'value' => array('flexslider_fade', 'flexslider_slide', 'nivo'))
	));
	vc_add_param("vc_gallery",array(
	      "type" => "attach_images",
	      "heading" => esc_html__("Images", "js_composer"),
	      "param_name" => "images",
	      "value" => "",
	      "description" => esc_html__("Select images from media library.", "js_composer"),
	      "dependency" => Array('element' => "source", 'value' => array('media_library'))
	));
	vc_add_param("vc_gallery",array(
	      "type" => "textfield",
	      "heading" => esc_html__("Image size", "js_composer"),
	      "param_name" => "img_size",
	      "description" => esc_html__("Enter image size in pixels - e.g 600x400 (Width x Height) Or use WordPress image size names such as \"full\"", "js_composer"),
	      "dependency" => Array('element' => "source", 'value' => array('media_library'))
	));

	vc_add_param("vc_gallery",array(
		  "type" => "dropdown",
		  "heading" => esc_html__("Controls", "js_composer"),
		  "param_name" => "flickity_controls",
		  "value" => array(
			    "Pagination" => "pagination",
			    "Material Pagination" => "material_pagination",
			    "Next/Prev Arrows" => "next_prev_arrows",
			    "None" => 'none'
			),
		  'save_always' => true,
		  "description" => esc_html__("Please select the controls you would like for your gallery ", "js_composer"),
		  "dependency" => Array('element' => "type", 'value' => array('flickity_style'))
	));

	vc_add_param("vc_gallery",array(
		  "type" => "dropdown",
		  "heading" => esc_html__("Desktop Columns", "js_composer"),
		  "param_name" => "flickity_desktop_columns",
		  "value" => array(
			    "1" => "1",
			    "2" => "2",
			    "3" => "3",
			    "4" => "4",
			    "5" => "5"
			),
		  'save_always' => true,
		  "description" => '',
		  "dependency" => Array('element' => "type", 'value' => array('flickity_style'))
	));
	vc_add_param("vc_gallery",array(
		  "type" => "dropdown",
		  "heading" => esc_html__("Small Desktop Columns", "js_composer"),
		  "param_name" => "flickity_small_desktop_columns",
		  "value" => array(
			    "1" => "1",
			    "2" => "2",
			    "3" => "3",
			    "4" => "4",
			    "5" => "5"
			),
		  'save_always' => true,
		  "description" => '',
		  "dependency" => Array('element' => "type", 'value' => array('flickity_style'))
	));
	vc_add_param("vc_gallery",array(
		  "type" => "dropdown",
		  "heading" => esc_html__("Tablet Columns", "js_composer"),
		  "param_name" => "flickity_tablet_columns",
		  "value" => array(
			    "1" => "1",
			    "2" => "2",
			    "3" => "3",
			    "4" => "4",
			    "5" => "5"
			),
		  'save_always' => true,
		  "description" => '',
		  "dependency" => Array('element' => "type", 'value' => array('flickity_style'))
	));

	 vc_add_param("vc_gallery",array(
	      "type" => 'checkbox',
	      "heading" => esc_html__("Free Scroll", "js_composer"),
	      "param_name" => "flickity_free_scroll",
	      "description" => esc_html__("Enables content to be freely flicked without aligning cells to an end position.", "js_composer"),
	      "value" => Array(esc_html__("Yes, please", "js_composer") => 'true'),
	      "dependency" => Array('element' => "type", 'value' => array('flickity_style'))
	  ));

	  vc_add_param("vc_gallery",array(
	      "type" => 'checkbox',
	      "heading" => esc_html__("Enable Auto Play", "js_composer"),
	      "param_name" => "flickity_autoplay",
	      "description" => esc_html__("Will cause your images to auto play until user interaction", "js_composer"),
	      "value" => Array(esc_html__("Yes, please", "js_composer") => 'true'),
	      "dependency" => Array('element' => "type", 'value' => array('flickity_style'))
	  ));

	  vc_add_param("vc_gallery",array(
	      "type" => 'textfield',
	      "heading" => esc_html__("Auto Play Duration", "js_composer"),
	      "param_name" => "flickity_autoplay_dur",
	      "description" => esc_html__("Enter a custom duration in milliseconds between auto play advances e.g. 5000", "js_composer"),
	      "dependency" => Array('element' => "type", 'value' => array('flickity_style'))
	  ));

	vc_add_param("vc_gallery",array(
      "type" => "dropdown",
      "heading" => esc_html__("Box Shadow", "js_composer"),
      'save_always' => true,
      "param_name" => "flickity_box_shadow",
      "value" => array(esc_html__("None", "js_composer") => "none", esc_html__("Small Depth", "js_composer") => "small_depth", esc_html__("Medium Depth", "js_composer") => "medium_depth", esc_html__("Large Depth", "js_composer") => "large_depth", esc_html__("Very Large Depth", "js_composer") => "x_large_depth"),
      "description" => esc_html__("Select your desired image box shadow", "js_composer"),
      "dependency" => Array('element' => "type", 'value' => array('flickity_style'))
    ));

	 vc_add_param("vc_gallery",array(
	      "type" => 'checkbox',
	      "heading" => esc_html__("Flexible Slider Height", "js_composer"),
	      "param_name" => "flexible_slider_height",
	      "description" => esc_html__("Would you like the height of your slider to constantly scale in porportion to the screen size?", "js_composer"),
	      "value" => Array(esc_html__("Yes, please", "js_composer") => 'true'),
	      "dependency" => Array('element' => "type", 'value' => array('nectarslider_style'))
	  ));
		vc_add_param("vc_gallery",array(
	      "type" => 'checkbox',
	      "heading" => esc_html__("Disable Autorotate?", "js_composer"),
	      "param_name" => "disable_auto_rotate",
	      "description" => esc_html__("This will stop the slider from automatically rotating.", "js_composer"),
	      "value" => Array(esc_html__("Yes, please", "js_composer") => 'true'),
	      "dependency" => Array('element' => "type", 'value' => array('nectarslider_style'))
	  ));
	  vc_add_param("vc_gallery",array(
	      "type" => 'checkbox',
	      "heading" => esc_html__("Hide Arrow Navigation?", "js_composer"),
	      "param_name" => "hide_arrow_navigation",
	      "description" => esc_html__("Would you like this slider to hide the arrows on the right and left sides?", "js_composer"),
	      "value" => Array(esc_html__("Yes, please", "js_composer") => 'true'),
	      "dependency" => Array('element' => "type", 'value' => array('nectarslider_style'))
	  ));
	  vc_add_param("vc_gallery",array(
	      "type" => 'checkbox',
	      "heading" => esc_html__("Display Bullet Navigation?", "js_composer"),
	      "param_name" => "bullet_navigation",
	      "description" => esc_html__("Would you like this slider to display bullets on the bottom?", "js_composer"),
	      "value" => Array(esc_html__("Yes, please", "js_composer") => 'true'),
	      "dependency" => Array('element' => "type", 'value' => array('nectarslider_style'))
	  ));
	  vc_add_param("vc_gallery",array(
	      "type" => "dropdown",
	      "heading" => esc_html__("Bullet Navigation Style", "js_composer"),
	      "param_name" => "bullet_navigation_style",
	      "value" => array(
				'See Through & Solid On Active' => 'see_through',
				'Solid & Scale On Active' => 'scale',
				'See Through - Autorotate Visualized' => 'see_through_ar_visualized'
	      ),
	      'save_always' => true,
	      "description" => 'Please select your overall bullet navigation style here.',
	      "dependency" => Array('element' => "type", 'value' => array('nectarslider_style'))
	  ));


	vc_add_param("vc_gallery",array(
	      "type" => 'checkbox',
	      "heading" => esc_html__("Display Title/Caption?", "js_composer"),
	      "param_name" => "display_title_caption",
	      "value" => Array(esc_html__("Yes", "js_composer") => 'true'),
	      "dependency" => Array('element' => "type", 'value' => array('image_grid','parallax_image_grid'))
	));

	vc_add_param("vc_gallery",array(
		  "type" => "dropdown",
		  "heading" => esc_html__("Layout", "js_composer"),
		  "param_name" => "layout",
		  "admin_label" => true,
		  "value" => array(
				  "4 Columns" => "4",
			    "3 Columns" => "3",
					"2 Columns" => "2",
			    "Fullwidth" => "fullwidth",
			    "Constrained Fullwidth" => "constrained_fullwidth"
			),
		  'save_always' => true,
		  "description" => esc_html__("Please select the layout you would like for your gallery ", "js_composer"),
		  "dependency" => Array('element' => "type", 'value' => array('image_grid'))
	));
	vc_add_param("vc_gallery",array(
	      "type" => 'checkbox',
	      "heading" => esc_html__("Masonry Style", "js_composer"),
	      "param_name" => "masonry_style",
	      "description" => esc_html__("This will allow your gallery items to display in a masonry layout as opposed to a fixed grid. You can define your desired masonry size for each image when editing/adding them in the right hand side \"Attachment Details\" sidebar. Enabling this will override the \"Image Size\" field above. ", "js_composer"),
	      "value" => Array(esc_html__("Yes, please", "js_composer") => 'true'),
	      "dependency" => Array('element' => "type", 'value' => array('image_grid'))
	));
	
	vc_add_param("vc_gallery",array(
	      "type" => 'checkbox',
	      "heading" => esc_html__("Bypass Image Cropping", "js_composer"),
	      "param_name" => "bypass_image_cropping",
	      "description" => esc_html__("Enabling this will cause your image grid to bypass the default Salient image cropping which varies based on the defined Masonry Sizing field. The result will be a traditional masonry layout rather than a structured grid", "js_composer"),
	      "value" => Array(esc_html__("Yes, please", "js_composer") => 'true'),
	      "dependency" => Array('element' => "masonry_style", 'not_empty' => true)
	));
	
	vc_add_param("vc_gallery",array(
		  "type" => "dropdown",
		  "heading" => esc_html__("Item Spacing", "js_composer"),
		  "param_name" => "item_spacing",
		  'save_always' => true,
			"dependency" => Array('element' => "type", 'value' => array('image_grid')),
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
		  "description" => esc_html__("Please select the spacing you would like between your items. ", "js_composer")
	));
	vc_add_param("vc_gallery",array(
	      "type" => 'checkbox',
	      "heading" => esc_html__("Constrain Max Columns to 4?", "js_composer"),
	      "param_name" => "constrain_max_cols",
	      "description" => esc_html__("This will change the max columns to 4 (default is 5 for fullwidth). Activating this will make it easier to create a grid with no empty spaces at the end of the list on all screen sizes.", "js_composer"),
	      "value" => Array(esc_html__("Yes, please", "js_composer") => 'true'),
	      "dependency" => Array('element' => "layout", 'value' => 'fullwidth')
	));
	vc_add_param("vc_gallery",array(
		  "type" => "dropdown",
		  "heading" => esc_html__("Gallery Style", "js_composer"),
		  "param_name" => "gallery_style",
		  "admin_label" => true,
		  "value" => array(
			    "Meta on hover w/ zoom + entire thumb link" => "7",
			    "Meta overlaid w/ zoom effect on hover" => "3",
			    'Meta overlaid w/ zoom effect on hover alt' => '5',
					"Meta overlaid - bottom left aligned" => "8",
					"Meta on hover + entire thumb link" => "2",
			    "Meta from bottom on hover + entire thumb link" => "4",
					"Meta below thumb w/ links on hover" => "1"
			),
		  'save_always' => true,
		  "description" => esc_html__("Please select the style you would like your gallery to display in ", "js_composer"),
		  "dependency" => Array('element' => "type", 'value' => array('image_grid'))
	));

	vc_add_param("vc_gallery",array(
		  "type" => "dropdown",
		  "heading" => esc_html__("Load In Animation", "js_composer"),
		  "param_name" => "load_in_animation",
		  'save_always' => true,
		  "value" => array(
			    "None" => "none",
			    "Fade In" => "fade_in",
			    "Fade In From Bottom" => "fade_in_from_bottom",
			    "Perspective Fade In" => "perspective"
			),
		  "description" => esc_html__("Please select the style you would like your projects to display in ", "js_composer"),
		  "dependency" => Array('element' => "type", 'value' => array('image_grid'))
	));

	vc_add_param("vc_gallery",array(
	      "type" => "dropdown",
	      "heading" => esc_html__("On click", "js_composer"),
	      "param_name" => "onclick",
	      "value" => array( esc_html__("Do nothing", "js_composer") => "link_no", esc_html__("Open lightbox", "js_composer") => "link_image",  esc_html__("Open custom link", "js_composer") => "custom_link"),
	      "description" => esc_html__("What to do when slide is clicked?", "js_composer"),
	      'save_always' => true,
	      "dependency" => Array('element' => "type", 'value' => array('nectarslider_style', 'flexslider_style', 'flickity_style'))
	));
	vc_add_param("vc_gallery",array(
	      "type" => "exploded_textarea",
	      "heading" => esc_html__("Custom links", "js_composer"),
	      "param_name" => "custom_links",
	      "description" => esc_html__('Enter links for each slide here. Divide links with linebreaks (Enter).', 'js_composer'),
	      "dependency" => Array('element' => "onclick", 'value' => array('custom_link'))
	));

	vc_add_param("vc_gallery",array(
	      "type" => "dropdown",
	      "heading" => esc_html__("Custom link target", "js_composer"),
	      "param_name" => "custom_links_target",
	      "description" => esc_html__('Select where to open  custom links.', 'js_composer'),
	      "dependency" => Array('element' => "onclick", 'value' => array('custom_link')),
	      'save_always' => true,
	      'value' => array(esc_html__("Same window", "js_composer") => "_self", esc_html__("New window", "js_composer") => "_blank")
	));
	vc_add_param("vc_gallery",array(
	      "type" => "textfield",
	      "heading" => esc_html__("Extra class name", "js_composer"),
	      "param_name" => "el_class",
	      "description" => esc_html__("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "js_composer")
	));
	




	// Text With Icon
	vc_lean_map('text-with-icon', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/text-with-icon.php');
	

	// Fancy UL
	vc_lean_map('fancy-ul', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/fancy-ul.php');

	

	
	// Morphing Outline
	class WPBakeryShortCode_Morphing_Outline extends WPBakeryShortCode { }

	vc_lean_map('morphing_outline', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/morphing_outline.php');


	// Nectar Item Price
	class WPBakeryShortCode_Nectar_Food_Menu_Item extends WPBakeryShortCode { }
	vc_lean_map('nectar_food_menu_item', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/nectar_food_menu_item.php');

	

	// Nectar Btn
	class WPBakeryShortCode_Nectar_Btn extends WPBakeryShortCode { }

	vc_lean_map('nectar_btn', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/nectar_btn.php');

	

	//Nectar Icon
	class WPBakeryShortCode_Nectar_Icon extends WPBakeryShortCode { }

	vc_lean_map('nectar_icon', null, $nectar_template_dir . '/nectar/nectar-vc-addons/nectar_maps/nectar_icon.php');

	

	

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