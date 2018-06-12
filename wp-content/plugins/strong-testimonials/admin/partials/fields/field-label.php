<?php
/**
 * Field Label
 */
?>
<tr class="field-label-row">
    <th><?php _ex( 'Label', 'noun', 'strong-testimonials' ); ?></th>
    <td>
        <input class="field-label" type="text" name="fields[<?php echo $key; ?>][label]" value="<?php echo esc_attr( $field['label'] ); ?>">
        <label>
            <input type="checkbox" name="fields[<?php echo $key; ?>][show_label]" <?php checked( $field['show_label'], true ); ?>>
            <span class="help inline"><?php _e( 'Show this label on the form.', 'strong-testimonials' ); ?></span>
        </label>
    </td>
</tr>
