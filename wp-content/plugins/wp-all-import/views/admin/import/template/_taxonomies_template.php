<?php 

$custom_type = get_post_type_object( $post_type ); 

$exclude_taxonomies = apply_filters('pmxi_exclude_taxonomies', (class_exists('PMWI_Plugin')) ? array('post_format', 'product_type', 'product_shipping_class') : array('post_format'));	
$post_taxonomies = array_diff_key(get_taxonomies_by_object_type($post['is_override_post_type'] ? array_keys(get_post_types( '', 'names' )) : array($post_type), 'object'), array_flip($exclude_taxonomies));

if ( ! empty($post_taxonomies)): 
?>
	<div class="wpallimport-collapsed closed wpallimport-section">
		<div class="wpallimport-content-section">
			<div class="wpallimport-collapsed-header">
				<h3><?php _e('Taxonomies, Categories, Tags','wp_all_import_plugin');?></h3>	
			</div>
			<div class="wpallimport-collapsed-content" style="padding: 0;">
				<div class="wpallimport-collapsed-content-inner">
					<input type="button" rel="taxonomies_hints" value="<?php _e('Show Hints', 'wp_all_import_plugin');?>" class="show_hints">
					<table class="form-table" style="max-width:none;">
					
						<?php $private_ctx = 0; ?>	
						<tr>
							<td colspan="3" style="padding-bottom:20px;">								
								<?php foreach ($post_taxonomies as $ctx): if ("" == $ctx->labels->name or (class_exists('PMWI_Plugin') and strpos($ctx->name, "pa_") === 0 and $post_type == "product")) continue;?>					
								<?php if (! $ctx->show_ui ) $private_ctx++; ?>
								<table style="width:100%;">
									<tr class="<?php echo ( ! $ctx->show_ui) ? 'private_ctx' : ''; ?>">
										<td>
											<div class="post_taxonomy">
												<div class="input">
													<input type="hidden" name="tax_assing[<?php echo $ctx->name;?>]" value="0"/>
													<input type="checkbox" class="assign_post switcher" name="tax_assing[<?php echo $ctx->name;?>]" id="tax_assing_<?php echo $ctx->name;?>" <?php echo ( ! empty($post['tax_assing'][$ctx->name]) ) ? 'checked="checked"' : ''; ?> value="1"/>
													<label for="tax_assing_<?php echo $ctx->name;?>"><?php echo $ctx->labels->name; ?></label>											
												</div>
												<div class="switcher-target-tax_assing_<?php echo $ctx->name;?>">
													<div class="input sub_input">
														<div class="input">
															<input type="radio" name="tax_logic[<?php echo $ctx->name;?>]" value="single" id="tax_logic_single_<?php echo $ctx->name;?>" class="switcher" <?php echo (empty($post['tax_logic'][$ctx->name]) or $post['tax_logic'][$ctx->name] == 'single') ? 'checked="checked"' : ''; ?>/>
															<label for="tax_logic_single_<?php echo $ctx->name;?>"><?php printf(__('Each %s has just one %s', 'wp_all_import_plugin'), $custom_type->labels->singular_name, $ctx->labels->singular_name); ?></label>
															<div class="switcher-target-tax_logic_single_<?php echo $ctx->name;?> sub_input">
																<input type="hidden" name="term_assing[<?php echo $ctx->name;?>]" value="0"/>
																<input type="checkbox" name="term_assing[<?php echo $ctx->name;?>]" <?php echo (isset($post['term_assing'][$ctx->name])) ? (( ! empty($post['term_assing'][$ctx->name]) ) ? 'checked="checked"' : '') : 'checked="checked"'; ?> title="<?php _e('Assign post to the taxonomy.','wp_all_import_plugin');?>" value="1"/>
																<input type="text" class="widefat single_xpath_field" name="tax_single_xpath[<?php echo $ctx->name; ?>]" value="<?php echo ( ! empty($post['tax_single_xpath'][$ctx->name])) ? esc_textarea($post['tax_single_xpath'][$ctx->name]) : ''; ?>" style="width:50%;"/>
																<div class="input tax_is_full_search_single" style="margin: 10px 0;">
																	<input type="hidden" name="tax_is_full_search_single[<?php echo $ctx->name; ?>]" value="0"/>
																	<input type="checkbox" id="tax_is_full_search_single_<?php echo $ctx->name; ?>" class="switcher" <?php if ( ! empty($post['tax_is_full_search_single'][$ctx->name]) ) echo "checked='checked'"; ?> name="tax_is_full_search_single[<?php echo $ctx->name; ?>]" value="1"/>
																	<label for="tax_is_full_search_single_<?php echo $ctx->name;?>"><?php printf(__('Try to match terms to existing child %s', 'wp_all_import_plugin'), $ctx->labels->name); ?></label>
																	<div class="switcher-target-tax_is_full_search_single_<?php echo $ctx->name; ?> sub_input">
																		<div class="input tax_assign_to_one_term_single" style="margin: 10px 0;">
																			<input type="hidden" name="tax_assign_to_one_term_single[<?php echo $ctx->name; ?>]" value="0"/>
																			<input type="checkbox" id="tax_assign_to_one_term_single_<?php echo $ctx->name; ?>" <?php if ( ! empty($post['tax_assign_to_one_term_single'][$ctx->name]) ) echo "checked='checked'"; ?> name="tax_assign_to_one_term_single[<?php echo $ctx->name; ?>]" value="1"/>
																			<label for="tax_assign_to_one_term_single_<?php echo $ctx->name;?>"><?php printf(__('Only assign %s to the imported %s, not the entire hierarchy', 'wp_all_import_plugin'), $custom_type->labels->name, $ctx->labels->singular_name); ?></label>
																			<a href="#help" class="wpallimport-help" title="<?php _e('By default all categories above the matched category will also be assigned to the post. If enabled, only the imported category will be assigned to the post.', 'wp_all_import_plugin'); ?>" style="position:relative; top: -1px;">?</a>
																		</div>
																	</div>
																</div>
															</div>															
														</div>
														<div class="input">
															<input type="radio" name="tax_logic[<?php echo $ctx->name;?>]" value="multiple" id="tax_logic_multiple_<?php echo $ctx->name;?>" class="switcher" <?php echo (!empty($post['tax_logic'][$ctx->name]) and $post['tax_logic'][$ctx->name] == 'multiple') ? 'checked="checked"' : ''; ?>/>
															<label for="tax_logic_multiple_<?php echo $ctx->name;?>"><?php printf(__('Each %s has multiple %s', 'wp_all_import_plugin'), $custom_type->labels->singular_name, $ctx->labels->name); ?></label>
															<div class="switcher-target-tax_logic_multiple_<?php echo $ctx->name;?> sub_input">
																<input type="hidden" name="multiple_term_assing[<?php echo $ctx->name;?>]" value="0"/>
																<input type="checkbox" name="multiple_term_assing[<?php echo $ctx->name;?>]" <?php echo (isset($post['multiple_term_assing'][$ctx->name])) ? (( ! empty($post['multiple_term_assing'][$ctx->name]) ) ? 'checked="checked"' : '') : 'checked="checked"'; ?> title="<?php _e('Assign post to the taxonomy.','wp_all_import_plugin');?>" value="1"/>																
																<input type="text" class="widefat multiple_xpath_field" name="tax_multiple_xpath[<?php echo $ctx->name; ?>]" value="<?php echo ( ! empty($post['tax_multiple_xpath'][$ctx->name])) ? esc_textarea($post['tax_multiple_xpath'][$ctx->name]) : ''; ?>" style="width:50%;"/>
																<label><?php _e('Separated by', 'wp_all_import_plugin'); ?></label>										
																<input type="text" class="small tax_delim" name="tax_multiple_delim[<?php echo $ctx->name; ?>]" value="<?php echo ( ! empty($post['tax_multiple_delim'][$ctx->name]) ) ? str_replace("&amp;","&", htmlentities(htmlentities($post['tax_multiple_delim'][$ctx->name]))) : ',' ?>" />
																<div class="input tax_is_full_search_multiple" style="margin: 10px 0;">
																	<input type="hidden" name="tax_is_full_search_multiple[<?php echo $ctx->name; ?>]" value="0"/>
																	<input type="checkbox" id="tax_is_full_search_multiple_<?php echo $ctx->name; ?>" class="switcher" <?php if ( ! empty($post['tax_is_full_search_multiple'][$ctx->name]) ) echo "checked='checked'"; ?> name="tax_is_full_search_multiple[<?php echo $ctx->name; ?>]" value="1"/>
																	<label for="tax_is_full_search_multiple_<?php echo $ctx->name;?>"><?php printf(__('Try to match terms to existing child %s', 'wp_all_import_plugin'), $ctx->labels->name); ?></label>
																	<div class="switcher-target-tax_is_full_search_multiple_<?php echo $ctx->name; ?> sub_input">
																		<div class="input tax_assign_to_one_term_multiple" style="margin: 10px 0;">
																			<input type="hidden" name="tax_assign_to_one_term_multiple[<?php echo $ctx->name; ?>]" value="0"/>
																			<input type="checkbox" id="tax_assign_to_one_term_multiple_<?php echo $ctx->name; ?>" <?php if ( ! empty($post['tax_assign_to_one_term_multiple'][$ctx->name]) ) echo "checked='checked'"; ?> name="tax_assign_to_one_term_multiple[<?php echo $ctx->name; ?>]" value="1"/>
																			<label for="tax_assign_to_one_term_multiple_<?php echo $ctx->name;?>"><?php printf(__('Only assign %s to the imported %s, not the entire hierarchy', 'wp_all_import_plugin'), $custom_type->labels->name, $ctx->labels->singular_name); ?></label>
																			<a href="#help" class="wpallimport-help" title="<?php _e('By default all categories above the matched category will also be assigned to the post. If enabled, only the imported category will be assigned to the post.', 'wp_all_import_plugin'); ?>" style="position:relative; top: -1px;">?</a>
																		</div>
																	</div>
																</div>
															</div>															
														</div>
														<?php if ($ctx->hierarchical): ?>
														<div class="input">
															<input type="radio" name="tax_logic[<?php echo $ctx->name;?>]" value="hierarchical" id="tax_logic_hierarchical_<?php echo $ctx->name;?>" class="switcher" <?php echo (!empty($post['tax_logic'][$ctx->name]) and $post['tax_logic'][$ctx->name] == 'hierarchical') ? 'checked="checked"' : ''; ?>/>
															<label for="tax_logic_hierarchical_<?php echo $ctx->name;?>"><?php printf(__('%ss have hierarchical (parent/child) %s (i.e. Sports > Golf > Clubs > Putters)', 'wp_all_import_plugin'), $custom_type->labels->singular_name, $ctx->labels->name); ?></label>
															<div class="switcher-target-tax_logic_hierarchical_<?php echo $ctx->name;?> sub_input">
																<div class="input">
																	<input type="hidden" name="tax_hierarchical_logic_entire[<?php echo $ctx->name;?>]" value="0" />
																	<input type="checkbox" name="tax_hierarchical_logic_entire[<?php echo $ctx->name;?>]" value="1" id="hierarchical_logic_entire_<?php echo $ctx->name;?>" class="switcher" <?php echo (!empty($post['tax_hierarchical_logic_entire'][$ctx->name])) ? 'checked="checked"' : ''; ?>/>
																	<label for="hierarchical_logic_entire_<?php echo $ctx->name;?>"><?php _e('An element in my file contains the entire hierarchy (i.e. you have an element with a value = Sports > Golf > Clubs > Putters)', 'wp_all_import_plugin'); ?></label>
																	<div class="switcher-target-hierarchical_logic_entire_<?php echo $ctx->name;?> sub_input" style="margin-left: 20px; padding-left: 20px;">
																		<ul class="tax_hierarchical_logic no-margin">
																			<?php $txes_count = 0; if ( ! empty($post['tax_hierarchical_xpath'][$ctx->name])): foreach ($post['tax_hierarchical_xpath'][$ctx->name] as $k => $path) : if (empty($path)) continue; ?>
																				<li class="dragging">
																					<div style="position:relative;">
																						<input type="hidden" name="tax_hierarchical_assing[<?php echo $ctx->name;?>][<?php echo $k;?>]" value="0"/>
																						<input type="checkbox" class="assign_term" name="tax_hierarchical_assing[<?php echo $ctx->name;?>][<?php echo $k; ?>]" <?php echo (isset($post['tax_hierarchical_assing'][$ctx->name][$k])) ? (( ! empty($post['tax_hierarchical_assing'][$ctx->name][$k]) ) ? 'checked="checked"' : '') : 'checked="checked"'; ?> title="<?php _e('Assign post to the taxonomy.','wp_all_import_plugin');?>" value="1"/>
																						<input type="text" class="widefat hierarchical_xpath_field" name="tax_hierarchical_xpath[<?php echo $ctx->name; ?>][]" value="<?php echo esc_textarea($path); ?>"/>
																						<a href="javascript:void(0);" class="icon-item remove-ico" style="top:8px;"></a>
																					</div>
																				</li>
																			<?php $txes_count++; endforeach; endif; ?>
																			<?php if ( ! $txes_count): ?>
																				<li class="dragging">
																					<div style="position:relative;">
																						<input type="hidden" name="tax_hierarchical_assing[<?php echo $ctx->name;?>][0]" value="0"/>
																						<input type="checkbox" class="assign_term" name="tax_hierarchical_assing[<?php echo $ctx->name;?>][0]" checked="checked" title="<?php _e('Assign post to the taxonomy.','wp_all_import_plugin');?>" value="1"/>
																				    	<input type="text" class="widefat hierarchical_xpath_field" name="tax_hierarchical_xpath[<?php echo $ctx->name; ?>][]" value=""/>
																				    	<a href="javascript:void(0);" class="icon-item remove-ico" style="top:8px;"></a>
																				    </div>
																			    </li>
																			<?php endif; ?>
																			<li class="dragging template">
																				<div style="position:relative;">
																					<input type="hidden" name="tax_hierarchical_assing[<?php echo $ctx->name;?>][NUMBER]" value="0"/>
																					<input type="checkbox" class="assign_term" name="tax_hierarchical_assing[<?php echo $ctx->name;?>][NUMBER]" checked="checked" title="<?php _e('Assign post to the taxonomy.','wp_all_import_plugin');?>" value="1"/>
																			    	<input type="text" class="widefat hierarchical_xpath_field" name="tax_hierarchical_xpath[<?php echo $ctx->name; ?>][]" value=""/>
																			    	<a href="javascript:void(0);" class="icon-item remove-ico" style="top:8px;"></a>
																			    </div>
																		    </li>
																		</ul>																		
																		<label><?php _e('Separated by', 'wp_all_import_plugin'); ?></label>										
																		<input type="text" class="small tax_delim" name="tax_hierarchical_delim[<?php echo $ctx->name; ?>]" value="<?php echo ( ! empty($post['tax_hierarchical_delim'][$ctx->name]) ) ? str_replace("&amp;","&", htmlentities(htmlentities($post['tax_hierarchical_delim'][$ctx->name]))) : '>' ?>" />
																		<div class="input">
																			<input type="hidden" name="tax_hierarchical_last_level_assign[<?php echo $ctx->name; ?>]" value="0" />
																			<input type="checkbox" id="tax_hierarchical_last_level_assign_<?php echo $ctx->name; ?>" name="tax_hierarchical_last_level_assign[<?php echo $ctx->name; ?>]" value="1" <?php echo ( ! empty($post['tax_hierarchical_last_level_assign'][$ctx->name])) ? 'checked="checked"': '' ?> />
																			<label for="tax_hierarchical_last_level_assign_<?php echo $ctx->name; ?>"><?php printf(__('Only assign %s to the bottom level term in the hierarchy', 'wp_all_import_plugin'), $custom_type->label) ?></label>			
																		</div>
																		<div class="input">
																			<input type="hidden" name="is_tax_hierarchical_group_delim[<?php echo $ctx->name; ?>]" value="0" />
																			<input type="checkbox" id="is_tax_hierarchical_group_delim_<?php echo $ctx->name; ?>" name="is_tax_hierarchical_group_delim[<?php echo $ctx->name; ?>]" value="1" class="switcher" <?php echo ( ! empty($post['is_tax_hierarchical_group_delim'][$ctx->name])) ? 'checked="checked"': '' ?> />
																			<label for="is_tax_hierarchical_group_delim_<?php echo $ctx->name; ?>"><?php printf(__('Separate hierarchy groups via symbol', 'wp_all_import_plugin'), $custom_type->label) ?></label>			
																			<div class="switcher-target-is_tax_hierarchical_group_delim_<?php echo $ctx->name;?> sub_input">
																				<label><?php _e('Separated by', 'wp_all_import_plugin'); ?></label>										
																				<input type="text" class="small tax_delim" name="tax_hierarchical_group_delim[<?php echo $ctx->name; ?>]" value="<?php echo ( ! empty($post['tax_hierarchical_group_delim'][$ctx->name]) ) ? str_replace("&amp;","&", htmlentities(htmlentities($post['tax_hierarchical_group_delim'][$ctx->name]))) : '|' ?>" />
																			</div>
																		</div>
																		<a class="preview_taxonomies" href="javascript:void(0);" style="top:-35px; float: right; position: relative;" rel="preview_taxonomies"><?php _e('Preview', 'wp_all_import_plugin'); ?></a>
																		<div class="input">
																			<a href="javascript:void(0);" class="icon-item add-new-cat" style="width: 200px;"><?php _e('Add Another Hierarchy Group','wp_all_import_plugin');?></a> 																			
																		</div>
																	</div>
																</div>
																<div class="input">
																	<input type="hidden" name="tax_hierarchical_logic_manual[<?php echo $ctx->name;?>]" value="0" />
																	<input type="checkbox" name="tax_hierarchical_logic_manual[<?php echo $ctx->name;?>]" value="1" id="hierarchical_logic_manual_<?php echo $ctx->name;?>" class="switcher" <?php echo (!empty($post['tax_hierarchical_logic_manual'][$ctx->name])) ? 'checked="checked"' : ''; ?>/>
																	<label for="hierarchical_logic_manual_<?php echo $ctx->name;?>"><?php _e('Manually design the hierarchy with drag & drop', 'wp_all_import_plugin'); ?></label>
																	<div class="switcher-target-hierarchical_logic_manual_<?php echo $ctx->name;?> sub_input">
																		<p style="margin-bottom: 10px;"><?php printf(__('Drag the <img src="%s" class="wpallimport-drag-icon"/> to the right to create a child, drag up and down to re-order.'), WP_ALL_IMPORT_ROOT_URL . '/static/img/drag.png'); ?></p>
																		<ol class="sortable no-margin" style="margin-left: 20px;">
																			<?php
																			if ( ! empty($post['post_taxonomies'][$ctx->name]) ):

																				$taxonomies_hierarchy = json_decode($post['post_taxonomies'][$ctx->name]);
																				
																				if (!empty($taxonomies_hierarchy) and is_array($taxonomies_hierarchy)): $i = 0; 

																					foreach ($taxonomies_hierarchy as $cat) { $i++;
																						if (is_null($cat->parent_id) or empty($cat->parent_id))
																						{
																							?>
																							<li id="item_<?php echo $i; ?>" class="dragging">
																								<div class="drag-element">		
																									<input type="checkbox" class="assign_term" <?php if (!empty($cat->assign)): ?>checked="checked"<?php endif; ?> title="<?php _e('Assign post to the taxonomy.','wp_all_import_plugin');?>"/>
																									<input type="text" class="widefat xpath_field" value="<?php echo esc_textarea($cat->xpath); ?>"/>
																									
																									<?php do_action('pmxi_category_view', $cat, $i, $ctx->name, $post_type); ?>

																								</div>
																								<?php if ($i>1):?><a href="javascript:void(0);" class="icon-item remove-ico"></a><?php endif;?>
																								<?php echo reverse_taxonomies_html($taxonomies_hierarchy, $cat->item_id, $i, $ctx->name, $post_type); ?>
																							</li>
																							<?php
																						}
																					}

																				endif;
																			else:
																			?>
																			<li id="item_1" class="dragging">
																				<div class="drag-element">		
																					<input type="checkbox" class="assign_term" checked="checked" title="<?php _e('Assign post to the taxonomy.','wp_all_import_plugin');?>"/>
																					<input type="text" class="widefat xpath_field" value=""/>																																									
																				</div>
																				<a href="javascript:void(0);" class="icon-item remove-ico"></a>
																				<ol>
																					<li id="item_2" class="dragging">
																		            	<div class="drag-element">	            		
																		            		<input type="checkbox" class="assign_term" checked="checked" title="<?php _e('Assign post to the taxonomy.','wp_all_import_plugin');?>"/>
																		            		<input class="widefat xpath_field" type="text" value=""/>	            																				            		
																		            	</div>
																		            	<a href="javascript:void(0);" class="icon-item remove-ico"></a>																		            	
																		            </li>
																				</ol>																				
																			</li>
																			<?php
																			endif;?>

																			<li id="item" class="template">
																		    	<div class="drag-element">
																		    		<input type="checkbox" class="assign_term" checked="checked" title="<?php _e('Assign post to the taxonomy.','wp_all_import_plugin');?>"/>														    		
																		    		<input type="text" class="widefat xpath_field" value=""/>
																		    		<?php do_action('pmxi_category_view', false, false, $ctx->name, $post_type); ?>
																		    	</div>
																		    	<a href="javascript:void(0);" class="icon-item remove-ico"></a>
																		    </li>

																		</ol>																		
																		<input type="hidden" class="hierarhy-output" name="post_taxonomies[<?php echo $ctx->name; ?>]" value="<?php echo empty($post['post_taxonomies'][$ctx->name]) ? '' : esc_attr($post['post_taxonomies'][$ctx->name]) ?>"/>
																		<?php do_action('pmxi_category_options_view', ((!empty($post['post_taxonomies'][$ctx->name])) ? $post['post_taxonomies'][$ctx->name] : false), $ctx->name, $post_type, $ctx->labels->name); ?>														
																		<div class="input" style="margin-left:17px;">
																			<label><?php _e('Separated by', 'wp_all_import_plugin'); ?></label>										
																			<input type="text" class="small tax_delim" name="tax_manualhierarchy_delim[<?php echo $ctx->name; ?>]" value="<?php echo ( ! empty($post['tax_manualhierarchy_delim'][$ctx->name]) ) ? str_replace("&amp;","&", htmlentities(htmlentities($post['tax_manualhierarchy_delim'][$ctx->name]))) : ',' ?>" />
																		</div>
																		<a href="javascript:void(0);" class="icon-item add-new-ico"><?php _e('Add Another Row','wp_all_import_plugin');?></a>
																	</div>																	
																</div>
															</div>
														</div>
														<?php endif; ?>
														<div class="input" style="margin: 4px;">													
															<?php
																$tax_mapping = ( ! empty($post['tax_mapping'][$ctx->name]) ) ? json_decode($post['tax_mapping'][$ctx->name], true) : false;
															?>
															<input type="hidden" name="tax_enable_mapping[<?php echo $ctx->name; ?>]" value="0"/>
															<input type="checkbox" id="tax_mapping_<?php echo $ctx->name; ?>" class="pmxi_tax_mapping switcher" <?php if ( ! empty($post['tax_enable_mapping'][$ctx->name]) ) echo "checked='checked'"; ?> name="tax_enable_mapping[<?php echo $ctx->name; ?>]" value="1"/>
															<label for="tax_mapping_<?php echo $ctx->name;?>"><?php printf(__('Enable Mapping for %s', 'wp_all_import_plugin'), $ctx->labels->name); ?></label>
															<div class="switcher-target-tax_mapping_<?php echo $ctx->name;?> sub_input custom_type" rel="tax_mapping">
																<fieldset style="padding: 0;">															
																	<table cellpadding="0" cellspacing="5" class="tax-form-table" rel="tax_mapping_<?php echo $ctx->name; ?>" style="width: 100%;">
																		<thead>
																			<tr>
																				<td><?php _e('In Your File', 'wp_all_import_plugin') ?></td>
																				<td><?php _e('Translated To', 'wp_all_import_plugin') ?></td>
																				<td>&nbsp;</td>						
																			</tr>
																		</thead>
																		<tbody>	
																			<?php																																				
																				if ( ! empty($tax_mapping) and is_array($tax_mapping) ){

																					foreach ($tax_mapping as $key => $value) {

																						$k = $key;

																						if (is_array($value)){
																							$keys = array_keys($value);
																							$k = $keys[0];
																						}

																						?>
																						<tr class="form-field">
																							<td>
																								<input type="text" class="mapping_from widefat" value="<?php echo esc_textarea($k); ?>">
																							</td>
																							<td>
																								<input type="text" class="mapping_to widefat" value="<?php echo esc_textarea((is_array($value)) ? $value[$k] : $value); ?>">
																							</td>
																							<td class="action remove">
																								<a href="#remove" style="right:-10px; top: 7px;"></a>
																							</td>
																						</tr>
																						<?php
																					}

																				}
																				else{
																					?>
																					<tr class="form-field">
																						<td>
																							<input type="text" class="mapping_from widefat">
																						</td>
																						<td>
																							<input type="text" class="mapping_to widefat">
																						</td>
																						<td class="action remove">
																							<a href="#remove" style="right:-10px; top: 7px;"></a>
																						</td>
																					</tr>
																					<?php
																				}
																			?>												
																			<tr class="form-field template">
																				<td>
																					<input type="text" class="mapping_from widefat">
																				</td>
																				<td>
																					<input type="text" class="mapping_to widefat">
																				</td>
																				<td class="action remove">
																					<a href="#remove" style="right:-10px; top: 7px;"></a>
																				</td>
																			</tr>
																			<tr>
																				<td colspan="3">
																					<a href="javascript:void(0);" title="<?php _e('Add Another Rule', 'wp_all_import_plugin')?>" class="action add-new-key add-new-entry"><?php _e('Add Another Rule', 'wp_all_import_plugin') ?></a>
																				</td>
																			</tr>																	
																		</tbody>
																	</table>															
																	<input type="hidden" name="tax_mapping[<?php echo $ctx->name; ?>]" value="<?php if (!empty($post['tax_mapping'][$ctx->name])) echo esc_html($post['tax_mapping'][$ctx->name]); ?>"/>
																</fieldset>																
																<div class="input">																	
																	<input type="hidden" name="tax_logic_mapping[<?php echo $ctx->name; ?>]" value="0"/>
																	<input type="checkbox" id="tax_logic_mapping_<?php echo $ctx->name; ?>" name="tax_logic_mapping[<?php echo $ctx->name; ?>]" <?php echo ( ! empty($post['tax_logic_mapping'][$ctx->name]) ) ? 'checked="checked"' : ''; ?> value="1"/>
																	<label for="tax_logic_mapping_<?php echo $ctx->name; ?>"><?php _e('Apply mapping rules before splitting via separator symbol','wp_all_import_plugin'); ?></label>																	
																</div>
															</div>
														</div>															
													</div>											
												</div>
											</div>
										</td>
									</tr>
								</table>					
								<?php endforeach; ?>	
								<?php if ($private_ctx): ?>						
								<hr/>			
								<div class="input">
									<input type="checkbox" id="show_hidden_ctx"/>
									<label for="show_hidden_ctx"><?php _e('Show "private" taxonomies', 'wp_all_import_plugin'); ?></label>					
								</div>
								<?php endif;?>
							</td>
						</tr>												
					</table>
				</div>
			</div>
		</div>
		<div id="taxonomies_hints" style="display:none;">	
			<ul>
				<li><?php _e('Taxonomies that don\'t already exist on your site will be created.', 'wp_all_import_plugin'); ?></li>
				<li><?php _e('To import to existing parent taxonomies, use the existing taxonomy name or slug.', 'wp_all_import_plugin'); ?></li>
				<li><?php _e('To import to existing hierarchical taxonomies, create the entire hierarchy using the taxonomy names or slugs.', 'wp_all_import_plugin'); ?></li>			
			</ul>
		</div>
	</div>
<?php endif; ?>		