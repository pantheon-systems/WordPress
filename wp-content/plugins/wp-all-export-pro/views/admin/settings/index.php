<?php if (!$is_license_active): ?>
    <form name="settings" method="post" action="" class="settings">

        <div class="wpallexport-header">
            <div class="wpallexport-logo"></div>
            <div class="wpallexport-title">
                <p style="font-size:18px !important;"><?php _e('WP All Export', 'wp_all_export_plugin'); ?></p>
                <h3><?php _e('Settings', 'wp_all_export_plugin'); ?></h3>
            </div>
        </div>

        <h2 style="padding:0px;"></h2>

        <div class="wpallexport-setting-wrapper">
            <?php if ($this->errors->get_error_codes()): ?>
                <?php $this->error() ?>
            <?php endif ?>

            <h3><?php _e('Licenses', 'wp_all_export_plugin') ?></h3>

            <table class="form-table">
                <tbody>
                <tr>
                    <th scope="row"><label><?php _e('License Key', 'wp_all_export_plugin'); ?></label></th>
                    <td>
                    <input type="password" class="regular-text" name="license"
                               value="<?php if (!empty($post['license'])) esc_attr_e(PMXE_Plugin::decode($post['license'])); ?>"/>
                        <?php if (!empty($post['license'])) { ?>

                            <?php if (!empty($post['license_status']) && $post['license_status'] == 'valid') { ?>
                                <p style="color:green; display: inline-block;"><?php _e('Active', 'wp_all_export_plugin'); ?></p>
                            <?php } else { ?>
                                <span style="line-height: 28px;"><?php echo $post['license_status']; ?></span>
                            <?php } ?>

                        <?php } ?>
                        <p class="description"><?php _e('A license key is required to access plugin updates. You can use your license key on an unlimited number of websites. Do not distribute your license key to 3rd parties. You can get your license key in the <a target="_blank" href="http://www.wpallimport.com/portal">customer portal</a>.', 'wp_all_export_plugin'); ?></p>
                    </td>
                </tr>
                </tbody>
            </table>

            <div class="clear"></div>

            <p class="submit-buttons">
                <?php wp_nonce_field('edit-license', '_wpnonce_edit-license') ?>
                <input type="hidden" name="is_license_submitted" value="1"/>
                <input type="submit" class="button-primary" value="Save License"/>
            </p>

        </div>
    </form>

    <form name="settings" method="post" action="" class="settings">

        <table class="form-table">
            <tbody>

            <tr>
                <th scope="row"><label><?php _e('Automatic Scheduling License Key', 'wp_all_export_plugin'); ?></label></th>
                <td>
                    <input type="password" class="regular-text" name="scheduling_license"
                           value="<?php if (!empty($post['scheduling_license'])) esc_attr_e(PMXE_Plugin::decode($post['scheduling_license'])); ?>"/>
                    <?php if (!empty($post['scheduling_license'])) { ?>

                        <?php if (!empty($post['scheduling_license_status']) && $post['scheduling_license_status'] == 'valid') { ?>
                            <p style="color:green; display: inline-block;"><?php _e('Active', 'wp_all_export_plugin'); ?></p>
                        <?php } else { ?>
                            <input type="submit" class="button-secondary" name="pmxe_scheduling_license_activate"
                                   value="<?php _e('Activate License', 'wp_all_export_plugin'); ?>"/>
                            <span style="line-height: 28px;"><?php echo $post['scheduling_license_status']; ?></span>
                        <?php } ?>

                    <?php } ?>
                    <?php
                    $scheduling = \Wpae\Scheduling\Scheduling::create();
                    if(!($scheduling->checkLicense())){
                    ?>
                    <p class="description"><?php _e('A license key is required to use Automatic Scheduling. If you have already subscribed, <a href="https://www.wpallimport.com/portal/automatic-scheduling/" target="_blank">click here to access your license key</a>. If you dont have a license, <a href="https://www.wpallimport.com/checkout/?edd_action=add_to_cart&download_id=515704" target="_blank">click here to subscribe</a>.', 'wp_all_export_plugin'); ?></p>
                    <?php
                    }
                    ?>
                </td>
            </tr>
            </tbody>
        </table>

        <div class="clear"></div>

        <p class="submit-buttons">
            <?php wp_nonce_field('edit-license', '_wpnonce_edit-scheduling-license') ?>
            <input type="hidden" name="is_scheduling_license_submitted" value="1"/>
            <input type="submit" class="button-primary" value="Save Scheduling License"/>
        </p>
    </form>
