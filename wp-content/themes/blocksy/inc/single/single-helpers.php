<?php

require get_template_directory() . '/inc/single/excerpt.php';
require get_template_directory() . '/inc/single/page-elements.php';
require get_template_directory() . '/inc/single/comments.php';

if ( ! function_exists( 'blocksy_get_author_id' ) ) {
	function blocksy_get_author_id() {
		$author_id = get_queried_object_id();

		if (is_singular()) {
			$author_id = get_the_author_meta('ID');
		}

		if (! $author_id) {
			$author = get_user_by('slug', get_query_var('author_name'));
			$author_id = $author->ID;
		}

		return $author_id;
	}
}

if (! function_exists('blocksy_post_uses_vc')) {
	function blocksy_post_uses_vc() {
		$post = get_post();
		return $post && preg_match('/vc_row/', $post->post_content);
	}
}

if (! function_exists('blocksy_get_content_style_default')) {
	function blocksy_get_content_style_default($prefix = null) {
		if (! $prefix) {
			$prefix = blocksy_manager()->screen->get_prefix();
		}

		$default_style = 'wide';

		if (
			$prefix === 'bbpress_single'
			||
			(
				$prefix === 'courses_single'
				&&
				function_exists('tutor')
			)
		) {
			$default_style = 'boxed';
		}

		return $default_style;
	}
}

/**
 * User social channels
 *
 * @param string $tooltip Should output tooltips.
 */
