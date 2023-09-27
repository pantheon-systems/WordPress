<?php

if (! function_exists('blocksy_render_archive_card')) {
	function blocksy_render_archive_card($args = []) {
		$args = wp_parse_args(
			$args,
			[
				'prefix' => blocksy_manager()->screen->get_prefix([
					'allowed_prefixes' => ['blog'],
					'default_prefix' => 'blog'
				])
			]
		);

		$data_reveal_output = '';

		if (get_theme_mod(
			blocksy_manager()->screen->process_allowed_prefixes(
				$args['prefix'],
				[
					'allowed_prefixes' => ['blog'],
					'default_prefix' => 'blog'
				]
			) . '_has_posts_reveal',
			'no'
		) === 'yes') {
			$data_reveal_output = 'data-reveal="bottom:no"';
		}

		$card_render = apply_filters(
			'blocksy:posts-listing:cards:custom-output',
			null,
			$args['prefix']
		);

		if ($card_render) {
			$entry_open = '<article';
			$entry_open .= ' id="post-' . get_the_ID() . '"';
			$entry_open .= ' class="' . esc_attr(implode(' ', get_post_class($card_render['has_default_layout'] ? 'entry-card' : ''))) . '"';
			$entry_open .= ' ' . wp_kses_post($data_reveal_output);
			$entry_open .= '>';

			echo $entry_open;
			echo $card_render['output'];
			echo '</article>';

			return;
		}

		$blog_post_structure = blocksy_listing_page_structure([
			'prefix' => $args['prefix']
		]);

		$archive_order = apply_filters(
			'blocksy:posts-listing:archive-order',
			get_theme_mod(
				$args['prefix'] . '_archive_order',
				apply_filters('blocksy:posts-listing:archive-order:default', [
					[
						'id' => 'post_meta',
						'enabled' => true,
						'meta_elements' => blocksy_post_meta_defaults([
							[
								'id' => 'categories',
								'enabled' => true,
							],
						]),
					],

					[
						'id' => 'title',
						'enabled' => true,
					],

					[
						'id' => 'featured_image',
						'enabled' => true,
					],

					[
						'id' => 'excerpt',
						'enabled' => true,
					],

					[
						'id' => 'read_more',
						'enabled' => false,
					],

					[
						'id' => 'post_meta',
						'enabled' => true,
						'meta_elements' => blocksy_post_meta_defaults([
							[
								'id' => 'author',
								'enabled' => true,
							],

							[
								'id' => 'post_date',
								'enabled' => true,
							],

							[
								'id' => 'comments',
								'enabled' => true,
							],
						]),
					],

					[
						'id' => 'divider',
						'enabled' => false
					]
				], $args['prefix'])
			)
		);

		$featured_image_settings = null;
		$excerpt_settings = null;
		$title_settings = null;
		$read_more_settings = null;

		$last_enabled_component = null;

		if (! $archive_order) {
			$archive_order = [];
		}

		foreach (array_reverse($archive_order) as $index => $value) {
			if ($value['id'] === 'featured_image') {
				$featured_image_settings = $value;
			}

			if ($value['id'] === 'read_more') {
				$read_more_settings = $value;
			}

			if ($value['id'] === 'excerpt') {
				$excerpt_settings = $value;
			}

			if ($value['id'] === 'post_meta') {
				$post_meta_settings = $value;
			}

			if ($value['id'] === 'title') {
				$title_settings = $value;
			}
		}

		if ($blog_post_structure === 'simple') {
			foreach ($archive_order as $index => $value) {
				if ($value['id'] === 'featured_image') {
					unset($archive_order[$index]);
				}
			}

			array_unshift($archive_order, $featured_image_settings);
		}

		foreach (array_reverse($archive_order) as $index => $value) {
			if ($value['enabled'] && ! $last_enabled_component) {
				if (! isset($value['__id'])) {
					$id = blocksy_rand_md5();

					$archive_order[ count( $archive_order ) - 1 - $index ]['__id'] = $id;
					$value['__id'] = $id;
				}

				$last_enabled_component = $value['id'] . $value['__id'];
			}
		}

		$is_boundles = blocksy_default_akg('is_boundless', $featured_image_settings, 'yes');
		$featured_image_size = blocksy_default_akg('image_size', $featured_image_settings, 'medium_large');

		$has_title = false;

		foreach ($archive_order as $single_component) {
			if (! $single_component['enabled']) {
				continue;
			}

			if ($single_component['id'] === 'title') {
				$has_title = true;
			}
		}

		$featured_image_args = [
			'attachment_id' => apply_filters(
				'blocksy:archive:render-card-layers:featured_image:attachment_id',
				get_post_thumbnail_id()
			),
			'post_id' => get_the_ID(),
			'ratio' => blocksy_default_akg('thumb_ratio', $featured_image_settings, '4/3'),
			'tag_name' => 'a',
			'size' => $featured_image_size,
			'html_atts' => [
				'href' => esc_url(get_permalink()),
				'aria-label' => wp_strip_all_tags(get_the_title()),
			],
			'lazyload' => get_theme_mod(
				'has_lazy_load_archives_image',
				'yes'
			) === 'yes'
		];

		$card_type = get_theme_mod($args['prefix'] . '_card_type', 'boxed');

		if (
			$blog_post_structure === 'simple'
			&&
			$card_type === 'cover'
		) {
			$card_type = 'boxed';
		}

		if ($card_type === 'cover') {
			$featured_image_args['ratio'] = 'original';
		}

		if ($has_title) {
			$featured_image_args['html_atts']['tabindex'] = '-1';
		}

		if (
			$is_boundles === 'yes'
			&&
			$card_type === 'boxed'
			&&
			$blog_post_structure !== 'gutenberg'
		) {
			$featured_image_args['class'] = 'boundless-image';
		}

		$read_more_text = blocksy_translate_dynamic(blocksy_default_akg(
			'read_more_text',
			$read_more_settings,
			__('Read More', 'blocksy')
		), $args['prefix'] . '_archive_read_more_text');

		$read_more_arrow = '<svg width="17px" height="17px" viewBox="0 0 32 32"><path d="M 21.1875 9.28125 L 19.78125 10.71875 L 24.0625 15 L 4 15 L 4 17 L 24.0625 17 L 19.78125 21.28125 L 21.1875 22.71875 L 27.90625 16 Z "></path></svg>';

		if (blocksy_default_akg( 'read_more_arrow', $read_more_settings, 'no' ) === 'yes') {
			$read_more_text .= $read_more_arrow;
		}

		$button_type = blocksy_default_akg(
			'button_type',
			$read_more_settings,
			'background'
		);

		$outputs = null;

		$data_reveal_output = '';

		if (get_theme_mod(
			blocksy_manager()->screen->process_allowed_prefixes(
				$args['prefix'],
				[
					'allowed_prefixes' => ['blog'],
					'default_prefix' => 'blog'
				]
			) . '_has_posts_reveal',
			'no'
		) === 'yes') {
			$data_reveal_output = 'data-reveal="bottom:no"';
		}

		$entry_open = '<article';
		$entry_open .= ' id="post-' . get_the_ID() . '"';
		$entry_open .= ' class="' . esc_attr(implode(' ', get_post_class('entry-card'))) . '"';
		$entry_open .= ' ' . wp_kses_post($data_reveal_output);
		$entry_open .= '>';

		echo $entry_open;

		do_action('blocksy:loop:card:start');

		$had_a_meta = false;

		foreach ($archive_order as $single_component) {
			if (! $single_component['enabled']) {
				if (
					$blog_post_structure === 'simple'
					&&
					$single_component['id'] === 'featured_image'
				) {
					echo '<div class="card-content">';
				}

				continue;
			}

			$post_meta_default = null;

			if ('post_meta' === $single_component['id']) {
				$total_metas = [];

				foreach ($archive_order as $nested_single_component) {
					if ($nested_single_component['id'] === 'post_meta') {
						$total_metas[] = $nested_single_component;
					}
				}

				if (count($total_metas) > 1 && !$had_a_meta) {
					$post_meta_default = blocksy_post_meta_defaults([
						[
							'id' => 'categories',
							'enabled' => true,
						],
					]);
				} else {
					$post_meta_default = blocksy_post_meta_defaults([
						[
							'id' => 'author',
							'enabled' => true,
						],

						[
							'id' => 'post_date',
							'enabled' => true,
						],

						[
							'id' => 'comments',
							'enabled' => true,
						],
					]);
				}

				$had_a_meta = true;
			}

			$output = '';

			if ('post_meta' === $single_component['id']) {
				$output = blocksy_post_meta(
					blocksy_akg(
						'meta_elements',
						$single_component,
						$post_meta_default
					),
					[
						'meta_type' => blocksy_akg('meta_type', $single_component, 'simple'),
						'meta_divider' => blocksy_akg('meta_divider', $single_component, 'slash')
					]
				);
			}

			if (! $outputs) {
				$outputs = apply_filters('blocksy:archive:render-card-layers', [
					'title' => blocksy_entry_title(blocksy_default_akg('heading_tag', $title_settings, 'h2')),
					'featured_image' => (
						! get_the_post_thumbnail($featured_image_args['attachment_id'])
						&&
						! wp_get_attachment_image_url($featured_image_args['attachment_id'])
					) ? '' : apply_filters(
						'post_thumbnail_html',
						blocksy_image($featured_image_args),
						get_the_ID(),
						$featured_image_args['attachment_id'],
						$featured_image_args['size'],
						''
					),
					'excerpt' => blocksy_entry_excerpt(
						intval(
							blocksy_default_akg( 'excerpt_length', $excerpt_settings, '40' )
						),
						'entry-excerpt',
						null,
						blocksy_default_akg(
							'excerpt_source',
							$excerpt_settings,
							'excerpt'
						)
					),

					'read_more' => blocksy_html_tag(
						'a',
						[
							'class' => 'entry-button' . (
								$button_type === 'background' ? ' ct-button' : ''
							),
							'data-type' => $button_type,
							'data-alignment' => blocksy_default_akg( 'read_more_alignment', $read_more_settings, 'left' ),
							'href' => esc_url( get_permalink() )
						],
						$read_more_text
					),

					'divider' => '<div class="entry-divider"></div>'
				], $args['prefix']);
			}

			if (isset($outputs[$single_component['id']])) {
				$output = $outputs[$single_component['id']];
			}

			$output = apply_filters(
				'blocksy:archive:render-card-layer',
				$output,
				$single_component
			);

			if (! isset($single_component['__id'])) {
				$single_component['__id'] = '';
			}

			if (
				$card_type !== 'cover'
				&&
				$last_enabled_component === $single_component['id'] . $single_component['__id'] && (
					strpos($last_enabled_component, 'post_meta') !== false
					||
					strpos($last_enabled_component, 'featured_image') !== false
				)
			) {
				echo '<div class="ct-ghost"></div>';
			}

			/**
			 * Note to code reviewers: This line doesn't need to be escaped.
			 * Variabile $output used here escapes the value properly.
			 */
			echo $output;

			if (
				$blog_post_structure === 'simple'
				&&
				$single_component['id'] === 'featured_image'
			) {
				echo '<div class="card-content">';
			}

			if (
				$blog_post_structure === 'simple'
				&&
				$last_enabled_component === $single_component[
					'id'
				] . $single_component['__id']
			) {
				echo '</div>';
			}
		}

		do_action('blocksy:loop:card:end');

		echo '</article>';
	}
}


