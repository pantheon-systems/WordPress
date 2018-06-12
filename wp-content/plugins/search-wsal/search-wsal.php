<?php
/*
Plugin Name: Search for WP Security Audit Log
Plugin URI: http://www.wpsecurityauditlog.com/extensions/search-add-on-for-wordpress-security-audit-log/
Description: Automatically search for WordPress Security Alerts in the WordPress Audit Log using free-text based searches and filters.
Author: WP White Security
Version: 1.1.5
Text Domain: search-wsal
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
class WSAL_SearchExtension
{
    // <editor-fold desc="Properties & Constants">
    
    /**
     * @var WpSecurityAuditLog
     */
    public $wsal;
    
    /**
     * @var WSAL_AS_FilterManager
     */
    public $filters;

    /**
     * @var WSAL_Views_AuditLog
     */
    public $viewNotice;
    
    /**
     * @var WSAL_SearchExtension
     */
    protected static $instance;
    
    const CLS_AUDIT_LOG = 'WSAL_Views_AuditLog';
    
    // </editor-fold>
    
    // <editor-fold desc="Entry Points & Hooks">
    
    public function __construct()
    {
        add_action('admin_notices', array($this, 'admin_notice'));
        add_action('network_admin_notices', array($this, 'admin_notice'));
        add_action('wsal_init', array($this, 'wsal_init'));
        add_filter('wsal_auditlog_query', array($this, 'wsal_auditlog_query'));
        add_action('wsal_auditlog_before_view', array($this, 'wsal_auditlog_before_view'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        add_action('admin_footer', array($this, 'admin_footer'));
        add_action('admin_print_footer_scripts', array($this, 'admin_print_footer_scripts'));
        add_action('wp_ajax_WsalAsWidgetAjax', array($this, 'admin_ajax_widget'));
        self::$instance = $this;
    }
    
    public static function GetInstance()
    {
        return self::$instance;
    }
    
    public function admin_notice()
    {
        if (is_main_site()) {
            if (!class_exists('WpSecurityAuditLog') && !defined('WSAL_REQUIRED_WARNING')) {
                define('WSAL_REQUIRED_WARNING', true);
                ?><div class="error"><p><?php _e('<b>WP Security Audit Log</b> plugin must be installed and enabled for the <strong>Search Add On</strong> to function.', 'wsal-search-extension'); ?></p></div><?php
            } else {
                if (!empty($this->wsal)) {
                    $this->viewNotice = new WSAL_Views_AuditLog($this->wsal);
                    $this->viewNotice->RegisterNotice('search-wsal-plugin');
                    $licenseValid = $this->wsal->licensing->IsLicenseValid('search-wsal.php');
                    $class = $this->wsal->views->FindByClassName('WSAL_Views_Licensing');
                    if (false === $class) {
                        $class = new WSAL_Views_Licensing(self::$instance);
                    }
                    $licensingPageUrl = esc_attr($class->GetUrl());
                    if (!$this->viewNotice->IsNoticeDismissed('search-wsal-plugin') && !$licenseValid) {
                        ?><div class="updated" data-notice-name="search-wsal-plugin">
                        <p><?php _e(sprintf('Remember to <a href="%s">enter your plugin license code</a> for the <strong>Search Extension</strong>,
                                        to benefit from updates and support.', $licensingPageUrl), 'wsal-search-extension');?>
                            &nbsp;&nbsp;&nbsp;<a href="javascript:;" class="wsal-dismiss-notification"><?php _e('Dismiss this notice', 'wsal-search-extension'); ?></a></p>
                        </div><?php
                    }
                }
            }
        }
    }
    
    public function wsal_init(WpSecurityAuditLog $wsal)
    {
        // keep a reference to plugin
        $this->wsal = $wsal;
        // register this plugin as premium
        $wsal->licensing->AddPremiumPlugin(__FILE__);
        // register classes with autoloader
        $wsal->autoloader->Register('WSAL_AS_', dirname(__FILE__) . '/classes');
        // load filters
        $this->filters = new WSAL_AS_FilterManager(self::$instance);
    }
    
    public function wsal_auditlog_query($query)
    {
        $modified = false;
        
        // handle text search
        if (isset($_REQUEST['s']) && trim($_REQUEST['s'])) {
            // handle free text search
            $query->addSearchCondition(trim($_REQUEST['s']));
        } else {
            // fixes #4 (@see WP_List_Table::search_box)
            $_REQUEST['s'] = ' ';
        }
        
        // handle filter search
        if (isset($_REQUEST['Filters']) && is_array($_REQUEST['Filters'])) {
            $modified = true;
            foreach ($_REQUEST['Filters'] as $filter) {
                $filter = explode(':', $filter, 2);
                if (isset($filter[1]) && ($the_filter = $this->filters->FindFilterByPrefix($filter[0]))) {
                    $the_filter->ModifyQuery($query, $filter[0], $filter[1]);
                }
            }
        }
        
        // keep track of what we're doing
        if ($modified) {
            $this->wsal->alerts->Trigger(0003, array(
                'Message' => 'User searched in AuditLog.',
                //'Query SQL' => $query->GetSql(),
                //'Query Args' => $query->GetArgs(),
            ));
        }
        
        return $query;
    }
    
    public function wsal_auditlog_before_view(WSAL_AuditLogListView $listview)
    {
        $listview->search_box('search', 'wsal-as-search');
        
        ?><div id="wsal-as-filter-fields" style="display: none;"><?php
            foreach ($this->filters->GetFilters() as $filter) $filter->Render();
        ?></div><?php
        
        if (isset($_REQUEST['Filters']) && is_array($_REQUEST['Filters'])) {
            ?><script type="text/javascript">
                jQuery(document).ready(function(){
                    window.WsalAs.Attach(function(){
                        WsalAs.ClearFilters();
                        <?php foreach ($_REQUEST['Filters'] as $filter) { ?>
                            WsalAs.AddFilter(<?php echo json_encode($filter); ?>);
                        <?php } ?>
                    });
                });
            </script><?php
        }
    }
    
    public function admin_enqueue_scripts()
    {
        if ($this->IsAuditLogPage()) {
            $pluginsUrl = plugins_url('', __FILE__) . '/resources/';
            wp_enqueue_style(
                'auditlog-as',
                $pluginsUrl . 'auditlog.css',
                array(),
                filemtime(plugin_dir_path(__FILE__) . '/resources/auditlog.css')
            );

            wp_enqueue_style('wsal-datepick-css', $pluginsUrl . 'jquery.datepick/smoothness.datepick.css');
            wp_enqueue_script('wsal-datepick-plugin-js', $pluginsUrl.'jquery.datepick/jquery.plugin.min.js', array('jquery'));
            wp_enqueue_script('wsal-datepick-js', $pluginsUrl.'jquery.datepick/jquery.datepick.min.js', array('jquery'));
            
            foreach ($this->filters->GetWidgets() as $widgets) {
                $widgets[0]->StaHeader();
                foreach ($widgets as $widget) $widget->DynHeader();
            }

            $date_format = $this->GetDateFormat();
            ?><script type="text/javascript">
                var dateFormat = "<?php echo $date_format; ?>";

                function wsal_CreateDatePicker($, $input, date) {
                    $input.val(''); // clear
                    var WsalDatePick_onSelect = function(date){
                        date = date || new Date();
                        var v = $.datepick.formatDate(dateFormat, date[0]);
                        $input.val(v);
                        $(this).change();
                    };
                    $input.datepick({
                        dateFormat: dateFormat,
                        selectDefaultDate: true,
                        rangeSelect: false,
                        multiSelect: 0,
                        onSelect: WsalDatePick_onSelect
                    }).datepick('setDate', date);
                }

                function checkDate(value) {
                    if (dateFormat == 'mm-dd-yyyy' || dateFormat == 'dd-mm-yyyy') {
                        // regular expression to match date format mm-dd-yyyy or dd-mm-yyyy
                        re = /^(\d{1,2})-(\d{1,2})-(\d{4})$/;
                    } else {
                        // regular expression to match date format yyyy-mm-dd
                        re = /^(\d{4})-(\d{1,2})-(\d{1,2})$/;
                    }
                    
                    if(value != '' && !value.match(re)) {
                        return false;
                    }
                    return true;
                }
            </script><?php
        }
    }
    
    public function admin_footer()
    {
        if ($this->IsAuditLogPage()) {
            wp_enqueue_script(
                'typeahead-bundle',
                plugins_url('', __FILE__) . '/resources/typeahead.bundle.min.js',
                array('jquery'),
                filemtime(plugin_dir_path(__FILE__) . '/resources/typeahead.bundle.min.js')
            );
            wp_enqueue_script(
                'auditlog-as',
                plugins_url('', __FILE__) . '/resources/auditlog.js',
                array('typeahead-bundle', 'auditlog'),
                filemtime(plugin_dir_path(__FILE__) . '/resources/auditlog.js')
            );
        }
    }
    
    public function admin_print_footer_scripts()
    {
        if ($this->IsAuditLogPage()) {
            foreach ($this->filters->GetWidgets() as $widgets) {
                $widgets[0]->StaFooter();
                foreach ($widgets as $widget) $widget->DynFooter();
            }
        }
    }
    
    public function admin_ajax_widget(){
        try {
            if (!isset($_REQUEST['filter'])) throw new Exception('Parameter "filter" is required.');
            if (!isset($_REQUEST['widget'])) throw new Exception('Parameter "widget" is required.');
            if (!$this->wsal) throw new Exception('Ajax handler "' . __FUNCTION__ . '" was called too early.');

            $widget = $this->filters->FindWidget($_REQUEST['filter'], $_REQUEST['widget']);
            
            if (!$widget) throw new Exception('Widget could not be found.');
            
            $widget->HandleAjax();
            die;
        } catch (Exception $ex) {
            die(json_encode((object)array(
                'mesg' => $ex->getMessage(),
                'line' => $ex->getLine(),
                'file' => basename($ex->getFile()),
            )));
        }
    }
    
    // </editor-fold>
    
    // <editor-fold desc="Utility Methods">
    
    protected function IsAuditLogPage()
    {
        return $this->wsal != null                              // is wsal set up?
            && !!($view = $this->wsal->views->GetActiveView())  // is there an active view?
            && get_class($view) === self::CLS_AUDIT_LOG         // is the view AuditLog?
        ;
    }

    /**
     * Date Format from WordPress General Settings.
     * Used in the form help text.
     */
    public function GetDateFormat()
    {
        $date_format = $this->wsal->settings->GetDateFormat();
        $search = array('Y', 'm', 'd');
        $replace = array('yyyy', 'mm', 'dd');
        return str_replace($search, $replace, $date_format);
    }
    
    // </editor-fold>
}

return new WSAL_SearchExtension();
