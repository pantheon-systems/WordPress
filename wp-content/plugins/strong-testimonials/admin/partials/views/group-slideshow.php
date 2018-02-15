<?php /* translators: In the view editor. */ ?>
<div class="then then_not_display then_not_form then_slideshow then_not_single_template <?php echo apply_filters( 'wpmtst_view_section', '', 'slideshow' ); ?>" style="display: none;">
	<h3>
		<?php _e( 'Slideshow', 'strong-testimonials' ); ?>
	</h3>
	<table class="form-table multiple group-select">
		<tr>
			<?php include( 'option-slideshow-transition.php' ); ?>
		</tr>
		<tr>
			<?php include( 'option-slideshow-behavior.php' ); ?>
		</tr>
		<tr>
			<?php include( 'option-slideshow-navigation.php' ); ?>
		</tr>
	</table>
</div>
