<?php
/**
 * Field Type
 *
 * If disabled, create <select> with single option and add hidden input with current value.
 */
?>
<tr>
	<th><?php _ex( 'Type', 'noun', 'strong-testimonials' ); ?></th>
	<td>
		<?php
		if ( $adding ) :
		?>
			<select class="first-field field-type new" name="fields[<?php echo $key; ?>][input_type]">

				<?php /* Start with a blank option with event trigger to update optgroups */ ?>
				<option class="no-selection" value="none" name="none">&mdash;</option>

				<?php /* Post fields */ ?>
				<optgroup class="post" label="<?php _e( 'Post Fields', 'strong-testimonials' ); ?>">
				<?php foreach ( $field_types['post'] as $field_key => $field_parts ) : ?>
					<option value="<?php echo $field_key; ?>"><?php echo $field_parts['option_label']; ?></option>
				<?php endforeach; ?>
				</optgroup>

				<?php /* Custom fields */ ?>
				<optgroup class="custom" label="<?php _e( 'Custom Fields', 'strong-testimonials' ); ?>">
				<?php foreach ( $field_types['custom'] as $field_key => $field_parts ) : ?>
					<option value="<?php echo $field_key; ?>"><?php echo $field_parts['option_label']; ?></option>
				<?php endforeach; ?>
				</optgroup>

				<?php /* Special fields */ ?>
				<optgroup class="optional" label="<?php _e( 'Special Fields', 'strong-testimonials' ); ?>">
				<?php foreach ( $field_types['optional'] as $field_key => $field_parts ) : ?>
					<?php $data = ( $field_parts['name'] ) ? ' data-force-name="'.$field_parts['name'].'"' : ''; ?>
					<option value="<?php echo $field_key; ?>"<?php echo $data; ?>><?php echo $field_parts['option_label']; ?></option>
				<?php endforeach; ?>
				</optgroup>

			</select>

		<?php else : ?>

			<?php
			if ( 'post' == $field['record_type'] ) {
				foreach ( $field_types['post'] as $field_key => $field_parts ) {
					// compare field *name*
					if ( $field['name'] == $field_key )
						echo $field_parts['option_label'];
				}
			}
			elseif ( 'custom' == $field['record_type'] ) {
				foreach ( $field_types['custom'] as $field_key => $field_parts ) {
					// compare field *type*
					if ( $field['input_type'] == $field_key ) {
						echo $field_parts['option_label'];
					}
				}
			}
			elseif ( 'optional' == $field['record_type'] ) {
				foreach ( $field_types['optional'] as $field_key => $field_parts ) {
					// compare field *type*
					if ( $field['input_type'] == $field_key ) {
						echo $field_parts['option_label'];
					}
				}
			}

		endif; // editing
		?>
	</td>
</tr>
