<div id="post-preview" class="wpallimport-preview_images">

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

		<h3><?php _e('Test Images', 'wp_all_import_plugin'); ?></h3>	

		<?php 

		if ( ! empty($featured_images) ){		

			?>
			<p><?php _e('Click to test that your images are able to be accessed by WP All Import.', 'wp_all_import_plugin'); ?></p>

			<a class="test_images" href="javascript:void(0);" style="margin-left:0;" rel="<?php echo $post[$get['slug'] . 'download_images']; ?>"><?php _e('Run Test', 'wp_all_import_plugin'); ?></a>
					
			<?php			

			switch ($post[$get['slug'] . 'download_images']) {
				case 'no':
					$featured_delim = $post[$get['slug'] . 'featured_delim'];
					break;
				case 'gallery':
					$featured_delim = $post[$get['slug'] . 'gallery_featured_delim'];
					break;											
				default: // yes
					$featured_delim = $post[$get['slug'] . 'download_featured_delim'];
					break;
			}

			$imgs = array();

			$line_imgs = explode("\n", $featured_images);
			if ( ! empty($line_imgs) )
				foreach ($line_imgs as $line_img)
					$imgs = array_merge($imgs, ( ! empty($featured_delim) ) ? str_getcsv($line_img, $featured_delim) : array($line_img) );					

			$imgs = array_filter($imgs);
			
			switch ( $post[$get['slug'] . 'download_images'] ) 
			{
				// Use images currently uploaded in wp-content/uploads/wpallimport/files/
				case 'no':
					
					$wp_uploads = wp_upload_dir();

					?>
					<div class="test_progress">
						<div class="img_preloader"><?php _e('Retrieving images...'); ?></div>
						<div class="img_success"></div>
						<div class="img_failed"></div>
					</div>
					<h4><?php _e('WP All Import will import images from the following file paths:', 'wp_all_import_plugin'); ?></h4>
					<p><?php _e('Please ensure the images exists at these file paths', 'wp_all_import_plugin'); ?></p>
					<ul class="images_list">
						<?php foreach ($imgs as $img) : ?>
							
							<li rel="<?php echo trim($img);?>"><?php echo trim(preg_replace('%.*/wp-content%', '/wp-content', $wp_uploads['basedir']) . DIRECTORY_SEPARATOR . PMXI_Plugin::FILES_DIRECTORY . DIRECTORY_SEPARATOR . trim($img)); ?></li>
						
						<?php endforeach; ?> 					
					</ul>
					<h4><?php _e('Here are the above URLs, in &lt;img&gt; tags. '); ?></h4>
					
					<?php 
					foreach ($imgs as $img) { 
						$img_url = site_url() . preg_replace('%.*/wp-content%', '/wp-content', $wp_uploads['basedir']) . DIRECTORY_SEPARATOR . PMXI_Plugin::FILES_DIRECTORY . DIRECTORY_SEPARATOR . trim($img);
						?>
						<img src="<?php echo trim($img_url);?>" style="width:64px; margin:5px; vertical-align:top;"/>
						<?php
					}

					break;

				// 	Use images currently in Media Library
				case 'gallery':

					$wp_uploads = wp_upload_dir();

					?>
					<div class="test_progress">
						<div class="img_preloader"><?php _e('Searching images...'); ?></div>
						<div class="img_success"></div>
						<div class="img_failed"></div>
					</div>
					<h4><?php _e('WP All Import will import images from the media library', 'wp_all_import_plugin'); ?></h4>
					<p><?php _e('Please ensure the images exists at media library', 'wp_all_import_plugin'); ?></p>
					<ul class="images_list">
						<?php foreach ($imgs as $img) : ?>
							
							<?php

							$bn      = wp_all_import_sanitize_filename(basename($img));
							$img_ext = pmxi_getExtensionFromStr($img);									
							$default_extension = pmxi_getExtension($bn);																									

							$image_name = apply_filters("wp_all_import_image_filename", urldecode((($img_ext) ? str_replace("." . $default_extension, "", $bn) : $bn)) . (("" != $img_ext) ? '.' . $img_ext : ''));

							?>

							<li rel="<?php echo $image_name;?>"><?php echo $image_name; ?></li>
						
						<?php endforeach; ?> 					
					</ul>
					<h4><?php _e('Here are the above URLs, in &lt;img&gt; tags. '); ?></h4>
					
					<?php 
					foreach ($imgs as $img) 
					{ 
						$bn      = wp_all_import_sanitize_filename(basename($img));
						$img_ext = pmxi_getExtensionFromStr($img);									
						$default_extension = pmxi_getExtension($bn);																									

						$image_name = apply_filters("wp_all_import_image_filename", urldecode((($img_ext) ? str_replace("." . $default_extension, "", $bn) : $bn)) . (("" != $img_ext) ? '.' . $img_ext : ''));

						$attch   = wp_all_import_get_image_from_gallery($image_name);
						
						$img_url = (empty($attch)) ? '' : trim(wp_get_attachment_url($attch->ID));
						?>
						<img src="<?php echo trim($img_url);?>" style="width:64px; margin:5px; vertical-align:top;"/>
						<?php
					}

					break;
				
				// Download images hosted elsewhere
				default:
					
					?>
						<div class="test_progress">
							<div class="img_preloader"><?php _e('Download in progress...'); ?></div>
							<div class="img_success"></div>
							<div class="img_failed"></div>
						</div>
						<h4><?php _e('WP All Import will attempt to import images from the following URLs:'); ?></h4>
						<p><?php _e('Please check the URLs to ensure they point to valid images'); ?></p>
						<ul class="images_list">
							<?php foreach ($imgs as $img): ?>
								
								<li rel="<?php echo trim($img); ?>"><a href="<?php echo trim($img); ?>" target="_blank"><?php echo trim($img); ?></a></li>
							
							<?php endforeach; ?>					
						</ul>
						<h4><?php _e('Here are the above URLs, in &lt;img&gt; tags. '); ?></h4>
						<?php foreach ($imgs as $img) : ?>
							
							<img src="<?php echo trim($img);?>" style="width:64px; margin:5px; vertical-align:top;"/>
						
						<?php endforeach; ?>
							
					<?php

					break;
			}			
		}
		else
		{
			?>
			<p><?php _e('Images not found for current record.', 'wp_all_import_plugin'); ?></p>
			<?php
		}
		?>
	</div>
</div>