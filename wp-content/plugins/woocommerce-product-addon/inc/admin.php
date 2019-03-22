<?php
/**
 * admin related functions/hooks
 * 
 * @since 10.0
 **/
 
 ppom_direct_access_not_allowed();
 
 // adding column in product list
function ppom_admin_show_product_meta( $columns ){
    
    unset($columns['date']);
    unset($columns['product_tag']);
    $columns['ppom_meta'] = __( 'PPOM', "ppom");
    $columns['date'] = __( 'Date');
    return $columns;
    
    return array_merge( $columns, 
              array('ppom_meta' => __( 'PPOM', "ppom") )
              );
}

function ppom_admin_product_meta_column( $column, $post_id ) {
    
    switch ( $column ) {

      case 'ppom_meta' :
          
        	$product_meta = '';
        	$ppom		= new PPOM_Meta( $post_id );
			
            $ppom_settings_url = admin_url( 'admin.php?page=ppom');
            
            if( $ppom->has_multiple_meta() ) {
            	foreach($ppom->meta_id as $meta_id) {
            		$ppom_setting = $ppom->get_settings_by_id($meta_id);
            		$meta_title		= stripslashes($ppom_setting->productmeta_name);
                	$url_edit = add_query_arg(array('productmeta_id'=> $ppom_setting->productmeta_id, 'do_meta'=>'edit'), $ppom_settings_url);
                	echo sprintf(__('<a href="%s">%s</a>', "ppom"), $url_edit, $meta_title);
                	echo ', ';
            	}
            } else if ( $ppom->ppom_settings ){
                $url_edit = add_query_arg(array('productmeta_id'=> $ppom->meta_id, 'do_meta'=>'edit'), $ppom_settings_url);
                echo sprintf(__('<a href="%s">%s</a>', "ppom"), $url_edit, $ppom->meta_title);
            }else{
                echo sprintf(__('<a class="btn button" href="%s">%s</a>', "ppom"), $ppom_settings_url, "Add Fields");
            }
            
            break;

    }
}

function ppom_admin_product_meta_metabox() {
	
	add_meta_box ( 'ppom-select-meta', __ ( 'Select PPOM Meta', 'ppom' ), 'ppom_meta_list', 'product', 'side', 'default' );
}

function ppom_meta_list( $post ) {
    
  	$ppom		= new PPOM_Meta( $post->ID );
	$all_meta	= PPOM() -> get_product_meta_all ();
	$ppom_setting = admin_url('admin.php?page=ppom');
	
	$html = '<div class="options_group">';
	$html .= '<p>'.__('Select Meta to Show Fields on this product', 'ppom');
	// $html .= __(' Or <a target="_blank" class="button" href="'.esc_url($ppom_setting).'">Create New Meta</a>', 'ppom');
	$html .= '</p>';

	$html .= '<p>';
	$html .= '<select name="ppom_product_meta" id="ppom_product_meta" class="select">';
	$html .= '<option selected="selected"> ' . __('None', "ppom"). '</option>';
	
	foreach ( $all_meta as $meta ) {
			
		$html .= '<option value="'.esc_attr($meta->productmeta_id) . '" ';
		$html .= selected($ppom->single_meta_id, $meta->productmeta_id, false);
		$html .= 'id="select_meta_group-' . $meta->productmeta_id . '">';
		$html .= stripslashes($meta->productmeta_name);
		$html .= '</option>';
	}
	$html .= '</select>';
	
	if( $ppom->single_meta_id != 'None' ) {
		
		$url_edit = add_query_arg(array('productmeta_id'=> $ppom->single_meta_id, 'do_meta'=>'edit'), $ppom_setting);
		$html .= ' <a class="button" href="'.esc_url($url_edit).'" title="Edit"><span class="dashicons dashicons-edit"></span></a>';
	}
	
	// $html .= '<hr>';
	// $html .= ' <a class="button button-primary" href="'.esc_url($ppom_setting).'">Create New Meta</a>';
	
	$html .= '</p>';
	$html .= '</div>';
	
	$video_url = 'https://najeebmedia.com/ppom/#howtovideo';
	$html .= sprintf(__('<p><a href="%s" target="_blank">How to use?</a>', "ppom"), $video_url);
	$html .= sprintf(__(' - <a href="%s" target="_blank">Create New Meta</a></p>', "ppom"), $ppom_setting);
	
	echo apply_filters('ppom_select_meta_in_product', $html, $ppom, $all_meta);
	
	echo '<div class="ppom_extra_options_panel">';
	do_action('ppom_meta_box_after_list', $post);
	echo '</div>';
}

