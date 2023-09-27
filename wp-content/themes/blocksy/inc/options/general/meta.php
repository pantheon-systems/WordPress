<?php

if (! isset($skip_sync_id)) {
	$skip_sync_id = null;
}

if (! isset($has_meta_elements_wrapper_attr)) {
	$has_meta_elements_wrapper_attr = true;
}

if (! isset($sync_id)) {
	$sync_id = null;
}

if (! isset($is_cpt)) {
	$is_cpt = false;
}

if (! isset($has_label)) {
	$has_label = false;
}

if (! isset($prefix)) {
	$prefix = '';
} else {
	$prefix = $prefix . '_';
}

if (! isset($is_page)) {
	$is_page = false;
}

if (! isset($item_style_type)) {
	$item_style_type = 'ct-radio';
}

if (! isset($item_divider_type)) {
	$item_divider_type = 'ct-radio';
}

if (! isset($computed_cpt) || ! $computed_cpt) {
	$computed_cpt = 'single_blog_post';

	if ($is_page) {
		$computed_cpt = 'single_page';
	}
}

if (! isset($post_type)) {
	$post_type = 'post';

	if ($computed_cpt === 'product') {
		$post_type = 'product';
	}

	if ($computed_cpt === 'single_page') {
		$post_type = 'page';
	}

	$post_types = blocksy_manager()->post_types->get_supported_post_types();

	foreach ($post_types as $single_post_type) {
		if (
			$computed_cpt === $single_post_type . '_archive'
			||
			$computed_cpt === $single_post_type . '_single'
		) {
			$post_type = $single_post_type;
		}
	}
}

$taxonomies = array_values(array_diff(
	get_object_taxonomies($post_type),
	['post_format']
));

$taxonomies_options = [];

foreach ($taxonomies as $taxonomy) {
	$taxonomy_object = get_taxonomy($taxonomy);

	if (! $taxonomy_object->public) {
		continue;
	}

	$taxonomies_options[$taxonomy] = $taxonomy_object->label;
}

if (! isset($meta_elements)) {
	$meta_elements = blocksy_post_meta_defaults([
		[
			'id' => 'author',
			'enabled' => true,
		],

		[
			'id' => 'post_date',
			'enabled' => true,
		],

		[
			'id' => 'updated_date',
			'enabled' => false,
		],

		[
			'id' => 'categories',
			'enabled' => true,
		],

		[
			'id' => 'comments',
			'enabled' => true,
		]
	]);
}

$meta_elements = apply_filters(
	'blocksy:options:meta:meta_default_elements',
	$meta_elements,
	$prefix,
	$computed_cpt
);

$date_format_options = [
	blocksy_rand_md5() => [
		'type' => 'ct-group',
		'attr' => [ 'data-columns' => '1' ],
		'options' => [
			'date_format_source' => [
				'label' => __( 'Format', 'blocksy' ),
				'type' => 'ct-select',
				'value' => 'default',
				'view' => 'text',
				'design' => 'inline',
				'choices' => blocksy_ordered_keys(
					[
						'default' => __( 'Default', 'blocksy' ),
						'custom' => __( 'Custom', 'blocksy' ),
					]
				),
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [
					'date_format_source' => 'custom'
				],
				'options' => [
					'date_format' => [
						'label' => false,
						'type' => 'text',
						'design' => 'block',
						'value' => 'M j, Y',
						// translators: The interpolations addes a html link around the word.
						'desc' => sprintf(
							__('Date format %sinstructions%s.', 'blocksy'),
							'<a href="https://wordpress.org/support/article/formatting-date-and-time/#format-string-examples" target="_blank">',
							'</a>'
						),
						'disableRevertButton' => true,
					],
				],
			],
		],
	],
];

