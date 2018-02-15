<?php if(!class_exists('WSAL_Rep_Plugin')){ return; } ;?>
<?php
    // Class mapping
    $wsalCommon = $this->_plugin->reporting->common;
    // Get available roles
    $roles = $wsalCommon->GetRoles();
    // Get available alert catogories
    $alerts = $this->_plugin->alerts->GetCategorizedAlerts();
    // Get the Request method
    $rm = strtoupper($_SERVER['REQUEST_METHOD']);
?>
<?php
//region >>>  PREPARE DATA FOR JS

//## SITES
//limit 0f 20
$wsal_a = WSAL_Rep_Common::GetSites(20);
    $wsalRepSites = array();
    foreach ($wsal_a as $entry) {
        // entry.blog_id, entry.domain
        $c = new stdClass();
        $c->id = $entry->blog_id;
        $c->text = $entry->blogname;
        array_push($wsalRepSites, $c);
    }
$wsalRepSites = json_encode($wsalRepSites);

//## USERS
//limit 0f 20
$wsal_b = WSAL_Rep_Common::GetUsers(20);
    $wsalRepUsers = array();
    foreach ($wsal_b as $entry) {
        // entry.blog_id, entry.domain
        $c = new stdClass();
        $c->id = $entry->ID;
        $c->text = $entry->user_login;
        array_push($wsalRepUsers, $c);
    }
$wsalRepUsers = json_encode($wsalRepUsers);

//## ROLES
$wpRoles = array();
    foreach ($roles as $i => $entry) {
        // entry.blog_id, entry.domain
        $c = new stdClass();
        $c->id = $entry;
        $c->text = $entry;
        array_push($wpRoles, $c);
    }
$wsalRepRoles = json_encode($wpRoles);

//## IPs
//limit 0f 20
$wsal_ips = WSAL_Rep_Common::GetIPAddresses(20);
    $wsalRepIPs = array();
    foreach ($wsal_ips as $entry) {
        $c = new stdClass();
        $c->id = $entry;
        $c->text = $entry;
        array_push($wsalRepIPs, $c);
    }
$wsalRepIPs = json_encode($wsalRepIPs);

//## ALERT GROUPS
$ag = array();
    foreach ($alerts as $cname => $_entries) {
        $t = new stdClass();
        $t->text = $cname;
        $t->children = array();
        foreach ($_entries as $i => $_aObj) {
            $c = new stdClass();
            $c->id = $_aObj->type;
            $c->text = $c->id.' ('.$_aObj->desc.')';
            array_push($t->children, $c);
        }
        array_push($ag, $t);
    }
