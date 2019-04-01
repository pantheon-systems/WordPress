<?php

class OTGS_Installer_Support_Template_Factory {

	private $installer_path;

	public function __construct( $installer_path ) {
		$this->installer_path = $installer_path;
	}

	/**
	 * @return OTGS_Installer_Support_Template
	 */
	public function create() {
		$template_service_loader = new OTGS_Installer_Twig_Template_Service_Loader( array(
			$this->installer_path . '/templates/support/',
		) );

		$instances_factory = new OTGS_Installer_Instances_Factory();

		return new OTGS_Installer_Support_Template(
			$template_service_loader->get_service(),
			new OTGS_Installer_Logger_Storage( new OTGS_Installer_Log_Factory() ),
			new OTGS_Installer_Requirements(),
			$instances_factory->create()
		);
	}
}