<?php

/**
 * @author W-Shadow 
 * @copyright 2015
 *
 * The uninstallation script.
 */

if( defined( 'ABSPATH') && defined('WP_UNINSTALL_PLUGIN') ) {

	//Remove the plugin's settings
	delete_option('ws_menu_editor');
	if ( function_exists('delete_site_option') ){
		delete_site_option('ws_menu_editor');
	}
	
	//Remove hint visibility flags
    if ( function_exists('delete_metadata') ) {
        delete_metadata('user', 0, 'ame_show_hints', '', true);
    }

	//Remove module settings.
	delete_option('ws_ame_plugin_visibility');
	delete_option('ws_ame_dashboard_widgets');
	if ( function_exists('delete_site_option') ){
		delete_site_option('ws_ame_plugin_visibility');
		delete_site_option('ws_ame_hide_pv_notice');
		delete_site_option('ws_ame_dashboard_widgets');
	}
}