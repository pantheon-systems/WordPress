<?php
if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

if (!class_exists('WPFront_User_Role_Editor_Login_Redirect_List_Table')) {

    /**
     * Login Redirect List Table
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2015 WPFront.com
     */
    class WPFront_User_Role_Editor_Login_Redirect_List_Table extends WP_List_Table {

        private $main;

        public function __construct($main) {
            parent::__construct(array('screen' => 'login-redirect'));
            $this->main = $main;
        }

        function ajax_user_can() {
            return $this->main->can_edit_login_redirect();
        }

        function prepare_items() {
            $search = NULL;
            if (function_exists('wp_unslash'))
                $search = isset($_REQUEST['s']) ? wp_unslash(trim($_REQUEST['s'])) : '';
            else
                $search = isset($_REQUEST['s']) ? trim($_REQUEST['s']) : '';

            $entity = new WPFront_User_Role_Editor_Entity_Login_Redirect();

            $per_page = PHP_INT_MAX;
            $count = $entity->count($search);

            $page = isset($_GET['paged']) ? $_GET['paged'] : '1';
            $page = (int) $page;
            if ($page <= 0)
                $page = 1;

            if ($page > ceil($count / $per_page))
                $page = ceil($count / $per_page);

            $this->items = $entity->get_all_login_redirect($search);

            $this->set_pagination_args(array(
                'total_items' => $count,
                'per_page' => $per_page,
            ));
        }

        function get_bulk_actions() {
            $actions = array();
            if ($this->main->can_delete_login_redirect())
                $actions['delete'] = $this->main->__('Delete');

            return $actions;
        }

        function no_items() {
            echo $this->main->__('No login redirects found.');
        }

        function get_views() {
            $role_links = array();

            return $role_links;
        }

        function pagination($which) {
            parent::pagination($which);
        }

        function get_columns() {
            $columns = array(
                'cb' => '<input type="checkbox" />',
                'role' => $this->main->__('Role'),
                'priority' => $this->main->__('Priority'),
                'url' => $this->main->__('Login Redirect URL'),
                'logout_url' => $this->main->__('Logout Redirect URL'),
                'deny_wpadmin' => $this->main->__('WP-ADMIN'),
                'disable_toolbar' => $this->main->__('Toolbar')
            );

            return $columns;
        }

        function get_sortable_columns() {
            return array(
            );
        }

        function display_rows() {
            $alt = '';

            foreach ($this->items as $entity) {
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
                                    <label class="screen-reader-text" for="role_<?php echo $entity->get_role(); ?>"><?php echo sprintf($this->main->__('Select %s'), $entity->get_role()); ?></label>
                                    <input type="checkbox" id="code_<?php echo $entity->get_role(); ?>" name="role[]" value="<?php echo esc_attr($entity->get_role()); ?>" />
                                </th>
                                <?php
                                break;

                            case 'role':
                                $role = $this->main->get_role_display_name($entity->get_role());
                                echo "<td $attributes>";
                                $edit_link = $this->main->login_redirect_edit_url($entity->get_role());
                                if ($this->main->can_edit_login_redirect()) {
                                    ?>
                                <strong>
                                    <a href="<?php echo $edit_link; ?>" class="edit">
                                        <?php echo $role; ?>
                                    </a>
                                </strong>
                                <?php
                            } else {
                                echo $role;
                            }
                            ?>
                            <br/>
                            <?php
                            $actions = array();
                            if ($this->main->can_edit_login_redirect()) {
                                $actions['edit'] = '<a href="' . $edit_link . '">' . $this->main->__('Edit') . '</a>';
                            }
                            if ($this->main->can_delete_login_redirect()) {
                                $actions['delete'] = '<a href="' . $this->main->login_redirect_delete_url($entity->get_role()) . '">' . $this->main->__('Delete') . '</a>';
                            }
                            echo $this->row_actions($actions);
                            ?>
                            </td>
                            <?php
                            break;

                        case 'priority':
                            echo "<td $attributes>" . $entity->get_priority() . "</td>";
                            break;

                        case 'url':
                            echo "<td $attributes>" . $this->main->format_url($entity->get_url()) . "</td>";
                            break;
                        
                        case 'logout_url':
                            echo "<td $attributes>" . $this->main->format_url($entity->get_logout_url()) . "</td>";
                            break;

                        case 'deny_wpadmin':
                            echo "<td $attributes>" . ($entity->get_deny_wpadmin() ? '<i class="fa fa-times"></i>' : '<i class="fa fa-check"></i>') . "</td>";
                            break;

                        case 'disable_toolbar':
                            echo "<td $attributes>" . ($entity->get_disable_toolbar() ? '<i class="fa fa-times"></i>' : '<i class="fa fa-check"></i>') . "</td>";
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