$wsalRepAlertGroups = json_encode($ag);
//endregion >>>  PREPARE DATA FOR JS
?>
<?php
if ('POST' == $rm && isset($_POST['wsal_reporting_view_field'])) {
    // verify nonce
    if (!wp_verify_nonce($_POST['wsal_reporting_view_field'], 'wsal_reporting_view_action')) {
        wp_die(__('You do not have sufficient permissions to access this page - rep plugin.', 'reports-wsal'));
    }
    // The final filter array to use to filter alerts
    $filters = array(
        // option #1 - By Site(s)
        'sites' => array(), // by default, all sites
        // option #2 - By user(s)
        'users' => array(), // by default, all users
        // option #3 - By Role(s)
        'roles' => array(), // by default, all roles
        // option #4 - By IP Address(es)
        'ip-addresses' => array(), // by default, all IPs
        // option #5 - By Alert Code(s)
        'alert_codes' => array(
            'groups' => array(),
            'alerts' => array()
        ),
        // option #6 - Date range
        'date_range' => array(
            'start' => null,
            'end' => null
        ),
        // option #7 - Report format (HTML || CSV)
        'report_format' => $wsalCommon::REPORT_HTML
    );

    // The default error message to display if the form is not valid
    $messageFormNotValid = __('Invalid Request. Please refresh the page and try again.', 'reports-wsal');

    // Inspect the form data
    $formData = $_POST;

    //region >>>> By Site(s)
    if (isset($formData['wsal-rb-sites'])) {
        $rbs = intval($formData['wsal-rb-sites']);
        if (1 == $rbs) {
            /*[ already implemented in the $filters array ]*/
        } elseif (2 == $rbs) {
            // the textbox must be here and have values - these will be validated later on
            if (! isset($formData['wsal-rep-sites']) || empty($formData['wsal-rep-sites'])) {
                ?><div class="error"><p><?php _e('Error (TODO - error message): Please select SITES', 'reports-wsal');?></p></div><?php
            } else {
                $filters['sites'] = explode(',', $formData['wsal-rep-sites']);
            }
        }
    } else {
        ?><div class="error"><p><?php echo $messageFormNotValid; ?></p></div><?php
    }
    //endregion >>>> By Site(s)

    //region >>>> By User(s)
    if (isset($formData['wsal-rb-users'])) {
        $rbs = intval($formData['wsal-rb-users']);
        if (1 == $rbs) {
            /*[ already implemented in the $filters array ]*/
        } elseif (2 == $rbs) {
            // the textbox must be here and have values - these will be validated later on
            if (!isset($formData['wsal-rep-users']) || empty($formData['wsal-rep-users'])) {
                ?><div class="error"><p><?php _e('Error (TODO - error message): Please select USERS', 'reports-wsal');?></p></div><?php
            } else {
                $filters['users'] = explode(',', $formData['wsal-rep-users']);
            }
        }
    } else {
        ?><div class="error"><p><?php echo $messageFormNotValid; ?></p></div><?php
    }
    //endregion >>>> By User(s)

    //region >>>> By Role(s)
    if (isset($formData['wsal-rb-roles'])) {
        $rbs = intval($formData['wsal-rb-roles']);
        if (1 == $rbs) { /*[ already implemented in the $filters array ]*/
        } elseif (2 == $rbs) {
            // the textbox must be here and have values - these will be validated later on
            if (! isset($formData['wsal-rep-roles']) || empty($formData['wsal-rep-roles'])) {
                ?><div class="error"><p><?php _e('Error: Please select at least one role', 'reports-wsal');?></p></div><?php
            } else {
                $filters['roles'] = explode(',', $formData['wsal-rep-roles']);
            }
        }
    } else {
        ?><div class="error"><p><?php echo $messageFormNotValid; ?></p></div><?php
    }
    //endregion >>>> By Role(s)
    
    //region >>>> By IP(s)
    if (isset($formData['wsal-rb-ip-addresses'])) {
        $rbs = intval($formData['wsal-rb-ip-addresses']);
        if (1 == $rbs) { /*[ already implemented in the $filters array ]*/
        } elseif (2 == $rbs) {
            // the textbox must be here and have values - these will be validated later on
            if (! isset($formData['wsal-rep-ip-addresses']) || empty($formData['wsal-rep-ip-addresses'])) {
                ?><div class="error"><p><?php _e('Error: Please select at least one IP address', 'reports-wsal');?></p></div><?php
            } else {
                $filters['ip-addresses'] = explode(',', $formData['wsal-rep-ip-addresses']);
            }
        }
    } else {
        ?><div class="error"><p><?php echo $messageFormNotValid; ?></p></div><?php
    }
    //endregion >>>> By IP(s)

    //region >>>> By Alert Code(s)
    $_selectAllGroups = (isset($formData['wsal-rb-groups'])?true:false);
    $_selectAlerts = (isset($formData['wsal-rb-alert-codes'])?true:false);

    // Check alert groups
    if ($_selectAllGroups) {
        $filters['alert_codes']['groups'] = array_keys($alerts);
    } else {
        // check for selected alert groups
        if (isset($formData['wsal-rb-alerts']) && !empty($formData['wsal-rb-alerts'])) {
            $filters['alert_codes']['groups'] = $formData['wsal-rb-alerts'];
        }
        // check for individual alerts
        if (isset($formData['wsal-rb-alert-codes']) && isset($formData['wsal-rep-alert-codes']) && !empty($formData['wsal-rep-alert-codes'])) {
            $filters['alert_codes']['alerts'] = explode(',', $formData['wsal-rep-alert-codes']);
        }
    }

    //region >>>> By Date Range(s)
    if (isset($formData['wsal-start-date'])) {
        $filters['date_range']['start'] = trim($formData['wsal-start-date']);
    }
    if (isset($formData['wsal-end-date'])) {
        $filters['date_range']['end'] = trim($formData['wsal-end-date']);
    }
    //endregion >>>> By Date Range(s)

    //region >>>> Reporting Format
    if (isset($formData['wsal-rb-report-type'])) {
        if ($formData['wsal-rb-report-type'] == $wsalCommon::REPORT_HTML) {
            $filters['report_format'] = $wsalCommon::REPORT_HTML;
        } elseif ($formData['wsal-rb-report-type'] == $wsalCommon::REPORT_CSV) {
            $filters['report_format'] = $wsalCommon::REPORT_CSV;
        } else {
            ?><div class="error"><p><?php _e('Please select the report format.', 'reports-wsal'); ?></p></div><?php
        }
    } else {
        ?><div class="error"><p><?php echo $messageFormNotValid; ?></p></div><?php
    }
    //endregion >>>> Reporting Format
    ?>
    <script type="text/javascript">
        var filters = <?php echo json_encode($filters)?>;
        jQuery(document).ready(function(){
            AjaxCheckArchiveMatch(filters);
            AjaxGenerateReport(filters);
        });
    </script>
    <div class="updated">
        <?php $pluginPath = plugins_url(basename(realpath(dirname(__FILE__).'/../'))); ?>
        <p id="ajax-response">
            <img src="<?php echo $pluginPath; ?>/css/loading.gif">
            <?php _e(' Generating report. Please do not close this window', 'reports-wsal'); ?>
            <span id="ajax-response-counter"></span>
        </p>
    </div>
    <?php
    /* Delete the JSON file if exist */
    $upload_dir = wp_upload_dir();
    $this->_uploadsDirPath = $upload_dir['basedir'].'/reports/';
    $filename = $this->_uploadsDirPath.'report-user'.get_current_user_id().'.json';
    if (file_exists($filename)) {
        @unlink($filename);
    }
}
if (isset($_POST['wsal-notifications-submit'])) {
    if (isset($_POST['wsal-notif-email'])) {
        $this->createSummaryNotifications();
    }
} elseif (isset($_POST['wsal-notifications-submit-now'])) {
    if (isset($_POST['wsal-notif-email'])) {
        $this->createSummaryNotifications(true);
    }
}
$summary = $this->_plugin->reporting->common->GetOptionByName('activity-summary-notifications');
?>
<style type="text/css">
    #wsal-rep-container label input[type="checkbox"]+span { 
        margin-left: 3px;
    }
    #wsal-rep-container #label-xps:after {
        content: ' ';
        display:block;
        clear: both;
        margin-top: 3px;
    }
