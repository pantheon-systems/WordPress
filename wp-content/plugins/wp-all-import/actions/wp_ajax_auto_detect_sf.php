<?php
function pmxi_wp_ajax_auto_detect_sf(){

	if ( ! check_ajax_referer( 'wp_all_import_secure', 'security', false )){
		exit( json_encode(array('result' => array(), 'msg' => __('Security check', 'wp_all_import_plugin'))) );
	}

	if ( ! current_user_can( PMXI_Plugin::$capabilities ) ){
		exit( json_encode(array('result' => array(), 'msg' => __('Security check', 'wp_all_import_plugin'))) );
	}
	
	$input = new PMXI_Input();
	$fieldName = $input->post('name', '');
	$post_type = $input->post('post_type', 'post');
	global $wpdb;

	$result = array();

	if ($fieldName) {

		if ($post_type == 'import_users'){
			$values = $wpdb->get_results("
				SELECT DISTINCT usermeta.meta_value
				FROM ".$wpdb->usermeta." as usermeta
				WHERE usermeta.meta_key='".$fieldName."'
			", ARRAY_A);	
		}
		else{
			$values = $wpdb->get_results("
				SELECT DISTINCT postmeta.meta_value
				FROM ".$wpdb->postmeta." as postmeta
				WHERE postmeta.meta_key='".$fieldName."'
			", ARRAY_A);	
		}

		if ( ! empty($values) ){
			foreach ($values as $key => $value) {
				if ( ! empty($value['meta_value']) and is_serialized($value['meta_value'])){
					$v = unserialize($value['meta_value']);
					if ( ! empty($v) and is_array($v) ){
						foreach ($v as $skey => $svalue) {
							$result[] = array(
								'key' => $skey,
								'val' => maybe_serialize($svalue),								
							);
						}
						break;
					}										
				}					
			}
		}

	}

	exit( json_encode(array('result' => $result)) );

}