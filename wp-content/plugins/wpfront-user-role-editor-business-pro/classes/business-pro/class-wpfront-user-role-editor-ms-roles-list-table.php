<?php
if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

if (!class_exists('WPFront_User_Role_Editor_MS_Roles_List_Table')) {

    /**
     * Multisite List Roles
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_MS_Roles_List_Table extends WP_List_Table {

        private $main;
        private $blogs_count;
        private $builtin_roles = array();
        private $custom_roles = array();

        public function __construct($main) {
            parent::__construct(array('screen' => 'roles-network'));
            $this->main = $main;
        }

        function ajax_user_can() {
            $controller = new WPFront_User_Role_Editor_Business_Pro_Controller_Base($this->main);
            return $controller->can_manage_network_roles();
        }

        function prepare_items() {
            $rolesearch = '';
            if(function_exists('wp_unslash'))
                $rolesearch = isset($_REQUEST['s']) ? wp_unslash(trim($_REQUEST['s'])) : '';
            else
                $rolesearch = isset($_REQUEST['s']) ? trim($_REQUEST['s']) : '';

            $roletype = $this->get_roletype();
            $roles_per_page = 50;

            $this->items = array();

            $controller = new WPFront_User_Role_Editor_Business_Pro_Controller_Base($this->main);
            $blog_ids = $controller->get_ms_blog_ids();
            $this->blogs_count = count($blog_ids);

            $obj_roles = $controller->get_ms_roles_info();
            foreach ($obj_roles as $role_name => $role_info) {
                $is_default = in_array($role_name, WPFront_User_Role_Editor::$DEFAULT_ROLES);

                if ($is_default)
                    $this->builtin_roles[$role_name] = 1;
                else
                    $this->custom_roles[$role_name] = 1;

                $skip = FALSE;
                switch ($roletype) {
                    case 'built-in':
                        if (!$is_default)
                            $skip = TRUE;
                        break;
                    case 'custom':
                        if ($is_default)
                            $skip = TRUE;
                        break;
                }

                if ($skip) {
                    unset($obj_roles[$role_name]);
                    continue;
                }

                if ($rolesearch !== '' && stripos($role_name, $rolesearch) === FALSE) {
                    unset($obj_roles[$role_name]);
                    continue;
                }

                $obj_roles[$role_name]->role_type = $is_default ? 'Built-In' : 'Custom';
            }

            ksort($obj_roles);

            foreach ($obj_roles as $role_name => $role) {
                $this->items[] = (OBJECT) array('role_name' => $role_name, 'role_type' => $role->role_type, 'display_names' => $role->display_names, 'blogs' => $role->blogs);
            }

            restore_current_blog();

            $this->set_pagination_args(array(
                'total_items' => count($this->items),
                'per_page' => $roles_per_page,
            ));

            $page = isset($_GET['paged']) ? $_GET['paged'] : '1';
            $page = (int) $page;
            if ($page <= 0)
                $page = 1;

            if ($page > ceil(count($this->items) / $roles_per_page))
                $page = ceil(count($this->items) / $roles_per_page);

            for ($i = 0; $i < ($page - 1) * $roles_per_page; $i++) {
                array_shift($this->items);
            }

            while (count($this->items) > $roles_per_page) {
                array_pop($this->items);
            }
        }

        function get_bulk_actions() {
            $actions = array();
            $controller = new WPFront_User_Role_Editor_Business_Pro_Controller_Base($this->main);
            if ($controller->can_manage_network_roles())
                $actions['delete'] = $this->main->__('Delete');

            return $actions;
        }

        function no_items() {
            echo $this->main->__('No roles found.');
        }

        private function get_roletype() {
            $role_type = isset($_GET['roletype']) ? $_GET['roletype'] : 'all';

            switch ($role_type) {
                case 'all':
                case 'built-in':
                case 'custom':
                    break;

                default:
                    $role_type = 'all';
                    break;
            }

            return $role_type;
        }

        function get_views() {
            $role_type = $this->get_roletype();

            $role_links = array();

            $class = $role_type == 'all' ? ' class="current"' : '';
            $role_links['all'] = "<a href='" . $this->main->network_list_url() . "'$class>" . sprintf($this->main->__('All <span class="count">(%s)</span>'), count($this->builtin_roles) + count($this->custom_roles)) . '</a>';

            $class = $role_type == 'built-in' ? ' class="current"' : '';
            $role_links['built-in'] = "<a href='" . $this->main->network_list_url() . "&roletype=built-in'$class>" . sprintf($this->main->__('Built-In <span class="count">(%s)</span>'), count($this->builtin_roles)) . '</a>';

            $class = $role_type == 'custom' ? ' class="current"' : '';
            $role_links['custom'] = "<a href='" . $this->main->network_list_url() . "&roletype=custom'$class>" . sprintf($this->main->__('Custom <span class="count">(%s)</span>'), count($this->custom_roles)) . '</a>';

            return $role_links;
        }

        function pagination($which) {
            parent::pagination($which);
        }

        function get_columns() {
            $roles_columns = array(
                'cb' => '<input type="checkbox" />',
                'rolename' => $this->main->__('Role Name'),
                'displayname' => $this->main->__('Display Name'),
                'roletype' => $this->main->__('Role Type'),
                'blogs' => $this->main->__('Sites')
            );
            $roles_columns = apply_filters('wpmu_roles_columns', $roles_columns);

            return $roles_columns;
        }

        function get_sortable_columns() {
            return array(
            );
        }

        function display_rows() {
            $alt = '';

            foreach ($this->items as $role) {
                $alt = ( 'alternate' == $alt ) ? '' : 'alternate';
                ?>
                <tr class="<?php echo $alt; ?>">
                    <?php
                    list( $columns, $hidden ) = $this->get_column_info();

                    foreach ($columns as $column_name => $column_display_name) :
                        $class = "class='$column_name column-$column_name'";

                        $style = '';
                        if (in_array($column_name, $hidden))
                            $style = ' style="display:none;"';

                        $attributes = "$class$style";

                        switch ($column_name) {
                            case 'cb':
                                ?>
                                <th scope="row" class="check-column">
                                    <label class="screen-reader-text" for="role_<?php echo $role->role_name; ?>"><?php echo sprintf($this->main->__('Select %s'), $role->role_name); ?></label>
                                    <input type="checkbox" id="role_<?php echo $role->role_name; ?>" name="allroles[]" value="<?php echo esc_attr($role->role_name); ?>" />
                                </th>
                                <?php
                                break;

                            case 'rolename':
                                echo "<td $attributes>";
                                $edit_link = $this->main->network_list_url() . '&edit_role=' . $role->role_name;
                                ?>
                            <strong>
                                <a href="<?php echo $edit_link; ?>" class="edit">
                                    <?php echo $role->role_name; ?>
                                </a>
                            </strong>
                            <br/>
                            <?php
                            $actions = array();
                            $controller = new WPFront_User_Role_Editor_Business_Pro_Controller_Base($this->main);
                            if ($controller->can_manage_network_roles()) {
                                if ($role->role_name != WPFront_User_Role_Editor::ADMINISTRATOR_ROLE_KEY) {
                                    $actions['edit'] = '<a href="' . $edit_link . '">' . $this->main->__('Edit') . '</a>';
                                    $actions['delete'] = '<a href="' . $this->main->network_list_url() . '&delete_role=' . $role->role_name . '" class="delete">' . $this->main->__('Delete') . '</a>';
                                } else {
                                    $actions['edit'] = '<a href="' . $edit_link . '">' . $this->main->__('View') . '</a>';
                                }
                            }

                            $actions = apply_filters('ms_role_row_actions', $actions, $role);
                            echo $this->row_actions($actions);
                            ?>
                            </td>
                            <?php
                            break;

                        case 'displayname':
                            echo "<td $attributes>" . implode('<br />', $role->display_names) . "</td>";
                            break;

                        case 'roletype':
                            echo "<td $attributes>" . $this->main->__($role->role_type) . "</td>";
                            break;

                        case 'blogs':
                            echo "<td $attributes>";
                            if (count($role->blogs) == $this->blogs_count)
                                echo $this->main->__('[All Sites]');
                            else {
                                echo '<a class="link-role-sites" href="javascript:void(0);">' . sprintf($this->main->__('%d Site(s)'), count($role->blogs)) . '</a>';
                                echo '<div class="role-sites">';
                                foreach ($role->blogs as $blog_id) {
                                    $val = get_blog_details($blog_id);
                                    $val->userblog_id = $blog_id;

                                    if (!can_edit_network($val->site_id))
                                        continue;

                                    $path = ( $val->path == '/' ) ? '' : $val->path;
                                    echo '<span class="site-' . $val->site_id . '" >';
                                    echo '<a href="' . esc_url(network_admin_url('site-info.php?id=' . $val->userblog_id)) . '">' . str_replace('.' . get_current_site()->domain, '', $val->domain . $path) . '</a>';
                                    echo ' <small class="row-actions">';
                                    $actions = array();
                                    $actions['edit'] = '<a href="' . esc_url(network_admin_url('site-info.php?id=' . $val->userblog_id)) . '">' . __('Edit') . '</a>';

                                    $class = '';
                                    if (get_blog_status($val->userblog_id, 'spam') == 1)
                                        $class .= 'site-spammed ';
                                    if (get_blog_status($val->userblog_id, 'mature') == 1)
                                        $class .= 'site-mature ';
                                    if (get_blog_status($val->userblog_id, 'deleted') == 1)
                                        $class .= 'site-deleted ';
                                    if (get_blog_status($val->userblog_id, 'archived') == 1)
                                        $class .= 'site-archived ';

                                    $actions['view'] = '<a class="' . $class . '" href="' . esc_url(get_home_url($val->userblog_id)) . '">' . __('View') . '</a>';

                                    $actions = apply_filters('ms_role_list_site_actions', $actions, $val->userblog_id);

                                    $i = 0;
                                    $action_count = count($actions);
                                    foreach ($actions as $action => $link) {
                                        ++$i;
                                        ( $i == $action_count ) ? $sep = '' : $sep = ' | ';
                                        echo "<span class='$action'>$link$sep</span>";
                                    }
                                    echo '</small></span><br/>';
                                }
                                echo '</div>';
                            }
                            ?>
                            </td>
                            <?php
                            break;

                        default:
                            echo "<td $attributes>";
                            echo apply_filters('manage_roles_custom_column', '', $column_name, $role);
                            echo "</td>";
                            break;
                    }
                endforeach
                ?>
                </tr>
                <?php
            }
        }

    }

}