</style>
<div id="wsal-rep-container">
    <h2 id="wsal-tabs" class="nav-tab-wrapper">
        <a href="#tab-reports" class="nav-tab"><?php _e('Generate Reports', 'reports-wsal');?></a>
        <!-- <a href="#tab-archives" class="nav-tab"><?php _e('Generate Archives', 'reports-wsal');?></a> -->
        <a href="#tab-summary" class="nav-tab"><?php _e('Summary Email Reports', 'reports-wsal');?></a>
    </h2>
    <div class="nav-tabs">
        <div class="wsal-tab wrap" id="tab-reports">

            <p style="clear:both; margin-top: 30px"></p>

            <form id="wsal-rep-form" action="<?php echo $this->GetUrl();?>" method="post">
                <h4><?php _e('Generate a report', 'reports-wsal');?></h4>

    <!-- SECTION #1 -->
                <h4 class="wsal-reporting-subheading"><?php _e('Step 1: Select the type of report', 'reports-wsal');?></h4>

                <div class="wsal-rep-form-wrapper">

                    <!--// BY SITE -->
                    <div class="wsal-rep-section">
                        <label class="wsal-rep-label-fl"><?php _e('By Site(s)', 'reports-wsal');?></label>
                        <div class="wsal-rep-section-fl">
                            <p class="wsal-rep-clear">
                                <input type="radio" name="wsal-rb-sites" id="wsal-rb-sites-1" value="1" checked="checked" />
                                <label for="wsal-rb-sites-1"><?php _e('All Sites', 'reports-wsal');?></label>
                            </p>
                            <p class="wsal-rep-clear">
                                <input type="radio" name="wsal-rb-sites" id="wsal-rb-sites-2" value="2"/>
                                <label for="wsal-rb-sites-2"><?php _e('Specify sites', 'reports-wsal');?></label>
                                <input type="hidden" name="wsal-rep-sites" id="wsal-rep-sites"/>
                            </p>
                        </div>
                    </div>

                    <!--// BY USER -->
                    <div class="wsal-rep-section">
                        <label class="wsal-rep-label-fl"><?php _e('By User(s)', 'reports-wsal');?></label>
                        <div class="wsal-rep-section-fl">
                            <p class="wsal-rep-clear">
                                <input type="radio" name="wsal-rb-users" id="wsal-rb-users-1" value="1" checked="checked" />
                                <label for="wsal-rb-users-1"><?php _e('All Users', 'reports-wsal');?></label>
                            </p>
                            <p class="wsal-rep-clear">
                                <input type="radio" name="wsal-rb-users" id="wsal-rb-users-2" value="2"/>
                                <label for="wsal-rb-users-2"><?php _e('Specify users', 'reports-wsal');?></label>
                                <input type="hidden" name="wsal-rep-users" id="wsal-rep-users"/>
                            </p>
                        </div>
                    </div>

                    <!--// BY ROLE -->
                    <div class="wsal-rep-section">
                        <label class="wsal-rep-label-fl"><?php _e('By Role(s)', 'reports-wsal');?></label>
                        <div class="wsal-rep-section-fl">
                            <p class="wsal-rep-clear">
                                <input type="radio" name="wsal-rb-roles" id="wsal-rb-roles-1" value="1" checked="checked" />
                                <label for="wsal-rb-roles-1"><?php _e('All Roles', 'reports-wsal');?></label>
                            </p>
                            <p class="wsal-rep-clear">
                                <input type="radio" name="wsal-rb-roles" id="wsal-rb-roles-2" value="2"/>
                                <label for="wsal-rb-roles-2"><?php _e('Specify roles', 'reports-wsal');?></label>
                                <input type="hidden" name="wsal-rep-roles" id="wsal-rep-roles"/>
                            </p>
                        </div>
                    </div>

                    <!--// BY IP ADDRESS -->
                    <div class="wsal-rep-section">
                        <label class="wsal-rep-label-fl"><?php _e('By IP Address(es)', 'reports-wsal');?></label>
                        <div class="wsal-rep-section-fl">
                            <p class="wsal-rep-clear">
                                <input type="radio" name="wsal-rb-ip-addresses" id="wsal-rb-ip-addresses-1" value="1" checked="checked" />
                                <label for="wsal-rb-ip-addresses-1"><?php _e('All IP Addresses', 'reports-wsal');?></label>
                            </p>
                            <p class="wsal-rep-clear">
                                <input type="radio" name="wsal-rb-ip-addresses" id="wsal-rb-ip-addresses-2" value="2"/>
                                <label for="wsal-rb-ip-addresses-2"><?php _e('Specify IP Addresses', 'reports-wsal');?></label>
                                <input type="hidden" name="wsal-rep-ip-addresses" id="wsal-rep-ip-addresses"/>
                            </p>
                        </div>
                    </div>

                    <!--// BY ALERT GROUPS/CODE -->
                    <div class="wsal-rep-section">
                        <label class="wsal-rep-label-fl"><?php _e('By Alert Code(s)', 'reports-wsal');?></label>
                        <div class="wsal-rep-section-fl">
                            <p id="wsal-rep-js-groups" class="wsal-rep-clear">
                                <label for="wsal-rb-groups" class="wsal-rep-clear" id="label-xps">
                                    <input type="radio" name="wsal-rb-groups" id="wsal-rb-groups" value="0" checked="checked"/>
                                    <span style="margin-left: 0"><?php _e('Select All', 'reports-wsal');?></span>
                                </label>
                                <?php
                                if (empty($alerts)) {
                                    echo '<span>'.__('No alerts were found', 'reports-wsal').'</span>';
                                } else {
                                    $_alerts = array_keys($alerts);
                                    foreach ($_alerts as $i => $alert) {
                                        $id = 'wsal-rb-alert-'.$i;
                                        echo '<label for="'.$id.'" class="wsal-rep-clear">';
                                        echo '<input type="checkbox" name="wsal-rb-alerts[]" id="'.$id.'" class="wsal-js-groups" value="'.$alert.'"/>';
                                        echo '<span>'.$alert.'</span>';
                                        echo '</label>';
                                        $i++;
                                    }
                                }
                                ?>
                                <input type="checkbox" name="wsal-rb-alert-codes" id="wsal-rb-alert-codes-1"/>
                                <label for="wsal-rb-alert-codes-1"><?php _e('Specify Alert Codes', 'reports-wsal');?></label>
                                <input type="hidden" name="wsal-rep-alert-codes" id="wsal-rep-alert-codes"/>
                            </p>
                        </div>
                    </div>
                </div>
                <script id="wpsal_rep_s2" type="text/javascript">
                    jQuery(document).ready(function($)
                    {
                        // Alert groups
                        var wsalAlertGroups = $('.wsal-js-groups');
                        $("#wsal-rep-sites").select2({
                            data: JSON.parse('<?php echo $wsalRepSites;?>'),
                            placeholder: "<?php _e('Select site(s)');?>",
                            minimumResultsForSearch: 10,
                            multiple: true
                        }).on('select2-open',function(e){
                            var v = $(this).val();
                            if(!v.length){
                                $('#wsal-rb-sites-2').prop('checked', true);
                            }
                        }).on('select2-removed', function(){
                            var v = $(this).val();
                            if(!v.length){
                                $('#wsal-rb-sites-1').prop('checked',true);
                            }
                        }).on('select2-close', function(){
                            var v = $(this).val();
                            if(!v.length){
                                $('#wsal-rb-sites-1').prop('checked',true);
                            }
                        });
                        $("#wsal-rep-users").select2({
                            data: JSON.parse('<?php echo $wsalRepUsers;?>'),
                            placeholder: "<?php _e('Select user(s)');?>",
                            minimumResultsForSearch: 10,
                            multiple: true
                        }).on('select2-open',function(e){
                            var v = $(this).val();
                            if(!v.length){
                                $('#wsal-rb-users-2').prop('checked', true);
                            }
                        }).on('select2-removed', function(){
                            var v = $(this).val();
                            if(!v.length){
                                $('#wsal-rb-users-1').prop('checked',true);
                            }
                        }).on('select2-close', function(){
                            var v = $(this).val();
                            if(!v.length){
                                $('#wsal-rb-users-1').prop('checked',true);
                            }
                        });
                        $("#wsal-rep-roles").select2({
                            data: JSON.parse('<?php echo $wsalRepRoles;?>'),
                            placeholder: "<?php _e('Select role(s)');?>",
                            minimumResultsForSearch: 10,
                            multiple: true
                        }).on('select2-open',function(e){
                            var v = $(this).val();
                            if(!v.length){
                                $('#wsal-rb-roles-2').prop('checked', true);
                            }
                        }).on('select2-removed', function(){
                            var v = $(this).val();
                            if(!v.length){
                                $('#wsal-rb-roles-1').prop('checked',true);
                            }
                        }).on('select2-close', function(){
                            var v = $(this).val();
                            if(!v.length){
                                $('#wsal-rb-roles-1').prop('checked',true);
                            }
                        });

                        $("#wsal-rep-ip-addresses").select2({
                            data: JSON.parse('<?php echo $wsalRepIPs;?>'),
                            placeholder: "<?php _e('Select IP address(es)');?>",
                            minimumResultsForSearch: 10,
                            multiple: true
                        }).on('select2-open',function(e){
                            var v = $(this).val();
                            if(!v.length){
                                $('#wsal-rb-ip-addresses-2').prop('checked', true);
                            }
                        }).on('select2-removed', function(){
                            var v = $(this).val();
                            if(!v.length){
                                $('#wsal-rb-ip-addresses-1').prop('checked',true);
                            }
                        }).on('select2-close', function(){
                            var v = $(this).val();
                            if(!v.length){
                                $('#wsal-rb-ip-addresses-1').prop('checked',true);
                            }
                        });

                        $("#wsal-rep-alert-codes").select2({
                            data: <?php echo $wsalRepAlertGroups;?>,
                            placeholder: "<?php _e('Select Alert Code(s)');?>",
                            minimumResultsForSearch: 10,
                            multiple: true,
                            width: '500px'
                        }).on('select2-open',function(e){
                            var v = $(e).val;
                            if(v.length){
                                $('#wsal-rb-alert-codes-1').prop('checked', true);
                                $('#wsal-rb-groups').prop('checked', false);
                            }
                        }).on('select2-selecting', function(e){
                            var v = $(e).val;
                            if(v.length){
                                $('#wsal-rb-alert-codes-1').prop('checked', true);
                                $('#wsal-rb-groups').prop('checked', false);
                            }
                        }).on('select2-removed', function(e){
                            var v = $(this).val();
                            if(!v.length){
                                $('#wsal-rb-alert-codes-1').prop('checked', false);
                                // if none is checked, check the Select All input
                                var checked = $('.wsal-js-groups:checked');
                                if(!checked.length){
                                    $('#wsal-rb-groups').prop('checked', true);
                                }
                            }
                        });
                        function _deselectGroups(){
                            wsalAlertGroups.each(function(){
                                $(this).prop('checked', false);
                            });
                        }
                        $('#wsal-rb-groups').on('change', function(){
                            if($(this).is(':checked')){
                                // deselect all
                                _deselectGroups();
                                // deselect the alert codes checkbox if selected and no alert codes are provided
                                if($('#wsal-rb-alert-codes-1').is(':checked')){
                                    if(!$('#wsal-rep-alert-codes').val().length){
                                        $('#wsal-rb-alert-codes-1').prop('checked',false);
                                    }
                                }
                            }
                            else {
                                $(this).prop('checked', false);
                                // select first
                                $('.wsal-js-groups').get(0).prop('checked', true);
                            }
                        });
                        $('#wsal-rb-alert-codes-1').on('change', function(){
                            if($(this).prop('checked')==true){
                                $('#wsal-rb-groups').prop('checked', false);
                            }
                            else{
                                // if none is checked, check the Select All input
                                var checked = $('.wsal-js-groups:checked');
                                if(!checked.length){
                                    $('#wsal-rb-groups').prop('checked', true);
                                }
                            }
                        });
                        wsalAlertGroups.on('change',function(){
                            if($(this).is(':checked')){
                                $('#wsal-rb-groups').prop('checked', false);
                            }
                            else {
                                // if none is checked, check the Select All input
                                var checked = $('.wsal-js-groups:checked');
                                if(!checked.length){
                                    $('#wsal-rb-groups').prop('checked', true);
                                    var e = $("#wsal-rep-alert-codes").select2('val');
                                    if(!e.length){
                                        $('#wsal-rb-alert-codes-1').prop('checked', false);
                                    }
                                }
                            }
                        });
                        // Validation date format
                        $('.date-range').on('change', function(){
                            if (checkDate($(this))) {
                                jQuery(this).css('border-color', '#aaa');
                            } else {
                                jQuery(this).css('border-color', '#dd3d36');
                            }
                        });

                    });
                </script>

    <!-- SECTION #2 -->
                <?php
                    $date_format = $this->_plugin->reporting->common->GetDateFormat();
                ?>
                <h4 class="wsal-reporting-subheading"><?php _e('Step 2: Select the date range', 'reports-wsal');?></h4>

                <div class="wsal-note"><?php _e('Note: Do not select any dates if you would like to generate report from the beginning of the logs.', 'reports-wsal');?></div>

                <div class="wsal-rep-form-wrapper">
                    <!--// BY DATE -->
                    <div class="wsal-rep-section">
                        <label class="wsal-rep-label-fl label-datepick"><?php _e('Start Date', 'reports-wsal');?></label>
                        <div class="wsal-rep-section-fl">
                            <p class="wsal-rep-clear">
                                <input type="text" class="date-range" id="wsal-start-date" name="wsal-start-date" placeholder="<?php _e('Select start date', 'reports-wsal');?>"/>
                                <span class="description"> (<?php echo $date_format; ?>)</span>
                            </p>
                        </div>
                    </div>
                    <div class="wsal-rep-section">
                        <label class="wsal-rep-label-fl label-datepick"><?php _e('End Date', 'reports-wsal');?></label>
                        <div class="wsal-rep-section-fl">
                            <p class="wsal-rep-clear">
                                <input type="text" class="date-range" id="wsal-end-date" name="wsal-end-date" placeholder="<?php _e('Select end date', 'reports-wsal');?>"/>
                                <span class="description"> (<?php echo $date_format; ?>)</span>
                            </p>
                        </div>
                    </div>
                    <script type="text/javascript">
                        jQuery(document).ready(function($){
                            wsal_CreateDatePicker($, $('#wsal-start-date'), null);
                            wsal_CreateDatePicker($, $('#wsal-end-date'), null);
                        });
                    </script>
                </div>

    <!-- SECTION #3 -->
                <h4 class="wsal-reporting-subheading"><?php _e('Step 3: Select Report Format', 'reports-wsal');?></h4>

                <div class="wsal-rep-form-wrapper">
                    <div class="wsal-rep-section">
                        <div class="wsal-rep-section-fl">
                            <p class="wsal-rep-clear">
                                <input type="radio" name="wsal-rb-report-type" id="wsal-rb-type-1" value="<?php echo $wsalCommon::REPORT_HTML;?>" checked="checked" />
                                <label for="wsal-rb-type-1"><?php _e('HTML', 'reports-wsal');?></label>
                            </p>
                            <p class="wsal-rep-clear">
                                <input type="radio" name="wsal-rb-report-type" id="wsal-rb-type-2" value="<?php echo $wsalCommon::REPORT_CSV;?>"/>
                                <label for="wsal-rb-type-2"><?php _e('CSV', 'reports-wsal');?></label>
                            </p>
                        </div>
                    </div>
                </div>

    <!-- SECTION #4 -->
                <div class="wsal-rep-form-wrapper">
                    <div class="wsal-rep-section">
                        <div class="wsal-rep-section-fl">
                            <input type="submit" name="wsal-reporting-submit" id="wsal-reporting-submit" class="button-primary" value="<?php _e('Generate Report', 'reports-wsal');?>"/>
                        </div>
                    </div>
                </div>
                <?php wp_nonce_field('wsal_reporting_view_action', 'wsal_reporting_view_field'); ?>
            </form>
        </div>
        <!-- Tab Built-in Archives
        <div class="wsal-tab wrap" id="tab-archives">
        </div>-->
        <!-- Tab Built-in Summary-->
        <div class="wsal-tab wrap" id="tab-summary">
            <p style="clear:both; margin-top: 30px"></p>
            <?php
            $checked = array();
            if (!empty($summary)) {
                $checked = $summary->viewState;
                $email = $summary->email;
            }
            ?>
            <form id="wsal-summary-form" method="post">
                <span class="description"><?php _e('Use this node to configure the summary reports you would like to receive via email automatically every week or month.', 'reports-wsal');?></span>
                <h4><?php _e('Activity Summary report', 'reports-wsal');?></h4>
                <h4 class="wsal-reporting-subheading"><?php _e('Step 1: Select Frequency of Report(s)', 'reports-wsal');?></h4>
                <span class="description"><?php _e('Notifications will be send on the first day of the week/month.', 'reports-wsal');?></span>
                <div class="wsal-rep-form-wrapper">
                    <div class="wsal-rep-section">
                        <div class="wsal-rep-section-fl">
                            <p class="wsal-rep-clear">
                                <input type="radio" name="wsal-frequency" id="wsal-frequency-1" value="<?php echo $wsalCommon::REPORT_WEEKLY;?>" checked="checked" />
                                <label for="wsal-frequency-1"><?php _e('weekly', 'reports-wsal');?></label>
                            </p>
                            <p class="wsal-rep-clear">
                                <input type="radio" name="wsal-frequency" id="wsal-frequency-2" value="<?php echo $wsalCommon::REPORT_MONTHLY;?>" 
                                <?php if (!empty($summary->frequency) && ($wsalCommon::REPORT_MONTHLY == $summary->frequency)) { echo 'checked="checked"'; } ?> />
                                <label for="wsal-frequency-2"><?php _e('monthly', 'reports-wsal');?></label>
                            </p>
                        </div>
                    </div>
                </div>
                <h4 class="wsal-reporting-subheading"><?php _e('Step 2: Select Report Type(s)', 'reports-wsal');?></h4>
                <div class="wsal-rep-form-wrapper">
                    <div class="wsal-rep-section">
                        <label class="wsal-rep-label-fl"><?php _e('Summary Email Notifications', 'reports-wsal');?></label>
                        <div class="wsal-rep-section-fl">
                            <fieldset>
                                <label for="notification_1">
                                    <input type="checkbox" name="notification_1" id="notification_1" style="margin-top: 2px;" <?php if (in_array("notification_1", $checked)) { echo 'checked'; } ?>>
                                   <span><?php _e('Send me a list of logins in the last week/month', 'reports-wsal'); ?></span>
                                </label>
                                <br/>
                                <label for="notification_2">
                                    <?php $notification_2 = array_intersect(array("known", "unknown", "both"), $checked); ?>
                                    <input type="checkbox" name="notification_2" id="notification_2" style="margin-top: 2px;" <?php if (count($notification_2) > 0) { echo 'checked'; } ?>>
                                    <span><?php _e('Send me a list of failed logins in the last week/month for:', 'reports-wsal'); ?></span>
                                </label>
                                <br/>
                                <div class="sub-options">
                                    <label for="both">
                                        <input type="radio" name="usersType" id="both" style="margin: 2px;" checked="checked" value="both">
                                        <span><?php _e('All failed login attempts', 'reports-wsal'); ?></span>
                                    </label><br/>
                                    <label for="option_known">
                                        <input type="radio" name="usersType" id="option_known" style="margin: 2px;" <?php if (in_array("known", $checked)) { echo 'checked="checked"'; } ?> value="known">
                                        <span><?php _e('Failed login attempts of existing usernames (Alert 1002)', 'reports-wsal'); ?></span>
                                    </label><br/>
                                    <label for="option_unknown">
                                        <input type="radio" name="usersType" id="option_unknown" style="margin: 2px;" <?php if (in_array("unknown", $checked)) { echo 'checked="checked"'; } ?> value="unknown">
                                        <span><?php _e('Failed login attempts of Unknown usernames (Alert 1003)', 'reports-wsal'); ?></span>
                                    </label>
                                </div>
                                <label for="notification_3">
                                    <input type="checkbox" name="notification_3" id="notification_3" style="margin-top: 2px;" <?php if (in_array("notification_3", $checked)) { echo 'checked'; } ?>>
                                    <span><?php _e('Send me a list of content that was published in the last week/month', 'reports-wsal'); ?></span>
                                </label>
                                <br/>
                                <label for="notification_4">
                                    <input type="checkbox" name="notification_4" id="notification_4" style="margin-top: 2px;" <?php if (in_array("notification_4", $checked)) { echo 'checked'; } ?>>
                                    <span><?php _e('Send me a list of password changes in the last week/month (both alerts 4003 and 4004)', 'reports-wsal'); ?></span>
                                </label>
                                <br/>
                                <label for="notification_5">
                                    <input type="checkbox" name="notification_5" id="notification_5" style="margin-top: 2px;" <?php if (in_array("notification_5", $checked)) { echo 'checked'; } ?>>
                                    <span><?php _e('Send me a list of new created users in the last week/month (alert 4000 and 4012)', 'reports-wsal'); ?></span>
                                </label>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <!--// BY SITE -->
                    <div class="wsal-rep-section">
                        <label class="wsal-rep-label-fl"><?php _e('By Site(s)', 'reports-wsal');?></label>
                        <div class="wsal-rep-section-fl">
                            <p class="wsal-rep-clear">
                                <input type="radio" name="wsal-sum-sites" id="wsal-sum-sites-1" value="1" <?php if (empty($summary->sites)) { echo 'checked="checked"'; } ?>/>
                                <label for="wsal-sum-sites-1"><?php _e('All Sites', 'reports-wsal');?></label>
                            </p>
                            <p class="wsal-rep-clear">
                                <input type="radio" name="wsal-sum-sites" id="wsal-sum-sites-2" value="2" <?php if (!empty($summary->sites)) { echo 'checked="checked"'; } ?>/>
                                <label for="wsal-sum-sites-2"><?php _e('Specify sites', 'reports-wsal');?></label>
                                <input type="hidden" name="wsal-summary-sites" id="wsal-summary-sites"/>
                            </p>
                        </div>
                    </div>
                <h4 class="wsal-reporting-subheading"><?php _e('Step 3: Select Report Format', 'reports-wsal');?></h4>
                <div class="wsal-rep-form-wrapper">
                    <div class="wsal-rep-section">
                        <div class="wsal-rep-section-fl">
                            <p class="wsal-rep-clear">
                                <input type="radio" name="wsal-summary-type" id="wsal-summary-type-1" value="<?php echo $wsalCommon::REPORT_HTML;?>" checked="checked" />
                                <label for="wsal-summary-type-1"><?php _e('HTML', 'reports-wsal');?></label>
                            </p>
                            <p class="wsal-rep-clear">
                                <input type="radio" name="wsal-summary-type" id="wsal-summary-type-2" value="<?php echo $wsalCommon::REPORT_CSV;?>" 
                                <?php if (!empty($summary->type) && ($wsalCommon::REPORT_CSV == $summary->type)) { echo 'checked="checked"'; } ?> />
                                <label for="wsal-summary-type-2"><?php _e('CSV', 'reports-wsal');?></label>
                            </p>
                        </div>
                    </div>
                </div>
                <h4 class="wsal-reporting-subheading"><?php _e('Step 4: Specify Recepient(s)', 'reports-wsal');?></h4>
                <div class="wsal-rep-form-wrapper">
                    <div class="wsal-rep-section">
                        <label class="wsal-rep-label-fl"><?php _e('Email address(es):', 'reports-wsal');?></label>
                        <div class="wsal-rep-section-fl">
                            <input type="text" id="wsal-notif-email" style="min-width:350px;" name="wsal-notif-email" placeholder="Email *" value="<?php if (!empty($email)) { echo  $email; } ?>" required>
                        </div>
                    </div>
                </div>
                <div class="wsal-rep-form-wrapper">
                    <div class="wsal-rep-section">
                        <div class="wsal-rep-section-fl">
                            <?php $include = $this->_plugin->reporting->common->GetOptionByName('include-archive'); ?>
                            <fieldset>
                                <label for="include-archive">
                                    <input type="checkbox" name="include-archive" id="include-archive" <?php if ($include) { echo 'checked'; } ?>>
                                    <span><?php _e('Include Alerts from Archive Database', 'reports-wsal'); ?></span>
                                </label>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="wsal-rep-form-wrapper">
                    <div class="wsal-rep-section">
                        <div class="wsal-rep-section-fl">
                            <input type="submit" id="wsal-submit" name="wsal-notifications-submit" value="Save Notification(s)" class="button-primary">
                        </div>
                        <div class="wsal-rep-section-fl" style="margin-left:15px;">
                            <input type="submit" id="wsal-submit-now" name="wsal-notifications-submit-now" value="Generate Now" class="button-primary">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
