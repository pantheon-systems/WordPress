<?php
/**
 * handling all hooks callbacks in future
 * @since 8.0
 **/

if( ! defined("ABSPATH") ) die("Not Allowed");


// Saving Cropped image when posted from product page.
function ppom_hooks_save_cropped_image( $ppom_fields, $posted_data ) {
	
	$product_id = $posted_data['add-to-cart'];
	// var_dump($product_id);
	$cropped_fields = ppom_has_field_by_type($product_id, 'cropper');
	if( empty($cropped_fields) ) return $ppom_fields;
	
	$cropper_found = array();
	foreach($cropped_fields as $cropper) {
		
		if( isset($ppom_fields['fields'][$cropper['data_name']]) ) {
			
			$cropper_found = $ppom_fields['fields'][$cropper['data_name']];
			foreach($cropper_found as $file_id => $values) {
				
				if( empty($values['cropped']) ) continue;
				
				$image_data = $values['cropped'];
				$file_name	= isset($values['org']) ? $values['org'] : '';
				$file_name	= ppom_file_get_name($file_name, $product_id);
				ppom_save_data_url_to_image($image_data, $file_name);
			}
			// Saving cropped data to image
		}
	}
	
	// ppom_pa($cropper_found); exit;
	
	return $ppom_fields;
}

// Convert option price if currency swithcer found
function ppom_hooks_convert_price( $option_price ) {
	
	if( has_filter('woocs_exchange_value') && !empty($option_price) ) {
		global $WOOCS;
		if($WOOCS->current_currency != $WOOCS->default_currency) {
			$option_price = apply_filters('woocs_exchange_value', $option_price);
		}
	}
	
	return $option_price;
}

// Converting currency back to default currency rates due to WC itself converting these
// Like for cart line total, fixed fee etc.
function ppom_hooks_convert_price_back( $price ) {
	
	if( has_filter('woocs_exchange_value') ) {
		
		global $WOOCS;
		// var_dump($WOOCS->is_multiple_allowed);
		if($WOOCS->current_currency != $WOOCS->default_currency && ! $WOOCS->is_multiple_allowed) {
			
			// ppom conver all prices into current currency, but woocommerce also
			// converts cart prices to current, so have to get our currencies back to default rates
			$set_currencies 	= $WOOCS->get_currencies();
			$current_currency_rate = $set_currencies[$WOOCS->current_currency]['rate'];
			$price	= $WOOCS->back_convert($price, $current_currency_rate);
		}
	}
	
	
	return $price;
}

// Format order value for json encoded string for options
function ppom_hooks_format_order_value( $display_value, $meta, $item ) {
	
	$is_jsone = json_decode($display_value, true);
		
	if( ! $is_jsone instanceof \stdClass && ! is_array($is_jsone) )
		return $display_value;
		
	// checking that is it same json created by PPOM
	if( ! isset($is_jsone[0]['option']) )
		return $display_value;
		
	$option_display_values = array();
	foreach( $is_jsone as $option ) {
		
		$price = isset($option['price']) ? $option['price'] : '';
		
		$options_display = $option['option'];
		
		if( $price != '' ) {
			$options_display .= '('.ppom_price($price).')';
		}
		
		$option_display_values[] = $options_display;
	}
	
	$display_value = implode(",", $option_display_values);
	
	return $display_value;
}

