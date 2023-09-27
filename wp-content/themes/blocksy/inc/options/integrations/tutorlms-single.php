<?php

$prefix = 'courses_single_';

$design_options = [
	$prefix . 'hero_title_font' => [
		'type' => 'ct-typography',
		'label' => __( 'Title Font', 'blocksy' ),
		'value' => blocksy_typography_default_values([
			'size' => '30px'
		]),
		'design' => 'block',
		'sync' => 'live'
	],

	$prefix . 'hero_title_font_color' => [
		'label' => __( 'Title Font Color', 'blocksy' ),
		'type'  => 'ct-color-picker',
		'design' => 'inline',
		'sync' => 'live',

		'value' => [
			'default' => [
				'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
			],
		],

		'pickers' => [
			[
				'title' => __( 'Initial', 'blocksy' ),
				'id' => 'default',
				'inherit' => 'var(--heading-1-color, var(--headings-color))'
			],
		],
	],

	$prefix . 'hero_categories_font' => [
		'type' => 'ct-typography',
		'label' => __( 'Categories Font', 'blocksy' ),
		'value' => blocksy_typography_default_values([
			'size' => '14px',
			'variation' => 'n5',
			'line-height' => '1.3',
			// 'text-transform' => 'uppercase',
		]),
		'design' => 'block',
		'sync' => 'live',
		'divider' => 'top:full',
	],

	$prefix . 'hero_categories_colors' => [
		'label' => __( 'Categories Font Color', 'blocksy' ),
		'type'  => 'ct-color-picker',
		'design' => 'inline',
		'sync' => 'live',

		'value' => [
			'default' => [
				'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
			],

			'hover' => [
				'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
			],
		],

		'pickers' => [
			[
				'title' => __( 'Initial', 'blocksy' ),
				'id' => 'default',
				'inherit' => 'var(--color)'
			],

			[
				'title' => __( 'Hover', 'blocksy' ),
				'id' => 'hover',
				'inherit' => 'var(--linkHoverColor)'
			],
		],
	],

	$prefix . 'hero_actions_font' => [
		'type' => 'ct-typography',
		'label' => __( 'Course Actions Font', 'blocksy' ),
		'value' => blocksy_typography_default_values([
			'size' => '15px',
			'variation' => 'n4',
			'line-height' => '1.4',
			// 'text-transform' => 'uppercase',
		]),
		'design' => 'block',
		'sync' => 'live',
		'divider' => 'top:full',
	],

	$prefix . 'hero_actions_colors' => [
		'label' => __( 'Course Actions Font Color', 'blocksy' ),
		'type'  => 'ct-color-picker',
		'design' => 'inline',
		'sync' => 'live',

		'value' => [
			'default' => [
				'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
			],

			'hover' => [
				'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
			],
		],

		'pickers' => [
			[
				'title' => __( 'Initial', 'blocksy' ),
				'id' => 'default',
				'inherit' => 'var(--color)'
			],

			[
				'title' => __( 'Hover', 'blocksy' ),
				'id' => 'hover',
				'inherit' => 'var(--linkHoverColor)'
			],
		],
	],

	$prefix . 'hero_title_rating_font' => [
		'type' => 'ct-typography',
		'label' => __( 'Rating Font', 'blocksy' ),
		'value' => blocksy_typography_default_values([
			'size' => '14px',
			'variation' => 'n4',
			// 'text-transform' => 'uppercase',
		]),
		'design' => 'block',
		'sync' => 'live',
		'divider' => 'top:full',
	],

	$prefix . 'hero_title_rating_font_color' => [
		'label' => __( 'Rating Font Color', 'blocksy' ),
		'type'  => 'ct-color-picker',
		'design' => 'inline',
		'sync' => 'live',

		'value' => [
			'default' => [
				'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
			],
		],

		'pickers' => [
			[
				'title' => __( 'Initial', 'blocksy' ),
				'id' => 'default',
				'inherit' => 'var(--color)'
			],
		],
	],

	$prefix . 'hero_title_star_rating_color' => [
		'label' => __( 'Star Rating Color', 'blocksy' ),
		'type'  => 'ct-color-picker',
		'design' => 'inline',
		'divider' => 'top',
		'sync' => 'live',

		'value' => [
			'default' => [
				'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
			],
		],

		'pickers' => [
			[
				'title' => __( 'Initial', 'blocksy' ),
				'id' => 'default',
				'inherit' => '#ED9700'
			],
		],
	],

];

$options = [
	'tutorlms_course_options' => [
		'type' => 'ct-options',
		'inner-options' => [
			blocksy_get_options('general/page-title', [
				'prefix' => 'courses_single',
				'is_single' => true,
				'is_cpt' => true,
				'has_hero_elements' => false,
				'enabled_label' => sprintf(
					__('%s Title', 'blocksy'),
					'Course'
				),
				'design_options' => $design_options
			]),

			blocksy_rand_md5() => [
				'type' => 'ct-title',
				'label' => __( 'Course Structure', 'blocksy' ),
			],

			blocksy_rand_md5() => [
				'title' => __( 'General', 'blocksy' ),
				'type' => 'tab',
				'options' => [
					blocksy_get_options('single-elements/structure', [
						'skipped_structure' => ['type-4', 'type-3'],
						'default_structure' => 'type-1',
						'prefix' => 'courses_single',
					]),
				],
			],

			blocksy_rand_md5() => [
				'title' => __( 'Design', 'blocksy' ),
				'type' => 'tab',
				'options' => [

					blocksy_get_options('single-elements/structure-design', [
						'prefix' => 'courses_single',
					])

				],
			],

		]
	]
];

