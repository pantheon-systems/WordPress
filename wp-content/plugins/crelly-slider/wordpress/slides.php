<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly  ?>

<div id="cs-slides">
	<div class="cs-slide-tabs cs-tabs cs-tabs-border">
		<ul class="cs-sortable">
			<?php
			if($edit) {
				$j = 0;
				$slides_num = count($slides);
				foreach($slides as $slide) {
					if($j == $slides_num - 1) {
						echo '<li class="ui-state-default active">';
					}
					else {
						echo '<li class="ui-state-default">';
					}
					echo '<a><span class="cs-slide-name-text">' . __('Slide', 'crelly-slider') . ' <span class="cs-slide-index">' . esc_html(($slide->position) + 1) . '</span></span></a>';
					echo '<span title="' . __('Duplicate slide', 'crelly-slider') . '" class="cs-duplicate"></span>';
                    echo '<span title="' . __('Delete slide', 'crelly-slider') . '" class="cs-close"></span>';
					echo '</li>';

					$j++;
				}
			}
			?>
			<li class="ui-state-default ui-state-disabled"><a class="cs-add-new"><?php _e('Add Slide', 'crelly-slider'); ?></a></li>
			<div style="clear: both;"></div>
		</ul>

		<div class="cs-slides-list">
			<?php
				if($edit) {
					foreach($slides as $slide) {
						echo '<div class="cs-slide">';
						crellyslider_printSlide($slider, $slide, $edit);
						echo '</div>';
					}
				}
			?>
		</div>
		<div class="cs-void-slide"><?php crellyslider_printSlide($slider, false, $edit); ?></div>

		<div style="clear: both"></div>
	</div>
</div>

