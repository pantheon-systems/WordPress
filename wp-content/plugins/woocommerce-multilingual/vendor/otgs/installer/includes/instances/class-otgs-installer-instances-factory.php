<?php

class OTGS_Installer_Instances_Factory {

	public function create() {
		global $wp_installer_instances;

		return new OTGS_Installer_Instances( $wp_installer_instances );
	}
}