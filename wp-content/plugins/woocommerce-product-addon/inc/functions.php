<?php
/*
 * this file contains pluing meta information and then shared
 * between pluging and admin classes
 * * [1]
 */

ppom_direct_access_not_allowed();

function ppom_direct_access_not_allowed() {
    if( ! defined('ABSPATH') ) die('Not Allowed.');
}


function ppom_pa($arr){
	
	echo '<pre>';
	print_r($arr);
	echo '</pre>';
}

// Get field column
function ppom_get_field_colum( $meta ) {
	
	$field_column = '';
	if( empty($meta['width']) ) return 12;
	
	// Check width has old settings
	if( strpos( $meta['width'], '%' ) !== false ) {
		
		$field_column = 12;
	} elseif( intval($meta['width']) > 12 ) {
		$field_column = 12;
	} else {
		$field_column = $meta['width'];
	}
	
	return apply_filters('ppom_field_col', $meta['width'], $meta);
}

function ppom_translation_options( $option ) {
	
	if( !isset($option['option']) ) return $option;
	
	$option['option'] = ppom_wpml_translate($option['option'], 'PPOM');
	return $option;
}

/**
 * some WC functions wrapper
 * */
 

if( !function_exists('ppom_wc_add_notice')){
function ppom_wc_add_notice($string, $type="error"){
 	
 	global $woocommerce;
 	if( version_compare( $woocommerce->version, 2.1, ">=" ) ) {
 		wc_add_notice( $string, $type );
	    // Use new, updated functions
	} else {
	   $woocommerce->add_error ( $string );
	}
 }
}

if( !function_exists('ppom_add_order_item_meta') ){
	
	function ppom_add_order_item_meta($item_id, $key, $val){
		
		wc_add_order_item_meta( $item_id, $key, $val );
	}
}

/**
 * WPML
 * registering and translating strings input by users
 */
if( ! function_exists('nm_wpml_register') ) {
	

	function nm_wpml_register($field_value, $domain) {
		
		if ( ! function_exists ( 'icl_register_string' )) 
			return $field_value;
		
		$field_name = $domain . ' - ' . sanitize_key($field_value);
		//WMPL
	    /**
	     * register strings for translation
	     * source: https://wpml.org/wpml-hook/wpml_register_single_string/
	     */
	     
	     do_action( 'wpml_register_single_string', $domain, $field_name, $field_value );
	     
	    //WMPL
		}
}

if( ! function_exists('ppom_wpml_translate') ) {
	

	function ppom_wpml_translate($field_value, $domain) {
		
		$field_name = $domain . ' - ' . sanitize_key($field_value);
		//WMPL
	    /**
	     * register strings for translation
	     * source: https://wpml.org/wpml-hook/wpml_translate_single_string/
	     */
	    
	    $field_value = stripslashes($field_value);
		return apply_filters('wpml_translate_single_string', $field_value, $domain, $field_name );
		//WMPL
	}
}

/**
 * returning order id 
 * 
 * @since 7.9
 */
if ( ! function_exists('nm_get_order_id') ) {
	function nm_get_order_id( $order ) {
		
		$class_name = get_class ($order);
		if( $class_name != 'WC_Order' ) 
			return $order -> ID;
		
		if ( version_compare( WC_VERSION, '2.7', '<' ) ) {  
		
			// vesion less then 2.7
			return $order -> id;
		} else {
			
			return $order -> get_id();
		}
	}
}

/**
 * returning product id 
 * 
 * @since 7.9
 */

function ppom_get_product_id( $product ) {
		
	$product_id = '';
	if ( version_compare( WOOCOMMERCE_VERSION, '2.7', '<' ) ) {  
	
		// vesion less then 2.7
		$product_id = isset($product->id) ? $product->id : $product->ID;
	} else {
		
		if( is_a($product, 'WC_Product') ) {

			if( $product->get_type() == 'variation' ) {
				$product_id = $product->get_parent_id();
			}else {

				$product_id = $product -> get_id();
			}
		}
	}

	
	// WPML Check, if product is translated
	if( function_exists('icl_object_id') && apply_filters('ppom_use_parent_product_ml', false) ) {
		
		$wpml_default_lang = apply_filters( 'wpml_default_language', NULL );
		$product_id = apply_filters( 'wpml_object_id', $product_id, 'product', false, $wpml_default_lang);
	}
	
	return $product_id;
}

// Get product price after some filters like currency switcher
function ppom_get_product_price( $product ) {
	
	$product_price = $product->get_price();
	
	if( has_filter('woocs_exchange_value') ) {
		global $WOOCS;
		
		if($WOOCS->current_currency != $WOOCS->default_currency ) {
			if($WOOCS->is_multiple_allowed) {
				$product_price = apply_filters('woocs_raw_woocommerce_price', $product_price);
			} else {
				
				$product_price = apply_filters('woocs_exchange_value', $product_price);
			}
		}
	}
	
	return apply_filters('ppom_product_price', $product_price, $product);
}

/**
 * adding cart items to order
 * @since 8.2
 **/
