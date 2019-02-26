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

if (!class_exists('WPFront_User_Role_Editor_Widget_Permissions')) {

    /**
     * Widget Permissions Controller
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2015 WPFront.com
     */
    class WPFront_User_Role_Editor_Widget_Permissions extends WPFront_User_Role_Editor_Controller_Base {

        protected static $ALL_USERS = 1;
        protected static $LOGGEDIN_USERS = 2;
        protected static $GUEST_USERS = 3;
        protected static $ROLE_USERS = 4;
        protected static $META_DATA_KEY = 'wpfront-user-role-editor-widget-permissions-data';

        public function __construct($main) {
            parent::__construct($main);

            add_action('in_widget_form', array($this, 'in_widget_form'), 10, 3);
            add_filter('widget_update_callback', array($this, 'widget_update_callback'), 10, 4);
            add_filter('widget_display_callback', array($this, 'widget_display_callback'), 10, 3);

            add_action('wp_widget_permissions_custom_fields_roles_list', array($this, 'widget_permissions_custom_fields_roles_list'), 10, 3);

            add_action('admin_print_scripts-widgets.php', array($this, 'enqueue_widget_scripts'));
            add_action('admin_print_styles-widgets.php', array($this, 'enqueue_widget_styles'));
        }

        protected function get_meta_data($instance) {
            if (empty($instance) || empty($instance[self::$META_DATA_KEY])) {
                $data = (OBJECT) array('type' => self::$ALL_USERS);
            } else {
                $data = $instance[self::$META_DATA_KEY];
            }

            $data->type = intval($data->type);

            switch ($data->type) {
                case self::$ALL_USERS:
                case self::$LOGGEDIN_USERS:
                case self::$GUEST_USERS:
                case self::$ROLE_USERS:
                    break;

                default:
                    $data->type = self::$ALL_USERS;
                    break;
            }

            return $data;
        }

        public function in_widget_form($widget, $return, $instance) {
            if (!current_user_can('edit_widget_permissions'))
                return;

            $data = $this->get_meta_data($instance);
            ?>
            <p>
                <label><?php echo $this->__('User Restrictions'); ?></label>
                <span class="user-restriction-container">
                    <label><input class="user-restriction-type" type="radio" name="<?php echo $widget->get_field_name('user-restriction-type'); ?>" value="<?php echo self::$ALL_USERS; ?>" <?php echo $data->type === self::$ALL_USERS ? 'checked' : ''; ?> /><?php echo $this->__('All Users'); ?></label>
                    <label><input class="user-restriction-type" type="radio" name="<?php echo $widget->get_field_name('user-restriction-type'); ?>" value="<?php echo self::$LOGGEDIN_USERS; ?>" <?php echo $data->type === self::$LOGGEDIN_USERS ? 'checked' : ''; ?> /><?php echo $this->__('Logged in Users'); ?></label>
                    <label><input class="user-restriction-type" type="radio" name="<?php echo $widget->get_field_name('user-restriction-type'); ?>" value="<?php echo self::$GUEST_USERS; ?>" <?php echo $data->type === self::$GUEST_USERS ? 'checked' : ''; ?> /><?php echo $this->__('Guest Users'); ?></label>
                    <label><input class="user-restriction-type" type="radio" name="<?php echo $widget->get_field_name('user-restriction-type'); ?>" value="<?php echo self::$ROLE_USERS; ?>" <?php echo $data->type === self::$ROLE_USERS ? 'checked' : ''; ?> /><?php echo $this->__('Users by Role'); ?></label>
                    <span class="roles-container <?php echo $data->type === self::$ROLE_USERS ? '' : 'hidden'; ?>">
                        <?php do_action('wp_widget_permissions_custom_fields_roles_list', $widget, $return, $instance); ?>
                    </span>
                </span>
            </p>
            <?php
        }

        public function widget_update_callback($instance, $new_instance, $old_instance, $widget) {
            if (!current_user_can('edit_widget_permissions')) {
                if (empty($old_instance[self::$META_DATA_KEY]))
                    $instance[self::$META_DATA_KEY] = (OBJECT) array('type' => self::$ALL_USERS);
                else
                    $instance[self::$META_DATA_KEY] = $old_instance[self::$META_DATA_KEY];
                return $instance;
            }

            if (empty($new_instance['user-restriction-type'])) {
                $instance[self::$META_DATA_KEY] = (OBJECT) array('type' => self::$ALL_USERS);
            } else {
                $instance[self::$META_DATA_KEY] = (OBJECT) array('type' => intval($new_instance['user-restriction-type']));
            }
            return $instance;
        }

        public function widget_display_callback($instance, $widget, $args) {
            $data = $this->get_meta_data($instance);

            switch ($data->type) {
                case self::$LOGGEDIN_USERS:
                    if (is_user_logged_in())
                        return $instance;

                    return FALSE;

                case self::$GUEST_USERS:
                    if (!is_user_logged_in())
                        return $instance;

                    return FALSE;

                default:
                    break;
            }

            return $instance;
        }

        public function widget_permissions_custom_fields_roles_list($widget, $return, $instance) {
            printf($this->__('%s to limit based on roles.'), '<a target="_blank" href="https://wpfront.com/widgets">' . $this->__('Upgrade to Pro') . '</a>');
        }

        public function enqueue_widget_scripts() {
            wp_enqueue_script('jquery');
            wp_enqueue_script('wpfront-user-role-editor-widget-permissions-js', $this->main->pluginURL() . 'js/widget-permissions.js', array('jquery'), WPFront_User_Role_Editor::VERSION);
        }

        public function enqueue_widget_styles() {
            wp_enqueue_style('wpfront-user-role-editor-widget-permissions-css', $this->main->pluginURL() . 'css/widget-permissions.css', array(), WPFront_User_Role_Editor::VERSION);
        }

        public static function uninstall() {
            global $wp_registered_widgets;

            foreach ($wp_registered_widgets as $widget) {
                $index = -1;
                if (isset($widget['params'][0]['number']))
                    $index = $widget['params'][0]['number'];

                if ($index > -1) {
                    $option_name = $widget['callback'][0]->option_name;
                    $option = get_option($option_name);
                    if (isset($option[$index][self::$META_DATA_KEY])) {
                        unset($option[$index][self::$META_DATA_KEY]);
                        update_option($option_name, $option);
                    }
                }
            }
        }

    }

}