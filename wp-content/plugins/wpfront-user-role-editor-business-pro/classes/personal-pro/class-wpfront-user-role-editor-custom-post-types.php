<?php

if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('WPFront_User_Role_Editor_Custom_Post_Types')) {


    /**
     * Manage Custom Post Type Capabilities
     *
     * @author SyamM
     */
    class WPFront_User_Role_Editor_Custom_Post_Types extends WPFront_User_Role_Editor_Personal_Pro_Controller_Base {

        protected $registered_post_type_args = array();
        protected $customizable_post_types = array();

        public function __construct($main) {
            parent::__construct($main);

            add_filter('wpfront_ure_custom_post_type_upgrade_message', array($this, 'custom_post_type_upgrade_message'));
            add_filter('wpfront_ure_custom_post_type_permission_settings_list', array($this, 'custom_post_type_permission_settings_list'));
            add_action('wpfront_ure_update_customize_permission_custom_post_types', array($this, 'update_customize_permission_custom_post_types'), 10, 2);

            add_action('registered_post_type', array($this, 'registered_post_type'), 10, 2);
        }

        public function custom_post_type_upgrade_message($message) {
            if (current_user_can('manage_options'))
                return sprintf($this->__('%s in settings.'), '<a href="' . $this->settings_url() . '">' . $this->__('Enable customization') . '</a>');

            return '';
        }

        public function custom_post_type_permission_settings_list($list) {
            $list = array();

            $post_types = get_post_types(array('_builtin' => FALSE), 'objects');

            foreach ($post_types as $value) {
                if (in_array($value->name, $this->customizable_post_types))
                    $list[$value->name] = $value->labels->name;
            }

            return $list;
        }

        public function update_customize_permission_custom_post_types($new_values, $old_values) {
            $diff = array_diff($new_values, $old_values);
            global $wp_roles;

            foreach ($diff as $value) {
                $caps = (ARRAY) $this->get_post_type_capabilities($value);

                foreach ($wp_roles->role_objects as $role) {
                    foreach ($caps as $key => $value) {
                        if ($role->has_cap($key))
                            $role->add_cap($value);
                    }
                }
            }

            $diff = array_diff($old_values, $new_values);

            foreach ($diff as $value) {
                $caps = (ARRAY) $this->get_post_type_capabilities($value);

                foreach ($wp_roles->role_objects as $role) {
                    foreach ($caps as $key => $value) {
                        if ($key === 'read')
                            continue;
                        $role->remove_cap($value);
                    }
                }
            }
        }

        public function registered_post_type($post_type, $args) {
            $this->registered_post_type_args[$post_type] = $args;

            if ($args->_builtin)
                return;
            
            if(!is_user_logged_in() && !$args->public)
                return;
            
            if(is_user_logged_in() && is_admin() && !$args->show_ui)
                return;

            if (is_array($args->capability_type) || $args->capability_type !== 'post') {
                $this->add_role_permissions($post_type);
                return;
            }

            $this->customizable_post_types[] = $post_type;

            if (!in_array($post_type, $this->main->customize_permission_custom_post_types()))
                return;

            global $wp_post_types;

            $caps = $this->get_post_type_capabilities($post_type);
            $wp_post_types[$post_type]->cap = $caps;
            $wp_post_types[$post_type]->capability_type = $post_type;
            $this->add_role_permissions($post_type);

            $role = get_role(self::ADMINISTRATOR_ROLE_KEY);
            foreach ($caps as $key => $value) {
                if ($role->has_cap($key))
                    $role->add_cap($value);
            }
        }

        private function get_post_type_capabilities($post_type) {
            $args = $this->registered_post_type_args[$post_type];

            if (is_array($args->capability_type) || $args->capability_type !== 'post') {
                return $args->cap;
            }

            $args->capability_type = $post_type;
            $args->map_meta_cap = TRUE;
            $args->capabilities = array();

            return get_post_type_capabilities($args);
        }

        private function add_role_permissions($post_type) {
            $settings_key = "add_role_permissions_{$post_type}_processed";

            $caps = $this->get_post_type_capabilities($post_type);
            $cap_key = $caps->edit_posts . '_role_permissions';

            new WPFront_User_Role_Editor_Custom_Post_Permissions($post_type, $this->main, $cap_key, $caps);

            if(!is_admin())
                return;
            
            $this->main->add_role_capability($cap_key);
            
            $entity = new WPFront_User_Role_Editor_Entity_Options();
            if ($entity->get_option($settings_key) === '1')
                return;

            $role = get_role(self::ADMINISTRATOR_ROLE_KEY);
            if ($role->has_cap($cap_key)) {
                $entity->update_option($settings_key, '1');
                return;
            }
            $role->add_cap($cap_key);

            global $wp_roles;

            foreach ($wp_roles->role_objects as $role) {
                if ($role->has_cap($caps->edit_posts))
                    $role->add_cap($cap_key);
            }

            $entity->update_option($settings_key, '1');
        }

    }

}