function ppom_make_meta_data( $cart_item, $context="cart" ){
	
	if( ! isset($cart_item['ppom']['fields']) ) return $cart_item;
	
	$ppom_meta_ids = '';	
	// removing id field
	if ( !empty( $cart_item ['ppom'] ['fields']['id'] )) {
		$ppom_meta_ids = $cart_item ['ppom'] ['fields']['id'];
		unset( $cart_item ['ppom'] ['fields']['id']);
	}
	
	$ppom_meta = array(); 
	
	foreach($cart_item['ppom']['fields'] as $key => $value) {
		
		// if no value
		if( $value == '' ) continue;
		$product_id = ppom_get_product_id($cart_item['data']);
		// $cart_item['data'] ->post_type == 'product' ? $cart_item['data']->get_id() : $cart_item['data']->get_parent_id();
		$field_meta = ppom_get_field_meta_by_dataname( $product_id, $key, $ppom_meta_ids);
		
		// If field deleted while it's in cart
		if( empty($field_meta) ) continue;
		
		$field_type = isset($field_meta['type']) ? $field_meta['type'] : '';
		$field_title= isset($field_meta['title']) ? $field_meta['title'] : '';
		
		// third party plugin for different fields types
		$field_type = apply_filters('ppom_make_meta_data_field_type', $field_type, $field_meta);
		
		$meta_data = array();
		
		switch( $field_type ) {
			
			case 'quantities':
				$total_qty = 0;
				$qty_values = array();
				foreach($value as $label => $qty) {
					if( !empty($qty) ) {
						$qty_values[] = "{$label} = {$qty}";
						// $ppom_meta[$label] = $qty;
						$total_qty += $qty;	
					}
				}
				
				if( $total_qty > 0 ) {
					$qty_values[] = __('Total',"ppom").' = '.$total_qty;
					$meta_data = array('name'=>$field_title, 'value'=>implode(",",$qty_values));
					// A placeholder key to handle qunantity display in item meta data under myaccount
				}
				
				$ppom_meta['ppom_has_quantities'] = $total_qty;
				break;
				
			case 'file':
				if( $context == 'order') {
					$uploaded_filenames = array();
					foreach($value as $file_id => $file_uploaded) {
						$uploaded_filenames[] = $file_uploaded['org'];
					}
					$meta_data = array('name'=>$field_title, 'value'=>implode(',',$uploaded_filenames));
				} else {
					$file_thumbs_html = '';
					foreach($value as $file_id => $file_uploaded) {
						$file_name = $file_uploaded['org'];
						$file_thumbs_html .= ppom_create_thumb_for_meta($file_name, $product_id);
					}
					// $ppom_meta['ppom_has_files'][$key] = $value;
					$meta_data = array('name'=>$field_title, 'value'=>$file_thumbs_html);
					// $ppom_meta[$field_title] = $file_thumbs_html;
				}
				break;
				
			case 'cropper':
				if( $context == 'order') {
					$uploaded_filenames = array();
					foreach($value as $file_id => $file_cropped) {
						$uploaded_filenames[] = $file_cropped['org'];
					}
					$meta_data = array('name'=>$field_title, 'value'=>implode(',',$uploaded_filenames));
				} else {
					$file_thumbs_html = '';
					// ppom_pa($value);
					foreach($value as $file_id => $file_cropped) {
						
						$file_name = $file_cropped['org'];
						$file_thumbs_html .= ppom_create_thumb_for_meta($file_name, $product_id, true);
					}
					$meta_data = array('name'=>$field_title, 'value'=>$file_thumbs_html);
				}
				break;
				
			case 'image':
				if($value) {
					
					$meta_data = array('name'=>$field_title, 'value'=>ppom_generate_html_for_images($value));
				}
				break;
				
			case 'audio':
				if($value) {
					$ppom_file_count = 1;
					foreach($value as $id => $audio_meta) {
						$audio_meta = json_decode(stripslashes($audio_meta), true);
						$audio_url	= stripslashes($audio_meta['link']);
						$audio_html = '<a href="'.esc_url($audio_url).'" title="'.esc_attr($audio_meta['title']).'">'.$audio_meta['title'].'</a>';
						$meta_lable	= $field_title.': '.$ppom_file_count++;
						// $ppom_meta[$meta_lable] = $audio_html;
						$meta_data = array('name'=>$meta_lable, 'value'=>$audio_html);
					}
				}
				break;
				
			case 'bulkquantity':
				
				$bq_value = $value['option'].' ('.$value['qty'].')';
				// $ppom_meta[$key] = $value['option'].' ('.$value['qty'].')';
				$meta_data = array('name'=>$key, 'value'=>$bq_value);
				// A placeholder key to handle qunantity display in item meta data under myaccount
				$ppom_meta['ppom_has_quantities'] = array('name'=>$key, 'value'=>$value['qty'],'hidden'=>true);
				break;
				
			// NOTE: We have DISABLE this due to REST API values
			case 'checkbox':
				
				$option_posted = $value;
				
				if( is_array($option_posted) ) {
					
					$option_posted = array_map('stripslashes', $option_posted);
				}
				
				$option_label_array = array();
				$options_data_array = array();
				
				$product = new WC_Product($product_id);
				$options_filter	 = ppom_convert_options_to_key_val($field_meta['options'], $field_meta, $product);
				
				foreach($option_posted as $posted_value) {
					foreach($options_filter as $option_key => $option) {
	                    
	                    $option_value = stripslashes($option['raw']);
	                    
	                    if(  $posted_value == $option_value ) {
	                        $option_label_array[] = $option['label'];
	                        $options_data_array[] = array('option'=>$option['raw'],'price'=>$option['price'],'id'=>$option['option_id']);
	                    }
	                }
				}
				
				if( $context == 'api' ) {
					$meta_data = array('name'		=>$field_title, 
										'value'		=> json_encode($options_data_array),
										'display'	=> implode(',',$option_label_array), 
										);
				} else {
					
					$meta_data = array('name'=>$field_title, 'value'=> implode(',',$option_label_array));
				}
				break;
				
			case 'select':
			case 'radio':
				
				$posted_value = stripslashes($value);
				
				$option_price	= '';
				$option_data	= array();
				
				$product = new WC_Product($product_id);
				$options_filter	 = ppom_convert_options_to_key_val($field_meta['options'], $field_meta, $product);
				
				foreach($options_filter as $option_key => $option) {
	                    
                    $option_value = stripslashes($option['raw']);
                    
                    if(  $posted_value == $option_value ) {
                        $option_price = $option['label'];
                        $option_data[] = array('option'=>$option['raw'],'price'=>$option['price'],'id'=>$option['option_id']);
                        break;
                    }
                }
				
				if( $context == 'api' ) {
					$meta_data = array('name'		=> $field_title, 
										'value'		=> json_encode($option_data),
										'display'	=> $option_price, 
										);
				} else {
					$meta_data = array('name'		=> $field_title, 
										'value'		=> $option_price,
										);
				}
				break;
				
			case 'measure':
				
				// if units are set for this
				if( !empty ($cart_item['ppom']['unit'][$key]) ) {
					$field_title .= ' ('.$cart_item['ppom']['unit'][$key].')';
				}
				$meta_data = array('name' => $field_title, 'value' => $value);
				
				break;
				
			default:
				$value = is_array($value) ? implode(",", $value) : $value;
				// $ppom_meta[$field_title] = stripcslashes($value);
				$meta_data = array('name'=>$field_title, 'value'=>stripcslashes($value));
				break;
		}
		
		// Getting option price if field have
		$option_price = ppom_get_field_option_price( $field_meta, $value );
		if( $option_price != 0 ) {
			$meta_data['price'] = $option_price;
		}
		
		$meta_data_field = apply_filters('ppom_meta_data_field', $meta_data, $key, $field_meta, $product_id);
		$ppom_meta[$key] = $meta_data_field;
	}
	
	
	// ppom_pa($ppom_meta);
	return apply_filters('ppom_meta_data', $ppom_meta, $cart_item, $context);
}

