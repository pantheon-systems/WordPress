<?php

if (!defined('ABSPATH')) {
    exit();
}

require_once(plugin_dir_path(__FILE__) . "entities/class-wpfront-user-role-editor-entity-post-type-permissions.php");

if (!class_exists('WPFront_User_Role_Editor_Post_Type_Permissions')) {

    /**
     * Post Type Permissions
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    abstract class WPFront_User_Role_Editor_Post_Type_Permissions extends WPFront_User_Role_Editor_Personal_Pro_Controller_Base {

        private static $META_DATA_KEY;
        private static $POSTS_COLUMN_KEY;
        private static $GUEST_ROLE_KEY = 'wpfront-guest-role-1';
        protected static $extendable_post_types = array();
        protected static $extended_post_types = array();
        private $type;
        private $permission_cap;
        private $caps_mapping;
        private $roles;
        private $post_ID;

        public function __construct($type, $main, $permission_cap = NULL, $caps_mapping = NULL) {
            self::$META_DATA_KEY = WPFront_User_Role_Editor::PLUGIN_SLUG . '-role-permission';
            self::$POSTS_COLUMN_KEY = WPFront_User_Role_Editor::PLUGIN_SLUG . '-role-permission-column-key';

            parent::__construct($main);

            self::$extendable_post_types[] = $type;
            
            $disabled_post_types = $this->main->disable_extended_permission_post_types();
            if(in_array($type, $disabled_post_types))
                return;
            
            $this->type = $type;

            if ($permission_cap === NULL)
                $permission_cap = 'edit_' . $this->type . 's_role_permissions';
            $this->permission_cap = $permission_cap;

            if ($caps_mapping === NULL)
                $caps_mapping = (OBJECT) array(
                            'edit_posts' => 'edit_' . $this->type . 's',
                            'edit_published_posts' => 'edit_published_' . $this->type . 's',
                            'edit_others_posts' => 'edit_others_' . $this->type . 's',
                            'edit_private_posts' => 'edit_private_' . $this->type . 's',
                            'delete_posts' => 'delete_' . $this->type . 's',
                            'delete_published_posts' => 'delete_published_' . $this->type . 's',
                            'delete_others_posts' => 'delete_others_' . $this->type . 's',
                            'delete_private_posts' => 'delete_private_' . $this->type . 's'
                );
            $caps_mapping->read = 'read';
            $this->caps_mapping = $caps_mapping;

            self::$extended_post_types[$type] = (OBJECT) array(
                        'permission_cap' => $permission_cap,
                        'caps_mapping' => $caps_mapping
            );

            add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
            add_action('save_post', array($this, 'save_post'), 10, 1);

            add_filter('posts_where', array($this, 'posts_where'), 10, 2);
            add_filter('posts_join', array($this, 'posts_join'), 10, 2);

            add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));

            add_filter('user_has_cap', array($this, 'user_has_cap'), 10, 3);

            add_filter('manage_' . $type . 's_columns', array($this, 'manage_posts_columns'), 10, 1);
            add_filter('manage_' . $type . 's_custom_column', array($this, 'manage_posts_columns_content'), 10, 3);
        }

        public function add_meta_boxes() {
            if ($this->has_permission()) {
                add_meta_box(self::$META_DATA_KEY, $this->__('Role Permissions'), array($this, 'meta_box'), $this->type);
            }
        }

        protected function prepare_roles_data($post = NULL) {
            global $wp_roles;

            if (empty($post)) {
                $post = (OBJECT) array('ID' => 0);
            }

            $this->post_ID = $post->ID;

            $this->roles = array();
            $data = $this->get_data($post->ID, TRUE);

            $this->enable_permissions = FALSE;

            $roles = $wp_roles->get_names();
            asort($roles, SORT_STRING | SORT_FLAG_CASE);
            
            if(array_key_exists(self::ADMINISTRATOR_ROLE_KEY, $roles)) {
                $this->roles[self::ADMINISTRATOR_ROLE_KEY] = array($roles[self::ADMINISTRATOR_ROLE_KEY],
                    array(TRUE, TRUE),
                    array(TRUE, TRUE),
                    array(TRUE, TRUE)
                );
            }
            
            foreach ($roles as $key => $value) {
                if ($key === self::ADMINISTRATOR_ROLE_KEY) {
                    continue;
                }
                
                $this->roles[$key] = array($value,
                    array($this->has_access($key, 'read', $data)),
                    array($this->has_access($key, 'edit', $data)),
                    array($this->has_access($key, 'delete', $data))
                );

                $this->roles[$key][1][] = !$this->has_capability($key, 'read');
                $this->roles[$key][2][] = !$this->has_capability($key, 'edit');
                $this->roles[$key][3][] = !$this->has_capability($key, 'delete');

                if (!empty($data[$key]))
                    $this->enable_permissions = in_array('enable_permissions', $data[$key]);
            }

            $this->roles[self::$GUEST_ROLE_KEY] = array($this->__('[Guest]'),
                array($this->has_access(self::$GUEST_ROLE_KEY, 'read', $data), !$this->has_capability(self::$GUEST_ROLE_KEY, 'read')),
                array(FALSE, TRUE),
                array(FALSE, TRUE)
            );
        }

        public function get_roles_data($post = NULL) {
            $this->prepare_roles_data($post);

            return $this->roles;
        }

        public function meta_box($post = NULL) {
            $this->prepare_roles_data($post);

            wp_nonce_field(self::$META_DATA_KEY, self::$META_DATA_KEY . '-nonce');
            include($this->main->pluginDIR() . 'templates/personal-pro/post-type-permissions.php');
        }

        protected function verify_update_permission($post_id) {
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return FALSE;
            }

            if ($this->type !== get_post_type($post_id))
                return FALSE;

            if (empty($_POST[self::$META_DATA_KEY . '-nonce']))
                return FALSE;

            if (!wp_verify_nonce($_POST[self::$META_DATA_KEY . '-nonce'], self::$META_DATA_KEY))
                return FALSE;

            if (!$this->has_permission())
                return FALSE;

            return TRUE;
        }

        public function save_post($post_id) {
            if (!$this->verify_update_permission($post_id))
                return FALSE;

            $values = array('read', 'edit', 'delete');

            $data = array(self::ADMINISTRATOR_ROLE_KEY => $values);

            $enable_permissions = FALSE;
            if (!empty($_POST[self::$META_DATA_KEY . '-enable-role-permissions']))
                $enable_permissions = TRUE;

            if ($enable_permissions) {
                global $wp_roles;

                $post_data = NULL;
                if (!empty($_POST[self::$META_DATA_KEY])) {
                    $post_data = $_POST[self::$META_DATA_KEY];
                }
                if (!is_array($post_data))
                    $post_data = array();

                $roles = $wp_roles->get_names();
                $roles[self::$GUEST_ROLE_KEY] = self::$GUEST_ROLE_KEY;
                foreach ($roles as $key => $value) {
                    if ($key === self::ADMINISTRATOR_ROLE_KEY) {
                        continue;
                    }

                    if (array_key_exists($key, $post_data)) {
                        $data[$key] = array();
                        foreach ($values as $p) {
                            if (array_key_exists($p, $post_data[$key]))
                                $data[$key][] = $p;
                        }
                    } else
                        $data[$key] = array();
                }
            }

            $this->save_data($post_id, $data, $enable_permissions);

            return TRUE;
        }

        public function save_post_role_data($post_id, $role, $enable_permissions, $permissions) {
            if (!$this->verify_update_permission($post_id))
                return FALSE;

            if ($role === self::ADMINISTRATOR_ROLE_KEY)
                return FALSE;

            $this->prepare_roles_data();
            if (!array_key_exists($role, $this->roles))
                return FALSE;

            $data[$role] = array();
            if ($permissions->read)
                $data[$role][] = 'read';
            if ($permissions->edit)
                $data[$role][] = 'edit';
            if ($permissions->delete)
                $data[$role][] = 'delete';

            $this->save_data($post_id, $data, $enable_permissions);

            return TRUE;
        }

        public function posts_where($where, $query) {
            $post_type = '';
            if (isset($query->query_vars['post_type']))
                $post_type = $query->query_vars['post_type'];

            if (empty($post_type)) {
                if ($query->is_page)
                    $post_type = 'page';
                else
                    $post_type = 'post';
            }

            if ($post_type !== 'any' && $post_type !== $this->type)
                return $where;

            $user = wp_get_current_user();
            $user_roles = array();

            if (!is_user_logged_in() || $user->ID === 0)
                $user_roles[] = self::$GUEST_ROLE_KEY;
            elseif (empty($user->roles))
                return $where;
            else
                $user_roles = $user->roles;

            if (in_array(self::ADMINISTRATOR_ROLE_KEY, $user_roles))
                return $where;

            $entity = new WPFront_User_Role_Editor_Entity_Post_Type_Permissions();
            $table_name = $entity->table_name();

            global $wpdb;

            $roles = array();
            foreach ($user_roles as $role) {
                $roles[] = $wpdb->prepare('%s', $role);
            }
            $count = count($roles);
            $roles = implode(',', $roles);

            return $where . " AND $wpdb->posts.id NOT IN ("
                    . "SELECT post_id FROM $table_name "
                    . "WHERE role IN ($roles) "
                    . "AND enable_permissions = 1 "
                    . "AND has_read = 0 "
                    . "GROUP BY post_id "
                    . "HAVING COUNT(*) = $count"
                    . ") ";
        }

        public function posts_join($join, $query) {
            return $join;
        }

        public function user_has_cap($allcaps, $caps, $args) {
            if ($args[0] === "edit_{$this->type}")
                $args[0] = 'edit_post';
            elseif ($args[0] === "delete_{$this->type}")
                $args[0] = 'delete_post';

            if ($args[0] !== 'edit_post' && $args[0] !== 'delete_post') {
                return $allcaps;
            }

            if (empty($args[2]))
                return $allcaps;

            if (get_post_type($args[2]) !== $this->type)
                return $allcaps;

            $user = wp_get_current_user();
            if (empty($user))
                return $allcaps;

            if (empty($user->roles))
                return $allcaps;

            $permission_data = $this->get_data($args[2]);

            $has_permission = TRUE;
            if ($args[0] === 'edit_post')
                $has_permission = $this->has_access($user->roles, 'edit', $permission_data);
            if ($args[0] === 'delete_post')
                $has_permission = $this->has_access($user->roles, 'delete', $permission_data);

            if (!$has_permission) {
                foreach ($caps as $cap) {
                    unset($allcaps[$cap]);
                }
            }

            return $allcaps;
        }

        protected function save_data($post_id, $data, $enable_permissions) {
            $entity = new WPFront_User_Role_Editor_Entity_Post_Type_Permissions();
            $post_data = $entity->get_all_by_post_id($post_id);
            foreach ($post_data as $value) {
                $post_data[$value->get_role()] = $value;
            }

            $entity->update_enable_permissions($post_id, $enable_permissions);

            foreach ($data as $role_name => $permission) {
                if (isset($post_data[$role_name]))
                    $entity = $post_data[$role_name];
                else
                    $entity = new WPFront_User_Role_Editor_Entity_Post_Type_Permissions();

                $entity->set_role($role_name);
                $entity->set_post_type($this->type);
                $entity->set_post_id($post_id);
                $entity->set_enable_permissions($enable_permissions);
                $entity->set_has_read(in_array('read', $permission));
                $entity->set_has_edit(in_array('edit', $permission));
                $entity->set_has_delete(in_array('delete', $permission));
                $entity->save();
            }
        }

        protected function get_data($post_id, $real_data = FALSE) {
            $entity = new WPFront_User_Role_Editor_Entity_Post_Type_Permissions();
            $post_data = $entity->get_all_by_post_id($post_id);

            $data = array();
            foreach ($post_data as $value) {
                $obj = NULL;
                $enable_permissions = $value->get_enable_permissions();
                if ($real_data || $enable_permissions) {
                    $obj = array();
                    if ($value->get_has_read())
                        $obj[] = 'read';
                    if ($value->get_has_edit())
                        $obj[] = 'edit';
                    if ($value->get_has_delete())
                        $obj[] = 'delete';
                    if ($real_data && $enable_permissions)
                        $obj[] = 'enable_permissions';
                }
                $data[$value->get_role()] = $obj;
            }
            return $data;
        }

        public function manage_posts_columns($columns) {
            if ($this->has_permission()) {
                $columns[self::$POSTS_COLUMN_KEY] = $this->__('Role Permissions');
            }
            return $columns;
        }

        public function manage_posts_columns_content($column_name, $post_id) {
            switch ($column_name) {
                case self::$POSTS_COLUMN_KEY:
                    $entity = new WPFront_User_Role_Editor_Entity_Post_Type_Permissions();
                    if ($entity->is_enable_permissions($post_id)) {
                        printf('<img class="user-default" src="%s" />', $this->image_url() . 'check-icon.png');
                    }
                    break;
            }
        }

        protected function has_permission() {
            return current_user_can($this->permission_cap);
        }

        private function has_capability($role_name, $type) {
            if ($role_name === self::ADMINISTRATOR_ROLE_KEY || $type === 'read')
                return TRUE;

            if ($role_name === self::$GUEST_ROLE_KEY) {
                if ($type !== 'read')
                    return FALSE;

                $post = get_post($this->post_ID);
                if ($post->post_status === 'private')
                    return FALSE;
                else
                    return TRUE;
            }

            $role = get_role($role_name);

            foreach ((ARRAY) $this->caps_mapping as $key => $value) {
                if (strpos($key, $type) === 0 && $role->has_cap($value))
                    return TRUE;
            }

            return FALSE;
        }

        private function has_access($roles, $type, $meta_data) {
            if (!is_array($roles)) {
                $roles = array($roles);
            } else {
                if (empty($roles))
                    return TRUE;
            }

            foreach ($roles as $role_name) {
                if ($role_name === self::ADMINISTRATOR_ROLE_KEY)
                    return TRUE;

                if (!$this->has_capability($role_name, $type))
                    continue;

                if (!isset($meta_data[$role_name]))
                    return TRUE;

                if ($meta_data[$role_name] === NULL)
                    return TRUE;

                if (in_array($type, $meta_data[$role_name]))
                    return TRUE;
            }

            return FALSE;
        }

        public function enqueue_scripts() {
            wp_enqueue_script('jquery');

            $styleRoot = $this->main->pluginURL() . 'css/personal-pro/';
            wp_enqueue_style('wpfront-user-role-editor-personal-pro-admin-styles', $styleRoot . 'admin-style.css', array(), WPFront_User_Role_Editor::VERSION);
        }
        
        public static function get_extendable_post_types() {
            return self::$extendable_post_types;
        }

        public static function get_extended_post_types_info() {
            return self::$extended_post_types;
        }

    }

}
