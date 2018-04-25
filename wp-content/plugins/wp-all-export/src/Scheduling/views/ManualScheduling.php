<div>

    <label>
        <input type="radio" name="scheduling_enable"
               value="2" <?php if ($post['scheduling_enable'] == 2) { ?> checked="checked" <?php } ?>/>
        <h4 style="margin-top: 0;display: inline-block;"><?php _e('Manual Scheduling', PMXE_Plugin::LANGUAGE_DOMAIN); ?></h4>
    </label>
    <div style="margin-left: 26px; margin-bottom: 10px; font-size: 13px;"><?php _e('Run this export using cron jobs.'); ?></div>
    <div style="<?php if ($post['scheduling_enable'] != 2) { ?> display: none; <?php } ?>" class="manual-scheduling">

        <div class="wpallexport-free-edition-notice" style="margin: 15px 0; width: 90%; padding-left: 10px; padding-right: 10px;">
            <a style="font-size: 1.3em;" class="upgrade_link" target="_blank" href="https://www.wpallimport.com/checkout/?edd_action=add_to_cart&download_id=118611&edd_options%5Bprice_id%5D=1&utm_source=wordpress.org&utm_medium=cron&utm_campaign=free+wp+all+export+plugin"><?php _e('Upgrade to the Pro edition of WP All Export for Manual Scheduling','wp_all_export_plugin');?></a>
            <p>
                <?php _e('If you already own it, remove the free edition and install the Pro edition.','wp_all_export_plugin');?>
            </p>
        </div>

        <p style="margin:0;">
        <h5 style="margin-bottom: 10px; margin-top: 10px; font-size: 14px;  color: #ccc;"><?php _e('Trigger URL'); ?></h5>
        <code style="padding: 10px; border: 1px solid #ccc; display: block; width: 90%; color: #ccc; user-select: none; cursor: default;">
            <?php echo site_url() . '/wp-cron.php?export_key=●●●●●●●●●●●●&export_id=' . $export_id . '&action=trigger'; ?>
        </code>
        </p>
        <p style="margin: 0 0 15px;">
        <h5 style="margin-bottom: 10px; margin-top: 10px; font-size: 14px;  color: #ccc;"><?php _e('Processing URL'); ?></h5>
        <code style="padding: 10px; border: 1px solid #ccc; display: block; width: 90%; color: #ccc; user-select: none; cursor: default;">
            <?php echo site_url() . '/wp-cron.php?export_key=●●●●●●●●●●●●&export_id=' . $export_id . '&action=processing'; ?>
        </code>
        </p>
        <p style="margin: 0 0 15px;">
        <h5 style="margin-bottom: 10px; margin-top: 10px; font-size: 14px;"><?php _e('File URL'); ?></h5>
        <code style="padding: 10px; border: 1px solid #ccc; display: block; width: 90%;">
            <?php echo site_url() . '/wp-cron.php?security_token=' . substr(md5($cron_job_key . $export_id), 0, 16) . '&export_id=' . $export_id . '&action=get_data'; ?>
        </code>
        </p>
        <p style="margin: 0 0 15px;">
        <h5 style="margin-bottom: 10px; margin-top: 10px; font-size: 14px;"><?php _e('Bundle URL'); ?></h5>
        <code style="padding: 10px; border: 1px solid #ccc; display: block; width: 90%;">
            <?php echo site_url() . '/wp-cron.php?security_token=' . substr(md5($cron_job_key . $export_id), 0, 16) . '&export_id=' . $export_id . '&action=get_bundle'; ?>
        </code>
        </p>
        <p style="margin:0; padding-left: 0;"><?php _e('Read more about manual scheduling'); ?>: <a target="_blank"
                                                                                                    href="http://www.wpallimport.com/documentation/recurring/cron/">
                http://www.wpallimport.com/documentation/recurring/cron/</a>
        </p>
    </div>
</div>