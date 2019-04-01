<?php

if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('WP_MS_Sites_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-ms-sites-list-table.php';
}

if (!class_exists('WPFront_User_Role_Editor_MS_Sync_Sites_List_Table')) {

    /**
     * Multisite sync
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_MS_Sync_Sites_List_Table extends WP_MS_Sites_List_Table {

        public function __construct() {
            parent::__construct(array('screen' => 'sites'));

            add_filter('manage_sites_action_links', array($this, 'manage_sites_action_links'));
        }

        public function get_bulk_actions() {
            return array();
        }

        public function get_columns() {
            $columns = parent::get_columns();
            unset($columns['users']);
            $columns['id'] = 'id';
            return $columns;
        }

        public function manage_sites_action_links() {
            return array();
        }
        
        public function column_id($blog) {
            echo $blog['blog_id'];
        }

    }

}
