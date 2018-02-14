<?php

function wp_all_import_is_update_cf( $meta_key, $options ){
	
	if ( $options['update_all_data'] == 'yes') return true;

	if ( ! $options['is_update_custom_fields'] ) return false;			

	if ( $options['update_custom_fields_logic'] == "full_update" ) return true;
	if ( $options['update_custom_fields_logic'] == "only" and ! empty($options['custom_fields_list']) and is_array($options['custom_fields_list']) and in_array($meta_key, $options['custom_fields_list']) ) return true;
	if ( $options['update_custom_fields_logic'] == "all_except" and ( empty($options['custom_fields_list']) or ! in_array($meta_key, $options['custom_fields_list']) )) return true;
	
	return false;
}