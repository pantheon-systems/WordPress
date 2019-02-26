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

require_once plugin_dir_path(__FILE__) . 'class-wpfront-user-role-editor-nav-menu-walker.php';

if (!class_exists('WPFront_User_Role_Editor_Nav_Menu')) {

    /**
     * Navigation Menu Controller
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2015 WPFront.com
     */
    class WPFront_User_Role_Editor_Nav_Menu extends WPFront_User_Role_Editor_Controller_Base {

        protected static $ALL_USERS = 1;
        protected static $LOGGEDIN_USERS = 2;
        protected static $GUEST_USERS = 3;
        protected static $ROLE_USERS = 4;
        protected static $META_DATA_KEY = 'wpfront-user-role-editor-nav-menu-data';

        public function __construct($main) {
            parent::__construct($main);

            if ($this->main->disable_navigation_menu_permissions())
                return;

            add_action('init', array($this, 'wp_init'), 9999);
            add_action('wp_nav_menu_item_custom_fields', array($this, 'menu_item_custom_fields'), 10, 4);
            add_action('wp_nav_menu_item_title_user_restriction_type', array($this, 'menu_item_title_user_restriction_type'), 10, 4);
            add_action('wp_nav_menu_item_custom_fields_roles_list', array($this, 'menu_item_custom_fields_roles_list'), 10, 4);

            add_action('wp_update_nav_menu_item', array($this, 'update_nav_menu_item'), 10, 3);
            add_filter('wp_get_nav_menu_items', array($this, 'override_nav_menu_items'), 10, 3);

            add_action('admin_print_scripts-nav-menus.php', array($this, 'enqueue_menu_scripts'));
            add_action('admin_print_styles-nav-menus.php', array($this, 'enqueue_menu_styles'));
            add_action('load-nav-menus.php', array($this, 'menu_walker_override_notice_action'));
        }

        public function nav_menu_help_url() {
            return 'https://wpfront.com/user-role-editor-pro/navigation-menu-permissions/';
        }

        public function wp_init() {
            add_filter('wp_edit_nav_menu_walker', array($this, 'override_edit_nav_menu_walker'), 999999);
        }
        
        public function menu_walker_override_notice_action() {
            add_action('admin_notices', array($this, 'menu_walker_override_warning'));
        }

        public function override_edit_nav_menu_walker($current = 'Walker_Nav_Menu_Edit') {
            if ($current !== 'Walker_Nav_Menu_Edit' && !$this->main->override_navigation_menu_permissions())
                return $current;

            return 'WPFront_User_Role_Editor_Nav_Menu_Walker';
        }
        
        public function menu_walker_override_warning() {
            if ($this->main->disable_navigation_menu_permissions() === FALSE) {
                $menu_walker = apply_filters('wp_edit_nav_menu_walker', 'Walker_Nav_Menu_Edit', 0);
                if ($menu_walker !== $this->override_edit_nav_menu_walker()) {
                    printf(
                            '<div class="notice notice-error is-dismissible"><p>%s</p></div>', 
                            sprintf(
                                    $this->__('Menu walker class is overriden by a theme/plugin. Current value = %s. Navigation menu permissions may still work. %s'), 
                                    $menu_walker, 
                                    '<a target="_blank" href="' . $this->nav_menu_help_url() . '#navigation-menu-permission-warning">' . $this->__('More information') . '</a>'
                            )
                    );
                    
                }
            }
        }

        public function menu_item_title_user_restriction_type($item_id, $item, $depth, $args) {
            if (!current_user_can('edit_nav_menu_permissions'))
                return;

            $data = $this->get_meta_data($item_id);
            $text = $this->__('All Users');

            switch ($data->type) {
                case self::$LOGGEDIN_USERS:
                    $text = $this->__('Logged in Users');
                    break;
                case self::$GUEST_USERS:
                    $text = $this->__('Guest Users');
                    break;
                case self::$ROLE_USERS:
                    $text = $this->__('Users by Role');
                    break;
            }
            ?>
            <span class="is-submenu">
                <?php echo '(' . $text . ')'; ?>
            </span>
            <?php
        }

        public function menu_item_custom_fields_roles_list($item_id, $item, $depth, $args) {
            printf($this->__('%s to limit based on roles.'), '<a target="_blank" href="https://wpfront.com/navmenu">' . $this->__('Upgrade to Pro') . '</a>');
        }

        public function menu_item_custom_fields($item_id, $item, $depth, $args) {
            if (!current_user_can('edit_nav_menu_permissions'))
                return;

            $data = $this->get_meta_data($item_id);
            $this->main->create_nonce($item_id);
            ?>
            <p class="description description-wide"></p>
            <p class="description description-wide">
                <label><?php echo $this->__('User Restrictions'); ?></label>
                <span class="user-restriction-container">
                    <label><input class="user-restriction-type" type="radio" name="<?php echo 'user-restriction-type-' . $item_id; ?>" value="<?php echo self::$ALL_USERS; ?>" <?php echo $data->type === self::$ALL_USERS ? 'checked' : ''; ?> /><?php echo $this->__('All Users'); ?></label>
                    <label><input class="user-restriction-type" type="radio" name="<?php echo 'user-restriction-type-' . $item_id; ?>" value="<?php echo self::$LOGGEDIN_USERS; ?>" <?php echo $data->type === self::$LOGGEDIN_USERS ? 'checked' : ''; ?> /><?php echo $this->__('Logged in Users'); ?></label>
                    <label><input class="user-restriction-type" type="radio" name="<?php echo 'user-restriction-type-' . $item_id; ?>" value="<?php echo self::$GUEST_USERS; ?>" <?php echo $data->type === self::$GUEST_USERS ? 'checked' : ''; ?> /><?php echo $this->__('Guest Users'); ?></label>
                    <label><input class="user-restriction-type" type="radio" name="<?php echo 'user-restriction-type-' . $item_id; ?>" value="<?php echo self::$ROLE_USERS; ?>" <?php echo $data->type === self::$ROLE_USERS ? 'checked' : ''; ?> /><?php echo $this->__('Users by Role'); ?></label>
                    <span class="roles-container <?php echo $data->type === self::$ROLE_USERS ? '' : 'hidden'; ?>">
                        <?php do_action('wp_nav_menu_item_custom_fields_roles_list', $item_id, $item, $depth, $args); ?>
                    </span>
                </span>
            </p>
            <?php
        }

        protected function update_nav_menu_item_sub($menu_id, $menu_item_db_id, $args, $data) {
            return $data;
        }

        public function update_nav_menu_item($menu_id, $menu_item_db_id, $args) {
            if (!current_user_can('edit_nav_menu_permissions'))
                return;

            $data = $this->get_meta_data($menu_item_db_id);

            if (!empty($_POST['user-restriction-type-' . $menu_item_db_id])) {
                $this->main->verify_nonce($menu_item_db_id);

                $data->type = intval($_POST['user-restriction-type-' . $menu_item_db_id]);

                $data = $this->update_nav_menu_item_sub($menu_id, $menu_item_db_id, $args, $data);
            }

            update_post_meta($menu_item_db_id, self::$META_DATA_KEY, $data);
        }

        protected function override_nav_menu_items_sub($item, $data) {
            return FALSE;
        }

        public function override_nav_menu_items($items, $menu, $args) {
            if (is_admin()) {
                return $items;
            }

            $remove_parent = array();

            foreach ($items as $key => $item) {
                $data = $this->get_meta_data($item->db_id);

                $remove = FALSE;

                switch ($data->type) {
                    case self::$LOGGEDIN_USERS:
                        $remove = !is_user_logged_in();
                        break;
                    case self::$GUEST_USERS:
                        $remove = is_user_logged_in();
                        break;
                    case self::$ROLE_USERS:
                        $remove = $this->override_nav_menu_items_sub($item, $data);
                    default:
                        break;
                }

                if ($remove) {
                    $remove_parent[] = $item->ID;
                    unset($items[$key]);
                }
            }

            while (!empty($remove_parent)) {
                $continue = FALSE;

                foreach ($items as $key => $item) {
                    if (empty($item)) {
                        continue;
                    }

                    if (intval($item->menu_item_parent) === intval($remove_parent[0])) {
                        $remove_parent[] = $item->ID;
                        unset($items[$key]);
                        $continue = TRUE;
                    }
                }

                if ($continue)
                    continue;

                array_shift($remove_parent);
            }

            return array_values($items);
        }

        protected function get_meta_data($menu_item_db_id) {
            $data = get_post_meta($menu_item_db_id, self::$META_DATA_KEY, true);

            if (empty($data)) {
                $data = (OBJECT) array('type' => self::$ALL_USERS);
            }

            switch (intval($data->type)) {
                case self::$LOGGEDIN_USERS:
                case self::$GUEST_USERS:
                case self::$ROLE_USERS:
                    $data->type = intval($data->type);
                    break;
                default:
                    $data->type = self::$ALL_USERS;
                    break;
            }

            return $data;
        }

        public function enqueue_menu_scripts() {
            wp_enqueue_script('jquery');
            wp_enqueue_script('wpfront-user-role-editor-nav-menu-js', $this->main->pluginURL() . 'js/nav-menu.js', array('jquery'), WPFront_User_Role_Editor::VERSION);
        }

        public function enqueue_menu_styles() {
            wp_enqueue_style('wpfront-user-role-editor-nav-menu-css', $this->main->pluginURL() . 'css/nav-menu.css', array(), WPFront_User_Role_Editor::VERSION);
        }

        public static function uninstall() {
            delete_post_meta_by_key(self::$META_DATA_KEY);
        }

    }

}