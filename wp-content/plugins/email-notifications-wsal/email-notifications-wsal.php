<?php
/*
Plugin Name: Email Notifications for WP Security Audit Log
Plugin URI: http://www.wpwhitesecurity.com/wordpress-security-plugins/wp-security-audit-log/
Description: Configure notification triggers to be alerted via email of important changes on your WordPress website.
Author: WP White Security
Version: 2.1.2
Text Domain: email-notifications-wsal
Author URI: http://www.wpwhitesecurity.com/
License: GPL2

    WP Security Audit Log
    Copyright(c) 2014  Robert Abela  (email : robert@wpwhitesecurity.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Holds the option prefix
 */
define('WSAL_OPT_PREFIX', 'notification-');
/**
 * Holds the maximum number of notifications a user is allowed to add
 */
define('WSAL_MAX_NOTIFICATIONS', 50);
/*
 * Holds the name of the cache key if cache available
 */
define('WSAL_CACHE_KEY', '__NOTIF_CACHE__');
/*
 * Debugging true|false
 */
define('WSAL_DEBUG_NOTIFICATIONS', false);

class WSAL_NP_Plugin
{
    protected $wsal = null;
    public function __construct()
    {
        add_action('admin_notices', array($this, 'admin_notice'));
        add_action('network_admin_notices', array($this, 'admin_notice'));
        add_action('wsal_init', array($this, 'wsal_init'));
        // Listen for activation event
        register_activation_hook(__FILE__, array($this, 'wizard_plugin_activate'));
        add_action('admin_init', array($this, 'wizard_plugin_redirect'));
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'add_action_links'));
    }
    public function admin_notice()
    {
        if (is_main_site()) {
            if (!class_exists('WpSecurityAuditLog') && !defined('WSAL_REQUIRED_WARNING')) {
                define('WSAL_REQUIRED_WARNING', true);
                ?><div class="error"><p><?php _e('The <strong>WP Security Audit Log</strong> plugin must be installed and enabled for the <strong>Email Notifications Add On</strong> to function.', 'email-notifications-wsal'); ?></p></div><?php
            }
        }
    }
    public function wsal_init(WpSecurityAuditLog $wsal)
    {
        $wsal->licensing->AddPremiumPlugin(__FILE__);
        $wsal->autoloader->Register('WSAL_NP_', dirname(__FILE__) . '/classes');
        $wsalCommon = new WSAL_NP_Common($wsal);
        $wsal->wsalCommon = $wsalCommon;
        $wsal->views->AddFromClass('WSAL_NP_Notifications');
        $c = new WSAL_NP_Wizard($wsal);

        if (isset($_REQUEST['page'])) {
            $a = new WSAL_NP_AddNotification($wsal);
            $b = new WSAL_NP_EditNotification($wsal);
            $addNotifPageName = $a->GetSafeViewName();
            $editNotifPageName = $b->GetSafeViewName();
            $wizardfPageName = $c->GetSafeViewName();

            switch ($_REQUEST['page']) {
                case $addNotifPageName:
                    $wsal->views->AddFromClass('WSAL_NP_AddNotification');
                    break;
                case $editNotifPageName:
                    $wsal->views->AddFromClass('WSAL_NP_EditNotification');
                    break;
                case $wizardfPageName:
                    $wsal->views->AddFromClass('WSAL_NP_Wizard');
                    break;
            }
        }

        $wsal->alerts->AddFromClass('WSAL_NP_Notifier');
        $this->wsal = $wsal;
    }

    public function wizard_plugin_activate()
    {
        add_option('wizard_plugin_do_activation_redirect', true);
    }

    public function wizard_plugin_redirect()
    {
        if (get_option('wizard_plugin_do_activation_redirect', false)) {
            delete_option('wizard_plugin_do_activation_redirect');
            $wsal = WpSecurityAuditLog::GetInstance();
            $wsal->licensing->AddPremiumPlugin(__FILE__);
            $wsal->autoloader->Register('WSAL_NP_', dirname(__FILE__) . '/classes');
            $wsal->views->AddFromClass('WSAL_NP_Wizard');
            $wizard = new WSAL_NP_Wizard($wsal);
            //wp_enqueue_script('jquery.modal-js', $pluginPath.'/js/jquery.modal/jquery.modal.js', array('jquery'));
            //wp_enqueue_style('jquery.modal-css', $pluginPath.'/js/jquery.modal/jquery.modal.css');
            ?>
            <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css" rel="stylesheet" />
            <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
            <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
            <style type="text/css">
            .modal-content {
                border-radius: 0;
            }
            .modal-footer {
                padding-top: 0;
                border-top: none;
            }
            .btn {
                border-radius: 0px;
            }
            </style>
            <div class="modal fade" id="msg_modal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Email Notifications for WP Security Audit Log</h4>
                        </div>
                        <div class="modal-body">
                            <p>Do you want to launch the wizard to configure your first email notification alerts? If you select no you can launch it later or configure the email alerts manually.</p>
                        </div>
                        <div class="modal-footer">
                            <a href="<?php echo esc_attr($wizard->GetUrl() . '&first-time=1'); ?>" class="btn btn-primary">Launch Wizard</a>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">No thank you</button>
                        </div>
                    </div>
                </div>
            </div>
            <script type="text/javascript">
                jQuery('#msg_modal').modal('show');
            </script>  
            <?php
            //exit(wp_redirect($wizard->GetUrl() . '&first-time=1'));
        }
    }

    public function add_action_links($links)
    {
        $new_links = array(
            '<a href="' . admin_url('admin.php?page=wsal-np-notifications') . '">Configure Email Alerts</a>'
        );
        return array_merge($new_links, $links);
    }
}
return new WSAL_NP_Plugin();
