<?php if (!class_exists('WSAL_Ext_Plugin')) { exit('You are not allowed to view this page.'); }

/**
 * Main plugin view
 */
class WSAL_Ext_Settings extends WSAL_AbstractView
{
    const QUERY_LIMIT = 100;

    public function __construct(WpSecurityAuditLog $plugin)
    {
        parent::__construct($plugin);
        add_action('admin_notices', array($this, 'WsalDBExtensionPlugin'));
        add_action('network_admin_notices', array($this, 'WsalDBExtensionPlugin'));

        add_action('wp_ajax_MigrateOccurrence', array($this, 'MigrateOccurrence'));
        add_action('wp_ajax_MigrateMeta', array($this, 'MigrateMeta'));
        add_action('wp_ajax_MigrateBackOccurrence', array($this, 'MigrateBackOccurrence'));
        add_action('wp_ajax_MigrateBackMeta', array($this, 'MigrateBackMeta'));

        add_action('wp_ajax_MirroringNow', array($this, 'MirroringNow'));
        add_action('wp_ajax_ArchivingNow', array($this, 'ArchivingNow'));
        $this->RegisterNotice('external-db-wsal');
    }

    public function WsalDBExtensionPlugin()
    {
        if (is_main_site()) {
            $licenseValid = $this->_plugin->licensing->IsLicenseValid('external-db-wsal.php');
            $class = $this->_plugin->views->FindByClassName('WSAL_Views_Licensing');
            if (false === $class) {
                $class = new WSAL_Views_Licensing($this->_plugin);
            }
            $licensingPageUrl = esc_attr($class->GetUrl());
            if (!$this->IsNoticeDismissed('external-db-wsal') && !$licenseValid) {
                ?><div class="updated" data-notice-name="external-db-wsal">
                <p><?php _e(sprintf('Remember to <a href="%s">enter your plugin license code</a> for the <strong>External Database</strong>,
                                to benefit from updates and support.', $licensingPageUrl), 'external-db-wsal');?>
                    &nbsp;&nbsp;&nbsp;<a href="javascript:;" class="wsal-dismiss-notification"><?php _e('Dismiss this notice', 'external-db-wsal'); ?></a></p>
                </div><?php
            }
        }
    }

    public function GetTitle()
    {
        return __('External Database Configuration', 'external-db-wsal');
    }

    public function GetIcon()
    {
        return 'dashicons-admin-generic';
    }

    public function GetName()
    {
        return __('External DB', 'external-db-wsal');
    }

    public function GetWeight()
    {
        return 10;
    }

    public function Header()
    {
        $pluginPath = plugins_url(basename(realpath(dirname(__FILE__).'/../')));
        wp_enqueue_style('wsal-jq-timepick-css', $pluginPath.'/js/jquery.timeentry/jquery.timeentry.css');
        wp_enqueue_style('wsal-external-css', $pluginPath.'/css/styles.css');
        wp_enqueue_script('wsal-jq-plugin-js', $pluginPath.'/js/jquery.timeentry/jquery.plugin.min.js', array('jquery'));
        wp_enqueue_script('wsal-jq-timepick-js', $pluginPath.'/js/jquery.timeentry/jquery.timeentry.min.js', array('jquery'));
    }

    public function Footer()
    {
        $pluginPath = plugins_url(basename(realpath(dirname(__FILE__).'/../')));
        ?>
        <script type="text/javascript">
            var query_limit = <?php echo self::QUERY_LIMIT; ?>;
            var is_24_hours = <?php echo json_encode($this->_plugin->wsalCommonClass->IsTime24Hours()); ?>;

            jQuery(document).ready(function() {
                var archivingConfig = <?php echo json_encode($this->_plugin->wsalCommonClass->IsArchivingEnabled()); ?>;
                var archiving_status = jQuery('#archiving_status');
                var archivingTxtNot = jQuery('#archiving_status_text');

                function wsalArchivingStatus(checkbox, label){
                    if (checkbox.prop('checked')) {
                        label.text('On');
                        jQuery('#ArchiveName').prop('required', true);
                        jQuery('#ArchiveUser').prop('required', true);
                        jQuery('#ArchiveHostname').prop('required', true);
                    } else {
                        label.text('Off');
                        jQuery('#ArchiveName').prop('required', false);
                        jQuery('#ArchiveUser').prop('required', false);
                        jQuery('#ArchiveHostname').prop('required', false);
                    }
                }
                // Set On
                if (archivingConfig) {
                    archiving_status.prop('checked', true);
                }
                wsalArchivingStatus(archiving_status, archivingTxtNot);

                archiving_status.on('change', function() { 
                    wsalArchivingStatus(archiving_status, archivingTxtNot); 
                });

                var mirroringConfig = <?php echo json_encode($this->_plugin->wsalCommonClass->IsMirroringEnabled()); ?>;
                var mirroring_status = jQuery('#mirroring_status');
                var mirroringTxtNot = jQuery('#mirroring_status_text');

                function wsalMirroringStatus(checkbox, label){
                    if (checkbox.prop('checked')) {
                        label.text('On');
                    } else {
                        label.text('Off');
                    }
                }
                // Set On
                if (mirroringConfig) {
                    mirroring_status.prop('checked', true);
                }
                wsalMirroringStatus(mirroring_status, mirroringTxtNot);

                mirroring_status.on('change', function() { 
                    wsalMirroringStatus(mirroring_status, mirroringTxtNot); 
                });

                // Show/Hide Mirroring type 
                var checked = jQuery('input:radio[name=MirroringType]:checked').val();
                jQuery("#" + checked).show();
                setRequired(checked);
                
                jQuery('input:radio[name=MirroringType]').click(function() {
                    var selected = jQuery(this).val();
                    jQuery("tbody.desc").hide();
                    jQuery("#" + selected).show(200);
                    setRequired(selected);
                });

                function setRequired(mirroring_type){
                    if (mirroring_type == "database") {
                        jQuery('#MirrorName').prop('required', true);
                        jQuery('#MirrorUser').prop('required', true);
                        jQuery('#MirrorHostname').prop('required', true);
                        jQuery('#Papertrail').prop('required', false);
                    } else if (mirroring_type == "papertrail") {
                        jQuery('#MirrorName').prop('required', false);
                        jQuery('#MirrorUser').prop('required', false);
                        jQuery('#MirrorHostname').prop('required', false);
                        jQuery('#Papertrail').prop('required', true);
                    } else {
                        jQuery('#MirrorName').prop('required', false);
                        jQuery('#MirrorUser').prop('required', false);
                        jQuery('#MirrorHostname').prop('required', false);
                        jQuery('#Papertrail').prop('required', false);
                    }
                }
            });
        </script>
        <?php
        wp_enqueue_script('wsal-external-js', $pluginPath.'/js/wsal-external.js', array('jquery'));
    }

    protected function Save()
    {
        /* Save External Adapter config */
        if (!empty($_REQUEST["AdapterUser"]) && ($_REQUEST['AdapterUser'] != '') && ($_REQUEST['AdapterName'] != '') && ($_REQUEST['AdapterHostname'] != '')) {
            $adapterType       = trim($_REQUEST['AdapterType']);
            $adapterUser       = trim($_REQUEST['AdapterUser']);
            $adapterName       = trim($_REQUEST['AdapterName']);
            $adapterHostname   = trim($_REQUEST['AdapterHostname']);
            $adapterBasePrefix = trim($_REQUEST['AdapterBasePrefix']);
            $password = $this->_plugin->wsalCommonClass->EncryptPassword(trim($_REQUEST['AdapterPassword']));
            WSAL_Connector_ConnectorFactory::CheckConfig($adapterType, $adapterUser, $password, $adapterName, $adapterHostname, $adapterBasePrefix);

            /* Setting External Adapter DB config */
            $this->_plugin->wsalCommonClass->AddGlobalOption('adapter-type', $adapterType);
            $this->_plugin->wsalCommonClass->AddGlobalOption('adapter-user', $adapterUser);
            $this->_plugin->wsalCommonClass->AddGlobalOption('adapter-password', $password);
            $this->_plugin->wsalCommonClass->AddGlobalOption('adapter-name', $adapterName);
            $this->_plugin->wsalCommonClass->AddGlobalOption('adapter-hostname', $adapterHostname);
            $this->_plugin->wsalCommonClass->AddGlobalOption('adapter-base-prefix', $adapterBasePrefix);
            
            $plugin = new WpSecurityAuditLog();
            $config = WSAL_Connector_ConnectorFactory::GetConfigArray($adapterType, $adapterUser, $password, $adapterName, $adapterHostname, $adapterBasePrefix);

            $plugin->getConnector($config)->installAll(true);
        } else if (isset($_REQUEST["Archiving"])) {
            /* Save Archiving */
            $this->_plugin->wsalCommonClass->SetArchivingEnabled(isset($_REQUEST['SetArchiving']));
            $this->_plugin->wsalCommonClass->SetArchivingStop(isset($_REQUEST['StopArchiving']));
            if (isset($_REQUEST['RunArchiving'])) {
                $this->_plugin->wsalCommonClass->SetArchivingRunEvery($_REQUEST['RunArchiving']);
                // Reset old archiving cron job
                wp_clear_scheduled_hook('run_archiving');
            }
            if (!empty($_REQUEST["ArchiveUser"]) && ($_REQUEST['ArchiveUser'] != '') && ($_REQUEST['ArchiveName'] != '') && ($_REQUEST['ArchiveHostname'] != '')) {
                $archiveType       = trim($_REQUEST['ArchiveType']);
                $archiveUser       = trim($_REQUEST['ArchiveUser']);
                $archiveName       = trim($_REQUEST['ArchiveName']);
                $archiveHostname   = trim($_REQUEST['ArchiveHostname']);
                $archiveBasePrefix = trim($_REQUEST['ArchiveBasePrefix']);
                $password = $this->_plugin->wsalCommonClass->EncryptPassword(trim($_REQUEST['ArchivePassword']));
                WSAL_Connector_ConnectorFactory::CheckConfig($archiveType, $archiveUser, $password, $archiveName, $archiveHostname, $archiveBasePrefix);

                /* Setting Archive DB config */
                $this->_plugin->wsalCommonClass->AddGlobalOption('archive-type', $archiveType);
                $this->_plugin->wsalCommonClass->AddGlobalOption('archive-user', $archiveUser);
                $this->_plugin->wsalCommonClass->AddGlobalOption('archive-password', $password);
                $this->_plugin->wsalCommonClass->AddGlobalOption('archive-name', $archiveName);
                $this->_plugin->wsalCommonClass->AddGlobalOption('archive-hostname', $archiveHostname);
                $this->_plugin->wsalCommonClass->AddGlobalOption('archive-base-prefix', $archiveBasePrefix);

                $this->_plugin->wsalCommonClass->SetArchivingDateEnabled($_REQUEST['ArchiveBy'] == 'date');
                $this->_plugin->wsalCommonClass->SetArchivingLimitEnabled($_REQUEST['ArchiveBy'] == 'limit');
                if ($_REQUEST['ArchiveBy'] == 'date') {
                    $this->_plugin->wsalCommonClass->SetArchivingDate($_REQUEST['ArchivingDate']);
                    $this->_plugin->wsalCommonClass->SetArchivingDateType($_REQUEST['DateType']);
                } else {
                    $this->_plugin->wsalCommonClass->SetArchivingLimit($_REQUEST['ArchivingLimit']);
                }

                $plugin = new WpSecurityAuditLog();
                $config = WSAL_Connector_ConnectorFactory::GetConfigArray($archiveType, $archiveUser, $password, $archiveName, $archiveHostname, $archiveBasePrefix);
                    
                $plugin->getConnector($config)->installAll(true);
            }
        } else if (isset($_REQUEST["Mirroring"])) {
            /* Save Mirroring */
            $this->_plugin->wsalCommonClass->SetMirroringEnabled(isset($_REQUEST['SetMirroring']));
            $this->_plugin->wsalCommonClass->SetMirroringStop(isset($_REQUEST['StopMirroring']));
            if (isset($_REQUEST['RunMirroring'])) {
                $this->_plugin->wsalCommonClass->SetMirroringRunEvery($_REQUEST['RunMirroring']);
                // Reset old mirroring cron job
                wp_clear_scheduled_hook('run_mirroring');
            }
            if (isset($_REQUEST['MirroringType']) && $_REQUEST['MirroringType'] == 'database') {
                if (!empty($_REQUEST["MirrorUser"]) && ($_REQUEST['MirrorUser'] != '') && ($_REQUEST['MirrorName'] != '') && ($_REQUEST['MirrorHostname'] != '')) {
                    $mirrorType       = trim($_REQUEST['MirrorType']);
                    $mirrorUser       = trim($_REQUEST['MirrorUser']);
                    $mirrorName       = trim($_REQUEST['MirrorName']);
                    $mirrorHostname   = trim($_REQUEST['MirrorHostname']);
                    $mirrorBasePrefix = trim($_REQUEST['MirrorBasePrefix']);
                    $password = $this->_plugin->wsalCommonClass->EncryptPassword(trim($_REQUEST['MirrorPassword']));
                    WSAL_Connector_ConnectorFactory::CheckConfig($mirrorType, $mirrorUser, $password, $mirrorName, $mirrorHostname, $mirrorBasePrefix);

                    /* Setting Archive DB config */
                    $this->_plugin->wsalCommonClass->AddGlobalOption('mirror-type', $mirrorType);
                    $this->_plugin->wsalCommonClass->AddGlobalOption('mirror-user', $mirrorUser);
                    $this->_plugin->wsalCommonClass->AddGlobalOption('mirror-password', $password);
                    $this->_plugin->wsalCommonClass->AddGlobalOption('mirror-name', $mirrorName);
                    $this->_plugin->wsalCommonClass->AddGlobalOption('mirror-hostname', $mirrorHostname);
                    $this->_plugin->wsalCommonClass->AddGlobalOption('mirror-base-prefix', $mirrorBasePrefix);

                    $this->_plugin->wsalCommonClass->SetMirroringType($_REQUEST['MirroringType']);

                    $plugin = new WpSecurityAuditLog();
                    $config = WSAL_Connector_ConnectorFactory::GetConfigArray($mirrorType, $mirrorUser, $password, $mirrorName, $mirrorHostname, $mirrorBasePrefix);
                        
                    $plugin->getConnector($config)->installAll(true);
                }
            } else if (isset($_REQUEST['MirroringType']) && $_REQUEST['MirroringType'] == 'papertrail') {
                if (!empty($_REQUEST["Papertrail"]) && ($_REQUEST['Papertrail'] != '')) {
                    $this->_plugin->wsalCommonClass->SetMirroringType($_REQUEST['MirroringType']);
                    $papertrail = trim($_REQUEST['Papertrail']);
                    $this->_plugin->wsalCommonClass->SetPapertrailDestination($papertrail);
                    $this->_plugin->wsalCommonClass->SetPapertrailColorization(isset($_REQUEST['Colorization']));
                }
            } else if (isset($_REQUEST['MirroringType']) && $_REQUEST['MirroringType'] == 'syslog') {
                $this->_plugin->wsalCommonClass->SetMirroringType($_REQUEST['MirroringType']);
            }
        }
    }

    protected function CheckIfTableExist()
    {
        return $this->_plugin->wsalCommonClass->IsInstalled();
    }

    protected function CheckSetting()
    {
        $config = $this->_plugin->settings->GetAdapterConfig('adapter-type');
        if (!empty($config)) {
            return true;
        } else {
            return false;
        }
    }
    
    public function MigrateMeta()
    {
        $limit = self::QUERY_LIMIT;
        $index = intval($_POST['index']);
        $response = $this->_plugin->wsalCommonClass->MigrateMeta($index, $limit);
        echo json_encode($response);
        exit;
    }
    public function MigrateOccurrence()
    {
        $limit = self::QUERY_LIMIT;
        $index = intval($_POST['index']);
        $response = $this->_plugin->wsalCommonClass->MigrateOccurrence($index, $limit);
        echo json_encode($response);
        exit;
    }

    public function MigrateBackMeta()
    {
        $limit = self::QUERY_LIMIT;
        $index = intval($_POST['index']);
        $response = $this->_plugin->wsalCommonClass->MigrateBackMeta($index, $limit);
        echo json_encode($response);
        exit;
    }
    public function MigrateBackOccurrence()
    {
        $limit = self::QUERY_LIMIT;
        $index = intval($_POST['index']);
        $response = $this->_plugin->wsalCommonClass->MigrateBackOccurrence($index, $limit);
        echo json_encode($response);
        exit;
    }

    public function MirroringNow()
    {
        $this->_plugin->wsalCommonClass->mirroring_alerts();
        exit;
    }

    public function ArchivingNow()
    {
        $this->_plugin->wsalCommonClass->archiving_alerts();
        exit;
    }

    public function Render()
    {
        if (!$this->_plugin->settings->CurrentUserCan('edit')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'external-db-wsal'));
        }
        
        if (isset($_POST['submit'])) {
            try {
                $this->Save();
                ?><div class="updated">
                    <p><?php _e('Settings have been saved.', 'external-db-wsal'); ?></p>
                </div><?php
            } catch (Exception $ex) {
                ?><div class="error"><p><?php _e('Error: ', 'external-db-wsal'); ?><?php echo $ex->getMessage(); ?></p></div><?php
            }
        } else {
            $pluginPath = plugins_url(basename(realpath(dirname(__FILE__).'/../')));
            ?>
            <div id="ajax-response" class="notice hidden">
                <img src="<?=$pluginPath?>/css/default.gif">
                <p>
                    <?php _e('Please do not close this window while migrating alerts.', 'external-db-wsal'); ?>
                    <span id="ajax-response-counter"></span>
                </p>
            </div>
            <?php
        }
        ?>
        <div id="wsal-external-db">
            <h2 id="wsal-tabs" class="nav-tab-wrapper">
                <a href="#external" class="nav-tab"><?php _e('External Database', 'external-db-wsal');?></a>
                <a href="#mirroring" class="nav-tab"><?php _e('Mirroring', 'external-db-wsal');?></a>
                <a href="#archiving" class="nav-tab"><?php _e('Archiving', 'external-db-wsal');?></a>
            </h2>
            <div class="nav-tabs">
                <!-- Tab External Database -->
                <table class="form-table wsal-tab" id="external">
                    <form method="post" autocomplete="off">
                        <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']); ?>" />
                        <tbody class="widefat">
                            <tr>
                                <td colspan="2"><?php _e('Configure the database connection details below to store the WordPress audit log in an external database and not in the WordPress database.', 'external-db-wsal'); ?></td>
                            </tr>
                            <!-- Adapter Database Configuration -->
                            <?php
                                echo $this->getDatabaseFields('adapter');
                            ?>
                            <tr>
                                <th><label for="Current"><?php _e('Current Connection Details', 'external-db-wsal'); ?></label></th>
                                <td>
                                    <?php $adapterName = $this->_plugin->settings->GetAdapterConfig('adapter-name'); ?>
                                    <?php $adapterHostname = $this->_plugin->settings->GetAdapterConfig('adapter-hostname'); ?>
                                    <span class="current-connection"><?php _e('Currently Connected to database', 'external-db-wsal'); ?> 
                                    <strong><?=(!empty($adapterName)? $adapterName : 'Default')?></strong> 
                                    on server <strong><?=(!empty($adapterHostname)? $adapterHostname : 'Current')?></strong></span>
                                </td>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr>
                                <td colspan="2">
                                    <input type="submit" name="submit" id="submit" class="button button-primary" value="Save &amp; Test Changes">
                                </td>
                            </tr>
                            <?php
                            if ($this->CheckIfTableExist() && $this->CheckSetting()) {
                                $disabled = "";
                            } else {
                                $disabled = "disabled";
                            }
                            ?>
                            <tr>
                                <td colspan="2">
                                    <input type="button" name="wsal-migrate" id="wsal-migrate" class="button button-primary" value="Migrate Alerts from WordPress Database" <?=$disabled?>>
                                    <span class="description">
                                        <?php _e('Migrate existing WordPress Security Alerts from the WordPress database to the new external database.', 'external-db-wsal'); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php
                            if (!$this->CheckSetting()) {
                                $disabled = "disabled";
                            } else {
                                $disabled = "";
                            }
                            ?>
                            <tr>
                                <td colspan="2">
                                    <input type="button" name="wsal-migrate-back" id="wsal-migrate-back" class="button button-primary" value="Switch to WordPress Database" <?=$disabled?>>
                                    <span class="description">
                                        <?php _e('Remove the external database and start using the WordPress database again. In the process the alerts will be automatically migrated to the WordPress database.', 'external-db-wsal'); ?>
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </form>
                </table>
                <!-- Tab Mirroring -->
                <table class="form-table wsal-tab" id="mirroring">
                    <form method="post" autocomplete="off">
                        <input type="hidden" name="Mirroring" value="1">
                        <tbody class="widefat">
                            <tr>
                                <td colspan="2">
                                <?php _e('When you enable this option the WordPress audit trail will be mirrored to the configured database / data source.', 'external-db-wsal'); ?><br>
                                <?php _e('By mirroring the audit trail you ensure that you always have a backup copy of the audit trail and also ensure that the audit trail is not tampered with in the unfortunate event of an attack.', 'external-db-wsal'); ?></td>
                            </tr>
                            <tr>
                                <th><label for="Mirroring"><?php _e('Enable Mirroring', 'external-db-wsal'); ?></label></th>
                                <td>
                                    <fieldset>
                                        <label for="Mirroring">
                                            <span class="f-container">
                                                <span class="f-left">
                                                    <input type="checkbox" name="SetMirroring" value="1" class="switch" id="mirroring_status"/>
                                                    <label for="mirroring_status"></label>
                                                </span>
                                                <span class="f-right f-text"><span id="mirroring_status_text"></span></span>
                                            </span>
                                        </label>
                                    </fieldset>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="options"><?php _e('Mirroring options', 'external-db-wsal'); ?></label></th>
                                <td>
                                    <?php $type = $this->_plugin->wsalCommonClass->GetMirroringType(); ?>
                                    <fieldset>
                                        <p><input id="mirroring_db" type="radio" name="MirroringType" value="database" <?php if($type == "database")echo 'checked="checked"'; ?>>
                                        <label for="mirroring_db">Database</label> <?php if($type == "database")echo '(Configured and working)'; ?></p>
                                        <p><input id="mirroring_papertrail" type="radio" name="MirroringType" value="papertrail" <?php if($type == "papertrail")echo 'checked="checked"'; ?>>
                                        <label for="mirroring_papertrail">Papertrail</label> <?php if($type == "papertrail")echo '(Configured and working)'; ?></p>
                                        <p><input id="mirroring_syslog" type="radio" name="MirroringType" value="syslog" <?php if($type == "syslog")echo 'checked="checked"'; ?>>
                                        <label for="mirroring_syslog">SysLog</label> <?php if($type == "syslog")echo '(Configured and working)'; ?></p>
                                    </fieldset>
                                </td>
                        </tbody>
                        <tbody id="database" class="widefat desc" style="display:none">
                            <!-- Mirroring Database Configuration -->
                            <?php
                                echo $this->getDatabaseFields('mirror');
                            ?>
                        </tbody>
                        <tbody id="papertrail" class="widefat desc" style="display:none">
                            <!-- Papertrail Configuration -->
                            <tr>
                                <td colspan="2">
                                    <?php _e('Configure the below options to mirror the WordPress audit trail to Papertrail.', 'external-db-wsal'); ?>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="Papertrail"><?php _e('Destination', 'external-db-wsal'); ?></label></th>
                                <td>
                                    <fieldset>
                                        <?php $destination = $this->_plugin->wsalCommonClass->GetPapertrailDestination(); ?>
                                        <input type="text" id="Papertrail" name="Papertrail" value="<?php echo $destination; ?>" style="display: block; width: 250px;">
                                        <span class="description">
                                            <?php _e('Specify your destination. You can find your Papertrail Destination in the', 'external-db-wsal'); ?>
                                            &nbsp;<a href="https://papertrailapp.com/account/destinations" target="_blank">Log Destinations</a>&nbsp;
                                            <?php _e('section of your Papertrail account page. ', 'external-db-wsal'); ?><br>
                                            <?php _e('It should have the following format:', 'external-db-wsal'); ?>
                                            &nbsp;<strong>logs4.papertrailapp.com:54321</strong>
                                        </span>
                                    </fieldset>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="Colorization"><?php _e('Colorization', 'external-db-wsal'); ?></label></th>
                                <td>
                                    <fieldset>
                                        <label for="Colorization">
                                            <input type="checkbox" name="Colorization" value="1" id="Colorization"<?php
                                                if ($this->_plugin->wsalCommonClass->IsPapertrailColorizationEnabled()) echo ' checked="checked"';
                                            ?>/> <?php _e('Enable', 'external-db-wsal'); ?>
                                        </label>
                                    </fieldset>
                                </td>
                            </tr>
                        </tbody>
                        <tbody id="syslog" class="widefat desc" style="display:none">
                            <!-- SysLog Nothing to config -->
                        </tbody>
                        <tbody class="widefat">
                            <?php
                                echo $this->getScheduleFields('mirroring');
                            ?>
                        </tbody>
                        <tbody>
                            <?php
                            if (!$this->_plugin->wsalCommonClass->IsMirroringEnabled()) {
                                $disabled = "disabled";
                            } else {
                                $disabled = "";
                            }
                            ?>
                            <tr>
                                <td colspan="2">
                                    <input type="submit" name="submit" class="button button-primary" value="Save Changes">
                                    <input type="button" style="margin-left: 20px;" id="wsal-mirroring" class="button button-primary" value="Execute Mirroring Now" <?=$disabled?>>
                                </td>
                            </tr>
                        </tbody>
                    </form>
                </table>
                <!-- Tab Archiving -->
                <table class="form-table wsal-tab" id="archiving">
                    <form method="post" autocomplete="off">
                        <input type="hidden" name="Archiving" value="1">
                        <tbody class="widefat">
                            <tr>
                                <td colspan="2">
                                <?php _e('When you enable archiving you can archive a number of alerts from the main database to the archiving database.', 'external-db-wsal'); ?><br>
                                <?php _e('This means that there will be less alerts in the main database, therefore tasks such as searching will be much faster and the database will be easier to manage.', 'external-db-wsal'); ?></td>
                            </tr>
                            <tr>
                                <th><label for="Archiving"><?php _e('Enable Archiving', 'external-db-wsal'); ?></label></th>
                                <td>
                                    <fieldset>
                                        <label for="Archiving">
                                            <span class="f-container">
                                                <span class="f-left">
                                                    <input type="checkbox" name="SetArchiving" value="1" class="switch" id="archiving_status"/>
                                                    <label for="archiving_status"></label>
                                                </span>
                                                <span class="f-right f-text"><span id="archiving_status_text"></span></span>
                                            </span>
                                        </label>
                                    </fieldset>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="options"><?php _e('Archiving options', 'external-db-wsal'); ?></label></th>
                                <td>
                                    <fieldset>
                                        <?php $nbld = $this->_plugin->wsalCommonClass->IsArchivingDateEnabled(); ?>
                                        <label for="archive_date">
                                            <input type="radio" id="archive_date" name="ArchiveBy" value="date" <?php if ($nbld) {echo 'checked="checked"';} ?>>
                                            <?php echo __('Archive alerts older than', 'external-db-wsal'); ?>
                                        </label>
                                        <input type="number" name="ArchivingDate" value="<?php echo esc_attr($this->_plugin->wsalCommonClass->GetArchivingDate()); ?>">
                                        <?php $dateType = strtolower($this->_plugin->wsalCommonClass->GetArchivingDateType()); ?>
                                        <select name="DateType" class="age-type">
                                            <option value="weeks" <?php if ($dateType == 'weeks') { echo 'selected="selected"'; } ?>><?php _e('weeks', 'external-db-wsal'); ?></option>
                                            <option value="months" <?php if ($dateType == 'months') { echo 'selected="selected"'; } ?>><?php _e('months', 'external-db-wsal'); ?></option>
                                            <option value="years" <?php if ($dateType == 'years') { echo 'selected="selected"'; } ?>><?php _e('years', 'external-db-wsal'); ?></option>
                                        </select>
                                    </fieldset>
                                    <fieldset>
                                        <?php $nbld = $this->_plugin->wsalCommonClass->IsArchivingLimitEnabled(); ?>
                                        <label for="archive_limit">
                                            <input type="radio" id="archive_limit" name="ArchiveBy" value="limit" <?php if ($nbld) { echo 'checked="checked"'; } ?>>
                                            <?php echo __('Archive when audit log has more than', 'external-db-wsal'); ?>
                                        </label>
                                        <input type="number" name="ArchivingLimit" value="<?php echo esc_attr($this->_plugin->wsalCommonClass->GetArchivingLimit()); ?>">
                                        <?php echo __('alerts', 'external-db-wsal'); ?>
                                    </fieldset>
                                    <span class="description">
                                        <?php _e('The configured archiving options will override the Security Alerts Pruning settings configured in the plugin’s settings.', 'external-db-wsal'); ?>
                                    </span>
                                </td>
                            </tr>
                            <!-- Archive Database Configuration -->
                            <?php
                                echo $this->getDatabaseFields('archive');
                            ?>
                        </tbody>
                        <tbody class="widefat">
                            <?php
                                echo $this->getScheduleFields('archiving');
                            ?>
                        </tbody>
                        <tbody>
                            <?php
                            if (!$this->_plugin->wsalCommonClass->IsArchivingEnabled()) {
                                $disabled = "disabled";
                            } else {
                                $disabled = "";
                            }
                            ?>
                            <tr>
                                <td colspan="2">
                                    <input type="submit" name="submit" class="button button-primary" value="Save Changes">
                                    <input type="button" style="margin-left: 20px;" id="wsal-archiving" class="button button-primary" value="Execute Archiving Now" <?=$disabled?>>
                                </td>
                            </tr>
                        </tbody>
                    </form>
                </table>
            </div>
        </div>
        <?php
    }

