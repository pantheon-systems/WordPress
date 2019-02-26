<?php
//Remove "user has seen this menu" records.
if ( defined('ABSPATH') && defined('WP_UNINSTALL_PLUGIN') ) {
	delete_metadata('user', 0, 'ws_nmh_seen_menus', null, true);
}