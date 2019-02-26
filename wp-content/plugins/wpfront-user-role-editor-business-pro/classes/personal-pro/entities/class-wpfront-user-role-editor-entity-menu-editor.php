<?php

if (!defined('ABSPATH')) {
    exit();
}

require_once(plugin_dir_path(__FILE__) . "../../class-wpfront-user-role-editor-entity-base.php");

if (!class_exists('WPFront_User_Role_Editor_Entity_Menu_Editor')) {

    /**
     * Menu Editor Entity
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Entity_Menu_Editor extends WPFront_User_Role_Editor_Entity_Base {

        public function __construct() {
            parent::__construct('menu_editor');
        }

        protected function _db_data() {
            return array(
                $this->db_data_field('role', 'longtext'),
                $this->db_data_field('menu_slug', 'longtext'),
                $this->db_data_field('parent_menu_slug', 'longtext'),
                $this->db_data_field('enabled', 'bit')
            );
        }

        public function delete_all($role) {
            $this->set_role($role);
            $this->delete_by_role();
        }

        public static function uninstall() {
            self::$UNINSTALL = TRUE;

            $entity = new WPFront_User_Role_Editor_Entity_Menu_Editor();
            $entity->uninstall_action();
        }

    }

}