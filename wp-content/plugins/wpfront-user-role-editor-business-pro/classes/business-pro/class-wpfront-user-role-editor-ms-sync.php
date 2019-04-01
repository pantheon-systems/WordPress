<?php

if (!defined('ABSPATH')) {
    exit();
}

require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-ms-sync-sites-list-table.php");

if (!class_exists('WPFront_User_Role_Editor_MS_Sync')) {

    /**
     * Multisite sync
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_MS_Sync extends WPFront_User_Role_Editor_Business_Pro_Controller_Base {

        const MENU_SLUG = 'wpfront-user-role-editor-ms-sync';

        protected $step = 1;
        protected $source_blogid = NULL;
        protected $blogs = 'all';
        protected $selected_blogs = array();
        protected $add = FALSE;
        protected $overwrite = FALSE;
        protected $remove = FALSE;
        protected $update_new_user_default = FALSE;
        protected $error = NULL;

        public function __construct($main) {
            parent::__construct($main);

            $this->ajax_register('wp_ajax_wpfront_user_role_editor_sync_roles_step2_site_list', array($this, 'step2_site_list'));
            $this->ajax_register('wp_ajax_wpfront_user_role_editor_sync_roles_sync_blog', array($this, 'sync_blog'));
        }

        public function sync_roles() {
            if (!$this->can_manage_network_roles()) {
                $this->main->permission_denied();
            }

            if (!empty($_GET['step'])) {
                $this->step = (int) $_GET['step'];

                if ($this->step < 1)
                    $this->step = 1;

                if ($this->step > 4)
                    $this->step = 4;
            }

            if (!empty($_GET['sb']))
                $this->source_blogid = (int) $_GET['sb'];
            if ($this->source_blogid === 0)
                $this->source_blogid = NULL;

            if ($this->step === 2) {
                if ($this->source_blogid === NULL)
                    $this->error = $this->__('Source site not selected.');

                if (!empty($_POST['paged']))
                    unset($_REQUEST['paged']);
            }

            if ($this->step === 3) {
                if ($this->source_blogid === NULL)
                    $this->error = $this->__('Source site not selected.');

                if (empty($_POST['blogs'])) {
                    $this->error = $this->__('Destination type not selected.');
                    $this->step = 2;
                } else
                    $this->blogs = $_POST['blogs'];

                if ($this->blogs === 'selected') {
                    if (empty($_POST['selected-blogs'])) {
                        $this->error = $this->__('Destination sites not selected.');
                        $this->step = 2;
                    } else
                        $this->selected_blogs = explode(',', $_POST['selected-blogs']);
                }
            }

            if ($this->step === 4) {
                if ($this->source_blogid === NULL)
                    $this->error = $this->__('Source site not selected.');

                if (empty($_POST['blogs'])) {
                    $this->error = $this->__('Destination type not selected.');
                    $this->step = 2;
                } else
                    $this->blogs = $_POST['blogs'];

                if ($this->blogs === 'selected') {
                    if (empty($_POST['selected-blogs'])) {
                        $this->error = $this->__('Destination sites not selected.');
                        $this->step = 2;
                    } else
                        $this->selected_blogs = explode(',', $_POST['selected-blogs']);
                }

                if (!empty($_POST['add']))
                    $this->add = TRUE;

                if (!empty($_POST['overwrite']))
                    $this->overwrite = TRUE;

                if (!empty($_POST['remove']))
                    $this->remove = TRUE;

                if (!empty($_POST['update-new-user-default']))
                    $this->update_new_user_default = TRUE;
            }

            if ($this->source_blogid === NULL)
                $this->step = 1;

            $this->include_template();
        }

        public function step2_site_list() {
            $this->step = 'sitelist';
            $this->include_template();
            die();
        }

        public function sync_blog() {
            if (!$this->can_manage_network_roles()) {
                echo json_encode((OBJECT) array('name' => '', 'url' => '', 'result' => FALSE));
                die();
            }

            check_ajax_referer($_POST['referer'], 'nonce');

            if (!isset($_POST['source'])) {
                echo 'null';
                die();
            }
            $source = (int) $_POST['source'];

            if (!isset($_POST['destination'])) {
                echo 'null';
                die();
            }
            $destination = (int) $_POST['destination'];

            if (!isset($_POST['add'])) {
                echo 'null';
                die();
            }
            $add = (bool) $_POST['add'];

            if (!isset($_POST['overwrite'])) {
                echo 'null';
                die();
            }
            $overwrite = (bool) $_POST['overwrite'];

            if (!isset($_POST['remove'])) {
                echo 'null';
                die();
            }
            $remove = (bool) $_POST['remove'];

            if (!isset($_POST['update_new_user_default'])) {
                echo 'null';
                die();
            }
            $update_new_user_default = (bool) $_POST['update_new_user_default'];

            $blog = get_blog_details($destination);
            if ($blog === FALSE) {
                echo 'null';
                die();
            }

            $result = TRUE;

            if ($source !== $destination) {
                switch_to_blog($source);
                global $wp_roles;
                $source_roles = $wp_roles->role_objects;
                $source_display_names = $wp_roles->get_names();
                $source_default_role = get_option('default_role');

                switch_to_blog($destination);
                global $wp_roles;
                $destination_roles = $wp_roles->role_objects;

                foreach ($source_roles as $source_role_name => $source_role_object) {
                    $role_obj = get_role($source_role_name);
                    if ($role_obj === NULL && $add) {
                        add_role($source_role_name, $source_display_names[$source_role_name], $source_role_object->capabilities);
                    }

                    if ($role_obj !== NULL && $overwrite) {
                        WPFront_User_Role_Editor_Add_Edit::update_role($source_role_name, $source_display_names[$source_role_name], $source_role_object->capabilities);
                    }
                }

                foreach ($destination_roles as $destination_role_name => $destination_role_object) {
                    if ($remove && !array_key_exists($destination_role_name, $source_roles)) {
                        remove_role($destination_role_name);
                    }
                }

                if ($update_new_user_default && get_role($source_default_role) !== NULL) {
                    update_option('default_role', $source_default_role);
                }

                restore_current_blog();
            }

            echo json_encode((OBJECT) array('name' => $blog->blogname, 'url' => $blog->siteurl, 'result' => $result));
            die();
        }

        protected function include_template() {
            include($this->main->pluginDIR() . 'templates/business-pro/sync-roles.php');
        }

        protected function add_help_tab() {
            return array(
                array(
                    'id' => 'overview',
                    'title' => $this->__('Overview'),
                    'content' => '<p>'
                    . $this->__('This screen allows you to sync the roles between the network.')
                    . '</p>'
                    . '<p>'
                    . $this->__('You will select a site as source, then sync the roles to selected sites or entire sites within the network.')
                    . '</p>'
                ),
                array(
                    'id' => 'step1',
                    'title' => $this->__('Step 1'),
                    'content' => '<p><strong>'
                    . $this->__('Select source site.')
                    . '</strong></p>'
                    . '<p>'
                    . $this->__('You will select the source site in this step.')
                    . '</p>'
                ),
                array(
                    'id' => 'step2',
                    'title' => $this->__('Step 2'),
                    'content' => '<p><strong>'
                    . $this->__('Select destination sites.')
                    . '</strong></p>'
                    . '<p>'
                    . $this->__('You will select the destination sites in this step.')
                    . '</p>'
                    . '<p>'
                    . $this->__('Use "All Sites" to select all the sites within the network or you can select individual sites using "Selected Sites".')
                    . '</p>'
                ),
                array(
                    'id' => 'step3',
                    'title' => $this->__('Step 3'),
                    'content' => '<p><strong>'
                    . $this->__('Choose settings.')
                    . '</strong></p>'
                    . '<p><strong>'
                    . $this->__('Add roles existing only in source')
                    . '</strong>: '
                    . $this->__('Roles existing in source and do not existing in destination will be created.')
                    . '</p>'
                    . '<p><strong>'
                    . $this->__('Overwrite existing roles')
                    . '</strong>: '
                    . $this->__('If the role from source already exists in destination, it will be overwritten.')
                    . '</p>'
                    . '<p><strong>'
                    . $this->__('Remove roles existing only in destination')
                    . '</strong>: '
                    . $this->__('If a role do not exist in source, but exists in destination it will be removed.')
                    . '</p>'
                    . '<p><strong>'
                    . $this->__('Update new user default role')
                    . '</strong>: '
                    . $this->__('The destination new user default role will be updated as same as source.')
                    . '</p>'
                ),
                array(
                    'id' => 'step4',
                    'title' => $this->__('Step 4'),
                    'content' => '<p><strong>'
                    . $this->__('Confirm and Sync')
                    . '</strong></p>'
                    . '<p>'
                    . $this->__('You can verify your settings and start syncing.')
                    . '</p>'
                    . '<p>'
                    . $this->__('Click Sync Roles to start syncing.')
                    . '</p>'
                )
            );
        }

        protected function set_help_sidebar() {
            return array(
                array(
                    $this->__('Documentation on Multisite Sync'),
                    'multisite-sync-roles/'
                )
            );
        }

    }

}
