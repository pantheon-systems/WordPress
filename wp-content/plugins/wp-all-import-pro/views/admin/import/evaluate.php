<div class="updated found_records">
	<?php if ($is_csv):?>
		<h3><?php printf(__('<span class="matches_count">%s</span> <strong>%s</strong> will be imported', 'wp_all_import_plugin'), $node_list_count, _n('row', 'rows', $node_list_count, 'wp_all_import_plugin')); ?></h3>
	<?php else:?>
		<h3><?php printf(__('<span class="matches_count">%s</span> <strong>%s</strong> %s will be imported', 'wp_all_import_plugin'), $node_list_count, PMXI_Plugin::$session->source['root_element'], _n('element', 'elements', $node_list_count, 'wp_all_import_plugin')); ?></h3>
	<?php endif; ?>
	<h4><?php _e('Click an element to select it, or scroll down to add filtering options.', 'wp_all_import_plugin'); ?></h4>
	<?php if (PMXI_Plugin::getInstance()->getOption('highlight_limit') and $elements->length > PMXI_Plugin::getInstance()->getOption('highlight_limit')): ?>
		<p><?php _e('<strong>Note</strong>: Highlighting is turned off since can be very slow on large sets of elements.', 'wp_all_import_plugin') ?></p>
	<?php endif ?>
</div>
<div id="current_xml">	
	<?php if ($is_csv): ?>
		<?php PMXI_Render::render_csv_element($elements->item($elements->length > 1 ? $show_element : 0), false, '//'); ?>
	<?php else:?>
		<?php PMXI_Render::render_xml_element($elements->item($elements->length > 1 ? $show_element : 0), false, '//'); ?>
	<?php endif;?>
</div>
<script type="text/javascript">
(function($){	
	var paths = <?php echo json_encode($paths) ?>;
	var $xml = $('.wpallimport-xml');
	$('.wpallimport-console').fadeIn();
	$xml.html($('#current_xml').html()).css({'visibility':'visible'});
	for (var i = 0; i < paths.length; i++) {
		$xml.find('.xml-element[title="' + paths[i] + '"]').addClass('selected').parents('.xml-element').find('> .xml-content.collapsed').removeClass('collapsed').parent().find('> .xml-expander').html('-');
	}	
})(jQuery);
</script>