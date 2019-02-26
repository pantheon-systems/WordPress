<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

?>
<fieldset>
	<legend class="screen-reader-text">
		<span><?php echo esc_html( $title ) ?></span>
	</legend>
	<table class="vc_general vc_wp-form-table fixed">
		<thead>
		<tr>
			<th><?php _e( 'Post type', 'js_composer' ) ?></th>
			<th><?php _e( 'Template', 'js_composer' ) ?></th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ( $post_types as $post_type ) : ?>
			<?php $post_type_object = get_post_type_object( $post_type[0] ); ?>
			<tr>
				<td title="<?php echo esc_attr( $post_type[0] ) ?>">
					<?php echo esc_html( $post_type_object ? $post_type_object->labels->name : $post_type[0] ) ?>
				</td>
				<td>
					<select name="<?php echo $field_key ?>[<?php echo $post_type[0] ?>]">
						<option value=""><?php _e( 'None', 'js_composer' ) ?></option>
						<?php foreach ( $templates as $templates_category ) : ?>
							<optgroup label="<?php echo esc_attr( $templates_category['category_name'] ) ?>">
								<?php foreach ( $templates_category['templates'] as $template ) : ?>
									<?php
									$key = $template['type'] . '::' . esc_attr( $template['unique_id'] );
									$selected = isset( $value[ $post_type[0] ] ) && $value[ $post_type[0] ] === $key ? ' selected="true"' : '';
									?>
									<option value="<?php echo esc_attr( $key ) ?>"<?php echo $selected ?>><?php echo esc_html( $template['name'] ) ?></option>
								<?php endforeach; ?>
							</optgroup>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</fieldset>
