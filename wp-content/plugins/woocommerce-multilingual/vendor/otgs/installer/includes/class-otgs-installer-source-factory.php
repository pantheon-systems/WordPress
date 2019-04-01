<?php

class OTGS_Installer_Source_Factory {

	public function create() {
		WP_Filesystem();

		global $wp_filesystem;

		return new OTGS_Installer_Source( WP_Installer(), $wp_filesystem );
	}
}