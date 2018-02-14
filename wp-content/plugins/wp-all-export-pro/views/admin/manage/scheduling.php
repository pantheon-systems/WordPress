<h2>
	<?php _e('Cron Scheduling', 'wp_all_export_plugin') ?>
</h2>

<p>
	<?php _e('To schedule an export, you must create two cron jobs in your web hosting control panel. One cron job will be used to run the Trigger script, the other to run the Execution script.', 'wp_all_export_plugin'); ?>
</p>

<p>
	<?php _e('Trigger Script URL', 'wp_all_export_plugin');?><br />
	<small><?php _e('Run the trigger script when you want to update your export. Once per 24 hours is recommended.', 'wp_all_export_plugin'); ?></small><br />
	<input style='width: 700px;' type='text' value='<?php echo site_url() . '/wp-cron.php?export_key=' . $cron_job_key . '&export_id=' . $id . '&action=trigger'; ?>' />
	<br /><br />
	<?php _e('Execution Script URL', 'wp_all_export_plugin');?><br />
	<small><?php _e('Run the execution script frequently. Once per two minutes is recommended.','wp_all_export_plugin');?></small><br />
	<input style='width: 700px;' type='text' value='<?php echo site_url() . '/wp-cron.php?export_key=' . $cron_job_key . '&export_id=' . $id . '&action=processing'; ?>' /><br /><br />
	<?php _e('Export File URL', 'wp_all_export_plugin'); ?><br />	
	<input style='width: 700px;' type='text' value='<?php echo $file_path; ?>' /><br /><br />
	<?php if (! empty($bundle_url)): ?>			
		<?php _e('Export Bundle URL', 'wp_all_export_plugin'); ?><br />	
		<input style='width: 700px;' type='text' value='<?php echo $bundle_url; ?>' /><br /><br />
	<?php endif; ?>
</p>

<p><strong><?php _e('Trigger Script', 'wp_all_export_plugin'); ?></strong></p>

<p><?php _e('Every time you want to schedule the export, run the trigger script.', 'wp_all_export_plugin'); ?></p>

<p><?php _e('To schedule the export to run once every 24 hours, run the trigger script every 24 hours. Most hosts require you to use “wget” to access a URL. Ask your host for details.', 'wp_all_export_plugin'); ?></p>

<p><i><?php _e('Example:', 'wp_all_export_plugin'); ?></i></p>

<p>wget -q -O /dev/null "<?php echo site_url() . '/wp-cron.php?export_key=' . $cron_job_key . '&export_id=' . $id . '&action=trigger'; ?>"</p>
 
<p><strong><?php _e('Execution Script', 'wp_all_export_plugin'); ?></strong></p>

<p><?php _e('The Execution script actually executes the export, once it has been triggered with the Trigger script.', 'wp_all_export_plugin'); ?></p>

<p><?php _e('It processes in iteration (only exporting a few records each time it runs) to optimize server load. It is recommended you run the execution script every 2 minutes.', 'wp_all_export_plugin'); ?></p>

<p><?php _e('It also operates this way in case of unexpected crashes by your web host. If it crashes before the export is finished, the next run of the cron job two minutes later will continue it where it left off, ensuring reliability.', 'wp_all_export_plugin'); ?></p>

<p><i><?php _e('Example:', 'wp_all_export_plugin'); ?></i></p>

<p>wget -q -O /dev/null "<?php echo site_url() . '/wp-cron.php?export_key=' . $cron_job_key . '&export_id=' . $id . '&action=processing'; ?>"</p>

<p><strong><?php _e('Notes', 'wp_all_export_plugin'); ?></strong></p>
 
<p>
	<?php _e('Your web host may require you to use a command other than wget, although wget is most common. In this case, you must asking your web hosting provider for help.', 'wp_all_export_plugin'); ?>
</p>

<p>
	See the <a href='http://www.wpallimport.com/documentation/recurring/cron/'>documentation</a> for more details.
</p>

<a href="http://soflyy.com/" target="_blank" class="wpallexport-created-by"><?php _e('Created by', 'wp_all_export_plugin'); ?> <span></span></a>