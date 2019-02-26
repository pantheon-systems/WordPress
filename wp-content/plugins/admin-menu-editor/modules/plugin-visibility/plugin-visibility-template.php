<?php
/**
 * @var string $tabUrl Fully qualified URL of the "Plugins" tab.
 */

do_action('admin_menu_editor-display_header');
?>

<div id="ame-plugin-visibility-editor">
		<form method="post" data-bind="submit: saveChanges" class="ame-pv-save-form" action="<?php
		echo esc_attr(add_query_arg(array('noheader' => '1'), $tabUrl));
		?>">

			<?php submit_button('Save Changes', 'primary', 'submit', false); ?>

			<input type="hidden" name="action" value="save_plugin_visibility">
			<?php wp_nonce_field('save_plugin_visibility'); ?>

			<input type="hidden" name="settings" value="" data-bind="value: settingsData">
			<input type="hidden" name="selected_actor" value="" data-bind="value: selectedActor">
		</form>

		<?php require AME_ROOT_DIR . '/modules/actor-selector/actor-selector-template.php'; ?>

		<table class="widefat plugins">
			<thead>
				<tr>
					<th scope="col" class="ame-check-column">
						<!--suppress HtmlFormInputWithoutLabel -->
						<input type="checkbox" data-bind="checked: areAllPluginsChecked">
					</th>
					<th scope="col">Plugin</th>
					<th scope="col">Description</th>
				</tr>
			</thead>

			<tbody data-bind="foreach: plugins">
			<tr
				data-bind="
				css: {
					'active': isActive,
					'inactive': !isActive
				},
				visible: !isBeingEdited()
			">

				<!--
				Alas, we can't use the "check-column" class for this checkbox because WP would apply
				the default "check all boxes" behaviour and override our Knockout bindings.
				-->
				<th scope="row" class="ame-check-column">
					<!--suppress HtmlFormInputWithoutLabel -->
					<input
						type="checkbox"
						data-bind="
						checked: isChecked,
						attr: {
							id: 'ame-plugin-visible-' + $index(),
							'data-plugin-file': fileName
						}">
				</th>

				<td class="plugin-title">
					<label data-bind="attr: { 'for': 'ame-plugin-visible-' + $index() }">
						<strong data-bind="text: name"></strong>
					</label>
					<div class="row-actions">
						<span class="edit">
							<a href="#" title="Edit plugin name and description. This is a cosmetic change - the actual plugin files are not affected."
							   data-bind="click: openInlineEditor.bind($data)">Edit</a>
						</span>
					</div>
				</td>

				<td><p data-bind="text: description"></p></td>
			</tr>
			<tr class="inline-edit-row" data-bind="if: isBeingEdited">
				<td class="colspanchange" colspan="3">
					<fieldset class="ame-pv-inline-edit-left">
						<div class="inline-edit-col">
							<label>
								<span class="title">Name</span>
								<input type="text" data-bind="value: editableName" class="ame-pv-custom-name">
							</label>
						</div>
					</fieldset>
					<fieldset class="ame-pv-inline-edit-right">
						<div class="inline-edit-col">
							<label>
								<span class="title">Description</span>
								<textarea name="plugin-description" cols="30" rows="5"
								          class="ame-pv-custom-description"
								          data-bind="value: editableDescription"></textarea>
							</label>
						</div>
					</fieldset>

					<p class="submit">
						<?php
						submit_button(
							'Cancel',
							'secondary cancel alignleft',
							'pv-cancel',
							false,
							array(
								'data-bind' => 'click: cancelEdit.bind($data)'
							)
						);
						?>

						<a class="alignleft ame-pv-inline-reset" href="#"
						   title="Reset name and description to default values"
						   data-bind="click: resetNameAndDescription.bind($data)">Reset to default</a>

						<?php
						submit_button(
							'Update',
							'primary save alignright',
							'pv-update',
							false,
							array(
								'data-bind' => 'click: confirmEdit.bind($data)'
							)
						);
						?>
						<br class="clear">
					</p>
				</td>
			</tr>
			</tbody>

			<tfoot>
				<tr>
					<th scope="col" class="ame-check-column">
						<!--suppress HtmlFormInputWithoutLabel -->
						<input type="checkbox" data-bind="checked: areAllPluginsChecked">
					</th>
					<th scope="col">Plugin</th>
					<th scope="col">Description</th>
				</tr>
			</tfoot>

		</table>

	</div> <!-- /module container -->

<?php do_action('admin_menu_editor-display_footer'); ?>