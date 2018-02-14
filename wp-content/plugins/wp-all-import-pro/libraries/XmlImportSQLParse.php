<?php

class PMXI_SQLParser{

	public $xml_path;

	public $_filename;	

	public $targetDir;

	public function __construct($path, $targetDir = false){

		$this->_filename = $path;
		
		$wp_uploads = wp_upload_dir();		

		$this->targetDir = ( ! $targetDir ) ? wp_all_import_secure_file($wp_uploads['basedir'] . DIRECTORY_SEPARATOR . PMXI_Plugin::UPLOADS_DIRECTORY ) : $targetDir;
	}

	public function parse(){		

        $tmpname = wp_unique_filename($this->targetDir, str_replace("sql", "xml", basename($this->_filename)));
        
        $this->xml_path = $this->targetDir  . '/' . wp_all_import_url_title($tmpname);
        
        $this->toXML();

        return $this->xml_path;
	}

	protected function toXML(){

		$fp = fopen($this->_filename, 'rb'); 
		fseek($fp, 0);

		$xmlWriter = new XMLWriter();
	    $xmlWriter->openURI($this->xml_path);
	    $xmlWriter->setIndent(true);
	    $xmlWriter->setIndentString("\t");
	    $xmlWriter->startDocument('1.0', 'UTF-8');
	    $xmlWriter->startElement('data');

		while( ! feof($fp) )
	    {
	        //reset time limit for big files
	        set_time_limit(0);
	        
	        $sql = fread($fp, 1024 * 8);
	        
	        $count = preg_match_all("%INSERT INTO .*;%Uis", $sql, $matches);		

			if ( $count ){

				foreach ($matches[0] as $key => $insert) {
					
					$current_table = 'node';

					$table = preg_match_all("%INTO\s*[^\(].*\(%Uis", $insert, $table_matches);

					if ( $table )
						$current_table = sanitize_key(trim(trim(str_replace('INTO', '', trim($table_matches[0][0],'('))), '`'));
					
					$rawData = array();

					$headers = preg_match_all("%\(.*\)\s*VALUES%Uis", $insert, $headers_matches);

					if ( $headers ){
			 			
			 			foreach ($headers_matches[0] as $key => $found_headers) { 				
			 				$hdrs = explode(',', rtrim(ltrim(trim(rtrim(trim($found_headers), 'VALUES')),'('),')'));  				
			 				if ( ! empty($hdrs) ){
			 					foreach ($hdrs as $header) {
			 						$rawData[ sanitize_key(trim(trim($header), '`')) ] = '';
			 					}
			 				}
			 			} 			

			 			$values = preg_match_all("%\([^`].*\)\s*[,|;]{1}%Uis", $insert, $values_matches);

			 			if ( $values ){ 				
			 				foreach ($values_matches[0] as $key => $value) {
			 					$insertData = array();
			 					$vals = explode(',', rtrim(ltrim(trim(rtrim(rtrim(trim($value), ','),';')),'('),')'));		 					
			 					if ( ! empty($vals) ){
			 						$i = 0;
			 						foreach ($rawData as $r_key => $v) {
			 							foreach ($vals as $k => $val) {
				 							if ($i == $k) $insertData[$r_key] = trim(trim($val),"'");
				 						}
				 						$i++;
			 						} 						
			 					}
			 					if ( ! empty($insertData)){

							    	$xmlWriter->startElement($current_table);
							    		foreach ($insertData as $h => $xml_value) {
							    			$xmlWriter->startElement($h);
								    			$xmlWriter->writeCData($xml_value);   
								    		$xmlWriter->endElement();    	
							    		}    		
							    	$xmlWriter->endElement();    
								    
			 					} 			 					
			 				}
			 			}			 						 			
					}										
				}
			}
	    }
	    fclose($fp);	   

		$xmlWriter->endElement();

		$xmlWriter->flush(true); 

		return true;

	}
}