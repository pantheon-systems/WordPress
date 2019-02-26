<div id="post-preview" class="wpallimport-preview">

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
			
		<?php if (isset($title)): ?>
			<h2 class="title"><?php echo $title; ?></h2>
		<?php endif ?>
		<?php if (isset($content)): ?>
			<?php echo apply_filters('the_content', $content) ?>
		<?php endif ?>

	</div>

</div>