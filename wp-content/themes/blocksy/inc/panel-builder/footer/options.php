<?php

$options = [
	'has_reveal_effect' => [
		'label' => __( 'Enable reveal effect on', 'blocksy' ),
		'type' => 'ct-visibility',
		'design' => 'block',
		'allow_empty' => true,
		'desc' => __('Enables a nice reveal effect as you scroll down.', 'blocksy'),
		'setting' => ['transport' => 'postMessage'],

		'value' => [
			'desktop' => false,
			'tablet' => false,
			'mobile' => false,
		],

		'choices' => blocksy_ordered_keys([
			'desktop' => __('Desktop', 'blocksy'),
			'tablet' => __('Tablet', 'blocksy'),
			'mobile' => __('Mobile', 'blocksy'),
		]),
	],

	blocksy_rand_md5() => [
		'type' => 'ct-condition',
		'condition' => ['has_reveal_effect:visibility' => 'yes'],
		'options' => [

			'footerShadow' => [
				'label' => __( 'Shadow', 'blocksy' ),
				'type' => 'ct-box-shadow',
				'responsive' => true,
				'divider' => 'top',
				'hide_shadow_placement' => true,
				'value' => blocksy_box_shadow_value([
					'enable' => true,
					'h_offset' => 0,
					'v_offset' => 30,
					'blur' => 50,
					'spread' => 0,
					'inset' => false,
					'color' => [
						'color' => 'rgba(0, 0, 0, 0.1)',
					],
				])
			],

		],
	],

	blocksy_rand_md5() => [
		'type' => 'ct-divider',
	],

	'footerBackground' => [
		'label' => __( 'Footer Background', 'blocksy' ),
		'type' => 'ct-background',
		'design' => 'block:right',
		'responsive' => true,
		'setting' => [ 'transport' => 'postMessage' ],
		'value' => blocksy_background_default_value([
			'backgroundColor' => [
				'default' => [
					'color' => 'var(--paletteColor6)'
				],
			],
		]),
		'desc' => __( 'Please note, you can also change the background color for each row individually.', 'blocksy' ),
	],

];
