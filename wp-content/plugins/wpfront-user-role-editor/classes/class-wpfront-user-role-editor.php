<?php
/*
  WPFront User Role Editor Plugin
  Copyright (C) 2014, WPFront.com
  Website: wpfront.com
  Contact: syam@wpfront.com

  WPFront User Role Editor Plugin is distributed under the GNU General Public License, Version 3,
  June 2007. Copyright (C) 2007 Free Software Foundation, Inc., 51 Franklin
  St, Fifth Floor, Boston, MA 02110, USA

  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
  ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
  WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
  DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
  ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
  (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
  LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
  ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
  (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
  SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

if (!defined('ABSPATH')) {
    exit();
}

require_once(plugin_dir_path(__FILE__) . "base/class-wpfront-base.php");

if (!class_exists('WPFront_User_Role_Editor')) {

    /**
     * Main class of WPFront User Role Editor Plugin
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor extends WPFront_Base_URE {

        //Constants
        const VERSION = '2.14.2';
        const OPTIONS_GROUP_NAME = 'wpfront-user-role-editor-options-group';
        const OPTION_NAME = 'wpfront-user-role-editor-options';
        const PLUGIN_SLUG = 'wpfront-user-role-editor';

        public static $DYNAMIC_CAPS = array();
        public static $ROLE_CAPS = array(
            'list_roles',
            'create_roles',
            'edit_roles',
            'delete_roles',
            'edit_role_menus',
            'edit_posts_role_permissions',
            'edit_pages_role_permissions',
            'edit_nav_menu_permissions',
            'edit_content_shortcodes',
            'delete_content_shortcodes',
            'edit_login_redirects',
            'delete_login_redirects',
            'bulk_edit_roles',
            'edit_widget_permissions'
        );            

        public static $DEFAULT_ROLES = array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY, self::AUTHOR_ROLE_KEY, self::CONTRIBUTOR_ROLE_KEY, self::SUBSCRIBER_ROLE_KEY);
        public static $STANDARD_CAPABILITIES = array(
            'Dashboard' => array(
                'read' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY, self::AUTHOR_ROLE_KEY, self::CONTRIBUTOR_ROLE_KEY, self::SUBSCRIBER_ROLE_KEY),
                'edit_dashboard' => array(self::ADMINISTRATOR_ROLE_KEY)
            ),
            'Posts' => array(
                'publish_posts' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY, self::AUTHOR_ROLE_KEY),
                'edit_posts' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY, self::AUTHOR_ROLE_KEY, self::CONTRIBUTOR_ROLE_KEY),
                'delete_posts' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY, self::AUTHOR_ROLE_KEY, self::CONTRIBUTOR_ROLE_KEY),
                'edit_published_posts' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY, self::AUTHOR_ROLE_KEY),
                'delete_published_posts' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY, self::AUTHOR_ROLE_KEY),
                'edit_others_posts' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY),
                'delete_others_posts' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY),
                'read_private_posts' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY),
                'edit_private_posts' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY),
                'delete_private_posts' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY),
                'manage_categories' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY)
            ),
            'Media' => array(
                'upload_files' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY, self::AUTHOR_ROLE_KEY),
                'unfiltered_upload' => array(self::ADMINISTRATOR_ROLE_KEY)
            ),
            'Pages' => array(
                'publish_pages' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY),
                'edit_pages' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY),
                'delete_pages' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY),
                'edit_published_pages' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY),
                'delete_published_pages' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY),
                'edit_others_pages' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY),
                'delete_others_pages' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY),
                'read_private_pages' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY),
                'edit_private_pages' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY),
                'delete_private_pages' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY)
            ),
            'Comments' => array(
                'edit_comment' => array(),
                'moderate_comments' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY)
            ),
            'Themes' => array(
                'switch_themes' => array(self::ADMINISTRATOR_ROLE_KEY),
                'edit_theme_options' => array(self::ADMINISTRATOR_ROLE_KEY),
                'edit_themes' => array(self::ADMINISTRATOR_ROLE_KEY),
                'delete_themes' => array(self::ADMINISTRATOR_ROLE_KEY),
                'install_themes' => array(self::ADMINISTRATOR_ROLE_KEY),
                'update_themes' => array(self::ADMINISTRATOR_ROLE_KEY)
            ),
            'Plugins' => array(
                'activate_plugins' => array(self::ADMINISTRATOR_ROLE_KEY),
                'edit_plugins' => array(self::ADMINISTRATOR_ROLE_KEY),
                'install_plugins' => array(self::ADMINISTRATOR_ROLE_KEY),
                'update_plugins' => array(self::ADMINISTRATOR_ROLE_KEY),
                'delete_plugins' => array(self::ADMINISTRATOR_ROLE_KEY)
            ),
            'Users' => array(
                'list_users' => array(self::ADMINISTRATOR_ROLE_KEY),
                'create_users' => array(self::ADMINISTRATOR_ROLE_KEY),
                'edit_users' => array(self::ADMINISTRATOR_ROLE_KEY),
                'delete_users' => array(self::ADMINISTRATOR_ROLE_KEY),
                'promote_users' => array(self::ADMINISTRATOR_ROLE_KEY),
                'add_users' => array(self::ADMINISTRATOR_ROLE_KEY),
                'remove_users' => array(self::ADMINISTRATOR_ROLE_KEY)
            ),
            'Tools' => array(
                'import' => array(self::ADMINISTRATOR_ROLE_KEY),
                'export' => array(self::ADMINISTRATOR_ROLE_KEY)
            ),
            'Admin' => array(
                'manage_options' => array(self::ADMINISTRATOR_ROLE_KEY),
                'update_core' => array(self::ADMINISTRATOR_ROLE_KEY),
                'unfiltered_html' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY)
            ),
            'Links' => array(
                'manage_links' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY)
            )
        );
        public static $DEPRECATED_CAPABILITIES = array(
            'Deprecated' => array(
                'edit_files' => array(self::ADMINISTRATOR_ROLE_KEY),
                'level_0' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY, self::AUTHOR_ROLE_KEY, self::CONTRIBUTOR_ROLE_KEY, self::SUBSCRIBER_ROLE_KEY),
                'level_1' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY, self::AUTHOR_ROLE_KEY, self::CONTRIBUTOR_ROLE_KEY),
                'level_2' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY, self::AUTHOR_ROLE_KEY),
                'level_3' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY),
                'level_4' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY),
                'level_5' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY),
                'level_6' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY),
                'level_7' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY),
                'level_8' => array(self::ADMINISTRATOR_ROLE_KEY),
                'level_9' => array(self::ADMINISTRATOR_ROLE_KEY),
                'level_10' => array(self::ADMINISTRATOR_ROLE_KEY)
            )
        );
        public static $OTHER_CAPABILITIES = array(
            'Other Capabilities' => array(
            )
        );
        public static $CUSTOM_POST_TYPES_DEFAULTED = array();
        private static $CAPABILITIES = NULL;
        //Variables
        protected $admin_menu = array();
        protected $remove_admin_menu = array();
        protected $options;
        protected $objList;
        protected $objAddEdit;
        protected $objRestore;
        protected $objAddRemoveCap;
        protected $objAssignUsers;
        protected $objGoPro;
        protected $objNavMenu = NULL;
        protected $objLoginRedirect = NULL;
        protected $objAttachmentPermissions = NULL;
        protected $objUserPermissions = NULL;
        protected $objWidgetPermissions = NULL;

        function __construct() {
            parent::__construct(__FILE__, self::PLUGIN_SLUG);

            //$this->add_menu($this->__('WPFront User Role Editor'), $this->__('User Role Editor'));

            $this->options = new WPFront_User_Role_Editor_Options($this);
            $this->objGoPro = new WPFront_User_Role_Editor_Go_Pro($this);

            if ($this->objGoPro->has_license()) {
                $this->objList = new WPFront_User_Role_Editor_List($this);
                $this->objAddEdit = new WPFront_User_Role_Editor_Add_Edit($this);
                $this->objRestore = new WPFront_User_Role_Editor_Restore($this);
                $this->objAddRemoveCap = new WPFront_User_Role_Editor_Add_Remove_Capability($this);
                $this->objAssignUsers = new WPFront_User_Role_Editor_Assign_Roles($this);
                if ($this->objNavMenu === NULL)
                    $this->objNavMenu = new WPFront_User_Role_Editor_Nav_Menu($this);
                if ($this->objLoginRedirect === NULL)
                    $this->objLoginRedirect = new WPFront_User_Role_Editor_Login_Redirect($this);
                if ($this->objAttachmentPermissions === NULL)
                    $this->objAttachmentPermissions = new WPFront_User_Role_Editor_Attachment_Permissions($this);
                if ($this->objUserPermissions === NULL)
                    $this->objUserPermissions = new WPFront_User_Role_Editor_User_Permissions($this);
                if ($this->objWidgetPermissions === NULL)
                    $this->objWidgetPermissions = new WPFront_User_Role_Editor_Widget_Permissions($this);
            }
        }

        public function plugins_loaded() {
            //gravity forms
            if (!function_exists('members_get_capabilities')) {

                function members_get_capabilities() {
                    
                }

            }
        }

        public function admin_init() {
            register_setting(self::OPTIONS_GROUP_NAME, self::OPTION_NAME);

            $this->rename_role_capabilities();
        }

        protected function add_submenu_page($position, $title, $name, $capability, $slug, $func, $scripts = NULL, $styles = NULL, $controller = NULL, $admin_bar_parent = NULL, $admin_bar_title = NULL) {
            if ($scripts === NULL)
                $scripts = 'enqueue_role_scripts';
            if ($styles === NULL)
                $styles = 'enqueue_role_styles';

            $this->admin_menu[$position] = array($title, $name, $capability, $slug, $func, $scripts, $styles, $controller, $admin_bar_parent, $admin_bar_title);
        }

        protected function remove_submenu_page($position) {
            $this->remove_admin_menu[$position] = TRUE;
        }

        public function get_submenu_page_item($position) {
            return $this->admin_menu[$position];
        }

        protected function add_pro_page() {
            if (isset($this->admin_menu[1000]))
                return;

            if (current_user_can('manage_options'))
                $this->add_submenu_page(1000, $this->__('Go Pro'), '<span class="wpfront-go-pro">' . $this->__('Go Pro') . '</span>', 'manage_options', WPFront_User_Role_Editor_Go_Pro::MENU_SLUG, array($this->objGoPro, 'go_pro'));
        }

        public function admin_menu() {
            //parent::admin_menu();

            $this->add_pro_page();

            if ($this->objGoPro->has_license())
                $menu_slug = WPFront_User_Role_Editor_List::MENU_SLUG;
            else
                $menu_slug = WPFront_User_Role_Editor_Go_Pro::MENU_SLUG;

            if ($this->objGoPro->has_license()) {
                $this->add_submenu_page(10, $this->__('Roles'), $this->__('All Roles'), $this->get_capability_string('list'), WPFront_User_Role_Editor_List::MENU_SLUG, array($this->objList, 'list_roles'), NULL, NULL, $this->objList);
                $this->add_submenu_page(20, $this->__('Add New Role'), $this->__('Add New'), $this->get_capability_string('create'), WPFront_User_Role_Editor_Add_Edit::MENU_SLUG, array($this->objAddEdit, 'add_edit_role'), NULL, NULL, $this->objAddEdit, 'new-content', $this->__('Role'));
                $this->add_submenu_page(30, $this->__('Restore Role'), $this->__('Restore'), $this->get_capability_string('edit'), WPFront_User_Role_Editor_Restore::MENU_SLUG, array($this->objRestore, 'restore_role'), NULL, NULL, $this->objRestore);
                $this->add_submenu_page(35, $this->__('Add/Remove Capability'), $this->__('Add/Remove Cap'), $this->get_capability_string('edit'), WPFront_User_Role_Editor_Add_Remove_Capability::MENU_SLUG, array($this->objAddRemoveCap, 'add_remove_capability'), NULL, NULL, $this->objAddRemoveCap);
                $this->add_submenu_page(37, $this->__('Login Redirect'), $this->__('Login Redirect'), 'edit_login_redirects', WPFront_User_Role_Editor_Login_Redirect::MENU_SLUG, array($this->objLoginRedirect, 'login_redirect'), NULL, NULL, $this->objLoginRedirect);
                $this->add_submenu_page(100, $this->__('Settings'), $this->__('Settings'), 'manage_options', WPFront_User_Role_Editor_Options::MENU_SLUG, array($this->options, 'settings'), NULL, NULL, $this->options);
            }

            ksort($this->admin_menu);

            if (!empty($this->admin_menu)) {
                $menu_capability = reset($this->admin_menu);
                $menu_capability = $menu_capability[2];
                add_menu_page($this->__('Roles'), $this->__('Roles'), $menu_capability, $menu_slug, null, $this->pluginURL() . 'images/roles_menu.png', '69.999999');
            }

            foreach ($this->admin_menu as $key => $value) {
                if (!empty($this->remove_admin_menu[$key])) {
                    continue;
                }

                $page_hook_suffix = add_submenu_page($menu_slug, $value[0], $value[1], $value[2], $value[3], $value[4]);
                add_action('admin_print_scripts-' . $page_hook_suffix, array($this, $value[5]));
                add_action('admin_print_styles-' . $page_hook_suffix, array($this, $value[6]));
                if ($value[7] !== NULL)
                    $value[7]->set_page_hook($page_hook_suffix);
            }

            if ($this->objGoPro->has_license()) {
                $page_hook_suffix = add_users_page($this->__('Assign Roles | Migrate Users'), $this->__('Assign / Migrate'), 'promote_users', WPFront_User_Role_Editor_Assign_Roles::MENU_SLUG, array($this->objAssignUsers, 'assign_roles'));
                $this->objAssignUsers->set_page_hook($page_hook_suffix);
                add_action('admin_print_scripts-' . $page_hook_suffix, array($this, 'enqueue_role_scripts'));
                add_action('admin_print_styles-' . $page_hook_suffix, array($this, 'enqueue_role_styles'));
            }
        }

        public function admin_bar() {
            global $wp_admin_bar;

            foreach ($this->admin_menu as $key => $value) {
                if (!empty($value[8])) {
                    if (current_user_can($value[2])) {
                        $wp_admin_bar->add_menu(array(
                            'parent' => $value[8],
                            'id' => $value[3],
                            'title' => $value[9],
                            'href' => admin_url('admin.php?page=' . $value[3]),
                                )
                        );
                    }
                }
            }
        }

        //add scripts
        public function enqueue_role_scripts() {
//            $jsRoot = $this->pluginURLRoot . 'js/';

            wp_enqueue_script('jquery');
            wp_enqueue_script('postbox');
            wp_enqueue_script('jquery-ui-draggable');
        }

        //add styles
        public function enqueue_role_styles() {
            wp_enqueue_style('font-awesome-410', '//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css', array(), '4.1.0');
            $styleRoot = $this->pluginURLRoot . 'css/';
            wp_enqueue_style('wpfront-user-role-editor-styles', $styleRoot . 'style.css', array(), self::VERSION);
        }

        //options page scripts
        public function enqueue_options_scripts() {
            $this->enqueue_role_scripts();
        }

        //options page styles
        public function enqueue_options_styles() {
            $this->enqueue_role_styles();

            $styleRoot = $this->pluginURLRoot . 'css/';
            wp_enqueue_style('wpfront-user-role-editor-options', $styleRoot . 'options.css', array(), self::VERSION);
        }
        
        public function admin_enqueue_styles() {
            $styleRoot = $this->pluginURLRoot . 'css/';
            wp_enqueue_style('wpfront-user-role-editor-admin-css', $styleRoot . 'admin-style.css', array(), self::VERSION);
        }

        public function get_capability_string($capability) {
            if ($this->enable_role_capabilities())
                return $capability . '_roles';

            return $capability . '_users';
        }

        public function permission_denied() {
            wp_die($this->__('You do not have sufficient permissions to access this page.'));
        }

        public function current_user_can($capability) {
            switch ($capability) {
                case 'list_roles':
                    return current_user_can($this->get_capability_string('list'));
                case 'edit_roles':
                    return current_user_can($this->get_capability_string('edit'));
                case 'delete_roles':
                    return current_user_can($this->get_capability_string('delete'));
                case 'create_roles':
                    return current_user_can($this->get_capability_string('create'));
                default :
                    return current_user_can($capability);
            }
        }

        public function create_nonce($id = '') {
            if (empty($_SERVER['REQUEST_URI'])) {
                $this->permission_denied();
                exit;
                return;
            }
            $referer = $_SERVER['REQUEST_URI'];
            echo '<input type = "hidden" name = "_wpnonce' . $id . '" value = "' . wp_create_nonce($referer . $id) . '" />';
            echo '<input type = "hidden" name = "_wp_http_referer' . $id . '" value = "' . esc_html($referer) . '" />';
        }

        public function verify_nonce($id = '') {
            if (strtolower($_SERVER['REQUEST_METHOD']) === 'post') {
                $flag = TRUE;
                if (empty($_POST['_wpnonce' . $id])) {
                    $flag = FALSE;
                } else if (empty($_POST['_wp_http_referer' . $id])) {
                    $flag = FALSE;
                } else if (!wp_verify_nonce($_POST['_wpnonce' . $id], $_POST['_wp_http_referer' . $id] . $id)) {
                    $flag = FALSE;
                }

                if (!$flag) {
                    $this->permission_denied();
                    exit;
                }
            }
        }

        public function footer() {
            return;
            ?>
            <div class="footer">
                <a target="_blank" href="http://wpfront.com/contact/"><?php echo $this->__('Feedback'); ?></a> 
                |
                <a target="_blank" href="http://wpfront.com/donate/"><?php echo $this->__('Buy me a Beer'); ?></a> 
            </div>
            <?php
        }

        public function options_page_header($title, $optionsGroupName = self::OPTIONS_GROUP_NAME) {
            parent::options_page_header($title, $optionsGroupName);
        }

        public function options_page_footer($settingsLink, $FAQLink, $extraLinks = NULL) {
            parent::options_page_footer($settingsLink, $FAQLink, $extraLinks);
        }

        public function get_capabilities($exclude_custom_post_types = FALSE) {
            if (self::$CAPABILITIES != NULL)
                return self::$CAPABILITIES;

            self::$CAPABILITIES = array();

            foreach (self::$STANDARD_CAPABILITIES as $key => $value) {
                self::$CAPABILITIES[$key] = array();
                foreach ($value as $cap => $roles) {
                    self::$CAPABILITIES[$key][] = $cap;
                }
            }

            foreach (self::$DEPRECATED_CAPABILITIES as $key => $value) {
                self::$CAPABILITIES[$key] = array();
                foreach ($value as $cap => $roles) {
                    self::$CAPABILITIES[$key][] = $cap;
                }
            }

            reset(self::$OTHER_CAPABILITIES);
            $other_key = key(self::$OTHER_CAPABILITIES);

            if ($exclude_custom_post_types) {
                foreach (self::$DYNAMIC_CAPS as $cap) {
                    self::$ROLE_CAPS = array_diff(self::$ROLE_CAPS, array($cap));
                    self::$OTHER_CAPABILITIES[$other_key][] = $cap;
                }
            }

            if ($this->enable_role_capabilities())
                self::$CAPABILITIES['Roles (WPFront)'] = self::$ROLE_CAPS;

            //gravity forms
            self::$OTHER_CAPABILITIES[$other_key] = apply_filters('members_get_capabilities', self::$OTHER_CAPABILITIES[$other_key]);

            global $wp_roles;
            if (isset($wp_roles->roles) && is_array($wp_roles->roles)) {
                foreach ($wp_roles->roles as $key => $role) {
                    foreach ($role['capabilities'] as $cap => $value) {
                        $found = FALSE;
                        foreach (self::$CAPABILITIES as $g => $wcaps) {
                            if (in_array($cap, $wcaps)) {
                                $found = TRUE;
                                break;
                            }
                        }
                        if (!$found && !in_array($cap, self::$OTHER_CAPABILITIES[$other_key])) {
                            self::$OTHER_CAPABILITIES[$other_key][] = $cap;
                        }
                    }
                }
            }

            if (!$exclude_custom_post_types) {
                $post_types = get_post_types(array(
                    '_builtin' => FALSE
                ));

                $other_caps = self::$OTHER_CAPABILITIES[$other_key];
                unset(self::$OTHER_CAPABILITIES[$other_key]);

                foreach ($post_types as $key => $value) {
                    $post_type_object = get_post_type_object($key);
                    $caps = $post_type_object->cap;

                    if ($post_type_object->capability_type === 'post') {
                        if ($post_type_object->show_ui)
                            self::$CUSTOM_POST_TYPES_DEFAULTED[$this->get_custom_post_type_label($post_type_object)] = array();
                    } else {
                        $caps = (OBJECT) $this->remove_meta_capabilities((ARRAY) $caps, $other_caps);
                        $custom_caps = array();
                        foreach (self::$STANDARD_CAPABILITIES['Posts'] as $key => $value) {
                            if (isset($caps->$key)) {
                                $custom_caps[] = $caps->$key;
                                unset($caps->$key);
                            } elseif (isset($post_type_object->cap->$key)) {
                                $custom_caps[] = $post_type_object->cap->$key;
                            }
                        }
                        foreach ($caps as $key => $value) {
                            if (isset($caps->$key)) {
                                $custom_caps[] = $caps->$key;
                                unset($caps->$key);
                            }
                        }
                        self::$OTHER_CAPABILITIES[$this->get_custom_post_type_label($post_type_object)] = $custom_caps;
                    }
                }

                self::$OTHER_CAPABILITIES[$other_key] = $other_caps;
            }

            self::$OTHER_CAPABILITIES[$other_key] = array_unique(self::$OTHER_CAPABILITIES[$other_key]);

            foreach (self::$OTHER_CAPABILITIES as $key => $value) {
                if (count($value) === 0)
                    continue;

                //self::$CAPABILITIES[$key] = $value;
                self::$CAPABILITIES[$key] = self::$OTHER_CAPABILITIES[$key];

                if ($key != $other_key) {
                    foreach ($value as $cap) {
                        self::$OTHER_CAPABILITIES[$other_key] = array_values(array_diff(self::$OTHER_CAPABILITIES[$other_key], array($cap)));
                    }
                }
            }

            return self::$CAPABILITIES;
        }

        public function add_role_capability($cap) {
            self::$ROLE_CAPS[] = $cap;
            $this->add_dynamic_capability($cap);
        }

        public function add_dynamic_capability($cap) {
            self::$DYNAMIC_CAPS[$cap] = $cap;
        }

        private function get_custom_post_type_label($post_type_object) {
            return $post_type_object->labels->name . ' (' . $post_type_object->name . ')';
        }

        private function remove_meta_capabilities($caps, $other_caps) {
            foreach ($caps as $key => $value) {
                if ($key === 'read') {
                    unset($caps[$key]);
                    continue;
                }

                if (!in_array($value, $other_caps))
                    unset($caps[$key]);
            }

            if (array_key_exists('create_posts', $caps) && array_key_exists('edit_posts', $caps)) {
                if ($caps['create_posts'] === $caps['edit_posts'])
                    unset($caps['create_posts']);
            }

            return $caps;
        }

        public function reset_capabilities() {
            self::$CAPABILITIES = NULL;

            foreach (self::$OTHER_CAPABILITIES as $key => $value) {
                self::$OTHER_CAPABILITIES[$key] = array();
            }
        }

        public function display_deprecated() {
            return $this->options->display_deprecated();
        }

        public function enable_role_capabilities() {
            return TRUE;
        }

        public function remove_nonstandard_capabilities_restore() {
            return $this->options->remove_nonstandard_capabilities_restore();
        }

        public function override_edit_permissions() {
            return $this->options->override_edit_permissions();
        }

        public function disable_navigation_menu_permissions() {
            return $this->options->disable_navigation_menu_permissions();
        }
        
        public function override_navigation_menu_permissions() {
            return $this->options->override_navigation_menu_permissions();
        }

        public function customize_permission_custom_post_types() {
            return $this->options->customize_permission_custom_post_types();
        }
        
        public function disable_extended_permission_post_types() {
            return $this->options->disable_extended_permission_post_types();
        }

        public function enable_multisite_only_options($multisite) {
            return TRUE;
        }

        public function enable_pro_only_options() {
            return FALSE;
        }

        public function get_assignable_roles($roles = NULL) {
            if ($roles === NULL) {
                global $wp_roles;
                $roles = $wp_roles->roles;
            }

            $roles = $this->objUserPermissions->assignable_roles($roles);
            $all_roles = array();

            foreach ($roles as $key => $value) {
                $all_roles[$key] = get_role($key);
            }

            return $all_roles;
        }
        
        public function menu_walker_override_warning() {
            $this->objNavMenu->menu_walker_override_warning();
        }
        
        public function get_extendable_post_types() {
            return array();
        }

        private function rename_role_capabilities() {
            global $wp_roles;

            //removed in v2.5 but in wrong place.
//            foreach ($wp_roles->role_objects as $key => $role) {
//                foreach (self::$ROLE_CAPS as $value) {
//                    if ($role->has_cap('wpfront_' . $value)) {
//                        $role->add_cap($value);
//                        $role->remove_cap('wpfront_' . $value);
//                    }
//                }
//            }

            $role_admin = $wp_roles->role_objects[self::ADMINISTRATOR_ROLE_KEY];
            foreach (self::$ROLE_CAPS as $value) {
                $role_admin->add_cap($value);
            }
        }

        public static function Instanciate($file) {
            if (defined('WPFRONT_USER_ROLE_EDITOR_PLUGIN_FILE'))
                return;

            $f = 'wpfront-user-role-editor.php';
            $current_folder = strtolower(basename(dirname($file)));

            $folder = 'wpfront-user-role-editor-business-pro';
            if ($current_folder === $folder) {
                define('WPFRONT_USER_ROLE_EDITOR_PLUGIN_FILE', $file);
                new WPFront_User_Role_Editor_Business_Pro_Base();
                return;
            }

            if (!function_exists('is_plugin_active'))
                include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

            if (is_plugin_active($folder . '/' . $f))
                return;

            $folder = 'wpfront-user-role-editor-personal-pro';
            if ($current_folder === $folder) {
                define('WPFRONT_USER_ROLE_EDITOR_PLUGIN_FILE', $file);
                new WPFront_User_Role_Editor_Personal_Pro_Base();
                return;
            }

            if (is_plugin_active($folder . '/' . $f))
                return;

            define('WPFRONT_USER_ROLE_EDITOR_PLUGIN_FILE', $file);
            new WPFront_User_Role_Editor();
        }

        public static function wp_remote_get($url, $params = NULL) {
            if ($params === NULL) {
                $params = array('timeout' => 10);
            } else {
                $params = array_merge(array('timeout' => 10), $params);
            }

            $params['sslverify'] = FALSE;

            $result = wp_remote_post($url, $params);

            if (is_wp_error($result) || wp_remote_retrieve_response_code($result) !== 200) {
                $params['sslverify'] = TRUE;
                $result = wp_remote_post($url, $params);
            }

            return $result;
        }

    }

}

require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-controller-base.php");
require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-options.php");
require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-list.php");
require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-add-edit.php");
require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-delete.php");
require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-restore.php");
require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-add-remove-capability.php");
require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-assign-roles.php");
require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-go-pro.php");
require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-nav-menu.php");
require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-login-redirect.php");
require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-attachment-permissions.php");
require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-user-permissions.php");
require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-widget-permissions.php");
require_once(plugin_dir_path(__FILE__) . "integration/plugins/class-wpfront-user-role-editor-plugin-integration.php");



if (file_exists(plugin_dir_path(__FILE__) . "personal-pro/class-wpfront-user-role-editor-personal-pro.php"))
    require_once(plugin_dir_path(__FILE__) . "personal-pro/class-wpfront-user-role-editor-personal-pro.php");

if (file_exists(plugin_dir_path(__FILE__) . "business-pro/class-wpfront-user-role-editor-business-pro.php"))
    require_once(plugin_dir_path(__FILE__) . "business-pro/class-wpfront-user-role-editor-business-pro.php");
