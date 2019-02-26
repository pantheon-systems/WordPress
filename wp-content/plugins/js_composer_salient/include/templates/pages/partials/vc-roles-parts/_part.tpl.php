<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
<?php /** @var $controller Vc_Role_Access_Controller */ ?>
	<tr>
		<th scope="row"><?php echo esc_html( $main_label ) ?></th>
		<td>
			<fieldset>
				<legend class="screen-reader-text">
					<span><?php esc_html( $main_label ) ?></span></legend>
				<select name="<?php echo esc_attr( $params_prefix . '[_state]' ) ?>"
				        data-vc-part="<?php echo esc_attr( $part ) ?>"
				        data-vc-name="<?php echo esc_attr( '_state' ) ?>"
				        <?php if ( ! empty( $capabilities ) ) : ?>data-vc-roles="part-state"
				        data-vc-role-part="<?php echo esc_attr( $part . '-' . $role ) ?>"<?php endif; ?>
				        class="vc_ui-settings-roles-dropdown">
					<?php foreach ( $options as $option ) : ?>
						<option
							value="<?php echo esc_attr( $option[0] ? (string) $option[0] : '0' ) ?>"
							<?php echo isset( $option[2] ) ? ' data-vc-custom-selector="' . esc_attr( $option[2] ) . '"' : '' ?>
							<?php echo $controller->getState() === $option[0] ? ' selected' : '' ?>><?php echo esc_html( $option[1] ) ?></option>
					<?php endforeach; ?>
				</select>
				<?php if ( isset( $description ) && ! empty( $description ) ) : ?>
					<p class="description"><?php echo esc_html( $description ) ?></p>
				<?php endif; ?>
			</fieldset>
		</td>
	</tr>