if (! function_exists('blocksy_author_social_channels')) {
	function blocksy_author_social_channels($args = []) {
		$args = wp_parse_args(
			$args,
			[
				'new_tab' => true
			]
		);

		$meta_ids = [
			'facebook',
			'linkedin',
			'dribbble',
			'user_url',
			'twitter',
			'instagram',
			'pinterest',
			'wordpress',
			'github',
			'medium',
			'youtube',
			'vimeo',
			'vkontakte',
			'odnoklassniki',
			'tiktok'
		];

		$outputs = [];

		$descriptor = [
			'facebook' => [
				'label' => 'Facebook icon',
				'icon' => '<svg class="ct-icon" width="12" height="12" viewBox="0 0 20 20"><path d="M15.2 7.2h-3.9V4.8c0-.7.5-1.2 1.2-1.2H15V0h-3C9.3 0 7.2 2.2 7.2 4.8v2.4H4.8v3.6h2.4V20h4.3v-9.2h3l.7-3.6z"/></svg>'
			],
			'linkedin' => [
				'label' => 'LinkedIn icon',
				'icon' => '<svg class="ct-icon" width="12" height="12" viewBox="0 0 20 20"><path d="M.1 5.8h4.2V20H.1V5.8zm18.4 1.8c-.8-1-2-1.4-3.5-1.4-1.9 0-3.2 1-4.2 2.4h-.1l-.2-2.8H7.2c.1 1.4 0 14.2 0 14.2h4.3v-8.9c.3-1.1 1.1-1.7 2.2-1.7 1.4 0 2.1 1 2.1 3V20h4.1v-8.1c-.1-1.9-.5-3.3-1.4-4.3zM2.2 0C1 0 0 1 0 2.2c0 1.2 1 2.2 2.2 2.2 1.2 0 2.2-1 2.2-2.2C4.3 1 3.4 0 2.2 0z"/></svg>'
			],
			'dribbble' => [
				'label' => 'Dribbble icon',
				'icon' => '<svg class="ct-icon" width="12" height="12" viewBox="0 0 20 20"><path d="M10 0C4.5 0 0 4.5 0 10s4.5 10 10 10 10-4.5 10-10S15.5 0 10 0m6.1 5.2c1 1.2 1.6 2.8 1.7 4.4-1.1-.2-2.2-.4-3.2-.4-.8 0-1.6.1-2.3.2-.2-.4-.3-.8-.5-1.2 1.6-.6 3.1-1.6 4.3-3m-6.1-3c1.8 0 3.5.6 4.9 1.7-1 1.2-2.4 2.1-3.8 2.7-1-2-2-3.4-2.7-4.3.5 0 1-.1 1.6-.1M6.6 3c.5.6 1.6 2 2.8 4.2-2.4.8-4.8.9-6.2.9h-.7C3 5.9 4.5 4 6.6 3m-4.4 7v-.1h.9c1.6 0 4.3-.1 7.1-1 .2.3.3.7.4 1-1.9.6-3.3 1.6-4.4 2.6-1 .9-1.7 1.9-2.2 2.5-1.1-1.3-1.8-3.1-1.8-5m7.8 7.8c-1.7 0-3.3-.6-4.6-1.5.3-.5.9-1.3 1.8-2.2 1-.9 2.3-1.9 4.1-2.5.6 1.7 1.1 3.6 1.5 5.7-.9.3-1.8.5-2.8.5m4.4-1.4c-.4-1.9-.9-3.7-1.4-5.2.5-.1 1-.1 1.6-.1.9 0 2 .1 3.1.4-.4 2-1.6 3.8-3.3 4.9"/></svg>'
			],
			'user_url' => [
				'label' => 'Website icon',
				'icon' => '<svg class="ct-icon" width="12" height="12" viewBox="0 0 20 20"><path d="M10 0C4.5 0 0 4.5 0 10s4.5 10 10 10 10-4.5 10-10S15.5 0 10 0zm6.9 6H14c-.4-1.8-1.4-3.6-1.4-3.6s2.8.8 4.3 3.6zM10 2s1.2 1.7 1.9 4H8.1C8.8 3.6 10 2 10 2zM2.2 12s-.6-1.8 0-4h3.4c-.3 1.8 0 4 0 4H2.2zm.9 2H6c.6 2.3 1.4 3.6 1.4 3.6C4.3 16.5 3.1 14 3.1 14zM6 6H3.1c1.6-2.8 4.3-3.6 4.3-3.6S6.4 4.2 6 6zm4 12s-1.3-1.9-1.9-4h3.8c-.6 2.1-1.9 4-1.9 4zm2.3-6H7.7s-.3-2 0-4h4.7c.3 1.8-.1 4-.1 4zm.3 5.6s1-1.8 1.4-3.6h2.9c-1.6 2.7-4.3 3.6-4.3 3.6zm1.7-5.6s.3-2.1 0-4h3.4c.6 2.2 0 4 0 4h-3.4z"/></svg>'
			],
			'twitter' => [
				'label' => 'Twitter icon',
				'icon' => '<svg class="ct-icon" width="12" height="12" viewBox="0 0 20 20"><path d="M20 3.8c-.7.3-1.5.5-2.4.6.8-.5 1.5-1.3 1.8-2.3-.8.5-1.7.8-2.6 1-.7-.8-1.8-1.3-3-1.3-2.3 0-4.1 1.8-4.1 4.1 0 .3 0 .6.1.9-3.4-.1-6.4-1.7-8.4-4.2C1 3.2.8 3.9.8 4.7c0 1.4.7 2.7 1.8 3.4-.6 0-1.2-.2-1.8-.5v.1c0 2 1.4 3.6 3.3 4-.3.1-.7.1-1.1.1-.3 0-.5 0-.8-.1.5 1.6 2 2.8 3.8 2.8-1.4 1.1-3.2 1.8-5.1 1.8-.3 0-.7 0-1-.1 1.8 1.2 4 1.8 6.3 1.8 7.5 0 11.6-6.3 11.6-11.6v-.5c1-.6 1.6-1.3 2.2-2.1z"/></svg>'
			],
			'instagram' => [
				'label' => 'Instagram icon',
				'icon' => '<svg class="ct-icon" width="12" height="12" viewBox="0 0 20 20"><path d="M13.3 10c0 1.8-1.5 3.3-3.3 3.3S6.7 11.8 6.7 10 8.2 6.7 10 6.7s3.3 1.5 3.3 3.3zm6.6-4.2v8.4c0 3.2-2.6 5.8-5.8 5.8H5.8C2.6 20 0 17.4 0 14.1V5.8C0 2.6 2.6 0 5.8 0h8.4c3.2 0 5.8 2.6 5.7 5.8zM15 10c0-2.8-2.2-5-5-5s-5 2.2-5 5 2.2 5 5 5 5-2.2 5-5zm1.6-5.8c0-.4-.4-.8-.8-.8s-.8.4-.8.8.4.8.8.8c.5 0 .8-.4.8-.8z"/></svg>'
			],
			'pinterest' => [
				'label' => 'Pinterest icon',
				'icon' => '<svg class="ct-icon" width="12" height="12" viewBox="0 0 20 20"><path d="M10 0C4.5 0 0 4.5 0 10c0 4.1 2.5 7.6 6 9.2 0-.7 0-1.5.2-2.3s1.3-5.4 1.3-5.4-.3-.6-.3-1.6c0-1.5.9-2.6 1.9-2.6.9 0 1.3.7 1.3 1.5 0 .9-.6 2.3-.9 3.5-.3 1.1.5 1.9 1.6 1.9 1.9 0 3.2-2.4 3.2-5.3 0-2.2-1.5-3.8-4.2-3.8-3 0-4.9 2.3-4.9 4.8 0 .9.3 1.5.7 2 .1.1.2.2.1.5 0 .2-.2.6-.2.8-.1.3-.3.3-.5.3-1.4-.6-2-2.1-2-3.8 0-2.8 2.4-6.2 7.1-6.2 3.8 0 6.3 2.8 6.3 5.7 0 3.9-2.2 6.9-5.4 6.9-1.1 0-2.1-.6-2.4-1.2 0 0-.6 2.3-.7 2.7-.2.8-.6 1.5-1 2.1.9.2 1.8.3 2.8.3 5.5 0 10-4.5 10-10S15.5 0 10 0z"/></svg>'
			],
			'wordpress' => [
				'label' => 'WordPress icon',
				'icon' => '<svg class="ct-icon" width="12" height="12" viewBox="0 0 20 20"><path d="M1.9 4.1C3.7 1.6 6.7 0 10 0c2.4 0 4.6.9 6.3 2.3-.7.2-1.2 1-1.2 1.7 0 .9.5 1.6 1 2.4.5.7.9 1.6.9 2.9 0 .9-.3 2-.8 3.4l-1 3.5-3.8-11.3c.6 0 1.2-.1 1.2-.1.6 0 .5-.8 0-.8 0 0-1.7.1-2.8.1-1 0-2.7-.1-2.7-.1-.6 0-.7.8-.1.8 0 0 .5.1 1.1.1l1.6 4.4-2.3 6.8L3.7 4.9c.6 0 1.2-.1 1.2-.1.5 0 .4-.8-.1-.8 0 0-1.7.1-2.9.1.1 0 .1 0 0 0zM.8 6.2C.3 7.4 0 8.6 0 10c0 3.9 2.2 7.2 5.4 8.9L.8 6.2zm9.4 4.5l-3 8.9c.9.3 1.8.4 2.8.4 1.2 0 2.3-.2 3.4-.6l-3.2-8.7zm9-4.6c0 1-.2 2.2-.8 3.6l-3 8.8c2.8-1.8 4.6-4.9 4.6-8.4 0-1.5-.3-2.8-.8-4z"/></svg>'
			],
			'github' => [
				'label' => 'GitHub icon',
				'icon' => '<svg class="ct-icon" width="12" height="12" viewBox="0 0 20 20"><path d="M8.9.4C4.3.9.6 4.6.1 9.1c-.5 4.7 2.2 8.9 6.3 10.5.3.1.6-.1.6-.5v-1.6s-.4.1-.9.1c-1.4 0-2-1.2-2.1-1.9-.1-.4-.3-.7-.6-1-.3-.1-.4-.1-.4-.2 0-.2.3-.2.4-.2.6 0 1.1.7 1.3 1 .5.8 1.1 1 1.4 1 .4 0 .7-.1.9-.2.1-.7.4-1.4 1-1.8-2.3-.5-4-1.8-4-4 0-1.1.5-2.2 1.2-3-.1-.2-.2-.7-.2-1.4 0-.4 0-1 .3-1.6 0 0 1.4 0 2.8 1.3.5-.2 1.2-.3 1.9-.3s1.4.1 2 .3c1.3-1.3 2.8-1.3 2.8-1.3.2.6.2 1.2.2 1.6 0 .8-.1 1.2-.2 1.4.7.8 1.2 1.8 1.2 3 0 2.2-1.7 3.5-4 4 .6.5 1 1.4 1 2.3v2.6c0 .3.3.6.7.5 3.7-1.5 6.3-5.1 6.3-9.3 0-6-5.1-10.7-11.1-10z"/></svg>'
			],
			'medium' => [
				'label' => 'Medium icon',
				'icon' => '<svg class="ct-icon" width="12" height="12" viewBox="0 0 20 20"><path d="M2.4 5.3c0-.2-.1-.5-.3-.7L.3 2.4v-.3H6l4.5 9.8 3.9-9.8H20v.3l-1.6 1.5c-.1.1-.2.3-.2.4v11.2c0 .2 0 .3.2.4l1.6 1.5v.3h-7.8v-.3l1.6-1.6c.2-.2.2-.2.2-.4V6.5L9.4 17.9h-.6L3.6 6.5v7.6c0 .3.1.6.3.9L6 17.6v.3H0v-.3L2.1 15c.2-.2.3-.6.3-.9V5.3z"/></svg>'
			],
			'youtube' => [
				'label' => 'YouTube icon',
				'icon' => '<svg class="ct-icon" width="12" height="12" viewBox="0 0 20 20"><path d="M18.9 11.9L4.7 19.8c-.2.2-.6.2-1 .2-1 0-2.1-.8-2.1-2.1V2.1C1.6 1 2.4 0 3.7 0c.4 0 .6 0 1 .2l14.2 7.9c1 .6 1.5 1.7.8 2.7-.2.5-.6.9-.8 1.1z"/></svg>'
			],
			'vimeo' => [
				'label' => 'Vimeo icon',
				'icon' => '<svg class="ct-icon" width="12" height="12" viewBox="0 0 20 20"><path d="M20 5.3c-.1 1.9-1.4 4.6-4.1 8-2.7 3.5-5 5.3-6.9 5.3-1.2 0-2.2-1.1-3-3.2-1.5-5.7-2.2-9.1-3.5-9.1-.2 0-.7.3-1.6.9L0 6c2.3-2 4.5-4.3 5.9-4.4 1.6-.2 2.5.9 2.9 3.2 1.3 8.1 1.8 9.3 4.2 5.7.8-1.3 1.3-2.3 1.3-3 .2-2-1.6-1.9-2.8-1.4 1-3.2 2.9-4.8 5.6-4.7 2 0 3 1.3 2.9 3.9z"/></svg>'
			],
			'vkontakte' => [
				'label' => 'Vkontakte icon',
				'icon' => '<svg class="ct-icon" width="12" height="12" viewBox="0 0 20 20"><path d="M19.3 4.7H16c-.3 0-.5.1-.6.4 0 0-1.3 2.4-1.7 3.2-1.1 2.2-1.8 1.5-1.8.5V5.3c0-.6-.5-1.1-1.1-1.1H8.2c-.6 0-1.3.3-1.7.8 0 0 1.2-.2 1.2 1.5v2.6c0 .4-.3.7-.7.7-.2 0-.4-.1-.6-.2-1-1.4-1.8-2.9-2.5-4.5.1-.2-.2-.4-.4-.4H.6c-.4 0-.6.2-.6.5v.2c.9 2.5 4.8 10.2 9.2 10.2H11c.4 0 .7-.3.7-.7v-1.1c0-.4.3-.7.7-.7.2 0 .4.1.5.2l2.2 2.1c.2.2.5.3.7.3h2.9c1.4 0 1.4-1 .6-1.7-.5-.5-2.5-2.6-2.5-2.6-.3-.4-.4-.9-.1-1.3.6-.8 1.7-2.2 2.1-2.8.9-.9 2-2.6.5-2.6z"/></svg>'
			],
			'odnoklassniki' => [
				'label' => 'Odnoklassniki icon',
				'icon' => '<svg class="ct-icon" width="12" height="12" viewBox="0 0 20 20"><path d="M10 10c2.8 0 5-2.2 5-5s-2.2-5-5-5-5 2.2-5 5 2.3 5 5 5zm0-7.5c1.4 0 2.5 1.1 2.5 2.5S11.4 7.5 10 7.5 7.5 6.4 7.5 5 8.6 2.5 10 2.5zM14.5 13c-1.1.7-2.5 1-3.5 1.1l.8.8 2.9 2.9c1.1 1.1-.7 2.9-1.8 1.8L10 16.7l-2.9 2.9c-1.1 1-2.9-.7-1.8-1.8l2.9-2.9.8-.8c-1-.1-2.4-.4-3.5-1.1-1.4-.8-2-1.4-1.4-2.4.3-.6 1.1-1 2.4-.3 0 0 1.4 1.1 3.6 1.1s3.6-1.1 3.6-1.1c1-.8 1.8-.3 2.1.3.5 1 0 1.5-1.3 2.4z"/></svg>'
			],
			'tiktok' => [
				'label' => 'TikTok icon',
				'icon' => '<svg class="ct-icon" width="12" height="12" viewBox="0 0 20 20"><path d="M18.2 4.5c-2.3-.2-4.1-1.9-4.4-4.2V0h-3.4v13.8c0 1.4-1.2 2.6-2.8 2.6-1.4 0-2.6-1.1-2.6-2.6s1.1-2.6 2.6-2.6h.2l.5.1V7.5h-.7c-3.4 0-6.2 2.8-6.2 6.2S4.2 20 7.7 20s6.2-2.8 6.2-6.2v-7c1.1 1.1 2.4 1.6 3.9 1.6h.8V4.6l-.4-.1z"/></svg>'
			]
		];

		foreach ($meta_ids as $network_id) {
			$meta_value = get_the_author_meta($network_id);

			if (! $meta_value) {
				continue;
			}

			$attr = [
				'href' => esc_url($meta_value),
				'aria-label' => $descriptor[$network_id]['label']
			];

			$attr['rel'] = 'noopener';

			if ($args['new_tab']) {
				$attr['rel'] = 'noopener noreferrer nofollow';
				$attr['target'] = '_blank';
			}

			$outputs[] = blocksy_html_tag(
				'a',
				$attr,
				$descriptor[$network_id]['icon']
			);
		}

		if (empty($outputs)) {
			return;
		}

		echo blocksy_html_tag(
			'div',
			[
				'class' => 'author-box-social'
			],
			implode(' ', $outputs)
		);
	}
}

