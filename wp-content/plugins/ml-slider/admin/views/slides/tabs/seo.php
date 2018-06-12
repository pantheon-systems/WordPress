<?php if (!defined('ABSPATH')) {
    die('No direct access.');
} ?>
<div class="row can-inherit title<?php echo $inherit_image_title_class; ?>">
	<label><?php _e("Image Title Text", "ml-slider"); ?></label>
	<div class="input-label right">
		<label class="small" title="<?php _e("Enable this to inherit the image title", "ml-slider"); ?>">
			<?php _e("Use the image title", "ml-slider"); ?> <input autocomplete="off" type="checkbox" class="js-inherit-from-image" name="attachment[<?php echo $slide_id; ?>][inherit_image_title]" <?php echo $inherit_image_title_check; ?>>
		</label>
	</div>
	<div class="default"><?php echo $image_title ? $image_title : "<span class='no-content'>&nbsp;</span>"; ?></div>
	<input tabindex="0" type="text" size="50" name="attachment[<?php echo $slide_id; ?>][title]" value="<?php echo $title; ?>">
</div>
<div class="row can-inherit alt<?php echo $inherit_image_alt_class; ?>">
	<label><?php _e("Image Alt Text", "ml-slider"); ?></label>
	<div class="input-label right">
		<label class="small" title="<?php _e('Enable this to inherit the image alt text', 'ml-slider'); ?>">
			<?php _e("Use the image alt text", "ml-slider"); ?> <input autocomplete="off" type="checkbox" class="js-inherit-from-image" name="attachment[<?php echo $slide_id; ?>][inherit_image_alt]" <?php echo $inherit_image_alt_check; ?>>
		</label>
	</div>
	<div class="default"><?php echo $image_alt ? $image_alt : "<span class='no-content'>&nbsp;</span>"; ?></div>
	<input tabindex="0" type="text" size="50" name="attachment[<?php echo $slide_id; ?>][alt]" value="<?php echo $alt; ?>">
</div>
