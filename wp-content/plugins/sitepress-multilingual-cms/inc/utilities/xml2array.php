<?php

function icl_xml2array( $contents, $get_attributes = true ) {
	$xml2array = new WPML_XML2Array();

	return $xml2array->get( $contents, $get_attributes );
}