if (! function_exists('blocksy_author_meta_elements')) {
function blocksy_author_meta_elements($args = []) {
	if (! is_author()) {
		return;
	}

	$args = wp_parse_args(
		$args,
		[
			'value' => [
				'joined' => false,
				'articles_count' => false,
				'comments' => false
			],

			'attr' => []
		]
	);

	if (
		! $args['value']['joined']
		&&
		! $args['value']['articles_count']
		&&
		! $args['value']['comments']
	) {
		return;
	}

	$user_data = get_userdata(blocksy_get_author_id());

	$joined_date = '';

	if ($user_data && isset($user_data->user_registered)) {
		$joined_date = date_i18n(
			get_option('date_format', ''),
			strtotime($user_data->user_registered)
		);
	}

	$comments_count = get_comments([
		'type' => '',
		'user_id' => blocksy_get_author_id(),
		'count' => true,
	]);

	$posts_count = count_user_posts(
		blocksy_get_author_id(),
		array_merge(
			['post'],
			blocksy_manager()->post_types->get_supported_post_types()
		)
	);

	$container_attr = array_merge([
		'class' => 'entry-meta',
		'data-type' => 'simple:slash'
	], $args['attr']);

	?>

		<ul <?php echo blocksy_attr_to_html($container_attr) ?>>
			<?php if ($args['value']['joined'] && ! empty($joined_date)) { ?>
				<li class="meta-date"><?php echo esc_html(__( 'Joined', 'blocksy' )); ?>:&nbsp;<?php echo esc_html($joined_date) ?></li>
			<?php } ?>

			<?php if ($args['value']['articles_count']) { ?>
				<li class="meta-articles"><?php echo esc_html(__( 'Articles', 'blocksy' )); ?>:&nbsp;<?php echo esc_html($posts_count) ?></li>
			<?php } ?>

			<?php if ($args['value']['comments'] && intval($comments_count) > 0) { ?>
				<li class="meta-comments"><?php echo esc_html(__( 'Comments', 'blocksy' )); ?>:&nbsp;<?php echo esc_html($comments_count) ?></li>
			<?php } ?>
		</ul>

	<?php
}
}

