<?php if(! defined('WSAL_OPT_PREFIX')) { exit('Invalid request'); }
/**
 * Class WSAL_NP_Common
 *
 * Utility class
 */
class WSAL_NP_Common
{
    public $wsal = null;

    function __construct(WpSecurityAuditLog $wsal){
        $this->wsal = $wsal;
    }

    /**
     * Creates an unique random number
     * @param int $size The length of the number to generate
     * @return string
     */
    function UniqueNumber($size = 20) {
        $numbers = range(0, 100);
        shuffle($numbers);
        $n = join('', array_slice($numbers, 0, $size));
        return substr($n, 0, $size);
    }

    function AddGlobalOption($option, $value){
        $this->DeleteCacheNotif();
        $this->wsal->SetGlobalOption($option, $value);
    }

    function UpdateGlobalOption($option, $value){
        $this->DeleteCacheNotif();
        return $this->wsal->UpdateGlobalOption($option, $value);
    }

    function DeleteGlobalOption($option){
        $this->DeleteCacheNotif();
        return $this->wsal->DeleteByName($option);
    }

    function GetOptionByName($option){
        return $this->wsal->GetGlobalOption($option);
    }

    function DeleteCacheNotif(){
        if(function_exists('wp_cache_delete')){
            wp_cache_delete(WSAL_CACHE_KEY);
        }
    }

    /**
     * Retrieve the appropriate posts table name
     * @param $wpdb
     * @return string
     */
    function GetPostsTableName($wpdb){
        $pfx = $this->GetDbPrefix($wpdb);
        if($this->wsal->IsMultisite()){
            global $blog_id;
            $bid = ($blog_id==1 ? '' : $blog_id.'_');
            return $pfx.$bid.'posts';
        }
        return $pfx.'posts';
    }

    /**
     * Retrieve the appropriate db prefix
     * @param $wpdb
     * @return mixed
     */
    function GetDbPrefix($wpdb){
        if($this->wsal->IsMultisite()){
            return $wpdb->base_prefix;
        }
        return $wpdb->prefix;
    }

    /**
     * Validate the input from a condition
     * @param string $string
     * @return mixed
     */
    function ValidateInput($string){
        $string = preg_replace('/<script[^>]*?>.*?<\/script>/i', '', $string);
        $string = preg_replace('/<[\/\!]*?[^<>]*?>/i', '', $string);
        $string = preg_replace('/<style[^>]*?>.*?<\/style>/i', '', $string);
        $string = preg_replace('/<![\s\S]*?--[ \t\n\r]*>/i', '', $string);
        return preg_replace("/[^a-z0-9.':\-]/i", '', $string);
    }

