<h2>Bulk Delete Exports</h2>

<form method="post">
	<input type="hidden" name="action" value="bulk" />
	<input type="hidden" name="bulk-action" value="<?php echo esc_attr($action) ?>" />
	<?php foreach ($ids as $id): ?>
		<input type="hidden" name="items[]" value="<?php echo esc_attr($id) ?>" />
	<?php endforeach ?>
	
	<p><?php printf(__('Are you sure you want to delete <strong>%s</strong> selected %s?', 'pmxe_plugin'), $items->count(), _n('export', 'exports', $items->count(), 'pmxe_plugin')) ?></p>
	
	<p class="submit">
		<?php wp_nonce_field('bulk-exports', '_wpnonce_bulk-exports') ?>
		<input type="hidden" name="is_confirmed" value="1" />
		<input type="submit" class="button-primary" value="Delete" />
	</p>
</form>