/*
 * saving meta data against product
 */
function ppom_admin_process_product_meta( $post_id ) {
	
     //ppom_pa($_POST); exit; 
     
    $ppom_meta_selected = isset($_POST ['ppom_product_meta']) ? $_POST ['ppom_product_meta'] : '';
    
    // $ppom_save_in_array = array($ppom_meta_selected);
	update_post_meta ( $post_id, '_product_meta_id', $ppom_meta_selected );
    
    do_action('ppom_proccess_meta', $post_id);
}

// Show notices
function ppom_admin_show_notices() {
    
    if ( $resp_notices = get_transient( "ppom_meta_imported" ) ) {
		?>
		<div id="message" class="<?php echo $resp_notices['class']; ?> updated notice is-dismissible">
			<p><?php echo $resp_notices['message']; ?></p>
			<button type="button" class="notice-dismiss">
				<span class="screen-reader-text"><?php _e( 'Dismiss this notice', 'ppom' ); ?></span>
			</button>
		</div>
	<?php
	
	    delete_transient("ppom_meta_imported");
	}
}

function ppom_admin_pro_version_notice() {
		
	$ppom_pro = 'https://najeebmedia.com/wordpress-plugin/woocommerce-personalized-product-option/';
	echo '<p class="center"><a href="'.esc_url($ppom_pro).'" class="btn btn-primary">Get PRO - Unlock All 20 Fields</a></p>';
	
	// Get PRO discount
	$ppom_buy = 'https://www.2checkout.com/checkout/purchase?sid=1686663&quantity=1&product_id=15';
	echo '<p>COUPON Code: PPOM25-2018</p>';
	echo '<p><a href="'.esc_url($ppom_buy).'" class="btn btn-primary">Get 25% Discoun</a></p>';
}

function ppom_admin_rate_and_get() {
		
	if( !ppom_pro_is_installed() ) return '';
	
	
	$ppom_pro = 'https://najeebmedia.com/get-quote/';
	echo '<p class="center"><a href="'.esc_url($ppom_pro).'" class="btn btn-primary">Get One Addon Free - Contact</a></p>';
}

/*
 * saving admin setting in wp option data table
 */
function ppom_admin_save_settings() {
	
	// $this -> pa($_REQUEST);
	$existingOptions = get_option ( 'ppom' . '_settings' );
	// pa($existingOptions);
	
	update_option ( 'ppom' . '_settings', $_REQUEST );
	_e ( 'All options are updated', 'ppom' );
	die ( 0 );
}

/*
 * saving form meta in admin call
 */
function ppom_admin_save_form_meta() {
	
	// print_r($_REQUEST); exit;
	global $wpdb;
	
	extract ( $_REQUEST );
	
	$product_meta = apply_filters('ppom_meta_data_saving', $ppom);
	
	$send_file_attachment 	= "NA";
	$aviary_api_key			= "NA";
	$show_cart_thumb		= "NA";

	$dt = array (
			'productmeta_name'          => $productmeta_name,
			'productmeta_validation'	=> $enable_ajax_validation,
            'dynamic_price_display'     => $dynamic_price_hide,
            'send_file_attachment'		=> $send_file_attachment,
            'show_cart_thumb'			=> $show_cart_thumb,
			'aviary_api_key'            => trim ( $aviary_api_key ),
			'productmeta_style'         => $productmeta_style,
			'productmeta_categories'    => $productmeta_categories,
			'the_meta'                  => json_encode ( $product_meta ),
			'productmeta_created'       => current_time ( 'mysql' )
	);
	
	$format = array (
			'%s',
			'%s',
			'%s',
            '%s',
			'%s',
			'%s',
			'%s' 
	);
	
	global $wpdb;
	$ppom_table = $wpdb->prefix.PPOM_TABLE_META;
	$wpdb->insert($ppom_table, $dt, $format);
	$res_id = $wpdb->insert_id;
	
	$resp = array ();
	if ($res_id) {
		
		$resp = array (
				'message' => __ ( 'Form added successfully', 'ppom' ),
				'status' => 'success',
				'productmeta_id' => $res_id 
		);
	} else {
		
		$resp = array (
				'message' => __ ( 'No changes found.', 'ppom' ),
				'status' => 'success',
				'productmeta_id' => ''
		);
	}
	
	wp_send_json($resp);
}
	
