<?php

add_filter('safe_style_css', function($styles) {
	$styles[] = 'aspect-ratio';
	return $styles;
});

if (! function_exists('blocksy_post_meta')) {
	function blocksy_post_meta($post_meta_descriptor = null, $args = []) {
		if (! $post_meta_descriptor && ! is_array($post_meta_descriptor)) {
			$post_meta_descriptor = blocksy_post_meta_defaults([
				[
					'id' => 'author',
					'enabled' => true,
				],

				[
					'id' => 'comments',
					'enabled' => true,
				],

				[
					'id' => 'post_date',
					'enabled' => true,
				],

				[
					'id' => 'updated_date',
					'enabled' => true,
				],

				[
					'id' => 'categories',
					'enabled' => true,
				]
			]);
		}

		$args = wp_parse_args(
			$args,
			[
				'class' => '',
				'meta_type' => 'simple',
				'meta_divider' => 'none',

				'force_icons' => false,

				'prefix' => '',

				'attr' => []
			]
		);

		$has_any_enabled_element = false;

		foreach ($post_meta_descriptor as $index => $single_meta) {
			global $post;

			if (
				$single_meta['id'] === 'author'
				&&
				! isset($single_meta['label'])
			) {
				$post_meta_descriptor[$index]['label'] = __('By', 'blocksy');
			}

			if (
				(
					$single_meta['id'] === 'post_date'
					||
					$single_meta['id'] === 'updated_date'
				) && ! isset($single_meta['label'])
			) {
				$post_meta_descriptor[$index]['label'] = __('On', 'blocksy');
			}

			if (
				(
					$single_meta['id'] === 'categories'
				) && ! isset($single_meta['label'])
			) {
				$post_meta_descriptor[$index]['label'] = __('In', 'blocksy');
			}

			if ($post_meta_descriptor[$index]['enabled']) {
				$has_any_enabled_element = true;
			}
		}

		if (! $has_any_enabled_element) {
			return '';
		}

		$default_date_format = get_option('date_format', '');

		if (! empty($args['class'])) {
			$args['class'] = ' ' . $args['class'];
		}

		// Author ID
		global $post;
		$user_id = $post->post_author;

		global $authordata;

		if (! $authordata) {
			$authordata = get_userdata($user_id);
		}

		$container_attr = array_merge([
			'class' => 'entry-meta' . $args['class'],
			'data-type' => $args['meta_type'] . ':' . $args['meta_divider']
		], $args['attr']);

		ob_start();

		foreach ($post_meta_descriptor as $single_meta) {
			if (! $single_meta['enabled']) {
				continue;
			}

			do_action(
				'blocksy:post-meta:render-meta',
				$single_meta['id'],
				$single_meta,
				$args
			);

			if (
				$single_meta['id'] === 'author'
				&&
				get_the_author()
			) { ?><li class="meta-author" <?php echo blocksy_schema_org_definitions('author') ?>><?php
					if ($single_meta['has_author_avatar'] === 'yes') {
						echo blocksy_simple_image(
							apply_filters(
								'blocksy:post-meta:author:author-avatar-url',
								get_avatar_url(
									get_the_author_meta('ID'),
									[
										'size' => intval($single_meta['avatar_size']) * 2
									]
								)
							),
							[
								'tag_name' => 'a',
								'aspect_ratio' => false,
								'suffix' => 'static',
								'html_atts' => [
									'href' => get_author_posts_url(get_the_author_meta('ID')),
									'tabindex' => -1
								],
								'has_default_alt' => false,
								'img_atts' => [
									'width' => intval($single_meta['avatar_size']),
									'height' => intval($single_meta['avatar_size']),
									'style' => 'height:' . intval($single_meta['avatar_size']) . 'px',
									'alt' => blocksy_get_avatar_alt_for(get_the_author_meta('ID'))
								],
							]
						);
					}

					$meta_label = $args['prefix'] ? blocksy_translate_dynamic(
						$single_meta['label'],
						$args['prefix'] . '_' . $single_meta['id'] . '_label'
					) : $single_meta['label'];

					if (
						$args['meta_type'] === 'label'
						&&
						!empty($meta_label)
					) {
						echo '<span>' . esc_html($meta_label) . '</span>';
					}

					if ($args['meta_type'] === 'icons' || $args['force_icons']) {
						$icon = '<svg width="13" height="13" viewBox="0 0 15 15"><path d="M13.6,1.4c-1.9-1.9-4.9-1.9-6.8,0L2.2,6C2.1,6.1,2,6.3,2,6.5V12l-1.8,1.8c-0.3,0.3-0.3,0.7,0,1C0.3,14.9,0.5,15,0.7,15s0.3-0.1,0.5-0.2L3,13h5.5c0.2,0,0.4-0.1,0.5-0.2l2.7-2.7c0,0,0,0,0,0l1.9-1.9C15.5,6.3,15.5,3.3,13.6,1.4z M8.2,11.6H4.4l1.4-1.4h3.9L8.2,11.6z M12.6,7.2L11,8.9H7.1l3.6-3.6c0.3-0.3,0.3-0.7,0-1C10.4,4,10,4,9.7,4.3L5,9.1c0,0,0,0,0,0l-1.6,1.6V6.8l4.4-4.4c1.3-1.3,3.5-1.3,4.8,0C14,3.7,14,5.9,12.6,7.2C12.6,7.2,12.6,7.2,12.6,7.2z"/></svg>';

						if (function_exists('blc_get_icon')) {
							$icon = blc_get_icon([
								'icon_descriptor' => blocksy_akg('icon', $single_meta, [
									'icon' => 'blc blc-feather'
								]),
								'icon_container' => false
							]);
						}

						echo $icon;
					}

					global $authordata;

					echo blocksy_html_tag('a', array_merge([
						'class' => 'ct-meta-element-author',
						'href' => esc_url(get_author_posts_url($authordata->ID, $authordata->user_nicename)),
						/* translators: %s: Author's display name. */
						'title' => esc_attr(sprintf(__('Posts by %s', 'blocksy'), get_the_author())),
						'rel' => 'author',
					], blocksy_schema_org_definitions('author_url', [
						'array' => true
					]), (
						$args['meta_type'] === 'label' ? [
							// 'data-label' => $meta_label
						] : []
					)), blocksy_html_tag(
						'span',
						blocksy_schema_org_definitions('author_name', ['array' => true]),
						get_the_author()
					));

				?></li><?php }

				if ($single_meta['id'] === 'post_date') {
					?><li class="meta-date" <?php echo blocksy_schema_org_definitions('publish_date') ?>><?php
						if ($args['meta_type'] === 'icons' || $args['force_icons']) {
							$icon = '<svg width="13" height="13" viewBox="0 0 15 15"><path d="M7.5,0C3.4,0,0,3.4,0,7.5S3.4,15,7.5,15S15,11.6,15,7.5S11.6,0,7.5,0z M7.5,13.6c-3.4,0-6.1-2.8-6.1-6.1c0-3.4,2.8-6.1,6.1-6.1c3.4,0,6.1,2.8,6.1,6.1C13.6,10.9,10.9,13.6,7.5,13.6z M10.8,9.2c-0.1,0.2-0.4,0.4-0.6,0.4c-0.1,0-0.2,0-0.3-0.1L7.2,8.1C7,8,6.8,7.8,6.8,7.5V4c0-0.4,0.3-0.7,0.7-0.7S8.2,3.6,8.2,4v3.1l2.4,1.2C10.9,8.4,11,8.8,10.8,9.2z"/></svg>';

							if (function_exists('blc_get_icon')) {
								$icon = blc_get_icon([
									'icon_descriptor' => blocksy_akg('icon', $single_meta, [
										'icon' => 'blc blc-clock'
									]),
									'icon_container' => false
								]);
							}

							echo $icon;
						}

						$meta_label = $args['prefix'] ? blocksy_translate_dynamic(
							$single_meta['label'],
							$args['prefix'] . '_' . $single_meta['id'] . '_label'
						) : $single_meta['label'];

						if (
							$args['meta_type'] === 'label'
							&&
							!empty($meta_label)
						) {
							echo '<span>' . esc_html($meta_label) . '</span>';
						}

						$date_format = $single_meta['date_format'];

						if ($single_meta['date_format_source'] === 'default') {
							$date_format = $default_date_format;
						}

						echo blocksy_html_tag(
							'time',
							array_merge([
								'class' => 'ct-meta-element-date',
								'datetime' => get_the_date('c')
							], (
								($args['meta_type'] === 'label') ? ([
									// 'data-label' => $meta_label
								]) : []
							), (
								is_customize_preview() ? [
									'data-default-format' => $default_date_format,
									'data-date' => get_the_date('c')
								] : []
							)),
							esc_html(get_the_date($date_format))
						);
				?></li><?php }

			if ($single_meta['id'] === 'updated_date') {
				?><li class="meta-updated-date" <?php echo blocksy_schema_org_definitions('modified_date') ?>><?php
						if ($args['meta_type'] === 'icons' || $args['force_icons']) {
							$icon = '<svg width="13" height="13" viewBox="0 0 15 15"><path d="M7.5,0C3.4,0,0,3.4,0,7.5S3.4,15,7.5,15S15,11.6,15,7.5S11.6,0,7.5,0z M7.5,13.6c-3.4,0-6.1-2.8-6.1-6.1c0-3.4,2.8-6.1,6.1-6.1c3.4,0,6.1,2.8,6.1,6.1C13.6,10.9,10.9,13.6,7.5,13.6z M8.2,4v3.5C8.2,7.8,8,8,7.8,8.1L5.1,9.5C5,9.5,4.9,9.5,4.8,9.5c-0.3,0-0.5-0.1-0.6-0.4C4,8.8,4.1,8.4,4.5,8.3l2.4-1.2V4c0-0.4,0.3-0.7,0.7-0.7S8.2,3.6,8.2,4z"/></svg>';

							if (function_exists('blc_get_icon')) {
								$icon = blc_get_icon([
									'icon_descriptor' => blocksy_akg('icon', $single_meta, [
										'icon' => 'blc blc-clock'
									]),
									'icon_container' => false
								]);
							}

							echo $icon;
						}

						$meta_label = $args['prefix'] ? blocksy_translate_dynamic(
							$single_meta['label'],
							$args['prefix'] . '_' . $single_meta['id'] . '_label'
						) : $single_meta['label'];

						if (
							$args['meta_type'] === 'label'
							&&
							!empty($meta_label)
						) {
							echo '<span>' . esc_html($meta_label) . '</span>';
						}

						$date_format = $single_meta['date_format'];

						if ($single_meta['date_format_source'] === 'default') {
							$date_format = $default_date_format;
						}

						$proper_updated_date = intval(get_the_modified_date('U')) < intval(
							get_the_date('U')
						) ? get_the_date($date_format) : get_the_modified_date($date_format);

						$proper_updated_date_initial = intval(get_the_modified_date('U')) < intval(
							get_the_date('U')
						) ? get_the_date('c') : get_the_modified_date('c');


						echo blocksy_html_tag(
							'time',

							array_merge([
								'class' => 'ct-meta-element-date',
								'datetime' => $proper_updated_date_initial
							], (
								$args['meta_type'] === 'label' ? [
									// 'data-label' => $meta_label
								] : []
							), (
								is_customize_preview() ? [
									'data-default-format' => $default_date_format,
									'data-date' => $proper_updated_date_initial
								] : []
							)),

							esc_html($proper_updated_date)
						);
				?></li><?php }

				if ($single_meta['id'] === 'comments' && get_comments_number() > 0) {
					?><li class="meta-comments"><?php
					if ($args['meta_type'] === 'icons' || $args['force_icons']) {
						$icon = '<svg width="13" height="13" viewBox="0 0 15 15"><path d="M13.7,14.8L10.9,12H2.2C1,12,0,11,0,9.8l0-7.5C0,1,1,0,2.2,0l10.5,0C14,0,15,1,15,2.2v12c0,0.3-0.2,0.6-0.5,0.7c-0.1,0-0.2,0.1-0.3,0.1C14.1,15,13.9,14.9,13.7,14.8zM2.2,1.5c-0.4,0-0.8,0.3-0.8,0.8v7.5c0,0.4,0.3,0.8,0.8,0.8h9c0.2,0,0.4,0.1,0.5,0.2l1.7,1.7V2.2c0-0.4-0.3-0.8-0.8-0.8H2.2z"/></svg>';

						if (function_exists('blc_get_icon')) {
							$icon = blc_get_icon([
								'icon_descriptor' => blocksy_akg('icon', $single_meta, [
									'icon' => 'blc blc-comments'
								]),
								'icon_container' => false
							]);
						}

						echo $icon;
					}

				?><a href="<?php echo esc_attr(get_permalink()); ?>#comments"><?php
						// translators: text for one review
						$singular_text = __('1 Comment', 'blocksy');
						// translators: % refers to the number of comments, when more than 1
						$plural_text = __('% Comments', 'blocksy');

						if ( get_post_type() === 'product' ) {
							// translators: text for one review
							$singular_text = __('1 Review', 'blocksy');
							// translators: % refers to the number of reviews, when more than 1
							$plural_text = __('% Reviews', 'blocksy');
						}

						if ($args['meta_type'] === 'icons' && !$args['force_icons']) {
							$singular_text = '1';
							$plural_text = '%';
						}

						echo wp_kses_post(get_comments_number_text(
							'',
							$singular_text,
							$plural_text
						));
				?></a></li><?php }

			$maybe_taxonomy = null;

			if ($single_meta['id'] === 'categories') {
				$matching_taxonomy = blocksy_maybe_get_matching_taxonomy(
					get_post_type()
				);

				if ($matching_taxonomy) {
					$maybe_taxonomy = blocksy_akg('taxonomy', $single_meta, null);

					if (! $maybe_taxonomy) {
						$maybe_taxonomy = $matching_taxonomy;
					}
				}
			}

			if (
				$single_meta['id'] === 'categories'
				&&
				$maybe_taxonomy
				&&
				blocksy_get_categories_list([
					'taxonomy' => $maybe_taxonomy
				])
			) {
				if (! isset($single_meta['style'])) {
					$single_meta['style'] = 'simple';
				}

				$divider = '';

				if ($single_meta['style'] === 'simple') {
					$divider = ', ';
				}

				if ($single_meta['style'] === 'underline') {
					$divider = ' / ';
				}

				echo '<li class="meta-categories" data-type="' . esc_attr($single_meta['style']) . '">';

				if ($args['meta_type'] === 'icons' || $args['force_icons']) {
					$icon = '<svg width="13" height="13" viewBox="0 0 15 15"><path d="M14.4,1.2H0.6C0.3,1.2,0,1.5,0,1.9V5c0,0.3,0.3,0.6,0.6,0.6h0.6v7.5c0,0.3,0.3,0.6,0.6,0.6h11.2c0.3,0,0.6-0.3,0.6-0.6V5.6h0.6C14.7,5.6,15,5.3,15,5V1.9C15,1.5,14.7,1.2,14.4,1.2z M12.5,12.5h-10V5.6h10V12.5z M13.8,4.4H1.2V2.5h12.5V4.4z M5.6,7.5c0-0.3,0.3-0.6,0.6-0.6h2.5c0.3,0,0.6,0.3,0.6,0.6S9.1,8.1,8.8,8.1H6.2C5.9,8.1,5.6,7.8,5.6,7.5z"/></svg>';

					if (function_exists('blc_get_icon')) {
						$icon = blc_get_icon([
							'icon_descriptor' => blocksy_akg('icon', $single_meta, [
								'icon' => 'blc blc-box'
							]),
							'icon_container' => false
						]);
					}

					echo $icon;
				}

				if (
					$args['meta_type'] === 'label'
					&&
					! empty($single_meta['label'])
				) {
					echo '<span>' . esc_html($single_meta['label']) . '</span>';
				}

				echo blocksy_get_categories_list([
					'between' => $divider,
					'taxonomy' => $maybe_taxonomy
				]);

				echo '</li>';
			}
		}

		$to_return = ob_get_contents();

		ob_end_clean();

		if (empty(trim($to_return))) {
			return '';
		}

		ob_start();

		echo '<ul ' . blocksy_attr_to_html($container_attr) . ' ' . blocksy_schema_org_definitions('blog') . '>';

		/**
		 * Note to code reviewers: This line doesn't need to be escaped.
		 * Var $to_return used here has the value escaped properly.
		 */
		echo trim(preg_replace('/\s\s+/', ' ', apply_filters(
			'blocksy:post-meta:items',
			$to_return,
			$post_meta_descriptor,
			$args
		)));

		echo '</ul>';

		return ob_get_clean();
	}
}

