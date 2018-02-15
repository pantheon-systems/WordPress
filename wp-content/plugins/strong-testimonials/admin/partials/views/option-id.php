<?php /* translators: On the Views admin screen. */ ?>
<div class="then then_not_slideshow then_single then_not_multiple" style="display: none;">

	<div class="row-inner">
		<label>
			<select id="view-id" name="view[data][id]">
				<option value="0"><?php _e( '&mdash; select &mdash;' ); ?></option>
				<?php foreach ( $testimonials_list as $post ) : ?>
					<option value="<?php echo $post->ID; ?>" <?php selected( $view['id'], $post->ID ); ?>>
						<?php echo $post->post_title ? $post->post_title : __( '(untitled)', 'strong-testimonials' ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</label>
    </div>
    <div class="row-inner">
		<label for="view-post_id">
			<?php _ex( 'or enter its ID or slug', 'to select a testimonial', 'strong-testimonials' ); ?>
		</label>
		<input type="text" id="view-post_id" name="view[data][post_id]" size="30">
	</div>

</div>
