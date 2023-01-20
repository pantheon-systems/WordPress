<?php

if (get_theme_mod('emoji_scripts', 'yes') === 'yes') {
	call_user_func(
		'remove_action',
		'wp_head',
		'print_emoji_detection_script',
		7
	);

	call_user_func(
		'remove_action',
		'wp_print_styles',
		'print_emoji_styles'
	);

	call_user_func(
		'remove_action',
		'admin_print_scripts',
		'print_emoji_detection_script'
	);

	call_user_func(
		'remove_action',
		'admin_print_styles',
		'print_emoji_styles'
	);

	remove_filter('the_content_feed', 'wp_staticize_emoji');
	remove_filter('comment_text_rss', 'wp_staticize_emoji');
	remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

	add_filter('tiny_mce_plugins', function ($plugins) {
		if (is_array($plugins)) {
			return array_diff($plugins, array('wpemoji'));
		} else {
			return array();
		}
	});

	add_filter('wp_resource_hints', function ($urls, $relation_type) {
		if ('dns-prefetch' === $relation_type) {
			/** This filter is documented in wp-includes/formatting.php */
			$emoji_svg_url = apply_filters('emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/');

			$urls = array_diff($urls, array($emoji_svg_url));
		}

		return $urls;
	}, 10, 2);
}

