<h2><?php _e('Bulk Delete Imports', 'wp_all_import_plugin');?></h2>

<form method="post">
	<input type="hidden" name="action" value="bulk" />
	<input type="hidden" name="bulk-action" value="<?php echo esc_attr($action) ?>" />
	<?php foreach ($ids as $id): ?>
		<input type="hidden" name="items[]" value="<?php echo esc_attr($id) ?>" />
	<?php endforeach ?>
	
	<p><?php printf(__('Are you sure you want to delete <strong>%s</strong> selected %s?', 'wp_all_import_plugin'), $items->count(), _n('import', 'imports', $items->count(), 'wp_all_import_plugin')) ?></p>
	<div class="input">
		<input type="checkbox" id="is_delete_posts" name="is_delete_posts" class="switcher"/> <label for="is_delete_posts"><?php _e('Delete associated posts as well','wp_all_import_plugin');?> </label>
		<div class="switcher-target-is_delete_posts" style="padding: 5px 17px;">
			<div class="input">
				<input type="hidden" name="is_delete_images" value="no"/>
				<input type="checkbox" id="is_delete_images" name="is_delete_images" value="yes" />
				<label for="is_delete_images"><?php _e('Delete associated images from media gallery', 'wp_all_import_plugin') ?></label>			
			</div>
			<div class="input">
				<input type="hidden" name="is_delete_attachments" value="no"/>
				<input type="checkbox" id="is_delete_attachments" name="is_delete_attachments" value="yes" />
				<label for="is_delete_attachments"><?php _e('Delete associated files from media gallery', 'wp_all_import_plugin') ?></label>			
			</div>
		</div>
		<?php foreach($items->convertRecords() as $item) : ?>
			<?php if ( ! empty($item->options['deligate']) and $item->options['deligate'] == 'wpallexport' and class_exists('PMXE_Plugin')): ?>
				<?php
					$export = new PMXE_Export_Record();
					$export->getById($item->options['export_id']);
					if ( ! $export->isEmpty() ){
						printf(__('<p class="wpallimport-delete-posts-warning"><strong>Important</strong>: this import was created automatically by WP All Export. All posts exported by the "%s" export job have been automatically associated with this import.</p>', 'wp_all_export_plugin'), $export->friendly_name );
					}
				?>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
	
	<p class="submit">
		<?php wp_nonce_field('bulk-imports', '_wpnonce_bulk-imports') ?>
		<input type="hidden" name="is_confirmed" value="1" />
		<?php foreach ($ids as $id): ?>
			<input type="hidden" name="import_ids[]" value="<?php echo esc_attr($id) ?>" />
		<?php endforeach ?>
		<input type="submit" class="button-primary" value="Delete" />
	</p>
</form>