<?php

$inner_options = [
	blocksy_get_options('general/page-title', [
		'prefix' => 'categories',
		'is_archive' => true
	]),

	blocksy_get_options('general/posts-listing', [
		'prefix' => 'categories',
		'title' => __('Categories', 'blocksy')
	]),

	[
		blocksy_rand_md5() => [
			'type'  => 'ct-title',
			'label' => __( 'Categories Elements', 'blocksy' ),
		],
	],

	blocksy_get_options('general/sidebar-particular', [
		'prefix' => 'categories',
	])
];

if (
	function_exists('blc_get_content_block_that_matches')
	&&
	blc_get_content_block_that_matches([
		'template_type' => 'archive',
		'template_subtype' => 'canvas',
		'match_conditions_strategy' => 'categories'
	])
) {
	$inner_options = [
		blocksy_rand_md5() => [
			'type' => 'ct-notification',
			'attr' => [ 'data-type' => 'background:white' ],
			'text' => sprintf(
				__('This archive page is overrided by a custom template, to edit it please access %sthis page%s.', 'blocksy'),
				'<a href="' . get_edit_post_link(blc_get_content_block_that_matches([
					'template_type' => 'archive',
					'template_subtype' => 'canvas',
					'match_conditions_strategy' => 'categories'
				])) . '" target="_blank">',
				'</a>'
			)
		],
	];
}

$options = [
	'single_categories_section_options' => [
		'type' => 'ct-options',
		'setting' => [ 'transport' => 'postMessage' ],
		'inner-options' => $inner_options
	],
];
