<?php /* translators: On the Views admin screen. */ ?>
<div class="then then_not_display then_not_slideshow then_form then_not_single_template <?php echo apply_filters( 'wpmtst_view_section', '', 'form' ); ?>" style="display: none;">
	<h3>
		<?php _e( 'Actions', 'strong-testimonials' ); ?>
	</h3>
	<table class="form-table multiple group-select">
		<tr>
			<?php include('option-form-category.php'); ?>
		</tr>
		<tr>
			<?php include('option-form-ajax.php'); ?>
		</tr>
	</table>
</div>
