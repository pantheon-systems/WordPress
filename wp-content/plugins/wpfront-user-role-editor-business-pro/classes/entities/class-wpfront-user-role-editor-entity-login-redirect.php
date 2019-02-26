<?php

if (!defined('ABSPATH')) {
    exit();
}

require_once(plugin_dir_path(__FILE__) . "../class-wpfront-user-role-editor-entity-base.php");

if (!class_exists('WPFront_User_Role_Editor_Entity_Login_Redirect')) {

    /**
     * Login Redirect Entity
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2015 WPFront.com
     */
    class WPFront_User_Role_Editor_Entity_Login_Redirect extends WPFront_User_Role_Editor_Entity_Base {

        public function __construct() {
            parent::__construct('login_redirect');
        }

        protected function _db_data() {
            $logout_url = $this->db_data_field('logout_url', 'varchar(2000)');
            $logout_url->default = "''";
            
            return array(
                $this->db_data_field('role', 'varchar(250)'),
                $this->db_data_field('priority', 'int'),
                $this->db_data_field('url', 'varchar(2000)'),
                $this->db_data_field('deny_wpadmin', 'bit'),
                $this->db_data_field('disable_toolbar', 'bit'),
                $logout_url
            );
        }

        public function count($search = '') {
            if (empty($search))
                return parent::count();

            global $wpdb;

            $sql = $wpdb->prepare("role LIKE %s OR url LIKE %s", "%$search%", "%$search%");
            return parent::count($sql);
        }

        public function get_all_login_redirect($search = '') {
            if (!empty($search)) {
                global $wpdb;
                $search = $wpdb->prepare("role LIKE %s OR url LIKE %s", "%$search%", "%$search%");
            }

            $data = parent::get_all(array('priority' => FALSE), -1, -1, $search);

            global $wp_roles;
            $roles = $wp_roles->get_names();
            $entities = array();

            foreach ($data as $value) {
                if (isset($roles[$value->get_role()])) {
                    $entities[] = $value;
                }
            }

            return $entities;
        }

        public function get_next_priority() {
            $sql = "SELECT MAX(priority) FROM " . $this->table_name();

            global $wpdb;
            $result = $wpdb->get_var($sql);

            return intval($result) + 1;
        }

        public function add() {
            $priority = $this->get_next_priority();
            if ($this->get_priority() > $priority)
                $this->set_priority($priority);

            if ($this->get_priority() < 1)
                $this->set_priority(1);

            $sql = "UPDATE " . $this->table_name() . " "
                    . "SET priority = priority + 1 "
                    . "WHERE priority >= " . $this->get_priority();
            global $wpdb;
            $wpdb->query($sql);

            parent::add();
        }

        public function update() {
            $priority = $this->get_next_priority() - 1;
            if ($this->get_priority() > $priority)
                $this->set_priority($priority);

            if ($this->get_priority() < 1)
                $this->set_priority(1);

            $sql = "SELECT priority "
                    . "FROM " . $this->table_name() . " "
                    . "WHERE id = " . $this->get_id();
            global $wpdb;
            $current_priority = $wpdb->get_var($sql);
            $new_priority = $this->get_priority();

            if ($current_priority < $new_priority) {
                $sql = "UPDATE " . $this->table_name() . " "
                        . "SET priority = priority - 1 "
                        . "WHERE priority > $current_priority AND priority <= $new_priority";
                $wpdb->query($sql);
            }

            if ($current_priority > $new_priority) {
                $sql = "UPDATE " . $this->table_name() . " "
                        . "SET priority = priority + 1 "
                        . "WHERE priority >= $new_priority AND priority < $current_priority";
                $wpdb->query($sql);
            }

            parent::update();
        }

        public function delete() {
            $sql = "SELECT priority "
                    . "FROM " . $this->table_name() . " "
                    . "WHERE id = " . $this->get_id();
            global $wpdb;
            $current_priority = $wpdb->get_var($sql);

            $sql = "UPDATE " . $this->table_name() . " "
                    . "SET priority = priority - 1 "
                    . "WHERE priority > $current_priority";
            $wpdb->query($sql);

            parent::delete();
        }

        public static function uninstall() {
            self::$UNINSTALL = TRUE;

            $entity = new WPFront_User_Role_Editor_Entity_Login_Redirect();
            $entity->uninstall_action();
        }

    }

}