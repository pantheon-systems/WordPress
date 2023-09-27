<?php

if (! isset($selector)) {
	$selector = ':root';
}

$max_site_width = get_theme_mod( 'maxSiteWidth', 1290 );
$css->put(
	':root',
	'--normal-container-max-width: ' . $max_site_width . 'px'
);

$narrowContainerWidth = get_theme_mod( 'narrowContainerWidth', 750 );
$css->put(
	':root',
	'--narrow-container-max-width: ' . $narrowContainerWidth . 'px'
);

$wideOffset = get_theme_mod( 'wideOffset', 130 );
$css->put(
	':root',
	'--wide-offset: ' . $wideOffset . 'px'
);

$contentSpacingMap = [
	'none' => '0',
	'compact' => '0.8em',
	'comfortable' => '1.5em',
	'spacious' => '2em',
];

$contentSpacing = get_theme_mod('contentSpacing', 'comfortable');

$contentSpacingResult = isset(
	$contentSpacingMap[$contentSpacing]
) ? $contentSpacingMap[$contentSpacing] : $contentSpacingMap['comfortable'];

$css->put(':root', '--content-spacing: ' . $contentSpacingResult);

if ($contentSpacing === 'none') {
	$css->put(':root', '--has-content-spacing: 0');
}


blocksy_theme_get_dynamic_styles([
	'name' => 'admin/colors',
	'css' => $css,
	'mobile_css' => $mobile_css,
	'tablet_css' => $tablet_css,
	'context' => $context,
	'chunk' => 'admin',
	'selector' => $selector
]);

if (
	function_exists('get_current_screen')
	&&
	get_current_screen()
	&&
	get_current_screen()->is_block_editor()
) {
	if (get_current_screen()->base === 'post') {
		blocksy_theme_get_dynamic_styles([
			'name' => 'admin/editor',
			'css' => $css,
			'mobile_css' => $mobile_css,
			'tablet_css' => $tablet_css,
			'context' => $context,
			'chunk' => 'admin'
		]);
	}

	blocksy_theme_get_dynamic_styles([
		'name' => 'global/typography',
		'css' => $css,
		'mobile_css' => $mobile_css,
		'tablet_css' => $tablet_css,
		'context' => 'inline',
		'chunk' => 'admin'
	]);

	blocksy_output_responsive([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => ':root',
		'variableName' => 'buttonMinHeight',
		'value' => get_theme_mod('buttonMinHeight', 40)
	]);

	blocksy_output_spacing([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => ':root',
		'property' => 'buttonBorderRadius',
		'value' => get_theme_mod( 'buttonRadius',
			blocksy_spacing_value([
				'linked' => true,
				'top' => '3px',
				'left' => '3px',
				'right' => '3px',
				'bottom' => '3px',
			])
		)
	]);

	blocksy_output_spacing([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => ':root',
		'property' => 'button-padding',
		'value' => get_theme_mod( 'buttonPadding',
			blocksy_spacing_value([
				'linked' => false,
				'top' => '5px',
				'left' => '20px',
				'right' => '20px',
				'bottom' => '5px',
			])
		)
	]);
}

$post_id = null;

if (isset($_GET['post']) && $_GET['post']) {
	$post_id = $_GET['post'];
}

if ($post_id) {
	$post_atts = blocksy_get_post_options($post_id);

	$template_type = get_post_meta($post_id, 'template_type', true);
	$template_subtype = blocksy_akg('template_subtype', $post_atts, 'card');

	if ($template_type === 'archive' && $template_subtype === 'card') {
		$source = [
			'strategy' => $post_atts
		];

		$template_editor_width_source = blocksy_akg_or_customizer(
			'template_editor_width_source',
			$source,
			'small'
		);

		$template_editor_width = blocksy_akg_or_customizer(
			'template_editor_width',
			$source,
			'1290'
		);

		if ($template_editor_width_source === 'small') {
			$template_editor_width = 500;
		}

		if ($template_editor_width_source === 'medium') {
			$template_editor_width = 900;
		}

		$css->put(
			':root',
			'--block-max-width: ' . $template_editor_width . 'px !important'
		);
	}
}
