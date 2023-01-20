<?php
/**
 * Posts widget
 *
 * @copyright 2019-present Creative Themes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @package   Blocksy
 */

$all_post_types = [
	'post' => __('Posts', 'blocksy-companion'),
	'page' => __('Pages', 'blocksy-companion'),
];

if (function_exists('blocksy_manager')) {
	$post_types = blocksy_manager()->post_types->get_supported_post_types();

	foreach ($post_types as $single_post_type) {
		$post_type_object = get_post_type_object($single_post_type);

		if (! $post_type_object) {
			continue;
		}

		$all_post_types[$single_post_type] = $post_type_object->labels->singular_name;
	}
}

$cpt_options = [];

foreach ($all_post_types as $custom_post_type => $label) {
	if ($custom_post_type === 'page') {
		continue;
	}

	$opt_id = 'category';
	$label = __('Category', 'blocksy-companion');
	$label_multiple = __('All categories', 'blocksy-companion');
	$taxonomy = 'category';

	if ($custom_post_type !== 'post') {
		$opt_id = $custom_post_type . '_taxonomy';
		$label = __('Taxonomy', 'blocksy-companion');
		$label_multiple = __('All taxonomies', 'blocksy-companion');

		$taxonomies = get_object_taxonomies($custom_post_type);

		if (count($taxonomies) > 0) {
			$taxonomy = $taxonomies[0];
		} else {
			$taxonomy = 'nonexistent';
		}
	}

	$categories = get_terms([
		'taxonomy' => $taxonomy,
		// 'post_type' => $custom_post_type,
		'orderby' => 'name',
		'order' => 'ASC',
		'hide_empty' => false
	]);

	$category_choices = [
		'all_categories' => $label_multiple
	];

	if (! is_wp_error($categories)) {
		foreach ($categories as $category) {
			$category_choices[$category->term_id] = $category->name;
		}
	}

	$cpt_options[blocksy_rand_md5()] = [
		'type' => 'ct-condition',
		'condition' => [
			'post_type_source' => $custom_post_type,
			'post_source' => '!custom'
		],
		'options' => [
			$opt_id => [
				'type' => 'ct-select',
				'label' => $label,
				'value' => 'all_categories',
				'choices' => blocksy_ordered_keys($category_choices),
				'design' => 'inline',
			],
		]
	];
}

