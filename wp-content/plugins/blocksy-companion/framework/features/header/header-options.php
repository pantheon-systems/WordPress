<?php

$options = [

	'has_sticky_header' => [
		'label' => __( 'Sticky Functionality', 'blocksy-companion' ),
		'type' => 'ct-switch',
		'value' => 'no',

		'sync' => [
			'id' => 'header_placements_1'
		]
	],

	blocksy_rand_md5() => [
		'type' => 'ct-condition',
		'condition' => [ 'has_sticky_header' => 'yes' ],
		'options' => [

			'sticky_rows' => [
				'label' => false,
				'type' => 'ct-image-picker',
				'value' => 'middle',
				'design' => 'block',
				'sync' => [
					'id' => 'header_placements_1'
				],

				'choices' => [
					'middle' => [
						'src' => blocksy_image_picker_url('sticky-main.svg'),
						'title' => __('Only Main Row', 'blocksy-companion'),
					],

					'top_middle' => [
						'src' => blocksy_image_picker_url('sticky-top-main.svg'),
						'title' => __('Top & Main Row', 'blocksy-companion'),
					],

					'entire_header' => [
						'src' => blocksy_image_picker_url('sticky-all.svg'),
						'title' => __('All Rows', 'blocksy-companion'),
					],

					'middle_bottom' => [
						'src' => blocksy_image_picker_url('sticky-main-bottom.svg'),
						'title' => __('Main & Bottom Row', 'blocksy-companion'),
					],

					'top' => [
						'src' => blocksy_image_picker_url('sticky-top.svg'),
						'title' => __('Only Top Row', 'blocksy-companion'),
					],

					'bottom' => [
						'src' => blocksy_image_picker_url('sticky-bottom.svg'),
						'title' => __('Only Bottom Row', 'blocksy-companion'),
					],
				],
			],

			'sticky_effect' => [
				'label' => __('Effect', 'blocksy-companion' ),
				'type' => 'ct-select',
				'value' => 'shrink',
				'design' => 'block',
				'sync' => [
					'id' => 'header_placements_1'
				],
				'choices' => blocksy_ordered_keys([
					'shrink' => __('Default', 'blocksy-companion'),
					'slide' => __('Slide Down', 'blocksy-companion'),
					'fade' => __('Fade', 'blocksy-companion'),
					'auto-hide' => __('Auto Hide/Show', 'blocksy-companion'),
				]),
			],

			'sticky_offset' => [
				'label' => __( 'Offset', 'blocksy-companion' ),
				'type' => 'ct-slider',
				'min' => 0,
				'max' => 300,
				'value' => 0,
				'responsive' => true,
				'divider' => 'top',
				'sync' => [
					'id' => 'header_placements_1'
				],
			],

			'sticky_behaviour' => [
				'label' => __( 'Enable on', 'blocksy-companion' ),
				'type' => 'ct-visibility',
				'design' => 'block',
				'divider' => 'top',
				'value' => [
					'desktop' => true,
					// 'tablet' => true,
					'mobile' => true,
				],

				'choices' => blocksy_ordered_keys([
					'desktop' => __('Desktop', 'blocksy-companion'),
					// 'tablet' => __('Tablet', 'blocksy-companion'),
					'mobile' => __('Mobile', 'blocksy-companion'),
				]),

				'sync' => [
					'id' => 'header_placements_1'
				],
			],
		],
	],

	blocksy_rand_md5() => [
		'type' => 'ct-divider',
	],

	'has_transparent_header' => [
		'label' => __( 'Transparent Functionality', 'blocksy-companion' ),
		'type' => 'ct-switch',
		'value' => 'no',
		'sync' => [
			'id' => 'header_placements_1'
		]
	],

	blocksy_rand_md5() => [
		'type' => 'ct-condition',
		'condition' => [
			'has_transparent_header' => 'yes',
			'id' => 'type-1'
		],
		'options' => [
			'transparent_conditions' => [
				'type' => 'blocksy-display-condition',
				'value' => [
					[
						'type' => 'include',
						'rule' => 'everywhere'
					],

					[
						'type' => 'exclude',
						'rule' => '404'
					],

					[
						'type' => 'exclude',
						'rule' => 'search'
					],

					[
						'type' => 'exclude',
						'rule' => 'archives'
					]
				],
				'label' => __( 'Display Conditions', 'blocksy-companion' ),
				'display' => 'modal',
				'design' => 'block',
				// 'divider' => 'top',
				'sync' => [
					'id' => 'header_placements_1'
				]
			],
		]
	],

	blocksy_rand_md5() => [
		'type' => 'ct-condition',
		'condition' => [ 'has_transparent_header' => 'yes' ],
		'options' => [
			'transparent_behaviour' => [
				'label' => __( 'Enable on', 'blocksy-companion' ),
				'type' => 'ct-visibility',
				'design' => 'block',
				'sync' => 'live',
				'value' => [
					'desktop' => true,
					// 'tablet' => true,
					'mobile' => true,
				],

				'choices' => blocksy_ordered_keys([
					'desktop' => __('Desktop', 'blocksy-companion'),
					// 'tablet' => __('Tablet', 'blocksy-companion'),
					'mobile' => __('Mobile', 'blocksy-companion'),
				]),
			],

		],
	],

];

