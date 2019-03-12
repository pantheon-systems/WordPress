<?php
/**
 * Settings manager
 * 
 * Project: User Role Editor WordPress plugin
 * 
 * Author: Vladimir Garagulya
 * email: support@role-editor.com
 *
**/
class URE_Settings {
    
    protected static function get_action() {

        $action = 'show';
        $update_buttons = array(
            'ure_settings_update', 
            'ure_addons_settings_update', 
            'ure_settings_ms_update', 
            'ure_default_roles_update',
            'ure_settings_tools_exec');
        foreach($update_buttons as $update_button) {
            if (!isset($_POST[$update_button])) {
                continue;
            }
            if (!wp_verify_nonce($_POST['_wpnonce'], 'user-role-editor')) {
                wp_die('Security check failed');
            }
            $action = $update_button;
            break;            
        }

        return $action;

    }
    // end of get_settings_action()

    
    /**
     * Update General Options tab
     */
    protected static function update_general_options() {
        
        $lib = URE_Lib::get_instance();
        if (defined('URE_SHOW_ADMIN_ROLE') && (URE_SHOW_ADMIN_ROLE == 1)) {
            $show_admin_role = 1;
        } else {
            $show_admin_role = $lib->get_request_var('show_admin_role', 'post', 'checkbox');
        }
        $lib->put_option('show_admin_role', $show_admin_role);

        $caps_readable = $lib->get_request_var('caps_readable', 'post', 'checkbox');
        $lib->put_option('ure_caps_readable', $caps_readable);

        $show_deprecated_caps = $lib->get_request_var('show_deprecated_caps', 'post', 'checkbox');
        $lib->put_option('ure_show_deprecated_caps', $show_deprecated_caps);       
        
        $confirm_role_update = $lib->get_request_var('confirm_role_update', 'post', 'checkbox');
        $lib->put_option('ure_confirm_role_update', $confirm_role_update);
        
        $edit_user_caps = $lib->get_request_var('edit_user_caps', 'post', 'checkbox');
        $lib->put_option('edit_user_caps', $edit_user_caps);
        
        $caps_columns_quant = (int) $lib->get_request_var('caps_columns_quant', 'post', 'int');
        $lib->put_option('caps_columns_quant', $caps_columns_quant);       
        
        do_action('ure_settings_update1');

        $lib->flush_options();
        $lib->show_message(esc_html__('User Role Editor options are updated', 'user-role-editor'));
        
    }
    // end of update_general_options()

    
    /**
     * Update Additional Modules Options tab
     */
    protected static function update_addons_options() {
        
        $lib = URE_Lib::get_instance();
        $multisite = $lib->get('multisite');
        if (!$multisite) {
            $count_users_without_role = $lib->get_request_var('count_users_without_role', 'post', 'checkbox');
            $lib->put_option('count_users_without_role', $count_users_without_role);
        }
        do_action('ure_settings_update2');
        
        $lib->flush_options();
        $lib->show_message(esc_html__('User Role Editor options are updated', 'user-role-editor'));
    }
    // end of update_addons_options()
    
    
    protected static function update_default_roles() {
        global $wp_roles;    
        
        $lib = URE_Lib::get_instance();
        
        // Primary default role
        $primary_default_role = $lib->get_request_var('default_user_role', 'post');
        if (!empty($primary_default_role) && isset($wp_roles->role_objects[$primary_default_role]) && $primary_default_role !== 'administrator') {
            update_option('default_role', $primary_default_role);
        }
                
        // Other default roles
        $other_default_roles = array();
        foreach($_POST as $key=>$value) {
            $prefix = substr($key, 0, 8);
            if ($prefix!=='wp_role_') {
                continue;
            }
            $role_id = substr($key, 8);
            if ($role_id!=='administrator' && isset($wp_roles->role_objects[$role_id])) {
                $other_default_roles[] = $role_id;
            }            
        }  // foreach()
        $lib->put_option('other_default_roles', $other_default_roles, true);
        
        $lib->show_message(esc_html__('Default Roles are updated', 'user-role-editor'));
    }
    // end of update_default_roles()
    
    
    protected static function update_multisite_options() {
        
        $lib = URE_Lib::get_instance();
        
        $multisite = $lib->get('multisite');
        if (!$multisite) {
            return;
        }

        $allow_edit_users_to_not_super_admin = $lib->get_request_var('allow_edit_users_to_not_super_admin', 'post', 'checkbox');
        $lib->put_option('allow_edit_users_to_not_super_admin', $allow_edit_users_to_not_super_admin);        
        
        do_action('ure_settings_ms_update');

        $lib->flush_options();
        $lib->show_message(esc_html__('User Role Editor options are updated', 'user-role-editor'));
        
    }
    // end of update_multisite_options()

    
    protected static function tools_exec() {
        
        $lib = URE_Lib::get_instance();
        $roles_reset = $lib->get_request_var( 'ure_reset_roles_exec', 'post', 'int');
        if ( $roles_reset==1 ) {
            URE_Tools::reset_roles();
        } else {        
            do_action( 'ure_settings_tools_exec' );
        }
        
    }
    //end of tools_exec()
    
    
    private static function controller() {
        
        $action = self::get_action();
        switch ($action) {
            case 'ure_settings_update':
                self::update_general_options();
                break;
            case 'ure_addons_settings_update':
                self::update_addons_options();
                break;
            case 'ure_settings_ms_update':
                self::update_multisite_options();
                break;
            case 'ure_default_roles_update':
                self::update_default_roles();
                break;
            case 'ure_settings_tools_exec':
                self::tools_exec();
                break;
            case 'show':
            default:                
            ;
        } // switch()
        
    }
    // end of controller()
    
    
    public static function show_other_default_roles() {
        
        $lib = URE_Lib::get_instance();
        $other_default_roles = $lib->get_option('other_default_roles', array());
        $roles = $lib->get_user_roles();
        $wp_default_role = get_option('default_role');
        foreach ($roles as $role_id => $role) {
            if ( $role_id=='administrator' || $role_id==$wp_default_role ) {
                continue;
            }
            if ( in_array( $role_id, $other_default_roles ) ) {
                $checked = 'checked="checked"';
            } else {
                $checked = '';
            }
            echo '<label for="wp_role_' . $role_id .'"><input type="checkbox"	id="wp_role_' . $role_id . 
                '" name="wp_role_' . $role_id . '" value="' . $role_id . '"' . $checked .' />&nbsp;' . 
                esc_html__( $role['name'], 'user-role-editor' ) . '</label><br />';
          }		
           
    }
    // end of show_other_default_roles()
    
    
    
