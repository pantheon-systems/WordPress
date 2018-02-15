<?php if(! defined('WSAL_OPT_PREFIX')) { exit('Invalid request'); }

class WSAL_NP_Notifications extends WSAL_AbstractView
{

    public function __construct(WpSecurityAuditLog $plugin)
    {
        parent::__construct($plugin);
        add_action('admin_notices', array($this, 'WsalAdminNoticesNotificationsExtensionPlugin'));
        add_action('network_admin_notices', array($this, 'WsalAdminNoticesNotificationsExtensionPlugin'));
        $this->RegisterNotice('email-notifications-wsal-plugin');
    }

    public function WsalAdminNoticesNotificationsExtensionPlugin()
    {
        if (is_main_site()) {
            $licenseValid = $this->_plugin->licensing->IsLicenseValid('email-notifications-wsal.php');
            $class = $this->_plugin->views->FindByClassName('WSAL_Views_Licensing');
            if (false === $class) {
                $class = new WSAL_Views_Licensing($this->_plugin);
            }
            $licensingPageUrl = esc_attr($class->GetUrl());
            $wsUrl = 'http://www.wpwhitesecurity.com/plugins-premium-extensions/email-notifications-wordpress/';
            if (!$this->IsNoticeDismissed('email-notifications-wsal-plugin') && !$licenseValid) {
                ?><div class="updated" data-notice-name="email-notifications-wsal-plugin">
                <p><?php _e(sprintf('Remember to <a href="%s">enter your plugin license code</a> for the <strong>Notifications Extension</strong>,
                                to benefit from updates and support.', $licensingPageUrl), 'email-notifications-wsal');?>
                    &nbsp;&nbsp;&nbsp;<a href="javascript:;" class="wsal-dismiss-notification"><?php _e('Dismiss this notice', 'email-notifications-wsal'); ?></a></p>
                </div><?php
            }
        }
    }

    public function GetTitle()
    {
        return __('Email Notifications', 'email-notifications-wsal');
    }

    private function _addTitleButton()
    {
        $class = $this->_plugin->views->FindByClassName('WSAL_NP_AddNotification');
        if (false === $class) {
            $class = new WSAL_NP_AddNotification($this->_plugin);
        }

        $wizard = $this->_plugin->views->FindByClassName('WSAL_NP_Wizard');
        if (false === $wizard) {
            $wizard = new WSAL_NP_Wizard($this->_plugin);
        }
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($){
                $('.wsal-tab h2:first').append('<a href="<?php echo esc_attr($wizard->GetUrl() . "#tab-second"); ?>" class="add-new-h2"><?php _e("Launch Wizard", "wpsal-notifications"); ?></a> &nbsp; <a href="<?php echo esc_attr($class->GetUrl()); ?>" class="add-new-h2"><?php _e("Add New", "wpsal-notifications"); ?></a>');
            });
        </script>
        <?php
    }

    public function GetIcon()
    {
        return 'dashicons-admin-generic';
    }

    public function GetName()
    {
        return __('Email Notifications', 'email-notifications-wsal');
    }

    public function GetWeight()
    {
        return 7;
    }

    public function Header()
    {
        $pluginPath = plugins_url(basename(realpath(dirname(__FILE__).'/../')));
        wp_enqueue_style('wsal-notif-css', $pluginPath.'/css/styles.css');
        echo "<script type='text/javascript'> var dateFormat = '".$this->_plugin->wsalCommon->DateValidFormat()."'; </script>";
        wp_enqueue_script('wsal-notif-utils-js', $pluginPath.'/js/wsal-notification-utils.js', array('jquery'));
    }

    public function Footer()
    {
        ?><script type="text/javascript">
            jQuery(document).ready(function(){
                // tab handling code
                jQuery('#wsal-tabs>a').click(function(){
                    jQuery('#wsal-tabs>a').removeClass('nav-tab-active');
                    jQuery('table.wsal-tab').hide();
                    jQuery(jQuery(this).addClass('nav-tab-active').attr('href')).show();
                });
                // show relevant tab
                var hashlink = jQuery('#wsal-tabs>a[href="' + location.hash + '"]');
                if (hashlink.length) {
                    hashlink.click();
                } else {
                    jQuery('#wsal-tabs>a:first').click();
                }

                jQuery('#wsal-trigger-form input[type=checkbox]').unbind('change').change(function() {
                    current = this.name+'-email';
                    if (jQuery(this).is(':checked')) {
                        jQuery('#'+current).prop('required', true);
                    } else {
                        jQuery('#'+current).removeProp('required');
                    }
                });
            });
        </script><?php
    }

#region  >>> PRIVATE
    //@internal
    const WPSALP_NOTIF_ERROR = 1;

    private $_searchView = false;

    // Inspect the REQUEST and detect the requested view
    private function PrepareView()
    {
        // Default view
        if (!isset($_REQUEST['action'])) {
            return $this->_plugin->wsalCommon->GetNotBuiltInNotifications();
        }

        // From here on, all requests must be signed
        $nonce = $_REQUEST['_wpnonce'];
        if (!wp_verify_nonce($nonce, 'nonce-notifications-view')) {
            return self::WPSALP_NOTIF_ERROR;
        }

        $validActions = array(
            'disable_notification', 'enable_notification', 'delete_notification', 'view_disabled', 'search', 'bulk'
        );
        $action = sanitize_text_field($_REQUEST['action']);
        $id = isset($_REQUEST['id']) ? sanitize_text_field($_REQUEST['id']) : null; // the notification's ID

        if (! in_array($action, $validActions)) {
            return self::WPSALP_NOTIF_ERROR;
        }

        switch ($action) {
            case 'disable_notification':
            {
                if (empty($id)) {
                    return self::WPSALP_NOTIF_ERROR;
                }
                if (! $this->_disableNotification($id)) {
                    return self::WPSALP_NOTIF_ERROR;
                }
                return $this->_plugin->wsalCommon->GetNotBuiltInNotifications();
            }

            case 'enable_notification':
            {
                if (empty($id)) {
                    return self::WPSALP_NOTIF_ERROR;
                }
                if (! $this->_enableNotification($id)) {
                    return self::WPSALP_NOTIF_ERROR;
                }
                return $this->_plugin->wsalCommon->GetNotBuiltInNotifications();
            }

            case 'delete_notification':
            {
                if (empty($id)) {
                    return self::WPSALP_NOTIF_ERROR;
                }
                if (!$this->_deleteNotification($id)) {
                    return self::WPSALP_NOTIF_ERROR;
                }
                return $this->_plugin->wsalCommon->GetNotBuiltInNotifications();
            }

            case 'view_disabled':
            {
                return $this->_plugin->wsalCommon->GetDisabledNotifications();
            }

            case 'search':
            {
                $search = isset($_REQUEST['s']) ? sanitize_text_field($_REQUEST['s']) : null; // search term
                if (empty($search)) {
                    // display the default view
                    return $this->_plugin->wsalCommon->GetNotBuiltInNotifications();
                }
                $this->_searchView = true;
                return $this->_plugin->wsalCommon->GetSearchResults($search);
            }

            case 'bulk':
            {
                // this is coming through POST
                $rm = strtoupper($_SERVER['REQUEST_METHOD']);
                if ($rm != 'POST') {
                    return self::WPSALP_NOTIF_ERROR;
                }

                if (isset($_POST['bulk']) || isset($_POST['bulk2'])) {
                    $entries = (isset($_POST['entries']) && !empty($_POST['entries'])) ? $_POST['entries'] : null;
                    if (empty($entries)) {
                        // Noting to do; display the default view
                        return $this->_plugin->wsalCommon->GetNotBuiltInNotifications();
                    }

                    $b1 = strtolower($_POST['bulk']);
                    $b2 = strtolower($_POST['bulk2']);

                    // Invalid request
                    if ($b1 == -1 && $b2 == -1) {
                        return self::WPSALP_NOTIF_ERROR;
                    } elseif ($b1 == -1) {
                        // b2 must have valid values
                        if ($b2 == 'enable') {
                            $this->_bulkEnable($entries);
                        } elseif ($b2 == 'disable') {
                            $this->_bulkDisable($entries);
                        } elseif ($b2 == 'delete') {
                            $this->_bulkDelete($entries);
                        }
                        return $this->_plugin->wsalCommon->GetNotBuiltInNotifications();
                    } elseif ($b2 == -1) {
                        // b1 must have valid values
                        if ($b1 == 'enable') {
                            $this->_bulkEnable($entries);
                        } elseif ($b1 == 'disable') {
                            $this->_bulkDisable($entries);
                        } elseif ($b1 == 'delete') {
                            $this->_bulkDelete($entries);
                        }
                        return $this->_plugin->wsalCommon->GetNotBuiltInNotifications();
                    }
                }
                // Invalid request
                return self::WPSALP_NOTIF_ERROR;
            }
        }
        return self::WPSALP_NOTIF_ERROR;
    }

    private function _disableNotification($id)
    {
        $notif = $this->_plugin->wsalCommon->GetNotification($id);
        if ($notif === false) {
            return false;
        }
        $optName = $notif->option_name;
        $optData = unserialize($notif->option_value);
        $optData->status = 0;
        return $this->_plugin->wsalCommon->UpdateGlobalOption($optName, $optData);
    }

    private function _enableNotification($id)
    {
        $notif = $this->_plugin->wsalCommon->GetNotification($id);
        if ($notif === false) {
            return false;
        }
        $optName = $notif->option_name;
        $optData = unserialize($notif->option_value);
        $optData->status = 1;
        return $this->_plugin->wsalCommon->UpdateGlobalOption($optName, $optData);
    }

    private function _deleteNotification($id)
    {
        if (!$this->_plugin->settings->CurrentUserCan('edit')) {
            return false;
        }
        $notif = $this->_plugin->wsalCommon->GetNotification($id);
        if ($notif === false) {
            return false;
        }
        return $this->_plugin->wsalCommon->DeleteGlobalOption($notif->option_name);
    }

    private function _bulkEnable(array $entries)
    {
        foreach ($entries as $i => $id) {
            $this->_enableNotification($id);
        }
    }

    private function _bulkDisable(array $entries)
    {
        foreach ($entries as $i => $id) {
            $this->_disableNotification($id);
        }
    }

    private function _bulkDelete(array $entries)
    {
        foreach ($entries as $i => $id) {
            $this->_deleteNotification($id);
        }
    }

    private function createBuilt_in()
    {
        $results = array();
        $emails = array();
        $titles = array(
            1 => "User logs in",
            2 => "New user is created",
            3 => "User changed password",
            4 => "User changed the password of another user",
            5 => "User's role has changed",
            6 => "Published content is modified",
            7 => "Content is published",
            8 => "First time user logs in",
            9 => "New plugin is installed",
            10 => "Installed plugin is activated",
            11 => "Plugin file is modified",
            12 => "New theme is installed",
            13 => "Installed theme is activated",
            14 => "Theme file is modified",
            15 => "Critical Alert is Generated"
        );
        $events = array(
            1 => "1000",
            2 => array("4000", "4001", "4012"),
            3 => "4003",
            4 => "4004",
            5 => "4002",
            6 => array("2065", "2066", "2067"),
            7 => array("2001", "2005", "2030"),
            8 => "1000",
            9 => "5000",
            10 => "5001",
            11 => "2051",
            12 => "5005",
            13 => "5006",
            14 => "2046",
            15 => "2046"
        );
        for ($i=1; $i <= 15; $i++) {
            if (isset($_POST['built-in_'.$i])) {
                $emails[$i] = (isset($_POST['built-in-email_'.$i]) ? trim($_POST['built-in-email_'.$i]) : null);
                $results[] = $this->saveBuilt_in($i, $titles[$i], $emails[$i], $events[$i]);
            } else {
                $results[] = $this->saveBuilt_in($i, null, null, null);
            }
        }
        if (in_array(2, $results)) { ?>
            <div class="error"><p><?php _e('Notification could not be saved.', 'email-notifications-wsal'); ?></p></div>
        <?php
        } else if (in_array(1, $results)) { ?>
            <div class="updated"><p><?php _e('Notification successfully saved.', 'email-notifications-wsal'); ?></p></div>
        <?php
        }
    }

    public function saveBuilt_in($id, $title, $email, $events, $built_in = true)
    {
        $optName = WSAL_OPT_PREFIX."built-in-".$id;
        $data = new stdClass();
        $data->title = $title;
        $data->email = $email;
        $data->owner = get_current_user_id();
        $data->dateAdded = time();
        $data->status = 1;
        $data->viewState = array();
        $data->triggers = array();
        $data->id = $id;
        if ($built_in) {
            $data->built_in = 1;
        }
        if ($title == "First time user logs in") {
            $data->firstTimeLogin = 1;
        }
        if ($title == "Critical Alert is Generated") {
            $data->isCritical = 1;
        }
        if (isset($events)) {
            if (is_array($events)) {
                foreach ($events as $key => $event) {
                    $data->viewState[] = "trigger_id_".$id;
                    $data->triggers[] = array("select1" => ($key == 0 ? 0 : 1), "select2" => 0, "select3" => 0, "input1" => $event);
                }
            } else {
                $data->viewState[] = "trigger_id_".$id;
                $data->triggers[] = array("select1" => 0, "select2" => 0, "select3" => 0, "input1" => $events);
            }
        }
        if (count($data->triggers) > 0) {
            $result = $this->_plugin->wsalCommon->AddGlobalOption($optName, $data);
            if ($result === false) {
                return 2;
            } else {
                return 1;
            }
        } else {
            $this->_plugin->wsalCommon->DeleteGlobalOption("wsal-".$optName);
            return 0;
        }
    }

#endregion            PRIVATE

    public function Render()
    {
        if (!$this->_plugin->settings->CurrentUserCan('edit')) {
            wp_die(__('You do not have sufficient permissions to access this page', 'email-notifications-wsal'));
        }
        // Update title
        $this->_addTitleButton();

        $notifications = $this->PrepareView();

        if (self::WPSALP_NOTIF_ERROR == $notifications) {
            ?><div class="error"><p><?php _e('Invalid request.', 'email-notifications-wsal'); ?></p></div><?php
        }

        $allNotificationsCount = count($notifications);
        if (isset($_REQUEST['action'])) {
            if ($_REQUEST['action'] == 'view_disabled') {
                $disabledNotificationsCount = $allNotificationsCount;
            } else {
                $disabledNotificationsCount = count($this->_plugin->wsalCommon->GetDisabledNotifications());
            }
        } else {
            $disabledNotificationsCount = count($this->_plugin->wsalCommon->GetDisabledNotifications());
        }

        $nonce = wp_create_nonce('nonce-notifications-view');
        $viewAllUrl      = $this->GetUrl();
        $disableUrl      = $viewAllUrl.'&action=disable_notification&_wpnonce='.$nonce;
        $enableUrl       = $viewAllUrl.'&action=enable_notification&_wpnonce='.$nonce;
        $deleteUrl       = $viewAllUrl.'&action=delete_notification&_wpnonce='.$nonce;
        $viewDisabledUrl = $viewAllUrl.'&action=view_disabled&_wpnonce='.$nonce;
        $searchUrl       = $viewAllUrl.'&action=search&_wpnonce='.$nonce;
        $bulkActionUrl   = $viewAllUrl.'&action=bulk&_wpnonce='.$nonce;
        $eClass = $this->_plugin->views->FindByClassName('WSAL_NP_EditNotification');
        if (false === $eClass) {
            $eClass = new WSAL_NP_EditNotification($this->_plugin);
        }
        $editUrl = $eClass->GetUrl().'&action=wsal_edit_notification&_wpnonce='.wp_create_nonce('nonce-edit-notification');

        if (isset($_POST['wsal-submit'])) {
            $this->createBuilt_in();
        }
        $aBuilt_in = $this->_plugin->wsalCommon->GetBuiltIn();
        ?>
        <h2 id="wsal-tabs" class="nav-tab-wrapper">
            <a href="#tab-builder" class="nav-tab"><?php _e('Email Notifications Trigger Builder', 'email-notifications-wsal');?></a>
            <a href="#tab-built-in" class="nav-tab"><?php _e('Recommended Email Security Notifications', 'email-notifications-wsal');?></a>
        </h2>
        <div class="nav-tabs">
            <table class="wsal-tab widefat" id="tab-builder">
                <tbody>
                    <tr>
                        <td>
                            <h2></h2>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div>
                                <?php // Check to see if there are any notifications
                                if (!empty($notifications)) { ?>
                                    <script type="text/javascript">
                                        jQuery(document).ready(function($){
                                            $('.wsal_js_no_click').on('click',function(e){e.preventDefault();return false;});
                                            // Disable the "view disabled" link if there are no disabled notifications
                                            <?php if (!$disabledNotificationsCount): ?>
                                                $('#wsal-view-disabled-link').on('click', function(){return false;});
                                            <?php endif; ?>
                                        });
                                    </script>
                                    <div class="wrap">
                                        <ul class="subsubsub" id="wsal-top-notif-menu">
                                            <li class="all"><a class="current" href="<?php echo $viewAllUrl;?>"><?php _e('All', 'email-notifications-wsal');?> <span class="count">(<?php echo $allNotificationsCount;?>)</span></a> |</li>
                                            <li class="disabled"><a href="<?php echo $viewDisabledUrl;?>" id="wsal-view-disabled-link"><?php _e('Disabled', 'email-notifications-wsal');?> <span class="count">(<?php echo $disabledNotificationsCount;?>)</span></a></li>
                                        </ul>
                                        <form method="get" action="" onsubmit="javascript:return false;" id="notifications-filter">
                                            <p class="search-box">
                                                <label for="notification-search-input" class="screen-reader-text"><?php _e('Search Notifications', 'email-notifications-wsal');?>:</label>
                                                <input type="search" value="" name="" id="notification-search-input" maxlength="125"/>
                                                <input type="submit" value="<?php _e('Search Notifications', 'email-notifications-wsal');?>" class="button" id="search-submit" name=""/>
                                                <script type="text/javascript">
                                                    jQuery(document).ready(function($){
                                                        var searchInput = $('#notification-search-input');
                                                        $('#search-submit').on('click', function(e){
                                                            var val = wsalSanitize(searchInput.val().trim(), true);
                                                            if(!val.length){ e.preventDefault(); }
                                                            else { location.href = "<?php echo $searchUrl;?>&s="+val; }
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </p>
                                            <div class="tablenav top">
                                                <div class="alignleft actions bulkactions">
                                                    <select id="bulk" name="bulk">
                                                        <option selected="selected" value="-1"><?php _e('Bulk actions', 'email-notifications-wsal');?></option>
                                                        <option class="hide-if-no-js" value="enable"><?php _e('Enable', 'email-notifications-wsal');?></option>
                                                        <option class="hide-if-no-js" value="disable"><?php _e('Disable', 'email-notifications-wsal');?></option>
                                                        <option value="delete"><?php _e('Delete', 'email-notifications-wsal');?></option>
                                                    </select>
                                                    <input type="submit" value="<?php _e('Apply', 'email-notifications-wsal');?>" class="button action" id="doaction" name=""/>
                                                </div>
                                                <br class="clear">
                                            </div>
                                            <table id="wsal-notif-table" class="wp-list-table widefat fixed plugins">
                                                <thead>
                                                <tr>
                                                    <th class="manage-column column-cb check-column" id="cb" scope="col">
                                                        <label for="cb-select-all-1" class="screen-reader-text"><?php _e('Select All', 'email-notifications-wsal');?></label>
                                                        <input type="checkbox" id="cb-select-all-1"></th>
                                                    <th class="manage-column column-title" scope="col"><span><?php _e('Title', 'email-notifications-wsal');?></span></th>
                                                    <th class="manage-column column-author" scope="col"><?php _e('Author', 'email-notifications-wsal');?></th>
                                                    <th class="manage-column column-date" scope="col"><span><?php _e('Date', 'email-notifications-wsal');?></span></th>
                                                </tr>
                                                </thead>
                                                <tfoot>
                                                <tr>
                                                    <th class="manage-column column-cb check-column" scope="col">
                                                        <label for="cb-select-all-2" class="screen-reader-text"><?php _e('Select All', 'email-notifications-wsal');?></label>
                                                        <input type="checkbox" id="cb-select-all-2">
                                                    </th>
                                                    <th class="manage-column column-title" scope="col"><span><?php _e('Title', 'email-notifications-wsal');?></span></th>
                                                    <th class="manage-column column-author" scope="col"><?php _e('Author', 'email-notifications-wsal');?></th>
                                                    <th class="manage-column column-date" scope="col"><span><?php _e('Date', 'email-notifications-wsal');?></span></th>
                                                </tr>
                                                </tfoot>

                                                <tbody id="the-list">
                                                    <?php
                                                    $datetime_format = $this->_plugin->wsalCommon->GetDatetimeFormat();
                                                    $date_format = $this->_plugin->wsalCommon->GetDateFormat();
                                                    //================================
                                                    // SHOW NOTIFICATIONS
                                                    //================================
                                                    
                                                    foreach ($notifications as $k => $entry) {
                                                        $entryID = $entry->id;
                                                        $optValue = unserialize($entry->option_value);

                                                        $title = $optValue->title;
                                                        $enabled = $optValue->status;
                                                        $userID = $optValue->owner;
                                                        $user = get_user_by('id', $userID);
                                                        $userName = $user->user_nicename;
                                                        $dateAdded = $optValue->dateAdded;
                                                        $dateFull = date($datetime_format, $dateAdded);
                                                        $dateOnly = date($date_format, $dateAdded);
                                                        $editUrl .= '&id='.$entryID;
                                                        $userPageUrl = get_author_posts_url($userID);

                                                        ?>
                                                        <tr class="entry-<?php echo $entryID;?> <?php echo ($enabled) ? 'active' : '';?>" id="entry-<?php echo $entryID;?>">
                                                            <th class="check-column" scope="row">
                                                                <label for="cb-select-1" class="screen-reader-text"><?php echo __('Select', 'email-notifications-wsal') .' '.$title;?></label>
                                                                <input type="checkbox" value="<?php echo $entryID;?>" name="entries[]" id="cb-select-1">
                                                            </th>
                                                            <td class="post-title page-title column-title"><strong><a title="<?php _e('Edit this notification', 'email-notifications-wsal');?>" href="<?php echo $editUrl;?>" class="row-title"><?php echo $title;?></a></strong>
                                                                <div class="row-actions">
                                                                    <span class="edit"><a title="<?php _e('Edit this notification', 'email-notifications-wsal');?>" href="<?php echo $editUrl;?>"><?php _e('Edit', 'email-notifications-wsal');?></a> |
                                                                    <span class="view">
                                                                        <?php if ($enabled) :
                                                                            echo sprintf('<a title="%s" href="%s" >%s</a>',
                                                                                __('Disable this notification', 'email-notifications-wsal'), $disableUrl.'&id='.$entryID, __('Disable', 'email-notifications-wsal'));
                                                                            ?>
                                                                        <?php else :
                                                                            echo sprintf('<a title="%s" href="%s" >%s</a>',
                                                                                __('Enable this notification', 'email-notifications-wsal'), $enableUrl.'&id='.$entryID, __('Enable', 'email-notifications-wsal'));
                                                                            ?>
                                                                        <?php endif; ?>
                                                                    | </span>
                                                                    <span class="trash"><?php echo sprintf('<a href="%s" title="%s" class="submitdelete">%s</a>', $deleteUrl.'&id='.$entryID, __('Delete this notification', 'email-notifications-wsal'), __('Delete', 'email-notifications-wsal'));?></span>
                                                                </div>
                                                            </td>
                                                            <td class="author column-author"><a href="<?php echo $userPageUrl;?>"><?php echo $userName;?></a></td>
                                                            <td class="date column-date"><abbr title="<?php echo $dateFull;?>"><?php echo $dateOnly;?></abbr><br><?php _e('Published', 'email-notifications-wsal');?></td>
                                                        </tr>
                                                        <?php } ?>
                                                </tbody>
                                            </table>
                                            <div class="tablenav bottom">
                                                <div class="alignleft actions bulkactions">
                                                    <select id="bulk2" name="bulk2">
                                                        <option selected="selected" value="-1"><?php _e('Bulk actions', 'email-notifications-wsal');?></option>
                                                        <option class="hide-if-no-js" value="enable"><?php _e('Enable', 'email-notifications-wsal');?></option>
                                                        <option class="hide-if-no-js" value="disable"><?php _e('Disable', 'email-notifications-wsal');?></option>
                                                        <option value="delete"><?php _e('Delete', 'email-notifications-wsal');?></option>
                                                    </select>
                                                    <input type="submit" value="<?php _e('Apply', 'email-notifications-wsal');?>" class="button action" id="doaction2" name=""/>
                                                </div>
                                                <div class="alignleft actions"></div>

                                                <br class="clear">
                                            </div>
                                            <script type="text/javascript">
                                                jQuery(document).ready(function($){
                                                    // Register click event for bulk actions
                                                    $('#doaction, #doaction2').on('click', function(){
                                                        // Avoid sending both dropdowns with the same value
                                                        var dd = $(this).prev();
                                                        // make sure there's a valid option selected
                                                        if(dd.val() == -1){ return false; }
                                                        // clear the other dropdown
                                                        else {
                                                            var idd = dd.attr('id');
                                                            if(idd == 'bulk'){$('#bulk2').val(-1);}
                                                            else {$('#bulk').val(-1);}
                                                        }
                                                        $('#notifications-filter')
                                                            .removeAttr('onsubmit')
                                                            .attr('action', "<?php echo $bulkActionUrl;?>")
                                                            .attr('method', "post")
                                                            .submit();
                                                        return true;
                                                    });
                                                });
                                            </script>
                                        </form>
                                        <br class="clear">
                                    </div>
                                <?php } elseif (!empty($aBuilt_in) && count($aBuilt_in) > 0) { ?>

                                <?php } else {
                                    // Display the search form
                                    if ($this->_searchView) { ?>
                                        <form method="get" action="" onsubmit="javascript:return false;" id="notifications-filter">
                                            <p class="search-box">
                                                <label for="notification-search-input" class="screen-reader-text"><?php _e('Search Notifications', 'email-notifications-wsal');?>:</label>
                                                <input type="search" value="" name="" id="notification-search-input" maxlength="125"/>
                                                <input type="submit" value="<?php _e('Search Notifications', 'email-notifications-wsal');?>" class="button" id="search-submit" name=""/>
                                                <script type="text/javascript">
                                                    jQuery(document).ready(function($){
                                                        var searchInput = $('#notification-search-input');
                                                        $('#search-submit').on('click', function(e){
                                                            var val = wsalSanitize(searchInput.val().trim(), true);
                                                            if(!val.length){ e.preventDefault(); }
                                                            else { location.href = "<?php echo $searchUrl;?>&s="+val; }
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </p>
                                        </form>
                                        <div class="no-notifications-msg" style="clear: left; display: block;"><?php _e('<p>No notifications found to match your search.</p>', 'email-notifications-wsal');?></div>
                                    <?php } else {
                                        echo '<div class="no-notifications-msg">'.__('<p>No notifications found. Click the <code>Add New</code> button above to create one.</p>', 'email-notifications-wsal').'</div>';
                                    }
                                } /*[End else]*/ ?>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <!-- Tab Built-in Notifications-->

            <form id="wsal-trigger-form" action="<?php echo admin_url('admin.php?page=wsal-np-notifications');?>#tab-built-in" method="post">
            <table class="form-table wsal-tab" id="tab-built-in">
                <?php
                $checked = array();
                $email = array();
                if (!empty($aBuilt_in) && count($aBuilt_in) > 0) {
                    foreach ($aBuilt_in as $k => $v) {
                        $optValue = unserialize($v->option_value);
                        $checked[] = $optValue->viewState[0];
                        $email[$optValue->id] = $optValue->email;
                    }
                }
                ?>
                    <tbody class="widefat">
                        <tr>
                            <td colspan="2" style="padding-left:20px;">
                                <p>
                                    <span class="description"><?php _e('Tick the check box to enable a built-in notification. You can specify multiple email addresses by separating them with a comma (,).', 'email-notifications-wsal'); ?></span>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="columns"><?php _e('Instant User Changes and Actions', 'email-notifications-wsal'); ?></label></th>
                            <td>
                                <fieldset>
                                    <label for="built-in_1">
                                        <input type="checkbox" name="built-in_1" id="built-in_1" class="built-in" <?php echo(in_array("trigger_id_1", $checked)? 'checked' : '')?>>
                                        <span class="built-in-title"><?php _e('User logs in (1000)', 'email-notifications-wsal'); ?></span>
                                        <input type="text" class="built-in-email" name="built-in-email_1" id="built-in_1-email" placeholder="Email *" value="<?php echo(!empty($email[1])? $email[1] : null)?>">
                                    </label>
                                    <br/>
                                    <label for="built-in_2">
                                        <input type="checkbox" name="built-in_2" id="built-in_2" class="built-in" <?php echo(in_array("trigger_id_2", $checked)? 'checked' : '')?>>
                                        <span class="built-in-title"><?php _e('New user is created (alerts 4000, 4001, 4012)', 'email-notifications-wsal'); ?></span>
                                        <input type="text" class="built-in-email" name="built-in-email_2" id="built-in_2-email" placeholder="Email *" value="<?php echo(!empty($email[2])? $email[2] : null)?>">
                                    </label>
                                    <br/>
                                    <label for="built-in_3">
                                        <input type="checkbox" name="built-in_3" id="built-in_3" class="built-in" <?php echo(in_array("trigger_id_3", $checked)? 'checked' : '')?>>
                                        <span class="built-in-title"><?php _e('User changed password (4003)', 'email-notifications-wsal'); ?></span>
                                        <input type="text" class="built-in-email" name="built-in-email_3" id="built-in_3-email" placeholder="Email *" value="<?php echo(!empty($email[3])? $email[3] : null)?>">
                                    </label>
                                    <br/>
                                    <label for="built-in_4">
                                        <input type="checkbox" name="built-in_4" id="built-in_4" class="built-in" <?php echo(in_array("trigger_id_4", $checked)? 'checked' : '')?>>
                                        <span class="built-in-title"><?php _e('User changed the password of another user (4004)', 'email-notifications-wsal'); ?></span>
                                        <input type="text" class="built-in-email" name="built-in-email_4" id="built-in_4-email" placeholder="Email *" value="<?php echo(!empty($email[4])? $email[4] : null)?>">
                                    </label>
                                    <br/>
                                    <label for="built-in_5">
                                        <input type="checkbox" name="built-in_5" id="built-in_5" class="built-in" <?php echo(in_array("trigger_id_5", $checked)? 'checked' : '')?>>
                                        <span class="built-in-title"><?php _e("User's role has changed (4002)", 'email-notifications-wsal'); ?></span>
                                        <input type="text" class="built-in-email" name="built-in-email_5" id="built-in_5-email" placeholder="Email *" value="<?php echo(!empty($email[5])? $email[5] : null)?>">
                                    </label>
                                    <br/>
                                    <label for="built-in_6">
                                        <input type="checkbox" name="built-in_6" id="built-in_6" class="built-in" <?php echo(in_array("trigger_id_6", $checked)? 'checked' : '')?>>
                                        <span class="built-in-title"><?php _e('Published content is modified (alerts 2065, 2066, 2067)', 'email-notifications-wsal'); ?></span>
                                        <input type="text" class="built-in-email" name="built-in-email_6" id="built-in_6-email" placeholder="Email *" value="<?php echo(!empty($email[6])? $email[6] : null)?>">
                                    </label>
                                    <br/>
                                    <label for="built-in_7">
                                        <input type="checkbox" name="built-in_7" id="built-in_7" class="built-in" <?php echo(in_array("trigger_id_7", $checked)? 'checked' : '')?>>
                                        <span class="built-in-title"><?php _e('Content is published (alerts 2001, 2005, 2030)', 'email-notifications-wsal'); ?></span>
                                        <input type="text" class="built-in-email" name="built-in-email_7" id="built-in_7-email" placeholder="Email *" value="<?php echo(!empty($email[7])? $email[7] : null)?>">
                                    </label>
                                    <br/>
                                    <label for="built-in_8">
                                        <input type="checkbox" name="built-in_8" id="built-in_8" class="built-in" <?php echo(in_array("trigger_id_8", $checked)? 'checked' : '')?>>
                                        <span class="built-in-title"><?php _e('First time user logs in', 'email-notifications-wsal'); ?></span>
                                        <input type="text" class="built-in-email" name="built-in-email_8" id="built-in_8-email" placeholder="Email *" value="<?php echo(!empty($email[8])? $email[8] : null)?>">
                                    </label>
                                    <br/>
                                    <span class="description"><?php _e('When you enable this option you will receive an email notification for the first time each of the existing users login.', 'email-notifications-wsal'); ?></span>
                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="columns"><?php _e('Plugin Changes Notifications', 'email-notifications-wsal'); ?></label></th>
                            <td>
                                <fieldset>
                                    <label for="built-in_9">
                                        <input type="checkbox" name="built-in_9" id="built-in_9" style="margin-top: 2px;" <?php echo(in_array("trigger_id_9", $checked)? 'checked' : '')?>>
                                        <span class="built-in-title"><?php _e('New plugin is installed (5000)', 'email-notifications-wsal'); ?></span>
                                        <input type="text" class="built-in-email" name="built-in-email_9" id="built-in_9-email" placeholder="Email *" value="<?php echo(!empty($email[9])? $email[9] : null)?>">
                                    </label>
                                    <br/>
                                    <label for="built-in_10">
                                        <input type="checkbox" name="built-in_10" id="built-in_10" style="margin-top: 2px;" <?php echo(in_array("trigger_id_10", $checked)? 'checked' : '')?>>
                                        <span class="built-in-title"><?php _e('Installed plugin is activated (5001)', 'email-notifications-wsal'); ?></span>
                                        <input type="text" class="built-in-email" name="built-in-email_10" id="built-in_10-email" placeholder="Email *" value="<?php echo(!empty($email[10])? $email[10] : null)?>">
                                    </label>
                                    <br/>
                                    <label for="built-in_11">
                                        <input type="checkbox" name="built-in_11" id="built-in_11" style="margin-top: 2px;" <?php echo(in_array("trigger_id_11", $checked)? 'checked' : '')?>>
                                        <span class="built-in-title"><?php _e('Plugin file is modified (2051)', 'email-notifications-wsal'); ?></span>
                                        <input type="text" class="built-in-email" name="built-in-email_11" id="built-in_11-email" placeholder="Email *" value="<?php echo(!empty($email[11])? $email[11] : null)?>">
                                    </label>
                                    <br/>
                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="columns"><?php _e('Themes Changes Notifications', 'email-notifications-wsal'); ?></label></th>
                            <td>
                                <fieldset>
                                    <label for="built-in_12">
                                        <input type="checkbox" name="built-in_12" id="built-in_12" style="margin-top: 2px;" <?php echo(in_array("trigger_id_12", $checked)? 'checked' : '')?>>
                                        <span class="built-in-title"><?php _e('New theme is installed (5005)', 'email-notifications-wsal'); ?></span>
                                        <input type="text" class="built-in-email" name="built-in-email_12" id="built-in_12-email" placeholder="Email *" value="<?php echo(!empty($email[12])? $email[12] : null)?>">
                                    </label>
                                    <br/>
                                    <label for="built-in_13">
                                        <input type="checkbox" name="built-in_13" id="built-in_13" style="margin-top: 2px;" <?php echo(in_array("trigger_id_13", $checked)? 'checked' : '')?>>
                                        <span class="built-in-title"><?php _e('Installed theme is activated (5006)', 'email-notifications-wsal'); ?></span>
                                        <input type="text" class="built-in-email" name="built-in-email_13" id="built-in_13-email" placeholder="Email *" value="<?php echo(!empty($email[13])? $email[13] : null)?>">
                                    </label>
                                    <br/>
                                    <label for="built-in_14">
                                        <input type="checkbox" name="built-in_14" id="built-in_14" style="margin-top: 2px;" <?php echo(in_array("trigger_id_14", $checked)? 'checked' : '')?>>
                                        <span class="built-in-title"><?php _e('Theme file is modified (2046)', 'email-notifications-wsal'); ?></span>
                                        <input type="text" class="built-in-email" name="built-in-email_14" id="built-in_14-email" placeholder="Email *" value="<?php echo(!empty($email[14])? $email[14] : null)?>">
                                    </label>
                                    <br/>
                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="columns"><?php _e('Generic Notifications', 'email-notifications-wsal'); ?></label></th>
                            <td>
                                <fieldset>
                                    <label for="built-in_15">
                                        <input type="checkbox" name="built-in_15" id="built-in_15" style="margin-top: 2px;" <?php echo(in_array("trigger_id_15", $checked)? 'checked' : '')?>>
                                        <span class="built-in-title"><?php _e('Critical Alert is Generated', 'email-notifications-wsal'); ?></span>
                                        <input type="text" class="built-in-email" name="built-in-email_15" id="built-in_15-email" placeholder="Email *" value="<?php echo(!empty($email[15])? $email[15] : null)?>">
                                    </label>
                                    <br/>
                                </fieldset>
                            </td>
                        </tr>
                    </tbody>
                    <tbody>
                        <tr>
                            <td colspan="2" style="padding:10px 0px;">
                                <div id="wsal-section-email">
                                    <p>
                                        <input type="submit" id="wsal-submit" name="wsal-submit" value="Save Notification" class="button-primary">
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
            </table>
            </form>
        </div>
    <?php
    }
}
