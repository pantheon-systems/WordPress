<?php

class OTGS_Installer_Repository_Factory {

	public function create_repository( $params ) {
		return new OTGS_Installer_Repository( $params );
	}

	public function create_package( $params ) {
		return new OTGS_Installer_Package( $params );
	}

	public function create_product( $params ) {
		return new OTGS_Installer_Package_Product( $params );
	}
}