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

if (!class_exists('WPFront_User_Role_Editor_Restore')) {

    /**
     * Restore Role
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Restore extends WPFront_User_Role_Editor_Controller_Base {

        const MENU_SLUG = 'wpfront-user-role-editor-restore';

        static protected $restore_action = NULL;
        protected $roles;

        function __construct($main) {
            parent::__construct($main);

            $this->ajax_register('wp_ajax_wpfront_user_role_editor_restore_role', array($this, 'restore_role_callback'));
        }

        public function restore_role() {
            if (!$this->can_edit()) {
                $this->main->permission_denied();
                return;
            }

            $this->prepare_data(FALSE);
            $this->include_template();
        }

        protected function prepare_data($usename) {
            global $wp_roles;
            $site_roles = $wp_roles->role_names;

            foreach (WPFront_User_Role_Editor::$DEFAULT_ROLES as $value) {
                $text = $this->__(ucfirst($value));
                if (!$usename && array_key_exists($value, $site_roles))
                    $text = $site_roles[$value];

                $this->roles[$value] = $text;
            }
        }

        protected function include_template() {
            include($this->main->pluginDIR() . 'templates/restore-role.php');
        }

        public function restore_role_callback() {
            if (self::$restore_action !== NULL) {
                call_user_func(self::$restore_action);
                die();
            }

            check_ajax_referer($_POST['referer'], 'nonce');

            $result = $this->action_restore();

            echo sprintf('{ "result": %s, "message": "%s" }', $result[0] ? 'true' : 'false', $this->__('ERROR') . ': ' . $this->__($result[1]));
            die();
        }

        protected function action_restore() {
            $result = FALSE;
            $message = 'Unexpected error while restoring role.';

            if (empty($_POST['role'])) {
                $message = 'Role not found.';
            } elseif (!$this->can_edit()) {
                $message = 'Permission denied.';
            } else {
                $role_name = $_POST['role'];
                if (!in_array($role_name, WPFront_User_Role_Editor::$DEFAULT_ROLES)) {
                    $message = 'Role not found.';
                } else {
                    $role = get_role($role_name);
                    if ($role == NULL)
                        $role = add_role($role_name, $this->__(ucfirst($role_name)));
                    if ($role != NULL) {

                        foreach (WPFront_User_Role_Editor::$STANDARD_CAPABILITIES as $group => $caps) {
                            foreach ($caps as $cap => $roles) {
                                if (in_array($role->name, $roles))
                                    $role->add_cap($cap);
                                else
                                    $role->remove_cap($cap);
                            }
                        }

                        foreach (WPFront_User_Role_Editor::$DEPRECATED_CAPABILITIES as $group => $caps) {
                            foreach ($caps as $cap => $roles) {
                                if (in_array($role->name, $roles))
                                    $role->add_cap($cap);
                                else
                                    $role->remove_cap($cap);
                            }
                        }

                        if ($this->main->remove_nonstandard_capabilities_restore()) {
                            foreach (WPFront_User_Role_Editor::$ROLE_CAPS as $value) {
                                $role->remove_cap($value);
                            }

                            $this->main->get_capabilities();
                            foreach (WPFront_User_Role_Editor::$OTHER_CAPABILITIES as $group => $caps) {
                                foreach ($caps as $cap) {
                                    $role->remove_cap($cap);
                                }
                            }
                        } else {
                            if ($role->name == self::ADMINISTRATOR_ROLE_KEY) {
                                $this->main->get_capabilities();
                                foreach (WPFront_User_Role_Editor::$OTHER_CAPABILITIES as $group => $caps) {
                                    foreach ($caps as $cap) {
                                        $role->add_cap($cap);
                                    }
                                }
                            }
                        }

                        if ($role->name == self::ADMINISTRATOR_ROLE_KEY && $this->main->enable_role_capabilities()) {
                            foreach (WPFront_User_Role_Editor::$ROLE_CAPS as $value) {
                                $role->add_cap($value);
                            }
                        }

                        $result = TRUE;
                        $message = '';
                    }
                }
            }

            return array($result, $message);
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
                    $this->__('Documentation on Restore'),
                    'restore-role/'
                )
            );
        }

    }

}
