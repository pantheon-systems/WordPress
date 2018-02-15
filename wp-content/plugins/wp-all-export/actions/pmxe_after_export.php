<?php

function pmxe_pmxe_after_export($export_id, $export)
{
	if ( ! empty(PMXE_Plugin::$session) and PMXE_Plugin::$session->has_session() )
	{
		PMXE_Plugin::$session->set('file', '');
		PMXE_Plugin::$session->save_data();
	}

	if ( ! $export->isEmpty())
    {

        $export->set(
            array(
                'registered_on' => date('Y-m-d H:i:s'),
            )
        )->save();

		$splitSize = $export->options['split_large_exports_count'];

		$exportOptions = $export->options;
		// remove previously genereted chunks
		if ( ! empty($exportOptions['split_files_list']) and ! $export->options['creata_a_new_export_file'] )
		{
			foreach ($exportOptions['split_files_list'] as $file) {
				@unlink($file);
			}
		}

		$is_secure_import = PMXE_Plugin::getInstance()->getOption('secure');

		if ( ! $is_secure_import)
		{
			$filepath = get_attached_file($export->attch_id);
		}
		else
		{
			$filepath = wp_all_export_get_absolute_path($export->options['filepath']);
		}

		//TODO: Look into what is happening with this variable and what it is used for
		$is_export_csv_headers = apply_filters('wp_all_export_is_csv_headers_enabled', true, $export->id);

        if ( isset($export->options['include_header_row']) ) {
            $is_export_csv_headers = $export->options['include_header_row'];
        }

		$removeHeaders = false;

		$removeHeaders = apply_filters('wp_all_export_remove_csv_headers', $removeHeaders, $export->id);

        // Remove headers row from CSV file
        if ( (empty($is_export_csv_headers) && @file_exists($filepath) && $export->options['export_to'] == 'csv' && $export->options['export_to_sheet'] == 'csv') || $removeHeaders){

            $tmp_file = str_replace(basename($filepath), 'iteration_' . basename($filepath), $filepath);
            copy($filepath, $tmp_file);
            $in  = fopen($tmp_file, 'r');
            $out = fopen($filepath, 'w');

            $headers = fgetcsv($in, 0, XmlExportEngine::$exportOptions['delimiter']);

            if (is_resource($in)) {
                $lineNumber = 0;
                while ( ! feof($in) ) {
                    $data = fgetcsv($in, 0, XmlExportEngine::$exportOptions['delimiter']);
                    if ( empty($data) ) continue;
                    $data_assoc = array_combine($headers, array_values($data));
                    $line = array();
                    foreach ($headers as $header) {
                        $line[$header] = ( isset($data_assoc[$header]) ) ? $data_assoc[$header] : '';
                    }
                    if ( ! $lineNumber && XmlExportEngine::$exportOptions['include_bom']){
                        fwrite($out, chr(0xEF).chr(0xBB).chr(0xBF));
                        fputcsv($out, $line, XmlExportEngine::$exportOptions['delimiter']);
                    }
                    else{
                        fputcsv($out, $line, XmlExportEngine::$exportOptions['delimiter']);
                    }
                    apply_filters('wp_all_export_after_csv_line', $out, XmlExportEngine::$exportID);
                    $lineNumber++;
                }
                fclose($in);
            }
            fclose($out);
            @unlink($tmp_file);
        }	

		// Split large exports into chunks
		if ( $export->options['split_large_exports'] and $splitSize < $export->exported )
		{

			$exportOptions['split_files_list'] = array();							

			if ( @file_exists($filepath) )
			{					

				switch ($export->options['export_to']) 
				{
					case 'xml':

                        require_once PMXE_ROOT_DIR . '/classes/XMLWriter.php';

					    switch ( $export->options['xml_template_type'])
                        {
                            case 'XmlGoogleMerchants':
                            case 'custom':
                                // Determine XML root element
    //                            $main_xml_tag   = false;
    //                            preg_match_all("%<[\w]+[\s|>]{1}%", $export->options['custom_xml_template_header'], $matches);
    //                            if ( ! empty($matches[0]) ){
    //                              $main_xml_tag = preg_replace("%[\s|<|>]%","",array_shift($matches[0]));
    //                            }
                                // Determine XML recond element
                                $record_xml_tag = false;
                                preg_match_all("%<[\w]+[\s|>]{1}%", $export->options['custom_xml_template_loop'], $matches);
                                if ( ! empty($matches[0]) ){
                                  $record_xml_tag = preg_replace("%[\s|<|>]%","",array_shift($matches[0]));
                                }

                                $xml_header = PMXE_XMLWriter::preprocess_xml($export->options['custom_xml_template_header']);
                                $xml_footer = PMXE_XMLWriter::preprocess_xml($export->options['custom_xml_template_footer']);

                            break;

                            default:
                                $main_xml_tag = apply_filters('wp_all_export_main_xml_tag', $export->options['main_xml_tag'], $export->id);
                                $record_xml_tag = apply_filters('wp_all_export_record_xml_tag', $export->options['record_xml_tag'], $export->id);
                                $xml_header = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" . "\n" . "<".$main_xml_tag.">";
                                $xml_footer = "</".$main_xml_tag.">";
                            break;

                        }

						$records_count = 0;
						$chunk_records_count = 0;
						$fileCount = 1;

						$feed = $xml_header;

						$file = new PMXE_Chunk($filepath, array('element' => $record_xml_tag, 'encoding' => 'UTF-8'));
						// loop through the file until all lines are read				    				    			   			   	    			    			    
					    while ($xml = $file->read()) {				    	

					    	if ( ! empty($xml) )
					      	{
								$records_count++;
								$chunk_records_count++;
								$feed .= $xml;								
							}

							if ( $chunk_records_count == $splitSize or $records_count == $export->exported ){
								$feed .= $xml_footer;
								$outputFile = str_replace(basename($filepath), str_replace('.xml', '', basename($filepath)) . '-' . $fileCount++ . '.xml', $filepath);
								file_put_contents($outputFile, $feed);
								if ( ! in_array($outputFile, $exportOptions['split_files_list']))
						        	$exportOptions['split_files_list'][] = $outputFile;
								$chunk_records_count = 0;
								$feed = $xml_header;
							}
						}

						break;
					case 'csv':
						$in = fopen($filepath, 'r');

						$rowCount  = 0;
						$fileCount = 1;
						$headers = fgetcsv($in);
						while (!feof($in)) {
						    $data = fgetcsv($in);
						    if (empty($data)) continue;
						    if (($rowCount % $splitSize) == 0) {
						        if ($rowCount > 0) {
						            fclose($out);
						        }						        
						        $outputFile = str_replace(basename($filepath), str_replace('.csv', '', basename($filepath)) . '-' . $fileCount++ . '.csv', $filepath);
						        if ( ! in_array($outputFile, $exportOptions['split_files_list']))
						        	$exportOptions['split_files_list'][] = $outputFile;

						        $out = fopen($outputFile, 'w');						        
						    }						    
						    if ($data){				
						    	if (($rowCount % $splitSize) == 0) {
						    		fputcsv($out, $headers);
						    	}		    	
						        fputcsv($out, $data);
						    }
						    $rowCount++;
						}
						fclose($in);	
						fclose($out);	

						// convert splitted files into XLS format
						if ( ! empty($exportOptions['split_files_list']) && ! empty($export->options['export_to_sheet']) and $export->options['export_to_sheet'] != 'csv' )
						{
							require_once PMXE_Plugin::ROOT_DIR . '/classes/PHPExcel/IOFactory.php';

							foreach ($exportOptions['split_files_list'] as $key => $file) 
							{
								$objReader = PHPExcel_IOFactory::createReader('CSV');
								// If the files uses a delimiter other than a comma (e.g. a tab), then tell the reader
								$objReader->setDelimiter($export->options['delimiter']);
								// If the files uses an encoding other than UTF-8 or ASCII, then tell the reader
								$objPHPExcel = $objReader->load($file);
                                switch ($export->options['export_to_sheet']){
                                    case 'xls':
                                        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                                        $objWriter->save(str_replace(".csv", ".xls", $file));
                                        $exportOptions['split_files_list'][$key] = str_replace(".csv", ".xls", $file);
                                        break;
                                    case 'xlsx':
                                        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                                        $objWriter->save(str_replace(".csv", ".xlsx", $file));
                                        $exportOptions['split_files_list'][$key] = str_replace(".csv", ".xlsx", $file);
                                        break;
                                }
								@unlink($file);
							}
						}

						break;
					
					default:
						
						break;
				}				

				$export->set(array('options' => $exportOptions))->save();
			}	
		}	

		// convert CSV to XLS
		if ( @file_exists($filepath) and $export->options['export_to'] == 'csv' && ! empty($export->options['export_to_sheet']) and $export->options['export_to_sheet'] != 'csv')
		{			
			
			require_once PMXE_Plugin::ROOT_DIR . '/classes/PHPExcel/IOFactory.php';

			$objReader = PHPExcel_IOFactory::createReader('CSV');
			// If the files uses a delimiter other than a comma (e.g. a tab), then tell the reader
			$objReader->setDelimiter($export->options['delimiter']);
			// If the files uses an encoding other than UTF-8 or ASCII, then tell the reader

			$objPHPExcel = $objReader->load($filepath);

            switch ($export->options['export_to_sheet']) {
                case 'xls':
                    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                    $objWriter->save(str_replace(".csv", ".xls", $filepath));
                    @unlink($filepath);
                    $filepath = str_replace(".csv", ".xls", $filepath);
                    break;
                case 'xlsx':
                    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                    $objWriter->save(str_replace(".csv", ".xlsx", $filepath));
                    @unlink($filepath);
                    $filepath = str_replace(".csv", ".xlsx", $filepath);
                    break;
            }

			$exportOptions = $export->options;
			$exportOptions['filepath'] = wp_all_export_get_relative_path($filepath);
			$export->set(array('options' => $exportOptions))->save();

			$is_secure_import = PMXE_Plugin::getInstance()->getOption('secure');

			if ( ! $is_secure_import ){
				$wp_uploads = wp_upload_dir();
				$wp_filetype = wp_check_filetype(basename($filepath), null );
				$attachment_data = array(
				    'guid' => $wp_uploads['baseurl'] . '/' . _wp_relative_upload_path( $filepath ), 
				    'post_mime_type' => $wp_filetype['type'],
				    'post_title' => preg_replace('/\.[^.]+$/', '', basename($filepath)),
				    'post_content' => '',
				    'post_status' => 'inherit'
				);	
				if ( ! empty($export->attch_id) )
				{
					$attach_id = $export->attch_id;						
					$attachment = get_post($attach_id);
					if ($attachment)
					{
						update_attached_file( $attach_id, $filepath );
						wp_update_attachment_metadata( $attach_id, $attachment_data );	
					}
					else
					{
						$attach_id = wp_insert_attachment( $attachment_data, PMXE_Plugin::$session->file );				
					}
				}
			}

		}

		// make a temporary copy of current file
		if ( empty($export->parent_id) and @file_exists($filepath) and @copy($filepath, str_replace(basename($filepath), '', $filepath) . 'current-' . basename($filepath)))
		{
			$exportOptions = $export->options;
			$exportOptions['current_filepath'] = str_replace(basename($filepath), '', $filepath) . 'current-' . basename($filepath);						
			$export->set(array('options' => $exportOptions))->save();
		}
		
		$generateBundle = apply_filters('wp_all_export_generate_bundle', true);

		if($generateBundle) {

			// genereta export bundle
			$export->generate_bundle();

			if ( ! empty($export->parent_id) )
			{
				$parent_export = new PMXE_Export_Record();
				$parent_export->getById($export->parent_id);
				if ( ! $parent_export->isEmpty() )
				{
					$parent_export->generate_bundle(true);
				}
			}
		}


		// send exported data to zapier.com
		$subscriptions = get_option('zapier_subscribe', array());		
		if ( ! empty($subscriptions) and empty($export->parent_id))
		{			

			$wp_uploads = wp_upload_dir();

			$fileurl = str_replace($wp_uploads['basedir'], $wp_uploads['baseurl'], $filepath);		

			$response = array( 				
				'website_url' => home_url(),
				'export_id' => $export->id, 
				'export_name' => $export->friendly_name,
				'file_name' => basename($filepath),
				'file_type' => wp_all_export_get_export_format($export->options),
				'post_types_exported' => empty($export->options['cpt']) ? $export->options['wp_query'] : implode($export->options['cpt'], ','),
				'export_created_date' => $export->registered_on,
				'export_last_run_date' => date('Y-m-d H:i:s'),
				'export_trigger_type' => empty($_GET['export_key']) ? 'manual' : 'cron',
				'records_exported' => $export->exported,
				'export_file' => ''
			);

			if (file_exists($filepath))
			{
				$response['export_file_url'] = $fileurl;
				$response['status'] = 200;
				$response['message'] = 'OK';	
			}
			else
			{
				$response['export_file_url'] = '';
				$response['status'] = 300;
				$response['message'] = 'File doesn\'t exist';	
			}

			$response = apply_filters('wp_all_export_zapier_response', $response);

			foreach ($subscriptions as $zapier) 
			{
				if (empty($zapier['target_url'])) continue;

				wp_remote_post( $zapier['target_url'], array(
					'method' => 'POST',
					'timeout' => 45,
					'redirection' => 5,
					'httpversion' => '1.0',
					'blocking' => true,
					'headers' => array(
							'Content-Type' => 'application/json'
						),
					'body' => "[".json_encode($response)."]",
					'cookies' => array()
				    )
				);
			}			
		}

		// clean session 
		if ( ! empty(PMXE_Plugin::$session) and PMXE_Plugin::$session->has_session() )
		{
			PMXE_Plugin::$session->clean_session( $export->id );				
		}
	}	
}