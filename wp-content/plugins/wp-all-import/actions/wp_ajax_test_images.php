<?php

function pmxi_wp_ajax_test_images(){

	if ( ! check_ajax_referer( 'wp_all_import_secure', 'security', false )){
		exit( json_encode(array('result' => array(), 'failed_msgs' => array(__('Security check', 'wp_all_import_plugin')))));
	}

	if ( ! current_user_can( PMXI_Plugin::$capabilities ) ){
		exit( json_encode(array('result' => array(), 'failed_msgs' => array(__('Security check', 'wp_all_import_plugin')))));
	}

	$input = new PMXI_Input();

	$post = $input->post(array(
		'download' => 'yes',
		'imgs' => array()
	));	

	$result = array();
	
	$wp_uploads = wp_upload_dir();	
	$imgs_basedir = $wp_uploads['basedir'] . DIRECTORY_SEPARATOR . PMXI_Plugin::FILES_DIRECTORY . DIRECTORY_SEPARATOR;
	$targetDir = $wp_uploads['path'];
	$success_images = 0;
	$success_msg = '';

	$failed_msgs = array();

	if ( ! @is_writable($targetDir) )
	{
		$failed_msgs[] = sprintf(__('Uploads folder `%s` is not writable.', 'wp_all_import_plugin'), $targetDir);	
	}
	else
	{
		switch ($post['download']) 
		{
			// Use images currently uploaded in wp-content/uploads/wpallimport/files/
			case 'no':

				if ( ! empty($post['imgs']) )
				{
					foreach ($post['imgs'] as $img) 
					{
						if ( preg_match('%^(http|https|ftp|ftps)%i', $img)){
							$failed_msgs[] = sprintf(__('Use image name instead of URL `%s`.', 'wp_all_import_plugin'), $img);		
							continue;								
						}
						if ( @file_exists($imgs_basedir . $img) ){
							if (@is_readable($imgs_basedir . $img)){
								$success_images++;
							} else{
								$failed_msgs[] = sprintf(__('File `%s` isn\'t readable', 'wp_all_import_plugin'), preg_replace('%.*/wp-content%', '/wp-content', $imgs_basedir . $img));
							}
						} 					
						else{
							$failed_msgs[] = sprintf(__('File `%s` doesn\'t exist', 'wp_all_import_plugin'), preg_replace('%.*/wp-content%', '/wp-content', $imgs_basedir . $img));
						}
					}			
				}
				if ((int)$success_images === 1)
				{
					$success_msg = sprintf(__('%d image was successfully retrieved from `%s`', 'wp_all_import_plugin'), $success_images, preg_replace('%.*/wp-content%', '/wp-content', $wp_uploads['basedir']) . DIRECTORY_SEPARATOR . PMXI_Plugin::FILES_DIRECTORY);		
				}					
				elseif ((int)$success_images > 1)
				{
					$success_msg = sprintf(__('%d images were successfully retrieved from `%s`', 'wp_all_import_plugin'), $success_images, preg_replace('%.*/wp-content%', '/wp-content', $wp_uploads['basedir']) . DIRECTORY_SEPARATOR . PMXI_Plugin::FILES_DIRECTORY);		
				}					

				break;

			// 	Use images currently in Media Library
			case 'gallery':

				if ( ! empty($post['imgs']) )
				{
					global $wpdb;

					foreach ($post['imgs'] as $img) 
					{
						$bn      = wp_all_import_sanitize_filename(wp_all_import_basename($img));
						$img_ext = pmxi_getExtensionFromStr($img);									
						$default_extension = pmxi_getExtension($bn);																									

						$image_name = apply_filters("wp_all_import_image_filename", urldecode(sanitize_file_name((($img_ext) ? str_replace("." . $default_extension, "", $bn) : $bn))) . (("" != $img_ext) ? '.' . $img_ext : ''));

						$attch = wp_all_import_get_image_from_gallery($image_name, $targetDir);

						if ( ! empty($attch) )
						{
							$success_images++;
						}
						else
						{
							$failed_msgs[] = sprintf(__('Image `%s` not found in media library.', 'wp_all_import_plugin'), $image_name);
						}
					}
				}

				if ((int)$success_images === 1)
				{
					$success_msg = sprintf(__('%d image was successfully found in media gallery', 'wp_all_import_plugin'), $success_images);		
				}					
				elseif ((int)$success_images > 1)
				{
					$success_msg = sprintf(__('%d images were successfully found in media gallery', 'wp_all_import_plugin'), $success_images);		
				}				

				break;		

			// Download images hosted elsewhere
			default:
				
				$start = time();
				if ( ! empty($post['imgs']) )
				{								
					foreach ($post['imgs'] as $img) 
					{	
						if ( ! preg_match('%^(http|https|ftp|ftps)%i', $img)){
							$failed_msgs[] = sprintf(__('URL `%s` is not valid.', 'wp_all_import_plugin'), $img);		
							continue;								
						}
						
						$image_name = wp_unique_filename($targetDir, 'test');

						$image_filepath = $targetDir . '/' . $image_name;

						$url = trim($img);

						$request = get_file_curl($url, $image_filepath);

						$get_ctx = stream_context_create(array('http' => array('timeout' => 5)));

						if ( (is_wp_error($request) or $request === false) and ! @file_put_contents($image_filepath, @file_get_contents($img, false, $get_ctx))) {
							$failed_msgs[] = (is_wp_error($request)) ? $request->get_error_message() : sprintf(__('File `%s` cannot be saved locally', 'wp_all_import_plugin'), $img);										
						} elseif( ! ($image_info = apply_filters('pmxi_getimagesize', @getimagesize($image_filepath), $image_filepath)) or ! in_array($image_info[2], array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP))) {
							$failed_msgs[] = sprintf(__('File `%s` is not a valid image.', 'wp_all_import_plugin'), $img);										
						} else {
							$success_images++;											
						}						
						@unlink($image_filepath);
					}
				}
				$time = time() - $start;

				if ((int)$success_images === 1)
				{
					$success_msg = sprintf(__('%d image was successfully downloaded in %s seconds', 'wp_all_import_plugin'), $success_images, number_format($time, 2));		
				}					
				elseif ((int)$success_images > 1)
				{
					$success_msg = sprintf(__('%d images were successfully downloaded in %s seconds', 'wp_all_import_plugin'), $success_images, number_format($time, 2));		
				}					

				break;
		}			
	}

	exit(json_encode(array(		
		'success_images' => $success_images,
		'success_msg' => $success_msg,
		'failed_msgs' => $failed_msgs
	))); die;

}