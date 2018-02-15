<?php /* translators: On the Views admin screen. */ ?>
<th>
	<input type="checkbox" id="view-images" class="checkbox if toggle" name="view[data][thumbnail]" value="1" <?php checked( $view['thumbnail'] ); ?>>
	<label for="view-images">
		<?php _e( 'Featured Image', 'strong-testimonials' ); ?>
	</label>
</th>
<td colspan="2">

	<div class="then then_images" style="display: none;">

		<div class="row">
			<div class="row-inner">
				<div class="inline">
					<label for="view-thumbnail_size">
						Size
					</label>
					<select id="view-thumbnail_size" class="if select" name="view[data][thumbnail_size]">
						<?php foreach ( $image_sizes as $key => $size ) : ?>
							<option<?php if ( 'custom' == $key ) echo ' class="trip"'; ?> value="<?php echo $key; ?>"<?php selected( $key, $view['thumbnail_size'] ); ?>><?php echo $size['label']; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="inline then then_thumbnail_size" style="margin-left: 1em;">
					<label for="thumbnail_width">
						<?php _e( 'width', 'strong-testimonials' ); ?>
					</label>
					<input id="thumbnail_width" class="input-number-px" type="text" name="view[data][thumbnail_width]" value="<?php echo $view['thumbnail_width']; ?>"> px
					<span style="display: inline-block; color: #BBB; margin: 0 1em;">|</span>
                    <label for="thumbnail_height">
						<?php _e( 'height', 'strong-testimonials' ); ?>
					</label>
					<input id="thumbnail_height" class="input-number-px" type="text" name="view[data][thumbnail_height]" value="<?php echo $view['thumbnail_height']; ?>"> px
				</div>
			</div>
		</div>

		<div class="row">
			<div class="row-inner">
				<div class="inline">
					<input type="checkbox" id="view-lightbox" class="if toggle" name="view[data][lightbox]"
						   value="1" <?php checked( $view['lightbox'] ); ?> class="checkbox">
					<label for="view-lightbox">
						<?php _e( 'Open full-size image in a lightbox', 'strong-testimonials' ); ?>
					</label>
				</div>
				<div class="inline then then_lightbox">
					<p class="description"><?php _e( 'Requires a lightbox provided by your theme or another plugin.', 'strong-testimonials' ); ?></p>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="row-inner">
                <div class="inline then then_lightbox input" style="display: none;">
                    <label for="view-lightbox_class">
		                <?php _e( 'CSS class', 'strong-testimonials' ); ?>
                    </label>
                    <input type="text" id="view-lightbox_class" class="medium inline" name="view[data][lightbox_class]" value="<?php echo $view['lightbox_class']; ?>">
                    <p class="inline description tall">
						<?php _e( 'To add a class to the image link.', 'strong-testimonials' ); ?>
                    </p>
                </div>
			</div>
		</div>

		<div class="row">
			<div class="row-inner">
				<div class="inline">
					<label for="view-gravatar">
						<?php _e( 'If no Featured Image', 'strong-testimonials' ); ?>
					</label>
					<select id="view-gravatar" class="if select selectper" name="view[data][gravatar]">
						<option value="no" <?php selected( $view['gravatar'], 'no' ); ?>><?php _e( 'show nothing', 'strong-testimonials' ); ?></option>
						<option value="yes" <?php selected( $view['gravatar'], 'yes' ); ?>><?php _e( 'show Gravatar', 'strong-testimonials' ); ?></option>
						<option value="if" <?php selected( $view['gravatar'], 'if' ); ?>><?php _e( 'show Gravatar only if found', 'strong-testimonials' ); ?></option>
					</select>
				</div>
				<div class="inline">
					<div class="then fast then_not_no then_yes then_if" style="display: none;">
						<p class="description tall">
							<a href="<?php echo admin_url( 'options-discussion.php' ); ?>"><?php _e( 'Gravatar settings', 'strong-testimonials' ); ?></a>
						</p>
					</div>
				</div>
			</div>
		</div>

	</div><!-- .then_images -->

</td>
