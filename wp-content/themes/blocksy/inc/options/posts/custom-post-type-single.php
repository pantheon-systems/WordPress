<?php

if (! isset($is_general_cpt)) {
    $is_general_cpt = false;
}

if (! isset($is_bbpress)) {
	$is_bbpress = false;
}

$maybe_taxonomy = blocksy_maybe_get_matching_taxonomy($post_type->name, false);

$inner_options = array_merge([
	array_merge(blocksy_get_options(
		'general/page-title',
		apply_filters(
			'blocksy:options:cpt:page-title-args',
			[
				'prefix' => $post_type->name . '_single',
				'is_single' => true,
				'is_bbpress' => $is_bbpress,
				'is_cpt' => true,
				'enabled_label' => sprintf(
					__('%s Title', 'blocksy'),
					$post_type->labels->singular_name
				),
			],
			$post_type->name
		)
	), [
		blocksy_rand_md5() => [
			'type' => 'ct-title',
			'label' => sprintf(
				__('%s Structure', 'blocksy'),
				$post_type->labels->singular_name
			),
		],

		blocksy_rand_md5() => [
			'title' => __( 'General', 'blocksy' ),
			'type' => 'tab',
			'options' => array_merge([
				blocksy_get_options('single-elements/structure', [
					'prefix' => $post_type->name . '_single',
					'default_structure' => 'type-4',
					'default_content_style' => $is_bbpress ? 'boxed' : 'wide'
				])
			]),
		],

		blocksy_rand_md5() => [
			'title' => __( 'Design', 'blocksy' ),
			'type' => 'tab',
			'options' => [
				blocksy_get_options('single-elements/structure-design', [
					'prefix' => $post_type->name . '_single',
				])
			],
		],

	], $is_general_cpt ? [
		blocksy_rand_md5() => [
			'type' => 'ct-title',
			'label' => sprintf(
				__('%s Elements', 'blocksy'),
				$post_type->labels->singular_name
			),
		],
	] : []),
], $is_general_cpt ? array_merge([
	apply_filters(
		'blocksy_single_posts_post_elements_start',
		[],
		$post_type->name . '_single'
	),

	blocksy_get_options('single-elements/featured-image', [
		'prefix' => $post_type->name . '_single',
	]),

	$maybe_taxonomy ? [
		$post_type->name . '_single_has_post_tags' => [
			'label' => sprintf(
				__('%s %s', 'blocksy'),
				$post_type->labels->singular_name,
				get_taxonomy($maybe_taxonomy)->label
			),
			'type' => 'ct-switch',
			'value' => 'no',
			'sync' => blocksy_sync_single_post_container([
				'prefix' => $post_type->name . '_single'
			]),
		],
	] : [],

	blocksy_get_options('single-elements/post-share-box', [
		'prefix' => $post_type->name . '_single',
		'has_share_box' => 'no',
	]),

	blocksy_get_options('single-elements/author-box', [
		'prefix' => $post_type->name . '_single',
	]),

	blocksy_get_options('single-elements/post-nav', [
		'prefix' => $post_type->name . '_single',
		'enabled' => 'no',
		'post_type' => $post_type->name
	]),

	apply_filters(
		'blocksy_single_posts_post_elements_end',
		[],
		$post_type->name . '_single'
	),

	[
		blocksy_rand_md5() => [
			'type' => 'ct-title',
			'label' => __( 'Page Elements', 'blocksy' ),
		],
	],

	blocksy_get_options('single-elements/related-posts', [
		'prefix' => $post_type->name . '_single',
		'enabled' => 'no',
		'post_type' => $post_type->name
	]),

	blocksy_get_options('general/comments-single', [
		'prefix' => $post_type->name . '_single',
	]),

	apply_filters(
		'blocksy_single_posts_end_customizer_options',
		[],
		$post_type->name . '_single'
	),
]) : []);

if (
	function_exists('blc_get_content_block_that_matches')
	&&
	blc_get_content_block_that_matches([
		'template_type' => 'single',
		'template_subtype' => 'canvas',
		'match_conditions_strategy' => $post_type->name . '_single'
	])
) {
	$inner_options = [
		blocksy_rand_md5() => [
			'type' => 'ct-notification',
			'attr' => [ 'data-type' => 'background:white' ],
			'text' => sprintf(
				__('This single page is overrided by a custom template, to edit it please access %sthis page%s.', 'blocksy'),
				'<a href="' . get_edit_post_link(blc_get_content_block_that_matches([
					'template_type' => 'single',
					'template_subtype' => 'canvas',
					'match_conditions_strategy' => $post_type->name . '_single'
				])) . '" target="_blank">',
				'</a>'
			)
		],
	];
}

$options = [
	$post_type->name . '_single_section_options' => [
		'type' => 'ct-options',
		'inner-options' => $inner_options
	],
];