/**
 * Output author box.
 */
if (! function_exists('blocksy_author_box')) {
function blocksy_author_box() {
	$prefix = blocksy_manager()->screen->get_prefix();

	$type = get_theme_mod($prefix . '_single_author_box_type', 'type-2');

	$wrapper_tag = get_theme_mod($prefix . '_single_author_box_name_heading', 'h5');

	$has_author_box_social = get_theme_mod(
		$prefix . '_single_author_box_social',
		'yes'
	) === 'yes';

	$has_author_box_posts_count = get_theme_mod(
		$prefix . '_single_author_box_posts_count',
		'yes'
	) === 'yes';

	$class = 'author-box';

	$class .= ' ' . blocksy_visibility_classes(get_theme_mod(
		$prefix . '_author_box_visibility',
		[
			'desktop' => true,
			'tablet' => true,
			'mobile' => false,
		]
	));

	?>

	<div class="<?php echo esc_attr($class); ?>" data-type="<?php echo esc_attr($type); ?>">
		<?php
			echo blocksy_simple_image(
				apply_filters(
					'blocksy:single:author_box:author-avatar-url',
					get_avatar_url(
						blocksy_get_author_id(),
						[
							'size' => 120
						]
					)
				),
				[
					'tag_name' => 'a',
					'inner_content' => '
						<svg width="18px" height="13px" viewBox="0 0 20 15">
							<polygon points="14.5,2 13.6,2.9 17.6,6.9 0,6.9 0,8.1 17.6,8.1 13.6,12.1 14.5,13 20,7.5 "/>
						</svg>
					',
					'html_atts' => [
						'href' => get_author_posts_url(
							blocksy_get_author_id(),
							get_the_author_meta('user_nicename')
						),
					],

					'img_atts' => [
						'width' => 60,
						'height' => 60,
						'alt' => blocksy_get_avatar_alt_for(get_the_author_meta('ID'))
					],
				]
			);

		?>

		<section>
			<<?php echo $wrapper_tag ?> class="author-box-name">
				<?php the_author(); ?>
			</<?php echo $wrapper_tag ?>>

			<div class="author-box-bio">
				<?php //the_author_meta('description'); ?>

				<?php echo wp_kses_post(wpautop(get_the_author_meta('description'))); ?>
			</div>

			<?php
				if ($has_author_box_social) {
					blocksy_author_social_channels([
						'new_tab' => get_theme_mod(
							$prefix . '_single_author_box_social_link_target',
							'no'
						) === 'yes'
					]);
				}
			?>

			<?php if ($has_author_box_posts_count) {
				$posts_count = count_user_posts(
					blocksy_get_author_id(),
					array_merge(
						['post'],
						blocksy_manager()->post_types->get_supported_post_types()
					)
				);

				echo blocksy_html_tag(
					'a',
					[
						'href' => get_author_posts_url(blocksy_get_author_id()),
						'class' => 'ct-author-box-more'
					],
					esc_html(__('Articles', 'blocksy')) . ':&nbsp;' . $posts_count
				);
			} ?>
		</section>
	</div>

	<?php
}
}

