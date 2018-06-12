<?php
/**
*
*	Ajax action that will parse nested XML/CSV files
*
*/
function pmxi_wp_ajax_parse_nested_file(){

	if ( ! check_ajax_referer( 'wp_all_import_secure', 'security', false )){
		exit( json_encode(array('result' => array(), 'msg' => __('Security check', 'wp_all_import_plugin'))) );
	}

	if ( ! current_user_can( PMXI_Plugin::$capabilities ) ){
		exit( json_encode(array('result' => array(), 'msg' => __('Security check', 'wp_all_import_plugin'))) );
	}

	extract($_POST);

	$result = array();
	
	$wp_uploads = wp_upload_dir();	

	if ( ! empty($_POST['nested_type']) ){

		$root_element = '';
		$feed_type = '';
		$errors = new WP_Error();

		switch ($_POST['nested_type']){

			case 'upload':

				$uploader = new PMXI_Upload($_POST['nested_filepath'], $errors);			
				$upload_result = $uploader->upload();
				
				if ($upload_result instanceof WP_Error){
					$errors = $upload_result;
				}
				else{					
					$source    = $upload_result['source'];
					$filePath  = $upload_result['filePath'];
					if ( ! empty($upload_result['root_element'])) 
						$root_element = $upload_result['root_element'];
				}	

				break;

			case 'url':

				$uploader = new PMXI_Upload($_POST['nested_url'], $errors);			
				$upload_result = $uploader->url();
				
				if ($upload_result instanceof WP_Error){
					$errors = $upload_result;
				}
				else{				
					$source    = $upload_result['source'];
					$filePath  = $upload_result['filePath'];				
					if ( ! empty($upload_result['root_element'])) 
						$root_element = $upload_result['root_element'];
					$feed_type = $upload_result['feed_type'];
				}	

				break;

			case 'file':

				$uploader = new PMXI_Upload($_POST['nested_file'], $errors);			
				$upload_result = $uploader->file();
				
				if ($upload_result instanceof WP_Error){
					$errors = $upload_result;
				}
				else{				
					$source    = $upload_result['source'];
					$filePath  = $upload_result['filePath'];				
					if ( ! empty($upload_result['root_element'])) 
						$root_element = $upload_result['root_element'];				
				}

				break;
		}
	}

	if ( $errors->get_error_codes() )
	{
		$msgs = $errors->get_error_messages();
		ob_start();
		?>
		<?php foreach ($msgs as $msg): ?>
			<div class="error"><p><?php echo $msg ?></p></div>
		<?php endforeach ?>
		<?php
		exit(json_encode(array(		
			'success' => false,
			'errors'  => ob_get_clean()
		))); die;
	}
	else
	{

		$xml_tree = '';

		if ( @file_exists($filePath) ){

			$file = new PMXI_Chunk($filePath, array('element' => $root_element));

			if ( ! empty($file->options['element']) ) {						
				$customXpath = "/".$file->options['element'];
				$elements_cloud = $file->cloud;																															    		  
			}	
					
			$root_element = $file->options['element'];

			$file = new PMXI_Chunk($filePath, array('element' => $root_element, 'encoding' => 'UTF-8'));

			$tagno = 0;
			$loop  = 0;
			$count = 0;
			
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
					else break;
			    }
			}
			unset($file);					
		}

		exit(json_encode(array(		
			'success' => true,
			'source' => $source,
			'realpath' => $source['path'],
			'filePath' => $filePath,
			'root_element' => $root_element,
			'xml_tree' => $xml_tree,
			'xpath' => $customXpath,
			'count' => (($count) ? sprintf("<p class='green pmxi_counter'>" . __('Elements found', 'pmxi_pligun') . " <strong>%s</strong></p>", $count) : "<p class='red pmxi_counter'>" . __('Elements not found', 'pmxi_pligun') . "</p>")
		))); die;
	}

}