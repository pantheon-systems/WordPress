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

if (!class_exists('WPFront_User_Role_Editor_Add_Remove_Capability')) {

    /**
     * Add or Remove Capability
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Add_Remove_Capability extends WPFront_User_Role_Editor_Controller_Base {

        const MENU_SLUG = 'wpfront-user-role-editor-add-remove-capability';

        private $action;
        private $capability;
        private $roles_type;
        private $roles;
        private $message;

        public function add_remove_capability() {

            if (!$this->can_edit()) {
                $this->main->permission_denied();
                return;
            }

            $this->action = 'add';
            $this->capability = '';
            $this->roles_type = 'all';
            $this->roles = array();
            $this->message = NULL;

            if (!empty($_POST['add-remove-capability'])) {
                $this->main->verify_nonce();

                $this->action = $_POST['action_type'];
                $this->capability = trim($_POST['capability']);
                if (empty($this->capability))
                    $this->capability = NULL;
                $this->roles_type = $_POST['roles_type'];
                if ($this->roles_type === 'selected' && !empty($_POST['selected-roles']))
                    $this->roles = $_POST['selected-roles'];

                if (!empty($this->capability)) {
                    $roles = array();
                    switch ($this->roles_type) {
                        case 'all':
                            global $wp_roles;
                            $roles = $wp_roles->role_names;
                            break;
                        case 'selected':
                            $roles = $this->roles;
                            break;
                    }

                    $func = NULL;
                    switch ($this->action) {
                        case 'add':
                            $func = 'add_cap';
                            if (!isset($roles[self::ADMINISTRATOR_ROLE_KEY])) {
                                $roles[self::ADMINISTRATOR_ROLE_KEY] = TRUE;
                            }
                            break;
                        case 'remove':
                            $func = 'remove_cap';
                            if ($this->roles_type === 'all') {
                                $roles[self::ADMINISTRATOR_ROLE_KEY] = TRUE;
                            } else {
                                unset($roles[self::ADMINISTRATOR_ROLE_KEY]);
                            }
                            break;
                    }

                    foreach ($roles as $key => $value) {
                        $role = get_role($key);
                        if (!empty($role)) {
                            $role->$func($this->capability);
                        }
                    }

                    $this->message = $this->__('Roles updated.');
                }
            }

            include($this->main->pluginDIR() . 'templates/add-remove-capability.php');
        }

        private function get_roles() {
            global $wp_roles;
            $roles = $wp_roles->role_names;

            unset($roles[self::ADMINISTRATOR_ROLE_KEY]);

            return $roles;
        }

        protected function add_help_tab() {
            return array(
                array(
                    'id' => 'overview',
                    'title' => $this->__('Overview'),
                    'content' => '<p>'
                    . $this->__('This screen allows you to add a capability to roles or remove a capability from roles within your site.')
                    . '</p>'
                ),
                array(
                    'id' => 'action',
                    'title' => $this->__('Action'),
                    'content' => '<p>'
                    . $this->__('Select "Add Capability" to add a capability to roles.')
                    . '</p>'
                    . '<p>'
                    . $this->__('Select "Remove Capability" to remove a capability from roles.')
                    . '</p>'
                ),
                array(
                    'id' => 'capability',
                    'title' => $this->__('Capability'),
                    'content' => '<p>'
                    . $this->__('Use the Capability field to name the capability to be added or removed.')
                    . '</p>'
                ),
                array(
                    'id' => 'roles',
                    'title' => $this->__('Roles'),
                    'content' => '<p><strong>'
                    . $this->__('All Roles')
                    . '</strong>: '. $this->__('Select "All Roles", if you want the current action to be applied to all roles within your site.')
                    . '</p>'
                    . '<p><strong>'
                    . $this->__('Selected Roles')
                    . '</strong>: '. $this->__('Select "Selected Roles", if you want to individually select the roles. When this option is selected, "Administrator" role is included by default on "Add Capability" action and excluded by default on "Remove Capability" action.')
                    . '</p>'
                )
            );
        }

        protected function set_help_sidebar() {
            return array(
                array(
                    $this->__('Documentation on Add/Remove Capability'),
                    'add-remove-capability/'
                )
            );
        }

    }

}