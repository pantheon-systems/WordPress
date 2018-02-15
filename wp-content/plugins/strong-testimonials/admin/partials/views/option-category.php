<?php /* translators: On the Views admin screen. */ ?>
<th>
    <label for="view-category-select">
		<?php _e( 'Categories', 'strong-testimonials' ); ?>
    </label>
</th>
<?php if ( $cat_count ) : ?>
<td>
    <div id="view-category" class="row">
        <div class="table inline">
                <div class="table-row">
                    <div class="table-cell select-cell then_display then_slideshow then_not_form">
                        <select id="view-category-select" class="if selectper" name="view[data][category_all]">
                            <option value="allcats" <?php selected( $view['category'], 'all' ); ?>><?php _e( 'all', 'strong-testimonials' ); ?></option>
                            <option value="somecats" <?php echo( 'all' != $view['category'] ? 'selected' : '' ); ?>><?php _ex( 'select', 'verb', 'strong-testimonials' ); ?></option>
                        </select>
                    </div>
                    <div class="table-cell then then_not_allcats then_somecats" style="display: none;">
                        <div class="table">
							<?php if ( $cat_count > 5 ) : ?>
                            <div class="table-row">
                                <div class="table-cell">
                                    <div class="row" style="text-align: right; padding-bottom: 5px;">
                                        <input type="button" class="expand-cats button" value="expand list"/>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="table-row">
                                <div class="table-cell">
									<?php wpmtst_category_checklist( $view_cats_array ); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</td>
		<?php else : ?>
<td>
    <div id="view-category" class="row">
            <input type="hidden" name="view[data][category_all]" value="all">
            <p class="description tall"><?php _e( 'No categories found', 'strong-testimonials' ); ?></p>
    </div>
</td>
<?php endif; ?>
<td class="divider">
    <p><?php _e( '<code>category</code>', 'strong-testimonials' ); ?></p>
</td>
<td>
    <p><?php _e( 'a comma-separated list of category slugs or ID\'s', 'strong-testimonials' ); ?></p>
</td>
<td>
    <p><?php _e( '<code>category="accounting"</code>', 'strong-testimonials' ); ?></p>
</td>