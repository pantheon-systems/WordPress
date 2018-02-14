<?php
switch ($post_type){
	case 'taxonomies':
		$custom_type = new stdClass();
		$custom_type->labels = new stdClass();
		$custom_type->labels->singular_name = __('Taxonomy Term', 'wp_all_import_plugin');
		break;
	default:
		$custom_type = get_post_type_object( $post_type );
		break;
}
?>
<div class="wpallimport-collapsed closed wpallimport-section ">
	<div class="wpallimport-content-section ">
		<div class="wpallimport-collapsed-header">
			<h3><?php printf(__('Other %s Options','wp_all_import_plugin'), $custom_type->labels->singular_name);?></h3>	
		</div>
		<div class="wpallimport-collapsed-content" style="padding: 0;">
			<div class="wpallimport-collapsed-content-inner">
				<table class="form-table" style="max-width:none;">
					<tr>
						<td>					
							<input type="hidden" name="encoding" value="<?php echo ($this->isWizard) ? PMXI_Plugin::$session->encoding : $post['encoding']; ?>"/>
							<input type="hidden" name="delimiter" value="<?php echo ($this->isWizard) ? PMXI_Plugin::$session->is_csv : $post['delimiter']; ?>"/>

							<?php $is_support_post_format = ( current_theme_supports( 'post-formats' ) && post_type_supports( $post_type, 'post-formats' ) ) ? true : false; ?>
							
							<h4><?php _e('Post Status', 'wp_all_import_plugin') ?></h4>									
							<div class="input">
								<input type="radio" id="status_publish" name="status" value="publish" <?php echo 'publish' == $post['status'] ? 'checked="checked"' : '' ?> class="switcher"/>
								<label for="status_publish"><?php _e('Published', 'wp_all_import_plugin') ?></label>
							</div>
							<div class="input">
								<input type="radio" id="status_draft" name="status" value="draft" <?php echo 'draft' == $post['status'] ? 'checked="checked"' : '' ?> class="switcher"/>
								<label for="status_draft"><?php _e('Draft', 'wp_all_import_plugin') ?></label>
							</div>
							<div class="input fleft" style="position:relative;width:220px;">
								<input type="radio" id="status_xpath" class="switcher" name="status" value="xpath" <?php echo 'xpath' == $post['status'] ? 'checked="checked"': '' ?>/>
								<label for="status_xpath"><?php _e('Set with XPath', 'wp_all_import_plugin' )?></label> <br>
								<div class="switcher-target-status_xpath">
									<div class="input">
										&nbsp;<input type="text" class="smaller-text" name="status_xpath" style="width:190px;" value="<?php echo esc_attr($post['status_xpath']) ?>"/>
										<a href="#help" class="wpallimport-help" title="<?php _e('The value of presented XPath should be one of the following: (\'publish\', \'draft\', \'trash\').', 'wp_all_import_plugin') ?>" style="position:relative; top:13px; float: right;">?</a>
									</div>
								</div>
							</div>								
							<div class="clear"></div>													
						</td>
					</tr>			
					<tr>
						<td>					
							<h4><?php _e('Post Dates', 'wp_all_import_plugin') ?><a href="#help" class="wpallimport-help" style="position:relative; top: 1px;" title="<?php _e('Use any format supported by the PHP <b>strtotime</b> function. That means pretty much any human-readable date will work.', 'wp_all_import_plugin') ?>">?</a></h4>
							<div class="input">
								<input type="radio" id="date_type_specific" class="switcher" name="date_type" value="specific" <?php echo 'random' != $post['date_type'] ? 'checked="checked"' : '' ?> />
								<label for="date_type_specific">
									<?php _e('As specified', 'wp_all_import_plugin') ?>
								</label>
								<div class="switcher-target-date_type_specific" style="vertical-align:middle; margin-top: 5px; margin-bottom: 10px;">
									<input type="text" class="datepicker" name="date" value="<?php echo esc_attr($post['date']) ?>"/>
								</div>
							</div>
							<div class="input">
								<input type="radio" id="date_type_random" class="switcher" name="date_type" value="random" <?php echo 'random' == $post['date_type'] ? 'checked="checked"' : '' ?> />
								<label for="date_type_random">
									<?php _e('Random dates', 'wp_all_import_plugin') ?><a href="#help" class="wpallimport-help" style="position:relative; top:0;" title="<?php _e('Posts will be randomly assigned dates in this range. WordPress ensures posts with dates in the future will not appear until their date has been reached.', 'wp_all_import_plugin') ?>">?</a>
								</label>
								<div class="switcher-target-date_type_random" style="vertical-align:middle; margin-top:5px;">
									<input type="text" class="datepicker" name="date_start" value="<?php echo esc_attr($post['date_start']) ?>" />
									<?php _e('and', 'wp_all_import_plugin') ?>
									<input type="text" class="datepicker" name="date_end" value="<?php echo esc_attr($post['date_end']) ?>" />
								</div>
							</div>											
						</td>
					</tr>
					<tr>
						<td>
							<h4><?php _e('Comments', 'wp_all_import_plugin'); ?></h4>
							<div class="input">
								<input type="radio" id="comment_status_open" name="comment_status" value="open" <?php echo 'open' == $post['comment_status'] ? 'checked="checked"' : '' ?> class="switcher"/>
								<label for="comment_status_open"><?php _e('Open', 'wp_all_import_plugin') ?></label>
							</div>
							<div class="input">
								<input type="radio" id="comment_status_closed" name="comment_status" value="closed" <?php echo 'closed' == $post['comment_status'] ? 'checked="checked"' : '' ?> class="switcher"/>
								<label for="comment_status_closed"><?php _e('Closed', 'wp_all_import_plugin') ?></label>
							</div>
							<div class="input fleft" style="position:relative;width:220px;">
								<input type="radio" id="comment_status_xpath" class="switcher" name="comment_status" value="xpath" <?php echo 'xpath' == $post['comment_status'] ? 'checked="checked"': '' ?>/>
								<label for="comment_status_xpath"><?php _e('Set with XPath', 'wp_all_import_plugin' )?></label> <br>
								<div class="switcher-target-comment_status_xpath">
									<div class="input">
										&nbsp;<input type="text" class="smaller-text" name="comment_status_xpath" style="width:190px;" value="<?php echo esc_attr($post['comment_status_xpath']) ?>"/>
										<a href="#help" class="wpallimport-help" title="<?php _e('The value of presented XPath should be one of the following: (\'open\', \'closed\').', 'wp_all_import_plugin') ?>" style="position:relative; top:13px; float: right;">?</a>
									</div>
								</div>
							</div>		
						</td>
					</tr>
					<tr>
						<td>	
							<h4><?php _e('Trackbacks and Pingbacks', 'wp_all_import_plugin'); ?></h4>
							<div class="input">
								<input type="radio" id="ping_status_open" name="ping_status" value="open" <?php echo 'open' == $post['ping_status'] ? 'checked="checked"' : '' ?> class="switcher"/>
								<label for="ping_status_open"><?php _e('Open', 'wp_all_import_plugin') ?></label>
							</div>
							<div class="input">
								<input type="radio" id="ping_status_closed" name="ping_status" value="closed" <?php echo 'closed' == $post['ping_status'] ? 'checked="checked"' : '' ?> class="switcher"/>
								<label for="ping_status_closed"><?php _e('Closed', 'wp_all_import_plugin') ?></label>
							</div>
							<div class="input fleft" style="position:relative;width:220px;">
								<input type="radio" id="ping_status_xpath" class="switcher" name="ping_status" value="xpath" <?php echo 'xpath' == $post['ping_status'] ? 'checked="checked"': '' ?>/>
								<label for="ping_status_xpath"><?php _e('Set with XPath', 'wp_all_import_plugin' )?></label> <br>
								<div class="switcher-target-ping_status_xpath">
									<div class="input">
										&nbsp;<input type="text" class="smaller-text" name="ping_status_xpath" style="width:190px;" value="<?php echo esc_attr($post['ping_status_xpath']) ?>"/>
										<a href="#help" class="wpallimport-help" title="<?php _e('The value of presented XPath should be one of the following: (\'open\', \'closed\').', 'wp_all_import_plugin') ?>" style="position:relative; top:13px; float: right;">?</a>
									</div>
								</div>
							</div>								
						</td>
					</tr>
					<tr>
						<td>	
							<h4><?php _e('Post Slug', 'wp_all_import_plugin') ?></h4>
							<div>
								<input type="text" name="post_slug" style="width:100%;" value="<?php echo esc_attr($post['post_slug']); ?>" />
							</div> 
						</td>
					</tr>
					<tr>
						<td>
							<h4><?php _e('Post Author', 'wp_all_import_plugin') ?></h4>
							<div>
								<input type="text" name="author" value="<?php echo esc_attr($post['author']) ?>"/> <a href="#help" class="wpallimport-help" style="position: relative; top: -2px;" title="<?php _e('Assign the post to an existing user account by specifying the user ID, username, or e-mail address.', 'wp_all_import_plugin') ?>">?</a>			
							</div>																	
						</td>								
					</tr>	
					<tr>
						<td>
							<h4 style="float:left;"><?php _e('Download & Import Attachments', 'wp_all_import_plugin') ?></h4>
							<span class="separated_by" style="position:relative; top:15px; margin-right:0px;"><?php _e('Separated by','wp_all_import_plugin');?></span>
							<div>
								<input type="text" name="attachments" style="width:93%;" value="<?php echo esc_attr($post['attachments']) ?>" />
								<input type="text" class="small" name="atch_delim" value="<?php echo esc_attr($post['atch_delim']) ?>" style="width:5%; text-align:center; float:right;"/>
							</div>			
							<div class="input" style="margin:3px;">
								<input type="hidden" name="is_search_existing_attach" value="0" />
								<input type="checkbox" id="is_search_existing_attach" name="is_search_existing_attach" value="1" <?php echo $post['is_search_existing_attach'] ? 'checked="checked"' : '' ?> class="fix_checkbox"/>
								<label for="is_search_existing_attach"><?php _e('Search for existing attachments to prevent duplicates in media library','wp_all_import_plugin');?> </label>						
							</div>														
						</td>								
					</tr>	
					<?php if ($is_support_post_format):?>
					<tr>
						<td>													
							<h4><?php _e('Post Format', 'wp_all_import_plugin') ?></h4>
							<div>
								<?php $post_formats = get_theme_support( 'post-formats' ); ?>

								<div class="input">
									<input type="radio" id="post_format_<?php echo "standart_" . $post_type; ?>" name="post_format" value="0" <?php echo (empty($post['post_format']) or ( empty($post_formats) )) ? 'checked="checked"' : '' ?> />
									<label for="post_format_<?php echo "standart_" . $post_type; ?>"><?php _e( "Standard", 'wp_all_import_plugin') ?></label>
								</div>

								<?php								
									if ( ! empty($post_formats[0]) ){
										foreach ($post_formats[0] as $post_format) {
											?>
											<div class="input">
												<input type="radio" id="post_format_<?php echo $post_format . "_" . $entry; ?>" name="post_format" value="<?php echo $post_format; ?>" <?php echo $post_format == $post['post_format'] ? 'checked="checked"' : '' ?> />
												<label for="post_format_<?php echo $post_format . "_" . $entry; ?>"><?php _e( ucfirst($post_format), 'wp_all_import_plugin') ?></label>
											</div>
											<?php
										}
									}			
								?>
								<div class="input fleft" style="position:relative;width:220px; ">
									<input type="radio" id="post_format_xpath" class="switcher" name="post_format" value="xpath" <?php echo 'xpath' == $post['post_format'] ? 'checked="checked"': '' ?>/>
									<label for="post_format_xpath"><?php _e('Set with XPath', 'wp_all_import_plugin' )?></label> <br>
									<div class="switcher-target-post_format_xpath">
										<div class="input">
											&nbsp;<input type="text" class="smaller-text" name="post_format_xpath" style="width:190px;" value="<?php echo esc_attr($post['post_format_xpath']) ?>"/>											
										</div>
									</div>
								</div>	
							</div>									
						</td>
					</tr>
					<?php endif; ?>		

					<?php
					global $wp_version;
					if ( 'page' == $post_type || version_compare($wp_version, '4.7.0', '>=') ):?>
					<tr>
						<td>
							<h4><?php _e('Page Template', 'wp_all_import_plugin') ?></h4>
							<div class="input">
								<input type="radio" id="is_multiple_page_template_yes" name="is_multiple_page_template" value="yes" <?php echo 'yes' == $post['is_multiple_page_template'] ? 'checked="checked"' : '' ?> class="switcher" style="margin-left:0;"/>
								<label for="is_multiple_page_template_yes"><?php _e('Select a template', 'wp_all_import_plugin') ?></label>
								<div class="switcher-target-is_multiple_page_template_yes">
									<div class="input">
										<select name="page_template" id="page_template">
											<option value='default'><?php _e('Default', 'wp_all_import_plugin') ?></option>
											<?php page_template_dropdown($post['page_template']); ?>
										</select>
									</div>
								</div>
							</div>
							<div class="input fleft" style="position:relative;width:220px; margin-top: 5px;">
								<input type="radio" id="is_multiple_page_template_no" class="switcher" name="is_multiple_page_template" value="no" <?php echo 'no' == $post['is_multiple_page_template'] ? 'checked="checked"': '' ?> style="margin-left:0;"/>
								<label for="is_multiple_page_template_no"><?php _e('Set with XPath', 'wp_all_import_plugin' )?></label> <br>
								<div class="switcher-target-is_multiple_page_template_no">
									<div class="input">
										&nbsp;<input type="text" class="smaller-text" name="single_page_template" style="width:190px;" value="<?php echo esc_attr($post['single_page_template']) ?>"/>										
									</div>
								</div>
							</div>	
						</td>
					</tr>
					<?php endif; ?>
					<tr>
						<td>
							<?php if ( 'page' == $post_type ):?>	

								<h4><?php _e('Page Parent', 'wp_all_import_plugin') ?><a href="#help" class="wpallimport-help" title="<?php _e('Enter the ID, title, or slug of the desired page parent. If adding the child and parent pages in the same import, set \'Records per Iteration\' to 1, run the import twice, or run separate imports for child and parent pages.', 'wp_all_import_plugin') ?>" style="position:relative; top:-1px;">?</a></h4>

								<div class="input">
									<input type="radio" id="is_multiple_page_parent_yes" name="is_multiple_page_parent" value="yes" <?php echo 'yes' == $post['is_multiple_page_parent'] ? 'checked="checked"' : '' ?> class="switcher" style="margin-left:0;"/>
									<label for="is_multiple_page_parent_yes"><?php _e('Select page parent', 'wp_all_import_plugin') ?></label>
									<div class="switcher-target-is_multiple_page_parent_yes">
										<div class="input">
										<?php wp_dropdown_pages(array('post_type' => 'page', 'selected' => $post['parent'], 'name' => 'parent', 'show_option_none' => __('(no parent)', 'wp_all_import_plugin'), 'sort_column'=> 'menu_order, post_title', 'number' => 500)); ?>
										</div>
									</div>
								</div>

								<div class="input fleft" style="position:relative;width:220px; margin-top: 5px;">
									<input type="radio" id="is_multiple_page_parent_no" class="switcher" name="is_multiple_page_parent" value="no" <?php echo 'no' == $post['is_multiple_page_parent'] ? 'checked="checked"': '' ?> style="margin-left:0;"/>
									<label for="is_multiple_page_parent_no"><?php _e('Set with XPath', 'wp_all_import_plugin' )?></label> <br>
									<div class="switcher-target-is_multiple_page_parent_no">
										<div class="input">
											&nbsp;<input type="text" class="smaller-text" name="single_page_parent" style="width:190px;" value="<?php echo esc_attr($post['single_page_parent']) ?>"/>										
										</div>
									</div>
								</div>	

							<?php endif;?>

							<?php if ( 'page' != $post_type && $custom_type->hierarchical ): ?>

								<h4><?php _e('Post Parent', 'wp_all_import_plugin') ?><a href="#help" class="wpallimport-help" title="<?php _e('Enter the ID, title, or slug of the desired post parent. If adding the child and parent posts in the same import, set \'Records per Iteration\' to 1, run the import twice, or run separate imports for child and parent posts.', 'wp_all_import_plugin') ?>" style="position:relative; top:-1px;">?</a></h4>
								
								<div class="input">
									<input type="radio" id="is_multiple_page_parent_yes" name="is_multiple_page_parent" value="yes" <?php echo 'yes' == $post['is_multiple_page_parent'] ? 'checked="checked"' : '' ?> class="switcher" style="margin-left:0;"/>
									<label for="is_multiple_page_parent_yes"><?php _e('Set post parent', 'wp_all_import_plugin') ?></label>
									<div class="switcher-target-is_multiple_page_parent_yes">
										<div class="input">
											<input type="text" class="" name="parent" value="<?php echo esc_attr($post['parent']) ?>" />									
										</div>
									</div>
								</div>

								<div class="input fleft" style="position:relative;width:220px; margin-top: 5px;">
									<input type="radio" id="is_multiple_page_parent_no" class="switcher" name="is_multiple_page_parent" value="no" <?php echo 'no' == $post['is_multiple_page_parent'] ? 'checked="checked"': '' ?> style="margin-left:0;"/>
									<label for="is_multiple_page_parent_no"><?php _e('Set with XPath', 'wp_all_import_plugin' )?></label> <br>
									<div class="switcher-target-is_multiple_page_parent_no">
										<div class="input">
											&nbsp;<input type="text" class="smaller-text" name="single_page_parent" style="width:190px;" value="<?php echo esc_attr($post['single_page_parent']) ?>"/>										
										</div>
									</div>
								</div>	

							<?php endif; ?>
														
						</td>
					</tr>					
					<tr>
						<td>
							<h4><?php _e('Menu Order', 'wp_all_import_plugin') ?></h4>
							<div class="input">
								<input type="text" class="" name="order" value="<?php echo esc_attr($post['order']) ?>" />
							</div>
						</td>
					</tr>					
					<?php if ( ! empty($post['deligate']) and $post['deligate'] == 'wpallexport' ): ?>
					<tr>
						<td>
							<h4><?php _e('Dynamic Post Type', 'wp_all_import_plugin') ?></h4>
							<div class="input">
								<div style="margin: 11px; float: left;">
									<input type="hidden" name="is_override_post_type" value="0"/>
									<input type="checkbox" value="1" class="switcher-horizontal fix_checkbox" name="is_override_post_type" id="is_override_post_type" <?php echo ( ! empty($post['is_override_post_type'])) ? 'checked="checked"' : '' ?>>
									<label for="is_override_post_type"><?php _e('Slug','wp_all_import_plugin');?></label>
								</div>
								<div class="switcher-target-is_override_post_type" style="float: left; overflow: hidden;">
									<input type="text" name="post_type_xpath" style="vertical-align:middle; line-height: 26px;" value="<?php echo esc_attr($post['post_type_xpath']) ?>" />											
								</div>	
								<a href="#help" class="wpallimport-help" title="<?php _e('If records in this import have different post types specify the slug of the desired post type here.
', 'wp_all_import_plugin') ?>" style="position:relative; top:12px;">?</a>
							</div>
						</td>
					</tr>			
					<?php endif; ?>									
				</table>
			</div>
		</div>
	</div>
</div>