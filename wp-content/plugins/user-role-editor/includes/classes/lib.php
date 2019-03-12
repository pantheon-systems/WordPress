<?php
/*
 * Stuff specific for User Role Editor WordPress plugin
 * Author: Vladimir Garagulya
 * Author email: support@role-editor.com
 * Author URI: https://www.role-editor.com
 * 
*/


/**
 * This class contains general stuff for usage at WordPress plugins
 */
class URE_Lib extends URE_Base_Lib {

    const  TRANSIENT_EXPIRATION = 600;

    protected $wp_default_role = '';
    protected $advert = null;
    protected $bbpress = null; // reference to the URE_bbPress class instance
    protected $key_capability = ''; // Key user capability for get full access to the User Role Editor
    protected $settings_capability = ''; // User capability for access to User Role Editor Settings
    
    // when allow_edit_users_to_not_super_admin option is turned ON, we set this property to true 
    // when we raise single site admin permissions up to the superadmin for the 'Add new user' new-user.php page
    // User_Role_Editor::allow_add_user_as_superadmin()
    protected $raised_permissions = false; 
 
    protected $debug = false;
 
  
  
    /** class constructor
     * 
     * @param string $options_id
     * 
     */
    protected function __construct($options_id) {
                                           
        parent::__construct($options_id); 
        
        $this->debug = defined('URE_DEBUG') && (URE_DEBUG==1 || URE_DEBUG==true);
        $this->get_bbpress();        
        $this->upgrade();
        
    }
    // end of __construct()


