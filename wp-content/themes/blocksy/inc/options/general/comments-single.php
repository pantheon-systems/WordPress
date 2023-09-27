<?php

if (! isset($prefix)) {
	$prefix = '';
} else {
	$prefix = $prefix . '_';
}

if (! isset($enabled)) {
	$enabled = 'yes';
}

$options = [
	$prefix . 'has_comments' => [
		'label' => __( 'Comments', 'blocksy' ),
		'type' => 'ct-panel',
		'switch' => true,
		'value' => $enabled,
		'sync' => blocksy_sync_whole_page([
			'prefix' => $prefix,
		]),
		'inner-options' => [

			blocksy_rand_md5() => [
				'title' => __( 'General', 'blocksy' ),
				'type' => 'tab',
				'options' => [

					$prefix . 'has_comments_website' => [
						'label' => __( 'Website Input Field', 'blocksy' ),
						'type' => 'ct-switch',
						'value' => 'yes',
						'sync' => blocksy_sync_whole_page([
							'prefix' => $prefix,
						]),
					],

					$prefix . 'comments_label_position' => [
						'label' => __('Inputs Label Position', 'blocksy'),
						'type' => 'ct-radio',
						'value' => 'inside',
						'view' => 'text',
						'design' => 'block',
						'divider' => 'top',
						'choices' => [
							'inside' => __('Inside', 'blocksy'),
							'outside' => __('Outside', 'blocksy'),
						],

						'sync' => blocksy_sync_whole_page([
							'prefix' => $prefix,
						]),
					],

					$prefix . 'comments_position' => [
						'label' => __('Comment Form Position', 'blocksy'),
						'type' => 'ct-radio',
						'value' => 'below',
						'view' => 'text',
						'design' => 'block',
						'divider' => 'top',
						'choices' => [
							'below' => __('Below List', 'blocksy'),
							'above' => __('Above List', 'blocksy'),
						],

						'sync' => blocksy_sync_whole_page([
							'prefix' => $prefix,
						]),
					],

					blocksy_rand_md5() => [
						'type' => 'ct-divider',
					],

					$prefix . 'comments_containment' => [
						'label' => __('Module Placement', 'blocksy'),
						'type' => 'ct-radio',
						'value' => 'separated',
						'view' => 'text',
						'design' => 'block',
						'desc' => __('Separate or unify the comments module from or with the entry content area.', 'blocksy'),
						'choices' => [
							'separated' => __('Separated', 'blocksy'),
							'contained' => __('Contained', 'blocksy'),
						],

						'sync' => blocksy_sync_whole_page([
							'prefix' => $prefix,
						]),
		            ],

					blocksy_rand_md5() => [
						'type' => 'ct-divider',
					],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [ $prefix . 'comments_containment' => 'separated' ],
						'options' => [

							$prefix . 'comments_structure' => [
								'label' => __( 'Container Structure', 'blocksy' ),
								'type' => 'ct-radio',
								'value' => 'narrow',
								'view' => 'text',
								'design' => 'block',
								'choices' => [
									'narrow' => __( 'Narrow', 'blocksy' ),
									'normal' => __( 'Normal', 'blocksy' ),
								],
								'sync' => 'live'
							],

						],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [
							$prefix . 'comments_containment' => 'separated',
							$prefix . 'comments_structure' => 'narrow'
						],
						'options' => [
							$prefix . 'comments_narrow_width' => [
								'label' => __( 'Container Max Width', 'blocksy' ),
								'type' => 'ct-slider',
								'value' => 750,
								'min' => 500,
								'max' => 800,
								'divider' => 'bottom',
								'sync' => 'live'
							],
						],
					],

				],
			],

			blocksy_rand_md5() => [
				'title' => __( 'Design', 'blocksy' ),
				'type' => 'tab',
				'options' => [

					$prefix . 'comments_font_color' => [
						'label' => __( 'Font Color', 'blocksy' ),
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

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [ $prefix . 'comments_containment' => 'separated' ],
						'options' => [

							$prefix . 'comments_background' => [
								'label' => __( 'Container Background', 'blocksy' ),
								'type' => 'ct-background',
								'design' => 'inline',
								'divider' => 'top',
								'sync' => 'live',
								'value' => blocksy_background_default_value([
									'backgroundColor' => [
										'default' => [
											'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
										],
									],
								])
							],

						],
					],

				],
			],

		],
	],
];
