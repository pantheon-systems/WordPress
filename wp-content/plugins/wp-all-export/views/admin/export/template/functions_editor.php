<style type="text/css">
    .wpae-collapser {
        float: right;
        height: 20px;
        width: 20px;
        background: url('<?php echo PMXE_ROOT_URL . '/static/img/collapser.png'?>') 0 0;
        margin-right: 10px;
        background-size: cover;
    }

    .closed .wpae-collapser{
        background: url('<?php echo PMXE_ROOT_URL . '/static/img/collapser.png'?>') 0 20px;
        background-size: cover;
    }
</style>
<div class="wpallexport-collapsed wpallexport-section wpallexport-file-options functions-editor closed" style="margin-top: 0px;">
    <div class="wpallexport-content-section" style="padding-bottom: 0; margin-bottom: 10px;">
        <div class="wpallexport-collapsed-header edit-functions-collapsed-header" style="padding-left: 25px; background: none;">
            <div class="wpae-collapser"></div>
            <h3 style="font-size: 14px; line-height: normal; margin-top: 11px; color: #464646;"><?php _e('Function Editor', 'wp_all_export_plugin');?><a href="#help" class="wpallexport-help" title="<?php printf(__("Add functions here for use during your export. You can access this file at %s", "wp_all_export_plugin"), preg_replace("%.*wp-content%", "wp-content", $functions));?>" style="top: -1px;">?</a></h3>
        </div>
        <div class="wpallexport-collapsed-content" style="padding: 0; overflow: hidden; height: auto; display: none;">
            <div class="wpallexport-collapsed-content-inner" style="padding-top:0;">
                <textarea id="wp_all_export_code" name="wp_all_export_code"><?php echo (empty($functions_content)) ? "<?php\n\n?>": esc_textarea($functions_content);?></textarea>
                <div class="wpallexport-free-edition-notice php-functions-upgrade" style="margin: 15px 0; display: none;">
                    <a class="upgrade_link" target="_blank" href="https://www.wpallimport.com/checkout/?edd_action=add_to_cart&download_id=118611&edd_options%5Bprice_id%5D=1&utm_source=wordpress.org&utm_medium=custom-php&utm_campaign=free+wp+all+export+plugin" style="font-size: 1.3em;"><?php _e('Upgrade to Pro to use Custom PHP Functions','wp_all_export_plugin');?></a>
                    <p><?php _e('If you already own it, remove the free edition and install the Pro edition.','wp_all_export_plugin');?></p>
                </div>
            </div>
        </div>
    </div>
</div>
