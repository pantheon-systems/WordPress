<?php

namespace Blocksy;

class ConditionsManager {
	public function __construct() {
	}

	public function condition_matches($rules = [], $args = []) {
		$args = wp_parse_args($args, [
			'relation' => 'OR',
			// prefix | current-screen
			'strategy' => 'current-screen'
		]);

		if (empty($rules)) {
			return false;
		}

		$all_includes = array_filter($rules, function ($el) {
			return $el['type'] === 'include';
		});

		$all_excludes = array_filter($rules, function ($el) {
			return $el['type'] === 'exclude';
		});

		$resolved_includes = array_filter($all_includes, function ($el) use ($args) {
			if ($args['strategy'] === 'current-screen') {
				return $this->resolve_single_condition($el);
			}

			return $this->resolve_single_condition_with_prefix(
				$el,
				$args['strategy']
			);
		});

		$resolved_excludes = array_filter($all_excludes, function ($el) use ($args) {
			if ($args['strategy'] === 'current-screen') {
				return $this->resolve_single_condition($el);
			}

			return $this->resolve_single_condition_with_prefix(
				$el,
				$args['strategy']
			);
		});

		// If at least one exclusion is true -- return false
		if (! empty($resolved_excludes)) {
			return false;
		}

		if (empty($all_includes)) {
			return true;
		}

		if (! empty($all_includes)) {
			// If at least one inclusion is true - return true
			if ($args['relation'] === 'OR' && ! empty($resolved_includes)) {
				return true;
			}

			// For AND relation all includes need to be resolved
			if (
				$args['relation'] === 'AND'
				&&
				count($resolved_includes) === count($all_includes)
			) {
				return true;
			}
		}

		return false;
	}

