<?php
/*
Plugin Name: Reports for WP Security Audit Log
Plugin URI: http://www.wpsecurityauditlog.com/extensions/compliance-reports-add-on-for-wordpress/
Description: Generate reports to meet legal and regulatory compliance requirements, and keep track of users' productivity.
Author: WP White Security
Version: 2.0.7
Text Domain: reports-wsal
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
define('WSAL_CACHE_KEY_2', '__NOTIF_CACHE__');

class WSAL_Rep_Plugin
{
    protected $wsal = null;

    public function __construct(){
        add_action('admin_notices', array($this, 'admin_notice'));
        add_action('network_admin_notices', array($this, 'admin_notice'));
        add_action('wsal_init', array($this, 'wsal_init'));
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'add_action_links'));
    }
    public function admin_notice()
    {
        if (is_main_site()) {
            if(!class_exists('WpSecurityAuditLog') && !defined('WSAL_REQUIRED_WARNING')){
                define('WSAL_REQUIRED_WARNING', true);
                ?><div class="error"><p><?php _e('The <strong>WP Security Audit Log</strong> plugin must be installed and enabled for <strong>Reports for WP Security Audit Log</strong> plugin to function.', 'reports-wsal'); ?></p></div><?php
            }
        }
    }
    public function wsal_init(WpSecurityAuditLog $wsal)
    {
        $wsal->licensing->AddPremiumPlugin(__FILE__);
        $wsal->autoloader->Register('WSAL_Rep_', dirname(__FILE__) . '/classes');
        $wsal->reporting = new stdClass();
        $wsal->reporting->common = new WSAL_Rep_Common($wsal);
        $wsal->views->AddFromClass('WSAL_Rep_Views_Main');
        $wsal->repPlugin = $this;
    }

    public function add_action_links($links)
    {
        $new_links = array(
            '<a href="' . admin_url('admin.php?page=wsal-rep-views-main') . '">Generate Report</a>',
            '<a href="' . admin_url('admin.php?page=wsal-rep-views-main#tab-summary') . '">Email Summary Reports</a>'
        );
        return array_merge($new_links, $links);
    }

}
return new WSAL_Rep_Plugin();
