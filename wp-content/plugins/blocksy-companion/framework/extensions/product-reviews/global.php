<?php

$prefix = 'blc-product-review_single';


$product_scores_width = get_theme_mod($prefix . '_product_scores_width', 800);

if ($product_scores_width !== 800) {
	$css->put(
		blocksy_prefix_selector('.ct-product-scores', $prefix),
		'--product-scores-width: ' . $product_scores_width . 'px'
	);
}

blocksy_output_colors([
	'value' => get_theme_mod(
		$prefix . '_star_rating_color',
		[]
	),
	'default' => [
		'default' => [
			'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT')
		],
		'inactive' => [
			'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT')
		],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => blocksy_prefix_selector(
				'.ct-product-scores',
				$prefix
			),
			'variable' => 'star-rating-initial-color'
		],

		'inactive' => [
			'selector' => blocksy_prefix_selector(
				'.ct-product-scores',
				$prefix
			),
			'variable' => 'star-rating-inactive-color'
		],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod(
		$prefix . '_overall_score_text',
		[]
	),
	'default' => [
		'default' => [
			'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT')
		]
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => blocksy_prefix_selector(
				'.ct-product-scores',
				$prefix
			),
			'variable' => 'overall-score-text-color'
		],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod(
		$prefix . '_overall_score_backgroud',
		[]
	),
	'default' => [
		'default' => [
			'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT')
		]
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => blocksy_prefix_selector(
				'.ct-product-scores',
				$prefix
			),
			'variable' => 'overall-score-box-background'
		],
	],
]);


$prefix = 'blc-product-review_archive';

blocksy_output_colors([
	'value' => get_theme_mod(
		$prefix . '_star_rating_color',
		[]
	),
	'default' => [
		'default' => [
			'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT')
		],
		'inactive' => [
			'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT')
		],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => blocksy_prefix_selector(
				'.star-rating',
				$prefix
			),
			'variable' => 'star-rating-initial-color'
		],

		'inactive' => [
			'selector' => blocksy_prefix_selector(
				'.star-rating',
				$prefix
			),
			'variable' => 'star-rating-inactive-color'
		],
	],
]);