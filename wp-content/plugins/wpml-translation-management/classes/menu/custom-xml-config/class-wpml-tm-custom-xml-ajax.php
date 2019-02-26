<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TM_Custom_XML_AJAX extends WPML_TM_AJAX {
	const AJAX_ACTION_BASE = 'wpml-tm-custom-xml';

	private $custom_xml;
	private $reload_config_callback;
	private $validate;

	function __construct( WPML_Custom_XML $custom_xml, WPML_XML_Config_Validate $validate, $reload_config_callback = null ) {
		$this->custom_xml             = $custom_xml;
		$this->validate               = $validate;
		$this->reload_config_callback = $reload_config_callback;
	}

	function validate_content() {
		if ( $this->is_valid_request() ) {
			$content = $this->get_content();

			if ( $content && ! $this->validate->from_string( $content ) && $this->validate->get_errors() ) {
				$xml_errors = $this->validate->get_errors();
				foreach ( $xml_errors as $index => $xml_error ) {
					if ( 'The document has no document element.' === trim( $xml_error->message ) ) {
						unset( $xml_errors[ $index ] );
					}
				}
				$errors = array( __( 'The XML is not valid:', 'wpml-translation-management' ), $xml_errors );
				wp_send_json_error( $errors );
			} else {
				wp_send_json_success( __( 'The XML is valid.', 'wpml-translation-management' ) );
			}
		}
	}

	function save_content() {
		if ( $this->is_valid_request() ) {
			$content = $this->get_content();

			if ( null !== $content ) {
				$this->custom_xml->set( $content );
				if ( $this->reload_config_callback ) {
					call_user_func( $this->reload_config_callback );
				}
				wp_send_json_success( __( 'The XML has been saved.', 'wpml-translation-management' ) );
			} else {
				wp_send_json_error( __( 'The XML could not be saved.', 'wpml-translation-management' ) );
			}
		}
	}

	private function get_content() {
		$content = null;

		if ( array_key_exists( 'content', $_POST ) ) {
			$content = stripcslashes( $_POST['content'] );
		}

		return $content;
	}
}
