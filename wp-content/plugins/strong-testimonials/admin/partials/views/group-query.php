<?php /* translators: On the Views admin screen. */ ?>
<div class="then then_display then_not_form then_slideshow then_not_single_template <?php echo apply_filters( 'wpmtst_view_section', '', 'select' ); ?>" style="display: none;">
	<h3>
		<?php _e( 'Query', 'strong-testimonials' ); ?>
	</h3>
	<table class="form-table multiple group-select">
        <tr class="subheading">
            <td>Option</td>
            <td>Setting</td>
            <td class="divider" colspan="2">or Shortcode Attribute <span class="help-links"><span class="description"><a href="#tab-panel-wpmtst-help-shortcode" class="open-help-tab"><?php _e( 'Help' ); ?></a></span></span></td>
            <td>Example</td>
        </tr>
		<tr class="then then_display then_not_slideshow then_not_form" style="display: none;">
			<?php include( 'option-select.php' ); ?>
		</tr>
		<tr class="then then_slideshow then_not_single then_multiple" style="display: none;">
			<?php include( 'option-category.php' ); ?>
		</tr>
		<tr class="then then_slideshow then_not_single then_multiple" style="display: none;">
			<?php include( 'option-order.php' ); ?>
		</tr>
		<tr class="then then_slideshow then_not_single then_multiple" style="display: none;">
			<?php include( 'option-limit.php' ); ?>
		</tr>
	</table>
</div>
