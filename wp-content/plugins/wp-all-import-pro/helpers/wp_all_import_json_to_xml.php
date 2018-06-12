<?php

function wp_all_import_json_to_xml( $json = array() ){	

	return PMXI_ArrayToXML::toXml($json);
	
}

