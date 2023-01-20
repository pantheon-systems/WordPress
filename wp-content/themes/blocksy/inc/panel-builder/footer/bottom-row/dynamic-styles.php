<?php

blocksy_get_variables_from_file(
	get_template_directory() . '/inc/panel-builder/footer/middle-row/dynamic-styles.php',
	[],
	[
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'atts' => $atts,
		'root_selector' => $root_selector,
		'primary_item' => $primary_item,

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

	]
);

