<h2><?php _e('Delete Export', 'pmxe_plugin') ?></h2>

<form method="post">
	<p><?php printf(__('Are you sure you want to delete <strong>%s</strong> export?', 'pmxe_plugin'), $item->friendly_name) ?></p>	
	<p class="submit">
		<?php wp_nonce_field('delete-export', '_wpnonce_delete-export') ?>
		<input type="hidden" name="is_confirmed" value="1" />
		<input type="submit" class="button-primary" value="Delete" />
	</p>
	
</form>