<?php

if (get_theme_mod($prefix . '_has_share_box', 'no') === 'yes') {
	$share_box_icon_size = get_theme_mod($prefix . '_share_box_icon_size', 15);
	$share_box_type = get_theme_mod($prefix. '_share_box_type', 'type-1');

	if ($share_box_icon_size !== 15) {
		blocksy_output_responsive([
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,
			'selector' => blocksy_prefix_selector('.ct-share-box', $prefix),
			'variableName' => 'icon-size',
			'value' => $share_box_icon_size
		]);
	}

	$share_box_icons_spacing = get_theme_mod($prefix . '_share_box_icons_spacing', 10);

	if ($share_box_icons_spacing !== 10 && $share_box_type !== 'type-1') {
		blocksy_output_responsive([
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,
			'selector' => blocksy_prefix_selector('.ct-share-box', $prefix),
			'variableName' => 'spacing',
			'value' => $share_box_icons_spacing
		]);
	}

	$top_share_box_spacing = get_theme_mod($prefix . '_top_share_box_spacing', '50px');
	if ($top_share_box_spacing !== '50px') {
		blocksy_output_responsive([
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,
			'selector' => blocksy_prefix_selector('.ct-share-box[data-location="top"]', $prefix),
			'variableName' => 'margin',
			'value' => $top_share_box_spacing,
			'unit' => ''
		]);
	}

	$bottom_share_box_spacing = get_theme_mod($prefix . '_bottom_share_box_spacing', '50px');
	if ($bottom_share_box_spacing !== '50px') {
		blocksy_output_responsive([
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,
			'selector' => blocksy_prefix_selector('.ct-share-box[data-location="bottom"]', $prefix),
			'variableName' => 'margin',
			'value' => $bottom_share_box_spacing,
			'unit' => ''
		]);
	}


	if ($share_box_type === 'type-1') {
		blocksy_output_colors([
			'value' => get_theme_mod($prefix . '_share_items_icon_color', []),
			'default' => [
				'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
				'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			],
			'css' => $css,
			'variables' => [
				'default' => [
					'selector' => blocksy_prefix_selector('.ct-share-box[data-type="type-1"]', $prefix),
					'variable' => 'icon-color'
				],

				'hover' => [
					'selector' => blocksy_prefix_selector('.ct-share-box[data-type="type-1"]', $prefix),
					'variable' => 'icon-hover-color'
				],
			],
		]);

		blocksy_output_border([
			'css' => $css,
			'selector' => blocksy_prefix_selector('.ct-share-box[data-type="type-1"]', $prefix),
			'variableName' => 'border',
			'value' => get_theme_mod($prefix . '_share_items_border'),
			'default' => [
				'width' => 1,
				'style' => 'solid',
				'color' => [
					'color' => 'var(--border-color)',
				],
			]
		]);
	}


	if ($share_box_type === 'type-2') {

		$text_share_box_alignment = get_theme_mod($prefix . '_share_box_alignment', 'CT_CSS_SKIP_RULE');

		$share_box_alignment = blocksy_map_values([
			'value' => $text_share_box_alignment,
			'map' => [
				'left' => 'flex-start',
				'right' => 'flex-end'
			]
		]);

		blocksy_output_responsive([
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,
			'selector' => blocksy_prefix_selector('.ct-share-box[data-type="type-2"]', $prefix),
			'variableName' => 'horizontal-alignment',
			'value' => $share_box_alignment,
			'unit' => '',
		]);

		blocksy_output_responsive([
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,
			'selector' => blocksy_prefix_selector('.ct-share-box[data-type="type-2"]', $prefix),
			'variableName' => 'text-horizontal-alignment',
			'value' => $text_share_box_alignment,
			'unit' => '',
		]);
	}


	$share_box2_colors = get_theme_mod($prefix. '_share_box2_colors', 'custom');

	if ($share_box_type === 'type-2' && $share_box2_colors === 'custom') {
		blocksy_output_colors([
			'value' => get_theme_mod(
				$prefix . '_share_items_icon',
				[]
			),
			'default' => [
				'default' => [ 'color' => '#ffffff' ],
				'hover' => [ 'color' => '#ffffff' ],
			],
			'css' => $css,
			'variables' => [
				'default' => [
					'selector' => blocksy_prefix_selector('.ct-share-box[data-type="type-2"]', $prefix),
					'variable' => 'icon-color'
				],

				'hover' => [
					'selector' => blocksy_prefix_selector('.ct-share-box[data-type="type-2"]', $prefix),
					'variable' => 'icon-hover-color'
				],
			],
		]);

		blocksy_output_colors([
			'value' => get_theme_mod($prefix . '_share_items_background', []),
			'default' => [
				'default' => [ 'color' => 'var(--paletteColor1)' ],
				'hover' => [ 'color' => 'var(--paletteColor2)' ],
			],
			'css' => $css,
			'variables' => [
				'default' => [
					'selector' => blocksy_prefix_selector('.ct-share-box[data-type="type-2"]', $prefix),
					'variable' => 'background-color'
				],

				'hover' => [
					'selector' => blocksy_prefix_selector('.ct-share-box[data-type="type-2"]', $prefix),
					'variable' => 'background-hover-color'
				]
			],
		]);
	}
}


// Author Box
if (
	get_theme_mod($prefix . '_has_author_box', 'no') === 'yes'
	&&
	$prefix !== 'single_page'
) {

	$author_box_spacing = get_theme_mod($prefix. '_single_author_box_spacing', '40px');

	if ($author_box_spacing !== '40px') {
		blocksy_output_responsive([
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,
			'selector' => blocksy_prefix_selector('.author-box', $prefix),
			'variableName' => 'spacing',
			'value' => $author_box_spacing,
			'unit' => ''
		]);
	}

	blocksy_output_font_css([
		'font_value' => get_theme_mod(
			$prefix . '_single_author_box_name_font',
			blocksy_typography_default_values([])
		),
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => blocksy_prefix_selector('.author-box .author-box-name', $prefix),
	]);

	blocksy_output_colors([
		'value' => get_theme_mod($prefix . '_single_author_box_name_color'),
		'default' => [
			'default' => ['color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,

		'variables' => [
			'default' => [
				'selector' => blocksy_prefix_selector('.author-box .author-box-name', $prefix),
				'variable' => 'heading-color'
			],
		],

		'responsive' => true
	]);

	blocksy_output_font_css([
		'font_value' => get_theme_mod(
			$prefix . '_single_author_box_font',
			blocksy_typography_default_values([])
		),
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => blocksy_prefix_selector('.author-box .author-box-bio', $prefix),
	]);

	blocksy_output_colors([
		'value' => get_theme_mod($prefix . '_single_author_box_font_color', []),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			'initial' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,

		'variables' => [
			'default' => [
				'selector' => blocksy_prefix_selector('.author-box .author-box-bio', $prefix),
				'variable' => 'color'
			],

			'initial' => [
				'selector' => blocksy_prefix_selector('.author-box .author-box-bio', $prefix),
				'variable' => 'linkInitialColor'
			],

			'hover' => [
				'selector' => blocksy_prefix_selector('.author-box .author-box-bio', $prefix),
				'variable' => 'linkHoverColor'
			],
		],

		'responsive' => true
	]);

	blocksy_output_colors([
		'value' => get_theme_mod($prefix . '_single_author_box_social_icons_color', []),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,

		'variables' => [
			'default' => [
				'selector' => blocksy_prefix_selector('.author-box .author-box-social', $prefix),
				'variable' => 'icon-color'
			],

			'hover' => [
				'selector' => blocksy_prefix_selector('.author-box .author-box-social', $prefix),
				'variable' => 'icon-hover-color'
			]
		],

		'responsive' => true
	]);

	blocksy_output_colors([
		'value' => get_theme_mod($prefix . '_single_author_box_social_icons_background', []),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,

		'variables' => [
			'default' => [
				'selector' => blocksy_prefix_selector('.author-box .author-box-social', $prefix),
				'variable' => 'background-color'
			],

			'hover' => [
				'selector' => blocksy_prefix_selector('.author-box .author-box-social', $prefix),
				'variable' => 'background-hover-color'
			]
		],

		'responsive' => true
	]);




	$author_box_type = get_theme_mod($prefix. '_single_author_box_type', 'type-2');

	if ($author_box_type === 'type-1') {

		blocksy_output_background_css([
			'selector' => blocksy_prefix_selector('.author-box[data-type="type-1"]', $prefix),
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,
			'value' => get_theme_mod(
				$prefix . '_single_author_box_container_background',
				blocksy_background_default_value([
					'backgroundColor' => [
						'default' => [
							'color' => '#ffffff'
						],
					],
				])
			),
			'responsive' => true,
		]);

		blocksy_output_box_shadow([
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,
			'selector' => blocksy_prefix_selector('.author-box[data-type="type-1"]', $prefix),
			'value' => get_theme_mod(
				$prefix . '_single_author_box_shadow',
				blocksy_box_shadow_value([
					'enable' => true,
					'h_offset' => 0,
					'v_offset' => 50,
					'blur' => 90,
					'spread' => 0,
					'inset' => false,
					'color' => [
						'color' => 'rgba(210, 213, 218, 0.4)',
					],
				])
			),
			'responsive' => true
		]);

		blocksy_output_border([
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,
			'selector' => blocksy_prefix_selector('.author-box[data-type="type-1"]', $prefix),
			'variableName' => 'border',
			'value' => get_theme_mod($prefix . '_single_author_box_container_border'),
			'default' => [
				'width' => 1,
				'style' => 'none',
				'color' => [
					'color' => 'rgba(44,62,80,0.2)',
				],
			],
			'responsive' => true,
			// 'skip_none' => true
		]);

		blocksy_output_spacing([
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,
			'selector' => blocksy_prefix_selector('.author-box[data-type="type-1"]', $prefix),
			'property' => 'border-radius',
			'value' => get_theme_mod($prefix . '_single_author_box_border_radius',
				blocksy_spacing_value([
					'linked' => true,
				])
			)
		]);
	}

	if ($author_box_type === 'type-2') {
		blocksy_output_colors([
			'value' => get_theme_mod($prefix . '_single_author_box_border', []),
			'default' => [
				'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			],
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,
			
			'variables' => [
				'default' => [
					'selector' => blocksy_prefix_selector('.author-box[data-type="type-2"]', $prefix),
					'variable' => 'border-color'
				],
			],

			'responsive' => true,
		]);
	}
}

// Posts Navigation
if (
	get_theme_mod($prefix . '_has_post_nav', 'no') === 'yes'
	&&
	$prefix !== 'single_page'
) {

	$post_nav_spacing = get_theme_mod($prefix . '_post_nav_spacing', '50px');

	if ($post_nav_spacing !== '50px') {
		blocksy_output_responsive([
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,
			'selector' => blocksy_prefix_selector('.post-navigation', $prefix),
			'variableName' => 'margin',
			'value' => $post_nav_spacing,
			'unit' => ''
		]);
	}

	blocksy_output_colors([
		'value' => get_theme_mod($prefix . '_posts_nav_font_color', []),
		'default' => [
			'default' => [ 'color' => 'var(--color)' ],
			'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,
		'variables' => [
			'default' => [
				'selector' => blocksy_prefix_selector('.post-navigation', $prefix),
				'variable' => 'linkInitialColor'
			],

			'hover' => [
				'selector' => blocksy_prefix_selector('.post-navigation', $prefix),
				'variable' => 'linkHoverColor'
			],
		],
	]);

	blocksy_output_colors([
		'value' => get_theme_mod($prefix . '_posts_nav_image_overlay_color', []),
		'default' => [
			// 'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,
		'variables' => [
			// 'default' => [
			// 	'selector' => blocksy_prefix_selector('.post-navigation', $prefix),
			// 	'variable' => 'linkInitialColor'
			// ],

			'hover' => [
				'selector' => blocksy_prefix_selector('.post-navigation', $prefix),
				'variable' => 'image-overlay-color'
			],
		],
	]);

	blocksy_output_spacing([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => blocksy_prefix_selector('.post-navigation figure', $prefix),
		'property' => 'border-radius',
		'value' => get_theme_mod($prefix . '_posts_nav_image_border_radius',
			blocksy_spacing_value([
				'linked' => true,
			])
		)
	]);
}


// Related Posts
if (
	get_theme_mod($prefix . '_has_related_posts', 'no') === 'yes'
	&&
	$prefix !== 'single_page'
) {

	$related_posts_container_spacing = get_theme_mod($prefix . '_related_posts_container_spacing', '50px');

	if ($related_posts_container_spacing !== '50px') {
		blocksy_output_responsive([
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,
			'selector' => blocksy_prefix_selector('.ct-related-posts-container', $prefix),
			'variableName' => 'padding',
			'value' => $related_posts_container_spacing,
			'unit' => ''
		]);
	}

	blocksy_output_responsive([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => blocksy_prefix_selector('.ct-related-posts .ct-block-title', $prefix),
		'variableName' => 'horizontal-alignment',
		'value' => get_theme_mod($prefix . '_related_label_alignment', 'CT_CSS_SKIP_RULE'),
		'unit' => '',
	]);

	blocksy_output_background_css([
		'selector' => blocksy_prefix_selector('.ct-related-posts-container', $prefix),
		'css' => $css,
		'value' => get_theme_mod(
			$prefix . '_related_posts_background',
			blocksy_background_default_value([
				'backgroundColor' => [
					'default' => [
						'color' => 'var(--paletteColor6)'
					],
				],
			])
		)
	]);


	blocksy_output_colors([
		'value' => get_theme_mod($prefix . '_related_posts_label_color'),
		'default' => [
			'default' => ['color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,
		'variables' => [
			'default' => [
				'selector' => blocksy_prefix_selector('.ct-related-posts-container .ct-block-title', $prefix),
				'variable' => 'heading-color'
			],
		],
	]);

	blocksy_output_colors([
		'value' => get_theme_mod($prefix . '_related_posts_link_color'),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,
		'variables' => [
			'default' => [
				'selector' => blocksy_prefix_selector('.related-entry-title', $prefix),
				'variable' => 'heading-color'
			],

			'hover' => [
				'selector' => blocksy_prefix_selector('.related-entry-title', $prefix),
				'variable' => 'linkHoverColor'
			],
		],
	]);

	blocksy_output_colors([
		'value' => get_theme_mod($prefix . '_related_posts_meta_color'),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,
		'variables' => [
			'default' => [
				'selector' => blocksy_prefix_selector('.ct-related-posts .entry-meta', $prefix),
				'variable' => 'color'
			],

			'hover' => [
				'selector' => blocksy_prefix_selector('.ct-related-posts .entry-meta', $prefix),
				'variable' => 'linkHoverColor'
			],
		],
	]);

	blocksy_output_spacing([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => blocksy_prefix_selector('.ct-related-posts .ct-image-container', $prefix),
		'property' => 'borderRadius',
		'value' => get_theme_mod($prefix . '_related_thumb_radius',
			blocksy_spacing_value([
				'linked' => true,
			])
		)
	]);


	$relatedNarrowWidth = get_theme_mod($prefix . '_related_narrow_width', 750 );

	if ($relatedNarrowWidth !== 750) {
		$css->put(
			blocksy_prefix_selector('.ct-related-posts-container', $prefix),
			'--narrow-container-max-width: ' . $relatedNarrowWidth . 'px'
		);
	}

	$grid_columns = blocksy_expand_responsive_value(get_theme_mod(
		$prefix . '_related_posts_columns',
		[
			'desktop' => 3,
			'tablet' => 2,
			'mobile' => 1
		]
	));

	$columns_for_output = [
		'desktop' => 'repeat(' . $grid_columns['desktop'] . ', 1fr)',
		'tablet' => 'repeat(' . $grid_columns['tablet'] . ', 1fr)',
		'mobile' => 'repeat(' . $grid_columns['mobile'] . ', 1fr)'
	];

	blocksy_output_responsive([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => blocksy_prefix_selector(
			'.ct-related-posts',
			$prefix
		),
		'variableName' => 'grid-template-columns',
		'value' => $columns_for_output,
		'unit' => ''
	]);
}
