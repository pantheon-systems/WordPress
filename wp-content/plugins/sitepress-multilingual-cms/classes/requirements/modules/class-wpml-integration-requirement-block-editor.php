<?php

class WPML_Integration_Requirements_Block_Editor implements IWPML_Integration_Requirements_Module {

	/** @var WPML_Requirements $requirements */
	private $requirements;

	public function __construct( WPML_Requirements $requirements ) {
		$this->requirements = $requirements;
	}

	public function get_requirements() {
		if ( $this->requirements->is_plugin_active( 'wpml-translation-management' ) ) {
			return array(
				'wpml-string-translation',
			);
		}

		return array();
	}
}