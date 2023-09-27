<?php

$prefix = blocksy_manager()->screen->get_prefix();

if (function_exists('tutor_utils') && $prefix === 'courses_single') {
	if (tutor_utils()->is_enrolled()) {
		tutor_course_enrolled_lead_info();
	} else {
		tutor_course_lead_info();
	}

	return;
}

$is_page = blocksy_is_page();


$default_hero_elements = [];

$default_hero_elements[] = [
	'id' => 'custom_title',
	'enabled' => $prefix !== 'product',
];

if (! function_exists('tutor_utils') && $prefix !== 'courses_archive') {
	$default_hero_elements[] = [
		'id' => 'custom_description',
		'enabled' => $prefix !== 'product',
	];
}

if (
	(
		is_singular() || is_author()
	) && ! (
		function_exists('is_bbpress') && (
			get_post_type() === 'forum'
			||
			get_post_type() === 'topic'
			||
			get_post_type() === 'reply'
		)
	) && ! (get_post_type() === 'elementor_library')
) {
	$default_hero_elements[] = [
		'id' => 'custom_meta',
		'enabled' => ! $is_page && $prefix !== 'product',
	];
}

if (is_author()) {
	$default_hero_elements[] = [
		'id' => 'author_social_channels',
		'enabled' => true,
	];
}

$default_hero_elements[] = [
	'id' => 'breadcrumbs',
	'enabled' => $prefix === 'product',
];

$hero_elements = blocksy_akg_or_customizer(
	'hero_elements',
	blocksy_get_page_title_source(),
	$default_hero_elements
);

$meta_indexes = [
	'first' => null,
	'second' => null
];

foreach ($hero_elements as $index => $single_hero_element) {
	if (! isset($single_hero_element['enabled'])) {
		continue;
	}

	if ($single_hero_element['id'] === 'custom_meta') {
		if ($meta_indexes['first'] === null) {
			$meta_indexes['first'] = $index;
		} else {
			$meta_indexes['second'] = $index;
		}
	}
}

