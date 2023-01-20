<?php

function blocksy_get_product_review_overall_score($post_id = null) {
	if (! $post_id) {
		global $post;
		$post_id = $post->ID;
	}

	$atts = blocksy_get_post_options($post_id, [
		'meta_id' => 'blocksy_product_review_options'
	]);

	$scores = blocksy_akg('scores', $atts, []);

	if (empty($scores)) {
		return '';
	}

	$output = '<div class="ct-overall-score-layer">';

	$avg_score = apply_filters(
		'blocksy:ext:product-reviews:overall-score',
		round(array_reduce($scores, function ($carry, $score) {
			return $carry + floatval($score['score']);
		}, 0) / count($scores) * 10) / 10,
		$scores
	);

	$output .= '<span class="ct-score-label">';
	$output .= __('Rating', 'blocksy-companion');
	$output .= ':</span>';

	$output .= '<span class="ct-average-score">' . $avg_score . '/5</span>';

	$output .= '<div class="star-rating" role="img">';
	$width = (($avg_score / 5) * 100);
	$output .= '<span style="width: ' . $width . '%;"></span>';
	$output .= '</div>';

	$output .= '</div>';

	return $output;
}