<?php endif; ?>

<form class="settings" method="post" action="<?php echo $this->baseUrl ?>" enctype="multipart/form-data">

    <?php if ($is_license_active): ?>

    <div class="wpallexport-header">
        <div class="wpallexport-logo"></div>
        <div class="wpallexport-title">
            <p style="font-size:18px !important;"><?php _e('WP All Export', 'wp_all_export_plugin'); ?></p>
            <h3><?php _e('Settings', 'wp_all_export_plugin'); ?></h3>
        </div>
    </div>

    <h2 style="padding:0;"></h2>

    <div class="wpallexport-setting-wrapper">
        <?php if ($this->errors->get_error_codes()): ?>
            <?php $this->error() ?>
        <?php endif ?>

        <?php if (!empty($license_message)): ?>
            <div class="updated"><p><?php echo $license_message; ?></p></div>
        <?php endif; ?>

        <?php endif; ?>

        <h3><?php _e('Import/Export Templates', 'wp_all_export_plugin') ?></h3>
        <?php $templates = new PMXE_Template_List();
        $templates->getBy()->convertRecords() ?>
        <?php wp_nonce_field('delete-templates', '_wpnonce_delete-templates') ?>
        <?php if ($templates->total()): ?>
            <table>
                <?php foreach ($templates as $t): ?>
                    <tr>
                        <td>
                            <label class="selectit" for="template-<?php echo $t->id ?>"><input
                                    id="template-<?php echo $t->id ?>" type="checkbox" name="templates[]"
                                    value="<?php echo $t->id ?>"/> <?php echo $t->name ?></label>
                        </td>
                    </tr>
                <?php endforeach ?>
            </table>
            <p class="submit-buttons">
                <input type="submit" class="button-primary" name="delete_templates"
                       value="<?php _e('Delete Selected', 'wp_all_export_plugin') ?>"/>
                <input type="submit" class="button-primary" name="export_templates"
                       value="<?php _e('Export Selected', 'wp_all_export_plugin') ?>"/>
            </p>
        <?php else: ?>
            <em><?php _e('There are no templates saved', 'wp_all_export_plugin') ?></em>
        <?php endif ?>
        <p>
            <input type="hidden" name="is_templates_submitted" value="1"/>
            <input type="file" name="template_file"/>
            <input type="submit" class="button-primary" name="import_templates"
                   value="<?php _e('Import Templates', 'wp_all_export_plugin') ?>"/>
        </p>
        <?php if ($is_license_active): ?>
    </div>
<?php endif ?>

</form>
<br/>

