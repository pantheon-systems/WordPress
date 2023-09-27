<?php

$prefix = 'blc-product-review_single';

$atts = apply_filters(
	'blocksy:ext:product-reviews:frontend:atts',
	blocksy_get_post_options(null, [
		'meta_id' => 'blocksy_product_review_options'
	]),
	get_the_ID()
);

$gallery_images = array_map(function ($item) {
	return $item['attachment_id'];
}, blocksy_akg('gallery', $atts, []));

$thumb_id = get_post_thumbnail_id();

if ($thumb_id) {
	array_unshift($gallery_images, intval($thumb_id));
} else {
	$gallery_images = [];
}

$link_atts = [];

if (blocksy_akg('product_link_sponsored', $atts, 'no') === 'yes') {
	$link_atts['rel'] = 'sponsored';
}

if (blocksy_akg('product_link_target', $atts, 'no') === 'yes') {
	$link_atts['target'] = '_blank';

	if (! isset($link_atts['rel'])) {
		$link_atts['rel'] = '';
	}

	$link_atts['rel'] .= ' noopener noreferrer';
	$link_atts['rel'] = trim($link_atts['rel']);
}

echo '<section class="ct-product-hero">';
echo '<div class="ct-container">';
if (count($gallery_images) === 1) {
	$attachment_id = $gallery_images[0];

	$image_href = wp_get_attachment_image_src(
		$attachment_id,
		'full'
	);

	$width = null;
	$height = null;

	if ($image_href) {
		$width = $image_href[1];
		$height = $image_href[2];

		$image_href = $image_href[0];
	}

	echo blocksy_image([
		'attachment_id' => $gallery_images[0],
		'size' => 'full',
		'ratio' => apply_filters('blocksy:ext:product-reviews:gallery:ratio', '2/1'),
		'tag_name' => 'a',
		'html_atts' => array_merge([
			'href' => $image_href
		], $width ? [
			'data-width' => $width,
			'data-height' => $height
		] : []),
	]);
}

if (count($gallery_images) > 1) {
	$args = [
		'images' => $gallery_images,
		'size' => 'full',
		'images_ratio' => apply_filters('blocksy:ext:product-reviews:gallery:ratio', '2/1')
	];

	$args['pills_images'] = $gallery_images;

	if (count($gallery_images) <= 5) {
	} else {
		$args['pills_have_arrows'] = true;
		$args['pills_container_attr'] = [
			'data-flexy' => 'no'
		];
	}

	echo blocksy_flexy($args);
}

echo blocksy_output_hero_section([
	'type' => 'type-1'
]);

$scores = blocksy_akg('scores', $atts, []);

if (! empty($scores)) {
	echo '<div class="ct-product-scores">';

	echo '<ul>';

	foreach ($scores as $single_score) {
		echo '<li>';
		echo '<span>' . $single_score['label'] . '</span>';

		echo '<div class="star-rating" role="img">';
		$width = ((floatval($single_score['score']) / 5) * 100);

		echo '<span style="width: ' . $width . '%;">Rated <strong class="rating">3</strong> out of 5</span>';
		echo '</div>';
		echo '</li>';
	}

	echo '</ul>';

	echo '<div class="ct-overall-score" ' . blocksy_schema_org_definitions('reviewRating') . '>';

	$avg_score = apply_filters(
		'blocksy:ext:product-reviews:overall-score',
		round(array_reduce($scores, function ($carry, $score) {
			return $carry + floatval($score['score']);
		}, 0) / count($scores) * 10) / 10,
		$scores
	);

	echo '<span class="ct-average-score" ' . blocksy_schema_org_definitions('ratingValue') . '>' . $avg_score . '/5</span>';

	echo '<div class="star-rating" role="img">';
	$width = ( ( $avg_score / 5 ) * 100 );
	echo '<span style="width: ' . $width . '%;"></span>';
	echo '</div>';

	echo '<span class="ct-score-label">';
	echo __('Overall Score', 'blocksy-companion');
	echo '</span>';
	echo '</div>';

	echo '</div>';
}

$has_read_more = get_theme_mod($prefix . '_has_read_more', 'yes') === 'yes';
$has_buy_now = get_theme_mod($prefix . '_has_buy_now', 'yes') === 'yes';

if ($has_read_more || $has_buy_now) {
	echo '<div class="ct-product-actions-group">';

	$product_link = blocksy_akg('product_link', $atts, '#');
	$product_button_label = blocksy_akg(
		'product_button_label',
		$atts,
		__('Buy Now', 'blocksy-companion')
	);

	$product_read_content_button_label = blocksy_akg(
		'product_read_content_button_label',
		$atts,
		__('Read More', 'blocksy-companion')
	);

	if (
		! empty($product_button_label)
		&&
		$has_read_more
	) {
		echo '<a href="#post-' . get_the_ID() . '" class="ct-button">';
		echo $product_read_content_button_label;

	/*
	echo blc_get_icon([
		'icon_descriptor' => blocksy_akg('product_read_content_button_icon', $atts, [
			'icon' => 'fas fa-arrow-down'
		]),
	]);
	 */

		echo '</a>';
	}

	if (
		! empty($product_button_label)
		&&
		! empty($product_link)
		&&
		$has_buy_now
	) {
		echo blocksy_html_tag(
			'a',
			array_merge([
				'href' => esc_url($product_link),
				'class' => 'ct-button'
			], $link_atts),
			$product_button_label
		);

	/*
	echo blc_get_icon([
		'icon_descriptor' => blocksy_akg('product_button_icon', $atts, [
			'icon' => 'fas fa-cart-arrow-down'
		]),
	]);
	 */
	}

	echo '</div>';
}