/**
* hiding prices for variable product
* only when priced options are used
* 
* @since 8.2
**/
function ppom_meta_priced_options( $the_meta ) {
	
	$has_priced_option = false;
	foreach ( $the_meta as $key => $meta ) {
	
		$options		= ( isset($meta['options'] ) ? $meta['options'] : array());
		foreach($options as $opt)
		{
				
			if( isset($opt['price']) && $opt['price'] != '') {
				$has_priced_option = true;
			}
		}
	}
	
	return apply_filters('ppom_meta_priced_options', $has_priced_option, $the_meta);
}

/**
 * check if browser is IE
 **/
function ppom_if_browser_is_ie()
{
	//print_r($_SERVER['HTTP_USER_AGENT']);
	
	if(!(isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))){
		return false;
	}else{
		return true;
	}
}

// parsing viary tools to array notation
function ppom_get_editing_tools( $editing_tools ){

	parse_str ( $editing_tools, $tools );
	if (isset( $tools['editing_tools'] ) && $tools['editing_tools'])
		return implode(',', $tools['editing_tools']);
}

/**
 * Check if selected meta as input type included
 * return input: data_name
 * 
 **/
function ppom_has_posted_field_value( $posted_fields, $field ) {
	
	$has_value	= false;
	
	$data_name	= $field['data_name'];
	$type		= $field['type'] ;
	
	if( !empty($posted_fields) ) {
		foreach($posted_fields as $field_key => $value){
			
			
			if( $field_key == $data_name) {
				
				
				switch( $type ) {
					
					case 'quantities':
						$quantities_field = $value;
						$quantity = 0;
						foreach($quantities_field as $option => $qty) {
							$quantity += intval($qty);
						}
						
						if( $quantity > 0 ) {
							$has_value = true;
						}
						
					break;

					case 'fonts':

						if (isset($value['font']) && $value['font'] != '') {
							$has_value = true;
						}
						
					break;
					
					default:
						if( $value != '' ) {
							$has_value = true;
						}
					break;
						
				}
				
				
				if( $has_value ) break;
			}
		}
	}
	
	return apply_filters('ppom_has_posted_field_value', $has_value, $posted_fields, $field);
}

function ppom_is_aviary_installed() {

	if( is_plugin_active('nm-aviary-photo-editing-addon/index.php') ){
		return true;
	}else{
		return false;
	}
	
}

// Return ammount after apply percent
function ppom_get_amount_after_percentage($base_amount, $percent) {
	
	$base_amount = floatval($base_amount);
	$percent_amount = 0;
	$percent		= substr( $percent, 0, -1 );
	$percent_amount	= wc_format_decimal( (floatval($percent) / 100) * $base_amount, wc_get_price_decimals());
	
	return $percent_amount;
}