// While rendering fields return attributes for fields
function ppom_hooks_set_attributes($field_meta, $type) {
	
	$ppom_attribtues = array();
	
	$ppom_attribtues['data-errormsg']  = isset($field_meta['error_message']) ? ppom_wpml_translate($field_meta['error_message'], 'PPOM') : null;
	
	switch( $type ) {
	    
	    case 'text':
	        
	        $ppom_attribtues['maxlength'] = isset($field_meta['maxlength']) ? $field_meta['maxlength'] : null;
	        $ppom_attribtues['minlength'] = isset($field_meta['minlength']) ? $field_meta['minlength'] : null;
	        break;
	        
	   case 'textarea':
	        
	        $ppom_attribtues['maxlength'] = isset($field_meta['max_length']) ? $field_meta['max_length'] : null;
	        break;
	        
	        
	   case 'number':
	        
	        $ppom_attribtues['min'] = isset($field_meta['min']) ? $field_meta['min'] : null;
	        $ppom_attribtues['max'] = isset($field_meta['max']) ? $field_meta['max'] : null;
	        $ppom_attribtues['step'] = isset($field_meta['step']) ? $field_meta['step'] : null;
	        break;
	        
	}
	
	return $ppom_attribtues;
}

// enqueu required scripts/css for inputs
function ppom_hooks_load_input_scripts( $product ) {
    
    $product_id = ppom_get_product_id($product);
    $ppom		= new PPOM_Meta( $product_id );
	if( ! $ppom->fields ) return '';
	
    $ppom_meta_settings = $ppom->ppom_settings;
    $ppom_meta_fields = $ppom->fields;
    
    $ppom_inputs        	= array();
    $ppom_conditional_fields= array();
    $croppie_options		= array();
    $ppom_core_scripts  	= array('jquery');
    $show_price_per_unit	= false;
    
    // Font-awesome
    if( ppom_load_fontawesome() ) {
		wp_enqueue_style( 'prefix-font-awesome', '//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css' );
    }
    
    // Price display controller
	wp_enqueue_script( 'ppom-price', PPOM_URL.'/js/ppom-price.js', array('jquery','ppom-inputs'), PPOM_DB_VERSION, true);
		
	// Ajax validation
	if( $ppom->ajax_validation_enabled ) {
		wp_enqueue_script( 'ppom-ajax-validation', PPOM_URL.'/js/ppom-validation.js', array('jquery'), PPOM_DB_VERSION, true);
	}
		
	// ppom_pa($ppom_meta_fields);
	
    foreach($ppom_meta_fields as $field){
		
		
		$type			= $field['type'];
		$title			= ( isset($field['title']) ? $field ['title'] : '');
		$data_name		= ( isset($field['data_name']) ? $field ['data_name'] : $title);
		$data_name		= sanitize_key( $data_name );
		
		// var_dump($field['options']);
		if( isset($field['options']) && $type != 'bulkquantity') {
			$field['options'] = ppom_convert_options_to_key_val($field['options'], $field, $product);
		}
		
		
		$decimal_palces = wc_get_price_decimals();
		
		// Allow other types to be hooked
		$type = apply_filters('ppom_load_input_script_type', $type, $field, $product);
		
		switch( $type ) {
		    
		    case 'text':
		    	if( !empty($field['input_mask']) ) {
                	//Enqueue masking script
			    	$ppom_mask_api = PPOM_URL . '/js/inputmask/jquery.inputmask.bundle.js';
    	        	wp_enqueue_script( 'ppom-inputmask', $ppom_mask_api, array('jquery'), PPOM_VERSION, true);
                }
		    	
            	break;
            	
		    case 'date':
		        if(isset($field['jquery_dp']) && $field['jquery_dp'] == 'on') {
		        	$ppom_core_scripts[] = 'jquery-ui-datepicker';
		        	wp_enqueue_style( 'jqueryui', '//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css');
		        }
		        break;
			case 'daterange':
				
				// Check if value is in GET 
				if( !empty($_GET[$data_name]) ) {
					
					$value = $_GET[$data_name];
					$to_dates = explode(' - ', $value);
					$field['start_date'] = $to_dates[0];
					$field['end_date'] = $to_dates[0];
				}
				
		        wp_enqueue_script( 'moment', '//cdn.jsdelivr.net/momentjs/latest/moment.min.js', array('jquery'), PPOM_VERSION, true);
	            wp_enqueue_script( 'daterangepicker', '//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js', array('jquery'), PPOM_DB_VERSION, true);
	            wp_enqueue_style( 'daterangepicker', '//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css');
		        break;
		        
			case 'color':
				
				// Check if value is in GET 
				if( !empty($_GET[$data_name]) ) {
					
					$field['default_color'] = $_GET[$data_name];
				}
				
				
				$ppom_iris_api = PPOM_URL . '/js/color/Iris/dist/iris.js';
    	        wp_enqueue_script( 'ppom-iris', $ppom_iris_api, array('jquery','jquery-ui-core','jquery-ui-draggable', 'jquery-ui-slider'), PPOM_VERSION, true);
    	    	break;
    	    	
    	    case 'image':
				$ppom_tooltip = PPOM_URL . '/js/image-tooltip.js';
				wp_enqueue_script('ppom-zoom', $ppom_tooltip, array('jquery'), PPOM_VERSION, true);
    	    	break;
    	    	
    	    case 'pricematrix':
    	    	
    	    	if( isset($field['show_slider']) && $field['show_slider'] == 'on' ) {
	    	    	// Adding Bootstrap slider if slider is on
	    	    	$ppom_bs_slider_js = PPOM_URL . '/js/bs-slider/bootstrap-slider.min.js';
	    	    	$ppom_bs_slider_css = PPOM_URL . '/js/bs-slider/bootstrap-slider.min.css';
					wp_enqueue_script('ppom-bs-slider', $ppom_bs_slider_js, array('jquery'), PPOM_VERSION, true);
					wp_enqueue_style('ppom-bs-slider-css', $ppom_bs_slider_css);
    	    	}
    	    	
    	    	if( isset($field['show_price_per_unit']) && $field['show_price_per_unit'] == 'on' ) {
    	    		$show_price_per_unit = true;
    	    	}
    	    	break;
    	    	
    	    case 'palettes':
				
				
    	    	break;
    	    
    	    case 'cropper':
				$ppom_file_inputs[] = $field;
				
				
				$ppom_croppie_api	= PPOM_URL . '/js/croppie/node_modules/croppie/croppie.js';
		    	$ppom_cropper		= PPOM_URL . '/js/croppie/ppom-crop.js';
		    	$ppom_croppie_css	= PPOM_URL . '/js/croppie/node_modules/croppie/croppie.css';
		    
				$ppom_exif			= PPOM_URL . '/js/exif.js';
		        wp_enqueue_style( 'ppom-croppie-css', $ppom_croppie_css);
		        // Croppie options
				$croppie_options[$data_name]	= ppom_get_croppie_options($field);
		        
		        wp_enqueue_script( 'ppom-croppie', $ppom_croppie_api, '', PPOM_VERSION);
		        wp_enqueue_script( 'ppom-exif', $ppom_exif, '', PPOM_VERSION);
		        
		        
		        // wp_enqueue_script( 'ppom-croppie2', $ppom_cropper, array('jquery'), PPOM_VERSION);
    	        
    	        wp_enqueue_script( 'ppom-file-upload', PPOM_URL.'/js/file-upload.js', array('jquery', 'plupload','ppom-price'), PPOM_VERSION, true);
    	    	$plupload_lang = !empty($field['language']) ? $field['language'] : 'en';
    	    	// wp_enqueue_script( 'pluploader-language', PPOM_URL.'/js/plupload-2.1.2/js/i18n/'.$plupload_lang.'.js');
    	    	$file_upload_nonce_action = "ppom_uploading_file_action";
				$ppom_file_vars = array('ajaxurl' => admin_url( 'admin-ajax.php', (is_ssl() ? 'https' : 'http') ),
										'plugin_url' => PPOM_URL,
										'file_upload_path_thumb' => ppom_get_dir_url(true),
										'file_upload_path' => ppom_get_dir_url(),
										'mesage_max_files_limit'	=> __(' files allowed only', "ppom"),
										'file_inputs'		=> $ppom_file_inputs,
										'delete_file_msg'	=> __("Are you sure?", "ppom"),
										'plupload_runtime'	=> (ppom_if_browser_is_ie()) ? 'html5,html4' : 'html5,silverlight,html4,browserplus,gear',
										'croppie_options'	=> $croppie_options,
										'ppom_file_upload_nonce'	=> wp_create_nonce( $file_upload_nonce_action ),
										);
				wp_localize_script( 'ppom-file-upload', 'ppom_file_vars', $ppom_file_vars);
    	    	break;
    	    	
    	    //2- inc/hooks.php replace case 'file'
			case 'file':
    	    	$ppom_file_inputs[] = $field;
    	    	
    	    	$file_upload_pre_scripts = array('jquery', 'plupload','ppom-price');
    	    	// if Aviary Editor is used
				if($ppom_meta_settings -> aviary_api_key != ''){
					
					if(is_ssl()){
						wp_enqueue_script( 'aviary-api', '//dme0ih8comzn4.cloudfront.net/imaging/v3/editor.js');	
					}else{
						wp_enqueue_script( 'aviary-api', '//feather.aviary.com/imaging/v3/editor.js');	
					}
					
					$file_upload_pre_scripts[] = 'aviary-api';
				}
				
				wp_enqueue_script( 'ppom-file-upload', PPOM_URL.'/js/file-upload.js', $file_upload_pre_scripts,  PPOM_VERSION, true);
    	    	$plupload_lang = !empty($field['language']) ? $field['language'] : 'en';
    	    	// wp_enqueue_script( 'pluploader-language', PPOM_URL.'/js/plupload-2.1.2/js/i18n/'.$plupload_lang.'.js');
    	    	$file_upload_nonce_action = "ppom_uploading_file_action";
				$ppom_file_vars = array('ajaxurl' => admin_url( 'admin-ajax.php', (is_ssl() ? 'https' : 'http') ),
										'plugin_url' => PPOM_URL,
										'file_upload_path_thumb' => ppom_get_dir_url(true),
										'file_upload_path' => ppom_get_dir_url(),
										'mesage_max_files_limit'	=> __(' files allowed only', "ppom"),
										'file_inputs'		=> $ppom_file_inputs,
										'delete_file_msg'	=> __("Are you sure?", "ppom"),
										'aviary_api_key'	=> $ppom_meta_settings -> aviary_api_key,
										'plupload_runtime'	=> (ppom_if_browser_is_ie()) ? 'html5,html4' : 'html5,silverlight,html4,browserplus,gear',
										'ppom_file_upload_nonce'	=> wp_create_nonce( $file_upload_nonce_action ));
				wp_localize_script( 'ppom-file-upload', 'ppom_file_vars', $ppom_file_vars);
				
				break;
				
				
				case 'bulkquantity':
					
					$field['options'] = stripslashes($field['options']);
				break;
				
				case 'fixedprice':
					// Fixed price addon has option to control decimnal places
					$decimal_palces = !empty($field['decimal_place']) ? $field['decimal_place'] : wc_get_price_decimals();
					break;
		}
		
			// Conditional fields
			if( isset($field['logic']) && $field['logic'] == 'on' && !empty($field['conditions']) ){
				
				$field_conditions = $field['conditions'];
				
				//WPML Translation
				$condition_rules = $field_conditions['rules'];
				$rule_index = 0;
				foreach($condition_rules as $rule) {
					// ppom_pa($rule);
					$field_conditions['rules'][$rule_index]['element_values'] = ppom_wpml_translate($rule['element_values'], 'PPOM');
					$rule_index++;
				}
				
				$ppom_conditional_fields[$data_name] = $field_conditions;
			}
			
		/**
		 * creating action space to render hooks for more addons
		 **/
		 do_action('ppom_hooks_inputs', $field, $data_name);
		
    	$ppom_inputs[] = $field;
    }
    		
    
    // ppom_pa($ppom_conditional_fields);
    
    
    wp_enqueue_script( 'ppom-inputs', PPOM_URL.'/js/ppom.inputs.js', $ppom_core_scripts, PPOM_DB_VERSION, true);
	$ppom_input_vars = array('ajaxurl' => admin_url( 'admin-ajax.php', (is_ssl() ? 'https' : 'http') ),
							'ppom_inputs'		=> $ppom_inputs,
							'field_meta'		=> $ppom_meta_fields);
	wp_localize_script( 'ppom-inputs', 'ppom_input_vars', $ppom_input_vars);
	
	
	$ppom_input_vars['wc_thousand_sep']	= wc_get_price_thousand_separator();
	$ppom_input_vars['wc_currency_pos']	= get_option( 'woocommerce_currency_pos' );
	$ppom_input_vars['wc_decimal_sep']	= get_option('woocommerce_price_decimal_sep');
	$ppom_input_vars['wc_no_decimal']	= $decimal_palces;
	$ppom_input_vars['wc_product_price']= ppom_get_product_price($product);
	$ppom_input_vars['price_matrix_heading'] = ppom_get_option('ppom_label_discount_price', 'Discount Price');
	$ppom_input_vars['product_base_label'] = ppom_get_option('ppom_label_product_price', 'Product Price');
	$ppom_input_vars['option_total_label'] = ppom_get_option('ppom_label_option_total', 'Option Total');
	$ppom_input_vars['fixed_fee_heading'] = ppom_get_option('ppom_label_fixed_fee', 'Fixed Fee');
	$ppom_input_vars['total_discount_label'] = ppom_get_option('ppom_label_total_discount', 'Total Discount');
	$ppom_input_vars['total_without_fixed_label'] = ppom_get_option('ppom_label_total', 'Total');
	$ppom_input_vars['product_quantity_label'] = __("Product Quantity", "ppom");
	$ppom_input_vars['product_title'] = sprintf(__("%s", "ppom"), $product->get_title());
	$ppom_input_vars['per_unit_label'] = __("unit", "ppom");
	$ppom_input_vars['show_price_per_unit'] = $show_price_per_unit;
	$ppom_input_vars['text_quantity'] = __("Quantity","ppom");
	$ppom_input_vars['show_option_price'] =  $ppom->price_display;
	$ppom_input_vars['is_shortcode'] = is_product() ? 'no' : 'yes';
	$ppom_input_vars['plugin_url'] = PPOM_URL;
	
	$ppom_input_vars = apply_filters('ppom_input_vars', $ppom_input_vars, $product);
	
	wp_localize_script('ppom-price', 'ppom_input_vars', $ppom_input_vars);
	
	// Conditional fields
	if( !empty($ppom_conditional_fields) || apply_filters('ppom_enqueue_conditions_js', false)) {
		$ppom_input_vars['conditions'] = $ppom_conditional_fields;
		
		wp_enqueue_script( 'ppom-conditions', PPOM_URL.'/js/ppom-conditions.js', array('jquery','ppom-inputs'), PPOM_DB_VERSION, true);
		wp_localize_script('ppom-conditions', 'ppom_input_vars', $ppom_input_vars);	
	}
			
}

