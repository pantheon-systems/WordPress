<?php

function pmxi_wp_ajax_nested_xpath(){

	if ( ! check_ajax_referer( 'wp_all_import_secure', 'security', false )){
		exit( json_encode(array('result' => array(), 'msg' => __('Security check', 'wp_all_import_plugin'))) );
	}

	if ( ! current_user_can( PMXI_Plugin::$capabilities ) ){
		exit( json_encode(array('result' => array(), 'msg' => __('Security check', 'wp_all_import_plugin'))) );
	}
	
	extract($_POST);

	$result = array();

	if ( @file_exists($filePath) ){		

		$file = new PMXI_Chunk($filePath, array('element' => $root_element, 'encoding' => 'UTF-8'));

		$tagno = 0;
		$loop  = 0;
		$count = 0;
		$xml_tree = '';
		
	    while ($xml = $file->read()) {

	    	if ( ! empty($xml) )
	      	{								      		
	      		PMXI_Import_Record::preprocessXml($xml);
	      		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" . "\n" . $xml;				      						      						      		
	    					    	
		      	if ( '' != $customXpath){
			      	$dom = new DOMDocument('1.0', 'UTF-8');
					$old = libxml_use_internal_errors(true);
					$dom->loadXML($xml);
					libxml_use_internal_errors($old);
					$xpath = new DOMXPath($dom);									
					if (($elements = $xpath->query($customXpath)) and $elements->length){							
						$loop++;
						$count += $elements->length;
						if ( ! $tagno or $loop == $tagno ){
							ob_start();
							PMXI_Render::render_xml_element($elements->item(0), true);
							$xml_tree = ob_get_clean();
							$tagno = 1;
						}
					}										
				}
				else {
					exit(json_encode(array('success' => false, 'msg' => __('XPath is required', 'wp_all_import_plugin'))));
					die;
				}
		    }
		}
		unset($file);					
	}
	else{
		exit(json_encode(array('success' => false, 'msg' => 'File path is required', 'wp_all_import_plugin'))); die;		
	}

	exit(json_encode(array(		
		'success' => true,		
		'xml_tree' => $xml_tree,		
		'count' => (($count) ? sprintf("<p class='green pmxi_counter'>" . __('Elements found', 'pmxi_pligun') . " <strong>%s</strong></p>", $count) : "<p class='red pmxi_counter'>" . __('Elements not found', 'pmxi_pligun') . "</p>")
	))); die;

}