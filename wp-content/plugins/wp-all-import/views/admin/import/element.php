
<h2 class="wpallimport-wp-notices"></h2>

<form class="wpallimport-choose-elements no-enter-submit wpallimport-step-2 wpallimport-wrapper" method="post">	
	<div class="wpallimport-header">
		<div class="wpallimport-logo"></div>
		<div class="wpallimport-title">
			<p><?php _e('WP All Import', 'wp_all_import_plugin'); ?></p>
			<h2><?php _e('Import XML / CSV', 'wp_all_import_plugin'); ?></h2>					
		</div>
		<div class="wpallimport-links">
			<a href="http://www.wpallimport.com/support/" target="_blank"><?php _e('Support', 'wp_all_import_plugin'); ?></a> | <a href="http://www.wpallimport.com/documentation/" target="_blank"><?php _e('Documentation', 'wp_all_import_plugin'); ?></a>
		</div>
	</div>	
	<div class="clear"></div>	
	<?php $custom_type = get_post_type_object( PMXI_Plugin::$session->custom_type ); ?>
	<div class="wpallimport-content-section wpallimport-console">
		<div class="ajax-console">
			<?php if ($this->errors->get_error_codes()): ?>
				<?php $this->error() ?>			
			<?php endif ?>
		</div>		
		<input type="submit" class="button button-primary button-hero wpallimport-large-button" value="<?php _e('Continue to Step 3', 'wp_all_import_plugin'); ?>" style="position:absolute; top:45px; right:10px;"/>
	</div>
	
	<div class="wpallimport-content-section wpallimport-elements-preloader">
		<div class="preload" style="height: 80px; margin-top: 25px;"></div>
	</div>

	<div class="wpallimport-content-section" style="padding-bottom:0; max-height: 600px; overflow:scroll; width: 100%;">

		<table class="wpallimport-layout" style="width:100%;">
			<tr>				
				<?php if ( ! $is_csv): ?>
				<td class="left" style="width: 25%; min-width: unset; border-right: 1px solid #ddd;">
					<h3 class="txt_center"><?php _e('What element are you looking for?', 'wp_all_import_plugin'); ?></h3>				
					<?php
					if ( ! empty($elements_cloud) and ! $is_csv ){												
						foreach ($elements_cloud as $tag => $count){
							?>
							<a href="javascript:void(0);" rel="<?php echo $tag;?>" class="wpallimport-change-root-element <?php if (PMXI_Plugin::$session->source['root_element'] == $tag) echo 'selected';?>">
								<span class="tag_name"><?php echo strtolower($tag); ?></span>
								<span class="tag_count"><?php echo $count; ?></span>
							</a>
							<?php
						}						
					}
					?>			
				</td>			
				<?php endif; ?>	
				<td class="right" <?php if ( ! $is_csv){?>style="width:75%; padding:0;"<?php } else {?>style="width:100%; padding:0;"<?php }?>>
					<div class="action_buttons">
						<table style="width:100%;">
							<tr>
								<td>
									<a href="javascript:void(0);" id="prev_element" class="wpallimport-go-to">&nbsp;</a>
								</td>
								<td class="txt_center">

									<p class="wpallimport-root-element">
										<?php echo PMXI_Plugin::$session->source['root_element'];?>
									</p>								
									<input type="text" id="goto_element" value="1"/>
									<span class="wpallimport-elements-information">
										<?php printf(__('of <span class="wpallimport-elements-count-info">%s</span>','wp_all_import_plugin'), PMXI_Plugin::$session->count);?> 
									</span>																	

								</td>
								<td>
									<a href="javascript:void(0);" id="next_element" class="wpallimport-go-to">&nbsp;</a>
								</td>
							</tr>
						</table>																
					</div>
					<fieldset class="widefat" style="background:fafafa;">												
						
						<div class="input">

							<?php if ($is_csv !== false): ?>										
															
								<div class="wpallimport-set-csv-delimiter">
									<label>
										<?php _e("Set delimiter for CSV fields:", "pmxi_plugin"); ?>
									</label>									
									<input type="text" name="delimiter" value="<?php echo $is_csv;?>"/> 
									<input type="button" name="apply_delimiter" class="rad4" value="<?php _e('Apply', 'wp_all_import_plugin'); ?>"/>									
								</div>							

							<?php else: ?>
							
								<input type="hidden" value="" name="delimiter"/>

							<?php endif; ?>
						
						</div>

						<div class="wpallimport-xml">
							<?php //$this->render_xml_element($dom->documentElement) ?>
						</div>
					</fieldset>		
					<div class="import_information">
						<?php if (PMXI_Plugin::$session->wizard_type == 'new') :?>
						<h3>
							<?php printf(__('Each <span>&lt;<span class="root_element">%s</span>&gt;</span> element will be imported into a <span>New %s</span>'), PMXI_Plugin::$session->source['root_element'], $custom_type->labels->singular_name); ?>
						</h3>
						<?php else: ?>
						<h3>
							<?php printf(__('Data in <span>&lt;<span class="root_element">%s</span>&gt;</span> elements will be imported to <span>%s</span>'), PMXI_Plugin::$session->source['root_element'], $custom_type->labels->name); ?>
						</h3>
						<?php endif; ?>
						
						<h3 class="wp_all_import_warning">
							<?php _e('This doesn\'t look right, try manually selecting a different root element on the left.'); ?>
						</h3>
						
					</div>
				</td>
			</tr>
		</table>
	</div>

	<div class="wpallimport-collapsed closed">
		<div class="wpallimport-content-section">
			<div class="wpallimport-collapsed-header">
				<h3><?php _e('Add Filtering Options', 'wp_all_import_plugin'); ?></h3>
			</div>
			<div class="wpallimport-collapsed-content">
				<div>
					<div class="rule_inputs">
						<table style="width:100%;">
							<tr>
								<th><?php _e('Element', 'wp_all_import_plugin'); ?></th>
								<th><?php _e('Rule', 'wp_all_import_plugin'); ?></th>
								<th><?php _e('Value', 'wp_all_import_plugin'); ?></th>
								<th>&nbsp;</th>
							</tr>
							<tr>
								<td style="width:25%;">
									<select id="pmxi_xml_element">
										<option value=""><?php _e('Select Element', 'wp_all_import_plugin'); ?></option>
										<?php PMXI_Render::render_xml_elements_for_filtring($elements->item(0)); ?>
									</select>
								</td>
								<td style="width:25%;">
									<select id="pmxi_rule">
										<option value=""><?php _e('Select Rule', 'wp_all_import_plugin'); ?></option>
										<option value="equals"><?php _e('equals', 'wp_all_import_plugin'); ?></option>
										<option value="not_equals"><?php _e('not equals', 'wp_all_import_plugin'); ?></option>
										<option value="greater"><?php _e('greater than', 'wp_all_import_plugin');?></option>
										<option value="equals_or_greater"><?php _e('equals or greater than', 'wp_all_import_plugin'); ?></option>
										<option value="less"><?php _e('less than', 'wp_all_import_plugin'); ?></option>
										<option value="equals_or_less"><?php _e('equals or less than', 'wp_all_import_plugin'); ?></option>
										<option value="contains"><?php _e('contains', 'wp_all_import_plugin'); ?></option>
										<option value="not_contains"><?php _e('not contains', 'wp_all_import_plugin'); ?></option>
										<option value="is_empty"><?php _e('is empty', 'wp_all_import_plugin'); ?></option>
										<option value="is_not_empty"><?php _e('is not empty', 'wp_all_import_plugin'); ?></option>
									</select>
								</td>
								<td style="width:25%;">
									<input id="pmxi_value" type="text" placeholder="value" value=""/>
								</td>
								<td style="width:15%;">
									<a id="pmxi_add_rule" href="javascript:void(0);"><?php _e('Add Rule', 'wp_all_import_plugin');?></a>
								</td>
							</tr>
						</table>						
					</div>					
				</div>
				<div class="clear"></div>				
				<table class="xpath_filtering">
					<tr>
						<td style="width:5%; font-weight:bold; color: #000;"><?php _e('XPath','wp_all_import_plugin');?></td>
						<td style="width:95%;">
							<input type="text" name="xpath" value="<?php echo esc_attr($post['xpath']) ?>" style="max-width:none;" />					
							<input type="hidden" id="root_element" name="root_element" value="<?php echo PMXI_Plugin::$session->source['root_element']; ?>"/>					
						</td>
					</tr>
				</table>				
			</div>
		</div>	
		<div id="wpallimport-filters" class="wpallimport-collapsed-content" style="padding:0;">
			<table style="width: 100%; font-weight: bold; padding: 20px;">
				<tr>					
					<td style="width: 30%; padding-left: 30px;"><?php _e('Element', 'wp_all_import_plugin'); ?></td>
					<td style="width:20%;"><?php _e('Rule', 'wp_all_import_plugin'); ?></td>
					<td style="width:20%;"><?php _e('Value', 'wp_all_import_plugin'); ?></td>
					<td style="width:25%;"><?php _e('Condition', 'wp_all_import_plugin'); ?></td>
				</tr>
			</table>
			<div class="wpallimport-content-section">					
				<fieldset id="filtering_rules">					
					<p style="margin:20px 0 5px; text-align:center;"><?php _e('No filtering options. Add filtering options to only import records matching some specified criteria.', 'wp_all_import_plugin');?></p>					
					<ol class="filtering_rules">
						
					</ol>	
					<div class="clear"></div>				
					<a href="javascript:void(0);" id="apply_filters" style="display:none;"><?php _e('Apply Filters To XPath', 'wp_all_import_plugin');?></a>
				</fieldset>
			</div>	
		</div>
	</div>

	<hr>

	<p class="wpallimport-submit-buttons" style="text-align:center;">
		<a href="<?php echo add_query_arg('action', 'index', $this->baseUrl); ?>" class="back rad3"><?php _e('Back to Step 1','wp_all_import_plugin');?></a>
		&nbsp;
		<input type="hidden" name="is_submitted" value="1" />
		<?php wp_nonce_field('choose-elements', '_wpnonce_choose-elements') ?>
		<input type="submit" class="button button-primary button-hero wpallimport-large-button" value="<?php _e('Continue to Step 3', 'wp_all_import_plugin'); ?>" />
	</p>

	<a href="http://soflyy.com/" target="_blank" class="wpallimport-created-by"><?php _e('Created by', 'wp_all_import_plugin'); ?> <span></span></a>
	
</form>