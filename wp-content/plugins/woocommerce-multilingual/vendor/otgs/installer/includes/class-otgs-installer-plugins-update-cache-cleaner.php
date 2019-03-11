<?php

class OTGS_Installer_Plugins_Update_Cache_Cleaner {

	public function add_hooks() {
		add_action( 'otgs_installer_clean_plugins_update_cache', array( $this, 'clean_plugins_update_cache' ) );
	}

	public function clean_plugins_update_cache() {
		delete_site_transient( 'update_plugins' );
	}
}