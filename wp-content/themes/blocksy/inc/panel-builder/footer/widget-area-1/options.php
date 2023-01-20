<?php

if (! isset($sidebarId)) {
	$sidebarId = 'ct-footer-sidebar-1';
}

$options = [
	blocksy_rand_md5() => [
		'title' => __( 'General', 'blocksy' ),
		'type' => 'tab',
		'options' => [
			'widget' => [
				'type' => 'ct-widget-area',
				'sidebarId' => $sidebarId
			],

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			'horizontal_alignment' => [
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

			'vertical_alignment' => [
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

	blocksy_rand_md5() => [
		'title' => __( 'Design', 'blocksy' ),
		'type' => 'tab',
		'options' => [

			'widget_area_colors' => [
				'label' => __( 'Font Color', 'blocksy' ),
				'type'  => 'ct-color-picker',
				'design' => 'block:right',
				'responsive' => true,
				'setting' => [ 'transport' => 'postMessage' ],

				'value' => [
					'default' => [
						'color' => Blocksy_Css_Injector::get_skip_rule_keyword(),
					],

					'link_initial' => [
						'color' => Blocksy_Css_Injector::get_skip_rule_keyword(),
					],

					'link_hover' => [
						'color' => Blocksy_Css_Injector::get_skip_rule_keyword(),
					],
				],

				'pickers' => [
					[
						'title' => __( 'Initial', 'blocksy' ),
						'id' => 'default',
					],

					[
						'title' => __( 'Link Initial', 'blocksy' ),
						'id' => 'link_initial',
					],

					[
						'title' => __( 'Link Hover', 'blocksy' ),
						'id' => 'link_hover',
					],
				],
			],

			'widgets_link_type' => [
				'type' => 'ct-radio',
				'label' => __( 'Links Decoration', 'blocksy' ),
				'view' => 'text',
				'design' => 'block',
				'divider' => 'top:full',
				'setting' => [ 'transport' => 'postMessage' ],
				'value' => 'inherit',
				'choices' => [
					'none' => __( 'None', 'blocksy' ),
					'inherit' => __( 'Inherit', 'blocksy' ),
					'underline' => __( 'Underline', 'blocksy' ),
				],
			],

			'widget_area_margin' => [
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