    /**
     * Common function for the Database fields
     */
    private function getDatabaseFields($name)
    {
        $labelName = ucfirst($name);
        $optionName = strtolower($name);
        ?>
            <tr>
                <th><label for="<?php echo $labelName ?>Type"><?php _e('Database Type', 'external-db-wsal'); ?></label></th>
                <td>
                    <fieldset>
                        <?php $type = strtolower($this->_plugin->wsalCommonClass->GetOptionByName($optionName . '-type')); ?>
                        <select name="<?php echo $labelName ?>Type" id="<?php echo $labelName ?>Type">
                            <option value="MySQL" <?php if ($type == 'mysql') { echo 'selected="selected"'; } ?>>DB MySQL</option>
                        </select>
                        <br/>
                        <span class="description">
                            <?php _e('At the moment only MySQL server is support. Support for other different SQL sever types will be available in the future.', 'external-db-wsal'); ?>
                        </span>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th><label for="<?php echo $labelName ?>Name"><?php _e('Database Name', 'external-db-wsal'); ?></label></th>
                <td>
                    <fieldset>
                        <?php $name = $this->_plugin->wsalCommonClass->GetOptionByName($optionName . '-name'); ?>
                        <input type="text" id="<?php echo $labelName ?>Name" name="<?php echo $labelName ?>Name" value="<?php echo $name; ?>" style="display: block; width: 250px;">
                        <span class="description">
                            <?php _e('Specify the name of the database where you will store the WordPress Audit Log.', 'external-db-wsal'); ?>
                        </span>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th><label for="<?php echo $labelName ?>User"><?php _e('Database User', 'external-db-wsal'); ?></label></th>
                <td>
                    <fieldset>
                        <?php $user = $this->_plugin->wsalCommonClass->GetOptionByName($optionName . '-user'); ?>
                        <input type="text" id="A<?php echo $labelName ?>User" name="<?php echo $labelName ?>User" value="<?php echo $user; ?>" style="display: block; width: 250px;">
                        <span class="description">
                            <?php _e('Specify the username to be used to connect to the database.', 'external-db-wsal'); ?>
                        </span>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th><label for="<?php echo $labelName ?>Password"><?php _e('Database Password', 'external-db-wsal'); ?></label></th>
                <td>
                    <fieldset>
                        <input type="password" id="<?php echo $labelName ?>Password" name="<?php echo $labelName ?>Password" style="display: block; width: 250px;">
                        <span class="description">
                            <?php _e('Specify the password each time you want to submit new changes. For security reasons, the plugin does not store the password in this form.', 'external-db-wsal'); ?>
                        </span>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th><label for="<?php echo $labelName ?>Hostname"><?php _e('Database Hostname', 'external-db-wsal'); ?></label></th>
                <td>
                    <fieldset>
                        <?php $hostname = $this->_plugin->wsalCommonClass->GetOptionByName($optionName . '-hostname'); ?>
                        <input type="text" id="<?php echo $labelName ?>Hostname" name="<?php echo $labelName ?>Hostname" value="<?php echo $hostname; ?>" style="display: block; width: 250px;">
                        <span class="description">
                            <?php _e('Specify the hostname or IP address of the database server.', 'external-db-wsal'); ?>
                        </span>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th><label for="<?php echo $labelName ?>BasePrefix"><?php _e('Database Base prefix', 'external-db-wsal'); ?></label></th>
                <td>
                    <fieldset>
                        <?php
                        $basePrefix = $this->_plugin->wsalCommonClass->GetOptionByName($optionName . '-base-prefix');
                        if (empty($basePrefix)) {
                            $basePrefix = $this->_plugin->wsalCommonClass->GetOptionByName('adapter-base-prefix');
                            if (empty($basePrefix)) {
                                $basePrefix = $GLOBALS['wpdb']->base_prefix;
                            }
                        }
                        ?>
                        <input type="text" id="<?php echo $labelName ?>BasePrefix" name="<?php echo $labelName ?>BasePrefix" value="<?php echo $basePrefix; ?>" style="display: block; width: 250px;">
                        <span class="description">
                            <?php _e('Specify a prefix for the database tables of the audit log. Ideally this prefix should be different from the one you use for WordPress so it is not guessable.', 'external-db-wsal'); ?>
                        </span>
                    </fieldset>
                </td>
            </tr>
        <?php
    }

