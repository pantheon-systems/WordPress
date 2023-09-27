<?php

blocksy_get_variables_from_file(
	get_template_directory() . '/inc/panel-builder/header/menu/dynamic-styles.php',
	[],
	[
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'atts' => $atts,
		'root_selector' => $root_selector,
		'has_transparent_header' => isset($has_transparent_header) ? $has_transparent_header : false,
		'has_sticky_header' => isset($has_sticky_header) ? $has_sticky_header : false
	]
);
