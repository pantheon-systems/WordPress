<?php

if (!defined('ABSPATH')) {
    exit();
}

require_once(plugin_dir_path(__FILE__) . "../class-wpfront-user-role-editor-entity-base.php");

if (!class_exists('WPFront_User_Role_Editor_Entity_Options')) {

    /**
     * Options Entity
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Entity_Options extends WPFront_User_Role_Editor_Entity_Base {

        public function __construct() {
            parent::__construct('options');
        }

        protected function _db_data() {
            return array(
                $this->db_data_field('option_name', 'varchar(250)'),
                $this->db_data_field('option_value', 'longtext')
            );
        }

        public function get_option($key) {
            $entity = $this->get_by_option_name($key);
            if($entity === NULL) 
                return NULL;

            return $entity->get_option_value();
        }

        public function update_option($key, $value) {
            $entity = $this->get_by_option_name($key);
            if($entity === NULL) {
                $entity = new WPFront_User_Role_Editor_Entity_Options();
                $entity->set_option_name($key);
            }
            $entity->set_option_value($value);
            $entity->save();
        }

        public function delete_option($key) {
            $entity = $this->get_by_option_name($key);
            if($entity !== NULL)
                $entity->delete();
        }

        public static function uninstall() {
            self::$UNINSTALL = TRUE;

            $entity = new WPFront_User_Role_Editor_Entity_Options();
            $entity->uninstall_action();
        }

    }

}