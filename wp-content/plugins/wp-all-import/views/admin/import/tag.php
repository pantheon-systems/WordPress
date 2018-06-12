<div class="tag">		
	<div>
		<?php if ( ! empty($elements->length) ):?>		
			<div class="title">
				<!--h3><?php _e('Elements', 'wp_all_import_plugin'); ?></h3-->
				<div class="navigation">
					<?php if ($tagno > 1): ?><a href="#prev" class="previous_element">&nbsp;</a><?php else: ?><span class="previous_element">&nbsp;</span><?php endif ?>
					<?php printf(__('<strong><input type="text" value="%s" name="tagno" class="tagno"/></strong><span class="out_of"> of <strong class="pmxi_count">%s</strong></span>', 'wp_all_import_plugin'), $tagno, PMXI_Plugin::$session->count); ?>
					<?php if ($tagno < PMXI_Plugin::$session->count): ?><a href="#next" class="next_element">&nbsp;</a><?php else: ?><span class="next_element">&nbsp;</span><?php endif ?>
				</div>
			</div>
			<div class="clear"></div>
			<div class="wpallimport-xml resetable"> 
				<?php 
				if ( ! empty($elements->length) ):					
					if ( PMXI_Plugin::$session->options['delimiter'] ):
						PMXI_Render::render_csv_element($elements->item(0), true);
					else:
						PMXI_Render::render_xml_element($elements->item($elements->length > 1 ? $tagno : 0), true);
					endif;

				endif;
				?>			
			</div>			
		<?php else: ?>
			<div class="error inline below-h2" style="padding:10px; margin-top:45px;">
				<?php printf(__('History file not found. Probably you are using wrong encoding.', 'wp_all_import_plugin')); ?>			
			</div>
		<?php endif; ?>		
	</div>
</div>