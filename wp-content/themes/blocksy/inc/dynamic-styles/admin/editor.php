<?php

$post_type = get_current_screen()->post_type;

$post_id = null;

if (isset($_GET['post']) && $_GET['post']) {
	$post_id = $_GET['post'];
}

$prefix = blocksy_manager()->screen->get_admin_prefix($post_type);

$post_atts = blocksy_get_post_options($post_id);

$background_source = blocksy_default_akg(
	'background',
	$post_atts,
	blocksy_background_default_value([
		'backgroundColor' => [
			'default' => [
				'color' => Blocksy_Css_Injector::get_skip_rule_keyword()
			],
		],
	])
);

if (
	isset($background_source['background_type'])
	&&
	$background_source['background_type'] === 'color'
	&&
	isset($background_source['backgroundColor']['default']['color'])
	&&
	$background_source['backgroundColor']['default']['color'] === Blocksy_Css_Injector::get_skip_rule_keyword()
) {
	$background_source = get_theme_mod(
		$prefix . '_background',
		blocksy_background_default_value([
			'backgroundColor' => [
				'default' => [
					'color' => Blocksy_Css_Injector::get_skip_rule_keyword()
				],
			],
		])
	);

	if (
		isset($background_source['background_type'])
		&&
		$background_source['background_type'] === 'color'
		&&
		isset($background_source['backgroundColor']['default']['color'])
		&&
		$background_source['backgroundColor']['default']['color'] === Blocksy_Css_Injector::get_skip_rule_keyword()
	) {
		$background_source = get_theme_mod(
			'site_background',
			blocksy_background_default_value([
				'backgroundColor' => [
					'default' => [
						'color' => 'var(--paletteColor7)'
					],
				],
			])
		);
	}
}

blocksy_output_background_css([
	'selector' => '.edit-post-visual-editor__content-area > div',
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'value' => $background_source,
	'responsive' => true,
	'important' => true
]);

$source = [
	'strategy' => $post_atts
];

if (blocksy_default_akg(
	'content_style_source',
	$post_atts,
	'inherit'
) === 'inherit' && $post_type !== 'ct_content_block') {
	$source = [
		'prefix' => $prefix,
		'strategy' => 'customizer'
	];
}

$has_boxed = blocksy_akg_or_customizer(
	'content_style',
	$source,
	blocksy_get_content_style_default($prefix)
);

blocksy_output_responsive([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => "body",
	'variableName' => 'has-boxed',
	'value' => blocksy_map_values([
		'value' => $has_boxed,
		'map' => [
			'boxed' => 'var(--true)',
			'wide' => 'var(--false)'
		]
	]),
	'unit' => ''
]);

blocksy_output_responsive([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => "body",
	'variableName' => 'has-wide',
	'value' => blocksy_map_values([
		'value' => $has_boxed,
		'map' => [
			'wide' => 'var(--true)',
			'boxed' => 'var(--false)'
		]
	]),
	'unit' => ''
]);

$formInputHeight = get_theme_mod( 'formInputHeight', 40 );

if ($formInputHeight !== 40) {
	$css->put( ':root', '--form-field-height: ' . $formInputHeight . 'px' );
}


if (blocksy_some_device($has_boxed, 'boxed')) {
	blocksy_output_background_css([
		'selector' => 'body',
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'value' => blocksy_akg_or_customizer(
			'content_background',
			$source,
			blocksy_background_default_value([
				'backgroundColor' => [
					'default' => [
						'color' => 'var(--paletteColor8)'
					],
				],
			])
		),
		'responsive' => true,
		'conditional_var' => '--has-boxed'
	]);

	blocksy_output_spacing([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => 'body',
		'property' => 'boxed-content-spacing',
		'value' => blocksy_akg_or_customizer(
			'boxed_content_spacing',
			$source,
			[
				'desktop' => blocksy_spacing_value([
					'linked' => true,
					'top' => '40px',
					'left' => '40px',
					'right' => '40px',
					'bottom' => '40px',
				]),
				'tablet' => blocksy_spacing_value([
					'linked' => true,
					'top' => '35px',
					'left' => '35px',
					'right' => '35px',
					'bottom' => '35px',
				]),
				'mobile'=> blocksy_spacing_value([
					'linked' => true,
					'top' => '20px',
					'left' => '20px',
					'right' => '20px',
					'bottom' => '20px',
				]),
			]
		)
	]);

	blocksy_output_spacing([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => 'body',
		'property' => 'border-radius',
		'value' => blocksy_akg_or_customizer(
			'content_boxed_radius',
			$source,
			blocksy_spacing_value([
				'linked' => true,
				'top' => '3px',
				'left' => '3px',
				'right' => '3px',
				'bottom' => '3px',
			])
		)
	]);

	blocksy_output_box_shadow([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => 'body',
		'value' => blocksy_akg_or_customizer(
			'content_boxed_shadow',
			$source,
			blocksy_box_shadow_value([
				'enable' => true,
				'h_offset' => 0,
				'v_offset' => 12,
				'blur' => 18,
				'spread' => -6,
				'inset' => false,
				'color' => [
					'color' => 'rgba(34, 56, 101, 0.04)',
				],
			])
		),
		'responsive' => true
	]);
} else {
	$css->put(
		'body',
		'background-color: transparent'
	);
}

