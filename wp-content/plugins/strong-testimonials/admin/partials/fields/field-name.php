<?php
/**
 * Field Name
 *
 * Disabled inputs are not posted so store the field name in a hidden input.
 */
?>
<tr class="field-name-row">
	<th><?php _ex( 'Name', 'noun', 'strong-testimonials' ); ?></th>
	<td>
		<?php
		// Field names for certain types are read-only.
		if ( $field['name_mutable'] ) : ?>
			<input type="text" class="field-name"
				   name="fields[<?php echo $key; ?>][name]"
				   value="<?php echo isset( $field['name'] ) ? esc_attr( $field['name'] ) : ''; ?>">
			<span class="help field-name-help"><?php _e( 'Use only lowercase letters, numbers, and underscores.', 'strong-testimonials' ); ?></span>
			<span class="help field-name-help important"><?php _e( 'Cannot be "name" or "date".', 'strong-testimonials' ); ?></span>
		<?php else : ?>
			<input type="text" class="field-name" value="<?php echo $field['name']; ?>" disabled="disabled">
			<input type="hidden" name="fields[<?php echo $key; ?>][name]" value="<?php echo $field['name']; ?>">
		<?php endif ?>
	</td>
</tr>
