<?php

function pmxi_wp_ajax_upload_resource(){

	if ( ! check_ajax_referer( 'wp_all_import_secure', 'security', false )){
		exit( json_encode(array('success' => false, 'errors' => '<div class="error inline"><p>' . __('Security check', 'wp_all_import_plugin') . '</p></div>')) );
	}

	if ( ! current_user_can( PMXI_Plugin::$capabilities ) ){
		exit( json_encode(array('success' => false, 'errors' => '<div class="error inline"><p>' . __('Security check', 'wp_all_import_plugin') . '</p></div>')) );
	}
	
	$input = new PMXI_Input();

	$post = $input->post(array(
		'type' => '',
		'file' => '',
		'template' => ''
	));			

	$response = array(
		'success' => true,
		'errors' => false,		
		'upload_result' => '',
		'filesize' => 0,
		'notice' => false
	);

	if ($post['type'] == 'url'){

		$filesXML = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n<data><node></node></data>";

		$post['file'] = apply_filters('wp_all_import_feed_url', wp_all_import_sanitize_url($post['file']));

		$files = XmlImportParser::factory($filesXML, '/data/node', $post['file'], $file)->parse(); $tmp_files[] = $file;	

		foreach ($tmp_files as $tmp_file) { // remove all temporary files created
			@unlink($tmp_file);
		}

		$file_to_import = $post['file'];

		if ( ! empty($files) and is_array($files) )
		{
			$file_to_import = array_shift($files);
		}				

		$errors = new WP_Error;
		$uploader = new PMXI_Upload(trim($file_to_import), $errors);			
		$upload_result = $uploader->url('', $post['file'], $post['template']);

		if ($upload_result instanceof WP_Error){
			$errors = $upload_result;

			$msgs = $errors->get_error_messages();
			ob_start();
			?>
			<?php foreach ($msgs as $msg): ?>
				<div class="error inline"><p><?php echo $msg; ?></p></div>
			<?php endforeach ?>
			<?php
			$response = array(		
				'success' => false,
				'is_valid' => true,
				'errors'  => ob_get_clean()
			);			

		}
		else {

			// $root_element = wp_all_import_get_reader_engine( array($upload_result['filePath']), array('root_element' => $upload_result['root_element']) );	
				
			// if ( ! empty($root_element) and empty($upload_result['root_element']))
			// {
			// 	$upload_result['root_element'] = $root_element;
			// }

			// validate XML
			$file = new PMXI_Chunk($upload_result['filePath'], array('element' => $upload_result['root_element']));										    					    					   												

			$is_valid = true;

			if ( ! empty($file->options['element']) ) 						
				$defaultXpath = "/". $file->options['element'];																			    		  
			else
				$is_valid = false;
			
			if ( $is_valid ){

				while ($xml = $file->read()) {

			    	if ( ! empty($xml) ) { 

			      		//PMXI_Import_Record::preprocessXml($xml);
			      		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" . "\n" . $xml;								    
				      	$dom = new DOMDocument( '1.0', 'UTF-8' );
						$old = libxml_use_internal_errors(true);
						$dom->loadXML($xml);
						libxml_use_internal_errors($old);
						$xpath = new DOMXPath($dom);									
						if (($elements = $xpath->query($defaultXpath)) and $elements->length){
							break;
						}																		
				    }
				    /*else {
				    	$is_valid = false;
				    	break;
				    }*/
				}

				if ( empty($xml) ) $is_valid = false;
			}

			unset($file);
				
			if ( ! $is_valid )
			{				
				$response = array(		
					'success'  => false,
					'is_valid' => false,
					'errors'   => __("Please verify that the URL returns a valid import file.", "wp_all_import_plugin")
				);
			}
			else {
				$response['upload_result'] = $upload_result;			
				$response['filesize'] = filesize($upload_result['filePath']);
				$response['post_type'] = $upload_result['post_type'];
				$response['taxonomy_type'] = $upload_result['taxonomy_type'];

				if ( ! empty($response['post_type']) ) 
				{
					switch ( $response['post_type'] ) {

						case 'product':
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
		}
	} 	

	exit( json_encode($response) );
}