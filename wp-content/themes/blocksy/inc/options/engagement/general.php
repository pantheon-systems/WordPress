<?php

$options = [
	'general_visitor_eng_section_options' => [
		'label' => __( 'Visitor Engagement', 'blocksy' ),
		'type' => 'ct-panel',
		'setting' => [ 'transport' => 'postMessage' ],
		'inner-options' => [
			apply_filters(
				'blocksy_engagement_general_start_customizer_options',
				[]
			),

			[
				blocksy_rand_md5() => [
					'type' => 'ct-divider',
				],

				'enable_schema_org_markup' => [
					'label' => __( 'Schema.org Markup', 'blocksy' ),
					'type' => 'ct-switch',
					'value' => 'yes',
					'desc' => __( 'Enable Schema.org markup features for your website. You can disable this option if you use a SEO plugin and let it do the work.', 'blocksy' ),
				],
			],

			apply_filters(
				'blocksy_engagement_general_end_customizer_options',
				[]
			),
		],
	],
];