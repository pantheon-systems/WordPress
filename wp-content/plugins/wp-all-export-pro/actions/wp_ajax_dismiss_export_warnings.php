<?php

function pmxe_wp_ajax_dismiss_export_warnings(){

	if ( ! check_ajax_referer( 'wp_all_export_secure', 'security', false )){
		exit( json_encode(array('html' => __('Security check', 'wp_all_export_plugin'))) );
	}

	if ( ! current_user_can( PMXE_Plugin::$capabilities ) ){
		exit( json_encode(array('html' => __('Security check', 'wp_all_export_plugin'))) );
	}

    $input = new PMXE_Input();

    $post = $input->post('data', false);

    if ( ! empty($post) && ! empty($post['export_id'])){
        $option_name = 'wpae_dismiss_warnings_' . $post['export_id'];
        update_option($option_name, 1);
    }
	
	exit(json_encode(array('result' => true)));
}