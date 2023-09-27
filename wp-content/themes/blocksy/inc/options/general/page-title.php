<?php
/**
 * Page title options
 *
 * @copyright 2019-present Creative Themes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @package   Blocksy
 */

if (! isset($has_hero_type)) {
	$has_hero_type = true;
}

if (! isset($enabled_label)) {
	$enabled_label = __('Page Title', 'blocksy');
}

if (! isset($design_options)) {
	$design_options = [];
}

if (! isset($enabled_default)) {
	$enabled_default = 'yes';
}

if (! isset($is_cpt)) {
	$is_cpt = false;
}

if (! isset($has_hero_elements)) {
	$has_hero_elements = true;
}

if (! isset($is_bbpress)) {
	$is_bbpress = false;
}

if (! isset($is_tutorlms)) {
	$is_tutorlms = false;
}

if (! isset($is_woo)) {
	$is_woo = false;
}

if (! isset($is_page)) {
	$is_page = false;
}

if (! isset($is_author)) {
	$is_author = false;
}

if (! isset($has_default)) {
	$has_default = false;
}

if (! isset($is_home)) {
	$is_home = false;
}

if (! isset($is_single)) {
	$is_single = false;
}

if (! isset($is_search)) {
	$is_search = false;
}

if (! isset($is_archive)) {
	$is_archive = false;
}

if (! isset($prefix)) {
	$prefix = '';
} else {
	$prefix = $prefix . '_';
}

$computed_cpt = $is_cpt || $prefix === 'product_' ? trim( $prefix, '_') : false;

if ($is_cpt && ! $prefix) {
	$computed_cpt = $is_cpt;
}

$archives_have_hero = apply_filters(
	'blocksy:options:page-title:archives-have-hero',
	false
);

$custom_description_layer_name = __('Description', 'blocksy');

if ($is_author) {
	$custom_description_layer_name = __('Bio', 'blocksy');
}

if (
	(
		$is_single || $is_home
	) && !$is_bbpress
) {
	$custom_description_layer_name = __('Excerpt', 'blocksy');
}

if ($is_search) {
	$custom_description_layer_name = __('Subtitle', 'blocksy');
}

$default_hero_elements = [];

$default_hero_elements[] = array_merge([
	'id' => 'custom_title',
	'enabled' => $prefix !== 'product_',
	'heading_tag' => 'h1',
	'title' => __('Home', 'blocksy')
], (
	$is_author ? [
		'has_author_avatar' => 'yes',
		'author_avatar_size' => 60
	] : []
));

if (! $is_tutorlms) {
	$default_hero_elements[] = [
		'id' => 'custom_description',
		'enabled' => $prefix !== 'product_',
		'description_visibility' => [
			'desktop' => true,
			'tablet' => true,
			'mobile' => false,
		]
	];
}

if (
	(
		$is_single || $is_author
	) && !$is_bbpress && !$is_tutorlms
) {
	$default_hero_elements[] = [
		'id' => 'custom_meta',
		'enabled' => ! $is_page && $prefix !== 'product_',
		'meta_elements' => blocksy_post_meta_defaults([
			[
				'id' => 'author',
				'has_author_avatar' => 'yes',
				'enabled' => true,
			],

			[
				'id' => 'post_date',
				'enabled' => true,
			],

			[
				'id' => 'comments',
				'enabled' => true,
			],

			[
				'id' => 'categories',
				'enabled' => ! $is_page,
			],
		]),
		'page_meta_elements' => [
			'joined' => true,
			'articles_count' => true,
			'comments' => true
		]
	];
}

if ($is_author) {
	$default_hero_elements[] = [
		'id' => 'author_social_channels',
		'enabled' => true
	];
}

$default_hero_elements[] = [
	'id' => 'breadcrumbs',
	'enabled' => $prefix === 'product_',
];

