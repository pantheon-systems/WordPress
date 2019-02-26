<?php

if (!defined('ABSPATH')) {
    exit();
}

require_once(plugin_dir_path(__FILE__) . "../personal-pro/class-wpfront-user-role-editor-personal-pro.php");

if (!class_exists('WPFront_User_Role_Editor_Business_Pro_Base')) {

    /**
     * Main class of WPFront User Role Editor Pro Plugin
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Business_Pro_Base extends WPFront_User_Role_Editor_Personal_Pro_Base {

        protected $objMSList;
        protected $objMSAddEdit;
        protected $objMSRestore;
        protected $objMSSync;

        function __construct($product = 'WPFront User Role Editor Business Pro') {
            parent::__construct($product);

            if (is_multisite()) {
                if ($this->objGoPro->has_license()) {
                    $this->objMSList = new WPFront_User_Role_Editor_MS_List($this);
                    $this->objMSAddEdit = new WPFront_User_Role_Editor_MS_Add_Edit($this);
                    $this->objMSRestore = new WPFront_User_Role_Editor_MS_Restore($this);
                    $this->objMSSync = new WPFront_User_Role_Editor_MS_Sync($this);
                }

                add_action('network_admin_menu', array($this, 'network_admin_menu'));
            }

            //add_filter('wp_is_large_network', create_function('', 'return TRUE;'));
        }

        protected function plugin_dir_suffix() {
            return '-business-pro';
        }

        protected function notice_action() {
            if (is_multisite()) {
                return 'network_admin_notices';
            }

            return parent::notice_action();
        }

        protected function go_pro_url() {
            if (is_multisite())
                return network_admin_url('admin.php?page=' . WPFront_User_Role_Editor_Go_Pro::MENU_SLUG);

            return parent::go_pro_url();
        }

        public function admin_init() {
            parent::admin_init();
        }

        protected function add_pro_page() {
            if (is_multisite())
                return;

            parent::add_pro_page();
        }

        public function enqueue_role_pro_scripts() {
            parent::enqueue_role_pro_scripts();
        }

        public function enqueue_role_pro_styles() {
            parent::enqueue_role_pro_styles();
        }

        public function network_admin_menu() {
            if ($this->objGoPro->has_license())
                $menu_slug = WPFront_User_Role_Editor_MS_List::MENU_SLUG;
            else
                $menu_slug = WPFront_User_Role_Editor_Go_Pro::MENU_SLUG;

            add_menu_page($this->__('Roles'), $this->__('Roles'), 'manage_network_roles', $menu_slug, NULL, $this->pluginURL() . 'images/roles_menu.png', '9.999999');

            if ($this->objGoPro->has_license()) {
                $page_hook_suffix = add_submenu_page($menu_slug, $this->__('Roles'), $this->__('All Roles'), 'manage_network_roles', $menu_slug, array($this->objMSList, 'list_roles'));
                $this->objMSList->set_page_hook($page_hook_suffix);
                add_action('admin_print_scripts-' . $page_hook_suffix, array($this, 'enqueue_role_pro_scripts'));
                add_action('admin_print_styles-' . $page_hook_suffix, array($this, 'enqueue_role_pro_styles'));

                $page_hook_suffix = add_submenu_page($menu_slug, $this->__('Add New Role'), $this->__('Add New'), 'manage_network_roles', WPFront_User_Role_Editor_Add_Edit::MENU_SLUG, array($this->objMSAddEdit, 'add_edit_role'));
                $this->objMSAddEdit->set_page_hook($page_hook_suffix);
                add_action('admin_print_scripts-' . $page_hook_suffix, array($this, 'enqueue_role_pro_scripts'));
                add_action('admin_print_styles-' . $page_hook_suffix, array($this, 'enqueue_role_pro_styles'));

                $page_hook_suffix = add_submenu_page($menu_slug, $this->__('Restore Role'), $this->__('Restore'), 'manage_network_roles', WPFront_User_Role_Editor_Restore::MENU_SLUG, array($this->objMSRestore, 'restore_role'));
                $this->objMSRestore->set_page_hook($page_hook_suffix);
                add_action('admin_print_scripts-' . $page_hook_suffix, array($this, 'enqueue_role_pro_scripts'));
                add_action('admin_print_styles-' . $page_hook_suffix, array($this, 'enqueue_role_pro_styles'));

                $page_hook_suffix = add_submenu_page($menu_slug, $this->__('Sync Roles'), $this->__('Sync'), 'manage_network_roles', WPFront_User_Role_Editor_MS_Sync::MENU_SLUG, array($this->objMSSync, 'sync_roles'));
                $this->objMSSync->set_page_hook($page_hook_suffix);
                add_action('admin_print_scripts-' . $page_hook_suffix, array($this, 'enqueue_role_pro_scripts'));
                add_action('admin_print_styles-' . $page_hook_suffix, array($this, 'enqueue_role_pro_styles'));

                $page_hook_suffix = add_submenu_page($menu_slug, $this->__('Settings'), $this->__('Settings'), 'manage_network_roles', WPFront_User_Role_Editor_Options::MENU_SLUG, array($this->options, 'settings'));
                $this->options->set_page_hook($page_hook_suffix);
                add_action('admin_print_scripts-' . $page_hook_suffix, array($this, 'enqueue_role_pro_scripts'));
                add_action('admin_print_styles-' . $page_hook_suffix, array($this, 'enqueue_role_pro_styles'));
            }

            $page_hook_suffix = add_submenu_page($menu_slug, $this->__('Go Pro'), '<span class="wpfront-go-pro">' . $this->__('Go Pro') . '</span>', 'manage_network_roles', WPFront_User_Role_Editor_Go_Pro::MENU_SLUG, array($this->objGoPro, 'go_pro'));
            add_action('admin_print_scripts-' . $page_hook_suffix, array($this, 'enqueue_role_pro_scripts'));
            add_action('admin_print_styles-' . $page_hook_suffix, array($this, 'enqueue_role_pro_styles'));
        }

        public function large_network_warning() {
            if (wp_is_large_network()) {
                if ($this->options->ms_enable_large_network_functionalities())
                    return FALSE;

                echo '<p>'
                . $this->__('You have a large network of sites (usually 10,000 or more sites). '
                        . 'Roles functionalities will be CPU and memory intensive on large networks. '
                        . 'Use the settings screen to turn on these CPU and memory intensive functionalities.')
                . '</p>';
                return TRUE;
            }
            return FALSE;
        }

        public function enable_multisite_only_options($multisite) {
            if(is_multisite())
                return $multisite;

            return TRUE;
        }

    }

}

require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-business-pro-controller-base.php");
require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-ms-list.php");
require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-ms-add-edit.php");
require_once(plugin_dir_path(__FILE__) . "../class-wpfront-user-role-editor-add-edit.php");
require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-ms-restore.php");
require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-ms-sync.php");
require_once(plugin_dir_path(__FILE__) . "../class-wpfront-user-role-editor-options.php");