$product_specs = blocksy_akg('product_specs', $atts, []);
$product_pros = blocksy_akg('product_pros', $atts, []);
$product_cons = blocksy_akg('product_cons', $atts, []);
$product_description = blocksy_akg('product_description', $atts, '');

if (! empty($product_description)) {
	echo '<div class="ct-product-description" ' . blocksy_schema_org_definitions('reviewBody') . '>';

	echo '<div class="entry-content">';
	echo do_shortcode($product_description);
	echo '</div>';

	echo '</div>';
}

if (
	! empty($product_specs)
	||
	! empty($product_pros)
	||
	! empty($product_cons)
) {
	echo '<div class="ct-product-info">';

	if (! empty($product_specs)) {
		echo '<div class="ct-specs">';
		echo '<h5>' . __('Specs', 'blocksy-companion') . '</h5>';

		echo '<ul>';

		foreach ($product_specs as $single_spec) {
			echo '<li>';
			echo blocksy_html_tag(
				'span',
				[
					'class' => 'ct-icon-container'
				],
				"<svg width='13' height='13' viewBox='0 0 13 13'><path d='M5.3 0l-.2 1.7c-.8.2-1.5.7-2.1 1.2l-1.5-.7-1.2 2 1.4 1c-.1.5-.2.9-.2 1.3s.1.8.2 1.2l-1.4 1 1.2 2 1.5-.6c.6.6 1.3 1 2.1 1.2l.2 1.7h2.3l.2-1.7c.8-.2 1.5-.6 2.1-1.2l1.6.7 1.2-2-1.4-1c.1-.4.2-.8.2-1.2s-.1-.8-.2-1.2l1.4-1-1.2-2-1.5.5c-.6-.6-1.3-1-2.1-1.2L7.7 0H5.3zm1.2 4.5c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2z'/></svg>"
			);
			echo '<b>' . blocksy_akg('label', $single_spec, '') . ': </b>';
			echo blocksy_akg('value', $single_spec, '');
			echo '</li>';
		}

		echo '</ul>';
		echo '</div>';
	}

	if (! empty($product_pros)) {
		echo '<div class="ct-product-review-pros" ' . blocksy_schema_org_definitions('positiveNotes') . '>';
		echo '<h5>' . __('Pros', 'blocksy-companion') . '</h5>';

		echo '<ul>';

		foreach ($product_pros as $single_pro) {
			echo '<li>';
			echo blocksy_html_tag(
				'span',
				[
					'class' => 'ct-icon-container'
				],
				"<svg width='13' height='13' viewBox='0 0 13 13'><path d='M6.4.3c-.3 0-.5.3-.6.5l-.6 1.7-1.7 2c-.3.3-.4.5-.4.9v6c0 .7.6 1.3 1.3 1.3h5.2c.5 0 1-.3 1.1-.7l2-4.4c.2-.3.3-.6.3-.8v-.6c0-.7-.6-1.3-1.3-1.3H7.2s.7-1.6.7-2.7c0-1-.7-1.6-1.2-1.7-.2-.2-.2-.2-.3-.2zM1 4.8c-.5 0-1 .4-1 1v5.9c0 .6.4 1 1 1s1-.4 1-1V5.8c-.1-.5-.5-1-1-1z'/></svg>"
			);
			echo blocksy_akg('label', $single_pro, '');
			echo '</li>';
		}

		echo '</ul>';
		echo '</div>';
	}

	if (! empty($product_cons)) {
		echo '<div class="ct-product-review-cons" ' . blocksy_schema_org_definitions('negativeNotes') . '>';
		echo '<h5>' . __('Cons', 'blocksy-companion') . '</h5>';

		echo '<ul>';

		foreach ($product_cons as $single_cons) {
			echo '<li>';
			echo blocksy_html_tag(
				'span',
				[
					'class' => 'ct-icon-container'
				],
				"<svg width='13' height='13' viewBox='0 0 13 13'><path d='M6.6 12.6c.5-.2 1.2-.7 1.2-1.7 0-1.1-.7-2.7-.7-2.7h4.5c.7 0 1.3-.6 1.3-1.3v-.7c0-.3-.1-.5-.2-.8l-2-4.4c-.2-.4-.6-.7-1.1-.7H4.4c-.7 0-1.3.6-1.3 1.3v6c0 .3.1.6.3.9l1.7 2 .5 1.7c.1.3.3.5.6.5s.3 0 .4-.1zM1.9 7.2V1.4c0-.5-.4-1-1-1s-1 .3-1 1v5.9c0 .5.4 1 1 1s1-.6 1-1.1z'/></svg>"
			);
			echo blocksy_akg('label', $single_cons, '');
			echo '</li>';
		}

		echo '</ul>';
		echo '</div>';
	}

	echo '</div>';
}

echo '</div>';
echo '</section>';