    /**
     * Common function to schedule cron job
     */
    private function getScheduleFields($name)
    {
        $labelName = ucfirst($name);
        $optionName = strtolower($name);
        $configName = 'Is' . $labelName . 'Stop';
        ?>
            <tr>
                <th><label for="Run<?php echo $labelName ?>">Run <?php echo $optionName ?> process every</label></th>
                <td>
                    <fieldset>
                        <?php
                            $name = 'Get' . $labelName . 'RunEvery';
                            $every = strtolower($this->_plugin->wsalCommonClass->$name());
                        ?>
                        <select name="Run<?php echo $labelName ?>" id="Run<?php echo $labelName ?>">
                            <option value="tenminutes" <?php if ($every == 'tenminutes') { echo 'selected="selected"'; } ?>>10 minutes</option>
                            <option value="thirtyminutes" <?php if ($every == 'thirtyminutes') { echo 'selected="selected"'; } ?>>30 minutes</option>
                            <option value="fortyfiveminutes" <?php if ($every == 'fortyfiveminutes') { echo 'selected="selected"'; } ?>>45 minutes</option>
                            <option value="hourly" <?php if ($every == 'hourly') { echo 'selected="selected"'; } ?>>1 hour</option>
                        </select>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th><label for="Stop<?php echo $labelName ?>">Stop <?php echo $labelName ?></label></th>
                <td>
                    <fieldset>
                        <label for="Stop<?php echo $labelName ?>" class="no-margin">
                            <span class="f-container">
                                <span class="f-left">
                                    <input type="checkbox" name="Stop<?php echo $labelName ?>" value="1" class="switch" id="<?php echo $optionName ?>_stop"/>
                                    <label for="<?php echo $optionName ?>_stop" class="no-margin orange"></label>
                                </span>
                            </span>
                        </label>
                        <span class="description">Current status: <strong><span id="<?php echo $optionName ?>_stop_text"></span></strong></span>
                    </fieldset>
                </td>
            </tr>
            <script type="text/javascript">
                jQuery(document).ready(function() {
                    var <?php echo $optionName ?>Stop = <?php echo json_encode($this->_plugin->wsalCommonClass->$configName()); ?>;
                    var <?php echo $optionName ?>_stop = jQuery('#<?php echo $optionName ?>_stop');
                    var <?php echo $optionName ?>TxtNot = jQuery('#<?php echo $optionName ?>_stop_text');

                    function wsal<?php echo $labelName ?>Stop(checkbox, label){
                        if (checkbox.prop('checked')) {
                            label.text('Stopped');
                        } else {
                            label.text('Running');
                        }
                    }
                    // Set On
                    if (<?php echo $optionName ?>Stop) {
                        <?php echo $optionName ?>_stop.prop('checked', true);
                    }
                    wsal<?php echo $labelName ?>Stop(<?php echo $optionName ?>_stop, <?php echo $optionName ?>TxtNot);

                    <?php echo $optionName ?>_stop.on('change', function() { 
                        wsal<?php echo $labelName ?>Stop(<?php echo $optionName ?>_stop, <?php echo $optionName ?>TxtNot); 
                    });
                });
            </script>
        <?php
    }
}
