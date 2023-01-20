<?php

$inner_options = [
	blocksy_get_options('general/page-title', [
		'prefix' => $post_type->name . '_archive',
		'is_cpt' => true,
		'is_archive' => true,
		'enabled_label' => sprintf(
			__('%s Title', 'blocksy'),
			$post_type->labels->name
		),
	]),

	blocksy_get_options('general/posts-listing', [
		'prefix' => $post_type->name . '_archive',
		'title' => $post_type->labels->name,
		'is_cpt' => true
	]),

	[
		blocksy_rand_md5() => [
			'type' => 'ct-title',
			'label' => __( 'Page Elements', 'blocksy' ),
		],
	],

	blocksy_get_options('general/sidebar-particular', [
		'prefix' => $post_type->name . '_archive',
	]),

	blocksy_get_options('general/pagination', [
		'prefix' => $post_type->name . '_archive',
	]),

	[
		blocksy_rand_md5() => [
			'type' => 'ct-title',
			'label' => __('Functionality Options', 'blocksy'),
		],
	],

	apply_filters(
		'blocksy_posts_home_page_elements_end',
		[],
		$post_type->name . '_archive',
		$post_type->name
	),

	blocksy_get_options('general/cards-reveal-effect', [
		'prefix' => $post_type->name . '_archive',
	]),
];

$options = [
	$post_type->name . '_section_options' => [
		'type' => 'ct-options',
		'inner-options' => $inner_options
	],
];