function ppom_hooks_scripts() {
	
	// wp_enqueue_style('pepom-admin', PPOM_URL.'/css/admin.css');
	/*if( is_cart() ) {
		wp_enqueue_script( 'ppom-cart', PPOM_URL.'/js/ppom-cart.js', array('jquery'), PPOM_VERSION, true);
	}*/
}

function ppom_hooks_input_args($field_setting, $field_meta) {
    
    if($field_setting['type'] == 'date' && $field_meta['jquery_dp'] == 'on') {
        $field_setting['type'] = 'text';
        $field_setting['past_date'] = isset($field_meta['past_date']) ? $field_meta['past_date'] : '';
        $field_setting['no_weekends'] = isset($field_meta['no_weekends']) ? $field_meta['no_weekends'] : '';
    }
    
    // Adding conditional field
    if( isset($field_meta['logic']) && $field_meta['logic'] == 'on' ){
    	$field_setting['conditions'] = $field_meta['conditions'];
    }
    
    // Adding min/max for number input
    if( $field_setting['type'] == 'number' ) {
        $field_setting['min'] = !empty($field_meta['min']) ? $field_meta['min'] : '';
        $field_setting['max'] = !empty($field_meta['max']) ? $field_meta['max'] : '';
    }
    
    
    return $field_setting;
}

