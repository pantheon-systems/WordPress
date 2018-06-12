<style type="text/css">
    .wpae-shake {
        -webkit-animation: wpae_shake 0.4s 1 linear;
        -moz-animation: wpae_shake 0.4s 1 linear;
        -o-animation: wpae_shake 0.4s 1 linear;
    }
    @-webkit-keyframes wpae_shake {
        0% { -webkit-transform: translate(30px); }
        20% { -webkit-transform: translate(-30px); }
        40% { -webkit-transform: translate(15px); }
        60% { -webkit-transform: translate(-15px); }
        80% { -webkit-transform: translate(8px); }
        100% { -webkit-transform: translate(0px); }
    }
    @-moz-keyframes wpae_shake {
        0% { -moz-transform: translate(30px); }
        20% { -moz-transform: translate(-30px); }
        40% { -moz-transform: translate(15px); }
        60% { -moz-transform: translate(-15px); }
        80% { -moz-transform: translate(8px); }
        100% { -moz-transform: translate(0px); }
    }
    @-o-keyframes wpae_shake {
        0% { -o-transform: translate(30px); }
        20% { -o-transform: translate(-30px); }
        40% { -o-transform: translate(15px); }
        60% { -o-transform: translate(-15px); }
        80% { -o-transform: translate(8px); }
        100% { -o-origin-transform: translate(0px); }
    }
</style>

<form class="settings" method="post" action="<?php echo $this->baseUrl ?>" enctype="multipart/form-data">

    <div class="wpallexport-header">
		<div class="wpallexport-logo"></div>
		<div class="wpallexport-title">
			<p><?php _e('WP All Export', 'wp_all_export_plugin'); ?></p>
			<h3><?php _e('Settings', 'wp_all_export_plugin'); ?></h3>			
		</div>
	</div>
	<h2 style="padding:0px;"></h2>

    <div class="wpallexport-setting-wrapper">
		<?php if ($this->errors->get_error_codes()): ?>
			<?php $this->error() ?>
		<?php endif ?>

		<h3><?php _e('Import/Export Templates', 'wp_all_export_plugin') ?></h3>
		<?php $templates = new PMXE_Template_List(); $templates->getBy()->convertRecords() ?>
		<?php wp_nonce_field('delete-templates', '_wpnonce_delete-templates') ?>
		<?php if ($templates->total()): ?>
			<table>
				<?php foreach ($templates as $t): ?>
					<tr>
						<td>
							<label class="selectit" for="template-<?php echo $t->id ?>"><input id="template-<?php echo $t->id ?>" type="checkbox" name="templates[]" value="<?php echo $t->id ?>" /> <?php echo $t->name ?></label>
						</td>
					</tr>
				<?php endforeach ?>
			</table>
			<p class="submit-buttons">
				<input type="submit" class="button-primary" name="delete_templates" value="<?php _e('Delete Selected', 'wp_all_export_plugin') ?>" />
				<input type="submit" class="button-primary" name="export_templates" value="<?php _e('Export Selected', 'wp_all_export_plugin') ?>" />
			</p>
		<?php else: ?>
			<em><?php _e('There are no templates saved', 'wp_all_export_plugin') ?></em>
		<?php endif ?>
		<p>
			<input type="hidden" name="is_templates_submitted" value="1" />
			<input type="file" name="template_file"/>
			<input type="submit" class="button-primary" name="import_templates" value="<?php _e('Import Templates', 'wp_all_export_plugin') ?>" />
		</p>
	</div>

</form>
<br />

