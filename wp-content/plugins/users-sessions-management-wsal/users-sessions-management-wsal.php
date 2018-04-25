<?php
/*
Plugin Name: Users Sessions Management for WP Security Audit Log
Plugin URI: http://www.wpsecurityauditlog.com/extensions/user-sessions-management-wp-security-audit-log/
Description: 
Author: WP White Security
Version: 1.0.3
Text Domain: users-sessions-management-wsal
Author URI: http://www.wpwhitesecurity.com/
License: GPL2

    WP Security Audit Log
    Copyright(c) 2016  Robert Abela  (email : robert@wpwhitesecurity.com)

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

class WSAL_User_Management_Plugin
{
    protected $wsal = null;

    public function __construct()
    {
        add_action('admin_notices', array($this, 'admin_notice'));
        add_action('network_admin_notices', array($this, 'admin_notice'));
        add_action('wsal_init', array($this, 'wsal_init'));
        // listen for installation event
        register_activation_hook(__FILE__, array($this, 'destroy_on_activation'));
        add_filter('authenticate', array($this, 'prevent_concurrent_logins'), 100, 3);
        add_action('wp_login', array($this, 'notify_multi_sessions'), 10, 2);
    }

    public function admin_notice()
    {
        if (is_main_site()) {
            if (!class_exists('WpSecurityAuditLog') && !defined('WSAL_REQUIRED_WARNING')) {
                define('WSAL_REQUIRED_WARNING', true);
                ?><div class="error"><p><?php _e('The <strong>WP Security Audit Log</strong> plugin must be installed and enabled for the <strong>Users Sessions and Management Add On</strong> to work.', 'users-sessions-management-wsal'); ?></p></div><?php
            }
        }
    }

    public function wsal_init(WpSecurityAuditLog $wsal)
    {
        $wsal->licensing->AddPremiumPlugin(__FILE__);
        $wsal->autoloader->Register('WSAL_User_Management_', dirname(__FILE__) . '/classes');

        $wsal->usermanagement = new stdClass();
        $wsal->usermanagement->common = new WSAL_User_Management_Common($wsal);
        $wsal->views->AddFromClass('WSAL_User_Management_Views');
        $this->wsal = $wsal;

        // cron job Destroy expired sessions
        add_action('destroy_expired', array($this,'destroy_sessions_expired'));
        if (!wp_next_scheduled('destroy_expired')) {
            wp_schedule_event(time(), 'hourly', 'destroy_expired');
        }
    }

    /**
     * Only allow one session per user
     */
    public function prevent_concurrent_logins($current_user, $username, $password)
    {
         $is_allow_multiple = $this->wsal->usermanagement->common->GetOptionByName('user-management-allow-multi-sessions');
        if ($is_allow_multiple) {
            $users = $this->wsal->usermanagement->common->GetUsersWithSessions()->get_results();
            foreach ($users as $key => $user) {
                if (!empty($current_user->ID) && $current_user->ID == $user->ID) {
                    $is_user_known = $this->wsal->usermanagement->common->CheckLoggedInCookie($current_user->user_login);
                    if (!$is_user_known) {
                        $result = $this->wsal->usermanagement->common->GetBlocked();
                        // to send email blocked user
                        if (!empty($result)) {
                            $this->wsal->usermanagement->common->AlertByEmail('blocked', $result, $user->data);
                        }
                        do_action('wp_login_blocked', $username);
                        $msg = __('<strong>ERROR</strong>: Your session was blocked with the <a href="https://en-gb.wordpress.org/plugins/wp-security-audit-log" target="_blank">WP Security Audit Log plugin</a> because there is already another user logged in with the same username. Please contact the site administrator for more information.');
                        return new WP_Error('login_denied', $msg);
                    } else {
                        return $current_user;
                    }
                }
            }
        }
        return $current_user;
    }

    /**
     * Notify Multi Sessions
     */
    public function notify_multi_sessions($user_login, $current_user = null)
    {
        $count = 0;
        if (empty($current_user)) {
            $current_user = get_user_by('login', $user_login);
        }

        if (!empty($current_user->ID)) {
            $this->wsal->usermanagement->common->setCustomCookie($current_user->ID);
            $count = $this->wsal->usermanagement->common->CountSessionsByUser($current_user->ID);
        }

        $is_allow_multiple = $this->wsal->usermanagement->common->GetOptionByName('user-management-allow-multi-sessions');
        if (!$is_allow_multiple) {
            $result = $this->wsal->usermanagement->common->GetMultiSessions();
            // to send email multiple sessions
            if (!empty($result)) {
                $users = $this->wsal->usermanagement->common->GetUsersWithSessions()->get_results();
                foreach ($users as $key => $user) {
                    if (!empty($current_user->ID) && $current_user->ID == $user->ID) {
                        if ($count > 1) {
                            $this->wsal->usermanagement->common->AlertByEmail('multiple', $result, $user->data);
                        }
                    }
                }
            }
        }
        if ($count > 1) {
            $userRoles = $this->wsal->settings->GetCurrentUserRoles($current_user->roles);

            if ($this->wsal->settings->IsLoginSuperAdmin($current_user->user_login)) {
                $userRoles[] = 'superadmin';
            }
            $ip_addresses = $this->wsal->usermanagement->common->GetSessionIPs($current_user->ID);
            $this->wsal->alerts->Trigger(1005, array(
                'Username' => $current_user->user_login,
                'CurrentUserRoles' => $userRoles,
                'IPAddress' => $ip_addresses
            ), true);
        }
    }

    /**
     * Destroy expired sessions
     */
    public function destroy_sessions_expired()
    {
        $sessions = $this->wsal->usermanagement->common->GetAllSessions();
        if (!empty($sessions)) {
            foreach ($sessions as $session) {
                if ($session['expiration'] < time()) {
                    $this->wsal->usermanagement->common->DestroyUserSession($session['user_id'], $session['token_hash']);
                }
            }
        }
    }

    /**
     * Destroy on activation
     */
    public function destroy_on_activation()
    {
        if (class_exists('WpSecurityAuditLog')) {
            $wsal = WpSecurityAuditLog::GetInstance();
            $wsal->autoloader->Register('WSAL_User_Management_', dirname(__FILE__) . '/classes');

            $wsal->usermanagement = new stdClass();
            $wsal->usermanagement->common = new WSAL_User_Management_Common($wsal);
            $this->wsal = $wsal;
            $this->destroy_sessions_expired();
        }
    }
}
return new WSAL_User_Management_Plugin();
