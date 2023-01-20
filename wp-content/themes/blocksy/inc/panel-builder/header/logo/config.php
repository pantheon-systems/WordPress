<?php

$config = [
	'name' => __('Logo', 'blocksy'),
	// 'clone' => true,

	'typography_keys' => ['siteTitle', 'siteTagline'],

	'selective_refresh' => [
		'logo_type',
		'blogname',
		'custom_logo',
		'transparent_logo',
		'sticky_logo',
		'has_mobile_logo',
		'mobile_header_logo',
		'has_site_title',
		'has_tagline',
		'site_description_wrapper',
		'site_title_custom_tag',
		'site_title_wrapper'
	],

	'translation_keys' => [
		['key' => 'blogname'],
		['key' => 'blogdescription']
	]
];