$when_enabled_general_settings = [
	$has_hero_type ? [
		$prefix . 'hero_section' => [
			'label' => $has_default ? __('Type', 'blocksy') : false,
			'type' => 'ct-image-picker',
			'value' => ($is_woo || $is_author) ? 'type-2' : 'type-1',
			'design' => 'block',
			'sync' => blocksy_sync_whole_page([
				'prefix' => $prefix,
				'prefix_custom' => 'hero'
			]),
			'choices' => [
				'type-1' => [
					'src' => blocksy_image_picker_url('hero-type-1.svg'),
					'title' => __('Type 1', 'blocksy'),
				],

				'type-2' => [
					'src' => blocksy_image_picker_url('hero-type-2.svg'),
					'title' => __('Type 2', 'blocksy'),
				],
			],
		],
	] : [
		$prefix . 'hero_section' => [
			'type' => 'hidden',
			'value' => ($is_woo || $is_author) ? 'type-2' : 'type-1',
		]
	],

	[
		$prefix . 'hero_elements' => apply_filters('blocksy:options:page-title:hero-elements', [
			'label' => __('Elements', 'blocksy'),
			'type' => $has_hero_elements ? 'ct-layers' : 'hidden',
			'design' => 'block',
			'value' => $default_hero_elements,
			'sync' => [
				[
					'selector' => blocksy_prefix_custom_selector('.hero-section', 'hero'),
					'container_inclusive' => true,
					'prefix' => $prefix,
					'render' => function ($args) {
						echo blocksy_output_hero_section([
							'type' => get_theme_mod(
								$args['prefix'] . '_hero_section',
								'type-1'
							)
						]);
					},
					'prefix_custom' => 'hero'
				],

				[
					'prefix' => $prefix,
					'id' => $prefix . 'hero_elements_heading_tag',
					'loader_selector' => '.page-title',
					'prefix_custom' => 'hero'
				],

				[
					'prefix' => $prefix,
					'id' => $prefix . 'hero_elements_meta_first',
					'loader_selector' => '.entry-meta:1',
					'prefix_custom' => 'hero'
				],

				[
					'prefix' => $prefix,
					'id' => $prefix . 'hero_elements_meta_second',
					'loader_selector' => '.entry-meta:2',
					'prefix_custom' => 'hero'
				],

				[
					'prefix' => $prefix,
					'id' => $prefix . 'hero_elements_spacing',
					'loader_selector' => 'skip',
					'prefix_custom' => 'hero'
				],

				[
					'prefix' => $prefix,
					'id' => $prefix . 'hero_elements_author_avatar',
					'loader_selector' => '.ct-author-name',
					'prefix_custom' => 'hero'
				]
			],

			'settings' => [
				'breadcrumbs' => [
					'label' => __('Breadcrumbs', 'blocksy'),
					'options_condition' => [
						'itemIndex' => '!0'
					],
					'options' => [
						blocksy_rand_md5() => [
							'type' => 'ct-condition',
							'condition' => ['itemIndex' => '!0'],
							'options' => [

								'hero_item_spacing' => [
									'label' => __( 'Top Spacing', 'blocksy' ),
									'type' => 'ct-slider',
									'value' => 20,
									'min' => 0,
									'max' => 100,
									'responsive' => true,
									'sync' => [
										'id' => $prefix . 'hero_elements_spacing',
									],
								],
							]
						],

					],
				],

				'custom_title' => [
					'label' => $is_author ? __( 'Name & Avatar', 'blocksy' ) : __('Title', 'blocksy'),
					'options' => [
						[
							'heading_tag' => [
								'label' => __('Heading tag', 'blocksy'),
								'type' => 'ct-select',
								'value' => 'h1',
								'view' => 'text',
								'design' => 'inline',
								'sync' => [
									'id' => $prefix . 'hero_elements_heading_tag',
								],
								'choices' => blocksy_ordered_keys(
									[
										'h1' => 'H1',
										'h2' => 'H2',
										'h3' => 'H3',
										'h4' => 'H4',
										'h5' => 'H5',
										'h6' => 'H6',
									]
								),
							],
						],

						[
							$is_home ? [
								blocksy_rand_md5() => [
									'type' => 'ct-condition',
									'condition' => ['show_on_front' => 'posts'],
									'values_source' => 'global',
									'options' => [
										'title' => [
											'label' => __('Title', 'blocksy'),
											'type' => 'text',
											'value' => __('Home', 'blocksy'),
											'disableRevertButton' => true,
											'design' => 'inline',
										],
									]
								],
							] : []
						],

						[
							($is_archive || $is_bbpress) ? [
								'has_category_label' => [
									'label' => __('Category Label', 'blocksy'),
									'type' => 'ct-switch',
									'value' => 'yes',
								]
							] : []
						],

						[
							$is_author ? [
								blocksy_rand_md5() => [
									'type' => 'ct-group',
									'attr' => [ 'data-columns' => '1' ],
									'options' => [
										'has_author_avatar' => [
											'label' => __('Author avatar', 'blocksy'),
											'type' => 'ct-switch',
											'value' => 'yes',
											'sync' => [
												'id' => $prefix . 'hero_elements_author_avatar',
											],
										],

										blocksy_rand_md5() => [
											'type' => 'ct-condition',
											'condition' => [ 'has_author_avatar' => 'yes' ],
											'options' => [
												'author_avatar_size' => [
													'label' => __( 'Avatar Size', 'blocksy' ),
													'type' => 'ct-number',
													'design' => 'inline',
													'value' => 60,
													'min' => 15,
													'max' => 300,
													'sync' => [
														'id' => $prefix . 'hero_elements_spacing',
													],
												],
											],
										],

									],
								],
							] : []
						],

						blocksy_rand_md5() => [
							'type' => 'ct-condition',
							'condition' => ['itemIndex' => '!0'],
							'options' => [
								'hero_item_spacing' => [
									'label' => __( 'Top Spacing', 'blocksy' ),
									'type' => 'ct-slider',
									'value' => 20,
									'min' => 0,
									'max' => 100,
									'responsive' => true,
									'sync' => [
										'id' => $prefix . 'hero_elements_spacing',
									],
								],
							]
						]

					],
				],

				'custom_description' => [
					'label' => $custom_description_layer_name,
					'options' => [
						[
							$is_home ? [
								blocksy_rand_md5() => [
									'type' => 'ct-condition',
									'condition' => [ 'show_on_front' => 'posts' ],
									'values_source' => 'global',
									'options' => [
										'description' => [
											'label' => false,
											'type' => 'textarea',
											'value' => '',
											'disableRevertButton' => true,
											'design' => 'block',
										],
									]
								],
							] : []
						],

						blocksy_rand_md5() => [
							'type' => 'ct-condition',
							'condition' => [ $prefix . 'hero_section' => 'type-1' ],
							'values_source' => 'parent',
							'options' => [

								'hero_item_max_width' => [
									'label' => __('Max Width', 'blocksy'),
									'type' => 'ct-slider',
									'value' => 100,
									'min' => 10,
									'max' => 100,
									'defaultUnit' => '%',
									'responsive' => true,
									'sync' => [
										'id' => $prefix . 'hero_elements_spacing',
									],
								],

							],
						],

						blocksy_rand_md5() => [
							'type' => 'ct-condition',
							'condition' => ['itemIndex' => '!0'],
							'options' => [
								'hero_item_spacing' => [
									'label' => __( 'Top Spacing', 'blocksy' ),
									'type' => 'ct-slider',
									'value' => 20,
									'min' => 0,
									'max' => 100,
									'responsive' => true,
									'sync' => [
										'id' => $prefix . 'hero_elements_spacing',
									],
								],
							]
						],

						'description_visibility' => [
							'label' => __( 'Visibility', 'blocksy' ),
							'type' => 'ct-visibility',
							'design' => 'block',

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

							'sync' => [
								'id' => $prefix . 'hero_elements_spacing',
							],
						],
					],
				],

				'custom_meta' => [
					// 'label' => __('Post Meta', 'blocksy'),
					'label' => $is_author ? __( 'Author Meta', 'blocksy' ) : __('Post Meta', 'blocksy'),
					'clone' => true,
					'sync' => [
						'id' => $prefix . 'hero_elements_meta'
					],
					'options' => [
						$is_author ? [
							'page_meta_elements' => [
								'label' => __( 'Meta Elements', 'blocksy' ),
								'type' => 'ct-checkboxes',
								'design' => 'block',
								'attr' => [ 'data-columns' => '2' ],
								'allow_empty' => true,
								'choices' => blocksy_ordered_keys(
									[
										'joined' => __( 'Joined Date', 'blocksy' ),
										'articles_count' => __( 'Articles', 'blocksy' ),
										'comments' => __( 'Comments', 'blocksy' ),
									]
								),

								'value' => [
									'joined' => true,
									'articles_count' => true,
									'comments' => true
								],
							],
						] : [],

						[
							$is_single ? [
								blocksy_get_options('general/meta', [
									'skip_sync_id' => [
										'id' => $prefix . 'hero_elements_spacing',
									],
									'is_page' => $is_page,
									'is_cpt' => $is_cpt,
									'computed_cpt' => $computed_cpt
								])
							] : []
						],

						blocksy_rand_md5() => [
							'type' => 'ct-condition',
							'condition' => ['itemIndex' => '!0'],
							'options' => [
								'hero_item_spacing' => [
									'label' => __( 'Top Spacing', 'blocksy' ),
									'type' => 'ct-slider',
									'value' => 20,
									'min' => 0,
									'max' => 100,
									'responsive' => true,
									'sync' => [
										'id' => $prefix . 'hero_elements_spacing',
									],
								],
							]
						]
					],
				],

				'author_social_channels' => [
					'label' => __('Social Channels', 'blocksy'),
					'options_condition' => [
						'itemIndex' => '!0'
					],
					'options' => [
						blocksy_rand_md5() => [
							'type' => 'ct-condition',
							'condition' => ['itemIndex' => '!0'],
							'options' => [
								'hero_item_spacing' => [
									'label' => __( 'Top Spacing', 'blocksy' ),
									'type' => 'ct-slider',
									'value' => 20,
									'min' => 0,
									'max' => 100,
									'responsive' => true,
									'sync' => [
										'id' => $prefix . 'hero_elements_spacing',
									],
								],
							]
						],

						'link_target' => [
							'type'  => 'ct-switch',
							'label' => __( 'Open links in new tab', 'blocksy' ),
							'value' => 'no',
						],
					],
				],
			]
		], trim($prefix, '_')),

		blocksy_rand_md5() => [
			'type' => 'ct-divider',
		],

		blocksy_rand_md5() => [
			'type' => 'ct-condition',
			'condition' => [ $prefix . 'hero_section' => 'type-1' ],
			'options' => [

				$prefix . 'hero_alignment1' => [
					'type' => 'ct-radio',
					'label' => __( 'Horizontal Alignment', 'blocksy' ),
					'value' => apply_filters(
						'blocksy:hero:type-1:default-alignment',
						'CT_CSS_SKIP_RULE',
						trim($prefix, '_')
					),
					'view' => 'text',
					'attr' => [ 'data-type' => 'alignment' ],
					'responsive' => true,
					'design' => 'block',
					'sync' => 'live',
					'choices' => [
						'left' => '',
						'center' => '',
						'right' => '',
					],
				],

				$prefix . 'hero_margin' => [
					'label' => __( 'Container Bottom Spacing', 'blocksy' ),
					'type' => 'ct-slider',
					'value' => 40,
					'min' => 0,
					'max' => 300,
					'responsive' => true,
					'divider' => 'top',
					'setting' => [ 'transport' => 'postMessage' ],
				],
			],
		],

		blocksy_rand_md5() => [
			'type' => 'ct-condition',
			'condition' => [ $prefix . 'hero_section' => 'type-2' ],
			'options' => array_merge([

				$prefix . 'hero_alignment2' => [
					'type' => 'ct-radio',
					'label' => __( 'Horizontal Alignment', 'blocksy' ),
					'value' => 'center',
					'view' => 'text',
					'attr' => [ 'data-type' => 'alignment' ],
					'responsive' => true,
					'design' => 'block',
					'sync' => 'live',
					'choices' => [
						'left' => '',
						'center' => '',
						'right' => '',
					],
				],

				$prefix . 'hero_vertical_alignment' => [
					'type' => 'ct-radio',
					'label' => __( 'Vertical Alignment', 'blocksy' ),
					'value' => 'center',
					'view' => 'text',
					'design' => 'block',
					'responsive' => true,
					'attr' => [ 'data-type' => 'vertical-alignment' ],
					'sync' => 'live',

					'choices' => [
						'flex-start' => '',
						'center' => '',
						'flex-end' => '',
					],
				],
			]),
		],

	],

	[

		blocksy_rand_md5() => [
			'type' => 'ct-condition',
			'condition' => [ $prefix . 'hero_section' => 'type-2' ],
			'options' => [

				blocksy_rand_md5() => [
					'type' => 'ct-divider',
				],

				$prefix . 'hero_structure' => [
					'label' => __( 'Container Width', 'blocksy' ),
					'type' => 'ct-radio',
					'value' => 'narrow',
					'view' => 'text',
					'design' => 'block',
					'sync' => 'live',
					'choices' => [
						'normal' => __( 'Default', 'blocksy' ),
						'narrow' => __( 'Narrow', 'blocksy' ),
					],
				],

				blocksy_rand_md5() => [
					'type' => 'ct-divider',
				],

				$prefix . 'page_title_bg_type' => [
					'label' => __( 'Container Background Image', 'blocksy' ),
					'type' => 'ct-radio',
					'value' => ($is_archive && ! $archives_have_hero || $is_author || $is_search) ? 'color' : 'featured_image',
					'view' => 'text',
					'design' => 'block',
					'attr' => [ 'data-radio-text' => 'small' ],
					'choices' => array_merge(($is_author || $is_search || $is_archive && ! $archives_have_hero) ? [] : [
						'featured_image' => __( 'Featured', 'blocksy' ),
					], [
						'custom_image' => __( 'Custom', 'blocksy' ),
						'color' => __( 'None', 'blocksy' ),
					]),
					'sync' => [
						'id' => $prefix . 'hero_elements',
					],
				],

				blocksy_rand_md5() => [
					'type' => 'ct-condition',
					'condition' => [
						$prefix . 'page_title_bg_type' => 'custom_image'
					],
					'options' => [
						$prefix . 'custom_hero_background' => [
							'label' => __('Custom Image', 'blocksy'),
							'type' => 'ct-image-uploader',
							'design' => false,
							'divider' => 'top',
							'value' => [ 'attachment_id' => null ],
							'emptyLabel' => __('Select Image', 'blocksy'),
							'filledLabel' => __('Change Image', 'blocksy'),
							'sync' => [
								'id' => $prefix . 'hero_elements',
							],
						],
					],
				],

				blocksy_rand_md5() => [
					'type' => 'ct-condition',
					'condition' => [
						$prefix . 'page_title_bg_type' => 'custom_image | featured_image',
					],
					'options' => [

						$prefix . 'page_title_image_size' => [
							'label' => __('Image Size', 'blocksy'),
							'type' => 'ct-select',
							'value' => 'full',
							'view' => 'text',
							'design' => 'inline',
							'divider' => 'top',
							'choices' => blocksy_ordered_keys(blocksy_get_all_image_sizes()),
							'sync' => [
								'id' => $prefix . 'hero_elements',
							],
						],

						$prefix . 'parallax' => [
							'label' => __( 'Image Parallax Effect', 'blocksy' ),
							'desc' => __( 'Choose for which devices you want to enable the parallax effect.', 'blocksy' ),
							'type' => 'ct-visibility',
							'design' => 'block',
							'divider' => 'top',
							'allow_empty' => true,
							'sync' => 'live',
							'value' => [
								'desktop' => false,
								'tablet' => false,
								'mobile' => false,
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
					'type' => 'ct-divider',
				],

				$prefix . 'hero_height' => [
					'label' => __( 'Container Min Height', 'blocksy' ),
					'type' => 'ct-slider',
					'value' => '250px',
					'design' => 'block',
					'units' => [
						[ 'unit' => 'px','min' => 0, 'max' => 1000 ],
						[ 'unit' => 'vw', 'min' => 0, 'max' => 100 ],
						[ 'unit' => 'vh', 'min' => 0, 'max' => 100 ],
						[ 'unit' => 'em', 'min' => 0, 'max' => 100 ],
						[ 'unit' => 'rem', 'min' => 0, 'max' => 100 ],
					],
					'responsive' => true,
					'sync' => 'live'
				],
			],
		],
	],
];

$when_enabled_design_settings = [
	$design_options ? $design_options : [
		blocksy_rand_md5() => [
			'type' => 'ct-condition',
			'condition' => [
				$prefix . 'hero_elements:array-ids:custom_title:enabled' => 'true',
			],
			'options' => [
				$prefix . 'pageTitleFont' => [
					'type' => 'ct-typography',
					'label' => __( 'Title Font', 'blocksy' ),
					'value' => blocksy_typography_default_values([
						'size' => '30px'
					]),
					'design' => 'block',
					'sync' => 'live'
				],

				$prefix . 'pageTitleFontColor' => [
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
							'inherit' => [
								'var(--heading-1-color, var(--headings-color))' => [
									$prefix . 'hero_elements:array-ids:custom_title:heading_tag' => 'h1'
								],

								'var(--heading-2-color, var(--headings-color))' => [
									$prefix . 'hero_elements:array-ids:custom_title:heading_tag' => 'h2'
								],

								'var(--heading-3-color, var(--headings-color))' => [
									$prefix . 'hero_elements:array-ids:custom_title:heading_tag' => 'h3'
								],

								'var(--heading-4-color, var(--headings-color))' => [
									$prefix . 'hero_elements:array-ids:custom_title:heading_tag' => 'h4'
								],

								'var(--heading-5-color, var(--headings-color))' => [
									$prefix . 'hero_elements:array-ids:custom_title:heading_tag' => 'h5'
								],

								'var(--heading-6-color, var(--headings-color))' => [
									$prefix . 'hero_elements:array-ids:custom_title:heading_tag' => 'h6'
								]
							]
						],
					],
				],
			]
		],

		blocksy_rand_md5() => [
			'type' => 'ct-condition',
			'condition' => [
				'all' => [
					$prefix . 'hero_elements:array-ids-true:custom_meta:enabled' => 'true',
					'any' => [
						$prefix . 'hero_elements:array-ids:custom_meta:single_meta_elements/author' => 'true',
						$prefix . 'hero_elements:array-ids:custom_meta:single_meta_elements/comments' => 'true',
						$prefix . 'hero_elements:array-ids:custom_meta:single_meta_elements/date' => 'true',
						$prefix . 'hero_elements:array-ids:custom_meta:single_meta_elements/updated' => 'true',
						$prefix . 'hero_elements:array-ids:custom_meta:single_meta_elements/categories' => 'true',
						$prefix . 'hero_elements:array-ids:custom_meta:single_meta_elements/tags' => 'true',
						$prefix . 'hero_elements:array-ids:custom_meta:page_meta_elements/joined' => 'true',
						$prefix . 'hero_elements:array-ids:custom_meta:page_meta_elements/articles_count' => 'true',
						$prefix . 'hero_elements:array-ids:custom_meta:page_meta_elements/comments' => 'true',
					]
				],
			],

			'options' => [
				$prefix . 'pageMetaFont' => [
					'type' => 'ct-typography',
					'label' => __( 'Meta Font', 'blocksy' ),
					'value' => blocksy_typography_default_values([
						'size' => '12px',
						'variation' => 'n6',
						'line-height' => '1.3',
						'text-transform' => 'uppercase',
					]),
					'design' => 'block',
					'sync' => 'live',
					'divider' => 'top:full',
				],

				$prefix . 'pageMetaFontColor' => [
					'label' => __( 'Meta Font Color', 'blocksy' ),
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
					'type' => 'ct-has-meta-category-button',
					'optionId' => $prefix . 'hero_elements',
					'options' => [
						$prefix . 'page_meta_button_type_font_colors' => [
							'label' => __( 'Meta Button Font', 'blocksy' ),
							'type'  => 'ct-color-picker',
							'design' => 'inline',
							'divider' => 'top',
							'noColor' => [ 'background' => 'var(--color)'],
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
									'inherit' => 'var(--buttonTextInitialColor)'
								],

								[
									'title' => __( 'Hover', 'blocksy' ),
									'id' => 'hover',
									'inherit' => 'var(--buttonTextHoverColor)'
								],
							],
						],

						$prefix . 'page_meta_button_type_background_colors' => [
							'label' => __( 'Meta Button Background', 'blocksy' ),
							'type'  => 'ct-color-picker',
							'design' => 'inline',
							'noColor' => [ 'background' => 'var(--color)'],
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
									'inherit' => 'var(--buttonInitialColor)'
								],

								[
									'title' => __( 'Hover', 'blocksy' ),
									'id' => 'hover',
									'inherit' => 'var(--buttonHoverColor)'
								],
							],
						],
					]
				],

			],
		],

		blocksy_rand_md5() => [
			'type' => 'ct-condition',
			'condition' => [
				'all' => [
					$prefix . 'hero_elements:array-ids:custom_description:enabled' => 'true',
					'any' => [
						$prefix . 'hero_elements:array-ids:custom_description:description_visibility/desktop' => 'true',
						$prefix . 'hero_elements:array-ids:custom_description:description_visibility/tablet' => 'true',
						$prefix . 'hero_elements:array-ids:custom_description:description_visibility/mobile' => 'true',
					]
				]
			],
			'options' => [
				$prefix . 'pageExcerptFont' => [
					'type' => 'ct-typography',
					'label' => $is_single ? __( 'Excerpt Font', 'blocksy' ) : sprintf(
						// translators: %s entity of font
						__('%s Font', 'blocksy'),
						$custom_description_layer_name
					),
					'value' => blocksy_typography_default_values([]),
					'design' => 'block',
					'sync' => 'live',
					'divider' => 'top:full',
				],

				$prefix . 'pageExcerptColor' => [
					'label' => $is_single ? __('Excerpt Font Color', 'blocksy' ) : sprintf(
						// translators: %s entity of font color
						__('%s Font Color', 'blocksy'),
						$custom_description_layer_name
					),
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
			],
		],

		blocksy_rand_md5() => [
			'type' => 'ct-condition',
			'condition' => [
				$prefix . 'hero_elements:array-ids:breadcrumbs:enabled' => 'true',
			],
			'options' => [
				$prefix . 'breadcrumbsFont' => [
					'type' => 'ct-typography',
					'label' => __( 'Breadcrumbs Font', 'blocksy' ),
					'value' => blocksy_typography_default_values([]),
					'design' => 'block',
					'sync' => 'live',
					'divider' => 'top:full',
				],

				$prefix . 'breadcrumbsFontColor' => [
					'label' => __( 'Breadcrumbs Font Color', 'blocksy' ),
					'type'  => 'ct-color-picker',
					'design' => 'inline',
					'sync' => 'live',

					'value' => [
						'default' => [
							'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
						],

						'initial' => [
							'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
						],

						'hover' => [
							'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
						],
					],

					'pickers' => [
						[
							'title' => __( 'Text', 'blocksy' ),
							'id' => 'default',
							'inherit' => 'var(--color)'
						],

						[
							'title' => __( 'Link Initial', 'blocksy' ),
							'id' => 'initial',
							'inherit' => 'var(--linkInitialColor)'
						],

						[
							'title' => __( 'Link Hover', 'blocksy' ),
							'id' => 'hover',
							'inherit' => 'var(--linkHoverColor)'
						],
					],
				],
			]
		],
	],

	apply_filters(
		'blocksy:options:page-title:design:before_breadcrumbs',
		[],
		trim($prefix, '_')
	),

	blocksy_rand_md5() => [
		'type' => 'ct-condition',
		'condition' => [
			$prefix . 'hero_section' => 'type-2'
		],
		'options' => [

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ $prefix . 'page_title_bg_type' => '!color' ],
				'options' => [

					$prefix . 'pageTitleOverlay' => [
						'label' => __( 'Image Overlay Color', 'blocksy' ),
						'type' => 'ct-background',
						'design' => 'inline',
						'sync' => 'live',
						'divider' => 'top:full',
						'has_no_color' => true,
						'default_inherit_color' => 'rgba(18, 21, 25, 0.5)',
						'activeTabs' => ['color', 'gradient'],
						'value' => blocksy_background_default_value([
							'backgroundColor' => [
								'default' => [
									'color' => 'CT_CSS_SKIP_RULE',
								],
							],
						])
					],
				],
			],

			$prefix . 'pageTitleBackground' => [
				'label' => __( 'Container Background', 'blocksy' ),
				'type' => 'ct-background',
				'design' => 'inline',
				'sync' => 'live',
				'divider' => 'top:full',
				'value' => blocksy_background_default_value([
					'backgroundColor' => [
						'default' => [
							'color' => 'var(--paletteColor6)'
						],
					],
				])
			],

			$prefix . 'pageTitlePadding' => [
				'label' => __( 'Container Padding', 'blocksy' ),
				'type' => 'ct-spacing',
				'divider' => 'top:full',
				'setting' => [ 'transport' => 'postMessage' ],
				'value' => blocksy_spacing_value([
					'top' => '50px',
					'left' => 'auto',
					'right' => 'auto',
					'bottom' => '50px',
					'linked' => true,
				]),
				'responsive' => true
			],

		],
	],
];

