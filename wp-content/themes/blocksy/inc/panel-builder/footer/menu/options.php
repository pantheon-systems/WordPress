<?php

if (! isset($location)) {
	$location = 'Footer Menu';
}

$options = [
	'menu' => [
		'label' => __('Select Menu', 'blocksy'),
		'type' => 'ct-select',
		'value' => 'blocksy_location',
		'view' => 'text',
		'design' => 'inline',
		'setting' => [ 'transport' => 'postMessage' ],
		'placeholder' => __('Select menu...', 'blocksy'),
		'choices' => blocksy_ordered_keys(blocksy_get_menus_items($location)),
		'desc' => sprintf(
			// translators: placeholder here means the actual URL.
			__( 'Manage your menus in the %sMenus screen%s.', 'blocksy' ),
			sprintf(
				'<a href="%s" target="_blank">',
				admin_url('/nav-menus.php')
			),
			'</a>'
		),
	],

	blocksy_rand_md5() => [
		'type' => 'ct-divider',
	],

	blocksy_rand_md5() => [
		'title' => __( 'General', 'blocksy' ),
		'type' => 'tab',
		'options' => [
			'menu_items_direction' => [
				'type' => 'ct-radio',
				'label' => __( 'Items Direction', 'blocksy' ),
				'view' => 'text',
				'design' => 'block',
				'responsive' => true,
				'setting' => [ 'transport' => 'postMessage' ],
				'value' => 'horizontal',
				'choices' => [
					'horizontal' => __( 'Horizontal', 'blocksy' ),
					'vertical' => __( 'Vertical', 'blocksy' ),
				],
			],

			'footerMenuItemsSpacing' => [
				'label' => __( 'Items Spacing', 'blocksy' ),
				'type' => 'ct-slider',
				'value' => 25,
				'min' => 5,
				'max' => 100,
				'divider' => 'top',
				'responsive' => true,
				'setting' => [ 'transport' => 'postMessage' ],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ 'menu_items_direction:responsive' => 'horizontal' ],
				'options' => [

					'stretch_menu' => [
						'label' => __( 'Stretch Menu', 'blocksy' ),
						'type' => 'ct-switch',
						'value' => 'no',
						'divider' => 'top',
						'desc' => __('Enabling this option will make the menu to stretch and fit the width of its parent column. ', 'blocksy'),
						'setting' => [ 'transport' => 'postMessage' ],
					],

				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			'footerMenuAlignment' => [
				'type' => 'ct-radio',
				'label' => __( 'Horizontal Alignment', 'blocksy' ),
				'view' => 'text',
				'design' => 'block',
				'responsive' => true,
				'attr' => [ 'data-type' => 'alignment' ],
				'setting' => [ 'transport' => 'postMessage' ],
				'value' => 'CT_CSS_SKIP_RULE',
				'choices' => [
					'flex-start' => '',
					'center' => '',
					'flex-end' => '',
				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ 'menu_items_direction' => 'horizontal' ],
				'options' => [

					'footerMenuVerticalAlignment' => [
						'type' => 'ct-radio',
						'label' => __( 'Vertical Alignment', 'blocksy' ),
						'view' => 'text',
						'design' => 'block',
						'divider' => 'top',
						'responsive' => true,
						'attr' => [ 'data-type' => 'vertical-alignment' ],
						'setting' => [ 'transport' => 'postMessage' ],
						'value' => 'CT_CSS_SKIP_RULE',
						'choices' => [
							'flex-start' => '',
							'center' => '',
							'flex-end' => '',
						],
					],

				],
			],

			'footer_menu_visibility' => [
				'label' => __( 'Element Visibility', 'blocksy' ),
				'type' => 'ct-visibility',
				'design' => 'block',
				'divider' => 'top',
				// 'allow_empty' => true,
				'setting' => [ 'transport' => 'postMessage' ],
				'value' => [
					'desktop' => true,
					'tablet' => true,
					'mobile' => true,
				],

				'choices' => blocksy_ordered_keys([
					'desktop' => __( 'Desktop', 'blocksy' ),
					'tablet' => __( 'Tablet', 'blocksy' ),
					'mobile' => __( 'Mobile', 'blocksy' ),
				]),
			],

		],
	],

	blocksy_rand_md5() => [
		'title' => __( 'Design', 'blocksy' ),
		'type' => 'tab',
		'options' => [

			'footerMenuFont' => [
				'type' => 'ct-typography',
				'label' => __( 'Font', 'blocksy' ),
				'value' => blocksy_typography_default_values([
					'size' => '12px',
					'variation' => 'n7',
					'line-height' => '1.3',
					'text-transform' => 'uppercase',
				]),
				'setting' => [ 'transport' => 'postMessage' ],
			],

			'footerMenuFontColor' => [
				'label' => __( 'Font Color', 'blocksy' ),
				'type'  => 'ct-color-picker',
				'design' => 'inline',
				'setting' => [ 'transport' => 'postMessage' ],

				'value' => [
					'default' => [
						'color' => 'var(--color)',
					],

					'hover' => [
						'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
					],

					'active' => [
						'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
					],
				],

				'pickers' => [
					[
						'title' => __( 'Initial', 'blocksy' ),
						'id' => 'default',
					],

					[
						'title' => __( 'Hover', 'blocksy' ),
						'id' => 'hover',
						'inherit' => 'var(--linkHoverColor)',
					],

					[
						'title' => __( 'Active', 'blocksy' ),
						'id' => 'active',
						'inherit' => 'self:hover'
					],
				],
			],

			'footerMenuMargin' => [
				'label' => __( 'Margin', 'blocksy' ),
				'type' => 'ct-spacing',
				'divider' => 'top',
				'setting' => [ 'transport' => 'postMessage' ],
				'value' => blocksy_spacing_value([
					'linked' => true,
				]),
				'responsive' => true
			],

		],
	],
];
