<?php
if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

if (!class_exists('WPFront_User_Role_Editor_Content_Shortcodes_List_Table')) {

    /**
     * Content Shortcodes List Table
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2015 WPFront.com
     */
    class WPFront_User_Role_Editor_Content_Shortcodes_List_Table extends WP_List_Table {

        private $main;

        public function __construct($main) {
            parent::__construct(array('screen' => 'content-shortcodes'));
            $this->main = $main;
        }

        function ajax_user_can() {
            return $this->main->can_edit_content_shortcodes();
        }

        function prepare_items() {
            $search = NULL;
            if (function_exists('wp_unslash'))
                $search = isset($_REQUEST['s']) ? wp_unslash(trim($_REQUEST['s'])) : '';
            else
                $search = isset($_REQUEST['s']) ? trim($_REQUEST['s']) : '';

            $entity = new WPFront_User_Role_Editor_Entity_Content_Shortcodes();

            $per_page = 20;
            $count = $entity->count($search);

            $page = isset($_GET['paged']) ? $_GET['paged'] : '1';
            $page = (int) $page;
            if ($page <= 0)
                $page = 1;

            if ($page > ceil($count / $per_page))
                $page = ceil($count / $per_page);

            $this->items = $entity->get_all_shortcodes($page - 1, $per_page, $search);

            $this->set_pagination_args(array(
                'total_items' => $count,
                'per_page' => $per_page,
            ));
        }

        function get_bulk_actions() {
            $actions = array();
            if ($this->main->can_delete_content_shortcodes())
                $actions['delete'] = $this->main->__('Delete');

            return $actions;
        }

        function no_items() {
            echo $this->main->__('No shortcodes found.');
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
                'name' => $this->main->__('Name'),
                'shortcode' => $this->main->__('Shortcode'),
                'usertype' => $this->main->__('User Type'),
                'roles' => $this->main->__('Roles')
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
                                    <label class="screen-reader-text" for="code_<?php echo $entity->get_name(); ?>"><?php echo sprintf($this->main->__('Select %s'), $entity->get_name()); ?></label>
                                    <input type="checkbox" id="code_<?php echo $entity->get_name(); ?>" name="allcodes[]" value="<?php echo esc_attr($entity->get_id()); ?>" />
                                </th>
                                <?php
                                break;

                            case 'name':
                                echo "<td $attributes>";
                                $edit_link = $this->main->edit_url($entity->get_id());
                                if ($this->main->can_edit_content_shortcodes()) {
                                    ?>
                                <strong>
                                    <a href="<?php echo $edit_link; ?>" class="edit">
                                        <?php echo $entity->get_name(); ?>
                                    </a>
                                </strong>
                                <?php
                            } else {
                                echo $entity->get_name();
                            }
                            ?>
                            <br/>
                            <?php
                            $actions = array();
                            if ($this->main->can_edit_content_shortcodes()) {
                                $actions['edit'] = '<a href="' . $edit_link . '">' . $this->main->__('Edit') . '</a>';
                            }
                            if ($this->main->can_delete_content_shortcodes()) {
                                $actions['delete'] = '<a href="' . $this->main->delete_url($entity->get_id()) . '">' . $this->main->__('Delete') . '</a>';
                            }
                            echo $this->row_actions($actions);
                            ?>
                            </td>
                            <?php
                            break;

                        case 'shortcode':
                            echo "<td $attributes>" . $this->main->format_shortcode($entity->get_shortcode()) . "</td>";
                            break;

                        case 'usertype':
                            echo "<td $attributes>" . $this->main->format_user_type($entity->get_user_type()) . "</td>";
                            break;

                        case 'roles':
                            echo "<td $attributes>" . $this->main->format_roles($entity->get_roles()) . "</td>";
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
