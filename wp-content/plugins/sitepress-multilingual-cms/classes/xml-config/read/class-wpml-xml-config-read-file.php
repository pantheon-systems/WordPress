<?php

/**
 * @author OnTheGo Systems
 */
class WPML_XML_Config_Read_File implements WPML_XML_Config_Read {
	private $file_full_path;
	private $transform;
	private $validate;

	function __construct( $file_full_path, WPML_XML_Config_Validate $validate, WPML_XML_Transform $transform ) {
		$this->file_full_path = $file_full_path;
		$this->validate       = $validate;
		$this->transform      = $transform;
	}

	function get() {
		if ( file_exists( $this->file_full_path ) && $this->validate->from_file( $this->file_full_path ) ) {
			$xml = file_get_contents( $this->file_full_path );

			return $this->transform->get( $xml );
		}

		return null;
	}
}