$options = [
	[
		'title' => [
			'type' => 'text',
			'label' => __('Title', 'blocksy-companion'),
			'field_attr' => ['id' => 'widget-title'],
			'design' => 'inline',
			'value' => __('Posts', 'blocksy-companion'),
		],

		'posts_type' => [
			'type' => 'ct-select',
			'label' => __('Widget Design', 'blocksy-companion'),
			'value' => 'small-thumbs',
			'design' => 'inline',
			'choices' => blocksy_ordered_keys(
				[
					'no-thumbs' => __( 'Without Thumbnails', 'blocksy-companion' ),
					'small-thumbs' => __( 'Small Thumbnails', 'blocksy-companion' ),
					'large-thumbs' => __( 'Large Thumbnails', 'blocksy-companion' ),
					'large-small' => __( 'First Thumbnail Large', 'blocksy-companion' ),
					'rounded' => __( 'Rounded Thumbnails', 'blocksy-companion' ),
					'numbered' => __( 'Numbered', 'blocksy-companion' ),
				]
			),
		],

		blocksy_rand_md5() => [
			'type' => 'ct-condition',
			'condition' => [
				'posts_type' => 'small-thumbs|large-thumbs|large-small',
			],
			'options' => [

				'post_widget_image_ratio' => [
					'label' => __( 'Image Ratio', 'blocksy-companion' ),
					'type' => 'ct-ratio',
					'value' => 'original',
					'design' => 'inline',
				],

			],
		],

		'post_type_source' => [
			'type' => 'ct-select',
			'label' => __( 'Post Type', 'blocksy-companion' ),
			'value' => 'post',
			'design' => 'inline',
			'choices' => blocksy_ordered_keys($all_post_types)
		],

		blocksy_rand_md5() => [
			'type' => 'ct-condition',
			'condition' => ['post_type_source' => '!page'],
			'options' => [
				'post_source' => [
					'type' => 'ct-select',
					'label' => __( 'Source', 'blocksy-companion' ),
					'value' => 'categories',
					'design' => 'inline',
					'choices' => blocksy_ordered_keys(
						[
							'categories' => __('Taxonomies', 'blocksy-companion'),
							'custom' => __( 'Custom Query', 'blocksy-companion' ),
						]
					),
				],
			],
		],

		blocksy_rand_md5() => [
			'type' => 'ct-condition',
			'condition' => ['post_type_source' => 'page'],
			'options' => [
				'page_source' => [
					'type' => 'ct-select',
					'label' => __('Source', 'blocksy-companion'),
					'value' => 'default',
					'design' => 'inline',
					'choices' => blocksy_ordered_keys(
						[
							'default' => __('Default', 'blocksy-companion'),
							'custom' => __('Custom Query', 'blocksy-companion'),
						]
					),
				],
			],
		],
	],

	$cpt_options,

	blocksy_rand_md5() => [
		'type' => 'ct-condition',
		'condition' => [
			'post_type_source' => '!page',
			'post_source' => '!custom'
		],
		'options' => [
			'type' => [
				'type' => 'ct-select',
				'label' => __('Sort by', 'blocksy-companion'),
				'value' => 'recent',
				'design' => 'inline',
				'choices' => blocksy_ordered_keys(
					[
						'default' => __('Default', 'blocksy-companion'),
						'recent' => __('Recent', 'blocksy-companion'),
						'commented' => __('Most Commented', 'blocksy-companion'),
						'random' => __('Random', 'blocksy-companion'),
					]
				),
			],

			'days' => [
				'type' => 'ct-select',
				'label' => __( 'Order by', 'blocksy-companion' ),
				'value' => 'all_time',
				'design' => 'inline',
				'choices' => blocksy_ordered_keys(
					[
						'all_time' => __( 'All Time', 'blocksy-companion' ),
						'7' => __( '1 Week', 'blocksy-companion' ),
						'30' => __( '1 Month', 'blocksy-companion' ),
						'90' => __( '3 Months', 'blocksy-companion' ),
						'180' => __( '6 Months', 'blocksy-companion' ),
						'360' => __( '1 Year', 'blocksy-companion' ),
					]
				),
			],

			'posts_number' => [
				'type' => 'ct-number',
				'label' => __( 'Posts Count', 'blocksy-companion' ),
				'min' => 1,
				'max' => 30,
				'value' => 5,
				'design' => 'inline-full',
			],
		],
	],

	blocksy_rand_md5() => [
		'type' => 'ct-condition',
		'condition' => [
			'post_type_source' => '!page',
			'post_source' => 'custom'
		],
		'options' => [

			'post_id' => [
				'label' => __( 'Posts ID', 'blocksy-companion' ),
				'type' => 'text',
				'design' => 'inline',
				'desc' => sprintf(
					__('Separate posts ID by comma. How to find the %spost ID%s.', 'blocksy-companion'),
					'<a href="https://www.wpbeginner.com/beginners-guide/how-to-find-post-category-tag-comments-or-user-id-in-wordpress/" target="_blank">',
					'</a>'
				),
			],

		],
	],

	blocksy_rand_md5() => [
		'type' => 'ct-condition',
		'condition' => [
			'post_type_source' => 'page',
			'page_source' => 'custom'
		],
		'options' => [

			'page_id' => [
				'label' => __( 'Pages ID', 'blocksy-companion' ),
				'type' => 'text',
				'design' => 'inline',
				'desc' => sprintf(
					__('Separate pages ID by comma. More info %shere%s.', 'blocksy-companion'),
					'<a href="https://www.wpbeginner.com/beginners-guide/how-to-find-post-category-tag-comments-or-user-id-in-wordpress/" target="_blank">',
					'</a>'
				),
			],

		],
	],

	blocksy_rand_md5() => [
		'type' => 'ct-condition',
		'condition' => [
			'post_type_source' => 'page',
			'page_source' => '!custom'
		],
		'options' => [
			'page_number' => [
				'type' => 'ct-number',
				'label' => __( 'Pages Count', 'blocksy-companion' ),
				'min' => 1,
				'max' => 30,
				'value' => 5,
				'design' => 'inline-full',
			],
		],
	],

	'display_date' => [
		'type'  => 'ct-switch',
		'label' => __( 'Show Date', 'blocksy-companion' ),
		'value' => 'no',
		'design' => 'inline-full',
	],

	'display_comments' => [
		'type'  => 'ct-switch',
		'label' => __( 'Show Comments', 'blocksy-companion' ),
		'value' => 'no',
		'design' => 'inline-full',
	],

	'display_excerpt' => [
		'type'  => 'ct-switch',
		'label' => __( 'Show Excerpt', 'blocksy-companion' ),
		'value' => 'no',
		'design' => 'inline-full',
	],

	blocksy_rand_md5() => [
		'type' => 'ct-condition',
		'condition' => [ 'display_excerpt' => 'yes' ],
		'options' => [

			'excerpt_lenght' => [
				'type' => 'ct-number',
				'label' => __( 'Excerpt Lenght', 'blocksy-companion' ),
				'min' => 5,
				'max' => 30,
				'value' => 10,
				'design' => 'inline-full',
			],

		],
	],
];

