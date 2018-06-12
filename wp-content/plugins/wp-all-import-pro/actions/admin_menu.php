<?php
/**
 * Register plugin specific admin menu
 */

function pmxi_admin_menu() {
	global $menu, $submenu;
	
	if (current_user_can(PMXI_Plugin::$capabilities)) { // admin management options
		
		$wpai_menu = array(
			array('pmxi-admin-import',  __('New Import', 'wp_all_import_plugin')),
			array('pmxi-admin-manage' ,  __('Manage Imports', 'wp_all_import_plugin')),
			array('pmxi-admin-settings',  __('Settings', 'wp_all_import_plugin')),
			//array('pmxi-admin-license',  __('Licenses', 'wp_all_import_plugin')),
			array('pmxi-admin-history',  __('History', 'wp_all_import_plugin'))
			/*array('pmxi-admin-addons',  __('Add-ons', 'wp_all_import_plugin')), */			
		);

		$wpai_menu = apply_filters('pmxi_admin_menu', $wpai_menu);		

		add_menu_page(__('WP All Import', 'wp_all_import_plugin'), __('All Import', 'wp_all_import_plugin'), PMXI_Plugin::$capabilities, 'pmxi-admin-home', array(PMXI_Plugin::getInstance(), 'adminDispatcher'), PMXI_Plugin::ROOT_URL . '/static/img/xmlicon.png', 112);
		// workaround to rename 1st option to `Home`
		$submenu['pmxi-admin-home'] = array();

		foreach ($wpai_menu as $key => $value) {
			add_submenu_page('pmxi-admin-home', $value[1], $value[1], PMXI_Plugin::$capabilities, $value[0], array(PMXI_Plugin::getInstance(), 'adminDispatcher'));	
		}
		
	}	
}