	public function resolve_single_condition($rule) {
		if ($rule['rule'] === 'everywhere') {
			return true;
		}


		if ($rule['rule'] === 'singulars') {
			return is_singular();
		}


		if ($rule['rule'] === 'archives') {
			return is_archive();
		}

		if ($rule['rule'] === '404') {
			return is_404();
		}

		if ($rule['rule'] === 'search') {
			return is_search();
		}

		if ($rule['rule'] === 'blog') {
			return ! is_front_page() && is_home();
		}

		if ($rule['rule'] === 'front_page') {
			return is_front_page();
		}

		if ($rule['rule'] === 'privacy_policy_page') {
			$is_blocksy_page = blocksy_is_page();

			if (is_singular() || $is_blocksy_page) {
				$post_id = get_the_ID();

				if ($is_blocksy_page) {
					$post_id = $is_blocksy_page;
				}

				return intval($post_id) === intval(
					get_option('wp_page_for_privacy_policy')
				);
			}
		}

		if ($rule['rule'] === 'date') {
			return is_date();
		}

		if ($rule['rule'] === 'author') {
			if (
				isset($rule['payload'])
				&&
				isset($rule['payload']['user_id'])
			) {
				return is_author($rule['payload']['user_id']);
			}

			return is_author();
		}

		if ($rule['rule'] === 'woo_shop') {
			return function_exists('is_shop') && is_shop();
		}

		if ($rule['rule'] === 'single_post') {
			return is_singular('post');
		}

		if ($rule['rule'] === 'all_post_archives') {
			return is_post_type_archive('post');
		}

		if ($rule['rule'] === 'post_categories') {
			return is_category();
		}

		if ($rule['rule'] === 'post_tags') {
			return is_tag();
		}

		if ($rule['rule'] === 'single_page') {
			return is_singular('page');
		}

		if ($rule['rule'] === 'single_product') {
			return function_exists('is_product') && is_product();
		}

		if ($rule['rule'] === 'all_product_archives') {
			if (function_exists('is_shop')) {
				return is_shop() || is_product_tag() || is_product_category();
			}
		}

		if ($rule['rule'] === 'all_product_categories') {
			if (function_exists('is_shop')) {
				return is_product_category();
			}
		}

		if ($rule['rule'] === 'all_product_tags') {
			if (function_exists('is_shop')) {
				return is_product_tag();
			}
		}

		if ($rule['rule'] === 'user_logged_in') {
			return is_user_logged_in();
		}

		if ($rule['rule'] === 'user_logged_out') {
			return ! is_user_logged_in();
		}

		if ($rule['rule'] === 'user_post_author_id') {
			global $post;

			if (
				$post
				&&
				$post->post_author
				&&
				isset($rule['payload'])
				&&
				isset($rule['payload']['user_id'])
			) {
				$user_id = $rule['payload']['user_id'];
				$post_author = intval($post->post_author);

				if ($user_id === 'current_user') {
					return $post_author === intval(get_current_user_id());
				}

				return intval($user_id) === $post_author;
			}
		}

		if (strpos($rule['rule'], 'user_role_') !== false) {
			if (! is_user_logged_in()) {
				return false;
			}

			return in_array(
				str_replace('user_role_', '', $rule['rule']),
				get_userdata(wp_get_current_user()->ID)->roles
			);
		}

		if (strpos($rule['rule'], 'post_type_single_') !== false) {
			return is_singular(str_replace(
				'post_type_single_',
				'',
				$rule['rule']
			));
		}

		if (strpos($rule['rule'], 'post_type_archive_') !== false) {
			return is_post_type_archive(str_replace(
				'post_type_archive_',
				'',
				$rule['rule']
			));
		}

		if (strpos($rule['rule'], 'post_type_taxonomy_') !== false) {
			return is_tax(str_replace(
				'post_type_taxonomy_',
				'',
				$rule['rule']
			));
		}

		if (
			$rule['rule'] === 'post_ids'
			||
			$rule['rule'] === 'page_ids'
			||
			$rule['rule'] === 'custom_post_type_ids'
		) {
			if (function_exists('blocksy_is_page')) {
				$is_blocksy_page = blocksy_is_page();

				if (is_singular() || $is_blocksy_page) {
					$post_id = get_the_ID();

					if ($is_blocksy_page) {
						$post_id = $is_blocksy_page;
					}

					global $post;

					if (intval($post_id) === 0 && isset($post->post_name)) {
						$maybe_post = get_page_by_path($post->post_name);

						if ($maybe_post) {
							$post_id = $maybe_post->ID;
						}
					}

					if (
						isset($rule['payload'])
						&&
						isset($rule['payload']['post_id'])
						&&
						$post_id
						&&
						intval($post_id) === intval($rule['payload']['post_id'])
					) {
						return true;
					}
				}
			}
		}

		if (
			$rule['rule'] === 'current_language'
			&&
			function_exists('blocksy_get_current_language')
			&&
			! empty($rule['payload']['language'])
		) {
			return $rule['payload']['language'] === blocksy_get_current_language();
		}

		if (
			$rule['rule'] === 'bbpress_profile'
			&&
			function_exists('bbp_is_single_user_profile')
		) {
			return bbp_is_single_user_profile();
		}

		if ($rule['rule'] === 'taxonomy_ids') {
			if (is_tax() || is_category() || is_tag()) {
				$tax_id = get_queried_object_id();

				if (
					isset($rule['payload'])
					&&
					isset($rule['payload']['taxonomy_id'])
					&&
					$tax_id
					&&
					intval($tax_id) === intval($rule['payload']['taxonomy_id'])
				) {
					return true;
				}
			}
		}

		if ($rule['rule'] === 'post_with_taxonomy_ids') {
			$is_blocksy_page = blocksy_is_page();
			global $blocksy_is_quick_view;
			global $wp_query;

			global $post;

			if (is_singular() || $is_blocksy_page || $wp_query->in_the_loop) {
				$post_id = get_the_ID();

				if ($is_blocksy_page) {
					$post_id = $is_blocksy_page;
				}

				if (wp_doing_ajax() && isset($_GET['product_id'])) {
					$post_id = sanitize_text_field($_GET['product_id']);
				}

				if (
					isset($rule['payload'])
					&&
					isset($rule['payload']['taxonomy_id'])
					&&
					$post_id
					&&
					get_term($rule['payload']['taxonomy_id'])
					&&
					in_array(
						get_term($rule['payload']['taxonomy_id'])->taxonomy,
						get_object_taxonomies([
							'post_type' => get_post_type($post_id)
						])
					)
				) {
					return has_term(
						$rule['payload']['taxonomy_id'],
						get_term($rule['payload']['taxonomy_id'])->taxonomy,
						$post_id
					);
				}
			}
		}

		return false;
	}