    public function get_bbpress() {
        
        if ($this->bbpress===null) {
            $this->bbpress = new URE_bbPress();
        }
        
        return $this->bbpress;
        
    }
    // end of get_bbpress()
    
    
    public static function get_instance($options_id = '') {
        
        if (self::$instance === null) {
            if (empty($options_id)) {
                throw new Exception('URE_Lib::get_instance() - Error: plugin options ID string is required');
            }
            // new static() will work too
            self::$instance = new URE_Lib($options_id);
        }

        return self::$instance;
    }
    // end of get_instance()
        
    
    protected function upgrade() {
        
        if (!is_admin()) {
            return;
        }
        
        $ure_version = $this->get_option('ure_version', '0');
        if (version_compare( $ure_version, URE_VERSION, '<' ) ) {
            // put version upgrade stuff here
            
            $this->put_option('ure_version', URE_VERSION, true);
        }
        
    }
    // end of upgrade()
    
    
    /**
     * Is this the Pro version?
     * @return boolean
     */ 
    public function is_pro() {
        
        return false;
    }
    // end of is_pro()    
                
    
    public function set_raised_permissions($value) {
        
        $this->raised_permissions = !empty($value) ? true : false;
        
    }
    // end of set_raised_permissions()
        
        
    /**
     * get options for User Role Editor plugin
     * User Role Editor stores its options at the main blog/site only and applies them to the all network
     * 
     */
    protected function init_options($options_id) {        
        global $wpdb;
        
        if ($this->multisite) { 
            if ( ! function_exists( 'is_plugin_active_for_network' ) ) {    // Be sure the function is defined before trying to use it
                require_once( ABSPATH . '/wp-admin/includes/plugin.php' );                
            }
            $this->active_for_network = is_plugin_active_for_network(URE_PLUGIN_BASE_NAME);
        }
        $current_blog = $wpdb->blogid;
        if ($this->multisite && $current_blog!=$this->main_blog_id) {   
            if ($this->active_for_network) {   // plugin is active for whole network, so get URE options from the main blog
                switch_to_blog($this->main_blog_id);  
            }
        }
        
        $this->options_id = $options_id;
        $this->options = get_option($options_id);
        
        if ($this->multisite && $current_blog!=$this->main_blog_id) {
            if ($this->active_for_network) {   // plugin is active for whole network, so return back to the current blog
                restore_current_blog();
            }
        }

    }
    // end of init_options()
    
    
    /**
     * saves options array into WordPress database wp_options table
     */
    public function flush_options() {
        global $wpdb;
        
        $current_blog = $wpdb->blogid;
        if ($this->multisite && $current_blog!==$this->main_blog_id) {
            if ($this->active_for_network) {   // plugin is active for whole network, so get URE options from the main blog
                switch_to_blog($this->main_blog_id);  // in order to save URE options to the main blog
            }
        }
        
        update_option($this->options_id, $this->options);
        
        if ($this->multisite && $current_blog!==$this->main_blog_id) {            
            if ($this->active_for_network) {   // plugin is active for whole network, so return back to the current blog
                restore_current_blog();
            }
        }
        
    }
    // end of flush_options()
    
    
    public function get_main_blog_id() {
        
        return $this->main_blog_id;
        
    }                  
                                            	                   
    
    /**
     * Checks if user is allowed to use User Role Editor
     * 
     * @param int $user_id
     * @return boolean true 
     */
    public function user_is_admin($user_id = false) {

        $ure_key_capability = URE_Own_Capabilities::get_key_capability();
        if (empty($user_id)) {                    
            $user_id = get_current_user_id();
        }
        $result = user_can($user_id, $ure_key_capability);
        
        return $result;
    }
    // end of user_is_admin()

            
  /**
     * return array with WordPress user roles
     * 
     * @global WP_Roles $wp_roles
     * @global type $wp_user_roles
     * @return array
     */
    public function get_user_roles() {

        $bbpress = $this->get_bbpress();
        if ($bbpress->is_active()) {  // bbPress plugin is active
            $roles = $bbpress->get_roles();
        } else {
            $wp_roles = wp_roles();
            $roles = $wp_roles->roles;
        }        
        
        if (is_array($roles) && count($roles) > 0) {
            asort($roles);
        }

        return $roles;
    }
    // end of get_user_roles()
    
    
    /**
     * Respect 'editable_roles' filter, when needed
     * @return array
     */
    public function get_editable_user_roles($roles) {
                
        if (empty($roles)) {
            $roles = $this->get_user_roles();
        }
        $bbpress = $this->get_bbpress();
        if ($bbpress->is_active()) {
            remove_filter('editable_roles', 'bbp_filter_blog_editable_roles');
        }
        $roles = apply_filters('editable_roles', $roles);
        if ($bbpress->is_active()) {
            add_filter('editable_roles', 'bbp_filter_blog_editable_roles');
        }
        
        return $roles;
    }
    // end of get_editable_user_roles()                 
    
    
    /**
     * return array of built-in WP capabilities (WP 3.1 wp-admin/includes/schema.php) 
     * 
     * @return array 
     */
    public function get_built_in_wp_caps() {
        
        $caps_groups = URE_Capabilities_Groups_Manager::get_instance();                
        $caps = $caps_groups->get_built_in_wp_caps();
        
        return $caps;
    }
    // end of get_built_in_wp_caps()

                
    /**
     * Return all available post types except non-public WordPress built-in post types
     * 
     * @return array
     */
    public function _get_post_types() {
        
        $all_post_types = get_post_types();
        $internal_post_types = get_post_types(array('public'=>false, '_builtin'=>true));
        $post_types = array_diff($all_post_types, $internal_post_types);
                
        return $post_types;
    }
    // end of _get_post_types()
    
    
    public function get_edit_post_capabilities() {
        $capabilities = array(
            'create_posts',
            'edit_posts',
            'edit_published_posts',
            'edit_others_posts',
            'edit_private_posts',
            'publish_posts',
            'read_private_posts',
            'delete_posts',
            'delete_private_posts',
            'delete_published_posts',
            'delete_others_posts'
        );
        
        return $capabilities;
    }
    // end of get_edit_post_capabilities();
                
    
    public function init_full_capabilities( $ure_object ) {
                
        $capabilities = URE_Capabilities::get_instance();
        $full_list = $capabilities->init_full_list( $ure_object );
        
        return $full_list;        
    }
    // end of init_full_capabilities()

    
    public function restore_after_blog_switching($blog_id = 0) {
        
        if (!empty($blog_id)) {
            switch_to_blog($blog_id);
        }
        // cleanup blog switching data
        $GLOBALS['_wp_switched_stack'] = array();
        $GLOBALS['switched'] = ! empty( $GLOBALS['_wp_switched_stack'] );
    }
    // end of restore_after_blog_switching()
    
    
    /**
     * Returns administrator role ID
     * 
     * @return string
     */        
    public function get_admin_role() {

        $roles = $this->get_user_roles();
        if (isset($roles['administrator'])) {
            $admin_role_id = 'administrator';
        } else {        
            // go through all roles and select one with max quant of capabilities included
            $max_caps = -1;
            $admin_role_id = '';
            foreach(array_keys($roles) as $role_id) {
                $caps = count($roles[$role_id]['capabilities']);
                if ($caps>$max_caps) {
                    $max_caps = $caps;
                    $admin_role_id = $role_id;
                }
            }
        }        
        
        return $admin_role_id;
    }
    // end get_admin_role()
            
    
    /**
     * Returns text presentation of user roles
     * 
     * @param type $roles user roles list
     * @return string
     */
    public function roles_text($roles) {
        global $wp_roles;

        if (is_array($roles) && count($roles) > 0) {
            $role_names = array();
            foreach ($roles as $role) {
                if (isset($wp_roles->roles[$role])) {
                    $role_names[] = $wp_roles->roles[$role]['name'];
                } else {
                    $role_names[] = $role;
                }
            }
            $output = implode(', ', $role_names);
        } else {
            $output = '';
        }

        return $output;
    }
    // end of roles_text()
    
    
    public function about() {
        if ($this->is_pro()) {
            return;
        }

?>		  
            <h2>User Role Editor</h2>         
            
            <strong><?php esc_html_e('Version:', 'user-role-editor');?></strong> <?php echo URE_VERSION; ?><br/><br/>
            <a class="ure_rsb_link" style="background-image:url(<?php echo URE_PLUGIN_URL . 'images/vladimir.png'; ?>);" target="_blank" href="http://www.shinephp.com/"><?php _e("Author's website", 'user-role-editor'); ?></a><br/>
            <a class="ure_rsb_link" style="background-image:url(<?php echo URE_PLUGIN_URL . 'images/user-role-editor-icon.png'; ?>);" target="_blank" href="https://www.role-editor.com"><?php _e('Plugin webpage', 'user-role-editor'); ?></a><br/>
            <a class="ure_rsb_link" style="background-image:url(<?php echo URE_PLUGIN_URL . 'images/user-role-editor-icon.png'; ?>);" target="_blank" href="https://www.role-editor.com/download-plugin"><?php _e('Plugin download', 'user-role-editor'); ?></a><br/>
            <a class="ure_rsb_link" style="background-image:url(<?php echo URE_PLUGIN_URL . 'images/changelog-icon.png'; ?>);" target="_blank" href="https://www.role-editor.com/changelog"><?php _e('Changelog', 'user-role-editor'); ?></a><br/>
            <a class="ure_rsb_link" style="background-image:url(<?php echo URE_PLUGIN_URL . 'images/faq-icon.png'; ?>);" target="_blank" href="http://www.shinephp.com/user-role-editor-wordpress-plugin/#faq"><?php _e('FAQ', 'user-role-editor'); ?></a><br/>
            <hr />
                <div style="text-align: center;">
                    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
                        <input type="hidden" name="cmd" value="_s-xclick">
                        <input type="hidden" name="encrypted" 
                               value="-----BEGIN PKCS7-----MIIHZwYJKoZIhvcNAQcEoIIHWDCCB1QCAQExggEwMIIBLAIBADCBlDCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20CAQAwDQYJKoZIhvcNAQEBBQAEgYBME5QAQYFDddWBHA4YXI1x3dYmM77clH5s0CgokYnLVk0P8keOxMtYyNQo6xJs6pY1nJfE3tqNg8CZ3btJjmOUa6DsE+K8Nm6OxGHMQF45z8WAs+f/AvQWdSpPXD0eSMu9osNgmC3yv46hOT3B1J3rKkpeZzMThCdUfECqu+lluzELMAkGBSsOAwIaBQAwgeQGCSqGSIb3DQEHATAUBggqhkiG9w0DBwQIeMSZk/UuZnuAgcAort75TUUbtDhmdTi1N0tR9W75Ypuw5nBw01HkZFsFHoGezoT95c3ZesHAlVprhztPrizl1UzE9COQs+3p62a0o+BlxUolkqUT3AecE9qs9dNshqreSvmC8SOpirOroK3WE7DStUvViBfgoNAPTTyTIAKKX24uNXjfvx1jFGMQGBcFysbb3OTkc/B6OiU2G951U9R8dvotaE1RQu6JwaRgwA3FEY9d/P8M+XdproiC324nzFel5WlZ8vtDnMyuPxOgggOHMIIDgzCCAuygAwIBAgIBADANBgkqhkiG9w0BAQUFADCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20wHhcNMDQwMjEzMTAxMzE1WhcNMzUwMjEzMTAxMzE1WjCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20wgZ8wDQYJKoZIhvcNAQEBBQADgY0AMIGJAoGBAMFHTt38RMxLXJyO2SmS+Ndl72T7oKJ4u4uw+6awntALWh03PewmIJuzbALScsTS4sZoS1fKciBGoh11gIfHzylvkdNe/hJl66/RGqrj5rFb08sAABNTzDTiqqNpJeBsYs/c2aiGozptX2RlnBktH+SUNpAajW724Nv2Wvhif6sFAgMBAAGjge4wgeswHQYDVR0OBBYEFJaffLvGbxe9WT9S1wob7BDWZJRrMIG7BgNVHSMEgbMwgbCAFJaffLvGbxe9WT9S1wob7BDWZJRroYGUpIGRMIGOMQswCQYDVQQGEwJVUzELMAkGA1UECBMCQ0ExFjAUBgNVBAcTDU1vdW50YWluIFZpZXcxFDASBgNVBAoTC1BheVBhbCBJbmMuMRMwEQYDVQQLFApsaXZlX2NlcnRzMREwDwYDVQQDFAhsaXZlX2FwaTEcMBoGCSqGSIb3DQEJARYNcmVAcGF5cGFsLmNvbYIBADAMBgNVHRMEBTADAQH/MA0GCSqGSIb3DQEBBQUAA4GBAIFfOlaagFrl71+jq6OKidbWFSE+Q4FqROvdgIONth+8kSK//Y/4ihuE4Ymvzn5ceE3S/iBSQQMjyvb+s2TWbQYDwcp129OPIbD9epdr4tJOUNiSojw7BHwYRiPh58S1xGlFgHFXwrEBb3dgNbMUa+u4qectsMAXpVHnD9wIyfmHMYIBmjCCAZYCAQEwgZQwgY4xCzAJBgNVBAYTAlVTMQswCQYDVQQIEwJDQTEWMBQGA1UEBxMNTW91bnRhaW4gVmlldzEUMBIGA1UEChMLUGF5UGFsIEluYy4xEzARBgNVBAsUCmxpdmVfY2VydHMxETAPBgNVBAMUCGxpdmVfYXBpMRwwGgYJKoZIhvcNAQkBFg1yZUBwYXlwYWwuY29tAgEAMAkGBSsOAwIaBQCgXTAYBgkqhkiG9w0BCQMxCwYJKoZIhvcNAQcBMBwGCSqGSIb3DQEJBTEPFw0xMTEyMTAwODU3MjdaMCMGCSqGSIb3DQEJBDEWBBSFh6YmkoVtYdMaDd5G6EN0dGcPpzANBgkqhkiG9w0BAQEFAASBgAB91K/+gsmpbKxILdCVXCkiOg1zSG+tfq2EZSNzf8z/R1E3HH8qPdm68OToILsgWohKFwE+RCwcQ0iq77wd0alnWoknvhBBoFC/U0yJ3XmA3Hkgrcu6yhVijY/Odmf6WWcz79/uLGkvBSECbjTY0GLxvhRlsh2nAioCfxAr1cFo-----END PKCS7-----">
                        <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                        <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">                        
                    </form>                        
                </div>
<?php         
    }
    // end of about()
        
    
    public function show_admin_role_allowed() {
        $show_admin_role = $this->get_option('show_admin_role', 0);
        $show_admin_role = ((defined('URE_SHOW_ADMIN_ROLE') && URE_SHOW_ADMIN_ROLE==1) || $show_admin_role==1) && $this->user_is_admin();
        
        return $show_admin_role;
    }
    // end of show_admin_role()                           
    
    
    /**
     * Returns true if user has a real super administrator permissions
     * It takes into account $this->raised_permissions value, in order do not count a user with temporally raised permissions 
     * of a real superadmin under WP Multisite
     * For WP Singlesite superadmin is a user with 'administrator' role only in opposite the WordPress's is_super_admin(),
     * which counts as superadmin any user with 'delete_users' capability
     * 
     * @param int $user_id
     * @return boolean
     */
    public function is_super_admin($user_id = false) {
                
        if (empty($user_id)) {
            $user = wp_get_current_user();
            $user_id = $user->ID;
        } else {
            $user = get_userdata($user_id);
        }
        if (!$user || !$user->exists()) {
            return false;
        }
        
        if ( $this->multisite && !$this->raised_permissions && is_super_admin( $user_id ) ) {
            return true;
        }
        
        if (!$this->multisite && $this->user_has_role( $user, 'administrator' ) ) {
            return true;
        }                
        
        return false;
    }
    // end of is_super_admin()
    
    
    public function user_has_role( $user, $role) {
        
        if (empty($user)) {
            return false;
        }
        
        if (!is_a($user, 'WP_User')) {
            return false;
        }
        
        if (empty($user->roles)) {
            return false;
        }
        
        if (!in_array( $role, $user->roles ) ) {
            return false;
        }
        
        return true;
    }
    // end of user_has_role()
    
        
    // Returns true for any capability if user is a real superadmin under WordPress Multisite
    // Returns true if user has $capability assigned through the roles or directly
    // Returns true if user has role with name equal $cap
    public function user_has_capability($user, $cap) {
        global $wp_roles;

        if (!is_object($user) || !is_a( $user, 'WP_User') || empty($user->ID)) {
            return false;
        }
        
        // Do not replace with $this->is_super_admin() to exclude recursion
        if ($this->multisite && !$this->raised_permissions && is_super_admin($user->ID)) {  
            return true;
        }

        if (isset($user->caps[$cap])) {
            return true;
        }
        foreach ($user->roles as $role) {
            if ($role === $cap) {
                return true;
            }
            if (!empty($wp_roles->roles[$role]['capabilities'][$cap])) {
                return true;
            }
        }

        return false;
    }
    // end of user_has_capability()           

    
    // create assign_role object
    public function get_assign_role() {
        
        $assign_role = new URE_Assign_Role();
        
        return $assign_role;
    }
    // end of get_assign_role()
            

    /**
     * Compare if current URL path is equal to the required one
     * if $path is empty, then just check if URL leads to wp-admin
     * @param string $path
     * @return boolean
     */
    public function is_right_admin_path($path='') {
        $result = true;
        $admin_url = admin_url($path);   
        $parsed = parse_url($admin_url);
        $full_path = $parsed['path'];
        if ( stripos($_SERVER['REQUEST_URI'], $full_path)===false ) {
            $result = false;
        }
        
        return $result;
    }
    // end of is_right_admin_path()

    /*
     * It's overriden in Pro version to add bbPress roles
     */    
    public function get_all_editable_roles() {
        
        $roles = get_editable_roles();  // WordPress roles        
        
        return $roles;
    }
    // end of get_all_roles()
            
}
// end of URE_Lib class