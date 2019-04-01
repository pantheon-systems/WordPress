<?php

class OTGS_Installer_Plugin_Factory {

	/**
	 * @param array $params
	 *
	 * @return OTGS_Installer_Plugin
	 */
	public function create( array $params = array() ) {
		return new OTGS_Installer_Plugin( $params );
	}
}