/*
 * updating form meta in admin call
 */
function ppom_admin_update_form_meta() {
	
	// print_r($_REQUEST); exit;
	global $wpdb;
	
	extract ( $_REQUEST );
	// ppom_pa($product_meta); exit;
	
	$productmeta_name = isset($_REQUEST['productmeta_name']) ? sanitize_text_field($_REQUEST['productmeta_name']) : '';
	$productmeta_validation = isset($_REQUEST['enable_ajax_validation']) ? sanitize_text_field($_REQUEST['enable_ajax_validation']) : '';
	$dynamic_price_hide = isset($_REQUEST['dynamic_price_hide']) ? sanitize_text_field($_REQUEST['dynamic_price_hide']) : '';
	$send_file_attachment = isset($_REQUEST['send_file_attachment']) ? sanitize_text_field($_REQUEST['send_file_attachment']) : '';
	$show_cart_thumb = isset($_REQUEST['show_cart_thumb']) ? sanitize_text_field($_REQUEST['show_cart_thumb']) : '';
	$aviary_api_key = isset($_REQUEST['aviary_api_key']) ? sanitize_text_field($_REQUEST['aviary_api_key']) : '';
	$productmeta_style = isset($_REQUEST['productmeta_style']) ? sanitize_text_field($_REQUEST['productmeta_style']) : '';
	$productmeta_categories = isset($_REQUEST['productmeta_categories']) ? $_REQUEST['productmeta_categories'] : '';
	$product_meta = isset($_REQUEST['ppom']) ? $_REQUEST['ppom'] : '';
	
	
	// ppom_pa($product_meta); exit;
	$product_meta = apply_filters('ppom_meta_data_saving', $product_meta);
	
	
	
	$dt = array (
			'productmeta_name'          => $productmeta_name,
			'productmeta_validation'    => $productmeta_validation,
            'dynamic_price_display'     => $dynamic_price_hide,
            'send_file_attachment'		=> $send_file_attachment,
            'show_cart_thumb'			=> $show_cart_thumb,
			'aviary_api_key'            => trim ( $aviary_api_key ),
			'productmeta_style'         => $productmeta_style,
			'productmeta_categories'    => $productmeta_categories,
			'the_meta'                  => json_encode ( $product_meta )
	);
	
	// ppom_pa($dt); exit;
	
	$where = array (
			'productmeta_id' => $productmeta_id 
	);
	
	$format = array (
			'%s',
			'%s',
            '%s',
            '%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
	);
	$where_format = array (
			'%d' 
	);
	
	global $wpdb;
	$ppom_table = $wpdb->prefix.PPOM_TABLE_META;
	$rows_effected = $wpdb->update($ppom_table, $dt, $where, $format, $where_format);
	
	// $wpdb->show_errors(); $wpdb->print_error();
	
	$resp = array ();
	if ($rows_effected) {
		
		$resp = array (
				'message' => __ ( 'Form updated successfully', 'ppom' ),
				'status' => 'success',
				'productmeta_id' => $productmeta_id 
		);
	} else {
		
		$resp = array (
				'message' => __ ( 'Form updated successfully.', 'ppom' ),
				'status' => 'success',
				'productmeta_id' => $productmeta_id 
		);
	}
	
	wp_send_json($resp);
}

/*
 * delete meta
 */
