<?php
function pmxi_wp_ajax_import_failed(){

	if ( ! check_ajax_referer( 'wp_all_import_secure', 'security', false )){
		exit( json_encode(array('result' => false, 'msg' => __('Security check', 'wp_all_import_plugin'))) );
	}

	if ( ! current_user_can( PMXI_Plugin::$capabilities ) ){
		exit( json_encode(array('result' => false, 'msg' => __('Security check', 'wp_all_import_plugin'))) );
	}
	
	extract($_POST);
	$import = new PMXI_Import_record();
	$import->getbyId($id);
	$result = false;
	if ( ! $import->isEmpty()){
		$import->set(array(
			'executing' => 0,
			'last_activity' => date('Y-m-d H:i:s'),
			'failed' => 1,			
			'failed_on' => date('Y-m-d H:i:s')
		))->save();
		$result = true;
	}
	exit( json_encode( array('result' => $result)));
}