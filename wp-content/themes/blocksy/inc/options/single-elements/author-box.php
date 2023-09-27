<?php

if (! isset($prefix)) {
	$prefix = '';
} else {
	$prefix = $prefix . '_';
}

$options = [
	$prefix . 'has_author_box' => [
		'label' => __( 'Author Box', 'blocksy' ),
		'type' => 'ct-panel',
		'switch' => true,
		'value' => 'no',
		'sync' => blocksy_sync_single_post_container([
			'prefix' => $prefix
		]),
		'inner-options' => [

			blocksy_rand_md5() => [
				'title' => __( 'General', 'blocksy' ),
				'type' => 'tab',
				'options' => [

					$prefix . 'single_author_box_type' => [
						'label' => __('Box Type', 'blocksy'),
						'type' => 'ct-image-picker',
						'value' => 'type-2',
						'attr' => ['data-type' => 'background'],
						'sync' => [
							'prefix' => $prefix,
							'selector' => '.author-box',
							'render' => function () {
								if (have_posts()) {
									the_post();
								}

								blocksy_author_box();
							}
						],
						'choices' => [
							'type-1' => [
								'src' => blocksy_image_picker_url('author-box-type-1.svg'),
								'title' => __('Type 1', 'blocksy'),
							],

							'type-2' => [
								'src' => blocksy_image_picker_url('author-box-type-2.svg'),
								'title' => __('Type 2', 'blocksy'),
							],
						],
					],

					$prefix . 'single_author_box_name_heading' => [
						'label' => __( 'Author Name Tag', 'blocksy' ),
						'type' => 'ct-select',
						'value' => 'h5',
						'view' => 'text',
						'design' => 'inline',
						'divider' => 'top',
						'choices' => blocksy_ordered_keys(
							[
								'h1' => 'H1',
								'h2' => 'H2',
								'h3' => 'H3',
								'h4' => 'H4',
								'h5' => 'H5',
								'h6' => 'H6',
								'p' => 'p',
								'div' => 'div',
							]
						),
						'sync' => [
							'prefix' => $prefix,
							'selector' => '.author-box',
							'loader_selector' => '.author-box-name',
							'render' => function () {
								if (have_posts()) {
									the_post();
								}

								blocksy_author_box();
							}
						],
					],

					$prefix . 'single_author_box_posts_count' => [
						'label' => __( 'Posts Count', 'blocksy' ),
						'type' => 'ct-switch',
						'value' => 'yes',
						'divider' => 'top',
						'sync' => [
							'prefix' => $prefix,
							'selector' => '.author-box',
							'render' => function () {
								if (have_posts()) {
									the_post();
								}

								blocksy_author_box();
							}
						],
					],

					$prefix . 'single_author_box_social' => [
						'label' => __( 'Social Icons', 'blocksy' ),
						'type' => 'ct-switch',
						'value' => 'yes',
						'desc' => sprintf(
							// translators: placeholder here is the link URL.
							__(
								'You can set the author social channels %shere%s.',
								'blocksy'
							),
							sprintf(
								'<a href="%s" target="_blank">',
								admin_url('/profile.php')
							),
							'</a>'
						),
						'divider' => 'top',
						'sync' => [
							'prefix' => $prefix,
							'selector' => '.author-box',
							'render' => function () {
								if (have_posts()) {
									the_post();
								}

								blocksy_author_box();
							}
						],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [ $prefix . 'single_author_box_social' => 'yes' ],
						'options' => [

							$prefix . 'single_author_box_social_link_target' => [
								'type'  => 'ct-switch',
								'label' => __( 'Open links in new tab', 'blocksy' ),
								'value' => 'no',
								'sync' => [
									'prefix' => $prefix,
									'selector' => '.author-box',
									'render' => function () {
										if (have_posts()) {
											the_post();
										}

										blocksy_author_box();
									}
								],
							],

						],
					],

					$prefix . 'single_author_box_spacing' => [
						'label' => __( 'Container Inner Spacing', 'blocksy' ),
						'type' => 'ct-slider',
						'value' => '40px',
						'units' => blocksy_units_config([
							[
								'unit' => 'px',
								'min' => 0,
								'max' => 100,
							],
						]),
						'responsive' => true,
						'divider' => 'top',
						'sync' => 'live'
					],

					blocksy_rand_md5() => [
						'type' => 'ct-divider',
					],

					$prefix . 'author_box_visibility' => [
						'label' => __( 'Visibility', 'blocksy' ),
						'type' => 'ct-visibility',
						'design' => 'block',
						'sync' => 'live',

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
					],

				],
			],

			blocksy_rand_md5() => [
				'title' => __( 'Design', 'blocksy' ),
				'type' => 'tab',
				'options' => [

					$prefix . 'single_author_box_name_font' => [
						'type' => 'ct-typography',
						'label' => __( 'Author Name Font', 'blocksy' ),
						'sync' => 'live',
						'value' => blocksy_typography_default_values([]),
					],

					$prefix . 'single_author_box_name_color' => [
						'label' => __( 'Author Name Font Color', 'blocksy' ),
						'type'  => 'ct-color-picker',
						'design' => 'block:right',
						'responsive' => true,
						'divider' => 'bottom',
						'setting' => [ 'transport' => 'postMessage' ],
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
										$prefix . 'single_author_box_name_heading' => 'h1'
									],

									'var(--heading-2-color, var(--headings-color))' => [
										$prefix . 'single_author_box_name_heading' => 'h2'
									],

									'var(--heading-3-color, var(--headings-color))' => [
										$prefix . 'single_author_box_name_heading' => 'h3'
									],

									'var(--heading-4-color, var(--headings-color))' => [
										$prefix . 'single_author_box_name_heading' => 'h4'
									],

									'var(--heading-5-color, var(--headings-color))' => [
										$prefix . 'single_author_box_name_heading' => 'h5'
									],

									'var(--heading-6-color, var(--headings-color))' => [
										$prefix . 'single_author_box_name_heading' => 'h6'
									]
								]
							],
						],
					],

					$prefix . 'single_author_box_font' => [
						'type' => 'ct-typography',
						'label' => __( 'Author Bio Font', 'blocksy' ),
						'sync' => 'live',
						'value' => blocksy_typography_default_values([]),
					],

					$prefix . 'single_author_box_font_color' => [
						'label' => __( 'Author Bio Font Color', 'blocksy' ),
						'type'  => 'ct-color-picker',
						'design' => 'block:right',
						'responsive' => true,
						'divider' => 'bottom',
						'setting' => [ 'transport' => 'postMessage' ],
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

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [ $prefix . 'single_author_box_social' => 'yes' ],
						'options' => [

							$prefix . 'single_author_box_social_icons_color' => [
								'label' => __( 'Icons Color', 'blocksy' ),
								'type'  => 'ct-color-picker',
								'design' => 'block:right',
								'responsive' => true,
								'divider' => 'bottom',
								'setting' => [ 'transport' => 'postMessage' ],

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
										'inherit' => '#fff'
									],

									[
										'title' => __( 'Hover', 'blocksy' ),
										'id' => 'hover',
										'inherit' => '#fff'
									],
								],
							],

							$prefix . 'single_author_box_social_icons_background' => [
								'label' => __( 'Icons Background Color', 'blocksy' ),
								'type'  => 'ct-color-picker',
								'design' => 'block:right',
								'divider' => 'bottom',
								'responsive' => true,
								'setting' => [ 'transport' => 'postMessage' ],

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
										'inherit' => 'var(--paletteColor1)'
									],

									[
										'title' => __( 'Hover', 'blocksy' ),
										'id' => 'hover',
										'inherit' => 'var(--paletteColor2)'
									],
								],
							],

						],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [
							$prefix . 'single_author_box_type' => 'type-2'
						],
						'options' => [

							$prefix . 'single_author_box_border' => [
								'label' => __( 'Border Color', 'blocksy' ),
								'type'  => 'ct-color-picker',
								'design' => 'block:right',
								'divider' => 'bottom',
								'responsive' => true,
								'setting' => [ 'transport' => 'postMessage' ],

								'value' => [
									'default' => [
										'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
									],
								],

								'pickers' => [
									[
										'title' => __( 'Initial', 'blocksy' ),
										'id' => 'default',
										'inherit' => '#e8ebf0'
									],
								],
							],

						],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [
							$prefix . 'single_author_box_type' => 'type-1'
						],
						'options' => [

							$prefix . 'single_author_box_container_background' => [
								'label' => __( 'Background Color', 'blocksy' ),
								'type'  => 'ct-background',
								'design' => 'block:right',
								'responsive' => true,
								// 'divider' => 'bottom',
								'activeTabs' => ['color', 'gradient'],
								'sync' => 'live',
								'value' => blocksy_background_default_value([
									'backgroundColor' => [
										'default' => [
											'color' => '#ffffff',
										],
									],
								]),
							],

							$prefix . 'single_author_box_shadow' => [
								'label' => __( 'Shadow', 'blocksy' ),
								'type' => 'ct-box-shadow',
								'responsive' => true,
								'divider' => 'top',
								'sync' => 'live',
								'value' => blocksy_box_shadow_value([
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
							],

							$prefix . 'single_author_box_container_border' => [
								'label' => __( 'Border', 'blocksy' ),
								'type' => 'ct-border',
								'design' => 'block',
								'sync' => 'live',
								'divider' => 'top',
								'responsive' => true,
								'value' => [
									'width' => 1,
									'style' => 'none',
									'color' => [
										'color' => 'rgba(44,62,80,0.2)',
									],
								]
							],

							$prefix . 'single_author_box_border_radius' => [
								'label' => __( 'Border Radius', 'blocksy' ),
								'sync' => 'live',
								'type' => 'ct-spacing',
								'divider' => 'top',
								'value' => blocksy_spacing_value([
									'linked' => true,
								]),
								'responsive' => true
							],
						],
					],
				],
			],
		],
	],
];