<?php
// Prints a slide. If the ID is not false, prints the values from MYSQL database, else prints a slide with default values. It has to receive the $edit variable because the elements.php file has to see it
function crellyslider_printSlide($slider, $slide, $edit) {
	$void = !$slide ? true : false;

	$animations = array(
		'none' => array(__('None', 'crelly-slider'), false),
		'fade' => array(__('Fade', 'crelly-slider'), true),
		'fadeLeft' => array(__('Fade left', 'crelly-slider'), false),
		'fadeRight' => array(__('Fade right', 'crelly-slider'), false),
		'slideLeft' => array(__('Slide left', 'crelly-slider'), false),
		'slideRight' => array(__('Slide right', 'crelly-slider'), false),
		'slideUp' => array(__('Slide up', 'crelly-slider'), false),
		'slideDown' => array(__('Slide down', 'crelly-slider'), false),
	);
	?>

	<table class="cs-slide-settings-list cs-table">
		<thead>
			<tr class="odd-row">
				<th colspan="3"><?php _e('Slide Options', 'crelly-slider'); ?></th>
			</tr>
		</thead>

		<tbody>
			<tr class="cs-table-header">
				<td><?php _e('Option', 'crelly-slider'); ?></td>
				<td><?php _e('Parameter', 'crelly-slider'); ?></td>
				<td><?php _e('Description', 'crelly-slider'); ?></td>
			</tr>
			<tr>
				<td class="cs-name"><?php _e('Background', 'crelly-slider'); ?></td>
				<td class="cs-content">
					<?php
					if($void): ?>
					<?php _e('Background image:', 'crelly-slider'); ?> &nbsp;
						<form>
							<input type="radio" value="0" name="cs-slide-background_type_image" checked /> <?php _e('None', 'crelly-slider'); ?> &nbsp;
							<input type="radio" value="1" name="cs-slide-background_type_image" /> <input class="cs-slide-background_type_image-upload-button cs-button cs-is-default" type="button" value="<?php _e('Select image', 'crelly-slider'); ?>" />
						</form>

						<br />
						<br />

						<?php _e('Background color:', 'crelly-slider'); ?> &nbsp;
						<form>
							<br />
							<br />
							<input type="radio" value="0" name="cs-slide-background_type_color" checked /> <?php _e('Transparent', 'crelly-slider'); ?> &nbsp;
							<br />
							<input type="radio" value="1" name="cs-slide-background_type_color" /> <input class="cs-slide-background_type_color-picker-input cs-button cs-is-default" type="text" value="rgb(255, 255, 255)" />
							<br />
							<input type="radio" value="2" name="cs-slide-background_type_color" placeholder="<?php _e('Enter value', 'crelly-slider'); ?>" /> <input class="cs-slide-background_type_color-manual" type="text" />
						</form>

						<br />
						<br />

						<?php _e('Background position-x:', 'crelly-slider'); ?> &nbsp;
						<input type="text" value="center" class="cs-slide-background_propriety_position_x" />
						<br />
						<?php _e('Background position-y:', 'crelly-slider'); ?> &nbsp;
						<input type="text" value="center" class="cs-slide-background_propriety_position_y" />

						<br />
						<br />

						<?php _e('Background repeat:', 'crelly-slider'); ?> &nbsp;
						<form>
							<input type="radio" value="1" name="cs-slide-background_repeat" /> <?php _e('Repeat', 'crelly-slider'); ?> &nbsp;
							<input type="radio" value="0" name="cs-slide-background_repeat" checked /> <?php _e('No repeat', 'crelly-slider'); ?>
						</form>

						<br />
						<br />

						<?php _e('Background size:', 'crelly-slider'); ?> &nbsp;
						<input type="text" value="cover" class="cs-slide-background_propriety_size" />
					<?php else: ?>
						<?php _e('Background image:', 'crelly-slider'); ?> &nbsp;
						<form>
							<?php if($slide->background_type_image == 'none' || $slide->background_type_image == 'undefined'): ?>
								<input type="radio" value="0" name="cs-slide-background_type_image" checked /> <?php _e('None', 'crelly-slider'); ?> &nbsp;
								<input type="radio" value="1" name="cs-slide-background_type_image" /> <input class="cs-slide-background_type_image-upload-button cs-button cs-is-default" type="button" value="<?php _e('Select image', 'crelly-slider'); ?>" />
							<?php else: ?>
								<input type="radio" value="0" name="cs-slide-background_type_image" /> <?php _e('None', 'crelly-slider'); ?> &nbsp;
								<input type="radio" value="1" name="cs-slide-background_type_image" checked /> <input class="cs-slide-background_type_image-upload-button cs-button cs-is-default" type="button" value="<?php _e('Select image', 'crelly-slider'); ?>" />
							<?php endif; ?>
						</form>

						<br />
						<br />

						<?php _e('Background color:', 'crelly-slider'); ?> &nbsp;
						<form>
							<br />
							<br />
							<?php if($slide->background_type_color == 'transparent'): ?>
								<input type="radio" value="0" name="cs-slide-background_type_color" checked /> <?php _e('Transparent', 'crelly-slider'); ?> &nbsp;
							<?php else: ?>
								<input type="radio" value="0" name="cs-slide-background_type_color" /> <?php _e('Transparent', 'crelly-slider'); ?> &nbsp;
							<?php endif; ?>

							<br />
							<?php if($slide->background_type_color_input == '1' || ($slide->background_type_color_input == '-1' && $slide->background_type_color != 'transparent')): ?>
								<input type="radio" value="1" name="cs-slide-background_type_color" checked /> <input class="cs-slide-background_type_color-picker-input cs-button cs-is-default" type="text" value="<?php echo sanitize_text_field($slide->background_type_color); ?>" />
							<?php else: ?>
								<input type="radio" value="1" name="cs-slide-background_type_color" /> <input class="cs-slide-background_type_color-picker-input cs-button cs-is-default" type="text" value="rgb(255, 255, 255)" />
							<?php endif; ?>

							<br />
							<?php if($slide->background_type_color_input == '2'): ?>
								<input type="radio" value="2" name="cs-slide-background_type_color" checked /> <input class="cs-slide-background_type_color-manual" type="text" value="<?php echo sanitize_text_field($slide->background_type_color); ?>" />
							<?php else: ?>
								<input type="radio" value="2" name="cs-slide-background_type_color" /> <input class="cs-slide-background_type_color-manual" type="text" placeholder="<?php _e('Enter value', 'crelly-slider'); ?>" />
							<?php endif; ?>
						</form>

						<br />
						<br />

						<?php _e('Background position-x:', 'crelly-slider'); ?> &nbsp;
						<input type="text" value="<?php echo sanitize_text_field($slide->background_propriety_position_x); ?>" class="cs-slide-background_propriety_position_x" />
						<br />
						<?php _e('Background position-y:', 'crelly-slider'); ?> &nbsp;
						<input type="text" value="<?php echo sanitize_text_field($slide->background_propriety_position_y); ?>" class="cs-slide-background_propriety_position_y" />

						<br />
						<br />

						<?php _e('Background repeat:', 'crelly-slider'); ?> &nbsp;
						<form>
							<?php if($slide->background_repeat == 'repeat'): ?>
								<input type="radio" value="1" name="cs-slide-background_repeat" checked /> <?php _e('Repeat', 'crelly-slider'); ?> &nbsp;
								<input type="radio" value="0" name="cs-slide-background_repeat" /> <?php _e('No repeat', 'crelly-slider'); ?>
							<?php else: ?>
								<input type="radio" value="1" name="cs-slide-background_repeat" /> <?php _e('Repeat', 'crelly-slider'); ?> &nbsp;
								<input type="radio" value="0" name="cs-slide-background_repeat" checked /> <?php _e('No repeat', 'crelly-slider'); ?>
							<?php endif; ?>
						</form>

						<br />
						<br />

						<?php _e('Background size:', 'crelly-slider'); ?> &nbsp;
						<input type="text" value="<?php echo sanitize_text_field($slide->background_propriety_size); ?>" class="cs-slide-background_propriety_size" />
					<?php endif; ?>
				</td>
				<td class="cs-description">
					<?php _e('The background of the slide and its proprieties.', 'crelly-slider'); ?>
					<br />
					<br />
					<strong><?php _e('Presets:', 'crelly-slider'); ?></strong>
					<br />
					<ul class="cs-style-list">
						<li><a class="cs-slide-background-image-fullwidth-preset" href="javascript: void(0);"><?php _e('Full width responsive background image', 'crelly-slider'); ?></a></li>
						<li><a class="cs-slide-background-image-pattern-preset" href="javascript: void(0);"><?php _e('Pattern background image', 'crelly-slider'); ?></a></li>
					</ul>
				</td>
			</tr>
			<tr>
				<td class="cs-name"><?php _e('In animation', 'crelly-slider'); ?></td>
				<td class="cs-content">
					<select class="cs-slide-data_in">
						<?php
						foreach($animations as $key => $value) {
							echo '<option value="' . $key . '"';
							if(($void && $value[1]) || (!$void && $slide->data_in == $key)) {
								echo ' selected';
							}
							echo '>' . $value[0] . '</option>';
						}
						?>
					</select>
				</td>
				<td class="cs-description">
					<?php _e('The in animation of the slide.', 'crelly-slider'); ?>
				</td>
			</tr>
			<tr>
				<td class="cs-name"><?php _e('Out animation', 'crelly-slider'); ?></td>
				<td class="cs-content">
					<select class="cs-slide-data_out">
						<?php
						foreach($animations as $key => $value) {
							echo '<option value="' . $key . '"';
							if(($void && $value[1]) || (!$void && $slide->data_out == $key)) {
								echo ' selected';
							}
							echo '>' . $value[0] . '</option>';
						}
						?>
					</select>
				</td>
				<td class="cs-description">
					<?php _e('The out animation of the slide.', 'crelly-slider'); ?>
				</td>
			</tr>
			<tr>
				<td class="cs-name"><?php _e('Time', 'crelly-slider'); ?></td>
				<td class="cs-content">
					<?php
					if($void) echo '<input class="cs-slide-data_time" type="text" value="3000" />';
					else echo '<input class="cs-slide-data_time" type="text" value="' . sanitize_text_field($slide->data_time) .'" />';
					?>
					ms
				</td>
				<td class="cs-description">
					<?php _e('The time that the slide will remain on the screen.', 'crelly-slider'); ?>
				</td>
			</tr>
			<tr>
				<td class="cs-name"><?php _e('Ease In', 'crelly-slider'); ?></td>
				<td class="cs-content">
					<?php
					if($void) echo '<input class="cs-slide-data_easeIn" type="text" value="300" />';
					else echo '<input class="cs-slide-data_easeIn" type="text" value="' . sanitize_text_field($slide->data_easeIn) .'" />';
					?>
					ms
				</td>
				<td class="cs-description">
					<?php _e('The time that the slide will take to get in.', 'crelly-slider'); ?>
				</td>
			</tr>
			<tr>
				<td class="cs-name"><?php _e('Ease Out', 'crelly-slider'); ?></td>
				<td class="cs-content">
					<?php
					if($void) echo '<input class="cs-slide-data_easeOut" type="text" value="300" />';
					else echo '<input class="cs-slide-data_easeOut" type="text" value="' . sanitize_text_field($slide->data_easeOut) .'" />';
					?>
					ms
				</td>
				<td class="cs-description">
					<?php _e('The time that the slide will take to get out.', 'crelly-slider'); ?>
				</td>
			</tr>
			<tr>
				<td class="cs-name"><?php _e('Link', 'crelly-slider'); ?></td>
				<td class="cs-content">
					<?php
					if($void) echo '<input class="cs-background-link" type="text" value="" />';
					else echo '<input class="cs-background-link" type="text" value="' . stripslashes($slide->link) .'" />';
					?>
					<br />
					<?php
					if($void) echo '<input class="cs-background-link_new_tab" type="checkbox" />' . __('Open link in a new tab', 'crelly-slider');
					else {
						if($slide->link_new_tab) {
							echo '<input class="cs-background-link_new_tab" type="checkbox" checked />' . __('Open link in a new tab', 'crelly-slider');
						}
						else {
							echo '<input class="cs-background-link_new_tab" type="checkbox" />' . __('Open link in a new tab', 'crelly-slider');
						}
					}
					?>
				</td>
				<td class="cs-description">
					<?php _e('Open the link (e.g.: http://www.google.it) when the user clicks on the background. Leave it empty if you don\'t want it.', 'crelly-slider'); ?>
				</td>
			</tr>
			<tr>
				<td class="cs-name"><?php _e('Custom CSS', 'crelly-slider'); ?></td>
				<td class="cs-content">
					<?php
					if($void) echo '<textarea class="cs-slide-custom_css"></textarea>';
					else echo '<textarea class="cs-slide-custom_css">' . stripslashes($slide->custom_css) . '</textarea>';
					?>
				</td>
				<td class="cs-description">
					<?php _e('Apply CSS to the slide.', 'crelly-slider'); ?>
				</td>
			</tr>
			<tr>
				<td class="cs-name"><?php _e('Draft', 'crelly-slider'); ?></td>
				<td class="cs-content">
					<select class="cs-slide-draft">
						<?php
						if(!$void && $slide->draft) {
							echo '<option selected value="1">' . __('Yes', 'crelly-slider') . '</option>';
							echo '<option value="0">' . __('No', 'crelly-slider') . '</option>';
						}
						else {
							echo '<option value="1">' . __('Yes', 'crelly-slider') . '</option>';
							echo '<option selected value="0">' . __('No', 'crelly-slider') . '</option>';
						}
						?>
					</select>
				</td>
				<td class="cs-description">
					<?php _e('If it is set to "Yes", the slide will not be displayed to the users.', 'crelly-slider'); ?>
				</td>
			</tr>
		</tbody>
	</table>

	<br />
	<br />

	<?php
	// If the slide is not void, select its elements
	if(!$void) {
		global $wpdb;

		$id = isset($_GET['id']) ? (int)$_GET['id'] : NULL;
		if($id == NULL || ($id != NULL && !CrellySliderCommon::sliderExists($id))) {
			die();
		}

		$slide_parent = esc_sql($slide->position);
		$elements = $wpdb->get_results($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'crellyslider_elements WHERE slider_parent = %d AND slide_parent = %d', $id, $slide_parent));
	}
	else {
		$slide_id = NULL;
		$elements = NULL;
	}

	crellyslider_printElements($edit, $slider, $slide, $elements);
}
?>
