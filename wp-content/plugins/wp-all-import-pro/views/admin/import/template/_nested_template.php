<?php $custom_type = get_post_type_object( $post_type ); ?>

<script type="text/javascript">
	var plugin_url = '<?php echo WP_ALL_IMPORT_ROOT_URL; ?>';
</script>

<div class="wpallimport-collapsed closed nested_options wpallimport-section">
	<div class="wpallimport-content-section">
		<div class="wpallimport-collapsed-header">
			<h3><?php _e('Nested XML/CSV files','wp_all_import_plugin');?></h3>	
		</div>
		<div class="wpallimport-collapsed-content">
			<table class="form-table" style="max-width:none;">
				<tr>
					<td>						
						<div class="nested_files">
							<ul>
								<?php if ( ! empty($post['nested_files'])): ?>
									<?php 
										$nested_files = json_decode($post['nested_files'], true);
										foreach ($nested_files as $key => $file) {
										?>
										<li rel="<?php echo $key;?>"><?php echo $file;?> <a href="javascript:void(0);" class="unmerge"><?php _e('remove', 'wp_all_import_plugin'); ?></a></li>
										<?php
									}?>
								<?php endif; ?>
							</ul>
							<input type="hidden" value="<?php echo esc_attr($post['nested_files']); ?>" name="nested_files"/>
						</div>				
						<div class="nested_xml">						
							<div class="input" style="margin-left:15px;">							
								<input type="hidden" name="nested_local_path"/>
								<input type="hidden" name="nested_source_path"/>
								<input type="hidden" name="nested_root_element"/>
								<div class="nested_msgs"></div>
							</div>						
						</div>								
						<div class="clear"></div>	
						<div class="add_nested_file">
							
							<div class="msgs"></div>
							
							<div class="file-type-options">
								<label><?php _e('Specify the URL of the nested file to use.', 'wp_all_import_plugin'); ?></label>
								<input type="text" class="regular-text" name="nested_url" value="" style="width:100%; line-height:20px;" placeholder="http(s)://"/>
							</div>

							<a rel="parse" href="javascript:void(0);" class="parse"><?php _e('Add', 'wp_all_import_plugin'); ?></a>
						</div>											
					</td>
				</tr>							
			</table>
		</div>
	</div>
</div>