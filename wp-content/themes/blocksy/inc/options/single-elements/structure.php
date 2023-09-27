<?php

if (! isset($structure_label)) {
	$structure_label = false;
}

if (! isset($has_v_spacing)) {
	$has_v_spacing = true;
}

if (! isset($has_content_style)) {
	$has_content_style = true;
}

if (! isset($default_structure)) {
	$default_structure = 'type-3';
}

if (! isset($default_content_style)) {
	$default_content_style = 'wide';
}

if (! isset($prefix)) {
	$prefix = '';
} else {
	$prefix = $prefix . '_';
}

$structure_choices = [
	'type-3' => [
		'src' => blocksy_image_picker_url('narrow.svg'),
		'title' => __('Narrow Width', 'blocksy'),
	],

	'type-4' => [
		'src' => blocksy_image_picker_url('normal.svg'),
		'title' => __('Normal Width', 'blocksy'),
	],

	'type-2' => [
		'src' => blocksy_image_picker_url('left-single-sidebar.svg'),
		'title' => __('Left Sidebar', 'blocksy'),
	],

	'type-1' => [
		'src' => blocksy_image_picker_url('right-single-sidebar.svg'),
		'title' => __('Right Sidebar', 'blocksy'),
	]
];

if (! isset($skipped_structure)) {
	$skipped_structure = [];
}

foreach ($skipped_structure as $structure) {
	unset($structure_choices[$structure]);
}

$options = [
	[
		$prefix . 'structure' => [
			'label' => $structure_label,
			'type' => 'ct-image-picker',
			'value' => $default_structure,
			'choices' => $structure_choices,
			'sync' => blocksy_sync_whole_page([
				'prefix' => $prefix,
				'prefix_custom' => 'single-structure',
				'loader_selector' => '[class*="ct-container"]'
			]),
		],

	],

	$has_content_style ? [
		$prefix . 'content_style' => [
			'label' => __('Content Area Style', 'blocksy'),
			'type' => 'ct-radio',
			'value' => $default_content_style,
			'view' => 'text',
			'design' => 'block',
			'divider' => 'top',
			'responsive' => true,
			'choices' => [
				'wide' => __( 'Wide', 'blocksy' ),
				'boxed' => __( 'Boxed', 'blocksy' ),
			],
			'sync' => 'live'
		],
	] : [],

	$has_v_spacing ? [
		$prefix . 'content_area_spacing' => [
			'label' => __( 'Content Area Vertical Spacing', 'blocksy' ),
			'type' => 'ct-radio',
			'value' => 'both',
			'view' => 'text',
			'design' => $has_v_spacing ? 'block' : 'inline',
			'divider' => 'top',
			'attr' => [ 'data-type' => 'content-spacing' ],
			'sync' => "live",
			'choices' => [
				'both'   => '<span></span>
				<i class="ct-tooltip-top">' . __( 'Top & Bottom', 'blocksy' ) . '</i>',

				'top'    => '<span></span>
				<i class="ct-tooltip-top">' . __( 'Only Top', 'blocksy' ) . '</i>',

				'bottom' => '<span></span>
				<i class="ct-tooltip-top">' . __( 'Only Bottom', 'blocksy' ) . '</i>',

				'none'   => '<span></span>
				<i class="ct-tooltip-top">' . __( 'Disabled', 'blocksy' ) . '</i>',
			],
			'desc' => sprintf(
				// translators: placeholder here means the actual URL.
				__( 'You can customize the global spacing value in General ➝ Layout ➝ %sContent Area Spacing%s.', 'blocksy' ),
				sprintf(
					'<a data-trigger-section="general:layout_panel" href="%s">',
					admin_url('/customize.php?autofocus[section]=general&ct_autofocus=general:layout_panel')
				),
				'</a>'
			),
		],
	] : []
];

