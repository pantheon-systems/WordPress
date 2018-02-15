<th>
	<label for="view-class">
		<?php _e( 'CSS Classes', 'strong-testimonials' ); ?>
	</label>
</th>
<td colspan="2">
	<div class="then then_display then_form then_slideshow input" style="display: none;">
		<input type="text" id="view-class" class="long inline" name="view[data][class]" value="<?php echo $view['class']; ?>">
		<p class="inline description tall">
			<?php _e( 'For advanced users.', 'strong-testimonials' ); ?>
			<?php _e( 'Separate class names by spaces.', 'strong-testimonials' ); ?>
			<?php printf( '<a href="%s" target="_blank">%s</a>',
				esc_url( 'https://strongplugins.com/document/strong-testimonials/custom-css/' ),
				__( 'Tutorial', 'strong-testimonials' ) ); ?>
		</p>
	</div>
</td>
