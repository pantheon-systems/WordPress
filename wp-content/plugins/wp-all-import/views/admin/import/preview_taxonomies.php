<div id="post-preview" class="wpallimport-preview_taxonomies">

	<div class="title">
		<div class="navigation">			
			<?php if ($tagno > 1): ?><a href="#prev" class="previous_element">&nbsp;</a><?php else: ?><span class="previous_element">&nbsp;</span><?php endif ?>
			<?php printf(__('<strong><input type="text" value="%s" name="tagno" class="tagno"/></strong><span class="out_of"> of <strong class="pmxi_count">%s</strong></span>', 'wp_all_import_plugin'), $tagno, PMXI_Plugin::$session->count); ?>
			<?php if ($tagno < PMXI_Plugin::$session->count): ?><a href="#next" class="next_element">&nbsp;</a><?php else: ?><span class="next_element">&nbsp;</span><?php endif ?>			
		</div>
	</div>
	
	<div class="wpallimport-preview-content">
		<?php if ($this->errors->get_error_codes()): ?>
			<?php $this->error() ?>
		<?php endif ?>

		<h3><?php _e('Test Taxonomies Hierarchy', 'wp_all_import_plugin'); ?></h3>	

		<?php 	
		if ( ! empty($tax_hierarchical) ): 
			foreach ($tax_hierarchical as $ctx => $terms_arr): 
				$tax_info = get_taxonomy($ctx);			
				?>
				<p><?php echo $tax_info->labels->name; ?></p>
				<?php						
				if (!empty($terms_arr) and is_array($terms_arr)){
					foreach ($terms_arr as $terms) {

						// Apply mapping before splitting via separator symbol
						if (! empty($post['tax_enable_mapping'][$ctx]) and !empty($post['tax_logic_mapping'][$ctx])){
							$mapping_rules = json_decode($post['tax_mapping'][$ctx], true);
							if ( ! empty( $mapping_rules) ){
								foreach ($mapping_rules as $rule) {
									if ( ! empty($rule[trim($terms)])){
										$terms = trim($rule[trim($terms)]);
										break;
									}
								}
							}
						}
										
						$terms_a = ( ! empty($post['tax_hierarchical_delim'][$ctx])) ? explode($post['tax_hierarchical_delim'][$ctx], $terms) : explode(',', $terms);			

						if ( ! empty($terms_a) and is_array($terms_a)){
							foreach ($terms_a as $lvl => $term) {
								if ( ! empty($post['tax_mapping'][$ctx])){						
									$mapping_rules = json_decode($post['tax_mapping'][$ctx], true);
									if ( ! empty($mapping_rules) ){ 
										foreach ($mapping_rules as $rule_number => $rule) {
											if ( ! empty($rule[trim($term)])){ 
												$term = trim($rule[trim($term)]);										
												break;
											}
										}	
									}
								}
								?>
								<p><?php echo str_pad(trim($term), strlen(trim($term)) + $lvl, "-", STR_PAD_LEFT); ?></p>
								<?php
							}
						}
						else{
							?>
							<p><?php echo $terms_a; ?></p>
							<?php
						}
					}
				}

			endforeach; 
		endif; ?>
	</div>

</div>