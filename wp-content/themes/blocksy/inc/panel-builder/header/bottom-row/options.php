<?php

$options = blocksy_get_options(
	get_template_directory() . '/inc/panel-builder/header/middle-row/options.php',
	[
		'default_height' => [
			'mobile' => 80,
			'tablet' => 80,
			'desktop' => 80,
		],

		'default_background' => blocksy_background_default_value([
			'backgroundColor' => [
				'default' => [
					'color' => 'var(--paletteColor8)',
				],
			],
		])
	],
	false
);


