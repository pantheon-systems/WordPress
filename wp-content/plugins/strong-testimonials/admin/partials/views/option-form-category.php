<?php /* translators: On the Views admin screen. */ ?>
<th>
	<?php _e( 'Assign new submissions to a category', 'strong-testimonials' ); ?>
</th>
<?php if ( $cat_count ) : ?>
    <td>
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
					<?php wpmtst_form_category_checklist( $view_cats_array ); ?>
                </div>
            </div>
        </div>
    </td>
<?php else : ?>
    <td>
        <p class="description tall"><?php _e( 'No categories found', 'strong-testimonials' ); ?></p>
    </td>
<?php endif; ?>
