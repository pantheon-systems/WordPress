<?php
if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('WPFront_User_Role_Editor_Export')) {

    /**
     * Export Roles
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Export extends WPFront_User_Role_Editor_Personal_Pro_Controller_Base {

        const MENU_SLUG = 'wpfront-user-role-editor-export';
        const GENERATOR = 'WPFront User Role Editor';
        const VERSION = WPFront_User_Role_Editor::VERSION;

        private $roles;

        function __construct($main) {
            parent::__construct($main);

            $this->ajax_register('wp_ajax_wpfront_user_role_editor_export_roles', array($this, 'export_roles_callback'));
        }

        public function export_roles() {
            if (!$this->can_export()) {
                $this->main->permission_denied();
                return;
            }

            global $wp_roles;
            $this->roles = $wp_roles->get_names();

            include($this->main->pluginDIR() . 'templates/personal-pro/export-roles.php');
        }

        public function export_roles_callback() {
            if (!$this->can_export()) {
                $this->main->permission_denied();
                return;
            }

            $this->main->verify_nonce();

            $sitename = sanitize_key(get_bloginfo('name'));
            if (!empty($sitename))
                $sitename .= '.';
            $filename = $sitename . 'wpfront-roles.' . date('Y-m-d') . '.xml';

            header('Content-Description: File Transfer');
            header('Content-Disposition: attachment; filename=' . $filename);
            header('Content-Type: text/xml; charset=' . get_option('blog_charset'), true);

            function cdata($str) {
                if (seems_utf8($str) == false)
                    $str = utf8_encode($str);

                // $str = ent2ncr(esc_html($str));
                $str = '<![CDATA[' . str_replace(']]>', ']]]]><![CDATA[>', $str) . ']]>';

                return $str;
            }

            function meta_data() {
                ?>
                <generator><?php echo WPFront_User_Role_Editor_Export::GENERATOR; ?></generator>
                <version><?php echo WPFront_User_Role_Editor_Export::VERSION; ?></version>
                <date><?php echo date('D, d M Y H:i:s +0000'); ?></date>
                <source><?php bloginfo_rss('name'); ?></source>
                <source_url><?php bloginfo_rss('url'); ?></source_url>
                <user_display_name><?php echo cdata(wp_get_current_user()->display_name); ?></user_display_name>
                <user_id><?php echo wp_get_current_user()->ID; ?></user_id>
                <?php
            }

            function write_role($role) {
                global $wp_roles;
                $role_names = $wp_roles->get_names();
                ?>
                <role>
                    <name><?php echo cdata($role->name); ?></name>
                    <display_name><?php echo cdata($role_names[$role->name]); ?></display_name>
                    <capabilities>
                        <?php
                        foreach ($role->capabilities as $cap => $value) {
                            ?>
                            <capability>
                                <name><?php echo cdata($cap); ?></name>
                                <value><?php echo $value; ?></value>
                            </capability>
                            <?php
                        }
                        ?>
                    </capabilities>
                </role>
                <?php
            }

            echo '<?xml version="1.0" encoding="' . get_bloginfo('charset') . "\" ?>\n";
            echo "<root>\n";
            meta_data();

            $roles = array();
            if (!empty($_POST['export-roles'])) {
                $roles = $_POST['export-roles'];
                if (!is_array($roles))
                    $roles = array();
            }

            echo "<roles>\n";

            foreach ($roles as $role => $value) {
                $r = get_role($role);
                if ($r !== NULL)
                    write_role($r);
            }

            echo '</roles>';
            echo "</root>\n";
            die();
        }

        protected function add_help_tab() {
            return array(
                array(
                    'id' => 'overview',
                    'title' => $this->__('Overview'),
                    'content' => '<p>'
                    . $this->__('This screen allows you to export the roles existing within your site and import into the same site or a different site.')
                    . '</p>'
                    . '<p>'
                    . $this->__('Select the roles to export, then click Download Export File.')
                    . '</p>'
                )
            );
        }

        protected function set_help_sidebar() {
            return array(
                array(
                    $this->__('Documentation on Export'),
                    'export-roles/'
                )
            );
        }

    }

}
