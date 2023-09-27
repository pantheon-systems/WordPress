<?php

add_filter(
	'rest_post_query',
	function ($args, $request) {
		if (
			isset($request['post_type'])
			&&
			(strpos($request['post_type'], 'ct_forced') !== false)
		) {
			$post_type = explode(
				':',
				str_replace('ct_forced_', '', $request['post_type'])
			);

			if ($post_type[0] === 'any') {
				$post_type = array_diff(
					get_post_types(['public' => true]),
					['ct_content_block']
				);
			}

			$args = [
				'posts_per_page' => $args['posts_per_page'],
				'post_type' => $post_type,
				'paged' => 1,
				's' => $args['s'],
			];
		}

		if (
			isset($request['post_type'])
			&&
			(strpos($request['post_type'], 'ct_cpt') !== false)
		) {
			$next_args = [
				'posts_per_page' => $args['posts_per_page'],
				'post_type' => array_diff(
					get_post_types(['public' => true]),
					['post', 'page', 'attachment', 'ct_content_block']
				),
				'paged' => 1
			];

			if (isset($args['s'])) {
				$next_args['s'] = $args['s'];
			}

			$args = $next_args;
		}

		if (
			is_array($args['post_type'])
			&&
			in_array('product', $args['post_type'])
		) {
			if ('yes' === get_option('woocommerce_hide_out_of_stock_items')) {
				$meta_query = [];

				if (isset($args['meta_query'])) {
					$meta_query = $args['meta_query'];
				}

				$meta_query[] = array(
					'key'     => '_stock_status',
					'value'   => 'outofstock',
					'compare' => '!=',
				);

				$args['meta_query'] = $meta_query;
			}

			if (
				function_exists('wc_get_product_visibility_term_ids')
				&&
				count($args['post_type']) === 1
			) {
				$product_visibility_term_ids = wc_get_product_visibility_term_ids();

				$tax_query = [];

				if (isset($args['tax_query'])) {
					$tax_query = $args['tax_query'];
				}

				$tax_query['relation'] = 'AND';

				$tax_query[] = [
					[
						'taxonomy' => 'product_visibility',
						'field' => 'term_taxonomy_id',
						'terms' => $product_visibility_term_ids['exclude-from-search'],
						'operator' => 'NOT IN',
					]
				];

				$args['tax_query'] = $tax_query;
			}
		}

		return $args;
	},
	10,
	2
);

if (!is_admin()) {
	add_filter('pre_get_posts', function ($query) {
		if ($query->is_search && (
			is_search()
			||
			wp_doing_ajax()
		)) {
			if (!empty($_GET['ct_post_type'])) {
				$custom_post_types = blocksy_manager()->post_types->get_supported_post_types();

				if (function_exists('is_bbpress')) {
					$custom_post_types[] = 'forum';
					$custom_post_types[] = 'topic';
					$custom_post_types[] = 'reply';
				}

				$allowed_post_types = [];

				$post_types = explode(
					':',
					sanitize_text_field($_GET['ct_post_type'])
				);

				$known_cpts = ['post', 'page'];

				if (get_post_type_object('product')) {
					$known_cpts[] = 'product';
				}

				foreach ($post_types as $single_post_type) {
					if (
						in_array($single_post_type, $custom_post_types)
						||
						in_array($single_post_type, $known_cpts)
					) {
						$allowed_post_types[] = $single_post_type;
					}
				}

				$query->set('post_type', $allowed_post_types);

				if (in_array('product', $allowed_post_types)) {
					if ('yes' === get_option('woocommerce_hide_out_of_stock_items')) {
						$meta_query = [];

						if (! empty($query->get('meta_query'))) {
							$meta_query = $query->get('meta_query');
						}

						$meta_query[] = array(
							'key'     => '_stock_status',
							'value'   => 'outofstock',
							'compare' => '!=',
						);

						$query->set('meta_query', $meta_query);
					}
				}
			}
		}

		return $query;
	});
}

