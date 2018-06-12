<?php
function pmxi_wp_ajax_unmerge_file(){
	
	if ( ! check_ajax_referer( 'wp_all_import_secure', 'security', false )){
		exit( json_encode(array('success' => false, 'msg' => __('Security check', 'wp_all_import_plugin'))) );
	}

	if ( ! current_user_can( PMXI_Plugin::$capabilities ) ){
		exit( json_encode(array('success' => false, 'msg' => __('Security check', 'wp_all_import_plugin'))) );
	}

	$input = new PMXI_Input();

	$post = $input->post(array(
		'source' => ''
	));

	PMXI_Plugin::$session = PMXI_Session::get_instance();		
	
	if ( ! empty(PMXI_Plugin::$session->options['nested_files']) and ! empty($post['source'])){

		$nested_files = json_decode(PMXI_Plugin::$session->options['nested_files'], true);

		unset($nested_files[$post['source']]);

		$options = PMXI_Plugin::$session->options;
		$options['nested_files'] = json_encode($nested_files);

		PMXI_Plugin::$session->set('options', $options);

		PMXI_Plugin::$session->save_data();

		exit( json_encode(array(
			'success' => true,
			'nested_files' => $nested_files
		))); 
		die;
	}	

	exit( json_encode(array('success' => false)) ); die;
}