<div class="table-row form-view-name">
	<?php
	/**
	 * Using htmlspecialchars and stripslashes on $view_name to handle quotes, etc. in database.
	 * @since 2.11.14
	 */
	?>
    <div class="table-cell">
        <label for="view-name">
		    <?php _e( 'Name', 'strong-testimonials' ); ?>
        </label>
    </div>
    <div class="table-cell">
        <input type="text" id="view-name" class="view-name" name="view[name]"
               value="<?php echo htmlspecialchars( stripslashes( $view_name ) ); ?>" tabindex="1">
	</div>
</div>
