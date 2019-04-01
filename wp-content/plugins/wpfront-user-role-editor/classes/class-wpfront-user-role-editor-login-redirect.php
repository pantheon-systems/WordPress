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

require_once(plugin_dir_path(__FILE__) . "entities/class-wpfront-user-role-editor-entity-login-redirect.php");

if (!class_exists('WPFront_User_Role_Editor_Login_Redirect')) {

    /**
     * Login Redirect Controller
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2015 WPFront.com
     */
    class WPFront_User_Role_Editor_Login_Redirect extends WPFront_User_Role_Editor_Controller_Base {

        const MENU_SLUG = 'wpfront-user-role-editor-login-redirect';

        private $MODE = NULL;
        private $role = '';
        private $priority = '';
        private $url = '';
        private $logout_url = '';
        private $deny_wpadmin = FALSE;
        private $disable_toolbar = FALSE;
        private $valid_role = TRUE;
        private $valid_priority = TRUE;
        private $valid_url = TRUE;
        private $success_message = NULL;
        private $error_message = NULL;

        public function __construct($main) {
            parent::__construct($main);

            add_filter('login_redirect', array($this, 'login_redirect_callback'), 99999, 3);
            add_filter('logout_redirect', array($this, 'logout_redirect_callback'), 99999, 3);
            add_action('admin_init', array($this, 'admin_init'));
            add_filter('show_admin_bar', array($this, 'show_toolbar'), 10, 1);
        }

        public function login_redirect_callback($redirect_to, $request_from = '', $obj_user = NULL) {
            if (empty($obj_user) || is_wp_error($obj_user)) {
                $obj_user = wp_get_current_user();
            }

            if (empty($obj_user) || is_wp_error($obj_user)) {
                return $redirect_to;
            }

            if (empty($obj_user->roles) || !is_array($obj_user->roles))
                return $redirect_to;

            $roles = $obj_user->roles;
            if (in_array(self::ADMINISTRATOR_ROLE_KEY, $roles))
                return $redirect_to;

            $entity = new WPFront_User_Role_Editor_Entity_Login_Redirect();
            $data = $entity->get_all_login_redirect();

            foreach ($data as $value) {
                $role = $value->get_role();
                if (!$this->role_supported($role))
                    continue;

                if (in_array($role, $roles))
                    return $this->format_url($value->get_url());
            }

            return $redirect_to;
        }
        
        public function logout_redirect_callback($redirect_to, $request_from = '', $obj_user = NULL) {
            if (empty($obj_user) || is_wp_error($obj_user)) {
                $obj_user = wp_get_current_user();
            }

            if (empty($obj_user) || is_wp_error($obj_user)) {
                return $redirect_to;
            }

            if (empty($obj_user->roles) || !is_array($obj_user->roles))
                return $redirect_to;

            $roles = $obj_user->roles;
            if (in_array(self::ADMINISTRATOR_ROLE_KEY, $roles))
                return $redirect_to;

            $entity = new WPFront_User_Role_Editor_Entity_Login_Redirect();
            $data = $entity->get_all_login_redirect();

            foreach ($data as $value) {
                $role = $value->get_role();
                if (!$this->role_supported($role))
                    continue;

                if (in_array($role, $roles)) {
                    $url = $this->format_url($value->get_logout_url());
                    if(!empty($url))
                        return $url;
                }
            }

            return $redirect_to;
        }

        public function admin_init() {
            if (defined('DOING_AJAX') && DOING_AJAX) {
                return;
            }

            if (!empty($_SERVER['PHP_SELF']) && $_SERVER['PHP_SELF'] === '/wp-admin/admin-ajax.php') {
                return;
            }

            if (!empty($_SERVER['DOING_AJAX']) && $_SERVER['DOING_AJAX'] === '/wp-admin/admin-ajax.php') {
                return;
            }

            $obj_user = wp_get_current_user();
            if (empty($obj_user) || is_wp_error($obj_user)) {
                return;
            }

            if (empty($obj_user->roles) || !is_array($obj_user->roles))
                return;

            $roles = $obj_user->roles;
            if (in_array(self::ADMINISTRATOR_ROLE_KEY, $roles))
                return;

            $entity = new WPFront_User_Role_Editor_Entity_Login_Redirect();
            $data = $entity->get_all_login_redirect();

            foreach ($data as $value) {
                $role = $value->get_role();
                if (!$this->role_supported($role))
                    continue;

                if (in_array($role, $roles)) {
                    if ($value->get_deny_wpadmin()) {
                        wp_redirect($this->format_url($value->get_url()));
                    }

                    return;
                }
            }
        }

        public function show_toolbar($show) {
            $obj_user = wp_get_current_user();
            if (empty($obj_user) || is_wp_error($obj_user)) {
                return $show;
            }

            if (empty($obj_user->roles) || !is_array($obj_user->roles))
                return $show;

            $roles = $obj_user->roles;
            if (in_array(self::ADMINISTRATOR_ROLE_KEY, $roles))
                return $show;

            $entity = new WPFront_User_Role_Editor_Entity_Login_Redirect();
            $data = $entity->get_all_login_redirect();

            foreach ($data as $value) {
                $role = $value->get_role();
                if (!$this->role_supported($role))
                    continue;

                if (in_array($role, $roles)) {
                    if ($value->get_disable_toolbar()) {
                        return FALSE;
                    }

                    return $show;
                }
            }

            return $show;
        }

        public function login_redirect() {
            $this->main->verify_nonce('_login_redirect');

            if (!$this->can_edit_login_redirect()) {
                $this->main->permission_denied();
                return;
            }

            if ($this->get_mode() === 'DELETE' || $this->get_mode() === 'CONFIRM-DELETE') {
                if (!$this->can_delete_login_redirect()) {
                    $this->main->permission_denied();
                    return;
                }
            }

            if ($this->get_mode() === 'CONFIRM-DELETE') {
                $entity = new WPFront_User_Role_Editor_Entity_Login_Redirect();
                foreach ($this->role as $value) {
                    $entity = $entity->get_by_role($value);
                    $entity->delete();
                }

                $this->MODE = 'LIST';
            }

            if ($this->get_mode() === 'ADD') {
                $this->priority = $this->get_next_priority();
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->validate()) {
                switch ($this->get_mode()) {
                    case 'ADD':
                    case 'EDIT':
                        if ($this->role_supported($this->role)) {
                            $entity = new WPFront_User_Role_Editor_Entity_Login_Redirect();
                            $entity->set_role($this->role);
                            if ($this->get_mode() === 'EDIT') {
                                $entity = $entity->get_by_role($this->role);
                            }
                            $entity->set_priority($this->priority);
                            $entity->set_url($this->url);
                            $entity->set_logout_url($this->logout_url);
                            $entity->set_deny_wpadmin($this->deny_wpadmin);
                            $entity->set_disable_toolbar($this->disable_toolbar);
                            $entity->save();

                            if ($this->get_mode() === 'ADD') {
                                $this->success_message = $this->__('Login redirect added.');
                                $this->MODE = 'EDIT';
                            } else {
                                $this->success_message = $this->__('Login redirect updated.');
                            }
                        } else {
                            $this->error_message = $this->__('This role is not supported.');
                        }
                        break;

                    default:
                        break;
                }
            }

            if ($this->get_mode() === 'EDIT') {
                $entity = new WPFront_User_Role_Editor_Entity_Login_Redirect();
                $entity = $entity->get_by_role($this->role);

                $this->priority = $entity->get_priority();
                $this->url = $entity->get_url();
                $this->logout_url = $entity->get_logout_url();
                $this->deny_wpadmin = $entity->get_deny_wpadmin();
                $this->disable_toolbar = $entity->get_disable_toolbar();
            }

            include($this->main->pluginDIR() . 'templates/login-redirect.php');
        }

        private function validate() {
            $valid = TRUE;

            switch ($this->get_mode()) {
                case 'ADD':
                case 'EDIT':
                    if (empty($_POST['role'])) {
                        $this->valid_role = FALSE;
                        $valid = FALSE;
                    } else {
                        $this->role = $_POST['role'];
                    }

                    if (empty($_POST['priority'])) {
                        $this->valid_priority = FALSE;
                        $valid = FALSE;
                    } else {
                        $this->priority = intval($_POST['priority']);
                        if ($this->priority <= 0) {
                            $this->priority = '';
                            $this->valid_priority = FALSE;
                            $valid = FALSE;
                        }
                    }

                    if (empty($_POST['url'])) {
                        $this->valid_url = FALSE;
                        $valid = FALSE;
                    } else {
                        $this->url = $_POST['url'];
                    }
                    
                    if (!empty($_POST['logout_url'])) 
                        $this->logout_url = $_POST['logout_url'];

                    if (!empty($_POST['deny_wpadmin']))
                        $this->deny_wpadmin = TRUE;

                    if (!empty($_POST['disable_toolbar']))
                        $this->disable_toolbar = TRUE;
                    break;
            }

            return $valid;
        }

        private function get_mode() {
            if (empty($this->MODE)) {
                if (!empty($_POST['mode'])) {
                    $this->MODE = $_POST['mode'];
                    if ($this->MODE === 'BULK-DELETE') {
                        $this->MODE = NULL;
                        if ($_POST['action'] === 'delete' || $_POST['action2'] === 'delete') {
                            if (!empty($_POST['role'])) {
                                $this->MODE = 'DELETE';
                                $this->role = $_POST['role'];
                            }
                        }
                    } else {
                        $this->role = $_POST['role'];
                    }
                } elseif (!empty($_GET['mode'])) {
                    switch ($_GET['mode']) {
                        case 'add-new':
                            $this->MODE = 'ADD';
                            break;

                        case 'edit':
                            $this->MODE = 'EDIT';
                            $this->role = $_GET['role'];
                            break;

                        case 'delete':
                            $this->MODE = 'DELETE';
                            $this->role = array($_GET['role']);
                            break;
                    }
                }

                if (empty($this->MODE))
                    $this->MODE = 'LIST';
            }

            return $this->MODE;
        }

        private function get_roles() {
            global $wp_roles;
            $roles = $wp_roles->get_names();
            unset($roles[self::ADMINISTRATOR_ROLE_KEY]);

            $entity = new WPFront_User_Role_Editor_Entity_Login_Redirect();
            $data = $entity->get_all_login_redirect();
            foreach ($data as $value) {
                if (isset($roles[$value->get_role()])) {
                    unset($roles[$value->get_role()]);
                }
            }

            return $roles;
        }

        protected function role_supported($role, $extend = FALSE) {
            if ($role === self::ADMINISTRATOR_ROLE_KEY)
                return FALSE;

            global $wp_roles;
            $roles = $wp_roles->get_names();
            if (!isset($roles[$role]))
                return FALSE;

            if ($extend || in_array($role, WPFront_User_Role_Editor::$DEFAULT_ROLES))
                return TRUE;

            return FALSE;
        }

        public function login_redirect_add_new_url() {
            return parent::login_redirect_url() . '&mode=add-new';
        }

        public function login_redirect_edit_url($role) {
            return parent::login_redirect_url() . '&mode=edit&role=' . $role;
        }

        public function login_redirect_delete_url($role) {
            return parent::login_redirect_url() . '&mode=delete&role=' . $role;
        }

        public function can_edit_login_redirect() {
            return current_user_can('edit_login_redirects');
        }

        public function can_delete_login_redirect() {
            return current_user_can('delete_login_redirects');
        }

        public function get_role_display_name($role) {
            global $wp_roles;
            $roles = $wp_roles->get_names();

            if (isset($roles[$role]))
                return $roles[$role];

            return $role;
        }

        public function format_url($url) {
            if(empty($url))
                return '';
            
            $url = strtolower($url);

            if (strpos($url, '://') > -1)
                return $url;

            return home_url($url);
        }

        private function get_next_priority() {
            $entity = new WPFront_User_Role_Editor_Entity_Login_Redirect();
            return $entity->get_next_priority();
        }

        protected function add_help_tab() {
            if ($this->get_mode() === 'LIST' || $this->get_mode() === 'CONFIRM-DELETE') {
                return array(
                    array(
                        'id' => 'overview',
                        'title' => $this->__('Overview'),
                        'content' => '<p>'
                        . $this->__('Use this functionality to redirect a user to a specific page after they login based on their role.')
                        . '</p>'
                        . '<p>'
                        . $this->__('In addition, you can also deny the user access to WP-ADMIN and remove the toolbar (admin bar) from the front end.')
                        . '</p>'
                    ),
                    array(
                        'id' => 'columns',
                        'title' => $this->__('Columns'),
                        'content' => '<p>'
                        . $this->__('<b>Role</b>: The role of the user to qualify for this redirect.')
                        . '</p>'
                        . '<p>'
                        . $this->__('<b>Priority</b>: When a user has multiple roles, the role configuration with the highest priority will be selected.')
                        . '</p>'
                        . '<p>'
                        . $this->__('<b>Login Redirect URL</b>: The URL where the user will be redirected after login or on WP-ADMIN access if denied.')
                        . '</p>'
                        . '<p>'
                        . $this->__('<b>Logout Redirect URL</b>: The URL where the user will be redirected after logout.')
                        . '</p>'
                        . '<p>'
                        . $this->__('<b>WP-ADMIN</b>: Displays whether user has access to WP-ADMIN.')
                        . '</p>'
                        . '<p>'
                        . $this->__('<b>Toolbar</b>: Displays whether user will see toolbar on front end.')
                        . '</p>'
                    )
                );
            }
            
            if ($this->get_mode() === 'ADD' || $this->get_mode() === 'EDIT') {
                return array(
                    array(
                        'id' => 'overview',
                        'title' => $this->__('Overview'),
                        'content' => '<p>'
                        . $this->__('Add/Edit a new login redirect.')
                        . '</p>'
                    ),
                    array(
                        'id' => 'fields',
                        'title' => $this->__('Fields'),
                        'content' => '<p>'
                        . $this->__('<b>Role</b>: The role of the user to qualify for this redirect.')
                        . '</p>'
                        . '<p>'
                        . $this->__('<b>Priority</b>: When a user has multiple roles, the role configuration with the highest priority will be selected.')
                        . '</p>'
                        . '<p>'
                        . $this->__('<b>Login Redirect URL</b>: The URL where the user will be redirected after login or on WP-ADMIN access if denied.')
                        . '</p>'
                        . '<p>'
                        . $this->__('<b>Logout Redirect URL</b>: The URL where the user will be redirected after logout.')
                        . '</p>'
                        . '<p>'
                        . $this->__('<b>Deny WP-ADMIN</b>: If enabled user will be redirected to URL on WP-ADMIN access.')
                        . '</p>'
                        . '<p>'
                        . $this->__('<b>Disable Toolbar</b>: If enabled user will not see toolbar on front end.')
                        . '</p>'
                    )
                );
            }
            
            if ($this->get_mode() === 'DELETE') {
                return array(
                    array(
                        'id' => 'overview',
                        'title' => $this->__('Overview'),
                        'content' => '<p>'
                        . $this->__('Click "Confirm Delete" to delete displayed configurations.')
                        . '</p>'
                    )
                );
            }
        }

        protected function set_help_sidebar() {
            return array(
                array(
                    $this->__('Documentation on Login Redirect'),
                    'login-redirect/'
                )
            );
        }

    }

}