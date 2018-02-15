<?php

function pmxe_wp_ajax_generate_zapier_api_key(){

	if ( ! check_ajax_referer( 'wp_all_export_secure', 'security', false )){
		exit( json_encode(array('html' => __('Security check', 'wp_all_export_plugin'))) );
	}

	if ( ! current_user_can( PMXE_Plugin::$capabilities ) ){
		exit( json_encode(array('html' => __('Security check', 'wp_all_export_plugin'))) );
	}

	$api_key = wp_all_export_rand_char(32);

	PMXE_Plugin::getInstance()->updateOption('zapier_api_key', $api_key);
	
	exit(json_encode(array('api_key' => $api_key)));
}