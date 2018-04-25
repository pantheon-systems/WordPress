<?php
/**
 * Field text for checkbox and radio.
 */
?>
<tr class="field-label-row">
	<th><?php _ex( 'Text', 'noun', 'strong-testimonials' ); ?></th>
	<td>
		<input type="text" class="field-label"
			   name="fields[<?php echo $key; ?>][text]"
			   value="<?php echo esc_attr( $field['text'] ); ?>"
               placeholder="<?php _e('next to the checkbox', 'strong-testimonials' ); ?>">
	</td>
</tr>
