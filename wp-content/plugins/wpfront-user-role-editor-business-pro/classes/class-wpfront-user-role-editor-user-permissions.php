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

if (!class_exists('WPFront_User_Role_Editor_User_Permissions')) {

    /**
     * User Permissions
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2015 WPFront.com
     */
    class WPFront_User_Role_Editor_User_Permissions extends WPFront_User_Role_Editor_Controller_Base {

        private static $user_capabilities = array(
            'edit_users_higher_level' => 'edit_users',
            'delete_users_higher_level' => 'delete_users',
            'promote_users_higher_level' => 'promote_users',
            'promote_users_to_higher_level' => 'promote_users'
        );

        public function __construct($main) {
            parent::__construct($main);

            $users = WPFront_User_Role_Editor::$STANDARD_CAPABILITIES['Users'];
            foreach (self::$user_capabilities as $key => $value) {
                $users[$key] = $users[$value];
            }
            WPFront_User_Role_Editor::$STANDARD_CAPABILITIES['Users'] = $users;

            add_filter('user_has_cap', array($this, 'user_has_cap'), 10, 3);
            add_filter('editable_roles', array($this, 'assignable_roles'));

            add_action('admin_init', array($this, 'admin_init'));
        }

        public function assignable_roles($all_roles) {
            return $all_roles;
        }

        public function user_has_cap($allcaps, $caps, $args) {
            if (!empty($args) && count($args) === 3) {
                switch ($args[0]) {
                    case 'edit_user':
                        if (!$this->can_edit_user($args[1], $args[2])) {
                            foreach ($caps as $value) {
                                unset($allcaps[$value]);
                            }
                        }
                        break;

                    case 'delete_user':
                        if (!$this->can_delete_user($args[1], $args[2])) {
                            foreach ($caps as $value) {
                                unset($allcaps[$value]);
                            }
                        }
                        break;

                    case 'promote_user':
                        if (!$this->can_promote_user($args[1], $args[2])) {
                            foreach ($caps as $value) {
                                unset($allcaps[$value]);
                            }
                        }
                        break;
                }
            }

            return $allcaps;
        }

        protected function can_edit_user($user_id, $edit_user_id) {
            return TRUE;
        }

        protected function can_delete_user($user_id, $edit_user_id) {
            return TRUE;
        }

        protected function can_promote_user($user_id, $edit_user_id) {
            return TRUE;
        }

        public function admin_init() {
            $option_key = 'user_permission_capabilities_processed';
            $entity = new WPFront_User_Role_Editor_Entity_Options();

            $processed = $entity->get_option($option_key);
            if (!empty($processed))
                return;

            global $wp_roles;

            foreach ($wp_roles->role_objects as $key => $role) {
                foreach (self::$user_capabilities as $u_cap => $cap) {
                    if ($role->has_cap($cap)) {
                        $role->add_cap($u_cap);
                    }
                }
            }

            $entity->update_option($option_key, TRUE);
        }

    }

}

