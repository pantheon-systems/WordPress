<?php

$class = 'ct-header-search ct-toggle';

$item_visibility = blocksy_default_akg('header_search_visibility', $atts, [
	'tablet' => true,
	'mobile' => true,
]);

$class .= ' ' . blocksy_visibility_classes($item_visibility);


$label_class = 'ct-label';

$label_class .= ' ' . blocksy_visibility_classes(blocksy_akg('search_label_visibility', $atts,
	[
		'desktop' => false,
		'tablet' => false,
		'mobile' => false,
	]
));

$search_label = blocksy_akg('search_label', $atts, __('Search', 'blocksy'));
$search_label_position = blocksy_expand_responsive_value(
	blocksy_akg('search_label_position', $atts, 'left')
);

$icon = apply_filters(
	'blocksy:header:search:icon',
	'<svg class="ct-icon" aria-hidden="true" width="15" height="15" viewBox="0 0 15 15"><path d="M14.8,13.7L12,11c0.9-1.2,1.5-2.6,1.5-4.2c0-3.7-3-6.8-6.8-6.8S0,3,0,6.8s3,6.8,6.8,6.8c1.6,0,3.1-0.6,4.2-1.5l2.8,2.8c0.1,0.1,0.3,0.2,0.5,0.2s0.4-0.1,0.5-0.2C15.1,14.5,15.1,14,14.8,13.7z M1.5,6.8c0-2.9,2.4-5.2,5.2-5.2S12,3.9,12,6.8S9.6,12,6.8,12S1.5,9.6,1.5,6.8z"/></svg>'
);

if (function_exists('blc_get_icon') && isset($atts['icon'])) {
	$icon = blc_get_icon([
		'icon_descriptor' => blocksy_akg('icon', $atts, [
			'icon' => 'blc blc-search'
		]),
		'icon_container' => false,
		'icon_class' => 'ct-icon'
	]);
}

?>

<button
	data-toggle-panel="#search-modal"
	class="<?php echo esc_attr($class) ?>"
	aria-label="<?php echo __('Open search form', 'blocksy')?>"
	data-label="<?php echo $search_label_position[$device] ?>"
	<?php echo blocksy_attr_to_html($attr) ?>>

	<span class="<?php echo $label_class ?>"><?php echo $search_label; ?></span>

	<?php
		/**
		 * Note to code reviewers: This line doesn't need to be escaped.
		 * The value used here escapes the value properly.
		 * It contains an inline SVG, which is safe.
		 */
		echo $icon
	?>
</button>