    public static function get_settings_link() {
        
        $lib = URE_Lib::get_instance();
        $multisite = $lib->get('multisite');
        
        if ($multisite && is_network_admin()) {
            $link = 'settings.php';
        } else {
            $link = 'options-general.php';
        }
        
        return $link;
        
    }
    // end of get_settings_link();

    
    
    public static function show() {                
        
        $lib = URE_Lib::get_instance();
        self::controller();
        
        if (defined('URE_SHOW_ADMIN_ROLE') && (URE_SHOW_ADMIN_ROLE == 1)) {
            $show_admin_role = 1;
        } else {
            $show_admin_role = $lib->get_option('show_admin_role', 0);
        }
        $caps_readable = $lib->get_option('ure_caps_readable', 0);
        $show_deprecated_caps = $lib->get_option('ure_show_deprecated_caps', 0);
        $confirm_role_update = $lib->get_option('ure_confirm_role_update', 1);
        $edit_user_caps = $lib->get_option('edit_user_caps', 1);
        $caps_columns_quant = $lib->get_option('caps_columns_quant', 1);
        $multisite = $lib->get('multisite');
        if ($multisite) {
            $allow_edit_users_to_not_super_admin = $lib->get_option('allow_edit_users_to_not_super_admin', 0);
        } else {
            $count_users_without_role = $lib->get_option('count_users_without_role', 0);
        }
        
        $view = new URE_Role_View();
        $view->role_default_prepare_html(0);
        
        $ure_tab_idx = (int) $lib->get_request_var('ure_tab_idx', 'post', 'int');
                
        do_action('ure_settings_load');        

        $link = self::get_settings_link();        
        $active_for_network = $lib->get('active_for_network');
        $license_key_only = $multisite && is_network_admin() && !$active_for_network;

        
        require_once(URE_PLUGIN_DIR . 'includes/settings-template.php');
    }
    // end of show()
    
}
// end of URE_Settings class