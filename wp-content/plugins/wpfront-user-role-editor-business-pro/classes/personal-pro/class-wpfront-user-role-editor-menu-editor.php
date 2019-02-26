<?php

if (!defined('ABSPATH')) {
    exit();
}

require_once(plugin_dir_path(__FILE__) . "entities/class-wpfront-user-role-editor-entity-menu-editor.php");

if (!class_exists('WPFront_User_Role_Editor_Menu_Editor')) {

    /**
     * Menu Editor
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Menu_Editor extends WPFront_User_Role_Editor_Personal_Pro_Controller_Base {

        const MENU_SLUG = 'wpfront-user-role-editor-menu-editor';
        const MENU_SEPARATOR_TITLE = '';
        const MENU_SEPARATOR_CLASS = 'wp-menu-separator';
        const OVERRIDE_TYPE_SOFT = 'soft';
        const OVERRIDE_TYPE_HARD = 'hard';
        const EDITED_MENU_COLUMN_KEY = 'edited_menu_column_key';

        private static $override_type_option_name = 'menu_editor_override_type_';
        private static $hide_new_menus_option_name = 'menu_editor_hide_new_menus_';
        private static $disable_for_secondary_role_option_name = 'disable_for_secondary_role_';
        private $menu;
        private $roles;
        private $current_role;
        private $override_type = self::OVERRIDE_TYPE_SOFT;
        private $hide_new_menu = FALSE;
        private $disable_for_secondary_role = FALSE;
        private $result = NULL;
        private $wp_menu;
        private $wp_submenu;

        function __construct($main) {
            parent::__construct($main);

            add_action('admin_init', array($this, 'admin_init'), 1);

            add_action('admin_init', array($this, 'lock_menus'), 999999);
            //add_action('admin_menu', array($this, 'lock_menus'), 999999);
            add_filter('parent_file', array($this, 'lock_menus'), 999999, 1);

            add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));

            add_filter('manage_roles_columns', array($this, 'manage_roles_columns'), 10, 1);
            add_filter('manage_roles_custom_column', array($this, 'manage_roles_custom_column'), 10, 3);
            add_filter('role_row_actions', array($this, 'role_row_actions'), 10, 2);
        }

        public function admin_init() {
            global $menu, $submenu;

            $this->wp_menu = $menu;
            $this->wp_submenu = $submenu;

            $this->ajax_register('wp_ajax_wpfront_user_role_editor_copy_menus', array($this, 'copy_menus_callback'));

            $this->check_page_lock_data();
        }

        public function menu_editor() {
            if (!$this->can_edit_role_menus()) {
                $this->main->permission_denied();
                die();
            }

            global $wp_roles;
            $this->roles = $wp_roles->get_names();
            if (isset($this->roles[self::ADMINISTRATOR_ROLE_KEY])) {
                unset($this->roles[self::ADMINISTRATOR_ROLE_KEY]);
            }
            natcasesort($this->roles);

            $this->current_role = NULL;

            if (!empty($_GET['role'])) {
                $role = $_GET['role'];
                if (array_key_exists($role, $this->roles)) {
                    $this->current_role = get_role($role);
                }
            }

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $this->main->verify_nonce();
                if (!empty($_POST['role'])) {
                    $role = $_POST['role'];
                    if (array_key_exists($role, $this->roles)) {
                        $this->current_role = get_role($role);
                        if ($this->current_role !== NULL) {
                            if (!empty($_POST['submit']) && !empty($_POST['parent-menu']) && !empty($_POST['child-menu'])) {
                                $parent_menu = $_POST['parent-menu'];
                                $child_menu = $_POST['child-menu'];
                                if (!is_array($parent_menu))
                                    $parent_menu = array();
                                if (!is_array($child_menu))
                                    $child_menu = array();

                                $this->override_type = self::OVERRIDE_TYPE_SOFT;
                                if (!empty($_POST['override-type'])) {
                                    $this->override_type = $_POST['override-type'];
                                    if ($this->override_type !== self::OVERRIDE_TYPE_HARD)
                                        $this->override_type = self::OVERRIDE_TYPE_SOFT;
                                }
                                $this->set_override_type($this->current_role, $this->override_type);


                                if (!empty($_POST['hide-new-menus']))
                                    $this->set_hide_new_menus($this->current_role, 1);
                                else
                                    $this->set_hide_new_menus($this->current_role, 0);

                                if (!empty($_POST['disable-for-secondary-role']))
                                    $this->set_disable_for_secondary_role($this->current_role, 1);
                                else
                                    $this->set_disable_for_secondary_role($this->current_role, 0);

                                $entity = new WPFront_User_Role_Editor_Entity_Menu_Editor();
                                $table_data = $entity->get_all_by_role($this->current_role->name);
                                $table_data = $this->index_table_data($table_data);

                                foreach ($parent_menu as $key => $value) {
                                    $key = $this->decode_menu_key($key);
                                    $entity = NULL;
                                    if (isset($table_data[$key]))
                                        $entity = $table_data[$key]->entity;

                                    if ($entity === NULL)
                                        $entity = new WPFront_User_Role_Editor_Entity_Menu_Editor();

                                    $entity->set_role($this->current_role->name);
                                    $entity->set_menu_slug($key);
                                    $entity->set_parent_menu_slug('');
                                    $entity->set_enabled($value === 'true');
                                    $entity->save();
                                }

                                foreach ($child_menu as $parent_key => $submenus) {
                                    $parent_key = $this->decode_menu_key($parent_key);
                                    foreach ($submenus as $child_key => $value) {
                                        $child_key = $this->decode_menu_key($child_key);
                                        $entity = NULL;
                                        if (isset($table_data[$parent_key])) {
                                            if (isset($table_data[$parent_key]->children[$child_key])) {
                                                $entity = $table_data[$parent_key]->children[$child_key]->entity;
                                            }
                                        }

                                        if ($entity === NULL)
                                            $entity = new WPFront_User_Role_Editor_Entity_Menu_Editor();

                                        $entity->set_role($this->current_role->name);
                                        $entity->set_menu_slug($child_key);
                                        $entity->set_parent_menu_slug($parent_key);
                                        $entity->set_enabled($value === 'true');
                                        $entity->save();
                                    }
                                }

                                $this->result = (OBJECT) array('status' => TRUE, 'message' => $this->__('Menu changes saved.'));
                            }

                            if (!empty($_POST['doaction'])) {
                                if (!empty($_POST['copyfrom'])) {
                                    if ($_POST['copyfrom'] === '_restore_') {
                                        $this->delete_override_type($this->current_role);
                                        $this->delete_hide_new_menus($this->current_role);
                                        $this->delete_disable_for_secondary_role($this->current_role);

                                        $entity = new WPFront_User_Role_Editor_Entity_Menu_Editor();
                                        $entity->delete_all($this->current_role->name);

                                        $this->result = (OBJECT) array('status' => TRUE, 'message' => $this->__('Menu defaults restored.'));
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if (!empty($this->roles) && $this->current_role === NULL) {
                reset($this->roles);
                $this->current_role = get_role(key($this->roles));
            }

            if ($this->current_role === NULL) {
                wp_die($this->__('No editable roles found on this site.'));
                return;
            }

            $this->override_type = $this->get_override_type($this->current_role);
            $this->hide_new_menu = $this->get_hide_new_menus($this->current_role);
            $this->disable_for_secondary_role = $this->get_disable_for_secondary_role($this->current_role);

            $menu = $this->wp_menu;
            $submenu = $this->wp_submenu;

            $this->menu = array();

            $entity = new WPFront_User_Role_Editor_Entity_Menu_Editor();
            $table_data = $entity->get_all_by_role($this->current_role->name);
            $table_data = $this->index_table_data($table_data);

            foreach ($menu as $pos => $value) {
                if ($this->is_menu_separator($value)) {
                    $this->menu[] = NULL;
                } else {
                    $parent_slug = $this->escape_customize_slug($value[2]);
                    $parent_cap = $this->escape_customize_capability($value[1]);
                    $obj = (OBJECT) array(
                                'slug' => $parent_slug,
                                'name' => $this->remove_html($value[0]),
                                'capability' => $parent_cap,
                                'page' => $value[3],
                                'has_capability' => $this->current_role->has_cap($parent_cap),
                                'children' => array(),
                                'disabled' => !$this->current_role->has_cap($parent_cap),
                                'has_access' => $this->has_access($this->current_role, $this->hide_new_menu, $table_data, $parent_slug)
                    );
                    if (array_key_exists($obj->slug, $submenu)) {
                        $children = $submenu[$obj->slug];
                        foreach ($children as $key => $sub) {
                            $child_slug = $this->escape_customize_slug($sub[2]);
                            $child_cap = $this->escape_customize_capability($sub[1]);
                            $obj->children[] = (OBJECT) array(
                                        'name' => $this->remove_html($sub[0]),
                                        'capability' => $child_cap,
                                        'page' => '',
                                        'slug' => $child_slug,
                                        'has_capability' => $this->current_role->has_cap($child_cap),
                                        'disabled' => !$this->current_role->has_cap($child_cap),
                                        'has_access' => $this->has_access($this->current_role, $this->hide_new_menu, $table_data, $child_slug, $value[2])
                            );
                            if ($this->current_role->has_cap($child_cap)) {
                                $obj->disabled = FALSE;
                            }
                        }
                    }
                    $this->menu[] = $obj;
                }
            }

            include($this->main->pluginDIR() . 'templates/personal-pro/menu-editor.php');
        }

        public function lock_menus($parent_file = NULL) {
            if (WPFront_Static_URE::doing_ajax()) {
                return $parent_file;
            }

            if (empty($this->wp_menu)) {
                return $parent_file;
            }

            $user = wp_get_current_user();
            $roles = $user->roles;

            $primary = TRUE;

            while (!empty($roles)) {
                $obj_role = get_role(array_shift($roles));
                if ($obj_role === NULL)
                    continue;

                $this->override_type = $this->get_override_type($obj_role);
                $this->hide_new_menu = $this->get_hide_new_menus($obj_role);
                $this->disable_for_secondary_role = $this->get_disable_for_secondary_role($obj_role);

                if (!$primary) {
                    if ($this->disable_for_secondary_role)
                        continue;
                }

                $role = $obj_role->name;

                $entity = new WPFront_User_Role_Editor_Entity_Menu_Editor();
                $data = $entity->get_all_by_role($role);
                $data_indexed = $this->index_table_data($data);

                if ($this->override_type === self::OVERRIDE_TYPE_HARD) {
                    global $self, $submenu_file, $plugin_page, $pagenow;
                    $page = $plugin_page;
                    if ($page == NULL)
                        $page = $submenu_file;
                    if ($page == NULL)
                        $page = $self;
                    if($page == NULL) {
                        $page = $pagenow;
                        if(!empty($_SERVER['QUERY_STRING'])) {
                            $page .= '?' . $_SERVER['QUERY_STRING'];
                        }
                        $page = $this->escape_customize_slug($page);
                    }

                    if ($page !== NULL)
                        $page = html_entity_decode($page);

                    $row_data = NULL;
                    $parent_data = NULL;
                    if ($page != NULL) {
                        foreach ($data as $value) {
                            if ($value->get_menu_slug() == $page) {
                                if ($value->get_parent_menu_slug() != '')
                                    $row_data = $value;
                                else
                                    $parent_data = $value;
                            }
                        }

                        if ($row_data == NULL)
                            $row_data = $parent_data;

                        if ($row_data == NULL) {
                            if ($this->hide_new_menu) {
                                wp_die($this->page_lock_data());
                                die();
                                return;
                            }
                        } else {
                            if (!$this->has_access($obj_role, $this->hide_new_menu, $data_indexed, $row_data->get_menu_slug(), $row_data->get_parent_menu_slug())) {
                                wp_die($this->page_lock_data());
                                die();
                                return;
                            }
                        }
                    }
                }

                $menu = $this->wp_menu;
                $submenu = $this->wp_submenu;

                $prev_menu = NULL;
                foreach ($menu as $parent) {
                    if (!empty($submenu[$parent[2]])) {
                        foreach ($submenu[$parent[2]] as $child) {
                            if (!$this->has_access($obj_role, $this->hide_new_menu, $data_indexed, $this->escape_customize_slug($child[2]), $this->escape_customize_slug($parent[2]))) {
                                remove_submenu_page($parent[2], $child[2]);
                            }
                        }
                    }

//                if ($this->is_menu_separator($parent)) {
//                    if ($this->is_menu_separator($prev_menu)) {
//                        printf('%s <br />', $parent[2]);
//                        remove_menu_page($parent[2]);
//                    } else {
//                        $prev_menu = $parent;
//                    }
//                } else if (!$this->has_access($obj_role, $this->hide_new_menu, $data_indexed, $parent[2])) {
//                    remove_menu_page($parent[2]);
//                } else {
//                    $prev_menu = $parent;
//                }

                    if (!$this->has_access($obj_role, $this->hide_new_menu, $data_indexed, $parent[2])) {
                        remove_menu_page($parent[2]);
                    }
                }

                $primary = FALSE;
            }

            return $parent_file;
        }

        private function index_table_data($table_data) {
            foreach ($table_data as $value) {
                $menu_slug = $value->get_menu_slug();
                $parent_menu_slug = $value->get_parent_menu_slug();

                if ($parent_menu_slug === '') {
                    if (!isset($table_data[$menu_slug])) {
                        $table_data[$menu_slug] = (OBJECT) array(
                                    'entity' => $value,
                                    'children' => array()
                        );
                    }

                    $table_data[$menu_slug]->entity = $value;
                } else {
                    if (!isset($table_data[$parent_menu_slug])) {
                        $table_data[$parent_menu_slug] = (OBJECT) array(
                                    'entity' => NULL,
                                    'children' => array()
                        );
                    }

                    $table_data[$parent_menu_slug]->children[$menu_slug] = (OBJECT) array(
                                'entity' => $value,
                                'children' => array()
                    );
                }
            }
            return $table_data;
        }

        private function has_access($role, $hide_new_menu, $table_data, $menu_slug, $parent_menu_slug = '') {
            $menu_slug = html_entity_decode($menu_slug);
            $parent_menu_slug = html_entity_decode($parent_menu_slug);

            if ($parent_menu_slug === '') {
                if (isset($table_data[$menu_slug])) {
                    if ($table_data[$menu_slug]->entity === NULL)
                        return TRUE;

                    $menu = $this->wp_menu;
                    $submenu = $this->wp_submenu;

                    if (empty($submenu[$menu_slug]))
                        return $table_data[$menu_slug]->entity->get_enabled();

                    foreach ($submenu[$menu_slug] as $value) {
                        $submenu_slug = html_entity_decode($this->escape_customize_slug($value[2]));
                        if (isset($table_data[$menu_slug]->children[$submenu_slug])) {
                            if ($table_data[$menu_slug]->children[$submenu_slug]->entity->get_enabled() && $role->has_cap($this->escape_customize_capability($value[1])))
                                return TRUE;
                            else
                                continue;
                        }

                        return !$hide_new_menu;
                    }

                    return FALSE;
                }
                return !$hide_new_menu;
            }

            if (!isset($table_data[$parent_menu_slug]))
                return !$hide_new_menu;

            if (!isset($table_data[$parent_menu_slug]->children[$menu_slug]))
                return !$hide_new_menu;

            if ($table_data[$parent_menu_slug]->children[$menu_slug]->entity === NULL)
                return !$hide_new_menu;

            return $table_data[$parent_menu_slug]->children[$menu_slug]->entity->get_enabled();
        }

        private function get_override_type($role) {
            $entity = new WPFront_User_Role_Editor_Entity_Options();
            $value = $entity->get_option(self::$override_type_option_name . $role->name);
            if ($value === NULL)
                return self::OVERRIDE_TYPE_SOFT;

            return $value;
        }

        private function set_override_type($role, $value) {
            $entity = new WPFront_User_Role_Editor_Entity_Options();
            $entity->update_option(self::$override_type_option_name . $role->name, $value);
        }

        private function delete_override_type($role) {
            $entity = new WPFront_User_Role_Editor_Entity_Options();
            $entity->delete_option(self::$override_type_option_name . $role->name);
        }

        private function get_hide_new_menus($role) {
            $entity = new WPFront_User_Role_Editor_Entity_Options();
            $value = $entity->get_option(self::$hide_new_menus_option_name . $role->name);
            if ($value === NULL)
                return FALSE;

            return (BOOL) $value;
        }

        private function set_hide_new_menus($role, $value) {
            $entity = new WPFront_User_Role_Editor_Entity_Options();
            $entity->update_option(self::$hide_new_menus_option_name . $role->name, $value);
        }

        private function delete_hide_new_menus($role) {
            $entity = new WPFront_User_Role_Editor_Entity_Options();
            $entity->delete_option(self::$hide_new_menus_option_name . $role->name);
        }

        private function get_disable_for_secondary_role($role) {
            $entity = new WPFront_User_Role_Editor_Entity_Options();
            $value = $entity->get_option(self::$disable_for_secondary_role_option_name . $role->name);
            if ($value === NULL)
                return FALSE;

            return (BOOL) $value;
        }

        private function set_disable_for_secondary_role($role, $value) {
            $entity = new WPFront_User_Role_Editor_Entity_Options();
            $entity->update_option(self::$disable_for_secondary_role_option_name . $role->name, $value);
        }

        private function delete_disable_for_secondary_role($role) {
            $entity = new WPFront_User_Role_Editor_Entity_Options();
            $entity->delete_option(self::$disable_for_secondary_role_option_name . $role->name);
        }

        public function copy_menus_callback() {
            if (!$this->can_edit_role_menus()) {
                echo '[]';
                die();
            }

            if (!empty($_POST['role'])) {
                $role = $_POST['role'];

                $entity = new WPFront_User_Role_Editor_Entity_Menu_Editor();
                $data = $entity->get_all_by_role($role);
                $json = array();
                foreach ($data as $value) {
                    if (!$value->get_enabled())
                        $json[] = '["' . esc_attr(urlencode($value->get_menu_slug())) . '","' . esc_attr(urlencode($value->get_parent_menu_slug())) . '"]';
                }

                echo '[' . implode(',', $json) . ']';
            }

            die();
        }

        public function enqueue_scripts() {
            wp_enqueue_script('jquery');
        }

        private function page_lock_data() {
            return "<form id='wpfront-form' method='POST'>"
                    . "<input type='hidden' name='wpfront-user-role-editor-menu-editor-page-lock' value='1' />"
                    . "</form>"
                    . "<script type='text/javascript'>"
                    . "if(typeof(jQuery) === 'function') {"
                    . "jQuery('form').submit();"
                    . "} else {"
                    . "document.getElementById('wpfront-form').submit();"
                    . "}"
                    . "</script>";
        }

        private function check_page_lock_data() {
            if (!empty($_POST['wpfront-user-role-editor-menu-editor-page-lock'])) {
                $this->main->permission_denied();
                die();
            }
        }

        private function is_menu_separator($menu) {
            if ($menu == NULL)
                return FALSE;

            if ($menu[0] == self::MENU_SEPARATOR_TITLE || $menu[4] == self::MENU_SEPARATOR_CLASS)
                return TRUE;
            return FALSE;
        }

        private function remove_html($content) {
            //return preg_replace('/<span\b[^>]*>(.*?)<\/span>/i', '', $content);
            return strip_tags($content);
        }

        private function escape_customize_slug($slug) { //customize.php
            if (strpos($slug, 'customize.php') === 0) {
                $slug = urldecode(remove_query_arg('return', $slug));
                $slug = str_replace('#038;', '', $slug); //prev function has #038;, WP4.2 bug?
            }
            return $slug;
        }

        private function escape_customize_capability($cap) { //customize.php
            if ($cap === 'customize') {
                $cap = 'edit_theme_options';
            }
            return $cap;
        }

        private function decode_menu_key($key) {
            return htmlspecialchars_decode(urldecode($key));
        }

        public function manage_roles_columns($columns) {
            $columns[self::EDITED_MENU_COLUMN_KEY] = 'Menu Edited';
            return $columns;
        }

        public function manage_roles_custom_column($value, $column_name, $role_name) {
            if ($column_name === self::EDITED_MENU_COLUMN_KEY) {
                $entity = new WPFront_User_Role_Editor_Entity_Options();
                $entity = $entity->get_by_option_name(self::$override_type_option_name . $role_name);
                if ($entity === NULL) {
                    return '';
                } else {
                    return sprintf('<img class="user-default" src="%s" />', $this->image_url() . 'check-icon.png');
                }
            }

            return $value;
        }

        public function role_row_actions($actions, $role_object) {
            if ($this->can_edit_role_menus() && $role_object->name !== self::ADMINISTRATOR_ROLE_KEY)
                $actions['edit_menu'] = sprintf('<a href="%s">%s</a>', $this->get_edit_menu_url($role_object->name), $this->__('Edit Menu'));
            return $actions;
        }

        protected function add_help_tab() {
            return array(
                array(
                    'id' => 'overview',
                    'title' => $this->__('Overview'),
                    'content' => '<p>'
                    . $this->__('This screen allows you to enable and disable admin menus for a particular role.')
                    . '</p>'
                    . '<p>'
                    . $this->__('Deselect a menu from the grid to remove that menu.')
                    . '</p>'
                    . '<p>'
                    . $this->__("Disabled menu items are already hidden, because the selected role doesn't have the capability to display that menu. Grid displays menus which the current user has access to.")
                    . '</p>'
                ),
                array(
                    'id' => 'fields',
                    'title' => $this->__('Fields'),
                    'content' => '<p><strong>'
                    . $this->__('Override Role')
                    . '</strong>: '
                    . $this->__('The role you want to override.')
                    . '</p>'
                    . '<p><strong>'
                    . $this->__('Override Type')
                    . '</strong>: '
                    . $this->__('Soft - Pages may be available through directly typing the URL. Hard - Even URLs will be blocked.')
                    . '</p>'
                    . '<p><strong>'
                    . $this->__('Hide New Menus')
                    . '</strong>: '
                    . $this->__('Allow you to hide future menus.')
                    . '</p>'
                    . '<p><strong>'
                    . $this->__('Disable For Secondary Role')
                    . '</strong>: '
                    . $this->__('Disables menu settings while the selected role is a secondary role.')
                    . '</p>'
                ),
                array(
                    'id' => 'restore',
                    'title' => $this->__('Restore Default'),
                    'content' => '<p>'
                    . $this->__('Select "Restore default" from the "Copy from" drop down and click apply.')
                    . '</p>'
                )
            );
        }

        protected function set_help_sidebar() {
            return array(
                array(
                    $this->__('Documentation on Menu Editor'),
                    'menu-editor/'
                )
            );
        }

    }

}