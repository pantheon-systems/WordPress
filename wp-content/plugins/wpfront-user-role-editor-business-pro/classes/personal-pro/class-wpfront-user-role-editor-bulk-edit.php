<?php

if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('WPFront_User_Role_Editor_Bulk_Edit')) {

    /**
     * Bulk Edit
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2015 WPFront.com
     */
    class WPFront_User_Role_Editor_Bulk_Edit extends WPFront_User_Role_Editor_Personal_Pro_Controller_Base {

        const MENU_SLUG = 'wpfront-user-role-editor-bulk-edit';

        private $controller = NULL;
        private $can_add_remove_cap = FALSE;
        private $can_extended_perm = FALSE;

        function __construct($main) {
            parent::__construct($main);

            add_action('admin_init', array($this, 'admin_init'));
        }

        public function admin_init() {
            $this->can_add_remove_cap = $this->can_edit();
            $this->can_extended_perm = TRUE;

            if (!empty($_GET['bulk-edit-type'])) {
                switch ($_GET['bulk-edit-type']) {
                    case 'add-remove-capability':
                        if ($this->can_add_remove_cap) {
                            $this->controller = $this->main->get_submenu_page_item(35);
                            $this->controller = array($this->controller[7], $this->controller[4]);
                        }
                        break;
                    case 'extended-permissions':
                        if ($this->can_extended_perm) {
                            $this->controller = new WPFront_User_Role_Editor_Bulk_Edit_Extended_Permissions($this->main);
                            $this->controller = array($this->controller, array($this->controller, 'bulk_edit'));
                        }
                        break;
                }
            }
        }

        public function bulk_edit() {
            if (!$this->can_bulk_edit_roles()) {
                $this->main->permission_denied();
                return;
            }

            if ($this->controller === NULL) {
                include($this->main->pluginDIR() . 'templates/personal-pro/bulk-edit.php');
            } else {
                call_user_func($this->controller[1]);
            }
        }

        public function set_help_tab() {
            if ($this->controller === NULL) {
                parent::set_help_tab();
            } else {
                $this->controller[0]->set_help_tab();
            }
        }

        protected function add_help_tab() {
            return array(
                array(
                    'id' => 'overview',
                    'title' => $this->__('Overview'),
                    'content' => '<p>'
                    . $this->__('This screen allows you to bulk edit role permissions.')
                    . '</p>'
                    . '<p>'
                    . '<b>' . $this->__('Add or remove capability') . '</b>: ' . $this->__('Choose this option to add or remove capabilty from multiple roles.')
                    . '</p>'
                    . '<p>'
                    . '<b>' . $this->__('Extended permissions') . '</b>: ' . $this->__('Choose this option to bulk edit post extended permissions.')
                    . '</p>'
                )
            );
        }

        protected function set_help_sidebar() {
            return array(
                array(
                    $this->__('Documentation on Bulk Edit'),
                    'bulk-edit/'
                )
            );
        }

    }

}