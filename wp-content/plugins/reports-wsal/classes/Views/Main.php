<?php if(!class_exists('WSAL_Rep_Plugin')){ exit('You are not allowed to view this page.'); }

/**
 * Main plugin view
 */
class WSAL_Rep_Views_Main extends WSAL_AbstractView
{
    const REPORT_LIMIT = 100;

    public function __construct(WpSecurityAuditLog $plugin)
    {
        parent::__construct($plugin);
        add_action('admin_notices', array($this, 'WsalAdminNoticesReportingExtensionPlugin'));
        add_action('network_admin_notices', array($this, 'WsalAdminNoticesReportingExtensionPlugin'));
        $this->RegisterNotice('reports-wsal-plugin');

        add_action('wp_ajax_AjaxGenerateNow', array($this, 'AjaxGenerateNow'));
        add_action('wp_ajax_AjaxGenerateReport', array($this, 'AjaxGenerateReport'));
        add_action('wp_ajax_AjaxCheckArchiveMatch', array($this, 'AjaxCheckArchiveMatch'));

        if (!session_id()) {
            @session_start();
        }
    }

    public function WsalAdminNoticesReportingExtensionPlugin()
    {
        if (is_main_site()) {
            $licenseValid = $this->_plugin->licensing->IsLicenseValid('reports-wsal.php');
            $class = $this->_plugin->views->FindByClassName('WSAL_Views_Licensing');
            if (false === $class) {
                $class = new WSAL_Views_Licensing($this->_plugin);
            }
            $licensingPageUrl = esc_attr($class->GetUrl());
            if (!$this->IsNoticeDismissed('reports-wsal-plugin') && !$licenseValid) {
                ?><div class="updated" data-notice-name="reports-wsal-plugin">
                <p><?php _e(sprintf('Remember to <a href="%s">enter your plugin license code</a> for the <strong>Reporting Extension</strong>,
                                to benefit from updates and support.', $licensingPageUrl), 'reports-wsal');?>
                    &nbsp;&nbsp;&nbsp;<a href="javascript:;" class="wsal-dismiss-notification"><?php _e('Dismiss this notice', 'reports-wsal'); ?></a></p>
                </div><?php
            }
        }
    }

    public function GetTitle()
    {
        return __('Reporting', 'reports-wsal');
    }

    public function GetIcon()
    {
        return 'dashicons-admin-generic';
    }

    public function GetName()
    {
        return __('Reporting', 'reports-wsal');
    }

    public function GetWeight()
    {
        return 10;
    }

    public function Header()
    {
        $pluginPath = plugins_url(basename(realpath(dirname(__FILE__).'/../../')));
        wp_enqueue_style('wsal-rep-select2-css', $pluginPath.'/js/select2/select2.css');
        wp_enqueue_style('wsal-rep-select2-bootstrap-css', $pluginPath.'/js/select2/select2-bootstrap.css');
        wp_enqueue_style('wsal-jq-ui-css', $pluginPath.'/js/jquery.datepick/smoothness.datepick.css');
        wp_enqueue_style('wsal-reporting-css', $pluginPath.'/css/styles.css');

        wp_enqueue_script('wsal-jq-datepick-plugin-js', $pluginPath.'/js/jquery.datepick/jquery.plugin.min.js', array('jquery'));
        wp_enqueue_script('wsal-jq-datepick-js', $pluginPath.'/js/jquery.datepick/jquery.datepick.min.js', array('jquery'));
        wp_enqueue_script('wsal-reporting-select2-js', $pluginPath.'/js/select2/select2.min.js', array('jquery'));

        $date_format = $this->_plugin->reporting->common->GetDateFormat();
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

            function checkDate(field) {
                if (dateFormat == 'mm-dd-yyyy' || dateFormat == 'dd-mm-yyyy') {
                    // regular expression to match date format mm-dd-yyyy or dd-mm-yyyy
                    re = /^(\d{1,2})-(\d{1,2})-(\d{4})$/;
                } else {
                    // regular expression to match date format yyyy-mm-dd
                    re = /^(\d{4})-(\d{1,2})-(\d{1,2})$/;
                }
                
                if(field.val() != '' && !field.val().match(re)) {
                    field.val('');
                    return false;
                }
                return true;
            }
        </script><?php
    }

    public function Footer()
    {
        ?><script type="text/javascript">
            jQuery(document).ready(function(){
                // tab handling code
                jQuery('#wsal-tabs>a').click(function(){
                    jQuery('#wsal-tabs>a').removeClass('nav-tab-active');
                    jQuery('div.wsal-tab').hide();
                    jQuery(jQuery(this).addClass('nav-tab-active').attr('href')).show();
                });
                // show relevant tab
                var hashlink = jQuery('#wsal-tabs>a[href="' + location.hash + '"]');
                if (hashlink.length) {
                    hashlink.click();
                } else {
                    jQuery('#wsal-tabs>a:first').click();
                }
            });
        </script>
        <?php
        $notifications = $this->_plugin->reporting->common->GetOptionByName('activity-summary-notifications');
        $notificationsArray = array();
        if (!empty($notifications)) {
            foreach ($notifications->triggers as $key => $value) {
                $aNotificationData = array(
                    "key" => $key,
                    "sites" => (!empty($notifications->sites) ? $notifications->sites : null),
                    "type" => $notifications->type,
                    "frequency" => $notifications->frequency,
                    "alerts" => array()
                );
                if (is_array($value['alert_id'])) {
                    $aNotificationData["alerts"] = $value['alert_id'];
                } else {
                    $aNotificationData["alerts"][] = $value['alert_id'];
                }
                $notificationsArray[] = $aNotificationData;
            }
        }
        ?>
        <script type="text/javascript">

            var addArchive = false;
            var nextDate = null;
            var notificationsData = <?php echo json_encode($notificationsArray)?>;
            var arrayCheck = [];

            function AjaxGenerateNow() 
            {
                for (var n in notificationsData) {
                    AjaxGenerateNotification(notificationsData[n], n);
                    arrayCheck[n] = false;
                }                        
            }

            function AjaxGenerateNotification(notification, n) {
                var limit = <?php echo self::REPORT_LIMIT?>;
                jQuery.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    async: true,
                    dataType: 'text',
                    data: { 
                        action: 'AjaxGenerateNow',
                        key: notification["key"],
                        aAlerts: notification["alerts"],
                        type: notification["type"],
                        frequency: notification["frequency"],
                        sites: notification["sites"],
                        nextDate: nextDate,
                        limit: limit
                    },
                    success: function(result) {
                        nextDate = result;
                        if (nextDate != 0) {
                            var dateString = nextDate;
                            dateString = dateString.split(".");
                            var d = new Date(dateString[0]*1000);
                            jQuery("#ajax-response-counter").html(' Report type: '+n+', last day examined: '+d.toDateString()+' last day.');
                            AjaxGenerateNotification(notification, n);
                        } else {
                            arrayCheck[n] = true;
                            if (jQuery.inArray(false, arrayCheck) == -1) {
                                jQuery("#ajax-response").html("Process completed.");
                            }
                        }
                    },
                    error: function(xhr, textStatus, error) {
                        console.log(xhr.statusText);
                        console.log(textStatus);
                        console.log(error);
                    }
                });
            }

            function AjaxGenerateReport(filters) {
                var limit = <?php echo self::REPORT_LIMIT?>;
                jQuery.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    async: true,
                    dataType: 'json',
                    data: { 
                        action: 'AjaxGenerateReport',
                        filters: filters,
                        nextDate: nextDate,
                        limit: limit,
                        addArchive: addArchive
                    },
                    success: function(response) {
                        nextDate = response[0];
                        if (nextDate != 0) {
                            var dateString = nextDate;
                            dateString = dateString.split(".");
                            var d = new Date(dateString[0]*1000);
                            jQuery("#ajax-response-counter").html(' Last day examined: '+d.toDateString()+' last day.');
                            AjaxGenerateReport(filters);
                        } else {
                            if (response[1] !== null) {
                                jQuery("#ajax-response").html("Process completed.");
                                window.setTimeout(function(){ window.location.href = response[1]; }, 300);
                            } else {
                                jQuery("#ajax-response").html("There are no alerts that match your filtering criteria.");
                            }
                        }
                    },
                    error: function(xhr, textStatus, error) {
                        console.log(xhr.statusText);
                        console.log(textStatus);
                        console.log(error);
                    }
                });
            }

            function AjaxCheckArchiveMatch(filters) {
                jQuery.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    async: false,
                    dataType: 'json',
                    data: { 
                        action: 'AjaxCheckArchiveMatch',
                        filters: filters
                    },
                    success: function(response) {
                        if (response) {
                            var r = confirm('There are alerts in the archive database that match your report criteria.\nShould these alerts be included in the report?');
                            if (r == true) {
                                addArchive = true;
                            } else {
                                addArchive = false;
                            }
                        }
                    }
                });
            }
        </script><?php
    }

    public function AjaxGenerateNow()
    {
        $key = $_POST['key'];
        $aAlerts = $_POST['aAlerts'];
        $type = $_POST['type'];
        $frequency = $_POST['frequency'];
        $sites = (!empty($_POST['sites']) ? $_POST['sites'] : null);
        $nextDate = $_POST['nextDate'];
        $limit = $_POST['limit'];
        if (isset($_SESSION['is_archive_email'.$key])) {
            $this->_plugin->reporting->common->SwitchToArchiveDB();
        }
        $lastDate = $this->_plugin->reporting->common->BuildAttachment($key, $aAlerts, $type, $frequency, $sites, $nextDate, $limit);
        if ($lastDate == null) {
            // Switch to Archive DB
            if ($this->_plugin->reporting->common->GetOptionByName('include-archive')) {
                if (empty($_SESSION['is_archive_email'.$key])) {
                    $this->_plugin->reporting->common->SwitchToArchiveDB();
                    $nextDate = null;
                    $lastDate = $this->_plugin->reporting->common->BuildAttachment($key, $aAlerts, $type, $frequency, $sites, $nextDate, $limit);
                    if (!empty($lastDate)) {
                        $_SESSION['is_archive_email'.$key] = true;
                    }
                } else {
                    unset($_SESSION['is_archive_email'.$key]);
                }
            }
            if ($lastDate == null) {
                $this->_plugin->reporting->common->sendSummaryEmail($key, $aAlerts);
                $this->_plugin->reporting->common->CloseArchiveDB();
            }
        }
        echo $lastDate;
        exit;
    }

    public function AjaxGenerateReport()
    {
        if (isset($_SESSION['is_archive'])) {
            $this->_plugin->reporting->common->SwitchToArchiveDB();
        }
        $filters = $_POST['filters'];
        $filters['nextDate'] = $_POST['nextDate'];
        $filters['limit'] = $_POST['limit'];
        $report = $this->_plugin->reporting->common->GenerateReport($filters, false);
        // append to the JSON file
        $this->_plugin->reporting->common->generateReportJsonFile($report);
        $response[0] = (!empty($report['lastDate']) ? $report['lastDate'] : 0);
        if ($response[0] == null) {
            // Switch to Archive DB
            if (isset($_POST['addArchive']) && $_POST['addArchive'] === "true") {
                if (empty($_SESSION['is_archive'])) {
                    // first time
                    $this->_plugin->reporting->common->SwitchToArchiveDB();
                    $filters['nextDate'] = null;
                    $report = $this->_plugin->reporting->common->GenerateReport($filters, false);
                    // append to the JSON file
                    $this->_plugin->reporting->common->generateReportJsonFile($report);
                    if (!empty($report['lastDate'])) {
                        $_SESSION['is_archive'] = true;
                        $response[0] = $report['lastDate'];
                    }
                } else {
                    // last time
                    unset($_SESSION['is_archive']);
                }
            }
            if ($response[0] == null) {
                $response[1] = $this->_plugin->reporting->common->downloadReportFile();
                $this->_plugin->reporting->common->CloseArchiveDB();
            }
        }
        echo json_encode($response);
        exit;
    }

    public function AjaxCheckArchiveMatch()
    {
        $response = false;
        if ($this->_plugin->reporting->common->IsArchivingEnabled()) {
            $filters = $_POST['filters'];
            $this->_plugin->reporting->common->SwitchToArchiveDB();
            $response = $this->_plugin->reporting->common->IsMatchingReportCriteria($filters);
        }
        echo json_encode($response);
        exit;
    }

    private function createSummaryNotifications($send_now = false)
    {
        $optName = "activity-summary-notifications";
        $data = new stdClass();
        $data->title = "activity-summary-notifications";
        $data->email = trim($_POST['wsal-notif-email']);
        $data->type = $_POST['wsal-summary-type'];
        $data->frequency = $_POST['wsal-frequency'];
        $data->sites = array();
        if (isset($_POST['wsal-sum-sites'])) {
            $rbs = intval($_POST['wsal-sum-sites']);
            if (1 == $rbs) {
                /*[ already implemented in the $filters array ]*/
            } elseif (2 == $rbs) {
                if (isset($_POST['wsal-summary-sites']) && !empty($_POST['wsal-summary-sites'])) {
                    $data->sites = explode(',', $_POST['wsal-summary-sites']);
                }
            }
        }
        $data->owner = get_current_user_id();
        $data->dateAdded = time();
        $data->status = 1;
        $data->viewState = array();
        $data->triggers = array();
        if (isset($_POST['notification_1'])) {
            $data->viewState[] = "notification_1";
            $data->triggers[] = array("alert_id" => 1000);
        }
        if (isset($_POST['notification_2'])) {
            if (isset($_POST['usersType'])) {
                switch ($_POST['usersType']) {
                    case 'known':
                        $data->viewState[] = $_POST['usersType'];
                        $data->triggers[] = array("alert_id" => 1002);
                        break;
                    case 'unknown':
                        $data->viewState[] = $_POST['usersType'];
                        $data->triggers[] = array("alert_id" => 1003);
                        break;
                    default:
                        $data->viewState[] = $_POST['usersType'];
                        $data->triggers[] = array("alert_id" => array(1002, 1003));
                        break;
                }
            }
        }
        if (isset($_POST['notification_3'])) {
            $data->viewState[] = "notification_3";
            $data->triggers[] = array("alert_id" => array(2001, 2005, 2030));
        }
        if (isset($_POST['notification_4'])) {
            $data->viewState[] = "notification_4";
            $data->triggers[] = array("alert_id" => array(4003, 4004));
        }
        if (isset($_POST['notification_5'])) {
            $data->viewState[] = "notification_5";
            $data->triggers[] = array("alert_id" => array(4000, 4012));
        }

        if (count($data->triggers) > 0) {
            $this->_plugin->reporting->common->AddGlobalOption($optName, $data);
            $isSaved = true;
        } else {
            $result = $this->_plugin->reporting->common->DeleteGlobalOption("wsal-".$optName);
            $isSaved = false;
        }

        if (isset($_POST['include-archive'])) {
            $this->_plugin->reporting->common->AddGlobalOption("include-archive", true);
        } else {
            $result = $this->_plugin->reporting->common->DeleteGlobalOption("wsal-include-archive");
        }
        
        $pluginPath = plugins_url(basename(realpath(dirname(__FILE__).'/../../')));

        if ($send_now && $isSaved) { ?>
            <script type="text/javascript">
                jQuery(document).ready(function(){
                    AjaxGenerateNow();
                });
            </script>
            <div class="updated">
                <p id="ajax-response">
                    <img src="<?php echo $pluginPath; ?>/css/loading.gif">
                    <?php _e(' Generating reports and emails. Please do not close this window', 'reports-wsal'); ?>
                    <span id="ajax-response-counter"></span>
                </p>
            </div>
            <?php
        } else {
            if ($isSaved == false) { ?>
                <div class="error"><p><?php _e('Notification could not be saved/generated.', 'reports-wsal'); ?></p></div>
                <?php
            } else { ?>
                <div class="updated"><p><?php _e('Notification successfully saved.', 'reports-wsal'); ?></p></div>
                <?php
            }
        }
    }

    public function Render()
    {
        if (!$this->_plugin->settings->CurrentUserCan('edit')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'reports-wsal'));
        }
        // Verify the uploads directory
        $uploadsDirObj = wp_upload_dir();
        $wpsalRepUploadsDir = trailingslashit($uploadsDirObj['basedir']).'reports/';
        $pluginDir = realpath(dirname(__FILE__).'/../../');

        if ($this->_plugin->reporting->common->CheckDirectory($wpsalRepUploadsDir)) {
            include($pluginDir.'/inc/wsal-reporting-view.inc.php');
        } else {
            if (!wp_mkdir_p($wpsalRepUploadsDir)) { ?>
                <div class="error">
                    <?php
                    echo sprintf(__('The %s directory which the Reports plugin uses to create reports in was either not found or is not accessible.', 'reports-wsal'), 'uploads') . '<br><br>';
                    echo sprintf(__('In order for the plugin to function, the directory %s must be created and the plugin should have ', 'reports-wsal'), $wpsalRepUploadsDir) . '<br>';
                    echo sprintf(__('access to write to this directory, so please configure the following permissions: 0755. If you have any questions or need further assistance please %s', 'reports-wsal'), '<a href="mailto:support@wpwhitesecurity.com">contact us</a>');
                    ?>
                </div>
            <?php
            } else {
                include($pluginDir.'/inc/wsal-reporting-view.inc.php');
            }
        }
    }
}