function ppom_settings_link($links) {
	
	$quote_url = "https://najeebmedia.com/get-quote/";
	$ppom_setting_url = admin_url( 'admin.php?page=ppom');
	$video_url = 'https://najeebmedia.com/wordpress-plugin/woocommerce-personalized-product-option/#ppom-quick-video';
	
	$ppom_links = array();
	$ppom_links[] = sprintf(__('<a href="%s">Add Fields</a>', "ppom"), esc_url($ppom_setting_url) );
	$ppom_links[] = sprintf(__('<a href="%s" target="_blank">Quick Video Guide</a>', "ppom"), esc_url($video_url) );
	$ppom_links[] = sprintf(__('<a href="%s">Customized Solution</a>', "ppom"), esc_url($quote_url) );
	
	foreach($ppom_links as $link) {
		
  		array_push( $links, $link );
	}
	
  	return $links;
}

// Get field type by data_name
function ppom_get_field_meta_by_dataname( $product_id, $data_name, $ppom_id=null ) {
	
	$ppom		= new PPOM_Meta( $product_id );
	
	$ppom_fields= $ppom->fields;
	
	if( !empty($ppom_id) ) {
		$ppom_fields = $ppom->get_fields_by_id($ppom_id);
	}
	
	if( ! $ppom_fields ) return '';
	
	$data_name = apply_filters('ppom_get_field_by_dataname_dataname', $data_name, $ppom_id);
	
	$field_meta = '';
	foreach($ppom_fields as $field) {
	
		if( ! ppom_is_field_visible($field) ) continue;
		
		if( !empty($field['data_name']) && sanitize_key($field['data_name']) == $data_name) {
			$field_meta = $field;
			break;
		}
	}
	
	return $field_meta;
}

// Is PPOM meta has field of specific type
function ppom_has_field_by_type( $product_id, $field_type ) {
	
	$ppom		= new PPOM_Meta( $product_id );
	if( ! $ppom->fields ) return '';
	
	$fields_found = array();
	foreach($ppom->fields as $field) {
		
		if( !empty($field['type']) && $field['type'] == $field_type ) {
			$fields_found[] = $field;
		}
	}
	
	return $fields_found;
}

function ppom_load_template($file_name, $variables=array('')){

	if( is_array($variables))
    extract( $variables );
    
   $file_path =  PPOM_PATH . '/templates/'.$file_name;
   if( file_exists($file_path))
   	include ($file_path);
   else
   	die('File not found'.$file_path);
}

// load file from full given path
function ppom_load_file($file_path, $variables=array('')){

	if( is_array($variables))
    extract( $variables );
    
   if( file_exists($file_path))
   	include ($file_path);
   else
   	die('File not found'.$file_path);
}

function ppom_load_bootstrap_css() {
	
	$return = true;
	if( ppom_get_option('ppom_disable_bootstrap') == 'Yes' ) $return = false;
	
	return apply_filters('ppom_bootstrap_css', $return);
}

function ppom_load_fontawesome() {
	
	$return = true;
	if( ppom_get_option('ppom_disable_fontawesome') == 'Yes' ) $return = false;
	
	return apply_filters('ppom_disable_fontawesome', $return);
}


function ppom_convert_options_to_key_val($options, $meta, $product) {
	
	if( empty($options) ) return $options;
	
	// Do not change options for cropper
	// if( $meta['type'] == 'cropper' ) return $options;
	
	// ppom_pa($options);
	
	$ppom_new_option = array();
	foreach($options as $option) {
		
		if( isset($option['option']) ) {
			
			$option = ppom_translation_options($option);
			
			$option_price_without_tax	= '';
			$option_label	= $option['option'];
			$option_percent = '';
			
			$show_price		= isset($meta['show_price']) ? $meta['show_price'] : '';
			$data_name		= isset($meta['data_name']) ? $meta['data_name'] : '';
			
			$option_price	= isset($option['price']) ? $option['price'] : '';
			// This filter change prices for Currency switcher
			$option_price	= apply_filters('ppom_option_price', $option_price);
			
			// Price matrix discount
			$discount	= isset($meta['discount']) && $meta['discount'] == 'on' ? true : false;
			$discount_type	= isset($meta['discount_type']) ? $meta['discount_type'] : 'base';
			
			// $show_option_price = apply_filters('ppom_show_option_price', $show_price, $meta);
			if( !empty($option_price) ) {
				
				// $option_price = $option['price'];
				
				// check if price in percent
				if(strpos($option_price,'%') !== false){
					$option_price = ppom_get_amount_after_percentage($product->get_price(), $option_price);
					// check if price is fixed and taxable
					if(isset($meta['onetime']) && $meta['onetime'] == 'on' && isset($meta['onetime_taxable']) && $meta['onetime_taxable'] == 'on') {
						$option_price_without_tax = $option_price;
						$option_price = ppom_get_price_including_tax($option_price, $product);
					}
					
					$option_label = $option['option'] . ' ('.ppom_price($option_price).')';
					$option_percent = $option['price'];
				} else {
					
					// check if price is fixed and taxable
					if(isset($meta['onetime']) && $meta['onetime'] == 'on' && isset($meta['onetime_taxable']) && $meta['onetime_taxable'] == 'on') {
						$option_price_without_tax = $option_price;
						$option_price = ppom_get_price_including_tax($option_price, $product);
					}
					$option_label = $option['option'] . ' ('.ppom_price($option_price).')';
				}
				
			}
			
			$the_option = stripslashes($option['option']);
			
			$option_id = ppom_get_option_id($option, $data_name);
			
			$ppom_new_option[$the_option] = array('label'	=> apply_filters('ppom_option_label', stripcslashes($option_label), $option, $meta, $product), 
													'price'	=> $option_price,
													'raw'	=> $the_option,
													'without_tax'=>$option_price_without_tax,
													'percent'=> $option_percent,
													'option_id' => $option_id);
														
			if( $discount ) {
				$ppom_new_option[$the_option]['discount'] = $discount_type;
			}
			
			if( $meta['type'] == 'cropper' ) {
				
				$ppom_new_option[$the_option]['width'] = isset($option['width']) ? $option['width'] : '';
				$ppom_new_option[$the_option]['height'] = isset($option['height']) ? $option['height'] : '';
			}
			
		}
	}
	
	if( !empty($meta['first_option']) ) {
		$ppom_new_option[''] = array('label'=>sprintf(__("%s","ppom"),$meta['first_option']), 
										'price'	=> '',
										'raw'	=> '',
										'without_tax' => '');
	}
	
	// ppom_pa($ppom_new_option);
	return apply_filters('ppom_options_after_changes', $ppom_new_option, $options, $meta, $product);
}


