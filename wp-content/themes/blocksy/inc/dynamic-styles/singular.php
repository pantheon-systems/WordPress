<?php

do_action(
	'blocksy:global-dynamic-css:enqueue:singular',
	[
		'context' => $context,
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'atts' => $atts,
		'post_id' => $post_id,
		'post_type' => $post_type,
		'prefix' => $prefix
	]
);

$hero_mode = blocksy_akg('has_hero_section', $atts, 'default');

if ($hero_mode === 'enabled') {
	blocksy_theme_get_dynamic_styles([
		'name' => 'page-title/page-title',
		'css' => $css,
		'mobile_css' => $mobile_css,
		'tablet_css' => $tablet_css,
		'context' => $context,
		'chunk' => 'inline',
		'prefix' => $prefix,
		'source' => [
			'strategy' => $atts
		]
	]);
}

$default_content_style = blocksy_default_akg(
	'content_style_source',
	$atts,
	'inherit'
);

blocksy_output_background_css([
	'selector' => blocksy_prefix_selector('', $prefix),
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'value' => blocksy_default_akg(
		'background',
		$atts,
		blocksy_background_default_value([
			'backgroundColor' => [
				'default' => [
					'color' => Blocksy_Css_Injector::get_skip_rule_keyword()
				],
			],
		])
	),
	'responsive' => true,
	'forced_background_image' => true
]);

if ($default_content_style === 'custom') {
	blocksy_theme_get_dynamic_styles([
		'name' => 'global/single-content',
		'css' => $css,
		'mobile_css' => $mobile_css,
		'tablet_css' => $tablet_css,
		'context' => $context,
		'chunk' => 'inline',
		'prefix' => $prefix,
		'source' => [
			'strategy' => $atts
		]
	]);
}

