import ctEvents from 'ct-events'
import {
	watchOptionsWithPrefix,
	getPrefixFor,
	setRatioFor,
	disableTransitionsStart,
	disableTransitionsEnd,
	getOptionFor,
	withKeys,
} from '../helpers'
import { typographyOption } from '../variables/typography'
import { handleBackgroundOptionFor } from '../variables/background'
import { renderSingleEntryMeta } from '../helpers/entry-meta'
import { replaceFirstTextNode, applyPrefixFor } from '../helpers'

import { maybePromoteScalarValueIntoResponsive } from 'customizer-sync-helpers/dist/promote-into-responsive'

const prefix = getPrefixFor()

watchOptionsWithPrefix({
	getPrefix: () => prefix,
	getOptionsForPrefix: ({ prefix }) => [`${prefix}_archive_order`],

	render: ({ id }) => {
		if (id === `${prefix}_archive_order` || id === `${prefix}_card_type`) {
			disableTransitionsStart(document.querySelectorAll('.entries'))

			disableTransitionsEnd(document.querySelectorAll('.entries'))

			let archiveOrder = getOptionFor('archive_order', prefix)
			disableTransitionsStart(document.querySelectorAll('.entries'))

			let allItemsToOutput = archiveOrder.filter(
				({ enabled }) => !!enabled
			)

			allItemsToOutput.map((component, index) => {
				;[...document.querySelectorAll('.entries > article')].map(
					(article) => {
						let image = article.querySelector('.ct-image-container')
						let button = article.querySelector('.entry-button')

						if (component.id === 'featured_image' && image) {
							setRatioFor(component.thumb_ratio, image)

							image.classList.remove('boundless-image')

							if (
								(component.is_boundless || 'yes') === 'yes' &&
								getOptionFor('card_type', prefix) === 'boxed' &&
								getOptionFor('structure', prefix) !==
									'gutenberg'
							) {
								image.classList.add('boundless-image')
							}
						}

						if (component.id === 'read_more' && button) {
							button.dataset.type =
								component.button_type || 'simple'

							button.classList.remove('ct-button')

							if (
								(component.button_type || 'simple') ===
								'background'
							) {
								button.classList.add('ct-button')
							}

							button.dataset.alignment =
								component.read_more_alignment || 'left'

							replaceFirstTextNode(
								button,
								component.read_more_text || 'Read More'
							)
						}

						if (component.id === 'post_meta') {
							let moreDefaults = {}
							let el = article.querySelectorAll('.entry-meta')

							if (
								archiveOrder.filter(
									({ id }) => id === 'post_meta'
								).length > 1
							) {
								if (
									archiveOrder
										.filter(({ id }) => id === 'post_meta')
										.map(({ __id }) => __id)
										.indexOf(component.__id) === 0
								) {
									moreDefaults = {
										meta_elements: [
											{
												id: 'categories',
												enabled: true,
											},
										],
									}

									el = el[0]
								}

								if (
									archiveOrder
										.filter(({ id }) => id === 'post_meta')
										.map(({ __id }) => __id)
										.indexOf(component.__id) === 1
								) {
									moreDefaults = {
										meta_elements: [
											{
												id: 'author',
												enabled: true,
											},

											{
												id: 'post_date',
												enabled: true,
											},

											{
												id: 'comments',
												enabled: true,
											},
										],
									}

									if (el.length > 1) {
										el = el[1]
									}
								}
							}

							renderSingleEntryMeta({
								el,
								...moreDefaults,
								...component,
							})
						}
					}
				)
			})

			disableTransitionsEnd(document.querySelectorAll('.entries'))
		}
	},
})