foreach ($hero_elements as $index => $single_hero_element) {
	if (! isset($single_hero_element['enabled'])) {
		continue;
	}

	if (! $single_hero_element['enabled']) {
		continue;
	}

	do_action('blocksy:hero:' . $single_hero_element['id'] . ':before');

	do_action('blocksy:hero:element:render', $single_hero_element);

	if ($single_hero_element['id'] === 'breadcrumbs') {
		$breadcrumbs_builder = new Blocksy_Breadcrumbs_Builder();
		echo $breadcrumbs_builder->render();
	}

	if ($single_hero_element['id'] === 'custom_title') {
		$has_author_avatar = false;

		if (
			blocksy_akg('has_author_avatar', $single_hero_element, 'yes') === 'yes'
		) {
			if (is_author()) {
				$has_author_avatar = true;
			}
		}

		$title = '';

		$has_category_label_default = 'yes';

		if ($prefix === 'courses_archive' && function_exists('tutor')) {
			$has_category_label_default = 'no';
		}

		$has_category_label = blocksy_akg(
			'has_category_label',
			$single_hero_element,
			$has_category_label_default
		);

		if (function_exists('is_woocommerce') && is_woocommerce()) {
			$has_category_label = 'no';
		}

		if (
			(
				is_singular() || blocksy_is_page()
			) && ! is_search()
		) {
			if (! $post_id) {
				$post_id = get_the_ID();
			}

			if (! empty(get_the_title($post_id))) {
				$title = get_the_title($post_id);
			}

		} else {
			if (! is_search()) {
				if (! empty(get_the_archive_title())) {
					$title = wp_strip_all_tags(get_the_archive_title());

					$divider_symbol = ':';

					if (strpos($title, '：') !== false) {
						$divider_symbol = '：';
					}

					if (function_exists('is_shop') && is_shop()) {
						if (strpos($title, $divider_symbol) !== false) {
							$title_pieces = explode($divider_symbol, $title, 2);
							$title = $title_pieces[1];
						}
					}

					if (strpos($title, $divider_symbol) !== false) {
						$title_pieces = explode($divider_symbol, $title, 2);

						$title = '<span class="ct-title-label">' . $title_pieces[0] . '</span>' . $title_pieces[1];

						if ($has_category_label !== 'yes') {
							$title = $title_pieces[1];
						}
					}
				}

				if (is_author()) {
					$title = get_the_author_meta('display_name', blocksy_get_author_id());
				}
			} else {
				$title = sprintf(
					// translators: %s is the number of results
					__( '<span>Search Results for</span> %s', 'blocksy' ),
					get_search_query()
				);
			}

			if (!have_posts()) {
				// $title = __('Nothing Found', 'blocksy');
			}
		}

		if (is_home() && is_front_page()) {
			$title = blocksy_translate_dynamic(blocksy_akg(
				'title',
				$single_hero_element,
				(
					function_exists('is_shop') && is_shop()
				) ? __('Products', 'blocksy') : __('Home', 'blocksy')
			), $prefix . '_hero_custom_title');
		}

		if (! empty($title)) {
			$title = blocksy_html_tag(
				blocksy_akg('heading_tag', $single_hero_element, 'h1'),
				array_merge([
					'class' => 'page-title',
					'title' => strip_tags($title)
				], blocksy_schema_org_definitions('headline', [
					'array' => true
				])),
				$title
			);
		}

		if (is_author()) {
			echo '<div class="ct-author-name">';
		}

		if ($has_author_avatar) {
			$avatar_size = intval(blocksy_akg(
				'author_avatar_size',
				$single_hero_element,
				'60'
			)) * 2;

			$title = blocksy_simple_image(
				apply_filters(
					'blocksy:hero:title:author:author-avatar-url',
					get_avatar_url(
						blocksy_get_author_id(),
						[
							'size' => $avatar_size
						]
					)
				),
				[
					'tag_name' => 'span',
					'aspect_ratio' => false,
					'suffix' => 'static',
					'img_atts' => [
						'width' => $avatar_size / 2,
						'height' => $avatar_size / 2,
						'style' => 'height:' . (
							intval($avatar_size) / 2
						) . 'px',
						'alt' => blocksy_get_avatar_alt_for(get_the_author_meta('ID'))
					],
				]
			) . $title;
		}

		if (function_exists('is_woocommerce') && is_woocommerce()) {
			$title = apply_filters('woocommerce_page_title', $title);
		}

		do_action('blocksy:hero:title:before');
		echo $title;
		do_action('blocksy:hero:title:after');

		if (is_author()) {
			echo '</div>';
		}
	}

	if ($single_hero_element['id'] === 'custom_description') {
		$description = '';
		$description_class = 'page-description';

		$description_class .= ' ' . blocksy_visibility_classes(
			blocksy_akg(
				'description_visibility',
				$single_hero_element,
				[
					'desktop' => true,
					'tablet' => true,
					'mobile' => false,
				]
			)
		);

		if (
			(
				is_singular() || $is_page
			) && ! is_search()
		) {
			if (! $post_id) {
				$post_id = $is_page ? $is_page : get_the_ID();
			}

			if (function_exists('is_woocommerce') && is_shop()) {
				ob_start();
				woocommerce_taxonomy_archive_description();
				woocommerce_product_archive_description();
				$description = ob_get_clean();
			}

			if (has_excerpt($post_id)) {
				$description = blocksy_entry_excerpt(
					40,
					$description_class,
					$post_id
				);
			}
		} else {
			if (! is_search()) {
				if (! empty(get_the_archive_description())) {
					$description = '<div class="' . $description_class . '">' . get_the_archive_description() . '</div>';
				}

				if (is_author()) {
					if (! empty(trim(get_the_author_meta('description', blocksy_get_author_id())))) {
						$description = '<div class="' . $description_class . '">' . wp_kses_post(get_the_author_meta('description', blocksy_get_author_id())) . '</div>';
					}
				}

				if (empty($description) && ! have_posts()) {
					$description = __(
						"It seems we can't find what you're looking for. Perhaps searching can help.",
						'blocksy'
					);

					$description = '<div class="' . $description_class . '">' . $description . '</div>';
				}
			} else {
				$title = sprintf(
					// translators: %s is the number of results
					__( '<span>Search Results for</span> %s', 'blocksy' ),
					get_search_query()
				);

				if (! have_posts()) {
					// translators: %s are the opening and closing of the html tags
					$description = sprintf(
						__('%sSorry, but nothing matched your search terms. Please try again with some different keywords.%s', 'blocksy'),
						'<div class="' . $description_class . '">',
						'</div>'
					);
				}
			}
		}

		if (is_home() && is_front_page()) {
			if (! empty(blocksy_akg(
				'description',
				$single_hero_element,
				''
			))) {
				$description = '<div class="' . $description_class . '">' . blocksy_translate_dynamic(blocksy_akg(
					'description',
					$single_hero_element,
					(
						function_exists('is_shop') && is_shop()
					) ? __('This is where you can add new products to your store.', 'blocksy') : ''
				), $prefix . '_hero_custom_description') . '</div>';
			}
		}

		do_action('blocksy:hero:description:before');
		echo $description;
		do_action('blocksy:hero:description:after');
	}

	if ($single_hero_element['id'] === 'author_social_channels') {
		if (is_author()) {
			blocksy_author_social_channels([
				'new_tab' => blocksy_akg(
					'link_target',
					$single_hero_element,
					'no'
				) === 'yes'
			]);
		}
	}

	if ($single_hero_element['id'] === 'custom_meta') {
		$author_page_meta_elements = blocksy_akg(
			'page_meta_elements',
			$single_hero_element,
			[
				'joined' => true,
				'articles_count' => true,
				'comments' => true
			]
		);

		$meta_type = blocksy_akg(
			'meta_type',
			$single_hero_element,
			'simple'
		);

		$meta_divider = blocksy_akg(
			'meta_divider',
			$single_hero_element,
			'slash'
		);

		$force_icons = false;

		$attr = [];

		if (
			$meta_indexes['first'] !== null
			&&
			$meta_indexes['second'] !== null
		) {
			if ($index === $meta_indexes['first']) {
				do_action('blocksy:hero:' . $single_hero_element['id'] . ':first:before');
			}

			if ($index === $meta_indexes['second']) {
				do_action('blocksy:hero:' . $single_hero_element['id'] . ':second:before');
			}
		}

		if (
			$meta_indexes['first'] !== null
			&&
			$meta_indexes['second'] !== null
		) {
			if ($index === $meta_indexes['first']) {
				// $attr['data-id'] = 'first';
			}

			if ($index === $meta_indexes['second']) {
				$attr['data-id'] = 'second';
			}
		}

		if (is_author()) {
			blocksy_author_meta_elements([
				'value' => $author_page_meta_elements,
				'attr' => $attr
			]);
		}

		$has_meta_label = blocksy_akg(
			'has_meta_label',
			$single_hero_element,
			'no'
		) === 'yes';

		$single_meta_elements = null;

		if (is_singular() || $is_page) {
			$single_meta_elements = blocksy_akg(
				'meta_elements',
				$single_hero_element,
				blocksy_post_meta_defaults([
					[
						'id' => 'author',
						'has_author_avatar' => 'yes',
						'enabled' => true
					],

					[
						'id' => 'post_date',
						'enabled' => true
					],

					[
						'id' => 'comments',
						'enabled' => true
					],

					[
						'id' => 'categories',
						'enabled' => true
					],
				])
			);

			/**
			 * Note to code reviewers: This line doesn't need to be escaped.
			 * Function blocksy_post_meta() used here escapes the value properly.
			 * Mainly because the function outputs SVG.
			 */
			echo blocksy_post_meta(
				$single_meta_elements,
				[
					'attr' => $attr,
					'meta_type' => $meta_type,
					'meta_divider' => $meta_divider,
					'force_icons' => $force_icons,
					'prefix' => $prefix . '_hero_meta'
				]
			);
		}

		if (
			$meta_indexes['first'] !== null
			&&
			$meta_indexes['second'] !== null
		) {
			if ($index === $meta_indexes['first']) {
				do_action('blocksy:hero:' . $single_hero_element['id'] . ':first:after');
			}

			if ($index === $meta_indexes['second']) {
				do_action('blocksy:hero:' . $single_hero_element['id'] . ':second:after');
			}
		}
	}

	do_action('blocksy:hero:' . $single_hero_element['id'] . ':after');
}
