<?php

# only for wp-cli
if ( defined( 'WP_CLI' ) && WP_CLI ) {


###################################################
# extend wp-cli to purge cache, usage: wp fvm purge
###################################################

class fastvelocity_WPCLI {

	# purge files + cache
	public function purge() {
		fvm_purge_all();
		fvm_purge_others();
		WP_CLI::success('FVM and other caches were purged.');
	}
	
	# get cache size
	public function stats() {
		$stats = fastvelocity_get_cachestats();
		WP_CLI::success('FVM is using '.$stats.' for cache.');
	}	
	
}

# add commands
WP_CLI::add_command( 'fvm', 'fastvelocity_WPCLI' );



###################################################
}