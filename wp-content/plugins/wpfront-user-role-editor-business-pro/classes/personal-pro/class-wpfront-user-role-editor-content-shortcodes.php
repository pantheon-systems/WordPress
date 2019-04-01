<?php

if (!defined('ABSPATH')) {
    exit();
}

require_once(plugin_dir_path(__FILE__) . "entities/class-wpfront-user-role-editor-entity-content-shortcodes.php");

if (!class_exists('WPFront_User_Role_Editor_Content_Shortcodes')) {

    /**
     * Content restriction shortcodes
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2015 WPFront.com
     */
    class WPFront_User_Role_Editor_Content_Shortcodes extends WPFront_User_Role_Editor_Personal_Pro_Controller_Base {

        const MENU_SLUG = 'wpfront-user-role-editor-content-shortcodes';
        const GUEST_ROLE_KEY = 'wpfront-content-shortcodes-guest-role-1';
        const USER_TYPE_ALL = 'all';
        const USER_TYPE_LOGGED_IN = 'loggedin';
        const USER_TYPE_GUEST = 'guest';
        const USER_TYPE_ROLES = 'roles';

        private $mode = 'LIST';
        private $ID = 0;
        private $name = '';
        private $nameValid = TRUE;
        private $shortcode = '';
        private $shortcodeValid = TRUE;
        private $userType = self::USER_TYPE_ALL;
        private $roles = array();
        private $error = FALSE;
        private $message = NULL;

        public function __construct($main) {
            parent::__construct($main);

            add_action('plugins_loaded', array($this, 'plugins_loaded'));
        }
        
        private function get_mode() {
            if (!empty($_GET['func'])) {
                switch ($_GET['func']) {
                    case 'edit':
                        $this->mode = 'EDIT';
                        break;
                    case 'delete':
                        $this->mode = 'DELETE';
                        break;
                }
            }

            if (!empty($_POST['action']) && $_POST['action'] === 'delete')
                $this->mode = 'DELETE';

            if (!empty($_POST['action2']) && $_POST['action2'] === 'delete')
                $this->mode = 'DELETE';
        }

        public function content_shortcodes() {
            if (!$this->can_edit_content_shortcodes()) {
                $this->main->permission_denied();
            }

            $this->main->verify_nonce('_shortcodes');

            $this->get_mode();

            if ($this->mode === 'DELETE' && !$this->can_delete_content_shortcodes()) {
                $this->main->permission_denied();
                return;
            }

            if (strtolower($_SERVER['REQUEST_METHOD']) === 'post') {
                switch ($this->mode) {
                    case 'EDIT':
                        if (!empty($_POST['id']))
                            $this->ID = $_POST['id'];

                        if (!empty($_POST['name']))
                            $this->name = $_POST['name'];

                        if (!empty($_POST['shortcode']))
                            $this->shortcode = $_POST['shortcode'];

                        if (!empty($_POST['user_type']))
                            $this->userType = $_POST['user_type'];

                        if (!empty($_POST['selected-roles']))
                            $this->roles = $_POST['selected-roles'];

                        if ($this->valid()) {
                            $entity = new WPFront_User_Role_Editor_Entity_Content_Shortcodes();
                            if ($this->ID > 0) {
                                $entity = $entity->get_by_id($this->ID);
                                if (empty($entity)) {
                                    $this->error = TRUE;
                                    $this->message = $this->__('Shortcode not found.');
                                } else {
                                    $this->message = $this->__('Shortcode updated successfully.');
                                }
                            } else {
                                $entity = $entity->get_by_shortcode($this->shortcode);
                                if (!empty($entity)) {
                                    $entity = NULL;
                                    $this->error = TRUE;
                                    $this->message = $this->__('Shortcode already exists.');
                                    $this->shortcodeValid = FALSE;
                                } else {
                                    $entity = new WPFront_User_Role_Editor_Entity_Content_Shortcodes();
                                    $entity->set_author(wp_get_current_user()->ID);
                                    $entity->set_created_on(current_time('mysql'));
                                    $entity->set_created_on_gmt(current_time('mysql', 1));
                                    $this->message = $this->__('Shortcode added successfully.');
                                }
                            }

                            if (!empty($entity)) {
                                $entity->set_name($this->name);
                                $entity->set_shortcode($this->shortcode);
                                $entity->set_user_type($this->userType);
                                $entity->set_roles($this->roles);
                                $this->ID = $entity->save();
                            }
                        }
                        break;

                    case 'DELETE':
                        if (isset($_POST['confirm-delete']) && !empty($_POST['delete-shortcode'])) {
                            $delete = $_POST['delete-shortcode'];
                            if (!is_array($delete))
                                $delete = array();
                            $delete = array_keys($delete);

                            $entity = new WPFront_User_Role_Editor_Entity_Content_Shortcodes();
                            foreach ($delete as $value) {
                                $value = intval($value);
                                $entity = $entity->get_by_id($value);
                                if (!empty($entity))
                                    $entity->delete();
                            }

                            $this->message = $this->__('Shortcode(s) deleted.');
                        } elseif (!empty($_POST['allcodes'])) {
                            $delete = $_POST['allcodes'];
                            if (!is_array($delete))
                                $delete = array();

                            $this->ID = array();
                            $this->name = array();
                            $this->shortcode = array();

                            $entity = new WPFront_User_Role_Editor_Entity_Content_Shortcodes();
                            foreach ($delete as $value) {
                                $value = intval($value);
                                $entity = $entity->get_by_id($value);
                                if (!empty($entity)) {
                                    $this->ID[] = $entity->get_id();
                                    $this->name[] = $entity->get_name();
                                    $this->shortcode[] = $entity->get_shortcode();
                                }
                            }
                        }
                        break;
                }
            } elseif (strtolower($_SERVER['REQUEST_METHOD']) === 'get') {
                switch ($this->mode) {
                    case 'EDIT':
                        if (!empty($_GET['edit'])) {
                            $this->ID = intval($_GET['edit']);
                            $entity = new WPFront_User_Role_Editor_Entity_Content_Shortcodes();
                            $entity = $entity->get_by_id($this->ID);
                            if (!empty($entity)) {
                                $this->name = $entity->get_name();
                                $this->shortcode = $entity->get_shortcode();
                                $this->userType = $entity->get_user_type();
                                $this->roles = $entity->get_roles();
                            }
                        }
                        break;
                    case 'DELETE':
                        if (!empty($_GET['delete'])) {
                            $this->ID = intval($_GET['delete']);
                            $entity = new WPFront_User_Role_Editor_Entity_Content_Shortcodes();
                            $entity = $entity->get_by_id($this->ID);
                            if (!empty($entity)) {
                                $this->name = $entity->get_name();
                                $this->shortcode = $entity->get_shortcode();
                            }
                        }
                        break;
                }
            }

            include($this->main->pluginDIR() . 'templates/personal-pro/content-shortcodes.php');
        }

        private function valid() {
            $this->ID = intval($this->ID);
            $this->name = trim($this->name);
            $this->shortcode = trim($this->shortcode);

            if (empty($this->name))
                $this->nameValid = FALSE;

            if (!preg_match('/^[a-z0-9_]+$/', $this->shortcode))
                $this->shortcodeValid = FALSE;

            if (!($this->userType === self::USER_TYPE_ALL || $this->userType === self::USER_TYPE_LOGGED_IN || $this->userType === self::USER_TYPE_GUEST || $this->userType === self::USER_TYPE_ROLES))
                $this->userType = self::USER_TYPE_ALL;

            if (!is_array($this->roles))
                $this->roles = array();

            $roles = $this->get_roles();
            foreach ($this->roles as $key => $value) {
                if (!isset($roles[$key])) {
                    unset($this->roles[$key]);
                }
            }

            $this->roles = array_keys($this->roles);

            return $this->nameValid && $this->shortcodeValid;
        }

        private function get_roles() {
            global $wp_roles;
            $roles = $wp_roles->role_names;

            unset($roles[self::ADMINISTRATOR_ROLE_KEY]);

            $roles[self::GUEST_ROLE_KEY] = $this->__('[Guest]');

            return $roles;
        }

        public function format_shortcode($shortcode) {
            return '[' . $shortcode . '] [/' . $shortcode . ']';
        }

        public function format_user_type($user_type) {
            switch ($user_type) {
                case self::USER_TYPE_ALL:
                    return $this->__('All Users');
                    break;

                case self::USER_TYPE_LOGGED_IN:
                    return $this->__('Logged-in Users');
                    break;

                case self::USER_TYPE_GUEST:
                    return $this->__('Guest Users');
                    break;

                case self::USER_TYPE_ROLES:
                    return $this->__('Users in Roles');
                    break;
            }
        }

        public function format_roles($roles) {
            $r = array($this->__('Administrator'));

            $allowed = $this->get_roles();
            foreach ($roles as $value) {
                if (isset($allowed[$value])) {
                    $r[] = $allowed[$value];
                }
            }

            return implode(', ', $r);
        }

        public function add_url() {
            return $this->get_content_shortcodes_url() . '&func=edit';
        }

        public function edit_url($id = NULL) {
            return $this->add_url() . '&edit=' . $id;
        }

        public function delete_url($id = NULL) {
            $url = $this->get_content_shortcodes_url() . '&func=delete';

            if (!empty($id)) {
                $url .= '&delete=' . $id;
            }

            return $url;
        }

        public function plugins_loaded() {
            $entity = new WPFront_User_Role_Editor_Entity_Content_Shortcodes();
            $shortcodes = $entity->get_all_shortcodes();

            foreach ($shortcodes as $code) {
                add_shortcode($code->get_shortcode(), array($this, 'process_shortcode'));
            }
        }

        public function process_shortcode($atts, $content, $tag) {
            $entity = new WPFront_User_Role_Editor_Entity_Content_Shortcodes();
            $entity = $entity->get_by_shortcode($tag);

            $has_access = FALSE;

            if (!empty($entity)) {
                switch ($entity->get_user_type()) {
                    case self::USER_TYPE_ALL:
                        $has_access = TRUE;
                        break;
                    case self::USER_TYPE_LOGGED_IN:
                        if (is_user_logged_in())
                            $has_access = TRUE;
                        break;
                    case self::USER_TYPE_GUEST:
                        if (!is_user_logged_in())
                            $has_access = TRUE;
                        break;
                    case self::USER_TYPE_ROLES:
                        $user_roles = array();

                        if (is_user_logged_in()) {
                            $user = wp_get_current_user();
                            $user_roles = $user->roles;
                        } else {
                            $user_roles[] = self::GUEST_ROLE_KEY;
                        }

                        if (!is_array($user_roles))
                            $user_roles = array();

                        $roles = $entity->get_roles();
                        $roles[] = self::ADMINISTRATOR_ROLE_KEY;
                        foreach ($roles as $role) {
                            if (in_array($role, $user_roles)) {
                                $has_access = TRUE;
                                break;
                            }
                        }
                        break;
                }
            }

            if ($has_access)
                return do_shortcode($content);

            return '';
        }

        protected function add_help_tab() {
            $this->get_mode();
            
            switch ($this->mode) {
                case 'EDIT':
                    return array(
                        array(
                            'id' => 'overview',
                            'title' => $this->__('Overview'),
                            'content' => '<p>'
                            . $this->__('This screen allows you to add or edit a shortcode within your site.')
                            . '</p>'
                        ),
                        array(
                            'id' => 'name',
                            'title' => $this->__('Name'),
                            'content' => '<p>'
                            . $this->__('Use the Name field to enter an identifiable name for this shortcode. This field is required.')
                            . '</p>'
                        ),
                        array(
                            'id' => 'shortcode',
                            'title' => $this->__('Shortcode'),
                            'content' => '<p>'
                            . $this->__('Use this field to enter the shortcode string, that will be used by WordPress. This field is required and has to be unique. Only lowercase letters, numbers and underscore is allowd in this field.')
                            . '</p>'
                        ),
                        array(
                            'id' => 'usertype',
                            'title' => $this->__('User Type'),
                            'content' => '<p>'
                            . $this->__('Select the type of users, the content within the shortcode will be displayed. You can select All Users, Logged in Users, Guest Users or Users within specific roles.')
                            . '</p>'
                        )
                    );
                case 'DELETE':
                    return array(
                        array(
                            'id' => 'overview',
                            'title' => $this->__('Overview'),
                            'content' => '<p>'
                            . $this->__('This screen allows you to delete the shortcodes you selected to delete.')
                            . '</p>'
                        )
                    );
            }

            return array(
                array(
                    'id' => 'overview',
                    'title' => $this->__('Overview'),
                    'content' => '<p>'
                    . $this->__('This screen displays all the content shortcodes existing within your site.')
                    . '</p>'
                    . '<p>'
                    . $this->__('To add a new shortcode, click the Add New button at the top of the screen.')
                    . '</p>'
                ),
                array(
                    'id' => 'columns',
                    'title' => $this->__('Columns'),
                    'content' => '<p><strong>'
                    . $this->__('Name')
                    . '</strong>: '
                    . $this->__('User identifiable name of the shortcode.')
                    . '</p>'
                    . '<p><strong>'
                    . $this->__('Shortcode')
                    . '</strong>: '
                    . $this->__('Used by WordPress to identify the shortcode.')
                    . '</p>'
                    . '<p><strong>'
                    . $this->__('User Type')
                    . '</strong>: '
                    . $this->__('Determines who all will see the content wrapped within the shortcode.')
                    . '</p>'
                    . '<p><strong>'
                    . $this->__('Roles')
                    . '</strong>: '
                    . $this->__('Roles used when User Type is "Users in Roles".')
                    . '</p>'
                ),
                array(
                    'id' => 'actions',
                    'title' => $this->__('Actions'),
                    'content' => '<p>'
                    . $this->__('Hovering over a row in the list will display action links that allow you to manage the shortcodes. You can perform the following actions:')
                    . '</p>'
                    . '<p><strong>'
                    . $this->__('Edit')
                    . '</strong>: '
                    . $this->__('Allows you to edit that shortcode.')
                    . '</p>'
                    . '<p><strong>'
                    . $this->__('Delete')
                    . '</strong>: '
                    . $this->__('Allows you to delete that shortcode.')
                    . '</p>'
                ),
                array(
                    'id' => 'usage',
                    'title' => $this->__('Usage'),
                    'content' => '<p>'
                    . $this->__('To use the shortcode within a post/page, copy the shortcode from the Shortcode column and put your content within the shortcode.')
                    . '</p>'
                    . '<p>'
                    . $this->__('Ex: [shortcode]Your content here.[/shortcode]')
                    . '</p>'
                )
            );
        }

        protected function set_help_sidebar() {
            return array(
                array(
                    $this->__('Documentation on Content Shortcodes'),
                    'content-restriction-shortcodes/'
                )
            );
        }

    }

}

