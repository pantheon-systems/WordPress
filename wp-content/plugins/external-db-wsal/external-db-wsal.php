<?php
/*
Plugin Name: External DB for WP Security Audit Log
Plugin URI: http://www.wpsecurityauditlog.com/extensions/external-database-for-wp-security-audit-log/
Description: External DB plugin for WSAL.
Author: WP White Security
Version: 1.2.0
Text Domain: external-db-wsal
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

class WSAL_Ext_Plugin
{
    protected $wsal = null;

    public function __construct()
    {
        add_action('admin_notices', array($this, 'admin_notice'));
        add_action('network_admin_notices', array($this, 'admin_notice'));
        add_action('wsal_init', array($this, 'wsal_init'));
        if (class_exists('WpSecurityAuditLog')) {
            register_deactivation_hook(__FILE__, array($this, 'remove_config'));
        }
        add_filter('cron_schedules', array($this, 'my_add_intervals'));
    }

    public function admin_notice()
    {
        if (is_main_site()) {
            if (!class_exists('WpSecurityAuditLog') && !defined('WSAL_REQUIRED_WARNING')) {
                define('WSAL_REQUIRED_WARNING', true);
                ?><div class="error"><p><?php _e('The <strong>WP Security Audit Log</strong> plugin must be installed and enabled for <strong>External DB for WP Security Audit Log</strong> plugin to function.', 'reports-wsal'); ?></p></div><?php
            }
        }
    }

    public function wsal_init(WpSecurityAuditLog $wsal)
    {
        $wsal->licensing->AddPremiumPlugin(__FILE__);
        $wsal->autoloader->Register('WSAL_Ext_', dirname(__FILE__) . '/classes');
        $wsalCommonClass = new WSAL_Ext_Common($wsal);
        $wsal->wsalCommonClass = $wsalCommonClass;
        $wsal->views->AddFromClass('WSAL_Ext_Settings');
        $this->wsal = $wsal;

        // cron job archiving
        if ($this->wsal->wsalCommonClass->IsArchivingEnabled()) {
            if (!$this->wsal->wsalCommonClass->IsArchivingStop()) {
                add_action('run_archiving', array($this, 'archiving_alerts'));
                $every = strtolower($this->wsal->wsalCommonClass->GetArchivingRunEvery());
                if (!wp_next_scheduled('run_archiving')) {
                    wp_schedule_event(time(), $every, 'run_archiving');
                }
            }
        }

        // cron job mirroring
        if ($this->wsal->wsalCommonClass->IsMirroringEnabled()) {
            if (!$this->wsal->wsalCommonClass->IsMirroringStop()) {
                add_action('run_mirroring', array($this, 'mirroring_alerts'));
                $every = strtolower($this->wsal->wsalCommonClass->GetMirroringRunEvery());
                if (!wp_next_scheduled('run_mirroring')) {
                    wp_schedule_event(time(), $every, 'run_mirroring');
                }
            }
        }
    }

    public function remove_config()
    {
        $wsalCommonClass = $this->wsal->wsalCommonClass;
        $wsalCommonClass->RemoveConfig();
        $wsalCommonClass->RecreateTables();
    }

    /**
     * Archiving alerts
     */
    public function archiving_alerts()
    {
        $this->wsal->wsalCommonClass->archiving_alerts();
    }

    /**
     * Mirroring alerts
     */
    public function mirroring_alerts()
    {
        $this->wsal->wsalCommonClass->mirroring_alerts();
    }

    public function my_add_intervals($schedules) {
        $schedules['fortyfiveminutes'] = array(
            'interval' => 2700,
            'display' => __('Every 45 minutes')
        );
        $schedules['thirtyminutes'] = array(
            'interval' => 1800,
            'display' => __('Every 30 minutes')
        );
        $schedules['tenminutes'] = array(
            'interval' => 600,
            'display' => __('Every 10 minutes')
        );
        $schedules['oneminute'] = array(
            'interval' => 60,
            'display' => __('Every 1 minute')
        );
        return $schedules;
    }
}
return new WSAL_Ext_Plugin();
