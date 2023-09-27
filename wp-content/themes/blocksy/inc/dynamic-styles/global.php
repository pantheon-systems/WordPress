<?php

if (! class_exists('Blocksy_Manager')) {
	return;
}

Blocksy_Manager::instance()->builder->dynamic_css('header', [
	'css' => $css,
	'mobile_css' => $mobile_css,
	'tablet_css' => $tablet_css,
	'context' => $context,
	'chunk' => 'global'
]);

Blocksy_Manager::instance()->builder->dynamic_css('footer', [
	'css' => $css,
	'mobile_css' => $mobile_css,
	'tablet_css' => $tablet_css,
	'context' => $context,
	'chunk' => 'global'
]);

blocksy_theme_get_dynamic_styles([
	'name' => 'global/typography',
	'css' => $css,
	'mobile_css' => $mobile_css,
	'tablet_css' => $tablet_css,
	'context' => $context,
	'chunk' => 'global'
]);

blocksy_theme_get_dynamic_styles([
	'name' => 'global/background',
	'css' => $css,
	'mobile_css' => $mobile_css,
	'tablet_css' => $tablet_css,
	'context' => $context,
	'chunk' => 'global'
]);

blocksy_theme_get_dynamic_styles([
	'name' => 'page-title/all',
	'css' => $css,
	'mobile_css' => $mobile_css,
	'tablet_css' => $tablet_css,
	'context' => $context,
	'chunk' => 'global'
]);

blocksy_theme_get_dynamic_styles([
	'name' => 'global/comments',
	'css' => $css,
	'mobile_css' => $mobile_css,
	'tablet_css' => $tablet_css,
	'context' => $context,
	'chunk' => 'global',
	'prefixes' => blocksy_manager()->screen->get_single_prefixes()
]);

blocksy_theme_get_dynamic_styles([
	'name' => 'global/pagination',
	'css' => $css,
	'mobile_css' => $mobile_css,
	'tablet_css' => $tablet_css,
	'context' => $context,
	'chunk' => 'global',
	'prefixes' => blocksy_manager()->screen->get_archive_prefixes([
		'has_woocommerce' => true
	])
]);

blocksy_theme_get_dynamic_styles([
	'name' => 'global/posts-listing',
	'css' => $css,
	'mobile_css' => $mobile_css,
	'tablet_css' => $tablet_css,
	'context' => $context,
	'chunk' => 'global',
	'prefixes' => blocksy_manager()->screen->get_archive_prefixes([
		'has_categories' => true,
		'has_author' => true,
		'has_search' => true
	])
]);

if (class_exists('WooCommerce')) {
	blocksy_theme_get_dynamic_styles([
		'name' => 'global/woocommerce',
		'css' => $css,
		'mobile_css' => $mobile_css,
		'tablet_css' => $tablet_css,
		'context' => $context,
		'chunk' => 'global'
	]);
}

blocksy_theme_get_dynamic_styles([
	'name' => 'global/forms',
	'css' => $css,
	'mobile_css' => $mobile_css,
	'tablet_css' => $tablet_css,
	'context' => $context,
	'chunk' => 'global'
]);

blocksy_theme_get_dynamic_styles([
	'name' => 'global/all',
	'css' => $css,
	'mobile_css' => $mobile_css,
	'tablet_css' => $tablet_css,
	'context' => $context,
	'chunk' => 'global'
]);

blocksy_theme_get_dynamic_styles([
	'name' => 'global/single-elements',
	'css' => $css,
	'mobile_css' => $mobile_css,
	'tablet_css' => $tablet_css,
	'context' => $context,
	'chunk' => 'global',
	'prefixes' => blocksy_manager()->screen->get_single_prefixes()
]);

$supported_post_types = blocksy_manager()->post_types->get_supported_post_types();
$supported_post_types[] = 'single_blog_post';
$supported_post_types[] = 'single_page';

if (function_exists('is_product')) {
	$supported_post_types[] = 'product';
}

if (class_exists('bbPress')) {
	$supported_post_types[] = 'bbpress';
}

if (function_exists('tutor')) {
	$supported_post_types[] = 'courses_archive';
}

if (function_exists('is_buddypress')) {
	$supported_post_types[] = 'buddypress';
}

foreach ($supported_post_types as $post_type) {
	if (
		$post_type !== 'single_blog_post'
		&&
		$post_type !== 'single_page'
		&&
		$post_type !== 'product'
		&&
		$post_type !== 'courses_archive'
	) {
		$post_type .= '_single';
	}

	blocksy_theme_get_dynamic_styles([
		'name' => 'global/single-content',
		'css' => $css,
		'mobile_css' => $mobile_css,
		'tablet_css' => $tablet_css,
		'context' => $context,
		'chunk' => 'global',
		'prefix' => $post_type,
	]);
}

