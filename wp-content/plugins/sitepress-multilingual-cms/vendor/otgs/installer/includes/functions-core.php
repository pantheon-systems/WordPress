<?php
function WP_Installer(){
	return WP_Installer::instance();
}

function WP_Installer_Channels(){
	return WP_Installer_Channels::instance();
}

function get_OTGS_Installer_Factory() {
	static $installer_factory;

	if ( ! $installer_factory ) {
		$installer_factory = new OTGS_Installer_Factory( WP_Installer() );
	}

	return $installer_factory;
}