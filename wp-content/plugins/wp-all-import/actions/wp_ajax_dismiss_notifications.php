<?php
function pmxi_wp_ajax_dismiss_notifications(){

	if ( ! check_ajax_referer( 'wp_all_import_secure', 'security', false )){
		exit( json_encode(array('result' => false, 'msg' => __('Security check', 'wp_all_import_plugin'))) );
	}

	if ( ! current_user_can( PMXI_Plugin::$capabilities ) ){
		exit( json_encode(array('result' => false, 'msg' => __('Security check', 'wp_all_import_plugin'))) );
	}
	
	if (isset($_POST['addon']) ) {
		update_option($_POST['addon'] . '_notice_ignore', 'true');
	}
	exit( json_encode( array('result' => true)));
}