<?php

function pmxi_wp_ajax_save_import_functions(){

	if ( ! check_ajax_referer( 'wp_all_import_secure', 'security', false )){
		exit( json_encode(array('html' => __('Security check', 'wp_all_import_plugin'))) );
	}

	if ( ! current_user_can( PMXI_Plugin::$capabilities ) ){
		exit( json_encode(array('html' => __('Security check', 'wp_all_import_plugin'))) );
	}

	$uploads   = wp_upload_dir();
	$functions = $uploads['basedir'] . DIRECTORY_SEPARATOR . WP_ALL_IMPORT_UPLOADS_BASE_DIRECTORY . DIRECTORY_SEPARATOR . 'functions.php';
	$functions = apply_filters( 'import_functions_file_path', $functions );

	$input = new PMXI_Input();
	
	$post = $input->post('data', '');

	$response = wp_remote_post('http://phpcodechecker.com/api', array(
		'body' => array(
			'code' => $post
		)
	));

	if (is_wp_error($response))
	{
		if (strpos($post, "<?php") === false || strpos($post, "?>") === false)
		{
			exit(json_encode(array('result' => false, 'msg' => __('PHP code must be wrapped in "&lt;?php" and "?&gt;"', 'wp_all_import_plugin')))); die;	
		}	
		else
		{
			file_put_contents($functions, $post);
		}

   		exit(json_encode(array('result' => true, 'msg' => __('File has been successfully updated.', 'wp_all_import_plugin')))); die;
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
				exit(json_encode(array('result' => false, 'msg' => __('PHP code must be wrapped in "&lt;?php" and "?&gt;"', 'wp_all_import_plugin')))); die;	
			}	
			else
			{
				file_put_contents($functions, $post);
			}					
		}
	}	

	exit(json_encode(array('result' => true, 'msg' => __('File has been successfully updated.', 'wp_all_import_plugin')))); die;
}