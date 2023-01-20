<?php
/**
 * Author page
 *
 * @copyright 2020-present Creative Themes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @package   Blocksy
 */

$inner_options = [
	blocksy_get_options('general/page-title', [
		'prefix' => 'author',
		'is_author' => true
	]),

	blocksy_get_options('general/posts-listing', [
		'prefix' => 'author',
		'title' => __('Author Page', 'blocksy')
	]),

	[
		blocksy_rand_md5() => [
			'type'  => 'ct-title',
			'label' => __( 'Page Elements', 'blocksy' ),
		],
	],

	blocksy_get_options('general/sidebar-particular', [
		'prefix' => 'author',
	])
];

if (
	function_exists('blc_get_content_block_that_matches')
	&&
	blc_get_content_block_that_matches([
		'template_type' => 'archive',
		'template_subtype' => 'canvas',
		'match_conditions_strategy' => 'author'
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
					'match_conditions_strategy' => 'author'
				])) . '" target="_blank">',
				'</a>'
			)
		],
	];
}

$options = [
	'author_section_options' => [
		'type' => 'ct-options',
		'setting' => [ 'transport' => 'postMessage' ],
		'inner-options' => $inner_options
	],
];
