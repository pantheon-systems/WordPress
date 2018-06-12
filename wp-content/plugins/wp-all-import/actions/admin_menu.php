<?php
/**
 * Register plugin specific admin menu
 */

function pmxi_admin_menu() {
	global $menu, $submenu;
	
	if (current_user_can( PMXI_Plugin::$capabilities )) { // admin management options
		
		$wpai_menu = array(
			array('pmxi-admin-import',  __('New Import', 'pmxi_plugin')),
			array('pmxi-admin-manage' ,  __('Manage Imports', 'pmxi_plugin')),
			array('pmxi-admin-settings',  __('Settings', 'pmxi_plugin')),
			array('pmxi-admin-help',  __('Support', 'pmxi_plugin')),
			array('pmxi-admin-history',  __('History', 'pmxi_plugin')),			
		);

		$wpai_menu = apply_filters('pmxi_admin_menu', $wpai_menu);		

		add_menu_page(__('WP All Import', 'pmxi_plugin'), __('All Import', 'pmxi_plugin'), PMXI_Plugin::$capabilities, 'pmxi-admin-home', array(PMXI_Plugin::getInstance(), 'adminDispatcher'), PMXI_Plugin::ROOT_URL . '/static/img/xmlicon.png');
		// workaround to rename 1st option to `Home`
		$submenu['pmxi-admin-home'] = array();

		foreach ($wpai_menu as $key => $value) {
			add_submenu_page('pmxi-admin-home', $value[1], $value[1], PMXI_Plugin::$capabilities, $value[0], array(PMXI_Plugin::getInstance(), 'adminDispatcher'));	
		}
		
	}	
}