function ppom_admin_delete_meta() {
	global $wpdb;
	
	extract ( $_REQUEST );
	
	$res = $wpdb->query ( "DELETE FROM `" . $wpdb->prefix . PPOM_TABLE_META . "` WHERE productmeta_id = " . $productmeta_id );
	
	if ($res) {
		
		_e ( 'Meta deleted successfully', 'ppom' );
	} else {
		$wpdb->show_errors ();
		$wpdb->print_error ();
	}
	
	die ( 0 );
}

/*
 * delete meta
 */
function ppom_admin_delete_selected_meta() {
	
	global $wpdb;
	
	extract( $_REQUEST );
	$productmeta_ids = implode(', ', $productmeta_ids);
		
	$res = $wpdb->query ( "DELETE FROM `" . $wpdb->prefix . PPOM_TABLE_META . "` WHERE productmeta_id In (" . $productmeta_ids.")" );
	
	if ($res) {
		
		_e ( 'Meta deleted successfully', 'ppom' );
	} else {
		$wpdb->show_errors ();
		$wpdb->print_error ();
	}
	
	die ( 0 );
}


/*
 * simplifying meta for admin view in existing-meta.php
 */
function ppom_admin_simplify_meta($meta) {
	//echo $meta;
	$metas = json_decode ( $meta );
	
	if ($metas) {
		echo '<ul>';
		foreach ( $metas as $meta => $data ) {
			
			//ppom_pa($data);
			$req = (isset( $data -> required ) && $data -> required == 'on') ? 'yes' : 'no';
			$title = (isset( $data -> title )  ? $data -> title : '');
			$type = (isset( $data -> type )  ? $data -> type : '');
			$options = (isset( $data -> options )  ? $data -> options : '');
			
			echo '<li>';
			echo '<strong>label:</strong> ' . $title;
			echo ' | <strong>type:</strong> ' . $type;
			
			if (! is_object ( $options) && is_array ( $options )){
				echo ' | <strong>options:</strong> ';
				foreach($options as $option){
					
					$display_info = '';
					if( isset($option->option) ) {
						$display_info = $option->option;
					} elseif(isset($option->width)) {
						$display_info = $option->width.'x'.$option->height;
					}
					
					if( empty($option->price) ) { 
						echo $display_info .', ';
					} else{
						echo $display_info . ' (' .$option -> price .'), ';
					}
				}
			}
			
				
			echo ' | <strong>required:</strong> ' . $req;
			echo '</li>';
		}
		
		echo '</ul>';
	}
}

// Showing PPOM Edit on Product Page
function ppom_admin_bar_menu() {

	if( ! is_product() ) return;
	
	global $wp_admin_bar, $product;
	
	$product_id = ppom_get_product_id( $product ); 
	$ppom		= new PPOM_Meta( $product_id );
	
	if( ! $ppom->is_exists ) return;

	$ppom_setting_url = admin_url( 'admin.php');
	$ppom_setting_url = add_query_arg(array('page'=>'ppom',
									'productmeta_id'=>$ppom->single_meta_id,
									'do_meta'	=> 'edit'),
									$ppom_setting_url
									);
	
	$bar_title = "Edit PPOM ({$ppom->meta_title})";
	$wp_admin_bar->add_node( array(
		'id'     => 'ppom-setting-bar',
		'title'  => sprintf(__( "%s", "ppom"), $bar_title ),
		'href'  => $ppom_setting_url,
	) );
	
	$all_meta	= PPOM() -> get_product_meta_all ();
	foreach ( $all_meta as $meta ) {
			
			$apply_link = admin_url('admin-post.php');
			$apply_arg	= array('productid'=>$product_id,
								'metaid'=>$meta->productmeta_id,
								'metatitle'=>$meta->productmeta_name,
								'action'=>'ppom_attach');
			$apply_link = add_query_arg($apply_arg, $apply_link);
			$bar_title = "Apply {$meta->productmeta_name}";
			$wp_admin_bar->add_node( array(
			'id'    	=> "ppom-setting-bar-{$meta->productmeta_id}",
			'title' 	=> sprintf(__( "%s", "ppom"), $bar_title ),
			'href'  	=> $apply_link,
			'parent'	=> 'ppom-setting-bar',
		) );
	}
}