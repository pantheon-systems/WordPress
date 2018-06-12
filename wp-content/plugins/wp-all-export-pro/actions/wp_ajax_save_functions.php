<?php

function pmxe_wp_ajax_save_functions(){

	if ( ! check_ajax_referer( 'wp_all_export_secure', 'security', false )){
		exit( json_encode(array('html' => __('Security check', 'wp_all_export_plugin'))) );
	}

	if ( ! current_user_can( PMXE_Plugin::$capabilities ) ){
		exit( json_encode(array('html' => __('Security check', 'wp_all_export_plugin'))) );
	}

	$uploads   = wp_upload_dir();
	$functions = $uploads['basedir'] . DIRECTORY_SEPARATOR . WP_ALL_EXPORT_UPLOADS_BASE_DIRECTORY . DIRECTORY_SEPARATOR . 'functions.php';

	$input = new PMXE_Input();
	
	$post = $input->post('data', '');

	$response = wp_remote_post('http://phpcodechecker.com/api', array(
		'body' => array(
			'code' => $post
		)
	));

	if (is_wp_error($response))
	{
		$error_message = $response->get_error_message();   		
   		exit(json_encode(array('result' => false, 'msg' => $error_message))); die;
	}
	else
	{
		$body = json_decode(wp_remote_retrieve_body($response), true);

		if ($body['errors'] === 'TRUE')
		{			
			exit(json_encode(array('result' => false, 'msg' => $body['syntax']['message']))); die;	
		}
		elseif($body['errors'] === 'FALSE')
		{
			if (strpos($post, "<?php") === false || strpos($post, "?>") === false)
			{
				exit(json_encode(array('result' => false, 'msg' => __('PHP code must be wrapped in "&lt;?php" and "?&gt;"', 'wp_all_export_plugin')))); die;	
			}	
			else
			{
				file_put_contents($functions, $post);
			}					
		}
        elseif(empty($body)){
            file_put_contents($functions, $post);
        }
	}	

	exit(json_encode(array('result' => true, 'msg' => __('File has been successfully updated.', 'wp_all_export_plugin')))); die;
}