<?php

if ( ! function_exists('wp_all_import_xml2array')){
	function wp_all_import_xml2array( $xmlObject, $out = array () ) {
	 	foreach ( (array) $xmlObject as $index => $node )
        $out[$index] = ( is_object ( $node ) ) ? wp_all_import_xml2array ( $node ) : $node;

    	return $out;		 	
	}
}