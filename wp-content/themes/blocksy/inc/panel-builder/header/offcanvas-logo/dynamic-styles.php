<?php

if (! function_exists('blocksy_assemble_selector')) {
	return;
}

$logo_max_height = blocksy_akg('off_canvas_logo_max_height', $atts, 50);

if ($logo_max_height !== 50 || is_customize_preview()) {
	blocksy_output_responsive([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => blocksy_assemble_selector($root_selector),
		'variableName' => 'logo-max-height',
		'value' => $logo_max_height,
	]);
}

blocksy_output_spacing([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => blocksy_assemble_selector($root_selector),
	'important' => true,
	'value' => blocksy_default_akg(
		'off_canvas_logo_margin', $atts,
		blocksy_spacing_value([
			'linked' => true,
		])
	)
]);