if (! function_exists('blocksy_maybe_get_matching_taxonomy')) {
	function blocksy_maybe_get_matching_taxonomy($post_type, $is_category = true) {
		$category = $is_category ? 'category' : 'post_tag';

		if ($post_type === 'product') {
			$category = $is_category ? 'product_cat' : 'product_tag';
		}

		if (
			$post_type !== 'product'
			&&
			$post_type !== 'post'
		) {
			$taxonomies = array_values(array_diff(
				get_object_taxonomies($post_type),
				['post_format']
			));

			if (count($taxonomies) > 0) {
				$category = null;

				foreach ($taxonomies as $single_taxonomy) {
					$taxonomy_object = get_taxonomy($single_taxonomy);

					if (! $taxonomy_object->public) {
						continue;
					}

					if (
						$is_category && $taxonomy_object->hierarchical
						||
						! $is_category && ! $taxonomy_object->hierarchical
					) {
						$category = $single_taxonomy;
						break;
					}
				}
			} else {
				return null;
			}
		}

		if (! get_taxonomy($category)) {
			return null;
		}

		return $category;
	}
}

if (! function_exists('blocksy_get_the_term_list')) {
	function blocksy_get_the_term_list($args = []) {
		$args = wp_parse_args($args, [
			'post_id' => null,
			'taxonomy' => null,
			'before' => '',
			'sep' => '',
			'after' => '',
			'before_each' => '',
			'has_term_class' => true
		]);

		$terms = get_the_terms($args['post_id'], $args['taxonomy']);

		if (is_wp_error($terms)) {
			return '';
		}

		if (empty($terms)) {
			return false;
		}

		$links = [];

		foreach ($terms as $term) {
			$link = get_term_link($term, $args['taxonomy']);

			if (is_wp_error($link)) {
				return '';
			}

			$link_attr = [
				'href' => $link,
				'rel' => 'tag'
			];

			if ($args['has_term_class']) {
				$link_attr['class'] = 'ct-term-' . $term->term_id;
			}

			$links[] = blocksy_html_tag(
				'a',
				$link_attr,
				$args['before_each'] . $term->name
			);
		}

		return $args['before'] . implode($args['sep'], $links) . $args['after'];
	}
}

