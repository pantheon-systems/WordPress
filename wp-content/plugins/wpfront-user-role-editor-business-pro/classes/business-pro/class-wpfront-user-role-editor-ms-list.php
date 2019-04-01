<?php
if (!defined('ABSPATH')) {
    exit();
}

require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-ms-roles-list-table.php");
require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-ms-delete.php");

if (!class_exists('WPFront_User_Role_Editor_MS_List')) {

    /**
     * Multisite List Roles
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_MS_List extends WPFront_User_Role_Editor_Business_Pro_Controller_Base {

        const MENU_SLUG = 'wpfront-user-role-editor-ms-list';

        private $table;

        private function get_mode() {
            if (!empty($_GET['edit_role'])) {
                return array('EDIT', trim($_GET['edit_role']));
            }

            if (!empty($_GET['delete_role'])) {
                return array('DELETE', array(trim($_GET['delete_role'])));
            }

            $action = '';
            if (!empty($_POST['action']))
                $action = $_POST['action'] == '-1' ? $_POST['action2'] : $_POST['action'];

            switch ($action) {
                case 'delete':
                    if (!empty($_POST['allroles']))
                        return array('DELETE', $_POST['allroles']);
                    return array('LIST');
            }

            return array('LIST');
        }

        public function list_roles() {
            if (!$this->can_manage_network_roles()) {
                $this->main->permission_denied();
                return;
            }

            if ($this->main->large_network_warning())
                return;

            $mode = $this->get_mode();

            switch ($mode[0]) {
                case 'EDIT':
                    $obj = new WPFront_User_Role_Editor_MS_Add_Edit($this->main);
                    $obj->set_multisite(TRUE);
                    $obj->add_edit_role($mode[1]);
                    return;
                case 'DELETE':
                    $obj = new WPFront_User_Role_Editor_MS_Delete($this->main);
                    $obj->set_multisite(TRUE);
                    $obj->delete_role($mode[1]);
                    return;
            }

            $obj = new WPFront_User_Role_Editor_MS_Delete($this->main);
            if ($obj->is_pending_action())
                return;

            $this->table = new WPFront_User_Role_Editor_MS_Roles_List_Table($this);
            $this->table->prepare_items();
            ?>
            <div class="wrap ms-list-roles">
                <h2>
                    <?php
                    echo $this->__('Roles');
                    if ($this->can_manage_network_roles()) :
                        ?>
                        <a href="<?php echo $this->network_add_new_url(); ?>" class="add-new-h2"><?php echo $this->__('Add New'); ?></a><?php
            endif;
                    ?>
                </h2>

                <?php $this->table->views(); ?>

                <form action="" method="get" class="search-form">
                    <input type="hidden" name="page" value="<?php echo WPFront_User_Role_Editor_MS_List::MENU_SLUG; ?>" />
                    <?php $this->table->search_box($this->__('Search Roles'), 'all-role'); ?>
                </form>

                <form id="form-role-list" method='post'>
                    <?php $this->table->display(); ?>
                </form>
            </div>

            <script type="text/javascript">

                (function($) {
                    $("div.wrap.ms-list-roles td a.link-role-sites").click(function() {
                        $(this).next().toggle();
                    });
                })(jQuery);

            </script>

            <?php
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
                            . $this->__('This screen allows you to edit a role within your network.')
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
                            . $this->__('This screen allows you to delete roles from your network sites.')
                            . '</p>'
                            . '<p>'
                            . $this->__('Use the Roles List screen to select the roles you want to delete. You can delete individual roles using the Delete row action link or delete multiple roles at the same time using the bulk action.')
                            . '</p>'
                        )
                    );
            }

            return array(
                array(
                    'id' => 'overview',
                    'title' => $this->__('Overview'),
                    'content' => '<p>'
                    . $this->__('This screen displays all the roles existing within your multisite network.')
                    . '</p>'
                    . '<p>'
                    . $this->__('To add a new role, click the Add New button at the top of the screen or Add New in the Roles menu section.')
                    . '</p>'
                ),
                array(
                    'id' => 'columns',
                    'title' => $this->__('Columns'),
                    'content' => '<p><strong>'
                    . $this->__('Role Name')
                    . '</strong>: '
                    . $this->__('Is used by WordPress to identify this role.')
                    . '</p>'
                    . '<p><strong>'
                    . $this->__('Display Name')
                    . '</strong>: '
                    . $this->__('WordPress uses display name to display this role within the site. You can have different display names for the same role within the network.')
                    . '</p>'
                    . '<p><strong>'
                    . $this->__('Role Type')
                    . '</strong>: '
                    . $this->__('Displays whether that role is a built-in role or a custom role.')
                    . '</p>'
                    . '<p><strong>'
                    . $this->__('Sites')
                    . '</strong>: '
                    . $this->__('Display the sites that role belongs to. [All Sites] mean, that role is part of every site within the network. Else it will display the number of sites as a link. Clicking on the link will display the site list.')
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
                    . $this->__('Allows you to delete that role.')
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
                            $this->__('Documentation on Multisite Edit Role'),
                            'multisite-edit-role/'
                        )
                    );
                case 'DELETE':
                    return array(
                        array(
                            $this->__('Documentation on Multisite Delete Roles'),
                            'multisite-delete-role/'
                        )
                    );
            }

            return array(
                array(
                    $this->__('Documentation on Multisite Roles'),
                    'multisite-roles-list/'
                )
            );
        }

    }

}