<form name="settings" class="settings" method="post" action="<?php echo $this->baseUrl ?>">

    <h3><?php _e('Cron Exports', 'wp_all_export_plugin') ?></h3>

    <table class="form-table">
        <tbody>
        <tr>
            <th scope="row"><label><?php _e('Secret Key', 'wp_all_export_plugin'); ?></label></th>
            <td>
                <input type="text" class="regular-text" name="cron_job_key"
                       value="<?php echo esc_attr($post['cron_job_key']); ?>"/>
                <p class="description"><?php _e('Changing this will require you to re-create your existing cron jobs.', 'wp_all_export_plugin'); ?></p>
            </td>
        </tr>
        </tbody>
    </table>

    <div class="clear"></div>

    <h3><?php _e('Files', 'wp_all_export_plugin') ?></h3>

    <table class="form-table">
        <tbody>
        <tr>
            <th scope="row"><label><?php _e('Secure Mode', 'wp_all_export_plugin'); ?></label></th>
            <td>
                <fieldset style="padding:0;">
                    <legend class="screen-reader-text"><span><?php _e('Secure Mode', 'wp_all_export_plugin'); ?></span>
                    </legend>
                    <input type="hidden" name="secure" value="0"/>
                    <label for="secure"><input type="checkbox" value="1" id="secure"
                                               name="secure" <?php echo(($post['secure']) ? 'checked="checked"' : ''); ?>><?php _e('Randomize folder names', 'wp_all_export_plugin'); ?>
                    </label>
                </fieldset>
                <p class="description">
                    <?php
                    $wp_uploads = wp_upload_dir();
                    ?>
                    <?php printf(__('If enabled, exported files and temporary files will be saved in a folder with a randomized name in %s.<br/><br/>If disabled, exported files will be saved in the Media Library.', 'wp_all_export_plugin'), $wp_uploads['basedir'] . DIRECTORY_SEPARATOR . WP_ALL_EXPORT_UPLOADS_BASE_DIRECTORY); ?>
                </p>
            </td>
        </tr>
        </tbody>
    </table>
    <a name="license-key"></a>

    <h3><?php _e('Zapier Integration', 'wp_all_export_plugin') ?></h3>

    <table class="form-table">
        <tbody>
        <tr>
            <th scope="row"><label><?php _e('Getting Started', 'wp_all_export_plugin'); ?></label></th>
            <td>
                <p class="description"><?php printf(__('Zapier acts as a middle man between WP All Export and hundreds of other popular apps. To get started go to Zapier.com, create an account, and make a new Zap. Read more: <a target="_blank" href="https://zapier.com/zapbook/wp-all-export-pro/">https://zapier.com/zapbook/wp-all-export-pro/</a>', 'wp_all_export_plugin'), "https://zapier.com/zapbook/wp-all-export-pro/"); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label><?php _e('API Key', 'wp_all_export_plugin'); ?></label></th>
            <td>
                <input type="text" class="regular-text" name="zapier_api_key" readOnly="readOnly"
                       value="<?php if (!empty($post['zapier_api_key'])) esc_attr_e($post['zapier_api_key']); ?>"/>
                <input type="submit" class="button-secondary" name="pmxe_generate_zapier_api_key"
                       value="<?php _e('Generate New API Key', 'wp_all_export_plugin'); ?>"/>
                <p class="description"><?php _e('Changing the key will require you to update your existing Zaps on Zapier.', 'wp_all_export_plugin'); ?></p>
            </td>
        </tr>
        </tbody>
    </table>

    <div class="clear"></div>

    <p class="submit-buttons">
        <?php wp_nonce_field('edit-settings', '_wpnonce_edit-settings') ?>
        <input type="hidden" name="is_settings_submitted" value="1"/>
        <input type="submit" class="button-primary" value="Save Settings"/>
    </p>

</form>

