<?php
/**
 * This file includes a set of temporary fixes for known compatibility
 * issues with third-party plugins. These fixes likely should not to be
 * included with core merge.
 *
 * @package gutenberg
 * @since 1.3.0
 *
 * The goal is to provide a fix so
 * 1. users of the plugin can continue to use and test Gutenberg,
 * 2. provide a reference for developers of the plugin to work with, and
 * 3. provide reference for other plugin developers on how they might work
 *    with Gutenberg.
 */

/**
 * WPCOM markdown support causes issues when saving a Gutenberg post by
 * stripping out the <p> tags. This adds a filter prior to saving the post via
 * REST API to disable markdown support. Fixes markdown support provided by
 * plugins Jetpack, JP-Markdown, and WP Editor.MD
 *
 * @since 1.3.0
 *
 * @param  array $post      Post object which contains content to check for block.
 * @return array $post      Post object.
 */
function gutenberg_remove_wpcom_markdown_support( $post ) {
	if ( gutenberg_content_has_blocks( $post->post_content ) ) {
		remove_post_type_support( 'post', 'wpcom-markdown' );
	}
	return $post;
}
add_filter( 'rest_pre_insert_post', 'gutenberg_remove_wpcom_markdown_support' );
