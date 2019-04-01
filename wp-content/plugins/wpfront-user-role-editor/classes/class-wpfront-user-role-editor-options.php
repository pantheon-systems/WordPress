<?php

/*
  WPFront User Role Editor Plugin
  Copyright (C) 2013, WPFront.com
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

require_once(plugin_dir_path(__FILE__) . "entities/class-wpfront-user-role-editor-entity-options.php");

if (!class_exists('WPFront_User_Role_Editor_Options')) {

    /**
     * Options class for WPFront User Role Editor Plugin
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Options extends WPFront_User_Role_Editor_Controller_Base {

        const MENU_SLUG = WPFront_User_Role_Editor::PLUGIN_SLUG;

        function __construct($main) {
            parent::__construct($main);

            $this->ajax_register('wp_ajax_wpfront_user_role_editor_update_options', array($this, 'update_options_callback'));

            add_filter('wpfront_ure_custom_post_type_enable_custom_permission', array($this, 'custom_post_type_enable_custom_permission'), 10, 1);
        }

        public function settings() {
            include($this->main->pluginDIR() . 'templates/options-template.php');
        }

        private function get_custom_post_type_list() {
            $post_types = apply_filters('wpfront_ure_custom_post_type_permission_settings_list', array());
            $customize = $this->customize_permission_custom_post_types();

            foreach ($post_types as $key => $value) {
                $post_types[$key] = (OBJECT) array('label' => $value, 'enabled' => in_array($key, $customize));
            }

            return $post_types;
        }
        
        private function get_extendable_post_types() {
            $types = $this->main->get_extendable_post_types();
            $post_types = get_post_types(NULL, 'objects');
            $disable = $this->disable_extended_permission_post_types();
            
            $names = array();
            foreach ($types as $value) {
                $names[$value] = (OBJECT) array('label' => $post_types[$value]->label, 'enabled' => in_array($value, $disable)); 
            }
            
            return $names;
        }

        public function update_options_callback() {
            check_ajax_referer($_POST['referer'], 'nonce');

            if (isset($_POST['multisite']))
                $this->multisite = TRUE;

            $this->update_option_boolean('display_deprecated');
            $this->update_option_boolean('remove_nonstandard_capabilities_restore');
            $this->update_option_boolean('override_edit_permissions');
            $this->update_option_boolean('disable_navigation_menu_permissions');
            $this->update_option_boolean('override_navigation_menu_permissions');

            if ($this->multisite && wp_is_large_network()) {
                $this->update_option_boolean('enable_large_network_functionalities');
            }
            if ($this->main->enable_multisite_only_options($this->multisite)) {
                $this->update_option_boolean('remove_data_on_uninstall', TRUE);
            }

            if (isset($_POST['custom-post-types'])) {
                $custom_post_types = $_POST['custom-post-types'];
                if (is_array($custom_post_types)) {
                    $post_type_values = $this->customize_permission_custom_post_types();
                    foreach ($custom_post_types as $key => $value) {
                        if ($value === 'true') {
                            if (!in_array($key, $post_type_values))
                                $post_type_values[] = $key;
                        } else {
                            if (in_array($key, $post_type_values))
                                $post_type_values = array_diff($post_type_values, array($key));
                        }
                    }

                    do_action('wpfront_ure_update_customize_permission_custom_post_types', $post_type_values, $this->customize_permission_custom_post_types());
                    $this->update_option('customize_permission_custom_post_types', implode(',', $post_type_values));
                }
            }
            
            if (isset($_POST['extendable-post-types'])) {
                $extendable_post_types = $_POST['extendable-post-types'];
                if (is_array($extendable_post_types)) {
                    $post_type_values = $this->disable_extended_permission_post_types();
                    foreach ($extendable_post_types as $key => $value) {
                        if ($value === 'true') {
                            if (!in_array($key, $post_type_values))
                                $post_type_values[] = $key;
                        } else {
                            if (in_array($key, $post_type_values))
                                $post_type_values = array_diff($post_type_values, array($key));
                        }
                    }

                    $this->update_option('disable_extended_permission_post_types', implode(',', $post_type_values));
                }
            }

            if ($this->multisite)
                echo network_admin_url('admin.php?page=' . self::MENU_SLUG . '&settings-updated=true');
            else
                echo admin_url('admin.php?page=' . self::MENU_SLUG . '&settings-updated=true');
            die();
        }

        public function custom_post_type_enable_custom_permission($post_type) {
            $post_types = apply_filters('wpfront_ure_custom_post_type_permission_settings_list', array());

            if (empty($post_types[$post_type]))
                return FALSE;

            $post_type_values = $this->customize_permission_custom_post_types();
            if (!in_array($post_type, $post_type_values))
                $post_type_values[] = $post_type;

            do_action('wpfront_ure_update_customize_permission_custom_post_types', $post_type_values, $this->customize_permission_custom_post_types());
            $this->update_option('customize_permission_custom_post_types', implode(',', $post_type_values));

            return TRUE;
        }

        private function update_option_boolean($key, $clone = FALSE) {
            if (!empty($_POST[$key])) {
                $value = $_POST[$key];
                if ($value === 'true') {
                    $value = 1;
                } else if ($value === 'false') {
                    $value = 0;
                }

                $this->update_option($key, $value, $clone);
            }
        }

        private function update_option($key, $value, $clone = FALSE) {
            $prefix = '';
            if ($this->multisite) {
                $prefix = 'ms_';
                switch_to_blog($this->ms_options_blog_id());
            }

            $entity = new WPFront_User_Role_Editor_Entity_Options();
            $entity->update_option($prefix . $key, $value);
            if ($clone)
                $entity->update_option($key, $value);

            if ($this->multisite)
                restore_current_blog();
        }

        public function enable_role_capabilities() {
            return TRUE;
        }

        private function get_boolean_option($key, $ms = FALSE) {
            $value = $this->get_option($key, $ms);
            return $value === '1' ? TRUE : FALSE;
        }

        private function get_option($key, $ms = FALSE) {
            $prefix = '';
            if ($ms) {
                $prefix = 'ms_';
                switch_to_blog($this->ms_options_blog_id());
            }

            $entity = new WPFront_User_Role_Editor_Entity_Options();
            $value = $entity->get_option($prefix . $key);
            if ($value === NULL) {
                if ($ms === FALSE && is_multisite()) {
                    return $this->get_option($key, TRUE);
                }
            }

            if ($ms)
                restore_current_blog();

            return $value;
        }

        public function display_deprecated() {
            if ($this->multisite)
                return $this->ms_display_deprecated();

            return $this->get_boolean_option('display_deprecated');
        }

        public function remove_nonstandard_capabilities_restore() {
            if ($this->multisite)
                return $this->ms_remove_nonstandard_capabilities_restore();

            return $this->get_boolean_option('remove_nonstandard_capabilities_restore');
        }

        public function override_edit_permissions() {
            if ($this->multisite)
                return $this->ms_override_edit_permissions();

            return $this->get_boolean_option('override_edit_permissions');
        }

        public function disable_navigation_menu_permissions() {
            if ($this->multisite)
                return $this->ms_disable_navigation_menu_permissions();

            return $this->get_boolean_option('disable_navigation_menu_permissions');
        }
        
        public function override_navigation_menu_permissions() {
            if ($this->multisite)
                return $this->ms_override_navigation_menu_permissions();

            return $this->get_boolean_option('override_navigation_menu_permissions');
        }

        public function remove_data_on_uninstall() {
            if ($this->multisite)
                return $this->ms_remove_data_on_uninstall();

            return $this->get_boolean_option('remove_data_on_uninstall');
        }

        public function customize_permission_custom_post_types() {
            $value = $this->get_option('customize_permission_custom_post_types');
            if ($value === NULL || $value === '')
                return array();

            return explode(',', $value);
        }
        
        public function disable_extended_permission_post_types() {
            $value = $this->get_option('disable_extended_permission_post_types');
            if ($value === NULL || $value === '')
                return array();

            return explode(',', $value);
        }

        public static function get_ms_options_blog_id() {
            if (!is_multisite()) {
                throw new Exception('Invalid call');
            }

            $blog_id = 1;
            if (defined('BLOG_ID_CURRENT_SITE')) {
                $blog_id = BLOG_ID_CURRENT_SITE;
            }
            return $blog_id;
        }

        public function ms_options_blog_id() {
            return self::get_ms_options_blog_id();
        }

        public function ms_display_deprecated() {
            return $this->get_boolean_option('display_deprecated', TRUE);
        }

        public function ms_remove_nonstandard_capabilities_restore() {
            return $this->get_boolean_option('remove_nonstandard_capabilities_restore', TRUE);
        }

        public function ms_enable_large_network_functionalities() {
            return $this->get_boolean_option('enable_large_network_functionalities', TRUE);
        }

        public function ms_override_edit_permissions() {
            return $this->get_boolean_option('override_edit_permissions', TRUE);
        }

        public function ms_disable_navigation_menu_permissions() {
            return $this->get_boolean_option('disable_navigation_menu_permissions', TRUE);
        }
        
        public function ms_override_navigation_menu_permissions() {
            return $this->get_boolean_option('override_navigation_menu_permissions', TRUE);
        }

        public function ms_remove_data_on_uninstall() {
            return $this->get_boolean_option('remove_data_on_uninstall', TRUE);
        }

        protected function add_help_tab() {
            if ($this->multisite) {
                return array(
                    array(
                        'id' => 'overview',
                        'title' => $this->__('Overview'),
                        'content' => '<p>'
                        . $this->__('These settings are applicable for the network admin dashboard. Also these settings will propagate to the individual sites unless its overridden within that site’s settings.')
                        . '</p>'
                    ),
                    array(
                        'id' => 'settings',
                        'title' => $this->__('Settings'),
                        'content' => '<p><strong>'
                        . $this->__('Enable Large Network Functionalities')
                        . '</strong>: '
                        . $this->__('This setting is only visible when you have a large network.')
                        . ' <a href="http://wpfront.com/user-role-editor-pro/multisite-settings/" target="_blank">'
                        . $this->__('More details')
                        . '</a>'
                        . '</p>'
                        . '<p><strong>'
                        . $this->__('Display Deprecated Capabilities')
                        . '</strong>: '
                        . $this->__('If enabled, deprecated capabilities will be displayed within the add/edit screens.')
                        . '</p>'
                        . '<p><strong>'
                        . $this->__('Remove Non-Standard Capabilities on Restore')
                        . '</strong>: '
                        . $this->__('If enabled, while restoring WordPress built-in capabilities non-standard capabilities will be removed.')
                        . '</p>'
                        . '<p><strong>'
                        . $this->__('Override Edit Permissions')
                        . '</strong>: '
                        . $this->__('If enabled, ignores the check to the function get_editable_roles.')
                        . '</p>'
                        . '<p><strong>'
                        . $this->__('Disable Navigation Menu Permissions')
                        . '</strong>: '
                        . $this->__('If enabled, disables navigation menu permissions functionality.')
                        . '</p>'
                        . '<p><strong>'
                        . $this->__('Override Navigation Menu Permissions')
                        . '</strong>: '
                        . $this->__('If enabled, tries to reset navigation menu permissions UI.')
                        . '</p>'
                        . '<p><strong>'
                        . $this->__('Remove Data on Uninstall')
                        . '</strong>: '
                        . $this->__('If enabled, removes all data related to this plugin from database (except roles data) including license information if any. This will not deactivate the license automatically.')
                        . '</p>'
                    )
                );
            } else {
                return array(
                    array(
                        'id' => 'overview',
                        'title' => $this->__('Overview'),
                        'content' => '<p><strong>'
                        . $this->__('Display Deprecated Capabilities')
                        . '</strong>: '
                        . $this->__('If enabled, deprecated capabilities will be displayed within the add/edit screens.')
                        . '</p>'
                        . '<p><strong>'
                        . $this->__('Remove Non-Standard Capabilities on Restore')
                        . '</strong>: '
                        . $this->__('If enabled, while restoring WordPress built-in capabilities non-standard capabilities will be removed.')
                        . '</p>'
                        . '<p><strong>'
                        . $this->__('Override Edit Permissions')
                        . '</strong>: '
                        . $this->__('If enabled, ignores the check to the function get_editable_roles.')
                        . '</p>'
                        . '<p><strong>'
                        . $this->__('Disable Navigation Menu Permissions')
                        . '</strong>: '
                        . $this->__('If enabled, disables navigation menu permissions functionality.')
                        . '</p>'
                        . '<p><strong>'
                        . $this->__('Override Navigation Menu Permissions')
                        . '</strong>: '
                        . $this->__('If enabled, tries to reset navigation menu permissions UI.')
                        . '</p>'
                        . '<p><strong>'
                        . $this->__('Remove Data on Uninstall')
                        . '</strong>: '
                        . $this->__('If enabled, removes all data related to this plugin from database (except roles data) including license information if any. This will not deactivate the license automatically.')
                        . '</p>'
                    )
                );
            }
        }

        protected function set_help_sidebar() {
            if ($this->multisite) {
                return array(
                    array(
                        $this->__('Documentation on Multisite Settings'),
                        'multisite-settings/'
                    )
                );
            } else {
                return array(
                    array(
                        $this->__('Documentation on Settings'),
                        'settings/'
                    )
                );
            }
        }

    }

}