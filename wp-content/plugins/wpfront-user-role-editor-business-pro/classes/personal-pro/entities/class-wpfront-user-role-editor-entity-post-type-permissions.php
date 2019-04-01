<?php

if (!defined('ABSPATH')) {
    exit();
}

require_once(plugin_dir_path(__FILE__) . "../../class-wpfront-user-role-editor-entity-base.php");

if (!class_exists('WPFront_User_Role_Editor_Entity_Post_Type_Permissions')) {

    /**
     * Post Type Permissions Entity
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Entity_Post_Type_Permissions extends WPFront_User_Role_Editor_Entity_Base {

        public function __construct() {
            parent::__construct('post_type_permissions');
        }

        protected function _db_data() {
            return array(
                $this->db_data_field('role', 'longtext'),
                $this->db_data_field('post_type', 'varchar(200)'),
                $this->db_data_field('post_id', 'bigint(20)'),
                $this->db_data_field('enable_permissions', 'bit'),
                $this->db_data_field('has_read', 'bit'),
                $this->db_data_field('has_edit', 'bit'),
                $this->db_data_field('has_delete', 'bit')
            );
        }

        public function update_enable_permissions($post_id, $value) {
            global $wpdb;

            $table_name = $this->table_name();
            $wpdb->query($wpdb->prepare("UPDATE $table_name SET enable_permissions = %d WHERE post_id = %d", $value, $post_id));
        }

        public function is_enable_permissions($post_id) {
            global $wpdb;

            $table_name = $this->table_name();
            $result = $wpdb->get_var($wpdb->prepare("SELECT enable_permissions FROM $table_name WHERE post_id = %d", $post_id));
            if ($result === NULL)
                return FALSE;
            else if ($result === '1')
                return TRUE;
            else
                return FALSE;
        }

        public static function uninstall() {
            self::$UNINSTALL = TRUE;

            $entity = new WPFront_User_Role_Editor_Entity_Post_Type_Permissions();
            $entity->uninstall_action();
        }

    }

}