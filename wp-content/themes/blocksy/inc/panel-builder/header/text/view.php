<?php

if (! isset($device)) {
	$device = 'desktop';
}

$header_html_user_visibility = blocksy_akg(
	'user_visibility',
	$atts,
	[
		'logged_in' => true,
		'logged_out' => true,
	]
);

if (
	get_current_user_id() && ! $header_html_user_visibility['logged_in']
	||
	! get_current_user_id() && ! $header_html_user_visibility['logged_out']
) {
	return;
}

$class = 'ct-header-text';

if ($panel_type === 'header') {
	$visibility = blocksy_default_akg('visibility', $atts, [
		'tablet' => true,
		'mobile' => true,
	]);
} else {
	$visibility = blocksy_default_akg('footer_visibility', $atts, [
		'desktop' => true,
		'tablet' => true,
		'mobile' => true,
	]);
}

$class .= ' ' . blocksy_visibility_classes($visibility);

$text = do_shortcode(
	blocksy_translate_dynamic(
		blocksy_default_akg(
			'header_text',
			$atts,
			__('Sample text', 'blocksy')
		),
		'header:' . $section_id . ':' . $item_id . ':header_text'
	)
);

if (blocksy_default_akg('has_header_text_full_width', $atts, 'no') === 'yes') {
	$attr['data-width'] = 'stretch';
}

?>

<div
	class="<?php echo esc_attr($class) ?>"
	<?php echo blocksy_attr_to_html($attr) ?>>
	<div class="entry-content">
		<?php echo $text ?>
	</div>
</div>
