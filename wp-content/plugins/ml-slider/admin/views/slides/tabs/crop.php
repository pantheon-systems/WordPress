<?php if (!defined('ABSPATH')) die('No direct access.'); ?>
<div class="row">
    <label><?php _e("Crop Position", "ml-slider"); ?></label>
</div>
<div class="row">
    <select class="crop_position" name="attachment[<?php echo $slide_id; ?>][crop_position]">
        <option value="left-top" <?php echo selected($crop_position, 'left-top', false); ?>> <?php _e("Top Left", "ml-slider"); ?></option>
        <option value="center-top" <?php echo selected($crop_position, 'center-top', false); ?>> <?php _e("Top Center", "ml-slider"); ?></option>
        <option value="right-top" <?php echo selected($crop_position, 'right-top', false); ?>> <?php _e("Top Right", "ml-slider"); ?></option>
        <option value="left-center" <?php echo selected($crop_position, 'left-center', false); ?>> <?php _e("Center Left", "ml-slider"); ?></option>
        <option value="center-center" <?php echo selected($crop_position, 'center-center', false); ?>> <?php _e("Center Center", "ml-slider"); ?></option>
        <option value="right-center" <?php echo selected($crop_position, 'right-center', false); ?>> <?php _e("Center Right", "ml-slider"); ?></option>
        <option value="left-bottom" <?php echo selected($crop_position, 'left-bottom', false); ?>> <?php _e("Bottom Left", "ml-slider"); ?></option>
        <option value="center-bottom" <?php echo selected($crop_position, 'center-bottom', false); ?>> <?php _e("Bottom Center", "ml-slider"); ?></option>
        <option value="right-bottom" <?php echo selected($crop_position, 'right-bottom', false); ?>> <?php _e("Bottom Right", "ml-slider"); ?></option>
    </select>
</div>