function ppom_hooks_checkbox_valided($has_value, $posted_fields, $field) {
	
	if ( $field['type'] != 'checkbox' ) return $has_value;
	
	
	if( (!empty($field['max_checked']) || !empty($field['min_checked'])) && empty($field['required']) ) {
		$has_value = true;
	}
	
	if ( ! $has_value && empty($field['required'])) return $has_value;
	
	$data_name = $field['data_name'];
	$max_checked = isset($posted_fields[$data_name]) ? count($posted_fields[$data_name]) : 0;
	
	
	if ( !empty($field['max_checked']) && $max_checked > intval($field['max_checked']) ) {
		$has_value = false;
	}
	
	if ( !empty($field['min_checked']) && $max_checked < intval($field['min_checked']) ) {
		$has_value = false;
	}
	
		
	return $has_value;
}

function ppom_hooks_color_to_text_type($attr_value, $attr, $args) {
	
	if( $attr == 'type' && $attr_value == 'color' ) {
		$attr_value = 'text';
	}
	
	return $attr_value;
}

function ppom_hooks_show_option_price_pricematrix($show_price, $meta){
	
	if( $meta['type'] == 'pricematrix') {
		$show_price = 'on';
	}
	
	return $show_price;
}

/**
 * registration meta in wmp for translation
 * @since 7.0
 **/
