<?php /* translators: On the Views admin screen. */ ?>
<div class="then then_display then_not_form then_slideshow then_single_template <?php echo apply_filters( 'wpmtst_view_section', '', 'fields' ); ?>" style="display: none;">
	<h3>
		<?php _e( 'Fields', 'strong-testimonials' ); ?>
	</h3>
	<table class="form-table multiple group-show">
		<tr class="then then_display then_not_form then_slideshow then_not_single_template <?php echo apply_filters( 'wpmtst_view_section', '', 'title' ); ?>" style="display: none;">
			<?php include( 'option-title.php' ); ?>
		</tr>
		<tr class="then then_display then_not_form then_slideshow then_not_single_template <?php echo apply_filters( 'wpmtst_view_section', '', 'thumbnail' ); ?>" style="display: none;">
			<?php include( 'option-thumbnail.php' ); ?>
		</tr>
		<tr class="then then_display then_not_form then_slideshow then_not_single_template <?php echo apply_filters( 'wpmtst_view_section', '', 'content' ); ?>" style="display: none;">
			<?php include( 'option-content.php' ); ?>
		</tr>
		<tr class="then then_display then_not_form then_slideshow then_single_template <?php echo apply_filters( 'wpmtst_view_section', '', 'client-section' ); ?>" style="display: none;">
			<?php include( 'option-client-section.php' ); ?>
		</tr>
	</table>
</div>
