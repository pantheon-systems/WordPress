<?php

/**
 * @author OnTheGo Systems
 */
class WPML_XML_Config_Validate {
	private $errors = array();
	private $path_to_xsd;

	function __construct( $path_to_xsd = null ) {
		$this->path_to_xsd = $path_to_xsd ? realpath( $path_to_xsd ) : null;
	}

	public function get_errors() {
		return $this->errors;
	}

	/**
	 * @param string $file_full_path
	 *
	 * @return bool
	 */
	function from_file( $file_full_path ) {
		$this->errors = array();

		$xml = file_get_contents( $file_full_path );

		return $this->from_string( $xml );
	}

	/**
	 * @param string $xml
	 *
	 * @return bool
	 */
	function from_string( $xml ) {
		if ( '' === preg_replace( '/(\W)+/', '', $xml ) ) {
			return false;
		}

		$this->errors = array();

		libxml_use_internal_errors( true );

		$xml_object = $this->get_xml( $xml );
		if ( $this->path_to_xsd && ! $xml_object->schemaValidate( $this->path_to_xsd ) ) {
			$this->errors = libxml_get_errors();
		}

		libxml_clear_errors();

		return ! $this->errors;
	}

	/**
	 * @param string $content The string representation of the XML file
	 *
	 * @return DOMDocument
	 */
	private function get_xml( $content ) {
		$xml = new DOMDocument();
		$xml->loadXML( $content );

		return $xml;
	}
}