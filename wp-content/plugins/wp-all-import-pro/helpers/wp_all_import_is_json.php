<?php

if ( ! function_exists('wp_all_import_is_json')){
	function wp_all_import_is_json($string) {
	 	if (function_exists('json_last_error'))
	 	{
	 		json_decode($string);		 	
		 	switch (json_last_error()) {
		        case JSON_ERROR_NONE:
		            return true;
		        break;
		        case JSON_ERROR_DEPTH:
		            return new WP_Error( 'broke', __( "Maximum stack depth exceeded", "pmxi_plugin" ) );		            
		        break;
		        case JSON_ERROR_STATE_MISMATCH:
		        	return new WP_Error( 'broke', __( "Underflow or the modes mismatch", "pmxi_plugin" ) );		            
		        break;
		        case JSON_ERROR_CTRL_CHAR:
		        	return new WP_Error( 'broke', __( "Unexpected control character found", "pmxi_plugin" ) );		            
		        break;
		        case JSON_ERROR_SYNTAX:
		        	return new WP_Error( 'broke', __( "Syntax error, malformed JSON", "pmxi_plugin" ) );		            
		        break;
		        case JSON_ERROR_UTF8:
		        	return new WP_Error( 'broke', __( "Malformed UTF-8 characters, possibly incorrectly encoded", "pmxi_plugin" ) );		            
		        break;
		        default:
		        	return new WP_Error( 'broke', __( "Unknown json error", "pmxi_plugin" ) );		            
		        break;
		    }	
	 	}	
	 	return true; 		 
	}
}