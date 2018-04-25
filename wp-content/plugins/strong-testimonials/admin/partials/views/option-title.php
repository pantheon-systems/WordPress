<th>
    <input type="checkbox" id="view-title" name="view[data][title]" value="1" <?php checked( $view['title'] ); ?>
           class="checkbox if toggle">
    <label for="view-title">
		<?php _e( 'Title', 'strong-testimonials' ); ?>
    </label>
</th>
<td colspan="2">
    <div class="row">
        <div class="row-inner">
            <div class="then then_title" style="display: none;">
                <input type="checkbox" id="view-title_link" name="view[data][title_link]"
                       value="1" <?php checked( $view['title_link'] ); ?> class="checkbox">
                <label for="view-title_link">
					<?php printf( _x( 'Link to full %s', 'The name of this post type. "Testimonial" by default.', 'strong-testimonials' ), strtolower( apply_filters( 'wpmtst_cpt_singular_name', __( 'Testimonial', 'strong-testimonials' ) ) ) ); ?>
                </label>
            </div>
        </div>
    </div>
</td>
