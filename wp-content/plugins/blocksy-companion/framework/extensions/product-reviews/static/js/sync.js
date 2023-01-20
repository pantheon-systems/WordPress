import { applyPrefixFor } from 'blocksy-customizer-sync'
import ctEvents from 'ct-events'

const prefix = 'blc-product-review_single'

ctEvents.on(
	'ct:customizer:sync:collect-variable-descriptors',
	(allVariables) => {
		allVariables.result = {
			...allVariables.result,
			[`${prefix}_product_scores_width`]: {
				selector: applyPrefixFor('.ct-product-scores', prefix),
				variable: 'product-scores-width',
				unit: 'px',
			},

			[`${prefix}_star_rating_color`]: [
				{
					selector: applyPrefixFor('.ct-product-scores', prefix),
					variable: 'star-rating-initial-color',
					type: 'color:default',
				},

				{
					selector: applyPrefixFor('.ct-product-scores', prefix),
					variable: 'star-rating-inactive-color',
					type: 'color:inactive',
				},
			],

			[`${prefix}_overall_score_text`]: [
				{
					selector: applyPrefixFor('.ct-product-scores', prefix),
					variable: 'overall-score-text-color',
					type: 'color:default',
				},
			],

			[`${prefix}_overall_score_backgroud`]: [
				{
					selector: applyPrefixFor('.ct-product-scores', prefix),
					variable: 'overall-score-box-background',
					type: 'color:default',
				},
			],
		}
	}
)

const archivePrefix = 'blc-product-review_archive'

ctEvents.on(
	'ct:customizer:sync:collect-variable-descriptors',
	(allVariables) => {
		allVariables.result = {
			...allVariables.result,

			[`${archivePrefix}_star_rating_color`]: [
				{
					selector: applyPrefixFor('.star-rating', archivePrefix),
					variable: 'star-rating-initial-color',
					type: 'color:default',
				},

				{
					selector: applyPrefixFor('.star-rating', archivePrefix),
					variable: 'star-rating-inactive-color',
					type: 'color:inactive',
				},
			],
		}
	}
)
