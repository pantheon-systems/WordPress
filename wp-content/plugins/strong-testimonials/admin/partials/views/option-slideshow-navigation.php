<?php /* translators: In the view editor. */ ?>
<th>
	<label for="view-slideshow_nav">
		<?php _e( 'Navigation', 'strong-testimonials' ); ?>
	</label>
</th>
<td>

	<?php /* ----- CONTROLS ----- */ ?>
	<div class="row">
		<div class="row-inner">

			<?php /* ----- TYPE ----- */ ?>
			<div class="inline">
				<label for="view-slideshow_controls_type"><?php _e( 'Controls', 'strong-testimonials' ); ?></label>
				<select id="view-slideshow_controls_type" name="view[data][slideshow_settings][controls_type]" class="if selectnot">
				<?php foreach ( $view_options['slideshow_nav_method']['controls'] as $key => $type ) : ?>
					<option value="<?php echo $key; ?>" id="<?php echo $key; ?>"
					<?php selected( $view['slideshow_settings']['controls_type'], $key ); ?>
					<?php if ( 'none' == $key ) echo ' class="trip"'; ?>>
					<?php echo $type['label']; ?>
					</option>
				<?php endforeach; ?>
				</select>
			</div>

			<?php /* ----- STYLE ----- */ ?>
			<div class="inline then then_slideshow_controls_type" style="display: none;">
				<label for="view-slideshow_controls_style"><?php _e( 'Style', 'strong-testimonials' ); ?></label>
				<select id="view-slideshow_controls_style" name="view[data][slideshow_settings][controls_style]">
				<?php foreach ( $view_options['slideshow_nav_style']['controls'] as $key => $style ) : ?>
					<option value="<?php echo $key; ?>" <?php selected( $view['slideshow_settings']['controls_style'], $key ); ?>><?php echo $style['label']; ?></option>
				<?php endforeach; ?>
				</select>
			</div>

		</div>
	</div>

	<?php /* ----- PAGER ----- */ ?>
	<div class="row">
		<div class="row-inner then then_has-pager">

			<?php /* ----- TYPE ----- */ ?>
			<div class="inline">
				<label for="view-slideshow_pager_type"><?php _e( 'Pagination', 'strong-testimonials' ); ?></label>
				<select id="view-slideshow_pager_type" name="view[data][slideshow_settings][pager_type]" class="if selectnot">
					<?php foreach ( $view_options['slideshow_nav_method']['pager'] as $key => $type ) : ?>
						<option value="<?php echo $key; ?>" id="<?php echo $key; ?>"
							<?php selected( $view['slideshow_settings']['pager_type'], $key ); ?>
							<?php if ( 'none' == $key ) echo ' class="trip"'; ?>>
							<?php echo $type['label']; ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>

			<?php /* ----- STYLE ----- */ ?>
			<div class="inline then then_slideshow_pager_type" style="display: none;">
				<label for="view-slideshow_pager_style"><?php _e( 'Style', 'strong-testimonials' ); ?></label>
				<select id="view-slideshow_pager_style" name="view[data][slideshow_settings][pager_style]" class="if selectnot">
				<?php foreach ( $view_options['slideshow_nav_style']['pager'] as $key => $style ) : ?>
				<option value="<?php echo $key; ?>" <?php selected( $view['slideshow_settings']['pager_style'], $key ); ?>><?php echo $style['label']; ?></option>
				<?php endforeach; ?>
				</select>
			</div>

		</div>
	</div>

	<?php /* ----- POSITION ----- */ ?>
	<div class="row">
		<div class="row-inner">

			<div class="inline then then_has-position" style="display: none;">
				<label for="view-slideshow_nav_position"><?php _e( 'Position', 'strong-testimonials' ); ?></label>
				<select id="view-slideshow_nav_position" name="view[data][slideshow_settings][nav_position]">
				<?php foreach ( $view_options['slideshow_nav_position'] as $key => $label ) : ?>
					<option value="<?php echo $key; ?>" <?php selected( $view['slideshow_settings']['nav_position'], $key ); ?>><?php echo $label; ?></option>
				<?php endforeach; ?>
				</select>
				<?php _e( 'the testimonial frame', 'strong-testimonials' ); ?>
			</div>

		</div>
	</div>

</td>
