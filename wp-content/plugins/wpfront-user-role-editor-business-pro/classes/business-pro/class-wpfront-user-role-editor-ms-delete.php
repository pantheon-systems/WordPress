<?php

if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('WPFront_User_Role_Editor_MS_Delete')) {

    /**
     * WPFront User Role Editor Plugin Multisite Delete Role
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_MS_Delete extends WPFront_User_Role_Editor_Delete {

        public function delete_role($delete_roles) {
            $controller = new WPFront_User_Role_Editor_Business_Pro_Controller_Base($this->main);
            if (!$controller->can_manage_network_roles()) {
                $this->main->permission_denied();
                return;
            }

            $delete_mode = FALSE;
            if (!empty($_POST['confirm-delete'])) {
                $this->main->verify_nonce();
                $delete_mode = TRUE;
            }

            $blog_ids = $controller->get_ms_blog_ids();
            $role_data = array();

            foreach ($blog_ids as $blog_id) {
                switch_to_blog($blog_id);

                $this->prepare_data($delete_roles);

                if ($delete_mode) {
                    $this->delete();
                } else {
                    foreach ($this->roles as $role_name => $value) {
                        if (isset($role_data[$role_name])) {
                            if (!in_array($value->display_name, $role_data[$role_name]->display_name))
                                $role_data[$role_name]->display_name[] = $value->display_name;
                            $role_data[$role_name]->is_deletable = $role_data[$role_name]->is_deletable || $value->is_deletable;
                            $role_data[$role_name]->status_message = $role_data[$role_name]->status_message === $value->status_message ? $value->status_message : '';
                        } else {
                            $role_data[$role_name] = (OBJECT) array(
                                        'name' => $value->name,
                                        'display_name' => array($value->display_name),
                                        'is_deletable' => $value->is_deletable,
                                        'status_message' => $value->status_message
                            );
                        }
                    }
                }
            }

            restore_current_blog();

            if($delete_mode) {
                printf('<script type="text/javascript">window.location.replace("%s");</script>', $controller->network_list_url());
                return;
            }

            foreach ($role_data as $key => $value) {
                $value->display_name = implode(', ', $value->display_name);
            }

            $this->roles = $role_data;

            $this->include_template();
        }

    }

}