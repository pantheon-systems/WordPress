<?php

if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('WPFront_User_Role_Editor_Personal_Pro_Controller_Base')) {

    /**
     * Base class of WPFront User Role Editor Controllers
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Personal_Pro_Controller_Base extends WPFront_User_Role_Editor_Controller_Base {

        public function get_edit_menu_url($role) {
            return admin_url('admin.php?page=') . WPFront_User_Role_Editor_Menu_Editor::MENU_SLUG . '&role=' . $role;
        }
        
        public function get_content_shortcodes_url() {
            return admin_url('admin.php?page=') . WPFront_User_Role_Editor_Content_Shortcodes::MENU_SLUG;
        }
        
        public function get_bulk_edit_url() {
            return admin_url('admin.php?page=') . WPFront_User_Role_Editor_Bulk_Edit::MENU_SLUG;
        }

        public function can_edit_role_menus() {
            return current_user_can('edit_role_menus');
        }

        public function can_import() {
            return current_user_can('import');
        }

        public function can_export() {
            return current_user_can('export');
        }
        
        public function can_edit_content_shortcodes() {
            return current_user_can('edit_content_shortcodes');
        }
        
        public function can_delete_content_shortcodes() {
            return current_user_can('delete_content_shortcodes');
        }
        
        public function can_bulk_edit_roles() {
            return current_user_can('delete_content_shortcodes');
        }

    }

}