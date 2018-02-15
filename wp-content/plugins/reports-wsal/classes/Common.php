<?php if(!class_exists('WSAL_Rep_Plugin')){ exit('You are not allowed to view this page.'); }
/**
 * Class WSAL_Rep_Common
 * Provides utility methods to generate reports
 */
class WSAL_Rep_Common
{
    const REPORT_HTML = 0;
    const REPORT_CSV = 1;
    const REPORT_WEEKLY = 0;
    const REPORT_MONTHLY = 1;

    protected $wsal = null;
    protected $ko = null;
    protected $km = null;

    protected $_gmt_offset_sec = 0;
    protected $_datetimeFormat = null;
    protected $_dateFormat = null;
    protected $_timeFormat = null;
    //@see CheckDirectory()
    protected $_uploadsDirPath = null;

    protected $_attachments = null;
    //@internal
    //@desc holds the alert groups
    private $_catAlertGroups = array();

    private static $_iswpmu = false;

    /**
     * Frequency montly date
     * For testing change date here [01 to 31]
     */
    private static $_monthly_day = '01';
    /**
     * Frequency weekly date
     * For testing change date here [1 (for Monday) through 7 (for Sunday)]
     */
    private static $_weekly_day = 1;
    /**
     * Schedule hook name
     * For testing change the name
     */
    private static $_schedule_hook = 'summary_email_reports';

    public function __construct(WpSecurityAuditLog $wsal)
    {
        @ini_set('max_execution_time', '300');

        $this->wsal = $wsal;
        $this->ko = new WSAL_Rep_Util_O();
        $this->km = new WSAL_Rep_Util_M();

        // Get DateTime Format from WordPress General Settings
        $this->_datetimeFormat = $this->wsal->settings->GetDatetimeFormat(false);
        $this->_dateFormat = $this->wsal->settings->GetDateFormat();
        $this->_timeFormat = $this->wsal->settings->GetTimeFormat();

        $timezone = $this->wsal->settings->GetTimezone();
        if ($timezone) {
            $this->_gmt_offset_sec = get_option('gmt_offset') * (60*60);
        } else {
            $this->_gmt_offset_sec = date('Z');
        }

        self::$_iswpmu = $this->wsal->IsMultisite();
        // cron job wordpress
        add_action(self::$_schedule_hook, array($this,'cronJob'));
        if (!wp_next_scheduled(self::$_schedule_hook)) {
            wp_schedule_event(time(), 'hourly', self::$_schedule_hook);
        }
        // cron job Reports Directory Pruning
        add_action('reports_pruning', array($this,'reportsPruning'));
        if (!wp_next_scheduled('reports_pruning')) {
            wp_schedule_event(time(), 'daily', 'reports_pruning');
        }
    }

    public function AddGlobalOption($option, $value){
        $this->DeleteCacheNotif();
        $this->wsal->SetGlobalOption($option, $value);
    }

    public function DeleteGlobalOption($option){
        $this->DeleteCacheNotif();
        return $this->wsal->DeleteByName($option);
    }

    public function GetOptionByName($option){
        return $this->wsal->GetGlobalOption($option);
    }

    public function DeleteCacheNotif(){
        if (function_exists('wp_cache_delete')) {
            wp_cache_delete(WSAL_CACHE_KEY_2);
        }
    }

