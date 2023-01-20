<?php

add_action('parse_tax_query', function ($query) {
	if (is_admin() || ! $query->is_main_query()) {
		return;
	}

	if (! (
		is_home() || is_archive() || is_search()
	)) {
		return;
	}

	if (function_exists('is_woocommerce')) {
		if (is_woocommerce()) {
			return;
		}
	}

	if ($query->get('post_type') === 'product') {
		return;
	}

	$prefix = blocksy_manager()->screen->get_prefix();

	if ($prefix === 'bbpress_single' || $prefix === 'courses_archive') {
		return;
	}

	$prefix = blocksy_manager()->screen->get_prefix([
		'allowed_prefixes' => [
			'blog',
			'categories',
			'woo_categories',
			'search'
		],
		'default_prefix' => 'blog'
	]);

	$query->set(
		'posts_per_page',
		intval(get_theme_mod(
			$prefix . '_archive_per_page',
			get_option('posts_per_page', 10)
		))
	);
});

if (! function_exists('blocksy_get_listing_card_type')) {
	function blocksy_get_listing_card_type($args = []) {
		$args = wp_parse_args(
			$args,
			[
				'prefix' => blocksy_manager()->screen->get_prefix()
			]
		);

		$blog_post_structure = blocksy_listing_page_structure([
			'prefix' => $args['prefix']
		]);

		if ($blog_post_structure === 'gutenberg') {
			return '';
		}

		$card_type = get_theme_mod($args['prefix'] . '_card_type', 'boxed');

		if ($card_type === 'cover') {
			if (
				$blog_post_structure === 'simple'
				||
				(
					function_exists('blc_get_content_block_that_matches')
					&&
					blc_get_content_block_that_matches([
						'template_type' => 'archive'
					])
				)
			) {
				$card_type = 'boxed';
			}
		}

		return $card_type;
	}
}

if (! function_exists('blocksy_listing_page_structure')) {
	function blocksy_listing_page_structure($args = []) {
		$args = wp_parse_args(
			$args,
			[
				'prefix' => blocksy_manager()->screen->get_prefix()
			]
		);


		$blog_post_structure = get_theme_mod(
			$args['prefix'] . '_structure',
			'grid'
		);

		if (
			$blog_post_structure === 'gutenberg'
			||
			$blog_post_structure === 'simple'
		) {
			$has_matching_template = (
				function_exists('blc_get_content_block_that_matches')
				&&
				blc_get_content_block_that_matches([
					'template_type' => 'archive',
					'match_conditions_strategy' => rtrim($args['prefix'], '_')
				])
			);

			if ($has_matching_template) {
				return 'grid';
			}
		}

		return $blog_post_structure;
	}
}

if (! function_exists('blocksy_cards_get_deep_link')) {
	function blocksy_generic_get_deep_link($args = []) {
		$args = wp_parse_args(
			$args,
			[
				'suffix' => '',
				'prefix' => null
			]
		);

		if (! $args['prefix']) {
			$args['prefix'] = blocksy_manager()->screen->get_prefix();
		}

		$attr = [];

		if (is_customize_preview()) {
			$attr['data-shortcut'] = 'border:outside';
			$attr['data-location'] = blocksy_first_level_deep_link(
				$args['prefix']
			);

			if (! empty($args['suffix'])) {
				$attr['data-location'] .= ':' . $args['suffix'];
			}
		}

		return blocksy_attr_to_html($attr);
	}
}

