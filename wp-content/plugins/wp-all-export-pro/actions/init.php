<?php
	
function pmxe_init()
{
	if ( ! empty($_GET['zapier_auth']) )
	{
		if ( ! empty($_GET['api_key']) )
		{			

			$zapier_api_key = PMXE_Plugin::getInstance()->getOption('zapier_api_key');

			if ( ! empty($zapier_api_key) and $zapier_api_key == $_GET['api_key'] )
			{
				exit(json_encode(array('status' => 'success')));					
			}
			else
			{
				http_response_code(401);
				exit(json_encode(array('status' => __('Error. Incorrect API key, check the WP All Export Pro settings page.', 'wp_all_export_plugin'))));
			}						
		}
		else
		{
			http_response_code(401);
			exit(json_encode(array('status' => __('Error. Incorrect API key, check the WP All Export Pro settings page.', 'wp_all_export_plugin'))));
		}		
	}
	if(!empty($_GET['check_connection'])) {
	    exit(json_encode(array('success' => true)));
    }
}