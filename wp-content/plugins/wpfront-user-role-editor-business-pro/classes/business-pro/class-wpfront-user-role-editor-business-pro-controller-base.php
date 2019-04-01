<?php

if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('WPFront_User_Role_Editor_Business_Pro_Controller_Base')) {

    /**
     * Base class of WPFront User Role Editor Controllers
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Business_Pro_Controller_Base extends WPFront_User_Role_Editor_Personal_Pro_Controller_Base {

        public function can_manage_network_roles() {
            return current_user_can('manage_network_roles');
        }

        public function network_list_url() {
            return network_admin_url('admin.php') . '?page=' . WPFront_User_Role_Editor_MS_List::MENU_SLUG;
        }

        public function network_add_new_url() {
            return network_admin_url('admin.php') . '?page=' . WPFront_User_Role_Editor_Add_Edit::MENU_SLUG;
        }

        public function network_sync_url() {
            return network_admin_url('admin.php') . '?page=' . WPFront_User_Role_Editor_MS_Sync::MENU_SLUG;
        }

        public function get_ms_blog_ids() {
            $blog_ids = $this->cache_get('get_ms_blog_ids');
            if ($blog_ids !== NULL) {
                return $blog_ids;
            }

            global $wpdb;
            $query = "SELECT blog_id FROM $wpdb->blogs WHERE site_id = '{$wpdb->siteid}'";
            $blog_ids = array();
            $result = $wpdb->get_results($query, ARRAY_A);
            foreach ($result as $value) {
                $blog_ids[] = (int) $value['blog_id'];
            }

            $this->cache_add('get_ms_blog_ids', $blog_ids);
            return $blog_ids;
        }

        public function get_ms_max_blog_id() {
            $blog_id = $this->cache_get('get_ms_max_blog_id');
            if ($blog_id !== NULL) {
                return $blog_id;
            }

            global $wpdb;
            $query = "SELECT MAX(blog_id) FROM $wpdb->blogs WHERE site_id = '{$wpdb->siteid}'";
            $blog_id = $wpdb->get_var($query);

            $this->cache_add('get_ms_max_blog_id', $blog_id);
            return $blog_id;
        }

        public function get_ms_count_blogs() {
            $blog_count = $this->cache_get('get_ms_count_blogs');
            if ($blog_count !== NULL) {
                return $blog_count;
            }

            global $wpdb;
            $query = "SELECT COUNT(blog_id) FROM $wpdb->blogs WHERE site_id = '{$wpdb->siteid}'";
            $blog_count = $wpdb->get_var($query);

            $this->cache_add('get_ms_count_blogs', $blog_count);
            return $blog_count;
        }

        public function get_ms_roles_info() {
            $obj_roles = $this->cache_get('get_ms_roles_info');
            if ($obj_roles !== NULL)
                return $obj_roles;

            $blog_ids = $this->get_ms_blog_ids();

            $obj_roles = array();
            foreach ($blog_ids as $blog_id) {
                switch_to_blog($blog_id);
                global $wp_roles;

                foreach ($wp_roles->get_names() as $role_name => $display_name) {
                    if (!isset($obj_roles[$role_name]))
                        $obj_roles[$role_name] = (OBJECT) array(
                                    'role_name' => $role_name,
                                    'display_names' => array(),
                                    'blogs' => array()
                        );

                    if (!in_array($display_name, $obj_roles[$role_name]->display_names))
                        $obj_roles[$role_name]->display_names[] = $display_name;

                    $obj_roles[$role_name]->blogs[] = $blog_id;
                }
            }
            restore_current_blog();

            $this->cache_add('get_ms_roles_info', $obj_roles);
            return $obj_roles;
        }

        public function get_ms_role_capabilities($role) {
            $capabilities = $this->cache_get('get_ms_role_capabilities');
            if ($capabilities !== NULL)
                return $capabilities;

            $capabilities = array();
            $controller = new WPFront_User_Role_Editor_Business_Pro_Controller_Base($this->main);
            $roles_info = $controller->get_ms_roles_info();

            foreach ($roles_info[$role]->blogs as $blog_id) {
                switch_to_blog($blog_id);
                $role_obj = get_role($role);
                if ($role_obj === NULL)
                    continue;
                foreach ($role_obj->capabilities as $key => $value) {
                    if (array_key_exists($key, $capabilities))
                        $capabilities[$key] = $capabilities[$key] || $value;
                    else
                        $capabilities[$key] = $value;
                }
            }

            $this->cache_add('get_ms_role_capabilities', $capabilities);
            return $capabilities;
        }

    }

}