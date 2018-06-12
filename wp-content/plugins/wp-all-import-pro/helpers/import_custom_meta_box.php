<?php
if (!function_exists('import_custom_meta_box')){
	function import_custom_meta_box($edit_post) {
		?>
		<div id="postcustomstuff">					
			<table id="list-table">			
				<tbody class="list:meta" id="the-list">						
					<?php if (!empty(PMXI_Plugin::$session->data['pmxi_import']['options']['custom_name'])): foreach (PMXI_Plugin::$session->data['pmxi_import']['options']['custom_name'] as $i => $name): ?>
						<tr>
							<td class="left">
								<label class="screen-reader-text">Key</label>
								<input type="text" value="<?php echo esc_attr($name) ?>" name="custom_name[]" size="20">
								<div class="submit"><input type="submit" class="delete deletemeta" value="Delete"></div>
							</td>
							<td>
								<label class="screen-reader-text">Value</label>
								<textarea name="custom_value[]" rows="2" cols="30" class="widefat"><?php echo esc_html(PMXI_Plugin::$session->data['pmxi_import']['options']['custom_value'][$i]) ?></textarea>
							</td>
						</tr>						
					<?php endforeach; endif; ?>
				</tbody>
			</table>
			<?php meta_form(); ?>
		</div>	
		<p><?php _e('Custom fields can be used to add extra metadata to a post that you can <a href="http://codex.wordpress.org/Using_Custom_Fields" target="_blank">use in your theme</a>.'); ?></p>
		<?php
	}
}