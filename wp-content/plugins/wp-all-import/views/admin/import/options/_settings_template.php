<div class="wpallimport-collapsed closed wpallimport-section">
	<div class="wpallimport-content-section">
		<div class="wpallimport-collapsed-header">
			<h3><?php _e('Advanced Options','wp_all_import_plugin');?></h3>	
		</div>
		<div class="wpallimport-collapsed-content" style="padding: 0;">
			<div class="wpallimport-collapsed-content-inner">				
				<table class="form-table" style="max-width:none;">
					<tr>
						<td colspan="3">
							<h4><?php _e('Import Speed Optimization', 'wp_all_import_plugin'); ?></h4>									
							<div class="input">															
								<div class="input">
									<input type="radio" id="import_default_processing" class="switcher" name="import_processing" value="default" <?php echo ('ajax' != $post['import_processing']) ? 'checked="checked"': '' ?> style="margin-left:0;"/>
									<label for="import_default_processing"><?php _e('High Speed Small File Processing', 'wp_all_import_plugin' )?> <a href="#help" class="wpallimport-help" style="position:relative; top:0;" title="<?php _e('If the import takes longer than your server\'s timeout settings (max_execution_time, mod_fcgid read data timeout, etc.) it will fail.', 'wp_all_import_plugin'); ?>">?</a></label>					
								</div>
								
								<input type="radio" id="import_ajax_processing" class="switcher" name="import_processing" value="ajax" <?php echo 'ajax' == $post['import_processing'] ? 'checked="checked"': '' ?> style="margin-left:0;"/>
								<label for="import_ajax_processing"><?php _e('Iterative, Piece-by-Piece Processing', 'wp_all_import_plugin' )?></label>					
								
								<span class="switcher-target-import_ajax_processing pl17" style="display:block; clear: both; width: 100%;">
									<div class="pl17" style="margin:5px 0px;">							
										<label for="records_per_request"><?php _e('In each iteration, process', 'wp_all_import_plugin');?></label> <input type="text" name="records_per_request" style="vertical-align:middle; font-size:11px; background:#fff !important; width: 40px; text-align:center;" value="<?php echo esc_attr($post['records_per_request']) ?>" /> <?php _e('records', 'wp_all_import_plugin'); ?>
										<a href="#help" class="wpallimport-help" style="position: relative; top: -2px;" title="<?php _e('WP All Import must be able to process this many records in less than your server\'s timeout settings. If your import fails before completion, to troubleshoot you should lower this number. If you are importing images, especially high resolution images, high numbers here are probably a bad idea, since downloading the images can take lots of time - for example, 20 posts with 5 images each = 100 images. At 500Kb per image that\'s 50Mb that needs to be downloaded. Can your server download that before timing out? If not, the import will fail.', 'wp_all_import_plugin'); ?>">?</a>							
									</div>
									<div class="input pl17" style="margin:5px 0px;">
										<input type="hidden" name="chuncking" value="0" />
										<input type="checkbox" id="chuncking" name="chuncking" value="1" class="fix_checkbox" <?php echo $post['chuncking'] ? 'checked="checked"': '' ?>/>
										<label for="chuncking"><?php _e('Split file up into <strong>' . PMXI_Plugin::getInstance()->getOption('large_feed_limit') . '</strong> record chunks.', 'wp_all_import_plugin');?></label> 
										<a href="#help" class="wpallimport-help" style="position: relative; top: -2px;" title="<?php _e('This option will decrease the amount of slowdown experienced at the end of large imports. The slowdown is partially caused by the need for WP All Import to read deeper and deeper into the file on each successive iteration. Splitting the file into pieces means that, for example, instead of having to read 19000 records into a 20000 record file when importing the last 1000 records, WP All Import will just split it into 20 chunks, and then read the last chunk from the beginning.','wp_all_import_plugin'); ?>">?</a>							
									</div>							
								</span>									
							</div>				
							<div class="input">
								<input type="hidden" name="is_fast_mode" value="0" />
								<input type="checkbox" id="is_fast_mode" name="is_fast_mode" value="1" class="fix_checkbox" <?php echo $post['is_fast_mode'] ? 'checked="checked"': '' ?>/>
								<label for="is_fast_mode"><?php _e('Increase speed by disabling do_action calls in wp_insert_post during import.', 'wp_all_import_plugin') ?> 
									<a href="#help" class="wpallimport-help" style="position: relative; top: -2px;" title="<?php _e('This option is for advanced users with knowledge of WordPress development. Your theme or plugins may require these calls when posts are created. Next action will be disabled: \'transition_post_status\', \'save_post\', \'pre_post_update\', \'add_attachment\', \'edit_attachment\', \'edit_post\', \'post_updated\', \'wp_insert_post\'. Verify your created posts work properly if you check this box.', 'wp_all_import_plugin') ?>">?</a></label>
							</div>					
							<?php if ( ! $this->isWizard ): ?>

								<h4><?php _e('Post Type', 'wp_all_import_plugin'); ?></h4>
								<p><?php _e('Editing this will change the post type of the posts processed by this import. Re-run the import for the changes to take effect.', 'wp_all_import_plugin');?></p> <br>
								
									
								<input type="hidden" name="custom_type" value="<?php echo $post['custom_type'];?>">	

								<?php
									$hiddenPosts = array(
                                        'attachment',
                                        'revision',
                                        'nav_menu_item',
                                        'shop_webhook',
                                        'import_users',
                                        'wp-types-group',
                                        'wp-types-user-group',
                                        'wp-types-term-group',
                                        'acf-field',
                                        'acf-field-group',
                                        'custom_css',
                                        'customize_changeset',
                                        'oembed_cache'
                                    );
									$custom_types = get_post_types(array('_builtin' => true), 'objects') + get_post_types(array('_builtin' => false, 'show_ui' => true), 'objects'); 
									foreach ($custom_types as $key => $ct) {
										if (in_array($key, $hiddenPosts)) unset($custom_types[$key]);
									}
									$custom_types = apply_filters( 'pmxi_custom_types', $custom_types );

									$sorted_cpt = array();
									foreach ($custom_types as $key => $cpt){

										$sorted_cpt[$key] = $cpt;

										// Put users & comments & taxonomies after Pages
										if ( ! empty($custom_types['page']) && $key == 'page' || empty($custom_types['page']) && $key == 'post' ){
											$sorted_cpt['import_users'] = new stdClass();
											$sorted_cpt['import_users']->labels = new stdClass();
											$sorted_cpt['import_users']->labels->name = __('Users','wp_all_export_plugin');
											break;
										}
									}
									$order = array('shop_order', 'shop_coupon', 'shop_customer', 'product');
									foreach ($order as $cpt){
										if (!empty($custom_types[$cpt])) $sorted_cpt[$cpt] = $custom_types[$cpt];
									}

									uasort($custom_types, "wp_all_import_cmp_custom_types");

									foreach ($custom_types as $key => $cpt) {
										if (empty($sorted_cpt[$key])){
											$sorted_cpt[$key] = $cpt;
										}
									}

									$hidden_post_types = get_post_types(array('_builtin' => false, 'show_ui' => false), 'objects');
									foreach ($hidden_post_types as $key => $ct) {
										if (in_array($key, $hiddenPosts)) unset($hidden_post_types[$key]);
									}
									$hidden_post_types = apply_filters( 'pmxi_custom_types', $hidden_post_types );

								?>	
								<div class="wpallimport-change-custom-type">
									<select name="custom_type_selector" id="custom_type_selector" class="wpallimport-post-types">									
										<?php if ( ! empty($sorted_cpt)): $unknown_cpt = array(); ?>
											<?php foreach ($sorted_cpt as $key => $ct) :?>
												<?php 
													$image_src = 'dashicon-cpt';

													$cpt = $key;
													$cpt_label = $ct->labels->name;

													if (  in_array($cpt, array('post', 'page', 'product', 'import_users', 'shop_order', 'shop_coupon', 'shop_customer', 'users', 'comments', 'taxonomies') ) )
													{
														$image_src = 'dashicon-' . $cpt;										
													}
													else
													{
														$unknown_cpt[$key] = $ct;
														continue;
													}														
												?>
											<option value="<?php echo $cpt; ?>" data-imagesrc="dashicon <?php echo $image_src; ?>" <?php if ( $cpt == $post['custom_type'] ) echo 'selected="selected"';?>><?php echo $cpt_label; ?></option>
											<?php endforeach; ?>
											<?php if ( ! empty($unknown_cpt)):  ?>
												<?php foreach ($unknown_cpt as $key => $ct):?>
													<?php
													$image_src = 'dashicon-cpt';																								
													$cpt_label = $ct->labels->name;												
													?>
													<option value="<?php echo $key;?>" data-imagesrc="dashicon <?php echo $image_src; ?>" <?php if ($key == $post['custom_type']) echo 'selected="selected"'; ?>><?php echo $cpt_label; ?></option>
												<?php endforeach ?>
											<?php endif;?>
										<?php endif; ?>
										<?php if ( ! empty($hidden_post_types)): ?>							
											<?php foreach ($hidden_post_types as $key => $cpt) :?>	
												<?php 
													$image_src = 'dashicon-cpt';
													if (  in_array($key, array('post', 'page', 'product') ) )
														$image_src = 'dashicon-' . $key;
												?>
											<option value="<?php echo $key; ?>" data-imagesrc="dashicon <?php echo $image_src; ?>" <?php if ( $key == $post['custom_type'] ) echo 'selected="selected"';?>><?php echo $cpt->labels->name; ?></option>								
											<?php endforeach; ?>
										<?php endif; ?>			
									</select>

									<?php if ( ! class_exists('PMUI_Plugin') ): ?>
										<div class="wpallimport-upgrade-notice" rel="import_users">
											<p><?php _e('The User Import Add-On is Required to Import Users', 'wp_all_import_plugin'); ?></p>
											<a href="http://www.wpallimport.com/checkout/?edd_action=add_to_cart&download_id=1921&edd_options%5Bprice_id%5D=1" target="_blank" class="upgrade_link"><?php _e('Purchase the User Import Add-On', 'wp_all_import_plugin');?></a>
										</div>
									<?php endif; ?>
									<?php if ( class_exists('WooCommerce') && ! class_exists('PMWI_Plugin') ): ?>
										<div class="wpallimport-upgrade-notice" rel="product">
											<p><?php _e('The WooCommerce Add-On is Required to Import Products', 'wp_all_import_plugin'); ?></p>
											<a href="http://www.wpallimport.com/order-now/?utm_source=free-plugin&utm_campaign=wooco-products&utm_medium=in-plugin" target="_blank" class="upgrade_link"><?php _e('Get the WooCommerce Add-On', 'wp_all_import_plugin');?></a>
										</div>
									<?php endif; ?>
									<?php if ( class_exists('WooCommerce') &&  ( ! class_exists('PMWI_Plugin') || class_exists('PMWI_Plugin') && PMWI_EDITION == 'free') ): ?>
										<div class="wpallimport-upgrade-notice" rel="shop_order">
											<?php if (class_exists('PMWI_Plugin') && PMWI_EDITION == 'free'): ?>
												<p><?php _e('The Pro version of the WooCommerce Add-On is required to Import Orders, but you have the free version installed.', 'wp_all_import_plugin'); ?></p>
											<?php else: ?>
												<p><?php _e('The WooCommerce Add-On Pro is Required to Import Orders', 'wp_all_import_plugin'); ?></p>
											<?php endif; ?>
											<a href="http://www.wpallimport.com/order-now/?utm_source=free-plugin&utm_campaign=wooco-orders&utm_medium=in-plugin" target="_blank" class="upgrade_link"><?php _e('Purchase the WooCommerce Add-On Pro', 'wp_all_import_plugin');?></a>
										</div>
										<div class="wpallimport-upgrade-notice" rel="shop_coupon">
											<?php if (class_exists('PMWI_Plugin') && PMWI_EDITION == 'free'): ?>
												<p><?php _e('The Pro version of the WooCommerce Add-On is required to Import Coupons, but you have the free version installed.', 'wp_all_import_plugin'); ?></p>
											<?php else: ?>
												<p><?php _e('The WooCommerce Add-On Pro is Required to Import Coupons', 'wp_all_import_plugin'); ?></p>
											<?php endif; ?>
											<a href="http://www.wpallimport.com/order-now/?utm_source=free-plugin&utm_campaign=wooco-coupons&utm_medium=in-plugin" target="_blank" class="upgrade_link"><?php _e('Purchase the WooCommerce Add-On Pro', 'wp_all_import_plugin');?></a>
										</div>
									<?php endif; ?>
									<div class="wpallimport-upgrade-notice" rel="taxonomies">
										<p><?php _e('WP All Import Pro is Required to Import Taxonomies', 'wp_all_import_plugin'); ?></p>
										<a href="http://www.wpallimport.com/order-now/?utm_source=free-plugin&utm_campaign=taxonomies&utm_medium=in-plugin" target="_blank" class="upgrade_link"><?php _e('Purchase WP All Import Pro', 'wp_all_import_plugin');?></a>
									</div>

								</div>

								<h4><?php _e('XPath', 'wp_all_import_plugin'); ?></h4>
								<p><?php _e('Editing this can break your entire import. You will have to re-create it from scratch.', 'wp_all_import_plugin');?></p> <br>
								<div class="input">
									<input type="text" name="xpath" value="<?php echo esc_attr($import->xpath) ?>" style="width: 50%; font-size: 18px; color: #555; height: 50px; padding: 10px;"/>
								</div>														
								<?php if ( ! empty($post['delimiter']) ): ?>
								<h4><?php _e('CSV Delimiter', 'wp_all_import_plugin'); ?></h4>
								<div class="input">
									<input type="text" name="delimiter" value="<?php echo $post['delimiter']; ?>" style="width: 50px !important; font-size: 18px; color: #555; height: 50px; padding: 10px;"/>
								</div>
								<?php endif; ?>
								<h4><?php _e('Downloads', 'wp_all_import_plugin'); ?></h4>

								<div class="input">
									<button class="button button-primary download_import_template" rel="<?php echo add_query_arg(array('page' => 'pmxi-admin-manage', 'id' => $_GET['id'], 'action' => 'get_template', '_wpnonce' => wp_create_nonce( '_wpnonce-download_template' )), $this->baseUrl); ?>" style="background-image: none;"><?php _e('Import Template', 'wp_all_import_plugin'); ?></button>
									<button class="button button-primary download_import_bundle" rel="<?php echo add_query_arg(array('page' => 'pmxi-admin-manage', 'id' => $_GET['id'], 'action' => 'bundle', '_wpnonce' => wp_create_nonce( '_wpnonce-download_bundle' )), $this->baseUrl); ?>" style="background-image: none;"><?php _e('Import Bundle', 'wp_all_import_plugin'); ?></button>
								</div>
							<?php endif; ?>
							<h4><?php _e('Other', 'wp_all_import_plugin'); ?></h4>
							<div class="input">
								<input type="hidden" name="is_import_specified" value="0" />
								<input type="checkbox" id="is_import_specified" class="switcher fix_checkbox" name="is_import_specified" value="1" <?php echo $post['is_import_specified'] ? 'checked="checked"': '' ?>/>
								<label for="is_import_specified"><?php _e('Import only specified records', 'wp_all_import_plugin') ?> <a href="#help" class="wpallimport-help" style="position:relative; top:0;" title="<?php _e('Enter records or record ranges separated by commas, e.g. <b>1,5,7-10</b> would import the first, the fifth, and the seventh to tenth.', 'wp_all_import_plugin') ?>">?</a></label>
								<div class="switcher-target-is_import_specified" style="vertical-align:middle">
									<div class="input" style="display:inline;">
										<input type="text" name="import_specified" value="<?php echo esc_attr($post['import_specified']) ?>" style="width:320px;"/>
									</div>
								</div>
							</div>						
							<?php if (isset($source_type) and in_array($source_type, array('ftp', 'file'))): ?>						
								<div class="input">
									<input type="hidden" name="is_delete_source" value="0" />
									<input type="checkbox" id="is_delete_source" class="fix_checkbox" name="is_delete_source" value="1" <?php echo $post['is_delete_source'] ? 'checked="checked"': '' ?>/>
									<label for="is_delete_source"><?php _e('Delete source XML file after importing', 'wp_all_import_plugin') ?> <a href="#help" class="wpallimport-help" style="position:relative; top:0;" title="<?php _e('This setting takes effect only when script has access rights to perform the action, e.g. file is not deleted when pulled via HTTP or delete permission is not granted to the user that script is executed under.', 'wp_all_import_plugin') ?>">?</a></label>
								</div>						
							<?php endif; ?>
							<?php if (class_exists('PMLC_Plugin')): // option is only valid when `WP Wizard Cloak` pluign is enabled ?>						
								<div class="input">
									<input type="hidden" name="is_cloak" value="0" />
									<input type="checkbox" id="is_cloak" class="fix_checkbox" name="is_cloak" value="1" <?php echo $post['is_cloak'] ? 'checked="checked"': '' ?>/>
									<label for="is_cloak"><?php _e('Auto-Cloak Links', 'wp_all_import_plugin') ?> <a href="#help" class="wpallimport-help" style="position:relative; top:0;" title="<?php printf(__('Automatically process all links present in body of created post or page with <b>%s</b> plugin', 'wp_all_import_plugin'), PMLC_Plugin::getInstance()->getName()) ?>">?</a></label>
								</div> 						
							<?php endif; ?>	
							<div class="input">
								<input type="hidden" name="xml_reader_engine" value="0" />
								
								<?php if ( PMXI_Plugin::getInstance()->getOption('force_stream_reader') ): ?>
									<input type="checkbox" id="xml_reader_engine" class="fix_checkbox" name="xml_reader_engine" value="1" checked="checked" disabled="disabled"/>
									<label for="xml_reader_engine"><?php _e('Use StreamReader instead of XMLReader to parse import file', 'wp_all_import_plugin') ?> <a href="#help" class="wpallimport-help" style="position:relative; top:0;" title="<?php _e('WP All Import is being forced to use Stream Reader for all imports. Go to WP All Import ▸ Settings to modify this setting.', 'wp_all_import_plugin'); ?>">?</a></label>
								<?php else : ?>
									<input type="checkbox" id="xml_reader_engine" class="fix_checkbox" name="xml_reader_engine" value="1" <?php echo $post['xml_reader_engine'] ? 'checked="checked"': '' ?>/>
									<label for="xml_reader_engine"><?php _e('Use StreamReader instead of XMLReader to parse import file', 'wp_all_import_plugin') ?> <a href="#help" class="wpallimport-help" style="position:relative; top:0;" title="<?php _e('XMLReader is much faster, but has a bug that sometimes prevents certain records from being imported with import files that contain special cases.', 'wp_all_import_plugin'); ?>">?</a></label>
								<?php endif; ?>																	
							</div>						
							
							<div class="input" style="margin-top: 15px;">
								<p><?php _e('Friendly Name','wp_all_import_plugin');?></p> <br>
								<div class="input">
									<input type="text" name="friendly_name" style="vertical-align:middle; background:#fff !important; width: 50%;" value="<?php echo esc_attr($post['friendly_name']) ?>" />
								</div>
							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>