// Retrun unique option ID
function ppom_get_option_id($option, $data_name=null) {
	
	$default_option = is_null($data_name) ? $option['option'] : $data_name.'_'.$option['option'];
	
	$option_id = empty($option['id']) ? $default_option : $option['id'];

	return apply_filters('ppom_option_id', sanitize_key( $option_id ), $option, $data_name );
}

function ppom_get_price_including_tax( $price, $product ) {
	
	if(  'incl' !== get_option( 'woocommerce_tax_display_shop' ) ) return $price;
	
	$line_price   = $price;
	$return_price = $line_price;

	$tax_rates    = WC_Tax::get_rates( $product->get_tax_class() );
	$taxes        = WC_Tax::calc_tax( $line_price, $tax_rates, false );
	$tax_amount   = WC_Tax::get_tax_total( $taxes );
	$return_price = round( $line_price + $tax_amount, wc_get_price_decimals() );
	return $return_price;
	
	if ( $product->is_taxable() ) {
		if ( ! wc_prices_include_tax() ) {
			$tax_rates    = WC_Tax::get_rates( $product->get_tax_class() );
			$taxes        = WC_Tax::calc_tax( $line_price, $tax_rates, false );
			$tax_amount   = WC_Tax::get_tax_total( $taxes );
			$return_price = round( $line_price + $tax_amount, wc_get_price_decimals() );
		} else {
			$tax_rates      = WC_Tax::get_rates( $product->get_tax_class() );
			$base_tax_rates = WC_Tax::get_base_tax_rates( $product->get_tax_class( 'unfiltered' ) );

			/**
			 * If the customer is excempt from VAT, remove the taxes here.
			 * Either remove the base or the user taxes depending on woocommerce_adjust_non_base_location_prices setting.
			 */
			if ( ! empty( WC()->customer ) && WC()->customer->get_is_vat_exempt() ) {
				$remove_taxes = apply_filters( 'woocommerce_adjust_non_base_location_prices', true ) ? WC_Tax::calc_tax( $line_price, $base_tax_rates, true ) : WC_Tax::calc_tax( $line_price, $tax_rates, true );
				$remove_tax   = array_sum( $remove_taxes );
				$return_price = round( $line_price - $remove_tax, wc_get_price_decimals() );

			/**
			 * The woocommerce_adjust_non_base_location_prices filter can stop base taxes being taken off when dealing with out of base locations.
			 * e.g. If a product costs 10 including tax, all users will pay 10 regardless of location and taxes.
			 * This feature is experimental @since 2.4.7 and may change in the future. Use at your risk.
			 */
			} elseif ( $tax_rates !== $base_tax_rates && apply_filters( 'woocommerce_adjust_non_base_location_prices', true ) ) {
				$base_taxes   = WC_Tax::calc_tax( $line_price, $base_tax_rates, true );
				$modded_taxes = WC_Tax::calc_tax( $line_price - array_sum( $base_taxes ), $tax_rates, false );
				$return_price = round( $line_price - array_sum( $base_taxes ) + wc_round_tax_total( array_sum( $modded_taxes ), wc_get_price_decimals() ), wc_get_price_decimals() );
			}
		}
	}
	return apply_filters( 'ppom_get_price_including_tax', $return_price, $product);
}

// Check if field conditionally hidden
function ppom_is_field_hidden_by_condition( $field_name ) {
	
	if( !isset($_POST['ppom']['conditionally_hidden']) ) return false;
	
	$ppom_is_hidden = false;
	
	$ppom_hidden_fields = explode(",", $_POST['ppom']['conditionally_hidden']);
	// Remove duplicates
	$ppom_hidden_fields = array_unique( $ppom_hidden_fields );
	
	if( in_array($field_name, $ppom_hidden_fields) ) {
		
		$ppom_is_hidden = true;
	}
	
	return apply_filters('ppom_is_field_hidden_by_condition', $ppom_is_hidden);
}