    // array ('role_name_lower' => 'role_name_as_in_wp')
    public function GetRoles(){
        global $wp_roles;
        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }
        return $wp_roles->get_names();
    }

    /**
     * Date Format from WordPress General Settings.
     * Used in the form help text.
     */
    public function GetDateFormat() {
        $search = array('Y', 'm', 'd');
        $replace = array('yyyy', 'mm', 'dd');
        return str_replace($search, $replace, $this->_dateFormat);
    }

    /**
     * @param int|null $limit Maximum number of sites to return (null = no limit).
     * @return object Object with keys: blog_id, blogname, domain
     */
    final public static function GetSites($limit = null){
        global $wpdb;
        if (self::$_iswpmu) {
            $sql = 'SELECT blog_id, domain FROM ' . $wpdb->blogs;
            if (!is_null($limit)) $sql .= ' LIMIT ' . $limit;
            $res = $wpdb->get_results($sql);
            foreach ($res as $row) {
                $row->blogname = get_blog_option($row->blog_id, 'blogname');
            }
        } else {
            $res = new stdClass();
            $res->blog_id = get_current_blog_id();
            $res->blogname = esc_html(get_bloginfo('name'));
            $res = array($res);
        }
        return $res;
    }

    /**
     * Retrieve the information about the current blog
     * @return mixed
     */
    final public static function GetCurrentBlogInfo(){
        global $wpdb;
        $blogId = get_current_blog_id();
        $t = new stdClass();
        $t->blog_id = $blogId;
        $t->blogname = get_blog_option($blogId, 'blogname');
        $t->domain = $wpdb->get_var("SELECT domain FROM " . $wpdb->blogs.' WHERE blog_id='.$blogId);
        return $t;
    }

    /**
     * @param int|null $limit Maximum number of sites to return (null = no limit).
     */
    final public static function GetUsers($limit = null){
        global $wpdb;
        $t = $wpdb->users;
        $sql = "SELECT ID, user_login FROM {$t}";
        if (!is_null($limit)) $sql .= ' LIMIT ' . $limit;
        return $wpdb->get_results($sql);
    }


    final public function GetAlertCodes(){
        $data = $this->wsal->alerts->GetAlerts();
        $keys = array();
        if (!empty($data)) {
            $keys = array_keys($data);
            $keys = array_map(array($this,'PadKey'), $keys);
        }
        return $keys;
    }

    /**
     * @internal
     * @param string $key The key to pad
     * @return string
     */
    final public function PadKey($key){
        if (strlen($key) == 1) {
            $key = str_pad($key, 4, '0', STR_PAD_LEFT);
        }
        return $key;
    }

    /**
     * Check to see whether or not the specified directory is accessible
     * @param string $dirPath
     * @return bool
     */
    final public function CheckDirectory($dirPath){
        if (!is_dir($dirPath)) {
            return false;
        }
        if (!is_readable($dirPath)) {
            return false;
        }
        if (!is_writable($dirPath)) {
            return false;
        }
        // Create the index.php file if not already there
        $this->CreateIndexFile($dirPath);
        $this->_uploadsDirPath = $dirPath;
        return true;
    }

    /**
     * Create an index.php file, if none exists, in order to avoid directory listing in the specified directory
     * @param string $dirPath
     * @return bool
     */
    final public function CreateIndexFile($dirPath){
        // check if index.php file exists
        $dirPath = trailingslashit($dirPath);
        $result = 0;
        if (!is_file($dirPath.'index.php')) {
            $result = @file_put_contents($dirPath.'index.php', '<?php /*[WP Security Audit Log Reporter plugin: This file was auto-generated to prevent directory listing ]*/ exit;');
        }
        return ($result>0);
    }

    final public function meta_formatter($name, $value)
    {
        switch (true) {
            case $name == '%Message%':
                return esc_html($value);

            case $name == '%RevisionLink%':
                if (!empty($value) && $value != 'NULL') {
                    return esc_html(' Navigate to this URL to view the changes '.$value);
                } else {
                    return "";
                }

            case $name == '%CommentLink%':
            case $name == '%CommentMsg%':
                return strip_tags($value);

            case in_array($name, array('%MetaValue%', '%MetaValueOld%', '%MetaValueNew%')):
                return (
                strlen($value) > 50 ? (esc_html(substr($value, 0, 50)) . '&hellip;') :  esc_html($value)
                );

            case $name == '%RevisionLink%':
                return ' Browse this URL to view the changes: ' . esc_html($value);

            case $name == '%EditorLinkPost%':
                return '<br>View the post: ' . esc_html($value);

            case $name == '%EditorLinkPage%':
                return '<br>View the page: ' . esc_html($value);

            case $name == '%CategoryLink%':
                return '<br>View the category: ' . esc_html($value);

            case $name == '%EditorLinkForum%':
                return '<br>View the forum: ' . esc_html($value);
                
            case $name == '%EditorLinkTopic%':
                return '<br>View the topic: ' . esc_html($value);

            case $name == '%LinkFile%':
                return '<br>To view the requests open the log file '.esc_url($value);
                
            case strncmp($value, 'http://', 7) === 0:
            case strncmp($value, 'https://', 7) === 0:
                return esc_html($value);

            default:
                return esc_html($value);
        }
    }

    private $_errors = array();

    private function _addError($error){ array_push($this->_errors, $error);}
    final public function HasErrors(){ return (!empty($this->_errors)); }
    final public function GetErrors(){ return $this->_errors; }

    final public static function GetIPAddresses($limit = null)
    {
        $tmp = new WSAL_Models_Meta();
        $ips = $tmp->getAdapter()->GetMatchingIPs($limit);
        return $ips;
    }
    
    private function _getAlertDetails($entryId, $alertId, $siteId, $createdOn, $userId=null, $roles=null, $ip='', $ua='')
    {
        // must be a new instance every time, otherwise the alert message is not retrieved properly
        $this->ko = new WSAL_Rep_Util_O();
        //#! Get alert details
        $code = $this->wsal->alerts->GetAlert($alertId);
        $code = $code ? $code->code : 0;
        $const = (object)array('name' => 'E_UNKNOWN', 'value' => 0, 'description' => __('Unknown error code.', 'reports-wsal'));
        $const = $this->wsal->constants->GetConstantBy('value', $code, $const);

        // Blog details
        if ($this->wsal->IsMultisite()) {
            $blogInfo = get_blog_details($siteId, true);
            $blogName =  __('Unknown Site', 'reports-wsal');
            $blogUrl = '';
            if ($blogInfo) {
                $blogName = esc_html($blogInfo->blogname);
                $blogUrl = esc_attr($blogInfo->siteurl);
            }
        } else {
            $blogName = get_bloginfo('name');
            $blogUrl = '';
            if (empty($blogName)) {
                $blogName =  __('Unknown Site', 'reports-wsal');
            } else {
                $blogName = esc_html($blogName);
                $blogUrl = esc_attr(get_bloginfo('url'));
            }
        }

        // Get the alert message - properly
        $this->ko->id = $entryId;
        $this->ko->site_id = $siteId;
        $this->ko->alert_id = $alertId;
        $this->ko->created_on = $createdOn;
        if ($this->ko->is_migrated) {
            $this->ko->_cachedmessage = $this->ko->GetMetaValue('MigratedMesg', false);
        }
        if (!$this->ko->is_migrated || !$this->ko->_cachedmessage) {
            $this->ko->_cachedmessage = $this->ko->GetAlert()->mesg;
        }

        if (empty($userId)) {
            $username = __('System', 'reports-wsal');
            $role = '';
        } else {
            $user = new WP_User($userId);
            $username = $user->user_login;
            $role = (is_array($roles) ? implode(', ', $roles) : $roles);
        }
        if (empty($role)) {
            $role = '';
        }

        // Meta details
        $out = array(
            'blog_name' => $blogName,
            'blog_url' => $blogUrl,
            'alert_id' => $alertId,
            'date' => str_replace(
                '$$$',
                substr(number_format(fmod($createdOn + $this->_gmt_offset_sec, 1), 3), 2),
                date($this->_datetimeFormat, $createdOn + $this->_gmt_offset_sec)
            ),
            'code' => $const->name,
            // fill variables in message
            'message' => $this->ko->GetAlert()->GetMessage($this->ko->GetMetaArray(), array($this, 'meta_formatter'), $this->ko->_cachedmessage),
            'user_id' => $userId,
            'user_name' => $username,
            'role' => $role,
            'user_ip' => $ip,
            'user_agent' => $ua
        );
        return $out;
    }

    public function GenerateReport(array $filters, $validate = true)
    {
        //region >>> FILTERS VALIDATION
        if ($validate) {
            if (! isset($filters['sites'])) {
                $this->_addError(sprintf(__('Internal error. <code>%s</code> key was not found.', 'reports-wsal'), 'sites'));
                return false;
            }
            if (! isset($filters['users'])) {
                $this->_addError(sprintf(__('Internal error. <code>%s</code> key was not found.', 'reports-wsal'), 'users'));
                return false;
            }
            if (! isset($filters['roles'])) {
                $this->_addError(sprintf(__('Internal error. <code>%s</code> key was not found.', 'reports-wsal'), 'roles'));
                return false;
            }
            if (! isset($filters['ip-addresses'])) {
                $this->_addError(sprintf(__('Internal error. <code>%s</code> key was not found.', 'reports-wsal'), 'ip-addresses'));
                return false;
            }
            if (! isset($filters['alert_codes'])) {
                $this->_addError(sprintf(__('Internal error. <code>%s</code> key was not found.', 'reports-wsal'), 'alert_codes'));
                return false;
            }
                if (! isset($filters['alert_codes']['groups'])) {
                    $this->_addError(sprintf(__('Internal error. <code>%s</code> key was not found.', 'reports-wsal'), 'alert_codes["groups"]'));
                    return false;
                }
                if (! isset($filters['alert_codes']['alerts'])) {
                    $this->_addError(sprintf(__('Internal error. <code>%s</code> key was not found.', 'reports-wsal'), 'alert_codes["alerts"]'));
                    return false;
                }
            if (! isset($filters['date_range'])) {
                $this->_addError(sprintf(__('Internal error. <code>%s</code> key was not found.', 'reports-wsal'), 'date_range'));
                return false;
            }
                if (! isset($filters['date_range']['start'])) {
                    $this->_addError(sprintf(__('Internal error. <code>%s</code> key was not found.', 'reports-wsal'), 'date_range["start"]'));
                    return false;
                }
                if (! isset($filters['date_range']['end'])) {
                    $this->_addError(sprintf(__('Internal error. <code>%s</code> key was not found.', 'reports-wsal'), 'date_range["end"]'));
                    return false;
                }
            if (! isset($filters['report_format'])) {
                $this->_addError(sprintf(__('Internal error. <code>%s</code> key was not found.', 'reports-wsal'), 'report_format'));
                return false;
            }
        }
        //endregion >>> FILTERS VALIDATION

        // Filters
        $sites = (empty($filters['sites']) ? null : $filters['sites']);
        $users = (empty($filters['users']) ? null : $filters['users']);
        $roles = (empty($filters['roles']) ? null : $filters['roles']);
        $ipAddresses = (empty($filters['ip-addresses']) ? null : $filters['ip-addresses']);
        $alertGroups = (empty($filters['alert_codes']['groups']) ? null : $filters['alert_codes']['groups']);
        $alertCodes = (empty($filters['alert_codes']['alerts']) ? null : $filters['alert_codes']['alerts']);
        $dateStart = (empty($filters['date_range']['start']) ? null : $filters['date_range']['start']);
        $dateEnd = (empty($filters['date_range']['end']) ? null : $filters['date_range']['end']);
        $reportFormat = (empty($filters['report_format']) ? self::REPORT_HTML : self::REPORT_CSV);

        $_nextDate = (empty($filters['nextDate']) ? null : $filters['nextDate']);
        $_limit = (empty($filters['limit']) ? 0 : $filters['limit']);

        if (empty($alertGroups) && empty($alertCodes)) {
            $this->_addError(__('Please specify at least one Alert Group or specify an Alert Code.', 'reports-wsal'));
            return false;
        }

        if ($reportFormat <> self::REPORT_CSV && $reportFormat <> self::REPORT_HTML) {
            $this->_addError(__('Internal Error: Could not detect the type of the report to generate.', 'reports-wsal'));
            return false;
        }

        // Alert Groups
        $_codes = $this->GetCodesByGroups($alertGroups, $alertCodes);
        if (!$_codes) {
            return false;
        }

        /*
        -- @userId: COMMA-SEPARATED-LIST wordpress user id
        -- @siteId: COMMA-SEPARATED-LIST wordpress site id
        -- @roleName: REGEXP (must be quoted from PHP)
        -- @alertCode: COMMA-SEPARATED-LIST of numeric alert codes
        -- @startTimestamp: UNIX_TIMESTAMP
        -- @endTimestamp: UNIX_TIMESTAMP
         */
        /*Usage:
        --------------------------
        set @siteId = null; -- '1,2,3,4....';
        set @userId = null;
        set @roleName = null; -- '(administrator)|(editor)';
        set @alertCode = null; -- '1000,1002';
        set @startTimestamp = null;
        set @endTimestamp = null;
         */
        $_siteId = $sites ? "'".implode(',', $sites)."'" : 'null';
        $_userId = $users ? "'".implode(',', $users)."'" : 'null';
        $_roleName = 'null';
        if ($roles) {
            $_roleName = array();
            foreach ($roles as $k => $role) {
                array_push($_roleName, esc_sql('('.preg_quote($role).')'));
            }
            $_roleName = "'".implode('|', $_roleName)."'";
        }

        $_alertCode = "'".implode(',', $_codes)."'";

        $_startTimestamp = 'null';
        $_endTimestamp = 'null';

        if ($dateStart) {
            $dt = new DateTime();
            $df = $dt->createFromFormat($this->_dateFormat . " H:i:s", $dateStart . " 00:00:00");
            $_startTimestamp = $df->format('U');
        }
        if ($dateEnd) {
            $dt = new DateTime();
            $df = $dt->createFromFormat($this->_dateFormat . " H:i:s", $dateEnd . " 23:59:59");
            $_endTimestamp = $df->format('U');
        }

        $lastDate = null;
        $results = $this->wsal->getConnector()->getAdapter("Occurrence")->GetReporting($_siteId, $_userId, $_roleName, $_alertCode, $_startTimestamp, $_endTimestamp, $_nextDate, $_limit);

        if (!empty($results['lastDate'])) {
            $lastDate = $results['lastDate'];
            unset($results['lastDate']);
        }

        if (empty($results)) {
            $this->_addError(__('There are no alerts that match your filtering criteria. Please try a different set of rules.', 'reports-wsal'));
            return false;
        }

        $data = array();
        $dataAndFilters = array();
    
        //#! Get Alert details
        foreach ($results as $i => $entry) {
            $ip = esc_html($entry->ip);
            $ua = esc_html($entry->ua);
            $roles = maybe_unserialize($entry->roles);

            if ($entry->alert_id == '9999') {
                continue;
            }
            if (is_string($roles)) {
                $roles = str_replace(array("\"", "[", "]"), " ", $roles);
            }
            $t = $this->_getAlertDetails($entry->id, $entry->alert_id, $entry->site_id, $entry->created_on, $entry->user_id, $roles, $ip, $ua);
            if (!empty($ipAddresses)) {
                if (in_array($entry->ip, $ipAddresses)) {
                    array_push($data, $t);
                }
            } else {
                array_push($data, $t);
            }
        }

        if (empty($data)) {
            $this->_addError(__('There are no alerts that match your filtering criteria. Please try a different set of rules.', 'reports-wsal'));
            return false;
        }
        $dataAndFilters['data'] = $data;
        $dataAndFilters['filters'] = $filters;
        $dataAndFilters['lastDate'] = $lastDate;

        return $dataAndFilters;
    }

    private function FileGenerator($data, $filters)
    {
        $reportFormat = (empty($filters['report_format']) ? self::REPORT_HTML : self::REPORT_CSV);
        if ($reportFormat == self::REPORT_HTML) {
            $htmlReport = new WSAL_Rep_HtmlReportGenerator($this->_dateFormat, $this->_gmt_offset_sec);

            if (isset($filters['alert_codes']['alerts'])) {
                if (count($filters['alert_codes']['alerts']) == 1) {
                    $criteria = $this->GetCriteria($filters['alert_codes']['alerts'][0]);
                } else {
                    $criteria = $this->GetCriteria($filters['alert_codes']['alerts']);
                }
                if (!empty($criteria)) {
                    unset($filters['alert_codes']['alerts']);
                    $filters['alert_codes']['alerts'][0] = $criteria;
                }
            }

            $result = $htmlReport->Generate($data, $filters, $this->_uploadsDirPath, $this->_catAlertGroups);
            if ($result === 0) {
                $this->_addError(__('There are no alerts that match your filtering criteria. Please try a different set of rules.', 'reports-wsal'));
                $result = false;
            } elseif ($result === 1) {
                $this->_addError(sprintf(__('Error: The <strong>%s</strong> path is not accessible.', 'reports-wsal'), $this->_uploadsDirPath));
                $result = false;
            }
            return $result;
        }

        $csvReport = new WSAL_Rep_CsvReportGenerator($this->_dateFormat . " " . $this->_timeFormat);
        $result = $csvReport->Generate($data, $this->_uploadsDirPath);
        if ($result === 0) {
            $this->_addError(__('There are no alerts that match your filtering criteria. Please try a different set of rules.', 'reports-wsal'));
            $result = false;
        } elseif ($result === 1) {
            $this->_addError(sprintf(__('Error: The <strong>%s</strong> path is not accessible.', 'reports-wsal'), $this->_uploadsDirPath));
            $result = false;
        }
        return $result;
    }

    public function reportsPruning()
    {
        $uploadsDirObj = wp_upload_dir();
        $wpsalRepUploadsDir = trailingslashit($uploadsDirObj['basedir']).'reports/';
        if (file_exists($wpsalRepUploadsDir)) {
            if ($handle = opendir($wpsalRepUploadsDir)) {
                while (false !== ($entry = readdir($handle))) {
                    if ($entry != "." && $entry != "..") {
                        $aFileName = explode('_', $entry);
                        if (!empty($aFileName[2])) {
                            if ($aFileName[2] <= date("mdYHis", strtotime("-1 week"))) {
                                @unlink($wpsalRepUploadsDir.'/'.$entry);
                            }
                        }
                    }
                }
                closedir($handle);
            }
        }
    }

    private function checkCronJobDate($frequency)
    {
        if ($frequency == self::REPORT_WEEKLY) {
            $send = (date('w') == self::$_weekly_day) ? true : false;
        } else {
            $str_date = date('Y-m-').self::$_monthly_day;
            $send = (date('Y-m-d') == $str_date) ? true : false;
        }
        return $send;
    }

    public function cronJob()
    {
        $limit = 100;
        $notifications = $this->GetOptionByName('activity-summary-notifications');
        if (!empty($notifications)) {
            $sites = $notifications->sites;
            $type = $notifications->type;
            $frequency = $notifications->frequency;
            $send = $this->checkCronJobDate($frequency);
            if ($send) {
                if (!empty($notifications)) {
                    foreach ($notifications->triggers as $key => $value) {
                        $nextDate = null;
                        $aAlerts = array();
                        $isSwitched = false;
                        if (is_array($value['alert_id'])) {
                            $aAlerts = $value['alert_id'];
                        } else {
                            $aAlerts[] = $value['alert_id'];
                        }

                        do {
                            if ($isSwitched) {
                                $this->SwitchToArchiveDB();
                            }
                            $nextDate = $this->BuildAttachment($key, $aAlerts, $type, $frequency, $sites, $nextDate, $limit);
                            if ($nextDate == null) {
                                // Switch to Archive DB
                                if ($this->GetOptionByName('include-archive')) {
                                    if (!$isSwitched) {
                                        $this->SwitchToArchiveDB();
                                        $nextDate = $this->BuildAttachment($key, $aAlerts, $type, $frequency, $sites, null, $limit);
                                        $isSwitched = true;
                                    }
                                }
                            }
                            $lastDate = $nextDate;
                        } while ($lastDate != null);

                        if ($isSwitched) {
                            $this->CloseArchiveDB();
                        }

                        if ($lastDate == null) {
                            $this->sendSummaryEmail($key, $aAlerts);
                        }
                    }
                }
            }
        }
    }

    public function sendSummaryEmail($notificationKey, $alertCodes)
    {
        $result = null;
        $notifications = $this->GetOptionByName('activity-summary-notifications');
        if (!empty($notifications)) {
            $email = $notifications->email;
            $frequency = $notifications->frequency;
            $sites = $notifications->sites;
            if ($frequency == self::REPORT_WEEKLY) {
                $pre_subject = sprintf(__('Week number %s - Website %s', 'reports-wsal'), date("W", strtotime('-1 week')), get_bloginfo('name'));
            } else {
                $pre_subject = sprintf(__('Month %s %s- Website %s', 'reports-wsal'), date("F", strtotime('-1 month')), date("Y", strtotime('-1 month')), get_bloginfo('name'));
            }

            $attachments =  $this->GetAttachment($notificationKey);
            if (!empty($attachments)) {
                $criteria = $this->GetCriteria($alertCodes);
                $subject = $pre_subject.sprintf(__(' - %s Email Report', 'reports-wsal'), $criteria);
                $content = '<p>The report with the list of '.$criteria.' on website '.get_bloginfo('name').' for';
                if ($frequency == self::REPORT_WEEKLY) {
                    $content .= ' week '.date("W", strtotime('-1 week'));
                } else {
                    $content .= ' the month of '.date("F", strtotime('-1 month')).' '.date("Y", strtotime('-1 month'));
                }
                $content .= ' is attached.</p>';
                $content .= '<p>The report was automatically generated with the <a href="http://www.wpsecurityauditlog.com/extensions/compliance-reports-add-on-for-wordpress/">Reports Add-On</a> for the plugin <a href="http://www.wpsecurityauditlog.com">WP Security Audit Log</a>.</p>';
                $headers = "MIME-Version: 1.0\r\n";

                add_filter('wp_mail_content_type', array($this, '_set_html_content_type'));
                add_filter('wp_mail_from', array($this, 'custom_wp_mail_from'));
                add_filter('wp_mail_from_name', array($this, 'custom_wp_mail_from_name'));
                $result = wp_mail($email, $subject, $content, $headers, $attachments);

                remove_filter('wp_mail_content_type', array($this, '_set_html_content_type'));
                remove_filter('wp_mail_from', array($this, 'custom_wp_mail_from'));
                remove_filter('wp_mail_from_name', array($this, 'custom_wp_mail_from_name'));
            }
            return $result;
        }
        return $result;
    }

    public function BuildAttachment($attachKey, $aAlerts, $type, $frequency, $sites, $nextDate, $limit)
    {
        $lastDate = null;

        $result = $this->GetListEvents($aAlerts, $type, $frequency, $sites, $nextDate, $limit);
        if (!empty($result['lastDate'])) {
            $lastDate = $result['lastDate'];
            //unset($result['lastDate']);
        }
        $filename = $this->_uploadsDirPath.'result_'.$attachKey.'-user'.get_current_user_id().'.json';
        if (file_exists($filename)) {
            $data = json_decode(file_get_contents($filename), true);
            if (!empty($data)) {
                if (!empty($result)) {
                    foreach ($result['data'] as $value) {
                        array_push($data['data'], $value);
                    }
                }
                $data['lastDate'] = $lastDate;
                file_put_contents($filename, json_encode($data));
            }
        } else {
            if (!empty($result)) {
                file_put_contents($filename, json_encode($result));
            }
        }
        return $lastDate;
    }

    private function GetAttachment($attachKey)
    {
        $result = null;
        $upload_dir = wp_upload_dir();
        $this->_uploadsDirPath = trailingslashit($upload_dir['basedir']).'reports/';
        $filename = $this->_uploadsDirPath.'result_'.$attachKey.'-user'.get_current_user_id().'.json';
        if (file_exists($filename)) {
            $data = json_decode(file_get_contents($filename), true);
            $result = $this->FileGenerator($data['data'], $data['filters']);
            $result = $this->_uploadsDirPath.$result;
        }
        @unlink($filename);
        return $result;
    }

    public function generateReportJsonFile($report) 
    {
        $upload_dir = wp_upload_dir();
        $this->_uploadsDirPath = trailingslashit($upload_dir['basedir']).'reports/';
        $filename = $this->_uploadsDirPath.'report-user'.get_current_user_id().'.json';
        if (file_exists($filename)) {
            $data = json_decode(file_get_contents($filename), true);
            if (!empty($data)) {
                if (!empty($report)) {
                    foreach ($report['data'] as $value) {
                        array_push($data['data'], $value);
                    }
                }
                file_put_contents($filename, json_encode($data));
            }
        } else {
            if (!empty($report)) {
                file_put_contents($filename, json_encode($report));
            }
        }
    }

    public function downloadReportFile() 
    {
        $downloadPageUrl = null;
        $upload_dir = wp_upload_dir();
        $this->_uploadsDirPath =  trailingslashit($upload_dir['basedir']).'reports/';
        $filename = $this->_uploadsDirPath.'report-user'.get_current_user_id().'.json';
        if (file_exists($filename)) {
            $data = json_decode(file_get_contents($filename), true);
            $result = $this->FileGenerator($data['data'], $data['filters']);
            if (!empty($result)) {
                $pluginPath = plugins_url(basename(realpath(dirname(__FILE__).'/../')));
                $e = '&f='.base64_encode($result).'&ctype='.$data['filters']['report_format'];
                $downloadPageUrl = wp_nonce_url($pluginPath.'/download.php', 'wpsal_reporting_nonce_action', 'wpsal_reporting_nonce_name').$e;
            }
        }
        @unlink($filename);
        return $downloadPageUrl;
    }

    private function GetListEvents($aAlerts, $type, $frequency, $sites, $nextDate, $limit)
    {
        if ($frequency == self::REPORT_WEEKLY) {
            $start_date = date($this->_dateFormat, strtotime('-1 week'));
        } else {
            $start_date = date($this->_dateFormat, strtotime('-1 month'));
        }
        $filters['sites'] = $sites;
        $filters['users'] = array();
        $filters['roles'] = array();
        $filters['ip-addresses'] = array();
        $filters['alert_codes']['groups'] = array();
        $filters['alert_codes']['alerts'] = $aAlerts;
        $filters['date_range']['start'] = $start_date;
        $filters['date_range']['end'] = date($this->_dateFormat, time());
        $filters['report_format'] = $type;
        $filters['nextDate'] = $nextDate;
        $filters['limit'] = $limit;
        $upload_dir = wp_upload_dir();
        $this->_uploadsDirPath = trailingslashit($upload_dir['basedir']).'reports/';
        $result = $this->GenerateReport($filters, false);
        return $result;
    }

    private function GetCriteria($alertCodes)
    {
        $criteria = null;
        if (is_array($alertCodes)) {
            if (in_array('1002', $alertCodes) || in_array('1003', $alertCodes)) {
                $criteria = "List of Failed Logins";
            } elseif (in_array('2001', $alertCodes)) {
                $criteria = "List of Published Content";
            } elseif (in_array('4003', $alertCodes)) {
                $criteria = "List of Password Changes";
            } elseif (in_array('4000', $alertCodes)) {
                $criteria = "List of New Created Users";
            } elseif (in_array('1000', $alertCodes)) {
                $criteria = "List of Logins";
            }
        } else {
            switch ($alertCodes) {
                case '1000':
                    $criteria = "List of Logins";
                    break;
                case '1002':
                case '1003':
                    $criteria = "List of Failed Logins";
                    break;
            }
        }
        return $criteria;
    }

    /**
     * #### Alert Groups
     * if we have alert groups, we need to retrieve all alert codes for those groups
     * and add them to a final alert of alert codes that will be sent to db in the select query
     * the same goes for individual alert codes
     */
    private function GetCodesByGroups($alertGroups, $alertCodes, $showError = true)
    {
        $_codes = array();
        $hasAlertGroups = (empty($alertGroups) ? false : true);
        $hasAlertCodes = (empty($alertCodes) ? false : true);
        if ($hasAlertCodes) {
            // add the specified alerts to the final array
            $_codes = $alertCodes;
        }
        if ($hasAlertGroups) {
            // Get categorized alerts
            $catAlerts = $this->wsal->alerts->GetCategorizedAlerts();
            $this->_catAlertGroups = array_keys($catAlerts);
            if (empty($catAlerts)) {
                if ($showError) {
                    $this->_addError(__('Internal Error. Could not retrieve the alerts from the main plugin.', 'reports-wsal'));
                }
                return false;
            }
            // Make sure that all specified alert categories are valid
            foreach ($alertGroups as $k => $category) {
                // get alerts from the category and add them to the final array
                //#! only if the specified category is valid, otherwise skip it
                if (isset($catAlerts[$category])) {
                    // if this is the "System Activity" category...some of those alert needs to be padded
                    if ($category == __('System Activity', 'wp-security-audit-log')) {
                        foreach ($catAlerts[$category] as $i => $alert) {
                            $aid = $alert->type;
                            if (strlen($aid)==1) {
                                $aid = $this->PadKey($aid);
                            }
                            array_push($_codes, $aid);
                        }
                    } else {
                        foreach ($catAlerts[$category] as $i => $alert) {
                            array_push($_codes, $alert->type);
                        }
                    }
                }
            }
        }
        if (empty($_codes)) {
            if ($showError) {
                $this->_addError(__('Please specify at least one Alert Group or specify an Alert Code.', 'reports-wsal'));
            }
            return false;
        }
        return $_codes;
    }

    final public function _set_html_content_type()
    {
        return 'text/html';
    }

    final public function custom_wp_mail_from($original_email_from)
    {
        $email_from = $this->GetOptionByName('from-email');
        if (!empty($email_from)) {
            return $email_from;
        } else {
            return $original_email_from;
        }
    }

    final public function custom_wp_mail_from_name($original_email_from_name)
    {
        $email_from_name = $this->GetOptionByName('display-name');
        if (!empty($email_from_name)) {
            return $email_from_name;
        } else {
            return $original_email_from_name;
        }
    }

