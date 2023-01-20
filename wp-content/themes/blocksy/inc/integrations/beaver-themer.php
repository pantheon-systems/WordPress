<?php

add_filter(
	'fl_theme_builder_part_hooks',
	function () {
		return [
			[
				'label' => __('Header', 'blocksy'),
				'hooks' => [
					'blocksy:header:before' => __('Before Header', 'blocksy'),
					'blocksy:header:after'  => __('After Header', 'blocksy'),
				]
			],

			[
				'label' => __('Content', 'blocksy'),
				'hooks' => [
					'blocksy:content:before' => __('Before Content', 'blocksy'),
					'blocksy:content:top' => __('Top Content', 'blocksy'),
					'blocksy:content:bottom' => __('Bottom Content', 'blocksy'),
					'blocksy:content:after' => __('After Content', 'blocksy'),
				]
			],

			[

				'label' => __('Footer', 'blocksy'),
				'hooks' => [
					'blocksy:footer:before' => __('Before Footer', 'blocksy'),
					'blocksy:footer:after' => __('After Footer', 'blocksy'),
				]
			]
		];
	}
);

add_filter('fl_builder_settings_form_defaults', function ($defaults, $form_type) {
	if ('global' === $form_type) {
		$defaults->row_padding = '0';
		$defaults->row_width = '1290';
		$defaults->medium_breakpoint = '1000';
		$defaults->responsive_breakpoint = '690';
	}

	return $defaults;
}, 10, 2);