	public function resolve_single_condition_with_prefix($rule, $prefix) {
		if ($rule['rule'] === 'everywhere') {
			return true;
		}

		if ($rule['rule'] === 'singulars') {
			return (
				$prefix === 'single_blog_post'
				||
				$prefix === 'single_page'
				||
				strpos($prefix, '_single') !== false
			);
		}

		if ($rule['rule'] === 'archives') {
			return is_archive();
		}

		if ($rule['rule'] === '404') {
			return $prefix === '404';
		}

		if ($rule['rule'] === 'search') {
			return $prefix === 'search';
		}

		if ($rule['rule'] === 'blog') {
			return $prefix === 'blog';
		}

		if ($rule['rule'] === 'front_page') {
			return $prefix === 'blog';
		}

		if ($rule['rule'] === 'date') {
			return is_date();
		}

		if ($rule['rule'] === 'author') {
			return is_author();
		}

		if ($rule['rule'] === 'woo_shop') {
			return function_exists('is_shop') && is_shop();
		}

		if ($rule['rule'] === 'single_post') {
			return $prefix === 'single_blog_post';
		}

		if ($rule['rule'] === 'all_post_archives') {
			return is_post_type_archive('post');
		}

		if ($rule['rule'] === 'post_categories') {
			return $prefix === 'categories';
		}

		if ($rule['rule'] === 'post_tags') {
			return $prefix === 'categories';
		}

		if ($rule['rule'] === 'single_page') {
			return $prefix === 'single_page';
		}

		if ($rule['rule'] === 'single_product') {
			return $prefix === 'product';
		}

		if ($rule['rule'] === 'all_product_archives') {
			return $prefix === 'woo_categories';
		}

		if ($rule['rule'] === 'all_product_categories') {
			return $prefix === 'woo_categories';
		}

		if ($rule['rule'] === 'all_product_tags') {
			return $prefix === 'woo_categories';
		}

		if (strpos($rule['rule'], 'post_type_single_') !== false) {
			return $prefix === str_replace(
				'post_type_single_',
				'',
				$rule['rule']
			) . '_single';
		}

		if (strpos($rule['rule'], 'post_type_archive_') !== false) {
			return $prefix === str_replace(
				'post_type_archive_',
				'',
				$rule['rule']
			) . '_archive';
		}

		if (strpos($rule['rule'], 'post_type_taxonomy_') !== false) {
			return $prefix === str_replace(
				'post_type_taxonomy_',
				'',
				$rule['rule']
			) . '_archive';
		}

		return false;
	}