<script type="text/javascript">
    jQuery(document).ready(function($){
        $('#wsal-rep-form').on('submit', function(){
            //#! Sites
            var e = $('#wsal-rep-sites').val();
            if(!$('#wsal-rb-sites-1').is(':checked')){
                if(!e.length){
                    alert("<?php _e('Please specify at least one site', 'reports-wsal');?>");
                    return false;
                }
            }

            //#! Users
            if(!$('#wsal-rb-users-1').is(':checked')){
                e = $('#wsal-rep-users').val();
                if(!e.length){
                    alert("<?php _e('Please specify at least one user', 'reports-wsal');?>");
                    return false;
                }
            }

            //#! Roles
            if(!$('#wsal-rb-roles-1').is(':checked')){
                e = $('#wsal-rep-roles').val();
                if(!e.length){
                    alert("<?php _e('Please specify at least one role', 'reports-wsal');?>");
                    return false;
                }
            }

            //#! IP addresses
            if(!$('#wsal-rb-ip-addresses-1').is(':checked')){
                e = $('#wsal-rep-ip-addresses').val();
                if(!e.length){
                    alert("<?php _e('Please specify at least one IP address', 'reports-wsal');?>");
                    return false;
                }
            }

            //#! Alert groups
            if((!$('#wsal-rb-groups').is(':checked') && !$('.wsal-js-groups:checked').length)){
                if(!$('#wsal-rep-alert-codes').val().length){
                    alert("<?php _e('Please specify at least one Alert group or specify an Alert code', 'reports-wsal');?>");
                    return false;
                }
            }
            
            return true;
        });

        $("#wsal-summary-sites").select2({
            data: JSON.parse('<?php echo $wsalRepSites;?>'),
            placeholder: "<?php _e('Select site(s)');?>",
            minimumResultsForSearch: 10,
            multiple: true,
        }).on('select2-open',function(e){
            var v = $(this).val();
            if(!v.length){
                $('#wsal-sum-sites-2').prop('checked', true);
            }
        }).on('select2-removed', function(){
            var v = $(this).val();
            if(!v.length){
                $('#wsal-sum-sites-1').prop('checked',true);
            }
        }).on('select2-close', function(){
            var v = $(this).val();
            if(!v.length){
                $('#wsal-sum-sites-1').prop('checked',true);
            }
        });
        <?php if (!empty($summary->sites)) { ?>
            $("#wsal-summary-sites").select2('val', <?php echo json_encode($summary->sites)?>);
        <?php } ?>
    });
</script>
