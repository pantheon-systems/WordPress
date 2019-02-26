<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

global $wpdb;
$sliders = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'crellyslider_sliders');

if(!$sliders) {
	echo '<div class="cs-no-sliders">';
	_e('No Sliders found. Please add a new one.', 'crelly-slider');
	echo '</div>';
	echo '<br /><br />';
}
else {
	?>

	<table class="cs-sliders-list cs-table">
		<thead>
			<tr>
				<th colspan="5"><?php _e('Sliders List', 'crelly-slider'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr class="cs-table-header">
				<td><?php _e('ID', 'crelly-slider'); ?></td>
				<td><?php _e('Name', 'crelly-slider'); ?></td>
				<td><?php _e('Alias', 'crelly-slider'); ?></td>
				<td><?php _e('Shortcode', 'crelly-slider'); ?></td>
				<td><?php _e('Actions', 'crelly-slider'); ?></td>
			</tr>
			<?php
			foreach($sliders as $slider) {
				echo '<tr>';
				echo '<td class="cs-slider-id">' . esc_html($slider->id) . '</td>';
				echo '<td class="cs-slider-name"><a href="?page=crellyslider&view=edit&id=' . esc_html($slider->id) . '">' . esc_html($slider->name) . '</a></td>';
				echo '<td class="cs-slider-alias">' . esc_html($slider->alias) . '</td>';
				echo '<td class="cs-slider-shortcode">[crellyslider alias="' . esc_html($slider->alias) . '"]</td>';
				echo '<td>
					<a class="cs-edit-slider cs-button cs-button cs-is-success" href="?page=crellyslider&view=edit&id=' . esc_html($slider->id) . '">' . __('Edit Slider', 'crelly-slider') . '</a>
					<a class="cs-duplicate-slider cs-button cs-button cs-is-primary" href="javascript:void(0)" data-duplicate="' . esc_html($slider->id) . '">' . __('Duplicate Slider', 'crelly-slider') . '</a>
					<a class="cs-export-slider cs-button cs-button cs-is-warning" href="javascript:void(0)" data-export="' . esc_html($slider->id) . '">' . __('Export Slider', 'crelly-slider') . '</a>
					<a class="cs-delete-slider cs-button cs-button cs-is-danger" href="javascript:void(0)" data-delete="' . esc_html($slider->id) . '">' . __('Delete Slider', 'crelly-slider') . '</a>
				</td>';
				echo '</tr>';
			}
			?>
		</tbody>
	</table>
	<?php
}
?>

<br />
<a class="cs-button cs-is-primary cs-add-slider" href="?page=crellyslider&view=add"><?php _e('Add Slider', 'crelly-slider'); ?></a>
<a class="cs-button cs-is-warning cs-import-slider" href="javascript:void(0)"><?php _e('Import Slider', 'crelly-slider'); ?></a>
<input id="cs-import-file" type="file" style="display: none;">