function ppom_hooks_register_wpml( $meta_data ) {
	

	foreach($meta_data as $index => $data) {
		
		// If Dataname is not provided then generate it.
		$data['data_name'] = empty($data['data_name']) ? sanitize_key($data['title']) : $data['data_name'];
		
		// title 
		if( isset($data['title']) ) {
			
			nm_wpml_register($data['title'], 'PPOM');
		}
		
		// description
		if( isset($data['description']) ) {
		
			nm_wpml_register($data['description'], 'PPOM');
		}
		
		// error_message
		if( isset($data['error_message']) ) {
			
			nm_wpml_register($data['error_message'], 'PPOM');
		}
		
		// options (select, radio, checkbox)
		if( isset($data['options']) && is_array($data['options']) ) {
			
			$new_option = array();
			
			// If Option ID is not provided then generate it.
			foreach($data['options'] as $option){
				
				nm_wpml_register($option['option'], 'PPOM');
				
				$option['id']	= ppom_get_option_id($option);
				$new_option[]	= $option;
				
			}
			
			$data['options'] = $new_option;
			
		}
		
		$meta_data[$index] = $data;
		
	}
	
	// ppom_pa($meta_data); exit;
	return $meta_data;
}

function ppom_hooks_input_wrapper_class($input_wrapper_class, $field_meta) {

	if( ! isset($field_meta['logic']) ) {
		$input_wrapper_class .= ' ppom-c-show';
	}
	
	if( isset($field_meta['logic']) && $field_meta['logic'] != 'on' ) 
		return $input_wrapper_class;
	
	$input_wrapper_class .= ' ppom-input-'.$field_meta['id'];
	
	/**
	 * If conditional field then add class
	 * ppom-c-hide: if field need to be hidden with condition
	 * ppom-c-show: if field need to be visilbe with condition
	 * */
	// ppom_pa($field_meta);
	if( isset($field_meta['conditions']) ) {
		if( $field_meta['conditions']['visibility'] == 'Show') {
			$input_wrapper_class .= ' ppom-c-hide';
		} else {
			$input_wrapper_class .= ' ppom-c-show';
		}
	}
	
	return $input_wrapper_class;
}

