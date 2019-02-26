<?php
if( !function_exists('wpai_util_map') ){

	function wpai_util_map( $orig, $change, $source ){
  		
  		$orig = html_entity_decode($orig);
  		$change = html_entity_decode($change);
  		$source = html_entity_decode($source);
  		$original_array = array_map('trim',explode(',',$orig));
  
  		if ( empty($original_array) ) return "";
  
  		$change_array = array_map('trim',explode(',',$change));
  
  		if ( empty($change_array) or count($original_array) != count($change_array)) return ""; 
   
  		if( count($change_array) == count($original_array) ){
   			$replacement = array();
   			foreach ($original_array as $key => $el){
    			$replacement[$el] = $change_array[$key];
   			}
   			$result = strtr($source,$replacement);
  		}
  		return $result;

 	}

}