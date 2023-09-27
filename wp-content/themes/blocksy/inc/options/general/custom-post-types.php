<?php

$custom_post_types = blocksy_manager()->post_types->get_supported_post_types();

$options = [];

if (function_exists('is_bbpress')) {
	$options[blocksy_rand_md5()] = [
		'type' => 'ct-group-title',
		'kind' => 'divider',
		'priority' => 2.5,
	];

	$options['post_type_single_bbpress'] = [
		'title' => __('bbPress', 'blocksy'),
		'container' => ['priority' => 2.5],
		'options' => blocksy_get_options('posts/custom-post-type-single', [
			'post_type' => (object) [
				'name' => 'bbpress',
				'labels' => (object) [
					'singular_name' => __('bbPress', 'blocksy')
				]
			],

			'is_bbpress' => true
		]),
	];
}

if (function_exists('is_buddypress')) {
	$options[blocksy_rand_md5()] = [
		'type' => 'ct-group-title',
		'kind' => 'divider',
		'priority' => 2.5,
	];

	$options['post_type_single_buddypress'] = [
		'title' => __('BuddyPress', 'blocksy'),
		'container' => ['priority' => 2.5],
		'options' => blocksy_get_options('posts/custom-post-type-single', [
			'post_type' => (object) [
				'name' => 'buddypress',
				'labels' => (object) [
					'singular_name' => __('BuddyPress', 'blocksy')
				]
			],

			'is_bbpress' => true
		]),
	];
}

if (function_exists('tutor_course_enrolled_lead_info')) {

	$options[blocksy_rand_md5()] = [
		'type' => 'ct-group-title',
		'title' => __( 'Tutor LMS', 'blocksy' ),
		'priority' => 2.5,
	];

	$options['post_type_archive_tutorlms'] = [
		'title' => __('Course Archive', 'blocksy'),
		'container' => ['priority' => 2.5],
		'options' => blocksy_get_options('integrations/tutorlms-archive', []),
	];

	$options['post_type_single_tutorlms'] = [
		'title' => __('Course Single', 'blocksy'),
		'container' => ['priority' => 2.5],
		'options' => blocksy_get_options('integrations/tutorlms-single', []),
	];
}

foreach ($custom_post_types as $post_type) {
	$post_type_object = get_post_type_object($post_type);

	if (! $post_type_object) {
		continue;
	}


	$cpt_archive = apply_filters(
		'blocksy:custom_post_types:archive-options',
		[
			'title' => $post_type_object->labels->name,
			'container' => ['priority' => 2.5],
			'options' => blocksy_get_options('posts/custom-post-type-archive', [
				'post_type' => $post_type_object
			]),
		],

		$post_type,
		$post_type_object
	);

	$cpt_single = apply_filters(
		'blocksy:custom_post_types:single-options',
		[
			'title' => sprintf(
				__('Single %s', 'blocksy'),
				$post_type_object->labels->singular_name
			),
			'container' => ['priority' => 2.5, 'type' => 'child'],
			'options' => blocksy_get_options('posts/custom-post-type-single', [
				'post_type' => $post_type_object,
				'is_general_cpt' => true
			]),
		],
		$post_type,
		$post_type_object
	);

	if (
		$post_type === 'sfwd-courses'
		||
		$post_type === 'sfwd-topic'
		||
		$post_type === 'sfwd-lessons'
		||
		$post_type === 'sfwd-quiz'
	) {
		if (! isset($options['learndash_posts_archives'])) {
			$options[blocksy_rand_md5()] = [
				'type' => 'ct-group-title',
				'kind' => 'divider',
				'priority' => 2.48,
			];

			$options['learndash_posts_archives'] = [
				'title' => __( 'LearnDash', 'blocksy' ),
				'container' => [
					'priority' => 2.49
				],
				// 'only_if_exists' => true,
				'options' => []
			];
		}

		$options['learndash_posts_archives']['options'][
			'post_type_archive_' . $post_type
		] = $cpt_archive;

		$options['learndash_posts_archives']['options'][
			'post_type_single_' . $post_type
		] = $cpt_single;

		continue;
	}

	if (
		$post_type === 'courses'
		&&
		function_exists('tutor_course_enrolled_lead_info')
	) {
		continue;
	}

	$options[blocksy_rand_md5()] = [
		'type' => 'ct-group-title',
		'kind' => 'divider',
		'priority' => 2.5,
	];

	$options['post_type_archive_' . $post_type] = $cpt_archive;
	$options['post_type_single_' . $post_type] = $cpt_single;
}


