<?php if (!empty($variation_list_count)):?>
<div class="updated">
	<p><?php printf(__('Current selection matches <span class="matches_count">%s</span> %s.', 'wp_all_import_plugin'), $variation_list_count, _n('element', 'elements', $variation_list_count, 'wp_all_import_plugin')) ?></p>
	<?php if (PMXI_Plugin::getInstance()->getOption('highlight_limit') and $variation_list_count > PMXI_Plugin::getInstance()->getOption('highlight_limit')): ?>
		<p><?php _e('<strong>Note</strong>: Highlighting is turned off since can be very slow on large sets of elements.', 'wp_all_import_plugin') ?></p>
	<?php endif ?>
</div>
<div id="current_xml">
	<div class="variations_tag">	
		<input type="hidden" name="variations_tagno" value="<?php echo $tagno + 1 ?>" />
		<div class="title">			
			<div class="navigation">
				<?php if ($tagno > 0): ?><a href="#variation_prev" class="previous_element">&nbsp;</a><?php else: ?><span class="previous_element">&nbsp;</span><?php endif ?>
				<?php printf(__('#<strong>%s</strong> out of <strong>%s</strong>', 'wp_all_import_plugin'), $tagno + 1, $variation_list_count); ?>
				<?php if ($tagno < $variation_list_count - 1): ?><a href="#variation_next" class="next_element">&nbsp;</a><?php else: ?><span class="next_element">&nbsp;</span><?php endif ?>
			</div>
		</div>
		<div class="clear"></div>
		<div class="xml resetable"> <?php if (!empty($variation_list_count)) PMXI_Render::render_xml_element($variation_elements->item($tagno), true);  ?></div>	
	</div>
</div>
<?php endif; ?>
<script type="text/javascript">
(function($){	
	var paths = <?php echo json_encode($paths) ?>;
	var $xml = $('#variations_xml');	
	
	$xml.html($('#current_xml').html()).css({'visibility':'visible'});
	for (var i = 0; i < paths.length; i++) {
		$xml.find('.xml-element[title="' + paths[i] + '"]').addClass('selected').parents('.xml-element').find('> .xml-content.collapsed').removeClass('collapsed').parent().find('> .xml-expander').html('-');
	}
})(jQuery);
</script>