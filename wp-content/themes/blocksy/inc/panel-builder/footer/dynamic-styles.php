<?php

if (! function_exists('blocksy_assemble_selector')) {
	return;
}

// Box shadow
$has_reveal_effect = blocksy_akg('has_reveal_effect', $atts,  [
	'desktop' => false,
	'tablet' => false,
	'mobile' => false,
]);

if (function_exists('blocksy_output_responsive_switch')) {
	blocksy_output_responsive_switch([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => blocksy_assemble_selector(
			blocksy_mutate_selector([
				'selector' => blocksy_mutate_selector([
					'selector' => $root_selector,
					'operation' => 'suffix',
					'to_add' => 'footer.ct-footer'
				]),
				'operation' => 'container-suffix',
				'to_add' => '[data-footer*="reveal"]'
			])
		),
		'variable' => 'position',
		'on' => 'sticky',
		'off' => 'static',
		'value' => $has_reveal_effect,
		'skip_when' => 'all_disabled'
	]);
}

if (
	(
		function_exists('blocksy_some_device')
		&&
		blocksy_some_device($has_reveal_effect)
	)
	||
	is_customize_preview()
) {
	blocksy_output_box_shadow([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => blocksy_assemble_selector(
			blocksy_mutate_selector([
				'selector' => blocksy_mutate_selector([
					'selector' => $root_selector,
					'operation' => 'suffix',
					'to_add' => '.site-main'
				]),
				'operation' => 'container-suffix',
				'to_add' => '[data-footer*="reveal"]'
			])
		),
		'value' => blocksy_akg('footerShadow', $atts, blocksy_box_shadow_value([
			'enable' => true,
			'h_offset' => 0,
			'v_offset' => 30,
			'blur' => 50,
			'spread' => 0,
			'inset' => false,
			'color' => [
				'color' => 'rgba(0, 0, 0, 0.1)',
			],
		])),
		'variableName' => 'footer-box-shadow',
		'responsive' => $has_reveal_effect
	]);
}

blocksy_output_background_css([
	'selector' => blocksy_assemble_selector(
		blocksy_mutate_selector([
			'selector' => $root_selector,
			'operation' => 'suffix',
			'to_add' => 'footer.ct-footer'
		])
	),
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'value' => blocksy_akg(
		'footerBackground',
		$atts,
		blocksy_background_default_value([
			'backgroundColor' => [
				'default' => [
					'color' => 'var(--paletteColor6)'
				],
			],
		])
	),
	'responsive' => true,
]);

