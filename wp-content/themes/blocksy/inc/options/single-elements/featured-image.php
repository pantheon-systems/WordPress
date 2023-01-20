<?php

if (! isset($prefix)) {
	$prefix = '';
} else {
	$prefix = $prefix . '_';
}

$options = [
	$prefix . 'has_featured_image' => [
		'label' => __( 'Featured Image', 'blocksy' ),
		'type' => 'ct-panel',
		'switch' => true,
		'value' => 'no',
		'sync' => blocksy_sync_single_post_container([
			'prefix' => $prefix
		]),
		'inner-options' => [

			$prefix . 'featured_image_ratio' => [
				'label' => __( 'Image Ratio', 'blocksy' ),
				'type' => 'ct-ratio',
				'value' => 'original',
				'design' => 'inline',
				'sync' => 'live',
			],

			$prefix . 'featured_image_size' => [
				'label' => __('Image Size', 'blocksy'),
				'type' => 'ct-select',
				'value' => 'full',
				'view' => 'text',
				'design' => 'inline',
				'sync' => blocksy_sync_single_post_container([
					'prefix' => $prefix
				]),
				'choices' => blocksy_ordered_keys(blocksy_get_all_image_sizes())
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [
					$prefix . 'structure' => 'type-3 | type-4',
					$prefix . 'content_style' => '~wide',
				],
				'options' => [
					$prefix . 'featured_image_width' => [
						'label' => __( 'Image Width', 'blocksy' ),
						'type' => 'ct-radio',
						'value' => 'default',
						'view' => 'text',
						'design' => 'block',
						'divider' => 'top',
						'choices' => [
							'default' => __( 'Default', 'blocksy' ),
							'wide' => __( 'Wide', 'blocksy' ),
							'full' => __( 'Full', 'blocksy' ),
						],
						'sync' => 'live'
					],
				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [
					$prefix . 'hero_enabled' => 'yes',
					$prefix . 'hero_section' => '!type-2'
				],
				'options' => [
					$prefix . 'featured_image_location' => [
						'label' => __( 'Image Location', 'blocksy' ),
						'type' => 'ct-radio',
						'value' => 'above',
						'view' => 'text',
						'design' => 'block',
						'divider' => 'top',
						'choices' => [
							'above' => __( 'Above Title', 'blocksy' ),
							'below' => __( 'Below Title', 'blocksy' ),
						],
						'sync' => blocksy_sync_single_post_container([
							'prefix' => $prefix
						]),
					],
				],
			],

			$prefix . 'featured_image_visibility' => [
				'label' => __( 'Image Visibility', 'blocksy' ),
				'type' => 'ct-visibility',
				'design' => 'block',
				'divider' => 'top',

				'value' => [
					'desktop' => true,
					'tablet' => true,
					'mobile' => false,
				],

				'choices' => blocksy_ordered_keys([
					'desktop' => __( 'Desktop', 'blocksy' ),
					'tablet' => __( 'Tablet', 'blocksy' ),
					'mobile' => __( 'Mobile', 'blocksy' ),
				]),

				'sync' => 'live'
			],

		],
	],

];
