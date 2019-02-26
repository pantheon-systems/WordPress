<?php
if (!defined('ABSPATH')) {
    exit();
}

require_once(plugin_dir_path(__FILE__) . "../class-wpfront-user-role-editor.php");
require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-personal-pro-controller-base.php");
require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-export.php");
require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-import.php");
require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-menu-editor.php");
require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-post-permissions.php");
require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-page-permissions.php");
require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-custom-post-types.php");
require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-custom-post-permissions.php");
require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-nav-menu-pro.php");
require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-widget-permissions-pro.php");
require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-content-shortcodes.php");
require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-login-redirect-pro.php");
require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-attachment-permissions-pro.php");
require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-user-permissions-pro.php");
require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-bulk-edit.php");
require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-bulk-edit-extended-permissions.php");
require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-plugin-updater.php");

if (!class_exists('WPFront_User_Role_Editor_Personal_Pro_Base')) {

    /**
     * Main class of WPFront User Role Editor Pro Plugin
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Personal_Pro_Base extends WPFront_User_Role_Editor {

        protected $product;
        protected $objExport;
        protected $objImport;
        protected $objMenuEditor;
        protected $objPostPermissions;
        protected $objPagePermissions;
        protected $objCustomPostTypes;
        protected $objContentShortcodes;
        protected $objBulkEdit;

        function __construct($product = 'WPFront User Role Editor Personal Pro') {
            $this->objNavMenu = TRUE;
            $this->objLoginRedirect = TRUE;
            $this->objAttachmentPermissions = TRUE;
            $this->objUserPermissions = TRUE;
            $this->objWidgetPermissions = TRUE;

            parent::__construct();

            $this->product = $product;

            $this->objGoPro->set_license(self::PLUGIN_SLUG . $this->plugin_dir_suffix(), $product);

            if ($this->objGoPro->has_license()) {
                $this->objExport = new WPFront_User_Role_Editor_Export($this);
                $this->objImport = new WPFront_User_Role_Editor_Import($this);
                $this->objMenuEditor = new WPFront_User_Role_Editor_Menu_Editor($this);
                $this->objPostPermissions = new WPFront_User_Role_Editor_Post_Permissions($this);
                $this->objPagePermissions = new WPFront_User_Role_Editor_Page_Permissions($this);
                $this->objCustomPostTypes = new WPFront_User_Role_Editor_Custom_Post_Types($this);
                $this->objNavMenu = new WPFront_User_Role_Editor_Nav_Menu_Pro($this);
                $this->objAttachmentPermissions = new WPFront_User_Role_Editor_Attachment_Permissions_Pro($this);
                $this->objContentShortcodes = new WPFront_User_Role_Editor_Content_Shortcodes($this);
                $this->objLoginRedirect = new WPFront_User_Role_Editor_Login_Redirect_Pro($this);
                $this->objUserPermissions = new WPFront_User_Role_Editor_User_Permissions_Pro($this);
                $this->objBulkEdit = new WPFront_User_Role_Editor_Bulk_Edit($this);
                $this->objWidgetPermissions = new WPFront_User_Role_Editor_Widget_Permissions_Pro($this);
            }
        }

        protected function plugin_dir_suffix() {
            return '-personal-pro';
        }

        protected function notice_action() {
            return 'all_admin_notices';
        }

        protected function go_pro_url() {
            return admin_url('admin.php?page=' . WPFront_User_Role_Editor_Go_Pro::MENU_SLUG);
        }

        public function admin_init() {
            parent::admin_init();

            if (!$this->objGoPro->has_license() && current_user_can('manage_options')) {
                add_action($this->notice_action(), array($this, 'admin_notices'));
            }
        }

        public function admin_menu() {
            if ($this->objGoPro->has_license()) {
                $this->add_submenu_page(40, $this->__('Menu Editor'), $this->__('Menu Editor'), 'edit_role_menus', WPFront_User_Role_Editor_Menu_Editor::MENU_SLUG, array($this->objMenuEditor, 'menu_editor'), 'enqueue_role_pro_scripts', 'enqueue_role_pro_styles', $this->objMenuEditor);
                $this->add_submenu_page(45, $this->__('Content Shortcodes'), $this->__('Shortcodes'), 'edit_content_shortcodes', WPFront_User_Role_Editor_Content_Shortcodes::MENU_SLUG, array($this->objContentShortcodes, 'content_shortcodes'), 'enqueue_role_pro_scripts', 'enqueue_role_pro_styles', $this->objContentShortcodes);
                $this->add_submenu_page(50, $this->__('Export Roles'), $this->__('Export'), 'export', WPFront_User_Role_Editor_Export::MENU_SLUG, array($this->objExport, 'export_roles'), 'enqueue_role_pro_scripts', 'enqueue_role_pro_styles', $this->objExport);
                $this->add_submenu_page(60, $this->__('Import Roles'), $this->__('Import'), 'import', WPFront_User_Role_Editor_Import::MENU_SLUG, array($this->objImport, 'import_roles'), 'enqueue_role_pro_scripts', 'enqueue_role_pro_styles', $this->objImport);
                $this->add_submenu_page(70, $this->__('Bulk Edit'), $this->__('Bulk Edit'), 'bulk_edit_roles', WPFront_User_Role_Editor_Bulk_Edit::MENU_SLUG, array($this->objBulkEdit, 'bulk_edit'), 'enqueue_role_pro_scripts', 'enqueue_role_pro_styles', $this->objBulkEdit);

                $this->remove_submenu_page(35); //Add/Remove cap
            }

            parent::admin_menu();
        }

        public function enqueue_role_pro_scripts() {
            parent::enqueue_role_scripts();
        }

        public function enqueue_role_pro_styles() {
            parent::enqueue_role_styles();

            $styleRoot = $this->pluginURLRoot . 'css/personal-pro/';
            wp_enqueue_style('wpfront-user-role-editor-personal-pro-styles', $styleRoot . 'style.css', array(), WPFront_User_Role_Editor::VERSION);
        }

        public function admin_notices() {
            ?>
            <div class="update-nag" style="display: block;">
                <?php printf($this->__('%s license not activated.'), $this->__($this->product)); ?>
                <a href="<?php echo $this->go_pro_url(); ?>"><?php echo $this->__('Activate'); ?></a>
            </div>
            <?php
        }

        public function action_links($links, $file) {
            if ($this->objGoPro->has_license())
                return parent::action_links($links, $file);

            return $links;
        }

        public function enable_pro_only_options() {
            return TRUE;
        }
        
        public function get_extendable_post_types() {
            return WPFront_User_Role_Editor_Post_Type_Permissions::get_extendable_post_types();
        }

        public function get_extended_post_types_info() {
            return WPFront_User_Role_Editor_Post_Type_Permissions::get_extended_post_types_info();
        }

    }

}