$when_enabled_settings = [
	blocksy_rand_md5() => [
		'title' => __( 'General', 'blocksy' ),
		'type' => 'tab',
		'options' => $when_enabled_general_settings
	],

	blocksy_rand_md5() => [
		'title' => __( 'Design', 'blocksy' ),
		'type' => 'tab',
		'options' => $when_enabled_design_settings
	],
];

$options_when_not_default = [
	$prefix . 'hero_enabled' => [
		'label' => $enabled_label,
		'type' => 'ct-panel',
		'switch' => true,
		'value' => $enabled_default,
		'wrapperAttr' => ['data-label' => 'heading-label'],
		'sync' => blocksy_sync_whole_page([
			'prefix' => $prefix,
			'prefix_custom' => 'hero'
		]),
		'inner-options' => $when_enabled_settings
	]
];

// options output for posts/pages
$options_when_default = [
	$prefix . 'has_hero_section' => [
		// 'label' => $is_page ? __('Page Title', 'blocksy') : __(
		// 	'Post Title', 'blocksy'
		// ),
		'label' => false,
		'type' => 'ct-radio',
		'value' => 'default',
		'view' => 'text',
		'disableRevertButton' => true,
		'design' => $is_single ? 'block' : 'inline',
		'wrapperAttr' => [ 'data-spacing' => 'custom' ],
		'choices' => [
			'default' => __( 'Inherit', 'blocksy' ),
			'enabled' => __( 'Custom', 'blocksy' ),
			'disabled' => __( 'Disabled', 'blocksy' ),
		],
	],

	blocksy_rand_md5() => [
		'type' => 'ct-condition',
		'condition' => [ $prefix . 'has_hero_section' => 'default' ],
		'options' => [
			blocksy_rand_md5() => [
				'type' => 'ct-notification',
				'attr' => [ 'data-spacing' => 'custom' ],
				'text' => __( 'By default these options are inherited from Customizer options.', 'blocksy' ),
			],
		],
	],

	blocksy_rand_md5() => [
		'type' => 'ct-condition',
		'condition' => [$prefix . 'has_hero_section' => 'enabled'],
		'options' => $when_enabled_settings
	],
];

