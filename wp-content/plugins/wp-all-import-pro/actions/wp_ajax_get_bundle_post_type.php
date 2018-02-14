<?php

function pmxi_wp_ajax_get_bundle_post_type(){

	if ( ! check_ajax_referer( 'wp_all_import_secure', 'security', false )){
		exit( json_encode(array('success' => false, 'errors' => '<div class="error inline"><p>' . __('Security check', 'wp_all_import_plugin') . '</p></div>')) );
	}

	if ( ! current_user_can( PMXI_Plugin::$capabilities ) ){
		exit( json_encode(array('success' => false, 'errors' => '<div class="error inline"><p>' . __('Security check', 'wp_all_import_plugin') . '</p></div>')) );
	}
	
	$input = new PMXI_Input();

	$post = $input->post(array(	
		'file' => ''
	));			

	$response = array(			
		'post_type' => false,
		'notice' => false		
	);

	if (preg_match('%\W(zip)$%i', trim($post['file']))) {

		if ( ! class_exists('PclZip') ) include_once( PMXI_Plugin::ROOT_DIR . '/libraries/pclzip.lib.php' );

        $wp_uploads = wp_upload_dir();

		$uploads = $wp_uploads['basedir'] . DIRECTORY_SEPARATOR . PMXI_Plugin::FILES_DIRECTORY . DIRECTORY_SEPARATOR;

		$archive = new PclZip($uploads . $post['file']);

		$tmp_dir = $wp_uploads['basedir'] . DIRECTORY_SEPARATOR . PMXI_Plugin::TEMP_DIRECTORY . DIRECTORY_SEPARATOR . md5(time());

		@wp_mkdir_p($tmp_dir);

		$v_result_list = $archive->extract(PCLZIP_OPT_PATH, $tmp_dir, PCLZIP_OPT_REPLACE_NEWER);		

		if ( $v_result_list ) 
		{
			foreach ($v_result_list as $unzipped_file) 
			{
				if ($unzipped_file['status'] == 'ok' and preg_match('%\W(xml|csv|txt|dat|psv|json|xls|xlsx)$%i', trim($unzipped_file['stored_filename'])) and strpos($unzipped_file['stored_filename'], 'readme.txt') === false ) 
				{
					if ( strpos(basename($unzipped_file['stored_filename']), 'WP All Import Template') === 0 || strpos(basename($unzipped_file['stored_filename']), 'templates_') === 0 )
					{
						$templates = file_get_contents($unzipped_file['filename']);											

						$decodedTemplates = json_decode($templates, true);		

						$templateOptions = empty($decodedTemplates[0]) ? current($decodedTemplates) : $decodedTemplates;

						$options = (empty($templateOptions[0]['options'])) ? false : maybe_unserialize($templateOptions[0]['options']);			

						$response['post_type'] = ( ! empty($options) ) ? $options['custom_type'] : false;
                        $response['taxonomy_type'] = ( ! empty($options) ) ? $options['taxonomy_type'] : false;
					}					
				}
			}
		}	    
		
		wp_all_import_rmdir($tmp_dir);

		if ( ! empty($response['post_type'])) 
		{
			switch ( $response['post_type'] ) {
				
				case 'shop_order':
					
					if ( ! class_exists('WooCommerce') ) {
						$response['notice'] = __('<p class="wpallimport-bundle-notice">The import bundle you are using requires WooCommerce.</p><a class="upgrade_link" href="https://wordpress.org/plugins/woocommerce/" target="_blank">Get WooCommerce</a>.', 'wp_all_import_plugin');							
					}
					else {

						if ( ! defined('PMWI_EDITION') ) {

							$response['notice'] = __('<p class="wpallimport-bundle-notice">The import bundle you are using requires the Pro version of the WooCommerce Add-On.</p><a href="http://www.wpallimport.com/checkout/?edd_action=add_to_cart&download_id=1529&edd_options%5Bprice_id%5D=1" class="upgrade_link" target="_blank">Purchase the WooCommerce Add-On</a>.', 'wp_all_import_plugin');

						}
						elseif ( PMWI_EDITION != 'paid' ) {

							$response['notice'] = __('<p class="wpallimport-bundle-notice">The import bundle you are using requires the Pro version of the WooCommerce Add-On, but you have the free version installed.</p><a href="http://www.wpallimport.com/checkout/?edd_action=add_to_cart&download_id=1529&edd_options%5Bprice_id%5D=1" target="_blank" class="upgrade_link">Purchase the WooCommerce Add-On</a>.', 'wp_all_import_plugin');

						}							
					}

					break;

				case 'import_users':

					if ( ! class_exists('PMUI_Plugin') ) {
						$response['notice'] = __('<p class="wpallimport-bundle-notice">The import bundle you are using requires the User Import Add-On.</p><a href="http://www.wpallimport.com/checkout/?edd_action=add_to_cart&download_id=1921&edd_options%5Bprice_id%5D=1" target="_blank" class="upgrade_link">Purchase the User Import Add-On</a>.', 'wp_all_import_plugin');
					}

					break;
				
				default:
					# code...
					break;
			}
		}

	}	
	
	exit( json_encode($response) );
}