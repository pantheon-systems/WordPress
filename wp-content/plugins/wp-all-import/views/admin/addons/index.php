<div class="wrap" style="max-width:970px;">

	<h2><?php _e('WP All Import Add-ons', 'wp_all_import_plugin') ?></h2>
		
	<div id="pmxi-add-ons" class="clear">
		
		<div class="pmxi-add-on-group clear">
			<h3><?php _e('Premium Add-ons', 'wp_all_import_plugin'); ?></h3>
			<?php foreach( $premium as $addon ): ?>
			<div class="pmxi-add-on wp-box <?php if( $addon['active'] ): ?>pmxi-add-on-active<?php endif; ?>">
				<a target="_blank" href="<?php echo $addon['url']; ?>">
					<img src="<?php echo $addon['thumbnail']; ?>" />
				</a>
				<div class="inner">
					<h3><a target="_blank" href="<?php echo $addon['url']; ?>"><?php echo $addon['title']; ?></a></h3>
					<p><?php echo $addon['description']; ?></p>
				</div>
				<div class="footer">
					<?php if ( $addon['active'] ): ?>
						<a class="button button-disabled"><span class="pmxi-sprite-tick"></span><?php _e("Installed",'acf'); ?></a>
					<?php elseif ( $addon['free_installed'] ): ?>
						<a class="button button-disabled"><span class="pmxi-sprite-tick"></span><?php _e("Free Version Installed",'acf'); ?></a>
					<?php elseif ($addon['required_plugins']): ?>
						<?php 
						$all_required_plugins_installed = true;
						foreach ($addon['required_plugins'] as $name => $active): 
							if (!$active){
								?>
								<p style="margin:3px 0px;"><?php echo $name . __(' required', 'wp_all_import_plugin'); ?></p>
								<?php
								$all_required_plugins_installed = false;
							}							
						endforeach; 
						if ($all_required_plugins_installed){
							?>
							<a target="_blank" href="<?php echo $addon['url']; ?>" class="button"><?php _e("Download",'acf'); ?></a>
							<?php
						}
						?>
					<?php else: ?>					
						<a target="_blank" href="<?php echo $addon['url']; ?>" class="button"><?php _e("Purchase & Install",'acf'); ?></a>
					<?php endif; ?>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
		
		<div class="pmxi-add-on-group clear">
			<h3><?php _e('Free Add-ons', 'wp_all_import_plugin'); ?></h3>
			<?php foreach( $free as $addon ): ?>
			<div class="pmxi-add-on wp-box <?php if( $addon['active'] ): ?>pmxi-add-on-active<?php endif; ?>">
				<a target="_blank" href="<?php echo $addon['url']; ?>">
					<img src="<?php echo $addon['thumbnail']; ?>" />
				</a>
				<div class="inner">
					<h3><a target="_blank" href="<?php echo $addon['url']; ?>"><?php echo $addon['title']; ?></a></h3>
					<p><?php echo $addon['description']; ?></p>
				</div>
				<div class="footer">
					<?php if( $addon['active'] ): ?>
						<a class="button button-disabled"><span class="pmxi-sprite-tick"></span><?php _e("Installed",'acf'); ?></a>
					<?php elseif ($addon['paid_installed']): ?>
						<a class="button button-disabled"><span class="pmxi-sprite-tick"></span><?php _e("Paid Version Installed",'acf'); ?></a>
					<?php elseif ($addon['required_plugins']): ?>
						<?php 
						$all_required_plugins_installed = true;
						foreach ($addon['required_plugins'] as $name => $active): 
							if (!$active){
								?>
								<p style="margin:3px 0px;"><?php echo $name . __(' required', 'wp_all_import_plugin'); ?></p>
								<?php
								$all_required_plugins_installed = false;
							}							
						endforeach; 
						if ($all_required_plugins_installed){
							?>
							<a target="_blank" href="<?php echo $addon['url']; ?>" class="button"><?php _e("Download",'acf'); ?></a>
							<?php
						}
						?>
					<?php else: ?>
						<a target="_blank" href="<?php echo $addon['url']; ?>" class="button"><?php _e("Download",'acf'); ?></a>
					<?php endif; ?>
				</div>
			</div>
			<?php endforeach; ?>	
		</div>
		
				
	</div>
	
</div>
<script type="text/javascript">
(function($) {
	
	$(window).load(function(){
		
		$('#pmxi-add-ons .pmxi-add-on-group').each(function(){
		
			var $el = $(this),
				h = 0;
			
			
			$el.find('.pmxi-add-on').each(function(){
				
				h = Math.max( $(this).height(), h );
				
			});
			
			$el.find('.pmxi-add-on').height( h );
			
		});
		
	});
	
})(jQuery);	
</script>