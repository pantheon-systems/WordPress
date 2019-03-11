<?php

class OTGS_Installer_Repositories_Factory {

	public function create( WP_Installer $installer ) {
		return new OTGS_Installer_Repositories( $installer, new OTGS_Installer_Repository_Factory(), new OTGS_Installer_Subscription_Factory() );
	}
}