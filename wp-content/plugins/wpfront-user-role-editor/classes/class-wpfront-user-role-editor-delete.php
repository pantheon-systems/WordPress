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

if (!class_exists('WPFront_User_Role_Editor_Delete')) {

    /**
     * WPFront User Role Editor Plugin Delete Role
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Delete extends WPFront_User_Role_Editor_Controller_Base {

        protected $roles;

        function __construct($main) {
            parent::__construct($main);
        }

        public function delete_role($delete_roles) {
            if (!$this->can_delete()) {
                $this->main->permission_denied();
                return;
            }

            $this->prepare_data($delete_roles);

            if (!empty($_POST['confirm-delete'])) {
                $this->main->verify_nonce();
                $this->delete();
                printf('<script type="text/javascript">window.location.replace("%s");</script>', $this->list_url());
                return;
            }

            $this->include_template();
        }

        protected function include_template() {
            include($this->main->pluginDIR() . 'templates/delete-role.php');
        }

        protected function prepare_data($delete_roles) {
            $this->roles = array();

            $editable_roles = get_editable_roles();
            global $wp_roles;
            $override = $this->main->override_edit_permissions();
            if ($override)
                $editable_roles = $wp_roles->get_names();

            foreach ($delete_roles as $value) {
                if (array_key_exists($value, $wp_roles->role_names)) {
                    $status_message = '';
                    $is_deletable = TRUE;
                    if (!array_key_exists($value, $editable_roles)) {
                        $status_message = 'This role cannot be deleted: Permission denied.';
                        $is_deletable = FALSE;
                    } else if ($value == self::ADMINISTRATOR_ROLE_KEY) {
                        $status_message = '\'administrator\' role cannot be deleted.';
                        $is_deletable = FALSE;
                    } else {
                        global $user_ID;
                        $user = new WP_User($user_ID);
                        if (!$override && in_array($value, $user->roles)) {
                            $status_message = 'Current user\'s role cannot be deleted.';
                            $is_deletable = FALSE;
                        }
                    }
                    $this->roles[$value] = (OBJECT) array(
                                'name' => $value,
                                'display_name' => $wp_roles->role_names[$value],
                                'is_deletable' => $is_deletable,
                                'status_message' => $status_message
                    );
                }
            }
        }

        protected function delete() {
            if (!$this->can_delete())
                return;

            foreach ($this->roles as $key => $value) {
                if ($value->is_deletable) {
                    remove_role($key);
                }
            }
        }

        public function is_pending_action() {
            if (!empty($_POST['confirm-delete']) && !empty($_POST['delete-roles'])) {
                $this->delete_role(array_keys($_POST['delete-roles']));
                return TRUE;
            }
            return FALSE;
        }

        private function get_deleting_roles() {
            return $this->roles;
        }

        private function is_submit_allowed() {
            foreach ($this->roles as $key => $value) {
                if ($value->is_deletable)
                    return TRUE;
            }

            return FALSE;
        }

    }

}