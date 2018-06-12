<?php if ( ! $isWizard): ?>
<!--p class="note" style="float:left; margin-top:30px;"><?php _e('To run the import, click Run Import on the Manage Imports page.'); ?></p-->
<?php endif; ?>
<p class="wpallimport-submit-buttons">
	<?php wp_nonce_field('options', '_wpnonce_options') ?>
	<input type="hidden" name="is_submitted" value="1" />
	<input type="hidden" name="converted_options" value="1"/>
	
	<?php if ($isWizard): ?>

		<a href="<?php echo apply_filters('pmxi_options_back_link', add_query_arg('action', 'template', $this->baseUrl), $isWizard); ?>" class="back rad3"><?php _e('Back to Step 3', 'wp_all_import_plugin') ?></a>

		<?php if (isset($source_type) and in_array($source_type, array('url', 'ftp', 'file'))): ?>
			<!--input type="hidden" class="save_only" value="0" name="save_only"/-->
			<input type="submit" name="save_only" class="button button-primary button-hero wpallimport-large-button" value="<?php _e('Save Only', 'wp_all_import_plugin') ?>" style="background:#425f9a;"/>
		<?php endif ?>

		<input type="submit" class="button button-primary button-hero wpallimport-large-button" value="<?php _e('Continue', 'wp_all_import_plugin') ?>" />		

	<?php else: ?>		
		<a href="<?php echo apply_filters('pmxi_options_back_link', remove_query_arg('id', remove_query_arg('action', $this->baseUrl)), $isWizard); ?>" class="back rad3"><?php _e('Back to Manage Imports', 'wp_all_import_plugin') ?></a>
		<input type="submit" class="button button-primary button-hero wpallimport-large-button" value="<?php _e('Save Import Configuration', 'wp_all_import_plugin') ?>" />
	<?php endif ?>
</p>