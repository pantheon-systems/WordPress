<?php

if ( ! class_exists('PMXI_Nested')){

	class PMXI_Nested{
		protected $nested_files;
		protected $xpath;
		protected $dom;
		protected $elements;
		public $xml;

		public function __construct( $dom, $nested_files, $xml, $xpath, $elements = false){
			$this->dom = $dom;
			$this->nested_files = $nested_files;
			$this->xpath = $xpath;
			$this->xml = $xml;
			$this->elements = $elements;
		}

		public function merge(){

			/* Merge nested XML/CSV files */		
			if ( ! empty($this->nested_files) ){				
				$tmp_files = array();
				foreach ($this->nested_files as $key => $nfile) {
					$nested_fileURL = array_shift(XmlImportParser::factory($this->xml, $this->xpath, $nfile, $tmp_file)->parse()); $tmp_files[] = $tmp_file;						
					if ( ! empty($nested_fileURL) ){
						$errors = new WP_Error();

						$uploader = new PMXI_Upload($nested_fileURL, $errors);
						$upload_result = $uploader->url();
						
						if ($upload_result instanceof WP_Error){
							$errors = $upload_result;
						}
						else{				
							$source    = $upload_result['source'];
							$filePath  = $upload_result['filePath'];				
							if ( ! empty($upload_result['root_element'])) 
								$root_element = $upload_result['root_element'];
							else
								$root_element = '';
							$feed_type = $upload_result['feed_type'];
						}	

						unset($uploader);						

						$nested_xml = file_get_contents($filePath);
								      						    					    					    			    					    					    
				    	if ( ! empty($nested_xml) )
				      	{
				      		PMXI_Import_Record::preprocessXml($nested_xml);								
							
							if ( PMXI_Import_Record::validateXml($nested_xml) === true ){

					      		$nestedDom = new DOMDocument('1.0', 'UTF-8');
					      		$nestedold = libxml_use_internal_errors(true);
								$nestedDom->loadXML($nested_xml);				
								libxml_use_internal_errors($nestedold);
								$second = $nestedDom->documentElement;

								if ($second->hasChildNodes()) {
									
									foreach($second->childNodes as $node)
							        {
							           $importNode = $this->dom->importNode($node, true);
							           $this->dom->documentElement->appendChild($importNode);							           
							        }
							        
						        	$this->xml = ($this->elements) ? $this->dom->saveXML($this->elements->item(0)) : $this->dom->saveXML();
						        }
								unset($nestedDom);
							}
				      	}					    
					}
				}					
				foreach ($tmp_files as $tmp_file) { // remove all temporary files created
					@unlink($tmp_file);
				}	
			}
		}

		public function get_xml(){
			return $this->xml;
		}

	}
}