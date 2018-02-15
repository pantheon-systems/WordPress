<?php /* translators: On the Views admin screen. */ ?>
<th>
	<?php _e( 'Custom Fields', 'strong-testimonials' ); ?>
</th>
<td colspan="2">

	<div id="client-section-table">

		<div id="custom-field-list2" class="fields">
			<?php
			if ( isset( $view['client_section'] ) ) {
				foreach ( $view['client_section'] as $key => $field ) {
					wpmtst_view_field_inputs( $key, $field );
				}
			}
			?>
		</div>

	</div>

	<div id="add-field-bar">
		<input id="add-field" type="button" class="button-secondary" name="add-field"
			   value="<?php _e( 'Add Field', 'strong-testimonials' ); ?>">
	</div>

</td>
