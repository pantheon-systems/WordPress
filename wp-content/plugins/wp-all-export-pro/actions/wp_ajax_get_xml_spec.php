<?php

function pmxe_wp_ajax_get_xml_spec(){

	if ( ! check_ajax_referer( 'wp_all_export_secure', 'security', false )){
		exit( json_encode(array('html' => __('Security check', 'wp_all_export_plugin'))) );
	}

	if ( ! current_user_can( PMXE_Plugin::$capabilities ) ){
		exit( json_encode(array('html' => __('Security check', 'wp_all_export_plugin'))) );
	}

    require_once PMXE_ROOT_DIR . '/libraries/XmlSpec.php';

    $input = new PMXE_Input();

    $export_id = $input->get('id', 0);
    if (empty($export_id))
    {
      $export_id = ( ! empty(PMXE_Plugin::$session->update_previous)) ? PMXE_Plugin::$session->update_previous : 0;
    }

    $spec_class = $input->post('spec_class', false);

    $spec = new XmlSpec( $spec_class,  $export_id);

    if ( $spec->xml ){

      $fields = $spec->xml->get_required_fields();

      exit(json_encode(array('result' => true, 'fields' => $fields))); die;
    }

	exit(json_encode(array('result' => false, 'msg' => __('Specification not found.', 'wp_all_export_plugin')))); die;
}