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

if (!class_exists('WPFront_User_Role_Editor_Assign_Roles')) {

    /**
     * Assign Roles
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Assign_Roles extends WPFront_User_Role_Editor_Controller_Base {

        const MENU_SLUG = 'wpfront-user-role-editor-assign-roles';
        const SECONDARY_ROLE_COLUMN_KEY = 'secondary_roles';

        private $user = NULL;
        private $userPrimaryRole = '';
        private $userSecondaryRoles = array();
        private $users;
        private $migrateFromPrimaryRole = NULL;
        private $migrateToPrimaryRole = NULL;
        private $migrateToSecondaryRoles = array();
        private $primary_roles;
        private $secondary_roles;
        private $result = NULL;

        function __construct($main) {
            parent::__construct($main);

            add_filter('manage_users_columns', array($this, 'manage_users_columns'), 10, 1);
            add_filter('manage_users_custom_column', array($this, 'manage_users_columns_content'), 10, 3);

            add_filter('user_row_actions', array($this, 'user_row_actions'), 10, 2);

            add_action('edit_user_profile', array($this, 'edit_user_profile'), 10, 1);
            add_action('user_new_form', array($this, 'user_new_form'), 10, 1);
            //add_action('edit_user_profile_update', array($this, 'edit_user_profile_update'), 1000, 1);
            add_action('profile_update', array($this, 'edit_user_profile_update'), 1000, 1);
            add_action('edit_user_created_user', array($this, 'edit_user_created_user'), 1000, 1);
        }

        public function manage_users_columns($columns) {
            if (!WPFront_Static_URE::WP_Version('4.4')) {
                $columns[self::SECONDARY_ROLE_COLUMN_KEY] = $this->__('Secondary Roles');
            }

            return $columns;
        }

        public function manage_users_columns_content($value, $column_name, $user_id) {
            if ($column_name == self::SECONDARY_ROLE_COLUMN_KEY) {
                $user = get_userdata($user_id);
                return $this->get_secondary_role_list($user->roles);
            }
            return $value;
        }

        public function user_row_actions($actions, $user_object) {
            if ($this->can_assign_roles() && $user_object->ID !== wp_get_current_user()->ID && current_user_can('promote_user', $user_object->ID))
                $actions['assign_roles'] = sprintf('<a href="%s">%s</a>', $this->get_assign_role_url($user_object), $this->__('Assign Roles'));
            return $actions;
        }

        public function edit_user_profile($user) {
            if (is_multisite() && is_network_admin())
                return;

            if (!$this->can_assign_roles())
                return;

            //TODO: remove for Administrator.
            if ($user->ID === wp_get_current_user()->ID)
                return;

            if (!current_user_can('promote_user', $user->ID))
                return;

            $this->populate_roles_array();
            $roles = array_values($user->roles);
            array_shift($roles);

            $this->secondary_roles = apply_filters('wpfront_ure_edit_user_profile_secondary_roles', $this->secondary_roles, $user);
            
            //echo "<h3>{$this->__('Secondary Roles')}</h3>";
            echo '<table class="form-table">';
            echo '<tbody>';
            echo '<tr>';
            echo "<th scope='row'>{$this->__('Secondary Roles')}</th>";
            echo '<td>';
            if(empty($this->secondary_roles)) {
                echo $this->__('No roles for this site.');
            } else {
                foreach ($this->secondary_roles as $key => $value) {
                    echo '<div style="min-width:200px;width:25%;float:left;">';
                    printf('<label><input type="checkbox" name="wpfront-secondary-roles[%s]" %s />%s</label>', $key, in_array($key, $roles) ? 'checked' : '', $value);
                    echo '</div>';
                }
            }
            echo '</td>';
            echo '</tr>';
            echo '</tbody>';
            echo '</table>';
        }
        
        public function user_new_form() {
            if (is_multisite() && is_network_admin())
                return;

            if (!$this->can_assign_roles())
                return;

            $this->populate_roles_array();

            $this->secondary_roles = apply_filters('wpfront_ure_add_user_profile_secondary_roles', $this->secondary_roles);
            
            //echo "<h3>{$this->__('Secondary Roles')}</h3>";
            echo '<table class="form-table">';
            echo '<tbody>';
            echo '<tr>';
            echo "<th scope='row'>{$this->__('Secondary Roles')}</th>";
            echo '<td>';
            if(empty($this->secondary_roles)) {
                echo $this->__('No roles for this site.');
            } else {
                foreach ($this->secondary_roles as $key => $value) {
                    echo '<div style="min-width:200px;width:25%;float:left;">';
                    printf('<label><input type="checkbox" name="wpfront-secondary-roles[%s]" />%s</label>', $key, $value);
                    echo '</div>';
                }
            }
            echo '</td>';
            echo '</tr>';
            echo '</tbody>';
            echo '</table>';
        }

        public function edit_user_profile_update($user_id) {
            if (is_multisite() && is_network_admin())
                return;

            if (!$this->can_assign_roles())
                return;

            if ($user_id === wp_get_current_user()->ID)
                return;

            if (!current_user_can('promote_user', $user_id))
                return;

            //$user = get_user_to_edit($user_id); //fatal error - function not defined.
            $user = get_userdata($user_id);
            if (empty($user))
                return;

            $this->populate_roles_array();
            foreach ($this->secondary_roles as $key => $value) {
                if (!empty($_POST["wpfront-secondary-roles"][$key]))
                    $user->add_role($key);
            }
        }
        
        public function edit_user_created_user($user_id) {
            if (is_multisite() && is_network_admin())
                return;

            if (!$this->can_assign_roles())
                return;

            $user = get_userdata($user_id);
            if (empty($user))
                return;

            $this->populate_roles_array();
            foreach ($this->secondary_roles as $key => $value) {
                if (!empty($_POST["wpfront-secondary-roles"][$key]))
                    $user->add_role($key);
            }
        }

        private function get_assign_role_url($user_object = NULL) {
            if ($user_object == NULL) {
                if (is_multisite() && is_network_admin()) {
                    $site_id = 1;
                    if (!empty($_GET['id']))
                        $site_id = $_GET['id'];
                    return get_admin_url($site_id, 'users.php') . '?page=' . self::MENU_SLUG . '&assign_roles=';
                } else
                    return admin_url('users.php') . '?page=' . self::MENU_SLUG . '&assign_roles=';
            }
            return $this->get_assign_role_url() . $user_object->ID;
        }

        private function get_secondary_role_list($roles) {
            $names = array();

            global $wp_roles;

            foreach ($roles as $value) {
                if (array_key_exists($value, $wp_roles->role_names)) {
                    $names[] = $wp_roles->role_names[$value];
                }
            }

            array_shift($names);
            return implode(', ', $names);
        }

        private function populate_roles_array() {
            global $wp_roles;
            $roles_names = $wp_roles->get_names();
            $assignable_roles = $this->main->get_assignable_roles();

            $roles = array();
            foreach ($assignable_roles as $key => $value) {
                $roles[$key] = $roles_names[$key];
            }

            $this->primary_roles = $roles;
            $this->primary_roles[''] = '&mdash;' . $this->__('No role for this site') . '&mdash;';

            $this->secondary_roles = array();
            foreach ($roles as $key => $value) {
                if ($key != self::ADMINISTRATOR_ROLE_KEY)
                    $this->secondary_roles[$key] = $value;
            }
        }

        public function assign_roles() {
            if (!$this->can_assign_roles()) {
                $this->main->permission_denied();
                return;
            }

            $this->users = get_users(array('exclude' => array(wp_get_current_user()->ID)));
            $this->users = array_filter($this->users, array($this, 'array_filter_user'));
            $this->populate_roles_array();

            if (!empty($_POST['assignroles']) && !empty($_POST['assign-user'])) {
                $this->main->verify_nonce();

                $this->result = (OBJECT) array("success" => FALSE);

                $this->user = get_userdata($_POST['assign-user']);
                if ($this->user === FALSE || $this->user->ID === wp_get_current_user()->ID) {
                    $this->user = NULL;
                    $this->result->message = $this->__('Invalid user.');
                }

                if ($this->user != NULL) {
                    if (!current_user_can('promote_user', $this->user->ID)) {
                        $this->user = NULL;
                        $this->result->message = $this->__('Permission denied.');
                    }
                }

                if ($this->user != NULL) {
                    $primary_role = NULL;
                    if (isset($_POST['assign-primary-role'])) {
                        $primary_role = $_POST['assign-primary-role'];
                        if (!array_key_exists($primary_role, $this->primary_roles))
                            $primary_role = NULL;
                    }

                    if ($primary_role !== NULL) {
                        $secondary_roles = array();
                        if (!empty($_POST['assign-secondary-roles'])) {
                            $secondary_roles = $_POST['assign-secondary-roles'];
                        }
                        if (is_array($secondary_roles))
                            $secondary_roles = array_keys($secondary_roles);
                        else
                            $secondary_roles = array();

                        $this->user->set_role($primary_role);

                        foreach ($secondary_roles as $role) {
                            $this->user->add_role($role);
                        }

                        $this->result->success = TRUE;
                        $this->result->message = $this->__('Roles updated successfully.');
                    } else {
                        $this->result->message = $this->__('Invalid primary role specified.');
                    }
                }
            }

            if (!empty($_POST['migrateroles'])) {
                $this->main->verify_nonce();

                $this->result = (OBJECT) array("success" => FALSE);

                if (isset($_POST['migrate-from-primary-role'])) {
                    $this->migrateFromPrimaryRole = $_POST['migrate-from-primary-role'];
                    if (!array_key_exists($this->migrateFromPrimaryRole, $this->primary_roles)) {
                        $this->migrateFromPrimaryRole = NULL;
                    }
                }

                if ($this->migrateFromPrimaryRole === NULL) {
                    $this->result->message = $this->__('Invalid primary role.');
                } else {
                    if (isset($_POST['migrate-primary-role'])) {
                        $this->migrateToPrimaryRole = $_POST['migrate-primary-role'];
                        if (!array_key_exists($this->migrateToPrimaryRole, $this->primary_roles)) {
                            $this->migrateToPrimaryRole = NULL;
                        }
                    }

                    if ($this->migrateToPrimaryRole === NULL) {
                        $this->result->message = $this->__('Invalid primary role.');
                    } else {
                        if (!empty($_POST['migrate-secondary-roles'])) {
                            $this->migrateToSecondaryRoles = $_POST['migrate-secondary-roles'];
                            if (is_array($this->migrateToSecondaryRoles))
                                $this->migrateToSecondaryRoles = array_keys($this->migrateToSecondaryRoles);
                            else
                                $this->migrateToSecondaryRoles = array();
                        }

                        $users = get_users(array('exclude' => array(wp_get_current_user()->ID), 'role' => $this->migrateFromPrimaryRole));
                        $users = array_filter($users, array($this, 'array_migrate_filter_user'));

                        foreach ($users as $user) {
                            $user->set_role($this->migrateToPrimaryRole);

                            foreach ($this->migrateToSecondaryRoles as $role) {
                                $user->add_role($role);
                            }
                        }

                        $this->result->success = TRUE;
                        $this->result->message = sprintf($this->__('%d user(s) migrated.'), count($users));
                    }
                }
            }

            if ($this->user == NULL && !empty($_GET['assign_roles'])) {
                $this->user = get_userdata($_GET['assign_roles']);
                if ($this->user === FALSE || $this->user->ID === wp_get_current_user()->ID || !current_user_can('promote_user', $this->user->ID))
                    $this->user = NULL;
            }

            if ($this->user == NULL) {
                if (!empty($this->users))
                    $this->user = $this->users[0];
            }

            if ($this->user != NULL) {
                $roles = $this->user->roles;
                $this->userPrimaryRole = array_shift($roles);
                if ($this->userPrimaryRole === NULL)
                    $this->userPrimaryRole = '';
                $this->userSecondaryRoles = $roles;
            }

            include($this->main->pluginDIR() . 'templates/assign-roles.php');
        }

        private function array_filter_user($user) {
            return current_user_can('promote_user', $user->ID);
        }

        private function array_migrate_filter_user($user) {
            if ($this->migrateFromPrimaryRole === '') {
                if (empty($user->roles))
                    return TRUE;
            }

            if (empty($user->roles))
                return FALSE;

            if (!current_user_can('promote_user', $user->ID))
                return FALSE;

            $roles = $user->roles;
            $role = array_shift($roles);
            return $role === $this->migrateFromPrimaryRole;
        }

        private function primary_secondary_section($prefix, $selectPrimaryRole = NULL, $selectSecondaryRoles = array()) {
            ?>
            <tr>
                <th scope="row">
                    <?php echo $this->__('Primary Role'); ?>
                </th>
                <td>
                    <select id="<?php echo $prefix; ?>_roles_list" name="<?php echo $prefix; ?>-primary-role">
                        <?php
                        foreach ($this->primary_roles as $key => $role) {
                            ?>
                            <option value="<?php echo $key; ?>" <?php echo $selectPrimaryRole === $key ? 'selected' : ''; ?>>
                                <?php echo $role; ?>
                            </option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php echo $this->__('Secondary Roles'); ?>
                </th>
                <td>
                    <div class="role-list">
                        <?php
                        foreach ($this->secondary_roles as $key => $role) {
                            ?>
                            <div class="role-list-item">
                                <label>
                                    <input type="checkbox" name="<?php echo $prefix; ?>-secondary-roles[<?php echo $key; ?>]" <?php echo in_array($key, $selectSecondaryRoles) ? 'checked' : ''; ?> />
                                    <?php echo $role; ?>
                                </label>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </td>
            </tr>
            <?php
        }

        protected function add_help_tab() {
            return array(
                array(
                    'id' => 'overview',
                    'title' => $this->__('Overview'),
                    'content' => '<p>'
                    . $this->__('This screen allows you to assign multiple roles to a user and also allows you to migrate users from a role to another role.')
                    . '</p>'
                ),
                array(
                    'id' => 'assignroles',
                    'title' => $this->__('Assign Roles'),
                    'content' => '<p>'
                    . $this->__('To assign multiple roles to a user, select that user within the User drop down list and select the primary role you want for that user using the Primary Role drop down list. Select the secondary roles using the check boxes below, then click Assign Roles.')
                    . '</p>'
                ),
                array(
                    'id' => 'migrateusers',
                    'title' => $this->__('Migrate Users'),
                    'content' => '<p>'
                    . $this->__('To migrate users from one role to another role or to add secondary roles to users belonging to a particular primary role, use the migrate users functionality.')
                    . '</p>'
                    . '<p>'
                    . $this->__('Select the users using the From Primary Role drop down, to primary role using the Primary Role drop down and secondary roles using the check boxes then click Migrate Users.')
                    . '</p>'
                )
            );
        }

        protected function set_help_sidebar() {
            return array(
                array(
                    $this->__('Documentation on Assign / Migrate Users'),
                    'assign-migrate-users/'
                )
            );
        }

    }

}