<?php if ($is_license_active): ?>
    <form name="settings" method="post" action="" class="settings">

	<h3><?php _e('Licenses', 'wp_all_export_plugin') ?></h3>
	
	<table class="form-table">
		<tbody>			
			<tr>
				<th scope="row"><label><?php _e('License Key', 'wp_all_export_plugin'); ?></label></th>
				<td>
					<input type="password" class="regular-text" name="license" value="<?php if (!empty($post['license'])) esc_attr_e( PMXE_Plugin::decode($post['license'] )); ?>"/>
					<?php if( ! empty($post['license']) ) { ?>

                        <?php if (!empty($post['license_status']) && $post['license_status'] == 'valid') { ?>
                            <p style="color:green; display: inline-block;"><?php _e('Active', 'wp_all_export_plugin'); ?></p>
                        <?php } else { ?>
                            <input type="submit" class="button-secondary" name="pmxe_license_activate"
                                   value="<?php _e('Activate License', 'wp_all_export_plugin'); ?>"/>
                            <span style="line-height: 28px;"><?php echo $post['license_status']; ?></span>
                        <?php } ?>

                    <?php } ?>
                    <p class="description"><?php _e('A license key is required to access plugin updates. You can use your license key on an unlimited number of websites. Do not distribute your license key to 3rd parties. You can get your license key in the <a target="_blank" href="http://www.wpallimport.com/portal">customer portal</a>.', 'wp_all_export_plugin'); ?></p>
                </td>
            </tr>
            </tbody>
        </table>

        <div class="clear"></div>


        <p class="submit-buttons">
            <?php wp_nonce_field('edit-license', '_wpnonce_edit-license') ?>
            <input type="hidden" name="is_license_submitted" value="1"/>
            <input type="submit" class="button-primary" value="Save License"/>
        </p>
    </form>


    <form name="settings" method="post" action="" class="settings">

        <table class="form-table">
            <tbody>

            <tr>
                <th scope="row"><label><?php _e('Scheduling License Key', 'wp_all_export_plugin'); ?></label></th>
                <td>
                    <input type="password" class="regular-text" name="scheduling_license"
                           value="<?php if (!empty($post['scheduling_license'])) esc_attr_e(PMXE_Plugin::decode($post['scheduling_license'])); ?>"/>
                    <?php if (!empty($post['scheduling_license'])) { ?>

                        <?php if (!empty($post['scheduling_license_status']) && $post['scheduling_license_status'] == 'valid') { ?>
                            <p style="color:green; display: inline-block;"><?php _e('Active', 'wp_all_export_plugin'); ?></p>
                        <?php } else { ?>
                            <span style="line-height: 28px;"><?php echo $post['scheduling_license_status']; ?></span>
                        <?php } ?>

                    <?php } ?>
                    <?php
                    $scheduling = \Wpae\Scheduling\Scheduling::create();
                    if(!($scheduling->checkLicense())){
                        ?>
                        <p class="description"><?php _e('A license key is required to use Automatic Scheduling. If you have already subscribed, <a href="https://www.wpallimport.com/portal/automatic-scheduling/" target="_blank">click here to access your license key</a>. If you dont have a license, <a href="https://www.wpallimport.com/checkout/?edd_action=add_to_cart&download_id=515704" target="_blank">click here to subscribe</a>.', 'wp_all_export_plugin'); ?></p>
                        <?php
                    }
                    ?>                </td>
            </tr>
            </tbody>
        </table>

        <div class="clear"></div>

        <p class="submit-buttons">
            <?php wp_nonce_field('edit-license', '_wpnonce_edit-scheduling-license') ?>
            <input type="hidden" name="is_scheduling_license_submitted" value="1"/>
            <input type="submit" class="button-primary" value="Save Scheduling License"/>
        </p>
    </form>
<?php endif; ?>

<?php
$uploads = wp_upload_dir();
$functions = $uploads['basedir'] . DIRECTORY_SEPARATOR . WP_ALL_EXPORT_UPLOADS_BASE_DIRECTORY . DIRECTORY_SEPARATOR . 'functions.php';
$functions_content = file_get_contents($functions);
?>
<hr/>
<br>
<h3><?php _e('Function Editor', 'pmxe_plugin') ?></h3>

<textarea id="wp_all_export_code"
          name="wp_all_export_code"><?php echo (empty($functions_content)) ? "<?php\n\n?>" : esc_textarea($functions_content); ?></textarea>

<div class="input" style="margin-top: 10px;">

    <div class="input" style="display:inline-block; margin-right: 20px;">
        <input type="button" class="button-primary wp_all_export_save_functions"
               value="<?php _e("Save Functions", 'wp_all_export_plugin'); ?>"/>
        <a href="#help" class="wpallexport-help"
           title="<?php printf(__("Add functions here for use during your export. You can access this file at %s", "wp_all_export_plugin"), preg_replace("%.*wp-content%", "wp-content", $functions)); ?>"
           style="top: 0;">?</a>
        <div class="wp_all_export_functions_preloader"></div>
    </div>
    <div class="input wp_all_export_saving_status" style="display:inline-block;">

    </div>

</div>

<a href="http://soflyy.com/" target="_blank"
   class="wpallexport-created-by"><?php _e('Created by', 'wp_all_export_plugin'); ?> <span></span></a>
