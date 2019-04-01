<?php

if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('WPFront_User_Role_Editor_Nav_Menu_Pro')) {

    /**
     * Navigation Menu Controller Pro
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2015 WPFront.com
     */
    class WPFront_User_Role_Editor_Nav_Menu_Pro extends WPFront_User_Role_Editor_Nav_Menu {

        private static $GUEST_ROLE_KEY = 'wpfront_nav_menu_guest_role_key_1';

        protected function get_roles() {
            global $wp_roles;

            $roles = $wp_roles->role_names;
            $roles[self::$GUEST_ROLE_KEY] = $this->__('[Guest]');

            return $roles;
        }

        public function menu_item_custom_fields_roles_list($item_id, $item, $depth, $args) {
            $string = '';
            $roles = $this->get_roles();
            $data = $this->get_meta_data($item_id);

            foreach ($roles as $key => $value) {
                $string .= '<label><input type="checkbox" name="user-restriction-roles-' . $item_id . '[' . $key . ']" ' . ($key === self::ADMINISTRATOR_ROLE_KEY ? 'disabled' : '') . ' ' . (in_array($key, $data->roles) ? 'checked' : '') . ' />' . $value . '</label>';
            }

            echo $string;
        }

        protected function update_nav_menu_item_sub($menu_id, $menu_item_db_id, $args, $data) {
            $roles = array();

            if (!empty($_POST['user-restriction-roles-' . $menu_item_db_id])) {
                $post = $_POST['user-restriction-roles-' . $menu_item_db_id];

                foreach ($post as $key => $value) {
                    $roles[] = $key;
                }
            }

            if (!in_array(self::ADMINISTRATOR_ROLE_KEY, $roles))
                array_unshift($roles, self::ADMINISTRATOR_ROLE_KEY);

            $data->roles = $roles;

            return $data;
        }

        protected function override_nav_menu_items_sub($item, $data) {
            if ($data->type === self::$ROLE_USERS) {
                $user = wp_get_current_user();
                $user_roles = array();

                if (!is_user_logged_in() || $user->ID === 0)
                    $user_roles[] = self::$GUEST_ROLE_KEY;
                elseif (empty($user->roles))
                    $user_roles = array();
                else
                    $user_roles = $user->roles;
                
                foreach ($user_roles as $role) {
                    if(in_array($role, $data->roles)) {
                        return FALSE;
                    }
                }
                
                return TRUE;
            }

            return FALSE;
        }

        protected function get_meta_data($menu_item_db_id) {
            $data = parent::get_meta_data($menu_item_db_id);

            if (empty($data->roles) || !is_array($data->roles))
                $data->roles = array(self::ADMINISTRATOR_ROLE_KEY);

            if (!in_array(self::ADMINISTRATOR_ROLE_KEY, $data->roles))
                array_unshift($data->roles, self::ADMINISTRATOR_ROLE_KEY);

            return $data;
        }

    }

}