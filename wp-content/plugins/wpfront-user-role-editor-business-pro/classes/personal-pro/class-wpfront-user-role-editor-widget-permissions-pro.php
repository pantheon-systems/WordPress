<?php

if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('WPFront_User_Role_Editor_Widget_Permissions_Pro')) {

    /**
     * Navigation Menu Controller Pro
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2015 WPFront.com
     */
    class WPFront_User_Role_Editor_Widget_Permissions_Pro extends WPFront_User_Role_Editor_Widget_Permissions {

        private static $GUEST_ROLE_KEY = 'wpfront_widget_permissions_guest_role_key_1';

        protected function get_meta_data($instance) {
            $data = parent::get_meta_data($instance);

            if (empty($data->roles) || !is_array($data->roles))
                $data->roles = array(self::ADMINISTRATOR_ROLE_KEY);

            if (!in_array(self::ADMINISTRATOR_ROLE_KEY, $data->roles))
                array_unshift($data->roles, self::ADMINISTRATOR_ROLE_KEY);

            return $data;
        }

        protected function get_roles() {
            global $wp_roles;

            $roles = $wp_roles->role_names;
            $roles[self::$GUEST_ROLE_KEY] = $this->__('[Guest]');

            return $roles;
        }

        public function widget_permissions_custom_fields_roles_list($widget, $return, $instance) {
            $string = '';
            $roles = $this->get_roles();
            $data = $this->get_meta_data($instance);

            foreach ($roles as $key => $value) {
                $string .= '<label><input type="checkbox" name="' . $widget->get_field_name('user-restriction-roles') . '[' . $key . ']" ' . ($key === self::ADMINISTRATOR_ROLE_KEY ? 'disabled' : '') . ' ' . (in_array($key, $data->roles) ? 'checked' : '') . ' />' . $value . '</label>';
            }

            echo $string;
        }

        public function widget_update_callback($instance, $new_instance, $old_instance, $widget) {
            $instance = parent::widget_update_callback($instance, $new_instance, $old_instance, $widget);

            if (!current_user_can('edit_widget_permissions'))
                return $instance;

            $data = $instance[self::$META_DATA_KEY];
            $roles = array();

            if (!empty($new_instance['user-restriction-roles'])) {
                $post = $new_instance['user-restriction-roles'];

                foreach ($post as $key => $value) {
                    $roles[] = $key;
                }
            }

            if (!in_array(self::ADMINISTRATOR_ROLE_KEY, $roles))
                array_unshift($roles, self::ADMINISTRATOR_ROLE_KEY);

            $data->roles = $roles;

            $instance[self::$META_DATA_KEY] = $data;
            return $instance;
        }

        public function widget_display_callback($instance, $widget, $args) {
            $instance = parent::widget_display_callback($instance, $widget, $args);

            if ($instance === FALSE)
                return FALSE;

            $data = $this->get_meta_data($instance);

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
                    if (in_array($role, $data->roles)) {
                        return $instance;
                    }
                }

                return FALSE;
            }

            return $instance;
        }

    }

}