	public function get_all_rules($args = []) {
		$args = wp_parse_args($args, [
			// all | archive | singular
			'filter' => 'all'
		]);

		$has_woo = class_exists('WooCommerce');

		$common_rules = [
			[
				'title' => '',
				'rules' => [
					[
						'id' => 'everywhere',
						'title' => __('Entire Website', 'blocksy-companion')
					]
				]
			]
		];

		if ($args['filter'] === 'all') {
			$common_rules[] = [
				'title' => __('Basic', 'blocksy-companion'),
				'rules' => [
					[
						'id' => 'singulars',
						'title' => __('Singulars', 'blocksy-companion')
					],

					[
						'id' => 'archives',
						'title' => __('Archives', 'blocksy-companion')
					]
				]
			];

			$common_rules[] = [
				'title' => __('Posts', 'blocksy-companion'),
				'rules' => [
					[
						'id' => 'single_post',
						'title' => __('Single Post', 'blocksy-companion')
					],

					[
						'id' => 'all_post_archives',
						'title' => __('All Post Archives', 'blocksy-companion')
					],

					[
						'id' => 'post_categories',
						'title' => __('Post Categories', 'blocksy-companion')
					],

					[
						'id' => 'post_tags',
						'title' => __('Post Tags', 'blocksy-companion')
					],
				]
			];
		}

		if ($args['filter'] === 'archive') {
			$common_rules[] = [
				'title' => __('Basic', 'blocksy-companion'),
				'rules' => [
					[
						'id' => 'archives',
						'title' => __('Archives', 'blocksy-companion')
					]
				]
			];

			$common_rules[] = [
				'title' => __('Posts', 'blocksy-companion'),
				'rules' => [
					[
						'id' => 'all_post_archives',
						'title' => __('All Post Archives', 'blocksy-companion')
					],

					[
						'id' => 'post_categories',
						'title' => __('Post Categories', 'blocksy-companion')
					],

					[
						'id' => 'post_tags',
						'title' => __('Post Tags', 'blocksy-companion')
					],
				]
			];
		}

		if ($args['filter'] === 'singular') {
			$common_rules[] = [
				'title' => __('Basic', 'blocksy-companion'),
				'rules' => [
					[
						'id' => 'singulars',
						'title' => __('Singulars', 'blocksy-companion')
					]
				]
			];

			$common_rules[] = [
				'title' => __('Posts', 'blocksy-companion'),
				'rules' => [
					[
						'id' => 'single_post',
						'title' => __('Single Post', 'blocksy-companion')
					]
				]
			];
		}

		if ($args['filter'] === 'all' || $args['filter'] === 'singular') {
			$common_rules[] = [
				'title' => __('Pages', 'blocksy-companion'),
				'rules' => [
					[
						'id' => 'single_page',
						'title' => __('Single Page', 'blocksy-companion')
					],
				]
			];
		}

		$cpts = [];

		$custom_post_types = array_diff(
			get_post_types(['public' => true]),
			[
				'post',
				'page',
				'attachment',
				'documentation',
				'ct_content_block',
				'product'
			]
		);

		foreach ($custom_post_types as $custom_post_type) {
			$post_type_object = get_post_type_object($custom_post_type);

			if ($args['filter'] === 'all' || $args['filter'] === 'singular') {
				$cpts[] = [
					'id' => 'post_type_single_' . $custom_post_type,
					'title' => sprintf(
						__('%s Single', 'blocksy-companion'),
						$post_type_object->labels->singular_name
					)
				];
			}

			if ($args['filter'] === 'all' || $args['filter'] === 'archive') {
				$cpts[] = [
					'id' => 'post_type_archive_' . $custom_post_type,
					'title' => sprintf(
						__('%s Archive', 'blocksy-companion'),
						$post_type_object->labels->singular_name
					)
				];
			}

			$taxonomies = get_object_taxonomies($custom_post_type);

			if ($args['filter'] === 'all' || $args['filter'] === 'archive') {
				foreach ($taxonomies as $single_taxonomy) {
					$cpts[] = [
						'id' => 'post_type_taxonomy_' . $single_taxonomy,
						'title' => sprintf(
							__('%s %s Taxonomy', 'blocksy-companion'),
							$post_type_object->labels->singular_name,
							get_taxonomy($single_taxonomy)->label
						)
					];
				}
			}
		}

		$specific_conditions = [];

		$specific_ids = [];

		$specific_ids[] = [
			'id' => 'post_ids',
			'title' => __('Post ID', 'blocksy-companion')
		];

		$specific_ids[] = [
			'id' => 'page_ids',
			'title' => __('Page ID', 'blocksy-companion')
		];

		$specific_ids[] = [
			'id' => 'custom_post_type_ids',
			'title' => __('Custom Post Type ID', 'blocksy-companion')
		];

		$specific_ids[] = [
			'id' => 'post_with_taxonomy_ids',
			'title' => __('Post with Taxonomy ID', 'blocksy-companion')
		];

		$specific_ids[] = [
			'id' => 'taxonomy_ids',
			'title' => __('Taxonomy ID', 'blocksy-companion')
		];

		$specific_conditions[] = [
			'title' => __('Specific', 'blocksy-companion'),
			'rules' => $specific_ids
		];

		if ($args['filter'] === 'all' || $args['filter'] === 'archive') {
			$specific_conditions[] = [
				'title' => __('Other Pages', 'blocksy-companion'),
				'rules' => [
					[
						'id' => '404',
						'title' => __('404', 'blocksy-companion')
					],

					[
						'id' => 'search',
						'title' => __('Search', 'blocksy-companion')
					],

					[
						'id' => 'blog',
						'title' => __('Blog', 'blocksy-companion')
					],

					[
						'id' => 'front_page',
						'title' => __('Front Page', 'blocksy-companion')
					],

					[
						'id' => 'privacy_policy_page',
						'title' => __('Privacy Policy Page', 'blocksy-companion')
					],

					/*
					[
						'id' => 'date',
						'title' => __('Date', 'blocksy-companion')
					],
					 */

					[
						'id' => 'author',
						'title' => __('Author Archives', 'blocksy-companion')
					],
				],
			];
		}

		return array_merge(
			$common_rules,
			$has_woo && $args['filter'] === 'all' ? [
				[
					'title' => __('WooCommerce', 'blocksy-companion'),
					'rules' => [
						[
							'id' => 'woo_shop',
							'title' => __('Shop Home', 'blocksy-companion')
						],

						[
							'id' => 'single_product',
							'title' => __('Single Product', 'blocksy-companion')
						],

						[
							'id' => 'all_product_archives',
							'title' => __('Product Archives', 'blocksy-companion')
						],

						[
							'id' => 'all_product_categories',
							'title' => __('Product Categories', 'blocksy-companion')
						],

						[
							'id' => 'all_product_tags',
							'title' => __('Product Tags', 'blocksy-companion')
						],
					]
				]
			] : [],

			count($cpts) > 0 ? [
				[
					'title' => __('Custom Post Types', 'blocksy-companion'),
					'rules' => $cpts
				]
			] : [],

			$specific_conditions,

			[
				[
					'title' => __('User Auth', 'blocksy-companion'),
					'rules' => [
						[
							'id' => 'user_logged_in',
							'title' => __('User Logged In', 'blocksy-companion')
						],

						[
							'id' => 'user_logged_out',
							'title' => __('User Logged Out', 'blocksy-companion')
						],
					]
				],

				[
					'title' => __('User Roles', 'blocksy-companion'),
					'rules' => $this->get_user_roles_rules()
				],

				[
					'title' => __('Other', 'blocksy-companion'),
					'rules' => [
						[
							'id' => 'user_post_author_id',
							'title' => __('Post Author', 'blocksy-companion')
						]
					]
				],
			],

			(
				function_exists('blocksy_get_current_language')
				&&
				blocksy_get_current_language() !== '__NOT_KNOWN__'
			) ? [
				[
					'title' => __('Languages', 'blocksy-companion'),
					'rules' => [
						[
							'id' => 'current_language',
							'title' => __('Current Language', 'blocksy-companion')
						]
					]
				]
			] : [],

			function_exists('bbp_is_single_user_profile') && $args['filter'] === 'all' ? [
				[
					'title' => __('bbPress', 'blocksy-companion'),
					'rules' => [
						[
							'id' => 'bbpress_profile',
							'title' => __('Profile', 'blocksy-companion')
						]
					]
				]
			] : []
		);
	}

