<?php

$prefixes = [
	'single_blog_post',
	'blog',
	'categories',
	'search',
	'author',
	'single_page',
];

if (class_exists('WooCommerce')) {
	$prefixes[] = 'woo_categories';
	$prefixes[] = 'product';
}

if (class_exists('bbPress')) {
	$prefixes[] = 'bbpress_single';
}

if (function_exists('is_buddypress')) {
	$prefixes[] = 'buddypress_single';
}

$supported_post_types = blocksy_manager()->post_types->get_supported_post_types();

foreach ($supported_post_types as $cpt) {
	$prefixes[] = $cpt . '_single';
	$prefixes[] = $cpt . '_archive';
}

foreach ($prefixes as $prefix) {
	blocksy_theme_get_dynamic_styles([
		'name' => 'page-title/page-title',
		'css' => $css,
		'mobile_css' => $mobile_css,
		'tablet_css' => $tablet_css,
		'context' => $context,
		'chunk' => 'global',
		'prefix' => $prefix
	]);
}

