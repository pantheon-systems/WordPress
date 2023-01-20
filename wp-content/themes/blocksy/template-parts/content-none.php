<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Blocksy
 */

echo '<div class="entry-content">';

if (is_home() && current_user_can('publish_posts')) {
	printf(
		'<p>' . wp_kses(
			/* translators: 1: link to WP admin new post page. */
			__( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'blocksy' ),
			[
				'a' => [
					'href' => []
				]
			]
		) . '</p>',
		esc_url(admin_url('post-new.php'))
	);
} else {
	get_search_form();
}

echo '</div>';