/*============================== Support Archive Database ==============================*/

    public function IsArchivingEnabled()
    {
        return $this->GetOptionByName('archiving-e');
    }

    /**
     * Switch to Archive DB if is enabled
     */
    public function SwitchToArchiveDB()
    {
        if ($this->IsArchivingEnabled()) {
            $archiveType = $this->GetOptionByName('archive-type');
            $archiveUser = $this->GetOptionByName('archive-user');
            $password = $this->GetOptionByName('archive-password');
            $archiveName = $this->GetOptionByName('archive-name');
            $archiveHostname = $this->GetOptionByName('archive-hostname');
            $archiveBasePrefix = $this->GetOptionByName('archive-base-prefix');
            $config = WSAL_Connector_ConnectorFactory::GetConfigArray($archiveType, $archiveUser, $password, $archiveName, $archiveHostname, $archiveBasePrefix);
            $this->wsal->getConnector($config)->getAdapter('Occurrence');
        }
    }

    /**
     * Close Archive DB
     */
    public function CloseArchiveDB()
    {
        if ($this->IsArchivingEnabled()) {
            $archiveType = $this->GetOptionByName('archive-type');
            $archiveUser = $this->GetOptionByName('archive-user');
            $password = $this->GetOptionByName('archive-password');
            $archiveName = $this->GetOptionByName('archive-name');
            $archiveHostname = $this->GetOptionByName('archive-hostname');
            $archiveBasePrefix = $this->GetOptionByName('archive-base-prefix');
            $config = WSAL_Connector_ConnectorFactory::GetConfigArray($archiveType, $archiveUser, $password, $archiveName, $archiveHostname, $archiveBasePrefix);
            $result = $this->wsal->getConnector($config)->closeConnection();
            $this->wsal->getConnector(null, true)->getAdapter('Occurrence');
        }
    }

    public function IsMatchingReportCriteria($filters)
    {
        // Filters
        $sites       = (empty($filters['sites']) ? null : $filters['sites']);
        $users       = (empty($filters['users']) ? null : $filters['users']);
        $roles       = (empty($filters['roles']) ? null : $filters['roles']);
        $ipAddresses = (empty($filters['ip-addresses']) ? null : $filters['ip-addresses']);
        $alertGroups = (empty($filters['alert_codes']['groups']) ? null : $filters['alert_codes']['groups']);
        $alertCodes  = (empty($filters['alert_codes']['alerts']) ? null : $filters['alert_codes']['alerts']);
        $dateStart   = (empty($filters['date_range']['start']) ? null : $filters['date_range']['start']);
        $dateEnd     = (empty($filters['date_range']['end']) ? null : $filters['date_range']['end']);

        $_codes = $this->GetCodesByGroups($alertGroups, $alertCodes, false);

        $criteria['siteId'] = $sites ? "'".implode(',', $sites)."'" : 'null';
        $criteria['userId'] = $users ? "'".implode(',', $users)."'" : 'null';
        $criteria['roleName'] = 'null';
        $criteria['ipAddress'] = !empty($ipAddresses) ? "'".implode(',', $ipAddresses)."'" : 'null';
        $criteria['alertCode'] = !empty($_codes) ? "'".implode(',', $_codes)."'" : 'null';
        $criteria['startTimestamp'] = 'null';
        $criteria['endTimestamp'] = 'null';
        if ($roles) {
            $criteria['roleName'] = array();
            foreach ($roles as $k => $role) {
                array_push($_roleName, esc_sql('('.preg_quote($role).')'));
            }
            $criteria['roleName'] = "'".implode('|', $_roleName)."'";
        }
        if ($dateStart) {
            $dt = new DateTime();
            $df = $dt->createFromFormat($this->_dateFormat . " H:i:s", $dateStart . " 00:00:00");
            $criteria['startTimestamp'] = $df->format('U');
        }
        if ($dateEnd) {
            $dt = new DateTime();
            $df = $dt->createFromFormat($this->_dateFormat . " H:i:s", $dateEnd . " 23:59:59");
            $criteria['endTimestamp'] = $df->format('U');
        }
        $count = $this->wsal->getConnector()->getAdapter("Occurrence")->CheckMatchReportCriteria($criteria);
        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }
}