$options = [
	$prefix . 'meta_elements' => [
		'label' => $has_label ? __( 'Meta Elements', 'blocksy' ) : false,
		'type' => 'ct-layers',
		'itemClass' => $has_meta_elements_wrapper_attr ? 'ct-inner-layer' : '',
		// 'manageable' => true,
		'value' => $meta_elements,
		'sync' => $sync_id ? $sync_id : 'refresh',

		'settings' => array_merge([
			'author' => [
				'label' => __('Author', 'blocksy'),
				'options' => [
					'has_author_avatar' => [
						'label' => __( 'Author Avatar', 'blocksy' ),
						'type' => 'ct-switch',
						'value' => 'no',
					],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => ['has_author_avatar' => 'yes'],
						'options' => [
							'avatar_size' => array_merge([
								'label' => __('Avatar Size', 'blocksy'),
								'type' => 'ct-number',
								'design' => 'inline',
								'value' => 25,
								'min' => 15,
								'max' => 50,
							], $skip_sync_id ? [
								'sync' => $skip_sync_id
							] : []),
						],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [ 'meta_type' => 'label' ],
						'values_source' => 'parent',
						'options' => [
							'label' => [
								'label' => __('Label', 'blocksy'),
								'type' => 'text',
								'design' => 'inline',
								'value' => __('By', 'blocksy')
							],
						],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [ 'meta_type' => 'icons' ],
						'values_source' => 'parent',
						'options' => apply_filters(
							'blocksy:general:card:options:icon', 
							[],
							'blc blc-feather'
						)
					],
					
				],
			],

			'comments' => [
				'label' => __('Comments', 'blocksy'),
				'options' => [
					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [ 'meta_type' => 'icons' ],
						'values_source' => 'parent',
						'options' => apply_filters(
							'blocksy:general:card:options:icon', 
							[],
							'blc blc-comments'
						)
					],
				]
			],

			'post_date' => [
				'label' => __('Published Date', 'blocksy'),
				'options' => [
					$date_format_options,

					[
						blocksy_rand_md5() => [
							'type' => 'ct-condition',
							'condition' => [ 'meta_type' => 'label' ],
							'values_source' => 'parent',
							'options' => [
								'label' => [
									'label' => __('Label', 'blocksy'),
									'type' => 'text',
									'design' => 'inline',
									'value' => __('On', 'blocksy')
							],
							],
						],

						blocksy_rand_md5() => [
							'type' => 'ct-condition',
							'condition' => [ 'meta_type' => 'icons' ],
							'values_source' => 'parent',
							'options' => apply_filters(
								'blocksy:general:card:options:icon', 
								[],
								'blc blc-clock'
							)
						],
					],
					
				],
			],

			'updated_date' => [
				'label' => __('Updated Date', 'blocksy'),
				'options' => [
					$date_format_options,

					[
						blocksy_rand_md5() => [
							'type' => 'ct-condition',
							'condition' => [ 'meta_type' => 'label' ],
							'values_source' => 'parent',
							'options' => [
								'label' => [
									'label' => __('Label', 'blocksy'),
									'type' => 'text',
									'design' => 'inline',
									'value' => __('On', 'blocksy')
							],
							],
						],

						blocksy_rand_md5() => [
							'type' => 'ct-condition',
							'condition' => [ 'meta_type' => 'icons' ],
							'values_source' => 'parent',
							'options' => apply_filters(
								'blocksy:general:card:options:icon', 
								[],
								'blc blc-clock'
							)
						],
					],
				],
			],
		], ! empty($taxonomies_options) ? [
			'categories' => [
				'label' => __('Taxonomies', 'blocksy'),
				'clone' => 5,
				'options' => [
					'taxonomy' => [
						'label' => __( 'Taxonomy', 'blocksy' ),
						'type' => 'ct-select',
						'design' => 'inline',
						'setting' => [ 'transport' => 'postMessage' ],
						'view' => 'text',
						'choices' => blocksy_ordered_keys($taxonomies_options),
						'value' => blocksy_maybe_get_matching_taxonomy($post_type),
					],

					'style' => array_merge([
						'label' => __( 'Style', 'blocksy' ),
						'type' => 'ct-select',
						'design' => 'inline',
						'setting' => [ 'transport' => 'postMessage' ],
						'view' => 'text',
						'choices' => blocksy_ordered_keys(
							[
								'simple' => __( 'Default', 'blocksy' ),
								'pill' => __( 'Button', 'blocksy' ),
								'underline' => __( 'Underline', 'blocksy' ),
							]
						),
					]),

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [ 'meta_type' => 'label' ],
						'values_source' => 'parent',
						'options' => [
							'label' => array_merge([
								'label' => __('Label', 'blocksy'),
								'type' => 'text',
								'design' => 'inline',
								'value' => __('In', 'blocksy')
							], $skip_sync_id ? [
								'sync' => $skip_sync_id
							] : []),
						],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [ 'meta_type' => 'icons' ],
						'values_source' => 'parent',
						'options' => apply_filters(
							'blocksy:general:card:options:icon', 
							[],
							'blc blc-box'
						)
					],
				],
			]
		] : [], apply_filters(
			'blocksy:options:meta:meta_elements',
			[],
			$prefix,
			$computed_cpt
		)),
	],

	$prefix . 'meta_type' => array_merge([
		'label' => __('Items Style', 'blocksy'),
		'type' => $item_style_type,
		'value' => 'simple',
		'view' => 'text',
		'choices' => [
			'simple' => __('Simple', 'blocksy'),
			'label' => __('Labels', 'blocksy'),
			'icons' => __('Icons', 'blocksy'),
		],
	], $sync_id ? [
		'sync' => $sync_id
	] : []),

	$prefix . 'meta_divider' => array_merge([
		'label' => __('Items Divider', 'blocksy'),
		'type' => $item_divider_type,
		'value' => 'slash',
		'view' => 'text',
		'attr' => [ 'data-type' => 'meta-divider' ],
		'choices' => [
			'none' => __('none', 'blocksy'),
			'slash' => '',
			'line' => '',
			'circle' => '',
		],
	], $skip_sync_id ? [
		'sync' => $skip_sync_id
	] : [
		'sync' => 'live'
	]),
];

