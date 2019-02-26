<?php

if ( ! function_exists('wp_all_import_get_reader_engine')){

	function wp_all_import_get_reader_engine( $local_paths, $post, $import_id = 0 ) {

		$xml_reader_engine = 'xmlreader';
		
		$is_auto_detect_xml_reader = apply_filters( 'wp_all_import_auto_detect_reader_engine', true );

		// auto detect xml reader engine disabled
		if ( $is_auto_detect_xml_reader === false ) 
		{
			update_option('wpai_parser_type', 'xmlreader');
			
			return false;
		}		

    	$root_element = '';    	    	

		// auto-detect XML reader engine
		foreach ($local_paths as $key => $path) {						
												
			if ( @file_exists($path) ){							

				$file = new PMXI_Chunk( $path, array('element' => $post['root_element']), 'xmlreader' );

				$xmlreader_count = 0;

				if ( ! empty($file->options['element']) ) {		

					$root_element = $file->options['element'];				
					
					$xpath = "/" . $file->options['element'];

					$start_time = time();

					// loop through the file until all lines are read				    				    			   				    
				    while ($xml = $file->read()) {

				    	if ( ! empty($xml) )
				      	{				      		
				      		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" . "\n" . $xml;
					      	
					      	$dom = new DOMDocument('1.0', 'UTF-8');
							$old = libxml_use_internal_errors(true);
							$dom->loadXML($xml);
							libxml_use_internal_errors($old);
							$dxpath = new DOMXPath($dom);
							
							if (($elements = @$dxpath->query($xpath)) and $elements->length){										
								$xmlreader_count += $elements->length;										
								unset($dom, $dxpath, $elements);		
							}
					    }

					    $execution_time = time() - $start_time;

					    // if stream reader takes longer than 30 seconds just stop using it
					    // if ( $execution_time > 30 ) {
					    // 	break;
					    // }
					}
				}

				unset($file);

				// count element using xml streamer

				if ( ! empty($post['root_element'])) $root_element = $post['root_element'];

				$file = new PMXI_Chunk( $path, array('element' => $root_element), 'xmlstreamer' );

				$xmlstreamer_count = 0;

				if ( ! empty($file->options['element']) ) {						
					
					$xpath = "/" . $file->options['element'];

					$start_time = time();

					// loop through the file until all lines are read				    				    			   				    
				    while ($xml = $file->read()) {

				    	if ( ! empty($xml) )
				      	{				      		
				      		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" . "\n" . $xml;
					      	
					      	$dom = new DOMDocument('1.0', 'UTF-8');
							$old = libxml_use_internal_errors(true);
							$dom->loadXML($xml);
							libxml_use_internal_errors($old);
							$dxpath = new DOMXPath($dom);
							
							if (($elements = @$dxpath->query($xpath)) and $elements->length){										
								$xmlstreamer_count += $elements->length;										
								unset($dom, $dxpath, $elements);		
							}
					    }
					    
					    $execution_time = time() - $start_time;

					    // if stream reader takes longer than 30 seconds just stop using it
					    // if ( $execution_time > 30 ) {
					    // 	break;
					    // }
					}
				}

				unset($file);

				$xml_reader_engine = ($xmlreader_count >= $xmlstreamer_count) ? 'xmlreader' : 'xmlstreamer';				

				update_option('wpai_parser_type', $xml_reader_engine);
			}
		}

		return $root_element;
	}
}