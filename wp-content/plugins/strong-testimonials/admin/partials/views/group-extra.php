<?php /* translators: On the Views admin screen. */ ?>
<div class="then then_display then_not_form then_slideshow then_not_single_template <?php echo apply_filters( 'wpmtst_view_section', '', 'extra' ); ?>" style="display: none;">
	<h3>
		<?php _e( 'Extra', 'strong-testimonials' ); ?>
	</h3>
	<table class="form-table multiple group-layout">
		<tr class="then then_display then_not_form then_not_slideshow then_not_single then_multiple" style="display: none;">
			<?php include( 'option-pagination.php' ); ?>
		</tr>
		<tr class="then then_display then_not_form then_slideshow read-more" style="display: none;">
			<?php include( 'option-read-more-page.php' ); ?>
		</tr>
	</table>
</div>
