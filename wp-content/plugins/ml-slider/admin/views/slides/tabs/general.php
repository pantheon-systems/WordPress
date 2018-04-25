<?php if (!defined('ABSPATH')) die('No direct access.'); ?>
<div class="row can-inherit caption<?php echo $inherit_image_caption_class; ?>">
	<label><?php _e("Caption", "ml-slider"); ?></label>
	<div class="input-label right">
		<label class="small" title="<?php _e('Enable this to inherit the caption from the image', 'ml-slider'); ?>">
			<?php _e("Use the image caption", "ml-slider"); ?> <input autocomplete="off" type="checkbox" class="js-inherit-from-image" name="attachment[<?php echo $slide_id; ?>][inherit_image_caption]" <?php echo $inherit_image_caption_check; ?>>
		</label>
	</div>
	<div class="default"><?php echo $image_caption ? $image_caption : '<span class="no-content">' . __('No default caption set', 'ml-slider') . '</span>'; ?></div>
	<textarea name="attachment[<?php echo $slide_id; ?>][post_excerpt]"><?php echo $caption; ?></textarea>
</div>
<div class="row has-right-checkbox">
	<input class="url" type="text" name="attachment[<?php echo $slide_id; ?>][url]" placeholder="<?php _e("URL", "ml-slider"); ?>" value="<?php echo $url; ?>" />
	<div class="input-label right new_window">
		<label><?php _e("Open in a new window", "ml-slider"); ?> <input autocomplete="off" tabindex="0" type="checkbox" name="attachment[<?php echo $slide_id; ?>][new_window]" <?php echo $target; ?> /></label>
	</div>
</div>
