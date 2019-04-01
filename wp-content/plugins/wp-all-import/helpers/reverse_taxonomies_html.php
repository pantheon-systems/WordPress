<?php

if ( ! function_exists('reverse_taxonomies_html') ) {

	function reverse_taxonomies_html($post_taxonomies, $item_id, &$i, $ctx_name = '', $entry = ''){
		$childs = array();
		foreach ($post_taxonomies as $j => $cat) if ($cat->parent_id == $item_id and $cat->item_id != $item_id) { $childs[] = $cat; }
		
		if (!empty($childs)){
			?>
			<ol>
			<?php
			foreach ($childs as $child_cat){
				$i++;
				?>
	            <li id="item_<?php echo $i; ?>" class="dragging">
	            	<div class="drag-element">	            		
	            		<input type="checkbox" class="assign_term" <?php if (!empty($child_cat->assign)): ?>checked="checked"<?php endif; ?> title="<?php _e('Assign post to the taxonomy.','wp_all_import_plugin');?>"/>
	            		<input class="widefat xpath_field" type="text" value="<?php echo esc_textarea($child_cat->xpath); ?>"/>	            		
	            		<?php do_action('pmxi_category_view', $cat, $i, $ctx_name, $entry); ?>
	            	</div>
	            	<a href="javascript:void(0);" class="icon-item remove-ico"></a>
	            	<?php echo reverse_taxonomies_html($post_taxonomies, $child_cat->item_id, $i, $ctx_name, $entry); ?>
	            </li>
				<?php
			}
			?>
			</ol>
			<?php
		}
	}
}