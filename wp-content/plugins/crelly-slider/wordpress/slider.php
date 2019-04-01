<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly  ?>

<div id="cs-slider-settings">
	<?php
	// Contains the key, the display name and a boolean: true if is the default option
	$slider_select_options = array(
		'layout' => array(
			'full-width' => array(__('Full Width', 'crelly-slider'), true),
			'fixed' => array(__('Fixed', 'crelly-slider'), false),
		),
		'boolean' => array(
			1 => array(__('Yes', 'crelly-slider'), true),
			0 => array(__('No', 'crelly-slider'), false),
		),
		'boolean_not' => array(
			1 => array(__('Yes', 'crelly-slider'), false),
			0 => array(__('No', 'crelly-slider'), true),
		),
	);
	?>

	<?php if($edit) { ?>
		<input type="text" id="cs-slider-name" placeholder="<?php _e('Slider Name', 'crelly-slider'); ?>" value="<?php echo sanitize_text_field($slider->name); ?>" />
	<?php
	}
	else { ?>
		<input type="text" id="cs-slider-name" placeholder="<?php _e('Slider Name', 'crelly-slider'); ?>" />
	<?php } ?>

	<br />
	<br />

	<strong><?php _e('Alias:', 'crelly-slider'); ?></strong>
	<?php if($edit) { ?>
		<span id="cs-slider-alias"><?php echo esc_html($slider->alias); ?></span>
	<?php
	}
	else { ?>
		<span id="cs-slider-alias"></span>
	<?php } ?>

	<br />
	<br />

	<strong><?php _e('Shortcode:', 'crelly-slider'); ?></strong>
	<?php if($edit) { ?>
		<span id="cs-slider-shortcode">[crellyslider alias="<?php echo esc_html($slider->alias); ?>"]</span>
	<?php
	}
	else { ?>
		<span id="cs-slider-shortcode"></span>
	<?php } ?>

	<br />
	<br />

	<table class="cs-slider-settings-list cs-table">
		<thead>
			<tr class="odd-row">
				<th colspan="3"><?php _e('Slider General Options', 'crelly-slider'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr class="cs-table-header">
				<td><?php _e('Option', 'crelly-slider'); ?></td>
				<td><?php _e('Parameter', 'crelly-slider'); ?></td>
				<td><?php _e('Description', 'crelly-slider'); ?></td>
			</tr>
			<tr>
				<td class="cs-name"><?php _e('Layout', 'crelly-slider'); ?></td>
				<td class="cs-content">
					<select id="cs-slider-layout">
						<?php
						foreach($slider_select_options['layout'] as $key => $value) {
							echo '<option value="' . $key . '"';
							if((!$edit && $value[1]) || ($edit && $slider->layout == $key)) {
								echo ' selected';
							}
							echo '>' . $value[0] . '</option>';
						}
						?>
					</select>
				</td>
				<td class="cs-description">
					<?php _e('Modify the layout type of the slider.', 'crelly-slider'); ?>
				</td>
			</tr>
			<tr>
				<td class="cs-name"><?php _e('Responsive', 'crelly-slider'); ?></td>
				<td class="cs-content">
					<select id="cs-slider-responsive">
						<?php
						foreach($slider_select_options['boolean'] as $key => $value) {
							echo '<option value="' . $key . '"';
							if((!$edit && $value[1]) || ($edit && $slider->responsive == $key)) {
								echo ' selected';
							}
							echo '>' . $value[0] . '</option>';
						}
						?>
					</select>
				</td>
				<td class="cs-description">
					<?php _e('The slider will be adapted to the screen size.', 'crelly-slider'); ?>
				</td>
			</tr>
			<tr>
				<td class="cs-name"><?php _e('Start Width', 'crelly-slider'); ?></td>
				<td class="cs-content">
					<?php
					if(!$edit) echo '<input id="cs-slider-startWidth" type="text" value="1140" />';
					else echo '<input id="cs-slider-startWidth" type="text" value="' . sanitize_text_field($slider->startWidth) .'" />';
					?>
					px
				</td>
				<td class="cs-description">
					<?php _e('The content initial width of the slider.', 'crelly-slider'); ?>
				</td>
			</tr>
			<tr>
				<td class="cs-name"><?php _e('Start Height', 'crelly-slider'); ?></td>
				<td class="cs-content">
					<?php
					if(!$edit) echo '<input id="cs-slider-startHeight" type="text" value="500" />';
					else echo '<input id="cs-slider-startHeight" type="text" value="' . sanitize_text_field($slider->startHeight) .'" />';
					?>
					px
				</td>
				<td class="cs-description">
					<?php _e('The content initial height of the slider.', 'crelly-slider'); ?>
				</td>
			</tr>
			<tr>
				<td class="cs-name"><?php _e('Automatic Slide', 'crelly-slider'); ?></td>
				<td class="cs-content">
					<select id="cs-slider-automaticSlide">
						<?php
						foreach($slider_select_options['boolean'] as $key => $value) {
							echo '<option value="' . $key . '"';
							if((!$edit && $value[1]) || ($edit && $slider->automaticSlide == $key)) {
								echo ' selected';
							}
							echo '>' . $value[0] . '</option>';
						}
						?>
					</select>
				</td>
				<td class="cs-description">
					<?php _e('The slides loop is automatic.', 'crelly-slider'); ?>
				</td>
			</tr>
			<tr>
				<td class="cs-name"><?php _e('Show Controls', 'crelly-slider'); ?></td>
				<td class="cs-content">
					<select id="cs-slider-showControls">
						<?php
						foreach($slider_select_options['boolean'] as $key => $value) {
							echo '<option value="' . $key . '"';
							if((!$edit && $value[1]) || ($edit && $slider->showControls == $key)) {
								echo ' selected';
							}
							echo '>' . $value[0] . '</option>';
						}
						?>
					</select>
				</td>
				<td class="cs-description">
					<?php _e('Show the previous and next arrows.', 'crelly-slider'); ?>
				</td>
			</tr>
			<tr>
				<td class="cs-name"><?php _e('Show Navigation', 'crelly-slider'); ?></td>
				<td class="cs-content">
					<select id="cs-slider-showNavigation">
						<?php
						foreach($slider_select_options['boolean'] as $key => $value) {
							echo '<option value="' . $key . '"';
							if((!$edit && $value[1]) || ($edit && $slider->showNavigation == $key)) {
								echo ' selected';
							}
							echo '>' . $value[0] . '</option>';
						}
						?>
					</select>
				</td>
				<td class="cs-description">
					<?php _e('Show the links buttons to change slide.', 'crelly-slider'); ?>
				</td>
			</tr>
			<tr>
				<td class="cs-name"><?php _e('Enable swipe and drag', 'crelly-slider'); ?></td>
				<td class="cs-content">
					<select id="cs-slider-enableSwipe">
						<?php
						foreach($slider_select_options['boolean'] as $key => $value) {
							echo '<option value="' . $key . '"';
							if((!$edit && $value[1]) || ($edit && $slider->enableSwipe == $key)) {
								echo ' selected';
							}
							echo '>' . $value[0] . '</option>';
						}
						?>
					</select>
				</td>
				<td class="cs-description">
					<?php _e('Enable swipe left, swipe right, drag left, drag right commands.', 'crelly-slider'); ?>
				</td>
			</tr>
			<tr>
				<td class="cs-name"><?php _e('Show Progress Bar', 'crelly-slider'); ?></td>
				<td class="cs-content">
					<select id="cs-slider-showProgressBar">
						<?php
						foreach($slider_select_options['boolean'] as $key => $value) {
							echo '<option value="' . $key . '"';
							if((!$edit && $value[1]) || ($edit && $slider->showProgressBar == $key)) {
								echo ' selected';
							}
							echo '>' . $value[0] . '</option>';
						}
						?>
					</select>
				</td>
				<td class="cs-description">
					<?php _e('Draw the progress bar during the slide execution.', 'crelly-slider'); ?>
				</td>
			</tr>
			<tr>
				<td class="cs-name"><?php _e('Pause on Hover', 'crelly-slider'); ?></td>
				<td class="cs-content">
					<select id="cs-slider-pauseOnHover">
						<?php
						foreach($slider_select_options['boolean'] as $key => $value) {
							echo '<option value="' . $key . '"';
							if((!$edit && $value[1]) || ($edit && $slider->pauseOnHover == $key)) {
								echo ' selected';
							}
							echo '>' . $value[0] . '</option>';
						}
						?>
					</select>
				</td>
				<td class="cs-description">
					<?php _e('Pause the current slide when hovered.', 'crelly-slider'); ?>
				</td>
			</tr>
			<tr>
				<td class="cs-name"><?php _e('Random order', 'crelly-slider'); ?></td>
				<td class="cs-content">
					<select id="cs-slider-randomOrder">
						<?php
						foreach($slider_select_options['boolean_not'] as $key => $value) {
							echo '<option value="' . $key . '"';
							if((!$edit && $value[1]) || ($edit && $slider->randomOrder == $key)) {
								echo ' selected';
							}
							echo '>' . $value[0] . '</option>';
						}
						?>
					</select>
				</td>
				<td class="cs-description">
					<?php _e('The order of the slides is random (instead of being linear).', 'crelly-slider'); ?>
				</td>
			</tr>
			<tr>
				<td class="cs-name"><?php _e('Start from slide', 'crelly-slider'); ?></td>
				<td class="cs-content">
					<select id="cs-slider-startFromSlide">
						<?php
						if(! $edit) {
							echo '<option selected value="-1">' . __('Random slide', 'crelly-slider') . '</option>';
							echo '<option selected value="0">' . __('Slide', 'crelly-slider') . ' 1</option>';
						}
						else {
							if($edit && $slider->startFromSlide == -1) {
								echo '<option selected value="-1">' . __('Random slide', 'crelly-slider') . '</option>';
							}
							else {
								echo '<option value="-1">' . __('Random slide', 'crelly-slider') . '</option>';
							}
							for($i = 0; $i < count($slides); $i++) {
								echo '<option value="' . $i . '"';
								if((!$edit && $i == 0) || ($edit && $slider->startFromSlide == $i)) {
									echo ' selected';
								}
								echo '>' . __('Slide', 'crelly-slider') . ' ' . ($i + 1) . '</option>';
							}
						}
						?>
					</select>
				</td>
				<td class="cs-description">
					<?php _e('The slide that will be displayed first.', 'crelly-slider'); ?>
				</td>
			</tr>
			<tr>
			<td class="cs-name"><?php _e('Display slider from/to', 'crelly-slider'); ?></td>
				<td class="cs-content">					
					<?php
					$minDate = '1000-01-01 00:00:00';
					$maxDate = '9999-12-31 23:59:59';

					if(!$edit || ($edit && $slider->fromDate == $minDate)) echo '<input checked id="cs-slider-displayImmediately" type="checkbox">' . __('Display from now', 'crelly-slider');					
					else echo '<input id="cs-slider-displayImmediately" type="checkbox">' . __('Display from now', 'crelly-slider');					

					if(!$edit || ($edit && $slider->fromDate == $minDate)) echo '<div style="display: none;" id="cs-slider-displayFromWrapper">';
					else echo '<div id="cs-slider-displayFromWrapper">';
					
					echo '<br />';
					_e('Display from', 'crelly-slider');
					echo ':<br />';
					if(!$edit || ($edit && $slider->fromDate == $minDate)) echo '<input class="cs-slider-datepicker" id="cs-slider-fromDate" type="text" />';
					else echo '<input class="cs-slider-datepicker" id="cs-slider-fromDate" type="text" value="' . sanitize_text_field($slider->fromDate) .'" />';		

					echo '</div>';
					?>

					<br />
					<br />

					<?php
					if(!$edit || ($edit && $slider->toDate == $maxDate)) echo '<input checked id="cs-slider-displayForever" type="checkbox">' . __('Display forever', 'crelly-slider');					
					else echo '<input id="cs-slider-displayForever" type="checkbox">' . __('Display forever', 'crelly-slider');					

					if(!$edit || ($edit && $slider->toDate == $maxDate)) echo '<div style="display: none;" id="cs-slider-displayToWrapper">';
					else echo '<div id="cs-slider-displayToWrapper">';
					
					echo '<br />';
					_e('Display to', 'crelly-slider');
					echo ':<br />';
					if(!$edit || ($edit && $slider->toDate == $maxDate)) echo '<input class="cs-slider-datepicker" id="cs-slider-toDate" type="text" />';
					else echo '<input class="cs-slider-datepicker" id="cs-slider-toDate" type="text" value="' . sanitize_text_field($slider->toDate) .'" />';		

					echo '</div>';
					?>
				</td>
				<td class="cs-description">
					<?php _e('Need a slider during Black Friday days? This is the option you need!', 'crelly-slider'); ?>
				</td>
			</tr>
			<tr>
				<td class="cs-name"><?php _e('Callbacks', 'crelly-slider'); ?></td>
				<td class="cs-content">
					<?php
					if(!$edit || ($edit && stripslashes($slider->callbacks) == '')) {
					// Sorry for this ugly indentation, ajax compatibility problems...
					?>
<textarea id="cs-slider-callbacks">
beforeStart : function() {},
beforeSetResponsive : function() {},
beforeSlideStart : function() {},
beforePause	: function() {},
beforeResume : function() {},</textarea>
					<?php
					}
					else echo '<textarea id="cs-slider-callbacks">' . stripslashes($slider->callbacks) . '</textarea>';
					?>
				</td>
				<td class="cs-description">
					<?php _e('Some jQuery functions that you can fire during the slider execution.', 'crelly-slider'); ?>
				</td>
			</tr>
		</tbody>
	</table>
</div>