// Get cart item max quantity for matrix
function ppom_get_cart_item_max_quantity( $cart_item ) {
	
	$max_quantity = null;
	if( isset($cart_item['ppom']['ppom_pricematrix']) ) {
		$matrix 	= json_decode( stripslashes($cart_item['ppom']['ppom_pricematrix']) );
		$last_range = end($matrix);
		$qty_ranges = explode('-', $last_range->raw);
		$max_quantity	= $qty_ranges[1];
	}
	
	return $max_quantity;
}

// Extract relevant matrix from Matrix Range given by quantity
function ppom_extract_matrix_by_quantity($quantities_field, $product, $quantity) {
	
	$matrix = '';
	if( !isset($quantities_field['options']) ) return $matrix; 
	
	$options	= $quantities_field['options'];
	$ranges	 = ppom_convert_options_to_key_val($options, $quantities_field, $product);
	
	if( empty($ranges) ) return $matrix;
	
	foreach ($ranges as $range => $data) {
		
		$range_array	= explode('-', $range);
		$range_start	= $range_array[0];
		$range_end		= $range_array[1];
		
		$quantity = intval($quantity);
		if( $quantity >= $range_start && $quantity <= $range_end ) {
			$matrix = $data;
			break;
		}
	}
	
	return $matrix;
}

// Return thumbs size
function ppom_get_thumbs_size() {
	
	return apply_filters('ppom_thumbs_size', '75px');
}

// Return file size in kb
function ppom_get_filesize_in_kb( $file_name ) {
		
	$base_dir = ppom_get_dir_path();
	$file_path = $base_dir . 'confirmed/' . $file_name;
	
	if (file_exists($file_path)) {
		$size = filesize ( $file_path );
		return round ( $size / 1024, 2 ) . ' KB';
	}elseif(file_exists( $base_dir . '/' . $file_name ) ){
		$size = filesize ( $base_dir . '/' . $file_name );
		return round ( $size / 1024, 2 ) . ' KB';
	}
	
}


// Generating html for file input and cropper in order meta from filename
function ppom_generate_html_for_files( $file_names, $input_type, $item ) {
	
	$file_name_array = explode(',', $file_names);
	
	$order_html = '<table>';
	foreach($file_name_array as $file_name) {
		
			$file_edit_path = ppom_get_dir_path('edits').ppom_file_get_name($file_name, $item->get_product_id());
			
			// Making file thumb download with new path
			$ppom_file_url = ppom_get_file_download_url( $file_name, $item->get_order_id(), $item->get_product_id());
			$ppom_file_thumb_url = ppom_is_file_image($file_name) ? ppom_get_dir_url(true) . $file_name : PPOM_URL.'/images/file.png';
			$order_html .= '<tr><td><a href="'.esc_url($ppom_file_url).'">';
			$order_html .= '<img class="img-thumbnail" style="width:'.esc_attr(ppom_get_thumbs_size()).'" src="'.esc_url($ppom_file_thumb_url).'">';
			$order_html .= '</a></td>';
			
			
			// Requested by Kevin, hiding downloading file button after order on thank you page
			// @since version 16.6
			if( is_admin() ) {
				$order_html .= '<td><a class="button" href="'.esc_url($ppom_file_url).'">';
				$order_html .= __('Download File', "ppom");
				$order_html .= '</a></td>';
			}
			$order_html .= '</tr>';
			
			if( $input_type == 'cropper' ) {
				
					$cropped_file_name = ppom_file_get_name($file_name, $item->get_product_id());
					$cropped_url = ppom_get_dir_url() . 'cropped/' . $cropped_file_name;
					$order_html .= '<tr><td><a href="'.esc_url($cropped_url).'">';
					$order_html .= '<img style="width:'.esc_attr(ppom_get_thumbs_size()).'" class="img-thumbnail" src="'.esc_url($cropped_url).'">';
					$order_html .= '</a></td>';
					
					// Requested by Kevin, hiding downloading file button after order on thank you page
					// @since version 16.6
					if( is_admin() ) {
						$order_html .= '<td><a class="button" href="'.esc_url($cropped_url).'">';
						$order_html .= __('Cropped', "ppom");
						$order_html .= '</a></td>';
					}
					$order_html .= '</tr>';
					
			} elseif( file_exists($file_edit_path) ) {
				
				$edit_file_name = ppom_file_get_name($file_name, $item->get_product_id());
				$edit_url = ppom_get_dir_url() . 'edits/' . $edit_file_name;
				$edit_thumb_url = ppom_get_dir_url() . 'edits/thumbs/' . $file_name;
				$order_html .= '<tr><td><a href="'.esc_url($edit_url).'">';
				$order_html .= '<img style="width:'.esc_attr(ppom_get_thumbs_size()).'" class="img-thumbnail" src="'.esc_url($edit_thumb_url).'">';
				$order_html .= '</a></td>';
				$order_html .= '<td><a class="button" href="'.esc_url($edit_url).'">';
				$order_html .= __('Edited', "ppom");
				$order_html .= '</a></td></tr>';
			}
	}
	$order_html .= '</table>';
	
	return apply_filters('ppom_order_files_html', $order_html, $file_names, $input_type, $item);
}