if (! function_exists('blocksy_get_featured_image_source')) {
	function blocksy_get_featured_image_source() {
		static $result = null;

		$post_type = blocksy_manager()->post_types->is_supported_post_type();

		if ($post_type) {
			return [
				'prefix' => $post_type . '_single',
				'strategy' => 'customizer'
			];
		}

		if (blocksy_is_page()) {
			return [
				'strategy' => 'customizer',
				'prefix' => 'single_page'
			];
		}

		return [
			'strategy' => 'customizer',
			'prefix' => 'single_blog_post'
		];
	}
}

if (! function_exists('blocksy_get_featured_image_output')) {
	function blocksy_get_featured_image_output() {
		$featured_image_source = blocksy_get_featured_image_source();

		if (blocksy_akg_or_customizer(
			'has_featured_image',
			$featured_image_source,
			'no'
		) === 'no') {
			return '';
		}

		if (blocksy_default_akg(
			'disable_featured_image',
			blocksy_get_post_options(),
			'no'
		) === 'yes') {
		return '';
		}

		if (! has_post_thumbnail()) {
			return '';
		}

		$class = 'ct-featured-image';

		$class .= ' ' . blocksy_visibility_classes(
			blocksy_akg_or_customizer(
				'featured_image_visibility',
				$featured_image_source,
				[
					'desktop' => true,
					'tablet' => true,
					'mobile' => false,
				]
			)
		);

		if (blocksy_sidebar_position() === 'none') {
			$image_width = blocksy_akg_or_customizer(
				'featured_image_width',
				$featured_image_source,
				'default'
			);

			if ($image_width === 'wide') {
				$class .= ' alignwide';
			}

			if ($image_width === 'full') {
				$class .= ' alignfull';
			}
		}

		$maybe_figcaption = wp_get_attachment_caption(get_post_thumbnail_id());

		if (! empty($maybe_figcaption)) {
			$maybe_figcaption = '<figcaption>' . trim($maybe_figcaption) . '</figcaption>';
		} else {
			$maybe_figcaption = '';
		}

		return apply_filters(
			'post_thumbnail_html',
			blocksy_html_tag('figure', ['class' => $class], blocksy_image([
				'attachment_id' => get_post_thumbnail_id(),
				'post_id' => get_the_ID(),
				'ratio' => blocksy_akg_or_customizer(
					'featured_image_ratio',
					$featured_image_source,
					'original'
				),
				'size' => blocksy_akg_or_customizer(
					'featured_image_size',
					$featured_image_source,
					'full'
				),
				'lazyload' => get_theme_mod(
					'has_lazy_load_single_featured_image',
					'yes'
				) === 'yes'
			]) . $maybe_figcaption),
			get_the_ID(),
			get_post_thumbnail_id(),
			blocksy_akg_or_customizer(
				'featured_image_size',
				$featured_image_source,
				'full'
			),
			''
		);
	}
}

