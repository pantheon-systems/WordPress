<h2><?php _e('Update Import', 'wp_all_import_plugin') ?></h2>

<?php if ($this->errors->get_error_codes()): ?>
		<?php $this->error() ?>
<?php endif ?>

<?php if ($item->path): ?>
	<form method="post">
		<p><?php printf(__('Are you sure you want to update <strong>%s</strong> import?', 'wp_all_import_plugin'), $item->name) ?></p>
		<p><?php printf(__('Source path is <strong>%s</strong>', 'wp_all_import_plugin'), $item->path) ?></p>
		
		<p class="submit">
			<?php wp_nonce_field('update-import', '_wpnonce_update-import') ?>
			<input type="hidden" name="is_confirmed" value="1" />
			<input type="submit" class="button-primary ajax-update" value="Create Posts" />
		</p>
		
	</form>
<?php else: ?>
	<div class="error">
		<p><?php _e('Update feature is not available for this import since it has no external path linked.') ?></p>
	</div>
<?php endif ?>