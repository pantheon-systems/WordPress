<?php

/**
 * @author OnTheGo Systems
 */
class WPML_Queried_Object_Factory {
	public function create() {
		return new WPML_Queried_Object( $this->get_sitepress() );
	}

	private function get_sitepress() {
		global $sitepress;

		return $sitepress;
	}
}
