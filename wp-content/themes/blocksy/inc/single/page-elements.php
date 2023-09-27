<?php

if (! function_exists('blocksy_has_comments')) {
	function blocksy_has_comments() {
		$prefix = blocksy_manager()->screen->get_prefix();

		$has_comments = get_theme_mod($prefix . '_has_comments', 'yes');

		if ($has_comments === 'yes') {
			return comments_open() || get_comments_number();
		}

		return false;
	}
}

if (! function_exists('blocksy_display_page_elements')) {
function blocksy_display_page_elements($location = null) {
	$prefix = blocksy_manager()->screen->get_prefix();

	$has_related_posts = get_theme_mod(
		$prefix . '_has_related_posts',
		'no'
	) === 'yes' && (
		blocksy_default_akg(
			'disable_related_posts',
			blocksy_get_post_options(),
			'no'
		) !== 'yes'
	);

	$has_comments = get_theme_mod($prefix . '_has_comments', 'yes');

	$related_posts_location = get_theme_mod(
		$prefix . '_related_posts_containment',
		'separated'
	);
	$comments_location = null;

	if ($has_comments === 'yes') {
		$comments_location = get_theme_mod(
			$prefix . '_comments_containment',
			'separated'
		);
	}

	ob_start();

	if ($has_related_posts) {
		do_action('blocksy:single:related_posts:before');
		blocksy_related_posts($location);
		do_action('blocksy:single:related_posts:after');
	}

	$related_posts_output = ob_get_clean();

	if (
		(
			get_theme_mod($prefix . '_related_location', 'before') === 'before'
			||
			$comments_location !== $related_posts_location
		) && $has_related_posts && $related_posts_location === $location
	) {
		/**
		 * Note to code reviewers: This line doesn't need to be escaped.
		 * The var $related_posts_output used here escapes the value properly.
		 */
		echo $related_posts_output;
	}

	$container_class = 'ct-container';

	if (
		get_theme_mod(
			$prefix . '_comments_structure',
			'narrow'
		) === 'narrow'
	) {
		$container_class = 'ct-container-narrow';
	}

	if (
		$has_comments === 'yes'
		&&
		$comments_location === $location
		&&
		(comments_open() || get_comments_number())
	) {
		if ($location === 'separated') {
			echo '<div class="ct-comments-container">';
			echo '<div class="' . $container_class . '">';
		}

		comments_template();

		if ($location === 'separated') {
			echo '</div>';
			echo '</div>';
		}
	}

	if (
		get_theme_mod($prefix . '_related_location', 'before') === 'after'
		&&
		$comments_location === $related_posts_location
		&&
		$has_related_posts
		&&
		$related_posts_location === $location
	) {
		/**
		 * Note to code reviewers: This line doesn't need to be escaped.
		 * The var $related_posts_output used here escapes the value properly.
		 */
		echo $related_posts_output;
	}
}
}
