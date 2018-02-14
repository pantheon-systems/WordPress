<?php
$cron_job_key = PMXE_Plugin::getInstance()->getOption('cron_job_key');
$urlToExport = site_url() . '/wp-cron.php?security_token=' . substr(md5($cron_job_key . $update_previous->id), 0, 16) . '&export_id=' . $update_previous->id . '&action=get_data';
$uploads = wp_upload_dir();

$bundle_path = wp_all_export_get_absolute_path($update_previous->options['bundlepath']);

if (!empty($bundle_path)) {
    $bundle_url = site_url() . '/wp-cron.php?security_token=' . substr(md5($cron_job_key . $update_previous->id), 0, 16) . '&export_id=' . $update_previous->id . '&action=get_bundle&t=zip';
}

$isImportAllowedSpecification = new \Wpae\App\Specification\IsImportAllowed();
?>
<div id="export_finished" style="padding-top: 10px;">
    <?php
    if ($isGoogleFeed) {
        ?>
        <h3><?php _e('WP All Export successfully exported your data!', 'wp_all_export_plugin'); ?></h3>
    <?php
    $cronJobKey = PMXE_Plugin::getInstance()->getOption('cron_job_key');
    include_once('google_merchants_success.php');
    } else {
    ?>
        <h2 style="color:#425f9a; font-size:24px; margin-bottom: 36px;">What's next?</h2>

        <script type="text/javascript">
            jQuery(document).ready(function () {
                jQuery('.success-tabs .tab').click(function () {
                    jQuery('.success-tabs .tab').removeClass('selected');
                    jQuery(this).addClass('selected');
                    var rel = jQuery(this).attr('rel');
                    jQuery('.tab-content').removeClass('selected');
                    jQuery('.tab-content-container').find('#' + rel).addClass('selected');
                });
            });
        </script>
        <ul class="success-tabs">
            <li rel="tab1-content" class="tab selected"><?php _e("Download", 'wp_all_export_plugin'); ?></li>
            <li rel="tab2-content" class="tab"><?php _e("Scheduling", 'wp_all_export_plugin'); ?></li>
            <li rel="tab3-content" class="tab"><?php _e("External Apps", 'wp_all_export_plugin'); ?></li>
            <?php if ($isImportAllowedSpecification->isSatisfied($update_previous)): ?>
                <li rel="tab4-content" class="tab"><?php _e("Export, Edit, Import", 'wp_all_export_plugin'); ?></li>
            <?php endif; ?>
        </ul>
    <hr style="margin-top:0;"/>
        <div class="tab-content-container">
            <div class="tab-content selected normal-tab" id="tab1-content">
                <h3 style="margin-top: 30px; margin-bottom: 30px;"><?php _e("Click to Download", 'wp_all_export_plugin'); ?></h3>
                <div class="input">
                    <button class="button button-primary button-hero wpallexport-large-button download_data"
                            rel="<?php echo add_query_arg(array('action' => 'download', 'id' => $update_previous->id, '_wpnonce' => wp_create_nonce('_wpnonce-download_feed')), $this->baseUrl); ?>"><?php echo strtoupper(wp_all_export_get_export_format($update_previous->options)); ?></button>
                    <?php if (!empty($update_previous->options['split_large_exports'])): ?>
                        <button class="button button-primary button-hero wpallexport-large-button download_data"
                                rel="<?php echo add_query_arg(array('page' => 'pmxe-admin-manage', 'id' => $update_previous->id, 'action' => 'split_bundle', '_wpnonce' => wp_create_nonce('_wpnonce-download_split_bundle')), $this->baseUrl); ?>"><?php printf(__('Split %ss', 'wp_all_export_plugin'), strtoupper(wp_all_export_get_export_format($update_previous->options))); ?></button>
                    <?php endif; ?>
                    <?php if (PMXE_Export_Record::is_bundle_supported($update_previous->options)): ?>
                        <button class="button button-primary button-hero wpallexport-large-button download_data"
                                rel="<?php echo add_query_arg(array('page' => 'pmxe-admin-manage', 'id' => $update_previous->id, 'action' => 'bundle', '_wpnonce' => wp_create_nonce('_wpnonce-download_bundle')), $this->baseUrl); ?>"><?php _e('Bundle', 'wp_all_export_plugin'); ?></button>
                    <?php endif; ?>
                </div>

                <?php if (PMXE_Export_Record::is_bundle_supported($update_previous->options)): ?>
                    <div id="download-details">
                        <p style="margin-top:30px;">
                            <?php _e("The bundle contains your exported data and a settings file for WP All Import.", 'wp_all_export_plugin'); ?><br/>
                            <?php _e("Upload the Bundle to WP All Import on another site to quickly import this data.", 'wp_all_export_plugin');?>
                        </p>
                    </div>
                <?php endif; ?>
                <div style="margin-top:30px;">
                    <h3 style="margin-bottom: 0; margin-top: -10px;"><?php echo _e("Public URL", 'wp_all_export_plugin'); ?></h3>
                    <a href="<?php echo $urlToExport; ?>" <?php if (php_sapi_name() != 'cli-server') { ?> target="_blank" <?php } ?>
                       class="feed-url" style="margin-bottom: 0; font-size: 16px;"><?php echo $urlToExport; ?></a>
                    <p style="margin-top: 0;">
                        <?php _e("This URL will always provide the export file from this export, even if the file name changes.", 'wp_all_export_plugin'); ?>
                    </p>
                </div>
            </div>
            <div class="tab-content scheduling" id="tab2-content">
                <div class="wrap" style="text-align: left; padding-top: 10px;">

                <?php
                $export = $update_previous;
                require __DIR__.'/../../../src/Scheduling/views/SchedulingUI.php'; ?>

                </div>
            </div>
            <div class="tab-content normal-tab" id="tab3-content">
                <p>
                    <?php _e("Automatically send your data to over 500 apps with Zapier.", 'wp_all_export_plugin'); ?>
                    <br/>
                    <a href="https://zapier.com/zapbook/wp-all-export-pro/" target="_blank"><?php _e("Click here to read more about WP All Export's Zapier Integration.", 'wp_all_export_plugin'); ?></a>
                </p>
                <iframe width="560" height="315" src="https://www.youtube.com/embed/6tBacBmiHsQ" frameborder="0" allowfullscreen></iframe>
            </div>
            <?php if ($isImportAllowedSpecification->isSatisfied($update_previous)): ?>

                <div class="tab-content normal-tab" id="tab4-content">
                    <p>
                        <?php _e("After you've downloaded your data, edit it however you like.", 'wp_all_export_plugin'); ?><br/>
                        <?php _e("Then, click below to import the data with WP All Import without having to set anything up.", 'wp_all_export_plugin'); ?>
                    </p>
                    <p>
                        <button class="button button-primary button-hero wpallexport-large-button download_data"
                                rel="<?php echo add_query_arg(array('action' => 'download', 'id' => $update_previous->id, '_wpnonce' => wp_create_nonce('_wpnonce-download_feed')), $this->baseUrl); ?>"><?php _e('Download', 'wp_all_export_plugin'); ?> <?php echo strtoupper(wp_all_export_get_export_format($update_previous->options)); ?></button>

                        <button class="button button-primary button-hero wpallexport-large-button download_data"
                                rel="<?php echo add_query_arg(array('page' => 'pmxi-admin-import', 'id' => $update_previous->options['import_id'], 'deligate' => 'wpallexport'), remove_query_arg('page', $this->baseUrl)); ?>"><?php _e('Import with WP All Import', 'wp_all_export_plugin'); ?></button>
                    </p>
                    <p>
                        <?php _e("You can also start the import by clicking 'Import with WP All Import' on the Manage Exports page.", 'wp_all_export_plugin');?>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    <hr>
        <?php
    }
    ?>
</div>