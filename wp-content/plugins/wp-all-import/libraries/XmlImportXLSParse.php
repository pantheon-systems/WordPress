<?php

class PMXI_XLSParser{

	public $csv_path;	

	public $_filename;	

	public $targetDir;

	public $xml;

	public function __construct($path, $targetDir = false){

		$this->_filename = $path;
		
		$wp_uploads = wp_upload_dir();		

		$this->targetDir = ( ! $targetDir ) ? wp_all_import_secure_file($wp_uploads['basedir'] . DIRECTORY_SEPARATOR . PMXI_Plugin::UPLOADS_DIRECTORY ) : $targetDir;
	}

	public function parse(){		

        $tmpname = wp_unique_filename($this->targetDir,  preg_replace('%\W(xls|xlsx)$%i', ".csv", basename($this->_filename)));
        
        $this->csv_path = $this->targetDir  . '/' . wp_all_import_url_title($tmpname);               

        return $this->toXML();
	}

	protected function toXML(){

		include_once( PMXI_Plugin::ROOT_DIR . '/classes/PHPExcel/IOFactory.php' );

		$objPHPExcel = PHPExcel_IOFactory::load($this->_filename);

        $objPHPExcel = apply_filters('wp_all_import_phpexcel_object', $objPHPExcel, $this->_filename);

        $PHPExcelDelimiter = ",";
        $PHPExcelDelimiter = apply_filters('wp_all_import_phpexcel_delimiter', $PHPExcelDelimiter, $this->_filename);

		$objWriter   = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV')->setDelimiter($PHPExcelDelimiter)
                                                          ->setEnclosure('"')
                                                          ->setLineEnding("\r\n")
                                                          ->setSheetIndex(0)
                                                          ->save($this->csv_path);	

        include_once(PMXI_Plugin::ROOT_DIR . '/libraries/XmlImportCsvParse.php');

        $this->xml = new PMXI_CsvParser( array( 'filename' => $this->csv_path, 'targetDir' => $this->targetDir ) );

        @unlink($this->csv_path);

		return $this->xml->xml_path;

	}
}