<?php if ( ! empty( $capabilities ) ) : ?>
	<?php if ( isset( $use_table ) && true === $use_table ) : ?>
		<?php
		require_once vc_path_dir( 'EDITORS_DIR', 'popups/class-vc-add-element-box.php' );
		$add_box = new Vc_Add_Element_Box();
		?>
		<tr data-vc-role-related-part="<?php echo esc_attr( $part . '-' . $role ) ?>"
		    data-vc-role-part-state="<?php echo esc_attr( isset( $custom_value ) ? $custom_value : '*' ) ?>"
		    class="vc_role-custom-settings<?php echo ! isset( $custom_value ) || (string) $controller->getState() === (string) $custom_value ? ' vc_visible' : '' ?>">
			<th scope="row"></th>
			<td>
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php echo esc_html( $custom_label ) ?></span>
					</legend>
					<?php if ( isset( $categories ) && ! empty( $categories ) ) : ?>
						<?php vc_include_template( 'editors/partials/add_element_tabs.tpl.php', array(
							'categories' => $categories,
						) ) ?>
					<?php endif; ?>
					<table class="vc_general vc_wp-form-table fixed" data-vc-roles="table">
						<thead>
						<tr>
							<th><?php echo esc_html( $item_header_name ) ?></th>
							<?php foreach ( $cap_types as $type ) : ?>
								<th class="column-date">
									<label>
										<input type="checkbox" name="all"
										       data-vc-related-controls="tfoot [data-vc-roles-select-all-checkbox]"
										       data-vc-roles-select-all-checkbox="<?php echo esc_attr( $type[0] ) ?>"><?php echo esc_html( $type[1] ) ?>
									</label>
								</th>
							<?php endforeach; ?>
						</tr>
						</thead>
						<tfoot<?php echo isset( $global_set ) ? ' style="display: none;"' : '' ?>>
						<tr>
							<th><?php echo esc_html( $item_header_name ) ?></th>
							<?php foreach ( $cap_types as $type ) : ?>
								<th class="column-date">
									<label>
										<input type="checkbox" name="all"
										       data-vc-related-controls="thead [data-vc-roles-select-all-checkbox]"
										       data-vc-roles-select-all-checkbox="<?php echo esc_attr( $type[0] ) ?>"><?php echo esc_html( $type[1] ) ?>
									</label>
								</th>
							<?php endforeach; ?>
						</tr>
						</tfoot>
						<tbody<?php echo isset( $global_set ) ? ' style="display: none;"' : '' ?>>
						<?php foreach ( $capabilities as $cap ) : ?>
							<?php if ( ! isset( $ignore_capabilities ) || ! in_array( $cap['base'], $ignore_capabilities ) ) : ?>
								<?php
								$category_css_classes = '';
								if ( isset( $cap['_category_ids'] ) ) {
									foreach ( $cap['_category_ids'] as $id ) {
										$category_css_classes .= ' js-category-' . $id;
									}
								}
								?>
								<tr data-vc-capability="<?php echo esc_attr( $cap['base'] ) ?>"
								    class="<?php echo esc_attr( trim( $category_css_classes ) ) ?>">
									<td title="<?php echo esc_attr( $cap['base'] ) ?>">
										<?php echo $add_box->renderIcon( $cap ) ?>
										<div>
											<?php echo esc_html( $cap['name'] ) ?>
											<?php echo ! empty( $cap['description'] ) ? '<span class="vc_element-description">' . esc_html( $cap['description'] ) . '</span>' : '' ?>
										</div>
									</td>
									<?php foreach ( $cap_types as $type ) : ?>
										<td>
											<div class="vc_wp-form-checkbox">
												<label>
													<input type="checkbox"
													       name="<?php echo esc_attr( $params_prefix . '[' . $role . '][' . $part . ']' . '[' . $cap['base'] . '_' . $type[0] . ']' ) ?>"
													       data-vc-part="<?php echo esc_attr( $part ) ?>"
													       data-vc-name="<?php echo esc_attr( $cap['base'] . '_' . $type[0] ) ?>"
													       data-vc-roles="table-checkbox"
													       data-vc-cap="<?php echo esc_attr( $type[0] ) ?>"
													       value="1"<?php echo ! isset( $global_set ) && $controller->can( $cap['base'] . '_' . $type[0], false )->get() ? ' checked' : '' ?>>
													<?php echo esc_html( $type[1] ) ?>
												</label>
											</div>
										</td>
									<?php endforeach; ?>
								</tr>
							<?php endif; ?>
						<?php endforeach; ?>
						</tbody>
					</table>
				</fieldset>
			</td>
		</tr>
	<?php else : ?>
		<tr data-vc-role-related-part="<?php echo esc_attr( $part . '-' . $role ) ?>"
		    data-vc-role-part-state="<?php echo esc_attr( isset( $custom_value ) ? $custom_value : '*' ) ?>"
		    class="vc_role-custom-settings<?php echo ! isset( $custom_value ) || $controller->getState() === $custom_value ? ' vc_visible' : '' ?>">
			<th scope="row"></th>
			<td>
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php echo esc_html( $custom_label ) ?></span>
					</legend>
					<div class="vc_wp-form-row">
						<?php foreach ( $capabilities as $cap ) : ?>
							<div class="vc_wp-form-col vc_wp-form-checkbox">
								<label>
									<input type="checkbox"
									       name="<?php echo esc_attr( $params_prefix . '[' . $cap[0] . ']' ) ?>"
									       value="1"
									       class="vc_roles-settings-checkbox"
										<?php
										// hard coded yes :)
										if ( 'administrator' === $role && 'settings' === $part && ( 'vc-roles-tab' === $cap[0] || 'vc-updater-tab' === $cap[0] ) ) {
											echo ' disabled checked';
										} else {
											?>
											data-vc-part="<?php echo esc_attr( $part ) ?>"
											data-vc-name="<?php echo esc_attr( $cap[0] ) ?>"
											data-vc-roles="serialize"
											data-vc-roles="checkbox"<?php echo $controller->can( $cap[0], false )
											                                              ->get() ? ' checked' : '' ?>
											<?php
										}
										?>>
									<?php echo esc_html( $cap[1] ) ?>
								</label>
							</div>
						<?php endforeach; ?>
					</div>
				</fieldset>
			</td>
		</tr>
	<?php endif; ?>
<?php endif; ?>
