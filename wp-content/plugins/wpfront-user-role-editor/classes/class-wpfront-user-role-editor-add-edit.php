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

if (!class_exists('WPFront_User_Role_Editor_Add_Edit')) {

    /**
     * Add or Edit Role
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Add_Edit extends WPFront_User_Role_Editor_Controller_Base {

        const MENU_SLUG = 'wpfront-user-role-editor-add-new';

        protected static $copy_capabilities_action = NULL;
        protected $role = null;
        protected $is_editable = FALSE;
        protected $role_exists = FALSE;
        protected $error = FALSE;

        function __construct($main) {
            parent::__construct($main);

            $this->ajax_register('wp_ajax_wpfront_user_role_editor_copy_capabilities', array($this, 'copy_capabilities_callback'));
        }

        public function add_edit_role($role_name) {
            global $wp_roles;
            $roles = $wp_roles->role_names;

            if (array_key_exists($role_name, $roles)) {
                $this->role = get_role($role_name);
            }

            if ($this->role === NULL) {
                if (!$this->can_create()) {
                    $this->main->permission_denied();
                    return;
                }
            } else {
                if (!$this->can_edit()) {
                    $this->main->permission_denied();
                    return;
                }
            }

            if ($this->role == NULL) {
                $this->is_editable = TRUE;
            } else if ($role_name != self::ADMINISTRATOR_ROLE_KEY) {
                $editable_roles = get_editable_roles();
                if ($this->main->override_edit_permissions())
                    $editable_roles = $wp_roles->get_names();
                $this->is_editable = array_key_exists($role_name, $editable_roles);
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
                        if (array_key_exists($role_name, $roles)) {
                            $this->role_exists = TRUE;
                            break;
                        }
                        $error = add_role($role_name, $this->get_display_name(), $capabilities);
                        if ($error == NULL) {
                            $this->error = TRUE;
                            break;
                        }
                    } else {
//                        global $wp_roles;
//                        $wp_roles->roles[$this->role->name] = array(
//                            'name' => $this->get_display_name(),
//                            'capabilities' => $capabilities
//                        );
//                        update_option($wp_roles->role_key, $wp_roles->roles);
//                        $wp_roles->role_objects[$this->role->name] = new WP_Role($this->role->name, $capabilities);
//                        $wp_roles->role_names[$this->role->name] = $this->get_display_name();

                        self::update_role($this->role->name, $this->get_display_name(), $capabilities);
                    }

                    $success = TRUE;
                    break;
                }
            }

            if ($success) {
                printf('<script type="text/javascript">window.location.replace("%s");</script>', $this->list_url());
            } else {
                $this->include_template();
            }
        }

        protected function include_template() {
            include($this->main->pluginDIR() . 'templates/add-edit-role.php');
        }

        protected function is_display_name_valid() {
            if (empty($_POST['createrole']))
                return TRUE;

            if ($this->get_display_name() == '')
                return FALSE;

            return TRUE;
        }

        protected function is_display_name_disabled() {
            return !$this->is_editable;
        }

        protected function get_display_name() {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (empty($_POST['display_name']))
                    return '';

                return esc_html(trim($_POST['display_name']));
            }

            if ($this->role == NULL)
                return '';
            global $wp_roles;
            return $wp_roles->role_names[$this->role->name];
        }

        protected function is_role_name_valid() {
            if (empty($_POST['createrole']))
                return TRUE;

            if ($this->get_role_name() == '')
                return FALSE;

            return TRUE;
        }

        protected function is_role_name_disabled() {
            if ($this->role != NULL)
                return TRUE;
            if (!$this->is_editable)
                return TRUE;
            return FALSE;
        }

        protected function get_role_name() {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (empty($_POST['role_name']))
                    return '';

                return preg_replace('/\W/', '', preg_replace('/ /', '_', trim($_POST['role_name'])));
            }

            if ($this->role == NULL)
                return '';
            return $this->role->name;
        }

        protected function is_submit_disabled() {
            if (!$this->is_editable)
                return TRUE;
            return FALSE;
        }

        protected function exclude_custom_post_types() {
            return FALSE;
        }

        protected function get_capability_groups() {
            $caps_group = array();

            foreach ($this->main->get_capabilities($this->exclude_custom_post_types()) as $key => $value) {
                $deprecated = array_key_exists($key, WPFront_User_Role_Editor::$DEPRECATED_CAPABILITIES);
                $other = array_key_exists($key, WPFront_User_Role_Editor::$OTHER_CAPABILITIES);

                //network caps check
                $caps = array();
                foreach ($value as $cap) {
                    if(strpos($cap, 'manage_network_') === 0) {
                        continue;
                    }
                    
                    $caps[] = $cap;
                }
                
                $caps_group[$key] = (OBJECT) array(
                            'caps' => $caps,
                            'display_name' => $this->__($key),
                            'deprecated' => $deprecated,
                            'disabled' => !$this->is_editable, //!$this->is_editable || $deprecated, - to enable levels; for author drop down
                            'hidden' => $deprecated && !$this->main->display_deprecated(),
                            'key' => str_replace(' ', '-', $key),
                            'has_help' => !$other
                );
            }

            foreach (WPFront_User_Role_Editor::$CUSTOM_POST_TYPES_DEFAULTED as $key => $value) {
                $caps_group[$key] = (OBJECT) array(
                            'caps' => 'defaulted',
                            'display_name' => $this->__($key),
                            'deprecated' => FALSE,
                            'disabled' => TRUE,
                            'hidden' => FALSE,
                            'key' => str_replace(' ', '-', $key),
                            'has_help' => FALSE
                );
            }

            return $caps_group;
        }

        protected function get_copy_from() {
            if (!$this->is_editable)
                return array();

            global $wp_roles;
            $roles = $wp_roles->role_names;
            asort($roles);
            return $roles;
        }

        protected function is_role_exists() {
            return $this->role_exists;
        }

        protected function is_error() {
            return $this->error;
        }

        public function copy_capabilities_callback() {
            if (self::$copy_capabilities_action !== NULL) {
                call_user_func(self::$copy_capabilities_action);
                die();
            }

            check_ajax_referer($_POST['referer'], 'nonce');

            if (empty($_POST['role'])) {
                echo '{}';
                die();
                return;
            }

            $role = get_role($_POST['role']);
            if ($role == NULL) {
                echo '{}';
                die();
                return;
            }

            echo json_encode($role->capabilities);
            die();
        }

        protected function capability_checked($cap) {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (empty($_POST['capabilities']))
                    return FALSE;

                return array_key_exists($cap, $_POST['capabilities']);
            }

            if ($this->role != NULL) {
                if (array_key_exists($cap, $this->role->capabilities))
                    return $this->role->capabilities[$cap];
            }

            return FALSE;
        }

        protected function get_help_url($cap) {
            if (isset(WPFront_User_Role_Editor::$DYNAMIC_CAPS[$cap]))
                return NULL;

            return 'http://wpfront.com/wordpress-capabilities/#' . $cap;
        }

        public static function update_role($name, $display_name, $capabilities) {
            global $wp_roles;
            $wp_roles->roles[$name] = array(
                'name' => $display_name,
                'capabilities' => $capabilities
            );
            update_option($wp_roles->role_key, $wp_roles->roles);
            $wp_roles->role_objects[$name] = new WP_Role($name, $capabilities);
            $wp_roles->role_names[$name] = $display_name;
        }

        protected function add_help_tab() {
            return array(
                array(
                    'id' => 'overview',
                    'title' => $this->__('Overview'),
                    'content' => '<p>'
                    . $this->__('This screen allows you to add a new role within your site.')
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
                    $this->__('Documentation on Add New Role'),
                    'add-role/'
                )
            );
        }

        protected function postbox_title($value) {
            return '<label class="' . ($value->deprecated ? 'deprecated' : 'active') . '"><input id="' . $value->key . '" type="checkbox" class="select-all" ' . ($value->disabled ? 'disabled' : '') . ' />' . $value->display_name . '</label>';
        }

        public function postbox_render($context, $args) {
            $value = $args['args'];
            ?>
            <div class="main <?php echo $value->deprecated ? 'deprecated' : 'active'; echo ' '; echo $value->hidden ? 'hidden' : 'visible'; ?>">
                <?php
                if ($value->caps === 'defaulted') {
                    ?>
                    <div class="no-capability">
                        <?php
                        echo $this->__("Uses 'Posts' capabilities.");
                        $upgrade_message = sprintf($this->__("%s to customize capabilities."), '<a href="https://wpfront.com/ureaddedit" target="_blank">' . $this->__('Upgrade to Pro') . '</a>');
                        $upgrade_message = apply_filters('wpfront_ure_custom_post_type_upgrade_message', $upgrade_message);
                        echo ' ' . $upgrade_message;
                        ?>
                    </div>
                    <?php
                } else {
                    foreach ($value->caps as $cap) {
                        $cap = esc_html($cap);
                        ?>
                        <div>
                            <input type="checkbox" id="<?php echo $cap; ?>" name="capabilities[<?php echo $cap; ?>]" <?php echo $value->disabled ? 'disabled' : '' ?> <?php echo $this->capability_checked($cap) ? 'checked' : '' ?> />
                            <label for="<?php echo $cap; ?>" title="<?php echo $cap; ?>"><?php echo $cap; ?></label>
                            <?php
                            if ($value->has_help) {
                                $help_url = $this->get_help_url($cap);
                                if ($help_url !== NULL) {
                                    ?>
                                    <a target="_blank" href="<?php echo $help_url; ?>">
                                        <img class="help" src="<?php echo $this->image_url() . 'help.png'; ?>" />
                                    </a>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
            <?php
        }

    }

}