// Retrun cart fragment
/*function ppom_hooks_get_cart_fragment() {
	
	WC_AJAX::get_refreshed_fragments();
}*/

// Showing PPOM with shortcode
function ppom_hooks_render_shortcode( $atts ) {
	
	if( empty($atts['product']) ) return;
	
	$product_id = $atts['product'];
	$product	= new WC_Product($product_id);
	
	$ppom		= new PPOM_Meta( $product_id );
	if( ! $ppom->fields ) return '';
	
	echo '<div id="ppom-box-'.esc_attr($ppom->meta_id).'" class="ppom-wrapper woocommerce">';
	
	wc_print_notices();
	
	echo '<form class="woocommerce-cart-form" method="post" enctype="multipart/form-data">';

    
    // Loading all required scripts/css for inputs like datepicker, fileupload etc
    ppom_hooks_load_input_scripts( $product );
    
    // main css
    wp_enqueue_style( 'ppom-main', PPOM_URL.'/css/ppom-style.css');
    
    // PPOM Simple Popup files load
    wp_enqueue_style( 'ppom-sm-popup', PPOM_URL.'/css/ppom-simple-popup.css');
    wp_enqueue_script('PPOM-sm-popup', PPOM_URL."/js/ppom-simple-popup.js", array('jquery') );
    

    if ( $ppom->inline_css != '') {
		wp_add_inline_style( 'ppom-main', $ppom->inline_css );
    }
    
        
    $ppom_bs_css = PPOM_URL.'/css/bootstrap/bootstrap.css';
    $ppom_bs_modal_css = PPOM_URL.'/css/bootstrap/bootstrap.modal.css';
    // $ppom_bs_css = '//maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css';
    
    wp_enqueue_style( 'ppom-bootstrap', $ppom_bs_css);
    wp_enqueue_style( 'ppom-bootstrap-modal', $ppom_bs_modal_css);

    // Description Tooltips JS File
    wp_enqueue_script('PPOM-tooltip', PPOM_URL."/scripts/ppom-tooltip.js", array('jquery') );
    
    // If Bootstrap is enabled
    if( ppom_load_bootstrap_css() ) {
        
        $ppom_bs_js_cdn  = '//maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js';
        wp_enqueue_script( 'bootstrap-js', $ppom_bs_js_cdn, array('jquery'));
    }
    
    // ajax validation script
    /*if( $ppom_meta_saved_settings -> productmeta_validation == 'yes'){
    	wp_enqueue_script( 'woopa-ajax-validation', PPOM_URL.'/js/woopa-ajaxvalidation.js', array('jquery'));
    }
    	
    $woopa_vars = array('fields_meta' => stripslashes($ppom_meta_saved_settings -> the_meta),
    					'default_error_message'	=> __('it is a required field.', "ppom"));
    wp_localize_script( 'woopa-ajax-validation', 'woopa_vars', $woopa_vars);*/
    
    $template_vars = array('ppom_settings'  => $ppom->ppom_settings,
    						'product'	=> $product);
    
    ppom_load_template ( 'v10/render-fields.php', $template_vars );
    
    // Price container
	echo '<div id="ppom-price-container"></div>';
	
	echo '<button type="submit" name="add-to-cart" value="230" class="single_add_to_cart_button button alt">Add to cart</button>';
	
	echo '</form>';
	echo '</div>';   // Ends ppom-wrappper
}

function ppom_hooks_convert_option_json_to_string($row, $order) {
    
    $new_row = array();
    foreach($row as $key => $value) {
        
        // Scanning only key prefix products_
        if (strpos($key, 'products_') !== false) {
            
            $row[$key] = $value;
        }
    }
    return $row;
}