export const getPostListingVariables = () => ({
	...typographyOption({
		id: `${prefix}_cardTitleFont`,
		selector: applyPrefixFor('.entry-card .entry-title', prefix),
	}),

	[`${prefix}_archive_order`]: (v) => {
		let variables = []

		v.map((layer) => {
			if (layer.typography) {
				variables = [
					...variables,
					...typographyOption({
						id: 'test',
						selector: applyPrefixFor(
							`[data-field*="${layer.__id.substring(0, 6)}"]`,
							prefix
						),
						extractValue: (value) => {
							return layer.typography
						},
					}).test,
				]
			}

			if (layer.color) {
				variables = [
					...variables,

					{
						selector: applyPrefixFor(
							`[data-field*="${layer.__id.substring(0, 6)}"]`,
							prefix
						),
						variable: 'color',
						type: 'color:default',
						extractValue: () => {
							return layer.color
						},
					},

					{
						selector: applyPrefixFor(
							`[data-field*="${layer.__id.substring(0, 6)}"]`,
							prefix
						),
						variable: 'linkHoverColor',
						type: 'color:hover',
						extractValue: () => {
							return layer.color
						},
					},
				]
			}
		})

		return variables
	},

	[`${prefix}_columns`]: [
		{
			selector: applyPrefixFor('.entries', prefix),
			variable: 'grid-template-columns',
			responsive: true,
			extractValue: (val) => {
				const responsive = maybePromoteScalarValueIntoResponsive(val)

				return {
					desktop: `repeat(${responsive.desktop}, minmax(0, 1fr))`,
					tablet: `repeat(${responsive.tablet}, minmax(0, 1fr))`,
					mobile: `repeat(${responsive.mobile}, minmax(0, 1fr))`,
				}
			},
		},
	],

	[`${prefix}_cardTitleColor`]: [
		{
			selector: applyPrefixFor('.entry-card .entry-title', prefix),
			variable: 'heading-color',
			type: 'color:default',
		},

		{
			selector: applyPrefixFor('.entry-card .entry-title', prefix),
			variable: 'linkHoverColor',
			type: 'color:hover',
		},
	],

	...typographyOption({
		id: `${prefix}_cardExcerptFont`,
		selector: applyPrefixFor('.entry-excerpt', prefix),
	}),

	[`${prefix}_cardExcerptColor`]: {
		selector: applyPrefixFor('.entry-excerpt', prefix),
		variable: 'color',
		type: 'color',
	},

	...typographyOption({
		id: `${prefix}_cardMetaFont`,
		selector: applyPrefixFor('.entry-card .entry-meta', prefix),
	}),

	[`${prefix}_cardMetaColor`]: [
		{
			selector: applyPrefixFor('.entry-card .entry-meta', prefix),
			variable: 'color',
			type: 'color:default',
		},

		{
			selector: applyPrefixFor('.entry-card .entry-meta', prefix),
			variable: 'linkHoverColor',
			type: 'color:hover',
		},
	],

	[`${prefix}_card_meta_button_type_font_colors`]: [
		{
			selector: applyPrefixFor('.entry-card [data-type="pill"]', prefix),
			variable: 'buttonTextInitialColor',
			type: 'color:default',
		},

		{
			selector: applyPrefixFor('.entry-card [data-type="pill"]', prefix),
			variable: 'buttonTextHoverColor',
			type: 'color:hover',
		},
	],

	[`${prefix}_card_meta_button_type_background_colors`]: [
		{
			selector: applyPrefixFor('.entry-card [data-type="pill"]', prefix),
			variable: 'buttonInitialColor',
			type: 'color:default',
		},

		{
			selector: applyPrefixFor('.entry-card [data-type="pill"]', prefix),
			variable: 'buttonHoverColor',
			type: 'color:hover',
		},
	],

	[`${prefix}_cardButtonSimpleTextColor`]: [
		{
			selector: applyPrefixFor(
				'.entry-button[data-type="simple"]',
				prefix
			),
			variable: 'linkInitialColor',
			type: 'color:default',
		},

		{
			selector: applyPrefixFor(
				'.entry-button[data-type="simple"]',
				prefix
			),
			variable: 'linkHoverColor',
			type: 'color:hover',
		},
	],

	[`${prefix}_cardButtonBackgroundTextColor`]: [
		{
			selector: applyPrefixFor(
				'.entry-button[data-type="background"]',
				prefix
			),
			variable: 'buttonTextInitialColor',
			type: 'color:default',
		},

		{
			selector: applyPrefixFor(
				'.entry-button[data-type="background"]',
				prefix
			),
			variable: 'buttonTextHoverColor',
			type: 'color:hover',
		},
	],

	[`${prefix}_cardButtonOutlineTextColor`]: [
		{
			selector: applyPrefixFor(
				'.entry-button[data-type="outline"]',
				prefix
			),
			variable: 'linkInitialColor',
			type: 'color:default',
		},

		{
			selector: applyPrefixFor(
				'.entry-button[data-type="outline"]',
				prefix
			),
			variable: 'linkHoverColor',
			type: 'color:hover',
		},
	],

	[`${prefix}_cardButtonColor`]: [
		{
			selector: applyPrefixFor('.entry-button', prefix),
			variable: 'buttonInitialColor',
			type: 'color:default',
		},

		{
			selector: applyPrefixFor('.entry-button', prefix),
			variable: 'buttonHoverColor',
			type: 'color:hover',
		},
	],

	...handleBackgroundOptionFor({
		id: `${prefix}_cardBackground`,
		selector: applyPrefixFor('.entry-card', prefix),
		responsive: true,
	}),

	...handleBackgroundOptionFor({
		id: `${prefix}_card_overlay_background`,
		selector: applyPrefixFor(
			'.entry-card .ct-image-container:after',
			prefix
		),
		responsive: true,
	}),

	[`${prefix}_cardBorder`]: {
		selector: applyPrefixFor('.entry-card', prefix),
		variable: 'card-border',
		type: 'border',
		responsive: true,
		skip_none: true,
	},

	[`${prefix}_cardDivider`]: {
		selector: applyPrefixFor('[data-cards="simple"] .entry-card', prefix),
		variable: 'card-border',
		type: 'border',
	},

	[`${prefix}_entryDivider`]: {
		selector: applyPrefixFor('.entry-card', prefix),
		variable: 'entry-divider',
		type: 'border',
	},

	...withKeys(
		[`${prefix}_cardThumbRadius`, `${prefix}_card_min_height`],

		[
			{
				selector: applyPrefixFor(
					'.entry-card .ct-image-container',
					prefix
				),
				type: 'spacing',
				variable: 'borderRadius',
				responsive: true,
				extractValue: () => {
					return getOptionFor('cardThumbRadius', prefix)
				},
			},

			{
				selector: applyPrefixFor('.entries', prefix),
				variable: 'card-min-height',
				responsive: true,
				unit: 'px',
				extractValue: () => getOptionFor('card_min_height', prefix),
			},
		]
	),

	[`${prefix}_cardsGap`]: {
		selector: applyPrefixFor('.entries', prefix),
		variable: 'grid-columns-gap',
		responsive: true,
		unit: 'px',
	},

	[`${prefix}_card_spacing`]: {
		selector: applyPrefixFor('[data-cards] .entry-card', prefix),
		variable: 'card-inner-spacing',
		responsive: true,
		unit: 'px',
	},

	[`${prefix}_cardRadius`]: {
		selector: applyPrefixFor('.entry-card', prefix),
		type: 'spacing',
		variable: 'borderRadius',
		responsive: true,
	},

	[`${prefix}_cardShadow`]: {
		selector: applyPrefixFor('.entry-card', prefix),
		type: 'box-shadow',
		variable: 'box-shadow',
		responsive: true,
	},

	[`${prefix}_content_horizontal_alignment`]: {
		selector: applyPrefixFor('.entry-card', prefix),
		variable: 'horizontal-alignment',
		responsive: true,
		unit: '',
	},

	[`${prefix}_content_vertical_alignment`]: {
		selector: applyPrefixFor('.entry-card', prefix),
		variable: 'vertical-alignment',
		responsive: true,
		unit: '',
	},

	...(prefix.indexOf('single') === -1
		? {
				...handleBackgroundOptionFor({
					id: `${prefix}_background`,
					selector: `[data-prefix="${prefix}"]`,
					responsive: true,
				}),
		  }
		: {}),
})
