<?php

if (! function_exists('blocksy_render_sidebar')) {
function blocksy_render_sidebar() {
	if (blocksy_sidebar_position() === 'none') {
		return '';
	}

	$sticky_output = '';

	$type = get_theme_mod('sidebar_type', 'type-1');

	if (get_theme_mod('has_sticky_sidebar', 'no') === 'yes') {
		$sidebar_stick_behavior = get_theme_mod(
			'sidebar_stick_behavior',
			'sidebar'
		);

		if ($sidebar_stick_behavior === 'sidebar') {
			$sticky_output = 'data-sticky="sidebar"';
		} else {
			$sticky_output = 'data-sticky="widgets"';
		}
	}

	$widgets_separated_output = '';

	if (
		$type === 'type-2'
		&&
		get_theme_mod('separated_widgets', 'no') === 'yes'
	) {
		$widgets_separated_output = 'data-widgets="separated"';
	}

	$class_output = '';

	$sidebar_classes = blocksy_visibility_classes(get_theme_mod('sidebar_visibility', [
		'desktop' => true,
		'tablet' => false,
		'mobile' => false,
	]));

	if (! empty(trim($sidebar_classes))) {
		$class_output = 'class="' . $sidebar_classes . '"';
	}

	$sidebar_to_render = blocksy_get_sidebar_to_render();

	if (! is_active_sidebar($sidebar_to_render)) {
		return '<aside></aside>';
	}

	$prefix = blocksy_manager()->screen->get_prefix();

	$deep_link_args = [];

	if (! is_singular()) {
		$deep_link_args['suffix'] = $prefix . '_has_sidebar';
	}

	ob_start();


	?>

	<aside
		<?php echo wp_kses_post($class_output); ?>
		data-type="<?php echo esc_attr($type) ?>"
		id="sidebar"
		<?php echo blocksy_generic_get_deep_link($deep_link_args) ?>
		<?php echo blocksy_schema_org_definitions('sidebar') ?>>

		<?php do_action('blocksy:sidebar:before'); ?>

		<div
			class="ct-sidebar" <?php echo wp_kses_post($sticky_output); ?>
			<?php echo wp_kses_post($widgets_separated_output) ?>>
			<?php do_action('blocksy:sidebar:start'); ?>

			<?php
				$has_last_n_widgets = false;

				if (get_theme_mod('has_sticky_sidebar', 'no') === 'yes') {
					if ($sidebar_stick_behavior === 'last_n_widgets') {
						$has_last_n_widgets = true;
					}
				}

				if ($has_last_n_widgets) {
					add_action(
						'dynamic_sidebar',
						'blocksy_sidebar_render_dynamic_sidebar_hook'
					);
				}

				dynamic_sidebar($sidebar_to_render);

				if ($has_last_n_widgets) {
					echo '</div>';

					remove_action(
						'dynamic_sidebar',
						'blocksy_sidebar_render_dynamic_sidebar_hook'
					);
				}

			?>

			<?php do_action('blocksy:sidebar:end'); ?>
		</div>

		<?php do_action('blocksy:sidebar:after'); ?>
	</aside>

	<?php

	return ob_get_clean();
}
}

if (! function_exists('blocksy_sidebar_render_dynamic_sidebar_hook')) {
	function blocksy_sidebar_render_dynamic_sidebar_hook($widget) {
		$sidebars_widgets = wp_get_sidebars_widgets();
		$widget_id = $widget['id'];

		$reversed_widgets = array_reverse(
			$sidebars_widgets[blocksy_get_sidebar_to_render()]
		);

		$widget_index = array_search($widget_id, $reversed_widgets);

		$sticky_widget_number = intval(get_theme_mod(
			'sticky_widget_number',
			1
		));

		if ($widget_index + 1 === $sticky_widget_number) {
			echo '<div class="ct-sticky-widgets">';
		}
	}
}

