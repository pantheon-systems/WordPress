<?php

if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('WPFront_User_Role_Editor_MS_Add_Edit')) {

    /**
     * MS Role Add Edit
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_MS_Add_Edit extends WPFront_User_Role_Editor_Add_Edit {

        public function __construct($main) {
            parent::__construct($main);

            self::$copy_capabilities_action = array($this, 'copy_capabilities_action');
        }

        public function add_edit_role($role_name) {
            $controller = new WPFront_User_Role_Editor_Business_Pro_Controller_Base($this->main);
            if (!$controller->can_manage_network_roles()) {
                $this->main->permission_denied();
                return;
            }

            if ($this->main->large_network_warning())
                return;

            $roles_info = $controller->get_ms_roles_info();
            if (isset($roles_info[$role_name]))
                $this->role = $roles_info[$role_name];

            if ($this->role == NULL) {
                $this->is_editable = TRUE;
            } else if ($role_name != self::ADMINISTRATOR_ROLE_KEY) {
                $this->is_editable = TRUE;
            }

            $success = FALSE;
            if (!empty($_POST['createrole'])) {
                while (TRUE) {
                    $this->main->verify_nonce();

                    if (!$this->is_editable)
                        break;

                    if (!$this->is_display_name_valid())
                        break;
                    if ($this->role == NULL && !$this->is_role_name_valid())
                        break;

                    $capabilities = array();
                    if (!empty($_POST['capabilities'])) {
                        foreach ($_POST['capabilities'] as $key => $value) {
                            $capabilities[$key] = TRUE;
                        }
                    }

                    if ($this->role == NULL) {
                        $role_name = $this->get_role_name();
                        if (array_key_exists($role_name, $roles_info)) {
                            $this->role_exists = TRUE;
                            break;
                        }

                        $blog_ids = $controller->get_ms_blog_ids();
                        $display_name = $this->get_display_name();
                        foreach ($blog_ids as $blog_id) {
                            switch_to_blog($blog_id);
                            add_role($role_name, $display_name, $capabilities);
                        }
                        restore_current_blog();
                    } else {
                        $display_name = $this->get_display_name();
                        foreach ($this->role->blogs as $blog_id) {
                            switch_to_blog($blog_id);
                            self::update_role($this->role->role_name, $display_name, $capabilities);
                        }
                        restore_current_blog();
                    }

                    $success = TRUE;
                    break;
                }
            }

            if ($success) {
                printf('<script type="text/javascript">window.location.replace("%s");</script>', $controller->network_list_url());
            } else {
                $this->include_template();
            }
        }

        protected function exclude_custom_post_types() {
            return TRUE;
        }

        protected function get_capability_groups() {
            $caps_group = parent::get_capability_groups();

            $controller = new WPFront_User_Role_Editor_Business_Pro_Controller_Base($this->main);
            $blog_ids = $controller->get_ms_blog_ids();

            foreach ($blog_ids as $blog_id) {
                switch_to_blog($blog_id);
                $this->main->reset_capabilities();

                $group = parent::get_capability_groups();
                foreach (WPFront_User_Role_Editor::$OTHER_CAPABILITIES as $other_key => $other_value) {
                    if (array_key_exists($other_key, $group)) {
                        if (array_key_exists($other_key, $caps_group)) {
                            $caps_group[$other_key]->caps = array_unique(array_merge($caps_group[$other_key]->caps, $group[$other_key]->caps));
                        } else {
                            $caps_group[$other_key] = $group[$other_key];
                        }
                    }
                }
            }

            restore_current_blog();

            return $caps_group;
        }

        protected function get_copy_from() {
            $copy_from = array();
            $controller = new WPFront_User_Role_Editor_Business_Pro_Controller_Base($this->main);

            foreach ($controller->get_ms_roles_info() as $role_name => $role_info) {
                $copy_from[$role_name] = $role_info->display_names[0];
            }

            return $copy_from;
        }

        public function copy_capabilities_action() {
            if (!isset($_POST['multisite'])) {
                self::$copy_capabilities_action = NULL;
                parent::copy_capabilities_callback();
                die();
            }

            check_ajax_referer($_POST['referer'], 'nonce');

            $controller = new WPFront_User_Role_Editor_Business_Pro_Controller_Base($this->main);
            $roles_info = $controller->get_ms_roles_info();

            if (!isset($_POST['role'])) {
                echo '{}';
                die();
            }

            $role = $_POST['role'];

            if (!array_key_exists($role, $roles_info)) {
                echo '{}';
                die();
            }

            $capabilities = $controller->get_ms_role_capabilities($role);

            echo json_encode($capabilities);

            restore_current_blog();
            die();
        }

        protected function get_display_name() {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (empty($_POST['display_name']))
                    return '';

                return esc_html(trim($_POST['display_name']));
            }

            if ($this->role == NULL)
                return '';

            return $this->role->display_names[0];
        }

        protected function get_role_name() {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (empty($_POST['role_name']))
                    return '';

                return preg_replace('/\W/', '', preg_replace('/ /', '_', trim($_POST['role_name'])));
            }

            if ($this->role == NULL)
                return '';
            return $this->role->role_name;
        }

        protected function capability_checked($cap) {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (empty($_POST['capabilities']))
                    return FALSE;

                return array_key_exists($cap, $_POST['capabilities']);
            }

            if ($this->role != NULL) {
                $controller = new WPFront_User_Role_Editor_Business_Pro_Controller_Base($this->main);
                $capabilities = $controller->get_ms_role_capabilities($this->role->role_name);
                if (array_key_exists($cap, $capabilities))
                    return $capabilities[$cap];
            }

            return FALSE;
        }

        protected function add_help_tab() {
            return array(
                array(
                    'id' => 'overview',
                    'title' => $this->__('Overview'),
                    'content' => '<p>'
                    . $this->__('This screen allows you to add a new role within your network.')
                    . '</p>'
                    . '<p>'
                    . $this->__('You can copy capabilities from existing roles using the Copy from drop down list. Select the role you want to copy from, then click Apply to copy the capabilities. You can select or deselect capabilities even after you copy.')
                    . '</p>'
                ),
                array(
                    'id' => 'displayname',
                    'title' => $this->__('Display Name'),
                    'content' => '<p>'
                    . $this->__('Use the Display Name field to set the display name for the new role. WordPress uses display name to display this role within your site. This field is required.')
                    . '</p>'
                ),
                array(
                    'id' => 'rolename',
                    'title' => $this->__('Role Name'),
                    'content' => '<p>'
                    . $this->__('Use the Role Name field to set the role name for the new role. WordPress uses role name to identify this role within your site. Once set role name cannot be changed. This field is required. This plugin will auto populate role name from the display name you have given, but you can change it.')
                    . '</p>'
                ),
                array(
                    'id' => 'capabilities',
                    'title' => $this->__('Capabilities'),
                    'content' => '<p>'
                    . $this->__('Capabilities are displayed as different groups for easy access. The Roles section displays capabilities created by this plugin. The Other Capabilities section displays non-standard capabilities within your site. These are usually created by plugins and themes. Use the check boxes to select the capabilities required for this new role.')
                    . '</p>'
                )
            );
        }

        protected function set_help_sidebar() {
            return array(
                array(
                    $this->__('Documentation on Multisite Add New Role'),
                    'multisite-add-new-role/'
                )
            );
        }

    }

}