if (! function_exists('blocksy_get_categories_list')) {
	function blocksy_get_categories_list($args = []) {
		$args = wp_parse_args($args, [
			'between' => '',
			'taxonomy' => null,
			'before_each' => '',
			'has_term_class' => true
		]);

		global $post;

		if (get_post_type() === 'elementor_library') {
			return '';
		}

		if (get_post_type() === 'brizy_template') {
			return '';
		}

		$post_type = get_post_type($post);

		if (! $args['taxonomy']) {
			$args['taxonomy'] = blocksy_maybe_get_matching_taxonomy($post_type);
		}

		return blocksy_get_the_term_list([
			'post_id' => $post,
			'taxonomy' => $args['taxonomy'],
			'sep' => $args['between'],
			'before_each' => $args['before_each'],
			'has_term_class' => $args['has_term_class']
		]);
	}
}

function blocksy_post_meta_defaults($opts = [], $args = []) {
	$args = wp_parse_args(
		$args,
		[]
	);

	$defaults = [
		[
			'id' => 'author',
			'enabled' => false,
			'label' => __('By', 'blocksy'),
			'has_author_avatar' => 'no',
			'avatar_size' => 25
		],

		[
			'id' => 'post_date',
			'enabled' => false,
			'label' => __('On', 'blocksy'),
			'date_format_source' => 'default',
			'date_format' => 'M j, Y'
		],

		[
			'id' => 'updated_date',
			'enabled' => false,
			'label' => __('On', 'blocksy'),
			'date_format_source' => 'default',
			'date_format' => 'M j, Y'
		],

		[
			'id' => 'categories',
			'enabled' => false,
			'label' => __('In', 'blocksy'),
			'style' => 'simple'
		],

		[
			'id' => 'comments',
			'enabled' => false,
		]
	];

	$result = [];

	foreach ($defaults as $index => $single_meta) {
		$added = false;

		foreach ($opts as $single_opt) {
			if ($single_meta['id'] !== $single_opt['id']) {
				continue;
			}

			$future_layer = wp_parse_args($single_opt, $single_meta);

			if (! $future_layer['enabled']) {
				// continue;
			}

			$result[] = $future_layer;
			$added = true;
		}

		if (! $added) {
			$result[] = $single_meta;
		}
	}

	return $result;
}

