<form class="licenses" method="post" action="<?php echo $this->baseUrl ?>" enctype="multipart/form-data">

	<h2><?php _e('WP All Import Licenses', 'wp_all_import_plugin') ?></h2>
	<hr />
	<?php if ($this->errors->get_error_codes()): ?>
		<?php $this->error() ?>
	<?php endif ?>
	
	<table class="form-table">
		<tbody>
			<?php foreach ($addons as $class => $addon) : if ( ! $addon['active'] ) continue; ?>				
				<tr valign="top">	
					<th scope="row" valign="middle" style="width:200px; vertical-align: middle;">
						<?php echo $addon['title']; ?>
					</th>
					<td style="vertical-align: middle; width: 360px;">
						<input id="<?php echo $class; ?>_license_key" name="licenses[<?php echo $class; ?>]" type="text" class="regular-text" value="<?php if (!empty($post['licenses'][$class])) esc_attr_e( $post['licenses'][$class] ); ?>" />						
					</td>
					<td style="vertical-align: middle;">
					<?php if( ! empty($post['licenses'][$class]) ) { ?>

						<?php if( ! empty($post['statuses'][$class]) && $post['statuses'][$class] == 'valid' ) { ?>														
							<p style="color:green;"><?php _e('Active', 'wp_all_import_plugin'); ?></p>
							<!--input type="submit" class="button-secondary" name="pmxi_license_deactivate[<?php echo $class; ?>]" value="<?php _e('Deactivate License', 'wp_all_import_plugin'); ?>"/-->
						<?php } else { ?>													
							<input type="submit" class="button-secondary" name="pmxi_license_activate[<?php echo $class; ?>]" value="<?php _e('Activate License', 'wp_all_import_plugin'); ?>"/>
							<span style="line-height: 28px;"><?php echo $post['statuses'][$class]; ?></span>
						<?php } ?>
					
					<?php } ?>
					</td>
				</tr>				
			<?php endforeach; ?>
		</tbody>
	</table>	

	<p>
		<?php wp_nonce_field('edit-licenses', '_wpnonce_edit-licenses') ?>
		<input type="hidden" name="is_licenses_submitted" value="1" />		
		<input type="submit" class="button-primary" value="<?php _e('Save', 'wp_all_import_plugin') ?>" />
	</p>
</form>