<?php
/* translators: On the Views admin screen. */
?>
<!-- Read more > page -->
<th>
	<div class="checkbox">
		<input type="checkbox" id="view-more_page" class="if toggle" name="view[data][more_page]" value="1"
			<?php checked( isset( $view['more_page'] ) && $view['more_page'] );?> class="checkbox">
		<label for="view-more_page">
			<?php _e( '"Read more" link to a page or post', 'strong-testimonials' ); ?>
		</label>
	</div>
</th>
<td>
	<div class="row then then_more_page" style="display: none;">

		<!-- Select page -->
		<div class="row then then_more_page" style="display: none;">
			<div class="row-inner">
				<label>
					<select id="view-page" name="view[data][more_page_id]">
						<option value=""><?php _e( '&mdash; select &mdash;' ); ?></option>
                        <?php
                        do_action( 'wpmtst_readmore_page_list', $view );
                        if ( $custom_list ) {
                        ?>
                        <optgroup label="<?php _e( 'Custom', 'strong-testimonials' ); ?>">
                            <?php
                            foreach ( $custom_list as $page ) {
                                echo $page;
							}
                            ?>
                        </optgroup>
                        <?php
						}
                        ?>
						<optgroup label="<?php _e( 'Pages' ); ?>">
							<?php foreach ( $pages_list as $pages ) : ?>
								<option value="<?php echo $pages->ID; ?>" <?php selected( isset( $view['more_page_id'] ) ? $view['more_page_id'] : 0, $pages->ID ); ?>><?php echo $pages->post_title; ?></option>
							<?php endforeach; ?>
						</optgroup>
						<optgroup label="<?php _e( 'Posts' ); ?>">
							<?php foreach ( $posts_list as $posts ) : ?>
								<option value="<?php echo $posts->ID; ?>" <?php selected( isset( $view['more_page_id'] ) ? $view['more_page_id'] : 0, $posts->ID ); ?>><?php echo $posts->post_title; ?></option>
							<?php endforeach; ?>
						</optgroup>
					</select>
				</label>
				<label for="view-page_id2">
					<?php _ex( 'or enter its ID or slug', 'to select a target page', 'strong-testimonials' ); ?>
				</label>
				<input type="text" id="view-page_id2" name="view[data][more_page_id2]" size="30">
			</div>
		</div>

		<!-- Link text -->
		<div class="row">
			<div class="row-inner">
				<div class="inline">
					<label for="view-more_page_text">
						<?php _e( 'with link text', 'strong-testimonials' ); ?>
					</label>
					<input type="text" id="view-more_page_text" name="view[data][more_page_text]"
						   value="<?php echo $view['more_page_text']; ?>" size="50">
				</div>
			</div>
		</div>

		<!-- location -->
		<div class="row">
			<div class="row-inner">
				<label>
					<select id="view-more_page_hook" name="view[data][more_page_hook]">
						<option value="wpmtst_view_footer" <?php selected( 'wpmtst_view_footer', $view['more_page_hook'] ); ?>>
							<?php _ex( 'after the last testimonial', 'display setting', 'strong-testimonials' ); ?>
						</option>
						<option value="wpmtst_after_testimonial" <?php selected( 'wpmtst_after_testimonial', $view['more_page_hook'] ); ?>>
							<?php _ex( 'in each testimonial', 'display setting', 'strong-testimonials' ); ?>
						</option>
					</select>
				</label>
			</div>
		</div>

	</div>

</td>
