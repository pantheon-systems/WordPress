<?php

if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('WPFront_User_Role_Editor_MS_Restore')) {

    /**
     * MS Restore Role
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_MS_Restore extends WPFront_User_Role_Editor_Restore {

        public function __construct($main) {
            parent::__construct($main);

            self::$restore_action = array($this, 'restore_role_process');
        }

        public function restore_role() {
            $controller = new WPFront_User_Role_Editor_Business_Pro_Controller_Base($this->main);
            if (!$controller->can_manage_network_roles()) {
                $this->main->permission_denied();
                return;
            }

            if ($this->main->large_network_warning())
                return;

            $this->prepare_data(TRUE);
            $this->include_template();
        }

        public function restore_role_process() {
            if (empty($_POST['multisite'])) {
                self::$restore_action = NULL;
                parent::restore_role_callback();
                die();
            }

            check_ajax_referer($_POST['referer'], 'nonce');

            $controller = new WPFront_User_Role_Editor_Business_Pro_Controller_Base($this->main);
            if (!$controller->can_manage_network_roles()) {
                echo sprintf('{ "result": %s, "message": "%s" }', 'false', $this->__('ERROR') . ': ' . $this->main->__('Permission denied.'));
                die();
            }

            $blog_ids = $controller->get_ms_blog_ids();
            $success = TRUE;
            $partial_success = FALSE;
            $message = array();

            foreach ($blog_ids as $blog_id) {
                switch_to_blog($blog_id);

                $result = $this->action_restore();

                if ($result[0]) {
                    $partial_success = TRUE;
                } else {
                    $success = FALSE;
                    if (!in_array($this->main->__($result[1]), $message))
                        $message[] = $this->main->__($result[1]);
                }
            }

            if ($success) {
                echo sprintf('{ "result": %s, "message": "%s" }', 'true', '');
            } else {
                if ($partial_success)
                    echo sprintf('{ "result": %s, "message": "%s" }', 'false', $this->__('Partial Failure') . ': ' . implode(', ', $message));
                else
                    echo sprintf('{ "result": %s, "message": "%s" }', 'false', $this->__('ERROR') . ': ' . implode(', ', $message));
            }

            restore_current_blog();
            die();
        }

        protected function add_help_tab() {
            return array(
                array(
                    'id' => 'overview',
                    'title' => $this->__('Overview'),
                    'content' => '<p>'
                    . $this->__('This screen allows you to restore WordPress built-in roles to its standard capability settings.')
                    . '</p>'
                    . '<p>'
                    . $this->__('To restore a role, click the Restore button then Confirm.')
                    . '</p>'
                )
            );
        }

        protected function set_help_sidebar() {
            return array(
                array(
                    $this->__('Documentation on Multisite Restore'),
                    'multisite-restore-role/'
                )
            );
        }

    }

}
