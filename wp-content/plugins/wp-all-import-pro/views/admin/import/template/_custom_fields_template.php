<?php
switch ($post_type){
	case 'taxonomies':
		$custom_type = new stdClass();
		$custom_type->labels = new stdClass();
		$custom_type->labels->name = __('Taxonomy Terms', 'wp_all_import_plugin');
		$custom_type->labels->singular_name = __('Taxonomy Term', 'wp_all_import_plugin');
		break;
	default:
		$custom_type = get_post_type_object( $post_type );
		break;
}
?>
<div class="wpallimport-collapsed closed wpallimport-section wpallimport-custom-fields">
	<div class="wpallimport-content-section">
		<div class="wpallimport-collapsed-header">
			<h3><?php _e('Custom Fields','wp_all_import_plugin');?></h3>	
		</div>
		<div class="wpallimport-collapsed-content" style="padding: 0;">
			<div class="wpallimport-collapsed-content-inner">
				<script type="text/javascript">
					__META_KEYS = <?php echo json_encode($meta_keys) ?>;
				</script>			
				<?php if (empty($post['custom_name'])): ?>
				<div class="input cf_welcome">
					<?php if ( ! empty($meta_keys) ):?>
						<h1 style="font-size:23px; color:#40acad;"><?php printf(__('Your website is using Custom Fields to store data for %s.', 'wp_all_import_plugin'), $custom_type->labels->name); ?></h1>
						<a class="autodetect_cf auto_detect_cf" href="javascript:void(0);" rel="auto_detect_cf"><?php _e('See Detected Fields', 'wp_all_import_plugin'); ?></a>
					<?php else: ?>
						<h1 style="font-size:23px; color:#40acad;"><?php printf(__('No Custom Fields are present in your database for %s.', 'wp_all_import_plugin'), $custom_type->labels->name); ?></h1>
						<p class="wpallimport-note"><?php printf(__('Manually create a %s, and fill out each field you want to import data to. WP All Import will then display these fields as available for import below.', 'wp_all_import_plugin'), $custom_type->labels->singular_name); ?></p>
					<?php endif;?>
					<a href="javascript:void(0);" class="wpallimport-dismiss-cf-welcome"><?php _e('Hide Notice', 'wp_all_import_plugin'); ?></a>				
				</div>
				<div class="input cf_detect_result" style="display:none;">
					<h1 style="font-size:23px; color:#40acad;"> 
						<span class="cf_detected"></span> 
						<a class="autodetect_cf clear_detected_cf" href="javascript:void(0);" rel="clear_detected_cf"><?php _e('Clear All Fields', 'wp_all_import_plugin'); ?></a>
					</h1>
					<p class="wpallimport-note"><?php printf(__('If not all fields were detected, manually create a %s, and fill out each field you want to import data to. Then create a new import, and WP All Import will display these fields as available for import below.', 'wp_all_import_plugin'), $custom_type->labels->singular_name); ?></p>
					<a href="javascript:void(0);" class="wpallimport-dismiss-cf-welcome"><?php _e('Hide Notice', 'wp_all_import_plugin'); ?></a>				
				</div>			
				<?php endif; ?>
				<table class="form-table wpallimport-custom-fields-list" style="max-width:none;">
					<tr>
						<td colspan="3" style="padding-top:20px;">
							
							<table class="form-table custom-params" style="max-width:none; border:none;">
								<thead>
									<tr>
										<td style="padding-bottom:10px;"><?php _e('Name', 'wp_all_import_plugin') ?></td>
										<td style="padding-bottom:10px;"><?php _e('Value', 'wp_all_import_plugin') ?></td>					
									</tr>
								</thead>
								<tbody>				
									<?php if (!empty($post['custom_name'])):?>
										<?php foreach ($post['custom_name'] as $i => $name): ?>
											<?php $custom_mapping_rules = (!empty($post['custom_mapping_rules'][$i])) ? json_decode($post['custom_mapping_rules'][$i], true) : false; ?>
											<tr class="form-field">
												<td style="width: 45%;">
													<input type="text" name="custom_name[]"  value="<?php echo esc_attr($name) ?>" class="widefat wp_all_import_autocomplete" style="margin-bottom:10px;"/>
													<input type="hidden" name="custom_format[]" value="<?php echo ( ! empty($post['custom_format'][$i]) ) ? '1' : '0'; ?>"/>												
												</td>
												<td class="action">
													<div class="custom_type" rel="default">
														<textarea name="custom_value[]" class="widefat" <?php echo ( ! empty($post['custom_format'][$i]) ) ? 'style="display:none;"' : ''; ?>><?php echo esc_html($post['custom_value'][$i]) ?></textarea>
														<a class="specify_cf pmxi_cf_pointer" rel="serialized_<?php echo $i; ?>" href="javascript:void(0);" <?php echo ( empty($post['custom_format'][$i]) ) ? 'style="display:none;"' : ''; ?>><?php _e('Click to specify', 'wp_all_import_plugin'); ?></a>
														<div class="input wpallimport-custom-fields-actions">
															<a href="javascript:void(0);" class="wpallimport-cf-options"><?php _e('Field Options...', 'wp_all_import_plugin'); ?></a>
															<ul id="wpallimport-cf-menu-<?php echo $i;?>" class="wpallimport-cf-menu">
																<li class="<?php echo ( ! empty($post['custom_format'][$i]) ) ? 'active' : ''; ?>">
																	<a href="javascript:void(0);" class="set_serialize"><?php _e('Serialized', 'wp_all_import_plugin'); ?></a>
																</li>
																<li class="<?php echo ( ! empty($custom_mapping_rules) ) ? 'active' : ''; ?>">
																	<a href="javascript:void(0);" class="set_mapping pmxi_cf_mapping" rel="cf_mapping_<?php echo $i; ?>"><?php _e('Mapping', 'wp_all_import_plugin'); ?></a>
																</li>
															</ul>														
														</div>
													</div>
													<div id="serialized_<?php echo $i; ?>" class="custom_type" rel="serialized" style="display:none;">
														<fieldset>
															<table cellpadding="0" cellspacing="5" class="cf-form-table" rel="serialized_<?php echo $i; ?>">
																<thead>
																	<tr>
																		<td><?php _e('Key', 'wp_all_import_plugin') ?></td>
																		<td><?php _e('Value', 'wp_all_import_plugin') ?></td>
																		<td>&nbsp;</td>						
																	</tr>
																</thead>
																<tbody>	
																	<?php

																		$serialized_values = (!empty($post['serialized_values'][$i])) ? json_decode($post['serialized_values'][$i], true) : false;
																		
																		$filtered_serialized_values = array();

																		if ( ! empty($serialized_values) and is_array($serialized_values))

																			$filtered_serialized_values = array_filter($serialized_values);

																		if ( ! empty($filtered_serialized_values)){

																			foreach ( $filtered_serialized_values as $key => $value) {

																				$k = $key;

																				if (is_array($value)){
																					$keys = array_keys($value);
																					$k = $keys[0];
																				}

																				?>
																				<tr class="form-field">
																					<td>
																						<input type="text" class="serialized_key widefat" value="<?php echo $k; ?>">
																					</td>
																					<td>
																						<input type="text" class="serialized_value widefat" value="<?php echo esc_html((is_array($value)) ? $value[$k] : $value); ?>">
																					</td>
																					<td class="action remove">
																						<a href="#remove" style="right:-10px;"></a>
																					</td>
																				</tr>
																				<?php
																			}
																		}
																		else{
																			?>
																			<tr class="form-field">
																				<td>
																					<input type="text" class="serialized_key widefat">
																				</td>
																				<td>
																					<input type="text" class="serialized_value widefat">
																				</td>
																				<td class="action remove">
																					<a href="#remove" style="right:-10px;"></a>
																				</td>
																			</tr>
																			<?php
																		}
																	?>												
																	<tr class="form-field template">
																		<td>
																			<input type="text" class="serialized_key widefat">
																		</td>
																		<td>
																			<input type="text" class="serialized_value widefat">
																		</td>
																		<td class="action remove">
																			<a href="#remove" style="right:-10px;"></a>
																		</td>
																	</tr>
																	<tr>
																		<td colspan="3">
																			<a href="javascript:void(0);" title="<?php _e('Add Custom Field', 'wp_all_import_plugin')?>" class="action add-new-key add-new-entry"><?php _e('Add Another', 'wp_all_import_plugin') ?></a>
																		</td>
																	</tr>
																	<tr>
																		<td>
																			<div class="wrap" style="position:relative;">
																				<a class="save_popup auto_detect_sf" href="javascript:void(0);"><?php _e('Auto-Detect', 'wp_all_import_plugin'); ?></a>
																			</div>
																		</td>														
																		<td colspan="2">
																			<div class="wrap" style="position:relative;">
																				<a class="save_popup save_sf" href="javascript:void(0);"><?php _e('Save', 'wp_all_import_plugin'); ?></a>
																			</div>
																		</td>
																	</tr>																	
																</tbody>
															</table>
															<input type="hidden" name="serialized_values[]" value="<?php if (!empty($post['serialized_values'][$i])) echo esc_html($post['serialized_values'][$i]); ?>"/>
														</fieldset>
													</div>

													<div id="cf_mapping_<?php echo $i; ?>" class="custom_type" rel="mapping" style="display:none;">
														<fieldset>
															<table cellpadding="0" cellspacing="5" class="cf-form-table" rel="cf_mapping_<?php echo $i; ?>">
																<thead>
																	<tr>
																		<td><?php _e('In Your File', 'wp_all_import_plugin') ?></td>
																		<td><?php _e('Translated To', 'wp_all_import_plugin') ?></td>
																		<td>&nbsp;</td>						
																	</tr>
																</thead>
																<tbody>	
																	<?php																		
																		
																		if ( ! empty($custom_mapping_rules) and is_array($custom_mapping_rules)){
																			
																			foreach ($custom_mapping_rules as $key => $value) {

																				$k = $key;

																				if (is_array($value)){
																					$keys = array_keys($value);
																					$k = $keys[0];
																				}

																				?>
																				<tr class="form-field">
																					<td>
																						<input type="text" class="mapping_from widefat" value="<?php echo $k; ?>">
																					</td>
																					<td>
																						<input type="text" class="mapping_to widefat" value="<?php echo (is_array($value)) ? $value[$k] : $value; ?>">
																					</td>
																					<td class="action remove">
																						<a href="#remove" style="right:-10px;"></a>
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
																					<a href="#remove" style="right:-10px;"></a>
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
																			<a href="#remove" style="right:-10px;"></a>
																		</td>
																	</tr>
																	<tr>
																		<td colspan="3">
																			<a href="javascript:void(0);" title="<?php _e('Add Another', 'wp_all_import_plugin')?>" class="action add-new-key add-new-entry"><?php _e('Add Another', 'wp_all_import_plugin') ?></a>
																		</td>
																	</tr>
																	<tr>																										
																		<td colspan="3">
																			<div class="wrap" style="position:relative;">
																				<a class="save_popup save_mr" href="javascript:void(0);"><?php _e('Save Rules', 'wp_all_import_plugin'); ?></a>
																			</div>
																		</td>
																	</tr>
																</tbody>
															</table>
															<input type="hidden" name="custom_mapping_rules[]" value="<?php if (!empty($custom_mapping_rules)) echo esc_html($post['custom_mapping_rules'][$i]); ?>"/>
														</fieldset>
													</div>

													<span class="action remove">
														<a href="#remove" style="top: 8px; right: 0;"></a>
													</span>
												</td>														
											</tr>
										<?php endforeach ?>
									<?php else: ?>
										<tr class="form-field">
											<td style="width: 45%;">
												<input type="text" name="custom_name[]"  value="" class="widefat wp_all_import_autocomplete" style="margin-bottom:10px;"/>
												<input type="hidden" name="custom_format[]" value="0"/>											
											</td>
											<td class="action">
												<div class="custom_type" rel="default">
													<textarea name="custom_value[]" class="widefat"></textarea>
													<a class="specify_cf pmxi_cf_pointer" rel="serialized_0" href="javascript:void(0);" style="display:none;"><?php _e('Click to specify', 'wp_all_import_plugin'); ?></a>
													<div class="input wpallimport-custom-fields-actions">
														<a href="javascript:void(0);" class="wpallimport-cf-options"><?php _e('Field Options...', 'wp_all_import_plugin'); ?></a>
														<ul id="wpallimport-cf-menu-0" class="wpallimport-cf-menu">
															<li>
																<a href="javascript:void(0);" class="set_serialize"><?php _e('Serialized', 'wp_all_import_plugin'); ?></a>
															</li>
															<li>
																<a href="javascript:void(0);" class="set_mapping pmxi_cf_mapping" rel="cf_mapping_0"><?php _e('Mapping', 'wp_all_import_plugin'); ?></a>
															</li>
														</ul>																					
													</div>
												</div>
												<div id="serialized_0" class="custom_type" rel="serialized" style="display:none;">
													<fieldset>
														<table cellpadding="0" cellspacing="5" class="cf-form-table" rel="serialized_0">
															<thead>
																<tr>
																	<td><?php _e('Key', 'wp_all_import_plugin') ?></td>
																	<td><?php _e('Value', 'wp_all_import_plugin') ?></td>
																	<td>&nbsp;</td>						
																</tr>
															</thead>
															<tbody>	
																<tr class="form-field">
																	<td>
																		<input type="text" class="serialized_key widefat" value="">
																	</td>
																	<td>
																		<input type="text" class="serialized_value widefat" value="">
																	</td>
																	<td class="action remove">
																		<a href="#remove" style="right:-10px;"></a>
																	</td>
																</tr>
																<tr class="form-field template">
																	<td>
																		<input type="text" class="serialized_key widefat" value="">
																	</td>
																	<td>
																		<input type="text" class="serialized_value widefat"value="">
																	</td>
																	<td class="action remove">
																		<a href="#remove" style="right:-10px;"></a>
																	</td>
																</tr>
																<tr>
																	<td colspan="3">
																		<a href="javascript:void(0);" title="<?php _e('Add Another', 'wp_all_import_plugin')?>" class="action add-new-key add-new-entry"><?php _e('Add Another', 'wp_all_import_plugin') ?></a>
																	</td>
																</tr>
																<tr>
																	<td>
																		<div class="wrap" style="position:relative;">
																			<a class="save_popup auto_detect_sf" href="javascript:void(0);"><?php _e('Auto-Detect', 'wp_all_import_plugin'); ?></a>
																		</div>
																	</td>														
																	<td colspan="2">
																		<div class="wrap" style="position:relative;">
																			<a class="save_popup save_sf" href="javascript:void(0);"><?php _e('Save', 'wp_all_import_plugin'); ?></a>
																		</div>
																	</td>
																</tr>
															</tbody>
														</table>
														<input type="hidden" name="serialized_values[]" value=""/>
													</fieldset>
												</div>

												<div id="cf_mapping_0" class="custom_type" rel="mapping" style="display:none;">
													<fieldset>
														<table cellpadding="0" cellspacing="5" class="cf-form-table" rel="cf_mapping_0">
															<thead>
																<tr>
																	<td><?php _e('In Your File', 'wp_all_import_plugin') ?></td>
																	<td><?php _e('Translated To', 'wp_all_import_plugin') ?></td>
																	<td>&nbsp;</td>						
																</tr>
															</thead>
															<tbody>	
																<tr class="form-field">
																	<td>
																		<input type="text" class="mapping_from widefat" value="">
																	</td>
																	<td>
																		<input type="text" class="mapping_to widefat" value="">
																	</td>
																	<td class="action remove">
																		<a href="#remove" style="right:-10px;"></a>
																	</td>
																</tr>
																<tr class="form-field template">
																	<td>
																		<input type="text" class="mapping_from widefat" value="">
																	</td>
																	<td>
																		<input type="text" class="mapping_to widefat"value="">
																	</td>
																	<td class="action remove">
																		<a href="#remove" style="right:-10px;"></a>
																	</td>
																</tr>
																<tr>
																	<td colspan="3">
																		<a href="javascript:void(0);" title="<?php _e('Add Another', 'wp_all_import_plugin')?>" class="action add-new-key add-new-entry"><?php _e('Add Another', 'wp_all_import_plugin') ?></a>
																	</td>
																</tr>
																<tr>																						
																	<td colspan="3">
																		<div class="wrap" style="position:relative;">
																			<a class="save_popup save_mr" href="javascript:void(0);"><?php _e('Save Rules', 'wp_all_import_plugin'); ?></a>
																		</div>
																	</td>
																</tr>
															</tbody>
														</table>
														<input type="hidden" name="custom_mapping_rules[]" value=""/>
													</fieldset>
												</div>

												<span class="action remove">
													<a href="#remove" style="top: 8px; right: 0;"></a>
												</span>
											</td>													
										</tr>
									<?php endif;?>
									<tr class="form-field template">
										<td style="width: 45%;">
											<input type="text" name="custom_name[]" value="" class="widefat wp_all_import_autocomplete" style="margin-bottom:10px;"/>
											<input type="hidden" name="custom_format[]" value="0"/>										
										</td>
										<td class="action">
											<div class="custom_type" rel="default">
												<textarea name="custom_value[]" class="widefat"></textarea>
												<a class="specify_cf pmxi_cf_pointer" href="javascript:void(0);" style="display:none;"><?php _e('Click to specify', 'wp_all_import_plugin'); ?></a>
												<div class="input wpallimport-custom-fields-actions">
													<a href="javascript:void(0);" class="wpallimport-cf-options"><?php _e('Field Options...', 'wp_all_import_plugin'); ?></a>
													<ul class="wpallimport-cf-menu">
														<li>
															<a href="javascript:void(0);" class="set_serialize"><?php _e('Serialized', 'wp_all_import_plugin'); ?></a>
														</li>
														<li>
															<a href="javascript:void(0);" class="set_mapping pmxi_cf_mapping"><?php _e('Mapping', 'wp_all_import_plugin'); ?></a>
														</li>
													</ul>
												</div>
											</div>
											<div class="custom_type" rel="serialized" style="display:none;">
												<fieldset>
													<table cellpadding="0" cellspacing="5" class="cf-form-table">
														<thead>
															<tr>
																<td><?php _e('Key', 'wp_all_import_plugin') ?></td>
																<td><?php _e('Value', 'wp_all_import_plugin') ?></td>	
																<td>&nbsp;</td>				
															</tr>
														</thead>
														<tbody>
															<tr class="form-field">
																<td>
																	<input type="text" class="serialized_key widefat">
																</td>
																<td>
																	<input type="text" class="serialized_value widefat">
																</td>
																<td class="action remove">
																	<a href="#remove" style="right:-10px;"></a>
																</td>
															</tr>
															<tr class="form-field template">
																<td>
																	<input type="text" class="serialized_key widefat">
																</td>
																<td>
																	<input type="text" class="serialized_value widefat">
																</td>
																<td class="action remove">
																	<a href="#remove" style="right:-10px;"></a>
																</td>
															</tr>
															<tr>
																<td colspan="3">
																	<a href="javascript:void(0);" title="<?php _e('Add Another', 'wp_all_import_plugin'); ?>" class="action add-new-key add-new-entry"><?php _e('Add Another', 'wp_all_import_plugin') ?></a>
																</td>
															</tr>
															<tr>
																<td>
																	<div class="wrap" style="position:relative;">
																		<a class="save_popup auto_detect_sf" href="javascript:void(0);"><?php _e('Auto-Detect', 'wp_all_import_plugin'); ?></a>
																	</div>
																</td>														
																<td colspan="2">
																	<div class="wrap" style="position:relative;">
																		<a class="save_popup save_sf" href="javascript:void(0);"><?php _e('Save', 'wp_all_import_plugin'); ?></a>
																	</div>
																</td>
															</tr>
														</tbody>
													</table>
													<input type="hidden" name="serialized_values[]" value=""/>
												</fieldset>
											</div>

											<div class="custom_type" rel="mapping" style="display:none;">
												<fieldset>
													<table cellpadding="0" cellspacing="5" class="cf-form-table">
														<thead>
															<tr>
																<td><?php _e('In Your File', 'wp_all_import_plugin') ?></td>
																<td><?php _e('Translated To', 'wp_all_import_plugin') ?></td>	
																<td>&nbsp;</td>				
															</tr>
														</thead>
														<tbody>
															<tr class="form-field">
																<td>
																	<input type="text" class="mapping_from widefat">
																</td>
																<td>
																	<input type="text" class="mapping_to widefat">
																</td>
																<td class="action remove">
																	<a href="#remove" style="right:-10px;"></a>
																</td>
															</tr>
															<tr class="form-field template">
																<td>
																	<input type="text" class="mapping_from widefat">
																</td>
																<td>
																	<input type="text" class="mapping_to widefat">
																</td>
																<td class="action remove">
																	<a href="#remove" style="right:-10px;"></a>
																</td>
															</tr>
															<tr>
																<td colspan="3">
																	<a href="javascript:void(0);" title="<?php _e('Add Another', 'wp_all_import_plugin')?>" class="action add-new-key add-new-entry"><?php _e('Add Another', 'wp_all_import_plugin') ?></a>
																</td>
															</tr>
															<tr>																			
																<td colspan="3">
																	<div class="wrap" style="position:relative;">
																		<a class="save_popup save_mr" href="javascript:void(0);"><?php _e('Save Rules', 'wp_all_import_plugin'); ?></a>
																	</div>
																</td>
															</tr>
														</tbody>
													</table>
													<input type="hidden" name="custom_mapping_rules[]" value=""/>
												</fieldset>
											</div>

											<span class="action remove">
												<a href="#remove" style="top: 8px; right: 0;"></a>
											</span>
										</td>
									</tr>
									<tr>
										<td colspan="2"><a href="javascript:void(0);" title="<?php _e('Add Custom Field', 'wp_all_import_plugin')?>" class="action add-new-custom add-new-entry"><?php _e('Add Custom Field', 'wp_all_import_plugin') ?></a></td>
									</tr>
								</tbody>
							</table>

							<input type="hidden" id="existing_meta_keys" value="<?php echo esc_html(implode(',', $meta_keys)); ?>"/>
													
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>
<div class="wpallimport-overlay"></div>