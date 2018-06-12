<?php
function pmxi_wp_ajax_delete_import(){

	if ( ! check_ajax_referer( 'wp_all_import_secure', 'security', false )){
		exit( json_encode(array('result' => false, 'msg' => __('Security check', 'wp_all_import_plugin'))) );
	}

	if ( ! current_user_can( PMXI_Plugin::$capabilities ) ){
		exit( json_encode(array('result' => false, 'msg' => __('Security check', 'wp_all_import_plugin'))) );
	}
	
	$input = new PMXI_Input();

	$post = $input->post(array(
		'data' => '',
        'iteration' => 1
	));		

//	$get  = $input->get(array(
//		'iteration' => 1
//	));

	$params = array();
	parse_str($post['data'], $params);

	$response = array(
		'result' => false,
		'msg' => ''		
	);	

	if ( $params['is_delete_import'] and ! $params['is_delete_posts'] )
	{
		$response['redirect'] = add_query_arg('pmxi_nt', urlencode(__('Import deleted', 'wp_all_import_plugin')), $params['base_url']);
	}
	elseif( ! $params['is_delete_import'] and $params['is_delete_posts'])
	{
		$response['redirect'] = add_query_arg('pmxi_nt', urlencode(__('All associated posts deleted.', 'wp_all_import_plugin')), $params['base_url']);
	}
	elseif( $params['is_delete_import'] and $params['is_delete_posts'])
	{
		$response['redirect'] = add_query_arg('pmxi_nt', urlencode(__('Import and all associated posts deleted.', 'wp_all_import_plugin')), $params['base_url']);
	}
	else
	{
		$response['redirect'] = add_query_arg('pmxi_nt', urlencode(__('Nothing to delete.', 'wp_all_import_plugin')), $params['base_url']);
		exit( json_encode( $response ));
	}


	if ( ! empty($params['import_ids']))
	{
		foreach ($params['import_ids'] as $key => $id) {
			$import = new PMXI_Import_Record();
			$import->getById($id);			
			if ( ! $import->isEmpty() )
			{
				if ((int) $post['iteration'] === 1)
				{
					$import->set(array(
						'deleted' => 0						
					))->update();					
				}				

				$is_all_records_deleted = $import->deletePostsAjax( ! $params['is_delete_posts'], $params['is_delete_images'], $params['is_delete_attachments'] );

				$response['result'] = (empty($params['import_ids'][$key + 1])) ? $is_all_records_deleted : false;
				$response['msg']    = sprintf(__('Import #%d - %d records deleted', 'wp_all_import_plugin'), $import->id, $import->deleted);

				if ( $is_all_records_deleted === true )
				{
					$import->delete( ! $params['is_delete_posts'], $params['is_delete_images'], $params['is_delete_attachments'], $params['is_delete_import'] );
				}
			}				
		}
	}	
	
	exit( json_encode( $response ));
}