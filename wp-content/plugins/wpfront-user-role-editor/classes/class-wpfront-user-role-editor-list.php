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

require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-add-edit.php");

if (!class_exists('WPFront_User_Role_Editor_List')) {

    /**
     * Lists Roles
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_List extends WPFront_User_Role_Editor_Controller_Base {

        const MENU_SLUG = 'wpfront-user-role-editor-all-roles';

        private $role_data = NULL;
        private $custom_columns = NULL;

        function __construct($main) {
            parent::__construct($main);
        }

        private function get_mode() {
            if (!empty($_GET['edit_role'])) {
                return array('EDIT', trim($_GET['edit_role']));
            }

            if (!empty($_GET['delete_role'])) {
                return array('DELETE', array(trim($_GET['delete_role'])));
            }

            if (!empty($_GET['set_default_role'])) {
                return array('DEFAULT_ROLE', $_GET['set_default_role']);
            }

            $action = '';
            if (!empty($_POST['doaction_top']) && !empty($_POST['action_top'])) {
                $action = $_POST['action_top'];
            } else if (!empty($_POST['doaction_bottom']) && !empty($_POST['action_bottom'])) {
                $action = $_POST['action_bottom'];
            }

            if ($action == 'delete') {
                if (!empty($_POST['selected-roles'])) {
                    return array('DELETE', array_keys($_POST['selected-roles']));
                }
            }

            return array('LIST');
        }

        public function list_roles() {
            if (!$this->can_list())
                $this->main->permission_denied();

            $mode = $this->get_mode();
            switch ($mode[0]) {
                case 'EDIT':
                    $obj = new WPFront_User_Role_Editor_Add_Edit($this->main);
                    $obj->add_edit_role($mode[1]);
                    return;
                case 'DELETE':
                    $obj = new WPFront_User_Role_Editor_Delete($this->main);
                    $obj->delete_role($mode[1]);
                    return;
                case 'DEFAULT_ROLE':
                    $this->set_default_role($mode[1]);
                    printf('<script type="text/javascript">window.location.replace("%s");</script>', $this->list_url());
                    return;
            }

            $obj = new WPFront_User_Role_Editor_Delete($this->main);
            if ($obj->is_pending_action()) {
                return;
            }

            include($this->main->pluginDIR() . 'templates/list-roles.php');
        }

        private function set_default_role($default_role) {
            if (empty($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], $this->list_url())) {
                $this->main->permission_denied();
                return;
            }

            if (!current_user_can('manage_options')) {
                $this->main->permission_denied();
                return;
            }

            update_option('default_role', $default_role);
        }

        private function get_roles() {
            if ($this->role_data != NULL)
                return $this->role_data;

            $this->role_data = array();

            global $wp_roles;
            $roles = $wp_roles->role_names;
            asort($roles, SORT_STRING);

            $editable_roles = get_editable_roles();
            if ($this->main->override_edit_permissions())
                $editable_roles = $wp_roles->get_names();

            $user_default = get_option('default_role');

            $count_users = count_users();
            $count_users = $count_users['avail_roles'];

            foreach ($roles as $key => $value) {
                $this->role_data[$key] = array(
                    'role_name' => $key,
                    'display_name' => $value,
                    'is_default' => in_array($key, WPFront_User_Role_Editor::$DEFAULT_ROLES),
                    'user_count' => isset($count_users[$key]) ? $count_users[$key] : 0, //count(get_users(array('role' => $key))),
                    'caps_count' => count(array_filter($wp_roles->roles[$key]['capabilities'])),
                    'user_default' => $key == $user_default
                );

                if ($this->can_edit()) {
                    $this->role_data[$key]['edit_url'] = $this->edit_url() . $key;

                    if ($key === self::ADMINISTRATOR_ROLE_KEY)
                        $this->role_data[$key]['is_editable'] = FALSE;
                    else {
                        $this->role_data[$key]['is_editable'] = array_key_exists($key, $editable_roles);
                    }
                }

                if ($this->can_delete()) {
                    $this->role_data[$key]['delete_url'] = $this->delete_url() . $key;

                    if ($key === self::ADMINISTRATOR_ROLE_KEY)
                        $this->role_data[$key]['is_deletable'] = FALSE;
                    else {
                        $this->role_data[$key]['is_deletable'] = array_key_exists($key, $editable_roles);
                    }
                }

                if ($key != $user_default && current_user_can('manage_options')) {
                    $this->role_data[$key]['set_default_url'] = $this->set_default_url() . $key;
                }
            }

            switch ($this->get_current_list_filter()) {
                case 'all':
                    break;
                case 'haveusers':
                    foreach ($this->role_data as $key => $value) {
                        if ($this->role_data[$key]['user_count'] == 0)
                            unset($this->role_data[$key]);
                    }
                    break;
                case 'nousers':
                    foreach ($this->role_data as $key => $value) {
                        if (!$this->role_data[$key]['user_count'] == 0)
                            unset($this->role_data[$key]);
                    }
                    break;
                case 'builtin':
                    foreach ($this->role_data as $key => $value) {
                        if (!$this->role_data[$key]['is_default'])
                            unset($this->role_data[$key]);
                    }
                    break;
                case 'custom':
                    foreach ($this->role_data as $key => $value) {
                        if ($this->role_data[$key]['is_default'])
                            unset($this->role_data[$key]);
                    }
                    break;
            }

            $search = $this->get_search_term();
            $search = strtolower(trim($search));
            if ($search !== '') {
                foreach ($this->role_data as $key => $value) {
                    if (strpos(strtolower($value['display_name']), $search) === FALSE)
                        unset($this->role_data[$key]);
                }
            }

            return $this->role_data;
        }

        private function table_header() {
            if ($this->custom_columns === NULL)
                $this->custom_columns = apply_filters('manage_roles_columns', array());
            ?>
            <tr>
                <th scope="col" id="cb" class="manage-column column-cb check-column">
                    <label class="screen-reader-text" for="cb-select-all-1"><?php echo $this->__('Select All'); ?></label>
                    <input id="cb-select-all-1" type="checkbox" />
                </th>
                <th scope="col" id="displayname" class="manage-column column-displayname">
                    <a><span><?php echo $this->__('Display Name'); ?></span></a>
                </th>
                <th scope="col" id="rolename" class="manage-column column-rolename">
                    <a><span><?php echo $this->__('Role Name'); ?></span></a>
                </th>
                <th scope="col" id="roletype" class="manage-column column-roletype">
                    <a><span><?php echo $this->__('Type'); ?></span></a>
                </th>
                <th scope="col" id="userdefault" class="manage-column column-userdefault num">
                    <a><span><?php echo $this->__('User Default'); ?></span></a>
                </th>
                <th scope="col" id="usercount" class="manage-column column-usercount num">
                    <a><span><?php echo $this->__('Users'); ?></span></a>
                </th>
                <th scope="col" id="capscount" class="manage-column column-capscount num">
                    <a><span><?php echo $this->__('Capabilities'); ?></span></a>
                </th>
                <?php
                foreach ($this->custom_columns as $key => $value) {
                    echo "<th scope='col' id='$key' class='manage-column column-$key num'>"
                    . "<a><span>$value</span></a>"
                    . "</th>";
                }
                ?>
            </tr>
            <?php
        }

        private function bulk_actions($position) {
            ?>
            <div class="tablenav <?php echo $position; ?>">
                <div class="alignleft actions bulkactions">
                    <select name="action_<?php echo $position; ?>">
                        <option value="" selected="selected"><?php echo $this->__('Bulk Actions'); ?></option>
                        <?php if ($this->can_delete()) { ?>
                            <option value="delete"><?php echo $this->__('Delete'); ?></option>
                        <?php } ?>
                    </select>
                    <input type="submit" name="doaction_<?php echo $position; ?>" class="button bulk action" value="<?php echo $this->__('Apply'); ?>">
                </div>
                <div class="tablenav-pages one-page"><span class="displaying-num"><?php echo sprintf($this->__('%s item(s)'), count($this->get_roles())); ?></span>
                    <br class="clear">
                </div>
            </div>
            <?php
        }

        private function get_search_term() {
            if (empty($_POST['s']))
                return '';

            return esc_html($_POST['s']);
        }

        private function get_list_filters() {
            $filter_data = array();

            global $wp_roles;
            $role_data = $wp_roles->role_names;

            $page = admin_url('admin.php') . '?page=' . self::MENU_SLUG;

            $filter_data['all'] = array(
                'display' => $this->__('All'),
                'url' => $page,
                'count' => count($role_data)
            );

            $count_users = count_users();
            $count_users = $count_users['avail_roles'];

            $count = 0;
            foreach ($role_data as $key => $value) {
                if(isset($count_users[$key]) && $count_users[$key] > 0) //if (count(get_users(array('role' => $key))) > 0)
                    $count++;
            }
            $filter_data['haveusers'] = array(
                'display' => $this->__('Having Users'),
                'url' => $page . '&list=haveusers',
                'count' => $count
            );

            $filter_data['nousers'] = array(
                'display' => $this->__('No Users'),
                'url' => $page . '&list=nousers',
                'count' => count($role_data) - $count
            );

            $count = 0;
            foreach ($role_data as $key => $value) {
                if (in_array($key, WPFront_User_Role_Editor::$DEFAULT_ROLES))
                    $count++;
            }
            $filter_data['builtin'] = array(
                'display' => $this->__('Built-In'),
                'url' => $page . '&list=builtin',
                'count' => $count
            );

            $filter_data['custom'] = array(
                'display' => $this->__('Custom'),
                'url' => $page . '&list=custom',
                'count' => count($role_data) - $count
            );

            return $filter_data;
        }

        private function get_current_list_filter() {
            if (empty($_GET['list']))
                return 'all';

            $list = $_GET['list'];

            switch ($list) {
                case 'all':
                case 'haveusers':
                case 'nousers':
                case 'builtin':
                case 'custom':
                    break;
                default:
                    $list = 'all';
                    break;
            }

            return $list;
        }

        protected function add_help_tab() {
            $mode = $this->get_mode();
            switch ($mode[0]) {
                case 'EDIT':
                    return array(
                        array(
                            'id' => 'overview',
                            'title' => $this->__('Overview'),
                            'content' => '<p>'
                            . $this->__('This screen allows you to edit a role within your site.')
                            . '</p>'
                            . '<p>'
                            . $this->__('You can copy capabilities from existing roles using the Copy from drop down list. Select the role you want to copy from, then click Apply to copy the capabilities. You can select or deselect capabilities even after you copy.')
                            . '</p>'
                        ),
                        array(
                            'id' => 'displayname',
                            'title' => $this->__('Display Name'),
                            'content' => '<p>'
                            . $this->__('Use the Display Name field to edit display name of the role. WordPress uses display name to display this role within your site. This field is required.')
                            . '</p>'
                        ),
                        array(
                            'id' => 'rolename',
                            'title' => $this->__('Role Name'),
                            'content' => '<p>'
                            . $this->__('Role Name is read only. WordPress uses role name to identify this role within your site.')
                            . '</p>'
                        ),
                        array(
                            'id' => 'capabilities',
                            'title' => $this->__('Capabilities'),
                            'content' => '<p>'
                            . $this->__('Capabilities are displayed as different groups for easy access. The Roles section displays capabilities created by this plugin. The Other Capabilities section displays non-standard capabilities within your site. These are usually created by plugins and themes. Use the check boxes to select the capabilities required.')
                            . '</p>'
                        )
                    );
                case 'DELETE':
                    return array(
                        array(
                            'id' => 'overview',
                            'title' => $this->__('Overview'),
                            'content' => '<p>'
                            . $this->__('This screen allows you to delete roles from your WordPress site.')
                            . '</p>'
                            . '<p>'
                            . $this->__('Use the Roles List screen to select the roles you want to delete. You can delete individual roles using the Delete row action link or delete multiple roles at the same time using the bulk action.')
                            . '</p>'
                            . '<p>'
                            . $this->__('You cannot delete administrator role, current user’s role and roles you do not have permission to.')
                            . '</p>'
                        )
                    );
            }

            return array(
                array(
                    'id' => 'overview',
                    'title' => $this->__('Overview'),
                    'content' => '<p>'
                    . $this->__('This screen lists all the existing roles within your site.')
                    . '</p>'
                    . '<p>'
                    . $this->__('To add a new role, click the Add New button at the top of the screen or Add New in the Roles menu section.')
                    . '</p>'
                ),
                array(
                    'id' => 'columns',
                    'title' => $this->__('Columns'),
                    'content' => '<p><strong>'
                    . $this->__('Display Name')
                    . '</strong>: '
                    . $this->__('Used to display this role within this site.')
                    . '</p>'
                    . '<p><strong>'
                    . $this->__('Role Name')
                    . '</strong>: '
                    . $this->__('Is used by WordPress to identify this role.')
                    . '</p>'
                    . '<p><strong>'
                    . $this->__('Type')
                    . '</strong>: '
                    . $this->__('Says whether the role is a WordPress built-in role or not. There are five built-in roles.')
                    . '</p>'
                    . '<p><strong>'
                    . $this->__('User Default')
                    . '</strong>: '
                    . $this->__('Displays whether a role is the default role of a new user.')
                    . '</p>'
                    . '<p><strong>'
                    . $this->__('Users')
                    . '</strong>: '
                    . $this->__('Number of users in that role.')
                    . '</p>'
                    . '<p><strong>'
                    . $this->__('Capabilities')
                    . '</strong>: '
                    . $this->__('Number of capabilities that role have.')
                    . '</p>'
                    . '<p><strong>'
                    . $this->__('Menu Edited')
                    . '</strong>: '
                    . $this->__('Displays whether the menu has been edited for this role. This is a pro feature.')
                    . '</p>'
                ),
                array(
                    'id' => 'actions',
                    'title' => $this->__('Actions'),
                    'content' => '<p>'
                    . $this->__('Hovering over a row in the roles list will display action links that allow you to manage roles. You can perform the following actions:')
                    . '</p>'
                    . '<p><strong>'
                    . $this->__('View')
                    . '</strong>: '
                    . $this->__('Display details about the role. You can see the capabilities assigned for that role. View link will only appear when you do not have permission to edit that role.')
                    . '</p>'
                    . '<p><strong>'
                    . $this->__('Edit')
                    . '</strong>: '
                    . $this->__('Allows you to edit that role. You can see the capabilities assigned for that role and also edit them. Edit link will only appear when you have permission to edit that role.')
                    . '</p>'
                    . '<p><strong>'
                    . $this->__('Delete')
                    . '</strong>: '
                    . $this->__('Allows you to delete that role. Delete action will not appear if you do not have permission to delete that role.')
                    . '</p>'
                    . '<p><strong>'
                    . $this->__('Default')
                    . '</strong>: '
                    . $this->__('Allows you to set that role as the default role for new user registration.')
                    . '</p>'
                    . '<p><strong>'
                    . $this->__('Edit Menu')
                    . '</strong>: '
                    . $this->__('Takes you to the menu editor screen for that role. You need "edit_role_menus" capability for this link to appear. This is a pro feature.')
                    . '</p>'
                )
            );
        }

        protected function set_help_sidebar() {
            $mode = $this->get_mode();
            switch ($mode[0]) {
                case 'EDIT':
                    return array(
                        array(
                            $this->__('Documentation on Edit Role'),
                            'edit-role/'
                        )
                    );
                case 'DELETE':
                    return array(
                        array(
                            $this->__('Documentation on Delete Roles'),
                            'delete-role/'
                        )
                    );
            }

            return array(
                array(
                    $this->__('Documentation on Roles'),
                    'list-roles/'
                )
            );
        }

    }

}