<form name="settings" class="settings" method="post" action="<?php echo $this->baseUrl ?>">

	<h3><?php _e('Files', 'wp_all_export_plugin') ?></h3>
	
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row"><label><?php _e('Secure Mode', 'wp_all_export_plugin'); ?></label></th>
				<td>
					<fieldset style="padding:0;">
						<legend class="screen-reader-text"><span><?php _e('Secure Mode', 'wp_all_export_plugin'); ?></span></legend>
						<input type="hidden" name="secure" value="0"/>
						<label for="secure"><input type="checkbox" value="1" id="secure" name="secure" <?php echo (($post['secure']) ? 'checked="checked"' : ''); ?>><?php _e('Randomize folder names', 'wp_all_export_plugin'); ?></label>																				
					</fieldset>														
					<p class="description">
						<?php
							$wp_uploads = wp_upload_dir();
						?>
						<?php printf(__('If enabled, exported files and temporary files will be saved in a folder with a randomized name in %s.<br/><br/>If disabled, exported files will be saved in the Media Library.', 'wp_all_export_plugin'), $wp_uploads['basedir'] . DIRECTORY_SEPARATOR . WP_ALL_EXPORT_UPLOADS_BASE_DIRECTORY ); ?>
					</p>
				</td>
			</tr>			
		</tbody>
	</table>
    <p class="submit-buttons">
        <?php wp_nonce_field('edit-settings', '_wpnonce_edit-settings') ?>
        <input type="hidden" name="is_settings_submitted" value="1" />
        <input type="submit" class="button-primary" value="Save Settings" />
    </p>

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
					<input type="text" class="regular-text" name="zapier_api_key" readOnly="readOnly" value=""/>
					<input type="submit" class="button-secondary generate-zapier-api-key" name="pmxe_generate_zapier_api_key" value="<?php _e('Generate API Key', 'wp_all_export_plugin'); ?>"/>
					<p class="description"><?php _e('Changing the key will require you to update your existing Zaps on Zapier.', 'wp_all_export_plugin'); ?></p>
				</td>
			</tr>											
		</tbody>
	</table>	

	<div class="wpallexport-free-edition-notice zapier-upgrade" style="margin: 15px 0; padding: 20px; display: none;">
		<a class="upgrade_link" target="_blank" href="https://www.wpallimport.com/checkout/?edd_action=add_to_cart&download_id=118611&edd_options%5Bprice_id%5D=1&utm_source=wordpress.org&utm_medium=custom-php&utm_campaign=free+wp+all+export+plugin"><?php _e('Upgrade to the Pro edition of WP All Export for Zapier Integration','wp_all_export_plugin');?></a>
		<p><?php _e('If you already own it, remove the free edition and install the Pro edition.', 'wp_all_export_plugin'); ?></p>
	</div>

	<div class="clear"></div>
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

<?php
	$uploads = wp_upload_dir();
	$functions = $uploads['basedir'] . DIRECTORY_SEPARATOR . WP_ALL_EXPORT_UPLOADS_BASE_DIRECTORY . DIRECTORY_SEPARATOR . 'functions.php';
?>
<hr />
<br>
<h3><?php _e('Function Editor', 'pmxe_plugin') ?></h3>

<textarea id="wp_all_export_code" name="wp_all_export_code"><?php echo "<?php\n\n?>";?></textarea>						
<div class="wpallexport-free-edition-notice php-functions-upgrade" style="margin: 15px 0; padding: 20px; display: none;">
	<a class="upgrade_link" target="_blank" href="https://www.wpallimport.com/checkout/?edd_action=add_to_cart&download_id=118611&edd_options%5Bprice_id%5D=1&utm_source=wordpress.org&utm_medium=custom-php&utm_campaign=free+wp+all+export+plugin"><?php _e('Upgrade to the Pro edition of WP All Export to enable the Function Editor','wp_all_export_plugin');?></a>
	<p><?php _e('If you already own it, remove the free edition and install the Pro edition.', 'wp_all_export_plugin'); ?></p>
</div>

<div class="input" style="margin-top: 10px;">

	<div class="input wp_all_export_save_functions_container" style="display:inline-block; margin-right: 20px;">
		<input type="button" class="button-primary wp_all_export_save_functions" value="<?php _e("Save Functions", 'wp_all_export_plugin'); ?>"/>
		<a href="#help" class="wpallexport-help" title="<?php printf(__("Add functions here for use during your export. You can access this file at %s", "wp_all_export_plugin"), preg_replace("%.*wp-content%", "wp-content", $functions));?>" style="top: 0;">?</a>
		<div class="wp_all_export_functions_preloader"></div>
	</div>						
	<div class="input wp_all_export_saving_status" style="display:inline-block;">

	</div>

</div>

<a href="http://soflyy.com/" target="_blank" class="wpallexport-created-by"><?php _e('Created by', 'wp_all_export_plugin'); ?> <span></span></a>
