<?php

$options = [
	'tutorlms_archive_course_options' => [
		'type' => 'ct-options',
		'inner-options' => [
			blocksy_get_options('general/page-title', [
				'prefix' => 'courses_archive',
				'is_single' => true,
				'is_cpt' => true,
				'enabled_label' => sprintf(
					__('%s Title', 'blocksy'),
					'Courses'
				),
				'is_tutorlms' => true,
			]),

			blocksy_rand_md5() => [
				'type' => 'ct-title',
				'label' => __( 'Course Structure', 'blocksy' ),
			],

			blocksy_rand_md5() => [
				'title' => __( 'General', 'blocksy' ),
				'type' => 'tab',
				'options' => [

					blocksy_get_options('single-elements/structure', [
						'default_structure' => 'type-4',
						'prefix' => 'courses_archive',
						'has_content_style' => false,
						'has_v_spacing' => false,
					]),

				],
			],

			blocksy_rand_md5() => [
				'title' => __( 'Design', 'blocksy' ),
				'type' => 'tab',
				'options' => [

					blocksy_get_options('single-elements/structure-design', [
						'prefix' => 'courses_archive',
						'options_conditions' => [
							'courses_archive_content_style' => 'nonexistent'
						]
					])

				],
			],

		]
	]
];