    /**
     * Validate a partial IP address.
     * @param string $ip
     * @return bool
     */
    function IsValidPartialIP($ip) {
        if( !$ip or strlen(trim($ip)) == 0){
            return false;
        }
        $ip=trim($ip);
        $parts = explode('.', $ip);
        if (count($parts) <= 4) {
            foreach ($parts as $part) {
                if ($part > 255 || $part < 0) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    function GetRoleNames() {
        global $wp_roles;
        if (!isset( $wp_roles )){
            $wp_roles = new WP_Roles();
        }
        return $wp_roles->get_names();
    }

    /**
     * @internal
     * @param string $key The key to pad
     * @return string
     */
    function PadKey($key){
        if(strlen($key) == 1){
            $key = str_pad($key, 4, '0', STR_PAD_LEFT);
        }
        return $key;
    }

    /**
     * Datetime used in the Notifications.
     */
    public function GetDatetimeFormat() {
        $date_format = $this->GetDateFormat();
        $time_format = $this->GetTimeFormat();
        return $date_format . " " . $time_format;
    }

    /**
     * Date Format from WordPress General Settings.
     */
    public function GetDateFormat() {
        return $this->wsal->settings->GetDateFormat();
    }

    /**
     * Used in the form validation.
     */
    public function DateValidFormat() {
        $search = array('Y', 'm', 'd');
        $replace = array('yyyy', 'mm', 'dd');
        return str_replace($search, $replace, $this->GetDateFormat());
    }

    /**
     * Time Format from WordPress General Settings.
     */
    public function GetTimeFormat() {
        return $this->wsal->settings->GetTimeFormat();
    }

    /**
     * Check time 24 hours.
     * @return bool true/false
     */
    public function Show24Hours() {
        $format = $this->GetTimeFormat();
        if (strpos($format, 'g') !== false) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Validate a condition
     * @param object $select2
     * @param object $select3
     * @param string $inputValue
     * @return bool|int|mixed
     */
    function ValidateCondition($select2, $select3, $inputValue)
    {
        $values = $select2->data;
        $selected = $select2->selected;

        if (!isset($values[$selected])) {
            return array('error'=>__('The form is not valid. Please reload the page and try again.', 'email-notifications-wsal'));
        }

        // Get what's selected
        $what = strtoupper($values[$selected]);
        // if ALERT CODE
        if ('ALERT CODE' == $what) {
            $length = strlen($inputValue);
            if ($length <> 4) {
                return array('error'=>__('The ALERT CODE is not valid.', 'email-notifications-wsal'));
            }
            $alerts = $this->wsal->alerts->GetAlerts();
            if (empty($alerts)) {
                return array('error'=>__('Internal Error. Please reload the page and try again.', 'email-notifications-wsal'));
            }
            // Ensure this is a valid Alert Code
            $keys = array_keys($alerts);
            $keys = array_map(array($this,'PadKey'), $keys);
            if (!in_array($inputValue, $keys)) {
                return array('error'=>__('The ALERT CODE is not valid.', 'email-notifications-wsal'));
            }
        }
        // IF USERNAME
        elseif ('USERNAME' == $what) {
            $length = strlen($inputValue);
            if ($length > 50) {
                return array('error'=>__('The USERNAME is not valid. Maximum of 50 characters allowed.', 'email-notifications-wsal'));
            }
            // make sure this is a valid username
            if (!username_exists($inputValue)) {
                return array('error'=>__('The USERNAME does not exist.', 'email-notifications-wsal'));
            }
        }
        // IF USER ROLE
        elseif ('USER ROLE' == $what) {
            $length = strlen($inputValue);
            if ($length > 50) {
                return array('error'=>__('The USER ROLE is not valid. Maximum of 50 characters allowed.', 'email-notifications-wsal'));
            }
            // Ensure this is a valid role
            $roles = $this->GetRoleNames();
            $role = strtolower($inputValue);
            if (! isset($roles[$role])) {
                return array('error'=>__('The USER ROLE does not exist.', 'email-notifications-wsal'));
            }
        }
        // IF SOURCE IP
        elseif ('SOURCE IP' == $what) {
            $length = strlen($inputValue);
            if ($length > 15) {
                return array('error'=>__('The SOURCE IP is not valid. Maximum of 15 characters allowed.', 'email-notifications-wsal'));
            }
            $val_s3 = $select3->data[$select3->selected];
            if (!$val_s3) {
                return array('error'=>__('The form is not valid. Please reload the page and try again.', 'email-notifications-wsal'));
            }
            if ('IS EQUAL' == $val_s3) {
                $r = filter_var($inputValue, FILTER_VALIDATE_IP);
                if ($r) {
                    return true;
                } else {
                    return array('error'=>__('The SOURCE IP is not valid.', 'email-notifications-wsal'));
                }
            }
            $r = $this->IsValidPartialIP($inputValue);
            if ($r) {
                return true;
            } else {
                return array('error'=>__('The SOURCE IP fragment is not valid.', 'email-notifications-wsal'));
            }
        }
        // DATE
        elseif ('DATE' == $what) {
            $date_format = $this->DateValidFormat();
            if ($date_format == 'mm-dd-yyyy' || $date_format == 'dd-mm-yyyy') {
                // regular expression to match date format mm-dd-yyyy or dd-mm-yyyy
                $regEx = '/^\d{1,2}-\d{1,2}-\d{4}$/';
            } else {
                // regular expression to match date format yyyy-mm-dd
                $regEx = '/^\d{4}-\d{1,2}-\d{1,2}$/';
            }
            $r = preg_match($regEx, $inputValue);
            if ($r) {
                return true;
            } else {
                return array('error'=>__('DATE is not valid.', 'email-notifications-wsal'));
            }
        }
        // TIME
        elseif ('TIME' == $what) {
            $timeArray = explode(':', $inputValue);
            if (count($timeArray) == 2) {
                $p1 = intval($timeArray[0]);
                if ($p1 < 0 || $p1 > 23) {
                    return array('error'=>__('TIME is not valid.', 'email-notifications-wsal'));
                }
                $p2 = intval($timeArray[1]);
                if ($p2 < 0 || $p2 > 59) {
                    return array('error'=>__('TIME is not valid.', 'email-notifications-wsal'));
                }
                return true;
            }
            return false;
        }
        // POST ID, PAGE ID, CUSTOM POST ID
        elseif ('POST ID' == $what || 'PAGE ID' == $what || 'CUSTOM POST ID' == $what) {
            $e = sprintf(__('%s is not valid', 'email-notifications-wsal'), $what);
            $inputValue = intval($inputValue);
            if (! $inputValue) {
                return array('error'=> $e);
            }
            global $wpdb;
            $t = $this->GetPostsTableName($wpdb);
            $result = $wpdb->get_var(sprintf("SELECT COUNT(ID) FROM ".$t." WHERE ID = %d", $inputValue));

            if ($result >= 1) {
                return true;
            } else {
                $e = sprintf(__('%s was not found', 'email-notifications-wsal'), $what);
                return array('error'=> $e);
            }
        }
        // SITE ID
        elseif ('SITE DOMAIN' == $what) {
            $e = sprintf(__('%s is not valid', 'email-notifications-wsal'), $what);
            if (!$inputValue) {
                return array('error'=> $e);
            }
            if ($this->wsal->IsMultisite()) {
                global $wpdb;
                $result = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM " . $wpdb->blogs. " WHERE blog_id = %s", $inputValue));
            } else {
                return array('error'=>__('The enviroment is not multisite.', 'email-notifications-wsal'));
            }
            if (!empty($result) && $result >= 1) {
                return true;
            } else {
                $e = sprintf(__('%s was not found', 'email-notifications-wsal'), $what);
                return array('error'=> $e);
            }
        }
        // POST TYPE
        elseif ('POST TYPE' == $what) {
            $e = sprintf(__('%s is not valid', 'email-notifications-wsal'), $what);
            if (!$inputValue) {
                return array('error'=> $e);
            }
            
            $length = strlen($inputValue);
            if ($length < 4) {
                return array('error'=>__('The POST TYPE is not valid. Minimum of 4 characters allowed.', 'email-notifications-wsal'));
            }
            if ($length > 15) {
                return array('error'=>__('The POST TYPE is not valid. Maximum of 15 characters allowed.', 'email-notifications-wsal'));
            }
        }
        return true;
    }

    /**
     * Retrieve a notification from the database
     * @param int $id
     * @return mixed
     */
    function GetNotification($id)
    {
        $result = $this->wsal->GetNotification($id);
        return $result;
    }

    /**
     * Retrieve all notifications from the database
     * @param string $how
     * @return mixed
     */
    function GetNotifications()
    {
        $result = $this->wsal->GetNotificationsSetting(WSAL_OPT_PREFIX);
        return $result;
    }

    /**
     * Check to see whether or not we can add a new notification
     * @return bool
     */
    function CanAddNotification()
    {
        $num = $this->wsal->CountNotifications(WSAL_OPT_PREFIX);
        return $num < WSAL_MAX_NOTIFICATIONS ? true : false;
    }

    function GetDisabledNotifications()
    {
        $notifications = $this->GetNotifications();
        
        foreach ($notifications as $i => &$entry) {
            $item = unserialize($entry->option_value);

            if ($item->status == 1) {
                unset($notifications[$i]);
                continue;
            }
        }
        $notifications = array_values($notifications);
        return $notifications;
    }

    function GetNotBuiltInNotifications()
    {
        $notifications = $this->GetNotifications();
        
        foreach ($notifications as $i => &$entry) {
            $item = unserialize($entry->option_value);

            if (isset($item->built_in)) {
                unset($notifications[$i]);
                continue;
            }
        }
        $notifications = ($notifications) ? array_values($notifications) : null;
        return $notifications;
    }

    function GetBuiltIn()
    {
        $notifications = $this->GetNotifications();
        $aBuilt_in = array();
        foreach ($notifications as $i => &$entry) {
            $item = unserialize($entry->option_value);

            if (isset($item->built_in)) {
                $aBuilt_in[] = $notifications[$i];
            }
        }
        return $aBuilt_in;
    }

    function CheckBuiltInByName($name)
    {
        $name = 'wsal-notification-built-in-' . $name;
        $aBuilt_in = $this->GetBuiltIn();
        if (!empty($aBuilt_in)) {
            foreach ($aBuilt_in as $element) {
                if ($element->option_name == $name) {
                    $item = unserialize($element->option_value);
                    $checked = array();
                    foreach ($item->triggers as $value) {
                        array_push($checked, $value['input1']);
                    }
                    return array('title' => $item->title, 'email' => $item->email, 'checked' => $checked);
                }
            }
        }
        return null;
    }

    function CheckBuiltInByType($type)
    {
        $type = 'wsal-notification-built-in-' . $type;
        $aBuilt_in = $this->GetBuiltIn();
        if (!empty($aBuilt_in)) {
            foreach ($aBuilt_in as $element) {
                if ($element->option_name == $type) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Retrieve all notifications to display in the search view
     * @param wpdb $wpdb
     * @param $search
     * @return array
     */
    function GetSearchResults($search)
    {
        if (empty($search)) {
            return array();
        }
        $notifications = $this->GetNotifications();
        $tmp = array();
        foreach ($notifications as $entry) {
            $item = unserialize($entry->option_value);
            if (false !== ($r = stristr($item->title, $search))) {
                array_push($tmp, $entry);
                continue;
            }
        }
        return $tmp;
    }


    /**
     * JSON encode and display the Notification object in the Edit Notification view
     * @param WSAL_NP_NotificationBuilder $notifBuilder
     */
    function CreateJsOutputEdit(WSAL_NP_NotificationBuilder $notifBuilder)
    {
        echo '<script type="text/javascript" id="wsalModelWp">';
        echo "var wsalModelWp = '".json_encode($notifBuilder->get())."';";
        echo '</script>';
    }


    /**
     * Build the js script the view will use to rebuild the form in case of an error
     * @param $notifBuilder
     */
    function CreateJsObjOutput(WSAL_NP_NotificationBuilder $notifBuilder)
    {
        echo '<script type="text/javascript" id="wsalModelWp">';
        echo "var wsalModelWp = '".json_encode($notifBuilder->get())."';";
        echo '</script>';
    }

    function GetNotificationsPageUrl()
    {
        $class = $this->wsal->views->FindByClassName('WSAL_NP_Notifications');
        if (false === $class) {
            $class = new WSAL_NP_Notifications($this->wsal);
        }
        return esc_attr($class->GetUrl());
    }

    /**
     * Save or update a notification into the database. This method will also validate the notification.
     * @param WSAL_NP_NotificationBuilder $notifBuilder
     * @param object $notification
     * @param wpdb $wpdb
     * @param bool $update
     * @return null|void
     */
    function SaveNotification(WSAL_NP_NotificationBuilder $notifBuilder, $notification, $update = false)
    {
        if (!$update) {
            if (!$this->CanAddNotification()) {
                ?><div class="error"><p><?php _e('Title is required.', 'email-notifications-wsal'); ?></p></div><?php
                return $this->CreateJsObjOutput($notifBuilder);
            }
        }

        // Sanitize Title & Email
        $title = trim($notification->info->title);
        $title = str_replace(array('\\', '/'), '', $title);
        $title = sanitize_text_field($title);
        $email = trim($notification->info->email);

        $notifBuilder->clearTriggersErrors();

        // Validate title
        if (empty($title)) {
            ?><div class="error"><p><?php _e('Title is required.', 'email-notifications-wsal'); ?></p></div><?php
            $notifBuilder->update('errors', 'titleMissing', __('Title is required.', 'email-notifications-wsal'));
            return $this->CreateJsObjOutput($notifBuilder);
        } else {
            $regexTitle = '/[A-Z0-9\,\.\+\-\_\?\!\@\#\$\%\^\&\*\=]/si';
            if (! preg_match($regexTitle, $title)) {
                $notifBuilder->update('errors', 'titleMissing', __('Title is not valid.', 'email-notifications-wsal'));
                return $this->CreateJsObjOutput($notifBuilder);
            }
        }

        // Set triggers
        $triggers = $notification->triggers;

        // Validate triggers
        if (empty($triggers)) {
            $notifBuilder->update('errors', 'triggersMissing', __('Please add at least one condition.', 'email-notifications-wsal'));
            return $this->CreateJsObjOutput($notifBuilder);
        }


//---------------------------------------------
// Validate conditions
//---------------------------------------------
        $hasErrors = false; // just a flag so we won't have to count notifObj->errors->triggers
        $conditions = array(); // will hold the trigger entries that will be saved into DB, so we won't have to parse the obj again
        foreach ($triggers as $i => $entry) {
            // flag
            $j = $i+1; // to help us identify the right trigger in the DOM

            // simple obj mapping
            $select1 = $entry->select1;
            $select2 = $entry->select2;
            $select3 = $entry->select3;
            $input1 = $entry->input1;
            // Checking if selected SITE DOMAIN(9)
            if ($select2->selected == 9) {
                global $wpdb;
                $input1 = $wpdb->get_var($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs WHERE domain = %s", $input1));
            }
            // Validate each trigger/condition
            if ($i) {
                // Ignore the first trigger's select1 - because it's not used
                // so we start with the second one
                // make sure the provided selected index exists in the correspondent data array
                if (! isset($select1->data[$select1->selected])) {
                    $hasErrors = true;
                    $notifBuilder->updateTriggerError($j, __('The form is not valid. Please refresh the page and try again.', 'email-notifications-wsal'));
                    continue;
                }
            }
            if (! isset($select2->data[$select2->selected])) {
                $hasErrors = true;
                $notifBuilder->updateTriggerError($j, __('The form is not valid. Please refresh the page and try again.', 'email-notifications-wsal'));
                continue;
            }
            if (! isset($select3->data[$select3->selected])) {
                $hasErrors = true;
                $notifBuilder->updateTriggerError($j, __('The form is not valid. Please refresh the page and try again.', 'email-notifications-wsal'));
                continue;
            }

            // sanitize and validate input
            $input1 = $this->ValidateInput($input1);
            $size = strlen($input1);
            if ($size > 50) {
                $hasErrors = true;
                $notifBuilder->updateTriggerError($j, __("A trigger's condition must not be longer than 50 characters.", 'email-notifications-wsal'));
                continue;
            }

            $vm = $this->ValidateCondition($select2, $select3, $input1);
            if (is_array($vm)) {
                $hasErrors = true;
                $notifBuilder->updateTriggerError($j, $vm['error']);
                continue;
            }

            // add condition
            array_push($conditions, array(
                'select1' => intval($select1->selected),
                'select2' => intval($select2->selected),
                'select3' => intval($select3->selected),
                'input1' => $input1,
            ));
        }

        // Validate email
        if (empty($email)) {
            $notifBuilder->update('errors', 'emailMissing', __('Email is required.', 'email-notifications-wsal'));
            return $this->CreateJsObjOutput($notifBuilder);
        } else {
            $emails = explode(',', $email);
            $emailsValidate = array();
            // make sure this is a valid email address
            foreach ($emails as $item) {
                $emailsValidate[] = sanitize_email($item);
            }
            $email = implode(",", $emailsValidate);
            if (empty($email)) {
                $notifBuilder->update('errors', 'emailMissing', __('Email is not valid.', 'email-notifications-wsal'));
                return $this->CreateJsObjOutput($notifBuilder);
            }
        }

        if ($hasErrors) {
            return $this->CreateJsObjOutput($notifBuilder);
        }
        // save notification
        else {
            // Build the object that will be saved into DB
            if ($update) {
                $optName = $notification->special->optName;
                // Holds the notification data that will be saved into the db
                $data = new stdClass();
                $data->title = $notification->info->title;
                $data->email = $notification->info->email;
                $data->owner = $notification->special->owner;
                $data->dateAdded = $notification->special->dateAdded;
                $data->status = $notification->special->status;
                $data->viewState = $notification->viewState;
            } else {
                $optName = WSAL_OPT_PREFIX.$this->UniqueNumber();
                // Holds the notification data that will be saved into the db
                $data = new stdClass();
                $data->title = $title;
                $data->email = $email;
                $data->owner = get_current_user_id();
                $data->dateAdded = time();
                $data->status = 1;
                $data->viewState = $notification->viewState;
            }

            $data->triggers = $conditions; // this will be serialized by WP

            $result = $update ? $this->UpdateGlobalOption($optName, $data) : $this->AddGlobalOption($optName, $data);
            if ($result === false) {
                // catchy... update_option && update_site_option will both return false if one will use them to update an option
                // with the same value(s)
                // so we need to check the last error
                
                ?><div class="error"><p><?php _e('Notification could not be saved.', 'email-notifications-wsal'); ?></p></div><?php
                return $this->CreateJsObjOutput($notifBuilder);
            }
            // ALL GOOD
            ?><div class="updated"><p><?php _e('Notification successfully saved.', 'email-notifications-wsal'); ?></p></div><?php
                // send to Notifications page
                echo '<script type="text/javascript" id="wsalModelReset">';
                echo 'window.setTimeout(function(){location.href="'.$this->GetNotificationsPageUrl().'";}, 700);';
                echo '</script>';
        }
        return null;
    }
}
