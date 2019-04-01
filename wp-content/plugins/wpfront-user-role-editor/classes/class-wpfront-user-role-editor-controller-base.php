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

if (!class_exists('WPFront_User_Role_Editor_Controller_Base')) {

    /**
     * Base class of WPFront User Role Editor Controllers
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Controller_Base {

        const ADMINISTRATOR_ROLE_KEY = WPFront_User_Role_Editor::ADMINISTRATOR_ROLE_KEY;
        
        protected $main;
        protected $page_hook;
        protected $multisite = FALSE;
        private static $ajax_handlers = array();

        public function __construct($main) {
            $this->main = $main;

            add_action('admin_init', array($this, 'check_multisite'));
        }

        public function set_page_hook($page_hook) {
            $this->page_hook = $page_hook;
            add_action("load-{$this->page_hook}", array($this, 'set_help_tab'));
        }

        public static function register_ajax_handlers() {
            foreach (self::$ajax_handlers as $key => $value) {
                add_action($key, $value);
            }
        }

        protected function ajax_register($key, $func) {
            self::$ajax_handlers[$key] = $func;
        }

        public function check_multisite() {
            $this->multisite = is_network_admin();
        }

        public function set_multisite($multisite) {
            $this->multisite = $multisite;
        }

        public function set_help_tab() {
            $screen = get_current_screen();

            $tabs = $this->add_help_tab();
            if ($tabs !== NULL) {
                foreach ($tabs as $value) {
                    $screen->add_help_tab($value);
                }
            }

            $sidebar = $this->set_help_sidebar();
            if ($sidebar !== NULL) {
                $s = '<p><strong>' . $this->__('Links:') . '</strong></p>';

                foreach ($sidebar as $value) {
                    $s .= '<p><a target="_blank" href="http://wpfront.com/user-role-editor-pro/' . $value[1] . '">' . $value[0] . '</a></p>';
                }

                $s .= '<p><a target="_blank" href="http://wpfront.com/user-role-editor-pro/faq/">' . $this->__('FAQ') . '</a></p>';
                $s .= '<p><a target="_blank" href="http://wpfront.com/support/">' . $this->__('Support') . '</a></p>';
                $s .= '<p><a target="_blank" href="https://wordpress.org/support/view/plugin-reviews/wpfront-user-role-editor">' . $this->__('Review') . '</a></p>';
                $s .= '<p><a target="_blank" href="http://wpfront.com/contact/">' . $this->__('Contact') . '</a></p>';



                $screen->set_help_sidebar($s);
            }
        }

        protected function add_help_tab() {
            return NULL;
        }

        protected function set_help_sidebar() {
            return NULL;
        }

        public function can_list() {
            return $this->main->current_user_can('list_roles');
        }

        public function can_create() {
            return $this->main->current_user_can('create_roles');
        }

        public function can_edit() {
            return $this->main->current_user_can('edit_roles');
        }

        public function can_delete() {
            return $this->main->current_user_can('delete_roles');
        }

        public function can_assign_roles() {
            return current_user_can('promote_users') && current_user_can('list_users');
        }

        public function __($s) {
            return $this->main->__($s);
        }

        public function image_url() {
            return $this->main->pluginURL() . 'images/';
        }

        protected function footer() {
            $this->main->footer();
        }

        public function list_url() {
            return admin_url('admin.php') . '?page=' . WPFront_User_Role_Editor_List::MENU_SLUG;
        }

        public function add_new_url() {
            return admin_url('admin.php') . '?page=' . WPFront_User_Role_Editor_Add_Edit::MENU_SLUG;
        }

        public function edit_url() {
            return admin_url('admin.php') . '?page=' . WPFront_User_Role_Editor_List::MENU_SLUG . '&edit_role=';
        }

        public function delete_url() {
            return admin_url('admin.php') . '?page=' . WPFront_User_Role_Editor_List::MENU_SLUG . '&delete_role=';
        }

        public function set_default_url() {
            return admin_url('admin.php') . '?page=' . WPFront_User_Role_Editor_List::MENU_SLUG . '&nonce=' . wp_create_nonce($this->list_url()) . '&set_default_role=';
        }

        public function settings_url() {
            return admin_url('admin.php') . '?page=' . WPFront_User_Role_Editor_Options::MENU_SLUG;
        }
        
        public function login_redirect_url() {
            return admin_url('admin.php') . '?page=' . WPFront_User_Role_Editor_Login_Redirect::MENU_SLUG;
        }

        public function cache_add($key, $data) {
            wp_cache_set($key, $data, WPFront_User_Role_Editor::PLUGIN_SLUG);
        }

        public function cache_get($key) {
            $data = wp_cache_get($key, WPFront_User_Role_Editor::PLUGIN_SLUG);
            if ($data === FALSE)
                return NULL;

            return $data;
        }

    }

    add_action('admin_init', 'WPFront_User_Role_Editor_Controller_Base::register_ajax_handlers', 1001);
    wp_cache_add_non_persistent_groups(WPFront_User_Role_Editor::PLUGIN_SLUG);
}