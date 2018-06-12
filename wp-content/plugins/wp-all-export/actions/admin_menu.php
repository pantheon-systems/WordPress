<?php
/**
 * Register plugin specific admin menu
 */

function pmxe_admin_menu() {
	global $menu, $submenu;
	
	if (current_user_can( PMXE_Plugin::$capabilities )) { // admin management options
		
		add_menu_page(__('WP All Export', 'wp_all_export_plugin'), __('All Export', 'wp_all_export_plugin'), PMXE_Plugin::$capabilities, 'pmxe-admin-home', array(PMXE_Plugin::getInstance(), 'adminDispatcher'), PMXE_Plugin::ROOT_URL . '/static/img/xmlicon.png', 111);
		// workaround to rename 1st option to `Home`
		$submenu['pmxe-admin-home'] = array();		
		add_submenu_page('pmxe-admin-home', __('Export to XML', 'wp_all_export_plugin') . ' &lsaquo; ' . __('WP All Export', 'wp_all_export_plugin'), __('New Export', 'wp_all_export_plugin'), PMXE_Plugin::$capabilities, 'pmxe-admin-export', array(PMXE_Plugin::getInstance(), 'adminDispatcher'));
		add_submenu_page('pmxe-admin-home', __('Manage Exports', 'wp_all_export_plugin') . ' &lsaquo; ' . __('WP All Export', 'wp_all_export_plugin'), __('Manage Exports', 'wp_all_export_plugin'), PMXE_Plugin::$capabilities, 'pmxe-admin-manage', array(PMXE_Plugin::getInstance(), 'adminDispatcher'));
		add_submenu_page('pmxe-admin-home', __('Settings', 'wp_all_export_plugin') . ' &lsaquo; ' . __('WP All Export', 'wp_all_export_plugin'), __('Settings', 'wp_all_export_plugin'), PMXE_Plugin::$capabilities, 'pmxe-admin-settings', array(PMXE_Plugin::getInstance(), 'adminDispatcher'));
		// add_submenu_page('pmxe-admin-home', __('Feedback', 'wp_all_export_plugin') . ' &lsaquo; ' . __('WP All Export', 'wp_all_export_plugin'), __('Feedback', 'wp_all_export_plugin'), PMXE_Plugin::$capabilities, 'pmxe-admin-feedback', array(PMXE_Plugin::getInstance(), 'adminDispatcher'));
		//add_submenu_page('pmxe-admin-home', __('Support', 'wp_all_export_plugin') . ' &lsaquo; ' . __('WP All Export', 'wp_all_export_plugin'), __('Support', 'wp_all_export_plugin'), 'manage_options', 'pmxe-admin-help', array(PMXE_Plugin::getInstance(), 'adminDispatcher'));
		
	}	
}

