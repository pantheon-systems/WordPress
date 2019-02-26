<?php

if (!defined('ABSPATH')) {
    exit();
}

require_once(plugin_dir_path(__FILE__) . "../../class-wpfront-user-role-editor-entity-base.php");

if (!class_exists('WPFront_User_Role_Editor_Entity_Content_Shortcodes')) {

    /**
     * Content Shortcodes Entity
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2015 WPFront.com
     */
    class WPFront_User_Role_Editor_Entity_Content_Shortcodes extends WPFront_User_Role_Editor_Entity_Base {

        public function __construct() {
            parent::__construct('content_shortcodes');
        }

        protected function _db_data() {
            return array(
                $this->db_data_field('name', 'longtext'),
                $this->db_data_field('shortcode', 'longtext'),
                $this->db_data_field('user_type', 'tinytext'),
                $this->db_data_field('roles', 'longtext'),
                $this->db_data_field('author', 'bigint(20)'),
                $this->db_data_field('created_on', 'datetime'),
                $this->db_data_field('created_on_gmt', 'datetime')
            );
        }

        public function set_roles($roles) {
            parent::__call('set_roles', array(serialize($roles)));
        }

        public function get_roles() {
            $roles = parent::__call('get_roles', array());
            return unserialize($roles);
        }

        public function get_all_shortcodes($page_index = -1, $per_page = -1, $search = '') {
            if (!empty($search)) {
                global $wpdb;
                $search = $wpdb->prepare("name LIKE %s OR shortcode LIKE %s", "%$search%", "%$search%");
            }

            return parent::get_all(array('id' => TRUE), $page_index, $per_page, $search);
        }

        public function count($search = '') {
            if (empty($search))
                return parent::count();

            global $wpdb;

            $sql = $wpdb->prepare("name LIKE %s OR shortcode LIKE %s", "%$search%", "%$search%");
            return parent::count($sql);
        }

        public static function uninstall() {
            self::$UNINSTALL = TRUE;

            $entity = new WPFront_User_Role_Editor_Entity_Content_Shortcodes();
            $entity->uninstall_action();
        }

    }

}