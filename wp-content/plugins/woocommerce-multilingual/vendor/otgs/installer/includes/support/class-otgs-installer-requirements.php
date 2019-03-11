<?php

class OTGS_Installer_Requirements {

	private $requirements;

	public function get() {
		if ( ! $this->requirements ) {
			$this->requirements = $this->get_requirements();
		}

		return $this->requirements;
	}

	private function get_requirements() {
		return array(
			array(
				'name'   => 'cURL',
				'active' => function_exists( 'curl_version' ),
			),
			array(
				'name'   => 'simpleXML',
				'active' => extension_loaded( 'simplexml' ),
			),
		);
	}
}