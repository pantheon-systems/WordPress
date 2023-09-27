<?php

$class = 'ct-footer-copyright';

$class = trim($class . ' ' . blocksy_visibility_classes(blocksy_default_akg(
	'footer_copyright_visibility',
	$atts,
	[
		'desktop' => true,
		'tablet' => true,
		'mobile' => true,
	]
)));

$theme = blocksy_get_wp_theme();

$text = str_replace(
	'{current_year}',
	date("Y"),
	blocksy_translate_dynamic(blocksy_default_akg(
		'copyright_text',
		$atts,
		apply_filters(
			'blocksy:footer:copyright:default-value',
			__(
				'Copyright &copy; {current_year} - WordPress Theme by {theme_author}',
				'blocksy'
			)
		)
	), 'footer:' . $section_id . ':copyright:copyright_text')
);

$text = str_replace(
	'{site_title}',
	get_bloginfo('name'),
	$text
);

$text = do_shortcode(str_replace(
	'{theme_author}',
	blocksy_html_tag(
		'a',
		[
			'href' => $theme->get('AuthorURI')
		],
		$theme->get('Author')
	),
	$text
));

?>

<div
	class="<?php echo esc_attr($class) ?>"
	<?php echo blocksy_attr_to_html($attr) ?>>

	<?php echo $text ?>
</div>