// return html for images selected
function ppom_generate_html_for_images( $images ) {
	
	
	$ppom_html	=  '<table class="table table-bordered">';
	foreach($images as $id => $images_meta) {
		
		$images_meta	= json_decode(stripslashes($images_meta), true);
		$image_url		= stripslashes($images_meta['link']);
		$image_label	= $images_meta['title'];
		$image_html 	= '<img class="img-thumbnail" style="width:'.esc_attr(ppom_get_thumbs_size()).'" src="'.esc_url($image_url).'" title="'.esc_attr($image_label).'">';
		
		$ppom_html	.= '<tr><td><a href="'.esc_url($image_url).'" class="lightbox" itemprop="image" title="'.esc_attr($image_label).'">' . $image_html . '</a></td>';
		$ppom_html	.= '<td>' .esc_attr(ppom_files_trim_name( $image_label )) . '</td>';
		$ppom_html	.= '</tr>';
		
	}
	
	$ppom_html .= '</table>';
	
	return apply_filters('ppom_images_html', $ppom_html);
}

// Getting field option price
function ppom_get_field_option_price( $field_meta, $option_label ) {
	
	// var_dump($field_meta['options']);
	if( ! isset( $field_meta['options']) || $field_meta['type'] == 'bulkquantity' || $field_meta['type'] == 'cropper' ) return 0;
	
	$option_price = 0;
	foreach( $field_meta['options'] as $option ) {
		
		if( $option['option'] == $option_label && isset($option['price']) && $option['price'] != '' ) {
			
			$option_price = $option['price'];
		}
	}
	
	// For currency switcher
	$option_price = apply_filters('ppom_option_price', $option_price);
	
	return apply_filters("ppom_field_option_price", wc_format_decimal($option_price), $field_meta, $option_label);
}

// Getting field option price by ID
function ppom_get_field_option_price_by_id( $option, $product, $ppom_meta_ids ) {
	
	$data_name = isset($option['data_name']) ? $option['data_name'] : '';
	$option_id = isset($option['option_id']) ? $option['option_id'] : '';
	
	// soon we will remove this product param
	$product_id = null;
	$field_meta = ppom_get_field_meta_by_dataname($product_id , $data_name, $ppom_meta_ids );
	
	if( empty($field_meta) ) return 0;
	
	$field_type = isset($field_meta['type']) ? $field_meta['type'] : '';
	
	if( $field_type == 'bulkquantity' || $field_type == 'cropper' ) return 0;
	
	$option_price = 0;
	
	switch( $field_type ) {
		
		case 'image':
			
			if( isset( $field_meta['images']) ) {
				foreach( $field_meta['images'] as $option ) {
				
					$image_id	= $field_meta['data_name'].'-'.$option['id'];
					if( $image_id == $option_id && isset($option['price']) && $option['price'] != '' ) {
						
						if(strpos($option['price'],'%') !== false){
								$option_price = ppom_get_amount_after_percentage($product->get_price(), $option['price']);
						}else {
							// For currency switcher
							$option_price = apply_filters('ppom_option_price', $option['price']);
						}
					}
				}
			}
		break;
		
		default:
			
			if( isset( $field_meta['options']) ) {
				foreach( $field_meta['options'] as $option ) {
			
					if( $option['id'] == $option_id && isset($option['price']) && $option['price'] != '' ) {
						
						if(strpos($option['price'],'%') !== false){
								$option_price = ppom_get_amount_after_percentage($product->get_price(), $option['price']);
						}else {
							// For currency switcher
							$option_price = apply_filters('ppom_option_price', $option['price']);
						}
					}
				}
			}
		break;
	}
	
	
	return apply_filters("ppom_field_option_price_by_id", wc_format_decimal($option_price), $field_meta, $option_id, $product);
}

// Getting field option weight by ID
function ppom_get_field_option_weight_by_id( $option, $ppom_meta_ids ) {
	
	$data_name = isset($option['data_name']) ? $option['data_name'] : '';
	$option_id = isset($option['option_id']) ? $option['option_id'] : '';
	
	// soon we will remove this product param
	$product_id = null;
	$field_meta = ppom_get_field_meta_by_dataname( $product_id, $data_name, $ppom_meta_ids );
	
	if( empty($field_meta) ) return 0;
	
	if( ! isset( $field_meta['options']) || $field_meta['type'] == 'bulkquantity' || $field_meta['type'] == 'cropper' ) return 0;
	
	$option_weight = 0;
	foreach( $field_meta['options'] as $option ) {
		
		if( $option['id'] == $option_id && isset($option['weight']) && $option['weight'] != '' ) {
			
			$option_weight = $option['weight'];
		}
	}
	
	return apply_filters("ppom_field_option_weight_by_id", wc_format_decimal($option_weight), $field_meta, $option_id, $ppom_meta_ids);
}

// check if PPOM PRO is installed
function ppom_pro_is_installed() {
	
	$return = false;
	    
    if( class_exists('PPOM_PRO') ) 
        $return = true;
    return $return;
}

// Check if PPOM API is enable
function ppom_is_api_enable() {
        
    $api_enable = get_option( 'ppom_api_enable' );
    $api_key    = get_option( 'ppom_rest_secret_key' );
    
    $return = false;
    
    if( $api_enable == 'yes' && $api_key != '' ) {
        $return = true;
    }
    
    return $return;
}

