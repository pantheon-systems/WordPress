<?php

$options = [
	blocksy_rand_md5() => [
		'title' => __( 'General', 'blocksy' ),
		'type' => 'tab',
		'options' => [

			'copyright_text' => [
				'label' => __( 'Copyright Text', 'blocksy' ),
				'type' => 'wp-editor',
				'value' => apply_filters(
					'blocksy:footer:copyright:default-value',
					__('Copyright &copy; {current_year} - WordPress Theme by {theme_author}', 'blocksy')
				),
				'desc' => __( 'You can insert some arbitrary HTML code tags: {current_year}, {site_title} and {theme_author}', 'blocksy' ),
				'disableRevertButton' => true,
				'setting' => [ 'transport' => 'postMessage' ],
				'mediaButtons' => false,
				'tinymce' => [
					'toolbar1' => 'bold,italic,link,undo,redo',
				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			'footerCopyrightAlignment' => [
				'type' => 'ct-radio',
				'label' => __( 'Horizontal Alignment', 'blocksy' ),
				'view' => 'text',
				'design' => 'block',
				'responsive' => true,
				'attr' => [ 'data-type' => 'alignment' ],
				'setting' => [ 'transport' => 'postMessage' ],
				'value' => 'CT_CSS_SKIP_RULE',
				'choices' => [
					'left' => '',
					'center' => '',
					'right' => '',
				],
			],

			'footerCopyrightVerticalAlignment' => [
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

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			'footer_copyright_visibility' => [
				'label' => __( 'Element Visibility', 'blocksy' ),
				'type' => 'ct-visibility',
				'design' => 'block',
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

			'copyrightFont' => [
				'type' => 'ct-typography',
				'label' => __( 'Font', 'blocksy' ),
				'value' => blocksy_typography_default_values([
					'size' => '15px',
					'variation' => 'n4',
					'line-height' => '1.3',
				]),
				'setting' => [ 'transport' => 'postMessage' ],
			],

			'copyrightColor' => [
				'label' => __( 'Font Color', 'blocksy' ),
				'type'  => 'ct-color-picker',
				'design' => 'block:right',
				'responsive' => true,
				'setting' => [ 'transport' => 'postMessage' ],

				'value' => [
					'default' => [
						'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
					],

					'link_initial' => [
						'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
					],

					'link_hover' => [
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
						'title' => __( 'Link Initial', 'blocksy' ),
						'id' => 'link_initial',
						'inherit' => 'var(--linkInitialColor)'
					],

					[
						'title' => __( 'Link Hover', 'blocksy' ),
						'id' => 'link_hover',
						'inherit' => 'var(--linkHoverColor)'
					],
				],
			],

			'copyrightMargin' => [
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