	public function humanize_conditions($conditions) {
		$result = [];

		foreach ($conditions as $condition) {
			$type = $condition['type'] === 'include' ? __('Include', 'blocksy-companion') : __(
				'Exclude', 'blocksy-companion'
			);

			$maybe_descriptor = $this->find_rule_descriptor($condition['rule']);

			if (! $maybe_descriptor) {
				continue;
			}

			$to_append = $type . ' ' . $maybe_descriptor['title'];

			if (
				(
					$condition['rule'] === 'post_ids'
					||
					$condition['rule'] === 'page_ids'
					||
					$condition['rule'] === 'custom_post_type_ids'
				) && isset($condition['payload']['post_id'])
			) {
				$to_append .= ' (<a href="' . get_edit_post_link(
					$condition['payload']['post_id']
				) . '" target="_blank">' . get_the_title($condition['payload']['post_id']) . '</a>)';
			}

			if (
				(
					$condition['rule'] === 'taxonomy_ids'
					||
					$condition['rule'] === 'post_with_taxonomy_ids'
				) && isset($condition['payload']['taxonomy_id'])
			) {
				$tax = get_term_by(
					'term_taxonomy_id',
					$condition['payload']['taxonomy_id']
				);

				$to_append .= ' (<a href="' . get_edit_term_link(
					$condition['payload']['taxonomy_id']
				) . '" target="_blank">' . $tax->name . '</a>)';
			}

			if ($condition['rule'] === 'current_language') {
				$to_append = null;

				if (
					isset($condition['payload']['language'])
					&&
					function_exists('blocksy_get_all_i18n_languages')
				) {
					foreach (blocksy_get_all_i18n_languages() as $lang) {
						if ($lang['id'] === $condition['payload']['language']) {
							$to_append = $type . ' ' . $lang['name'] . ' ' . __(
								'Language', 'blocksy-companion'
							);
						}
					}
				}
			}

			if ($to_append) {
				$result[] = $to_append;
			}
		}

		return $result;
	}

	private function find_rule_descriptor($rule) {
		$all = $this->get_all_rules();

		foreach ($all as $rules_group) {
			foreach ($rules_group['rules'] as $single_rule) {
				if ($single_rule['id'] === $rule) {
					return $single_rule;
				}
			}
		}

		return null;
	}

	private function get_user_roles_rules() {
		$result = [];

		foreach (get_editable_roles() as $role_id => $role_info) {
			$result[] = [
				'id' => 'user_role_' . $role_id,
				'title' => $role_info['name']
			];
		}

		return $result;
	}
}