// options output for taxonomies
if (! $is_single) {
	$options_when_default = [
		blocksy_rand_md5() => [
			'title' => __( 'General', 'blocksy' ),
			'type' => 'tab',
			'options' => [
				$prefix . 'has_hero_section' => [
					'label' => __('Page Title', 'blocksy'),
					'type' => 'ct-radio',
					'value' => 'default',
					'view' => 'text',
					'disableRevertButton' => true,
					'design' => $is_single ? 'block' : 'inline',
					'wrapperAttr' => [ 'data-spacing' => 'custom' ],
					'choices' => [
						'default' => __( 'Inherit', 'blocksy' ),
						'enabled' => __( 'Custom', 'blocksy' ),
						'disabled' => __( 'Disabled', 'blocksy' ),
					],
				],

				blocksy_rand_md5() => [
					'type' => 'ct-condition',
					'condition' => [$prefix . 'has_hero_section' => 'enabled'],
					'options' => $when_enabled_general_settings
				],
			]
		],

		blocksy_rand_md5() => [
			'title' => __( 'Design', 'blocksy' ),
			'type' => 'tab',
			'options' => [

				blocksy_rand_md5() => [
					'type' => 'ct-condition',
					'condition' => [$prefix . 'has_hero_section' => 'enabled'],
					'options' => $when_enabled_design_settings
				],

				blocksy_rand_md5() => [
					'type' => 'ct-condition',
					'condition' => [$prefix . 'has_hero_section' => '!enabled'],
					'options' => [
						blocksy_rand_md5() => [
							'type' => 'ct-notification',
							'attr' => ['data-label' => 'no-label'],
							'text' => __('Options will appear here only if you will set Custom in Page Title option.', 'blocksy')
						]
					]
				],
			]
		],
	];
}

$options = $has_default ? $options_when_default : $options_when_not_default;

