<?php

if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('WPFront_User_Role_Editor_Import')) {

    /**
     * Import Roles
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Import extends WPFront_User_Role_Editor_Personal_Pro_Controller_Base {

        const MENU_SLUG = 'wpfront-user-role-editor-import';
        const GENERATOR = WPFront_User_Role_Editor_Export::GENERATOR;
        const VERSION = WPFront_User_Role_Editor_Export::VERSION;

        private $result = NULL;
        private $file_id;
        private $import_data = NULL;
        private $roles_data = NULL;

        function __construct($main) {
            parent::__construct($main);
        }

        public function import_roles() {
            if (!$this->can_import()) {
                $this->main->permission_denied();
                return;
            }

            if (!extension_loaded('simplexml')) {
                echo '<div class="error"><p>' . $this->__('This functionality requires SimpleXML extension, which is not loaded.') . '</p></div>';
                return;
            }

            if (!empty($_POST['submit'])) {
                check_admin_referer('import-upload');
                set_time_limit(0);

                if ($this->handle_upload()) {
                    $this->roles_data = array();

                    global $wp_roles;
                    foreach ($this->import_data->roles as $role) {
                        $this->roles_data[$role->name] = (OBJECT) array('display_name' => $role->display_name, 'override' => FALSE);
                        if (get_role($role->name) !== NULL)
                            $this->roles_data[$role->name]->override = TRUE;
                    }
                }
            }

            if (!empty($_POST['importroles'])) {
                $this->main->verify_nonce();
                set_time_limit(0);

                if (!empty($_POST['file-id'])) {
                    $this->file_id = $_POST['file-id'];

                    $this->parse_contents(get_attached_file($this->file_id));

                    if (is_wp_error($this->import_data)) {
                        $this->result = (OBJECT) array('success' => FALSE, 'message' => $this->import_data->get_error_message());
                        $this->import_data = NULL;
                    } else {
                        $this->roles_data = array();
                        if (!empty($_POST['import-roles'])) {
                            $this->roles_data = $_POST['import-roles'];
                            if (!is_array($this->roles_data))
                                $this->roles_data = array();
                        }
                        foreach ($this->import_data->roles as $role) {
                            if (array_key_exists($role->name, $this->roles_data)) {
                                if (get_role($role->name) === NULL) {
                                    add_role($role->name, $role->display_name, $role->capabilities);
                                } else {
                                    WPFront_User_Role_Editor_Add_Edit::update_role($role->name, $role->display_name, $role->capabilities);
                                }
                            }
                        }
                        $this->result = (OBJECT) array('success' => TRUE, 'message' => sprintf($this->__('%d role(s) imported.'), count($this->roles_data)));
                        $this->import_data = NULL;
                    }

                    wp_delete_attachment($this->file_id);
                }
            }

            include($this->main->pluginDIR() . 'templates/personal-pro/import-roles.php');
        }

        private function handle_upload() {
            $file = wp_import_handle_upload();

            $this->result = (OBJECT) array('success' => FALSE);
            $this->import_data = NULL;

            if (isset($file['error'])) {
                $this->result->message = esc_html($file['error']);
                return FALSE;
            } else if (!file_exists($file['file'])) {
                $this->result->message = sprintf($this->__('The export file could not be found at <code>%s</code>. It is likely that this was caused by a permissions problem.'), esc_html($file['file']));
                return FALSE;
            }

            $this->file_id = (int) $file['id'];

            $this->parse_contents($file['file']);
            if (is_wp_error($this->import_data)) {
                $this->result->message = $this->import_data->get_error_message();
                $this->import_data = NULL;
                return FALSE;
            }

            $this->result = NULL;
            return TRUE;
        }

        private function parse_contents($filePath) {
            libxml_use_internal_errors(true);

            $dom = new DOMDocument;
            $old_value = null;
            if (function_exists('libxml_disable_entity_loader')) {
                $old_value = libxml_disable_entity_loader(true);
            }
            $success = $dom->loadXML(file_get_contents($filePath));
            if (!is_null($old_value)) {
                libxml_disable_entity_loader($old_value);
            }

            if (!$success || isset($dom->doctype)) {
                $this->import_data = new WP_Error('SimpleXML_parse_error', $this->__('There was an error when reading this export file'), libxml_get_errors());
                return;
            }

            $xml = simplexml_import_dom($dom);
            unset($dom);

            if (!$xml) {
                $this->import_data = new WP_Error('SimpleXML_parse_error', $this->__('There was an error when reading this export file'), libxml_get_errors());
                return;
            }

            $this->import_data = (OBJECT) array();

            if (!$this->get_xml_data($xml, 'generator'))
                return;

            $this->import_data->generator = (string) $this->import_data->generator[0];

            if (!$this->get_xml_data($xml, 'version'))
                return;

            $this->import_data->version = (string) $this->import_data->version[0];

            if (version_compare(self::VERSION, $this->import_data->version, '<')) {
                $this->import_data = new WP_Error('parse_error', sprintf($this->__('Please update the plugin to latest version. This export file requires plugin version %s or higher.'), $this->import_data->version));
                return;
            }

            if (!$this->get_xml_data($xml, 'date'))
                return;

            $this->import_data->date = (string) $this->import_data->date[0];

            if (!$this->get_xml_data($xml, 'source'))
                return;

            $this->import_data->source = (string) $this->import_data->source[0];

            if (!$this->get_xml_data($xml, 'source_url'))
                return;

            $this->import_data->source_url = (string) $this->import_data->source_url[0];

            if (!$this->get_xml_data($xml, 'user_display_name'))
                return;

            $this->import_data->user_display_name = (string) $this->import_data->user_display_name[0];

            if (!$this->get_xml_data($xml, 'user_id'))
                return;

            $this->import_data->user_id = (string) $this->import_data->user_id[0];

            if (!$this->get_xml_data($xml, 'roles'))
                return;

            $this->import_data->roles = $this->import_data->roles[0];

            $roles = $this->import_data->roles->children();
            $this->import_data->roles = array();
            foreach ($roles as $role) {
                $obj = (OBJECT) array();
                $obj->name = (string) $role->name;
                $obj->display_name = (string) $role->display_name;
                $obj->capabilities = array();

                foreach ($role->capabilities->children() as $capability) {
                    if ((bool) $capability->value) {
                        $obj->capabilities[(string) $capability->name] = (bool) $capability->value;
                    }
                }

                $this->import_data->roles[] = $obj;
            }
        }

        private function get_xml_data($xml, $path) {
            $this->import_data->$path = $xml->xpath('/root/' . $path);

            if (empty($this->import_data->$path)) {
                $this->import_data = new WP_Error('parse_error', $this->__('This file does not appears to be a valid export file.'));
                return FALSE;
            }

            return TRUE;
        }

        protected function add_help_tab() {
            return array(
                array(
                    'id' => 'overview',
                    'title' => $this->__('Overview'),
                    'content' => '<p>'
                    . $this->__('Step 1: Select export file using the Browse button. Then click Upload file and Import.')
                    . '</p>'
                    . '<p>'
                    . $this->__('Step 2: Select the roles you want to import using the check boxes and click Import Roles.')
                    . '</p>'
                )
            );
        }

        protected function set_help_sidebar() {
            return array(
                array(
                    $this->__('Documentation on Import'),
                    'import-roles/'
                )
            );
        }

    }

}