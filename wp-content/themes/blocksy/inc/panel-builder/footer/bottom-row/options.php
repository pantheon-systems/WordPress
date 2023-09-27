<?php

$options = blocksy_get_options(
	get_template_directory() . '/inc/panel-builder/footer/middle-row/options.php',
	[
		'default_background' => blocksy_background_default_value([
			'backgroundColor' => [
				'default' => [
					'color' => 'transparent'
				],
			],
		]),

		'default_top_bottom_spacing' => [
			'mobile' => '15px',
			'tablet' => '25px',
			'desktop' => '25px',
		]
	],
	false
);