// Check if field is visible
function ppom_is_field_visible( $field ) {
	
	if( ! ppom_pro_is_installed() ) return true;
	
	$visibility = isset($field['visibility']) ? $field['visibility'] : 'everyone';
	$visibility_role = isset($field['visibility_role']) ? $field['visibility_role'] : '';
	
	$is_visible = false;
	switch( $visibility ) {
		
		case 'everyone':
			$is_visible = true;
			break;
			
		case 'members':
			if( is_user_logged_in() ) {
				$is_visible = true;
			}
			break;
			
		case 'guests':
			if( ! is_user_logged_in() ) {
				$is_visible = true;
			}
			break;
			
		case 'roles':
			$role = ppom_get_current_user_role();
			$allowed_roles = explode(',', $visibility_role);
			
			if( in_array($role, $allowed_roles) ) {
				$is_visible = true;
			}
			break;
	}
	
	return apply_filters('ppom_is_field_visible', $is_visible, $field);
	
}

// Get logged in user role
function ppom_get_current_user_role() {
  
	if( is_user_logged_in() ) {
		$user = wp_get_current_user();
		$role = ( array ) $user->roles;
		return $role[0];
	} else {
		return false;
	}
}

// Retrun price with currency symbol but without html
function ppom_price( $price ) {
	
	$price					= floatval($price);
	
	$decimal_separator		= wc_get_price_decimal_separator();
	$thousand_separator		= wc_get_price_thousand_separator();
	$decimals				= wc_get_price_decimals();
	$price_format			= get_woocommerce_price_format();
	$negative       		= $price < 0;
	
	// $wc_price = number_format( $price,$decimals, $decimal_separator, $thousand_separator );
	$wc_price = number_format( abs($price), $decimals, $decimal_separator, $thousand_separator );
	$formatted_price = ( $negative ? '-' : '' ) . sprintf( $price_format, get_woocommerce_currency_symbol(), $wc_price );
	return apply_filters('ppom_woocommerce_price', $formatted_price);
}

// If price set by pricematrix in cart return matrix
function ppom_get_price_matrix_chunk($product, $option_prices, $ppom_item_order_qty) {
	
	$matrix_found = '';
	
	$pricematrix_field = ppom_has_field_by_type(ppom_get_product_id($product), 'pricematrix');
	// ppom_pa($pricematrix_field);
	
	if ( ! $pricematrix_field ) return $matrix_found;
	
	if( count($pricematrix_field) > 0 ) {
		
		foreach( $pricematrix_field as $pm ) {
			
			//iterecting option_prices
			foreach( $option_prices as $op ) {
				
				if( $op['apply'] != 'matrix_discount' && $op['apply'] != 'matrix') continue;
				
				if( $op['data_name'] == $pm['data_name'] ) {
					$pricematrix_field = $pm;
					break;
				}
			}
		}
		
		// $pricematrix_field = $pricematrix_field[0];
		$matrix_found = ppom_extract_matrix_by_quantity($pricematrix_field, $product, $ppom_item_order_qty);
	}
	
	return apply_filters('ppom_price_matrix_found_in_cart', $matrix_found, $product, $option_prices);
}

function ppom_get_date_formats() {
	
	$formats = array (
						'mm/dd/yy' => 'Default - mm/dd/yyyy',
						'dd/mm/yy' => 'dd/mm/yyyy',
						'yy-mm-dd' => 'ISO 8601 - yy-mm-dd',
						'd M, y' => 'Short - d M, y',
						'd MM, y' => 'Medium - d MM, y',
						'DD, d MM, yy' => 'Full - DD, d MM, yy',
						'\'day\' d \'of\' MM \'in the year\' yy' => 'With text - \'day\' d \'of\' MM \'in the year\' yy',
						'\'Month\' MM \'day\' d \'in the year\' yy' => 'With text - \'Month\' January \'day\' 7 \'in the year\' yy'
				);
				
	return apply_filters('ppom_date_formats', $formats);
}

// Security: checking if attached fields have price
function ppom_is_price_attached_with_fields( $fields_posted ) {
	
	
	$is_price_attached = false;
	
	$option_price = 0;
	$ppom_id = $fields_posted['id'];
	foreach($fields_posted as $data_name => $value) {
		
		// soon prodcut_id will be removed
		$product_id = null;
		$field_meta = ppom_get_field_meta_by_dataname($product_id, $data_name, $ppom_id);
		$field_type	= isset($field_meta['type']) ? $field_meta['type'] : '';
		
		switch( $field_type ) {
			
			case 'checkbox':
				if( is_array($value) ) {
					foreach($value as $cb_value) {
						$option_price 	+= ppom_get_field_option_price($field_meta, $cb_value);
					}
				}
			break;
			
			default:
				$option_price 	+= ppom_get_field_option_price($field_meta, $value);
				break;
		}
	}
	
	if($option_price > 0) {
		$is_price_attached = true;
	}
	
	// If price matrix attached
	if( isset($_POST['ppom']['ppom_pricematrix']) ) {
		$is_price_attached = true;
	}
	
	// exit;
	
	return apply_filters('ppom_option_price_attached', $is_price_attached, $fields_posted, $product_id);
}

// PPOM Get settings
function ppom_get_option($key, $default_val=false) {
	
	$value = get_option($key);
	if( ! $value )
		$value = $default_val;
	return ppom_wpml_translate($value, 'PPOM');
}

// Checking PPOM Pro version
function ppom_get_pro_version() {
	
	if( ! defined('PPOM_PRO_VERSION') ) return 16.0;
	return intval( PPOM_PRO_VERSION );
}