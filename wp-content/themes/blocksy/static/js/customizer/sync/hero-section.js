import {
	getOptionFor,
	watchOptionsWithPrefix,
	responsiveClassesFor,
} from './helpers'
import { typographyOption } from './variables/typography'
import { handleBackgroundOptionFor } from './variables/background'
import { renderSingleEntryMeta } from './helpers/entry-meta'
import ctEvents from 'ct-events'

export const getPrefixFor = () => document.body.dataset.prefix

const getMetaSpacingVariables = ({ prefix }) =>
	[
		{
			key: 'author_social_channels',
			selector: `[data-prefix="${prefix}"] .hero-section .author-box-social`,
		},

		{
			key: 'custom_description',
			selector: `[data-prefix="${prefix}"] .hero-section .page-description`,
		},

		{
			key: 'custom_title',
			selector: [
				`[data-prefix="${prefix}"] .hero-section .page-title`,
				`[data-prefix="${prefix}"] .hero-section .ct-author-name`,
			].join(', '),
		},
		{
			key: 'breadcrumbs',
			selector: `[data-prefix="${prefix}"] .hero-section .ct-breadcrumbs`,
		},
		{
			key: 'custom_meta',
			selector: `[data-prefix="${prefix}"] .hero-section .entry-meta`,
		},
		{
			second_meta: true,
			key: 'custom_meta',
			selector: `[data-prefix="${prefix}"] .hero-section .entry-meta[data-id="second"]`,
		},
	].map(({ key, selector, second_meta }) => ({
		variable: 'itemSpacing',
		unit: 'px',
		responsive: true,
		selector,
		extractValue: (value) => {
			let component = value.find((component) => component.id === key)

			if (second_meta) {
				let allMeta = value.filter(
					(component) => component.id === 'custom_meta'
				)

				if (allMeta.length === 2) {
					component = allMeta[1]
				} else {
					return 'CT_CSS_SKIP_RULE'
				}
			}

			return (
				(
					component || {
						hero_item_spacing: 20,
					}
				).hero_item_spacing || 20
			)
		},
	}))

const getVariablesForPrefix = (prefix) => ({
	[`${prefix}_hero_height`]: {
		selector: `[data-prefix="${prefix}"] .hero-section[data-type="type-2"]`,
		variable: 'min-height',
		responsive: true,
		unit: '',
	},

	...typographyOption({
		id: `${prefix}_pageTitleFont`,
		selector: `[data-prefix="${prefix}"] .entry-header .page-title`,
	}),

	[`${prefix}_pageTitleFontColor`]: {
		selector: `[data-prefix="${prefix}"] .entry-header .page-title`,
		variable: 'heading-color',
		type: 'color',
	},

	...typographyOption({
		id: `${prefix}_pageMetaFont`,
		selector: `[data-prefix="${prefix}"] .entry-header .entry-meta`,
	}),

	[`${prefix}_pageMetaFontColor`]: [
		{
			selector: `[data-prefix="${prefix}"] .entry-header .entry-meta`,
			variable: 'color',
			type: 'color:default',
		},

		{
			selector: `[data-prefix="${prefix}"] .entry-header .entry-meta`,
			variable: 'linkHoverColor',
			type: 'color:hover',
		},
	],

	[`${prefix}_page_meta_button_type_font_colors`]: [
		{
			selector: `[data-prefix="${prefix}"] .entry-header [data-type="pill"]`,
			variable: 'buttonTextInitialColor',
			type: 'color:default',
		},

		{
			selector: `[data-prefix="${prefix}"] .entry-header [data-type="pill"]`,
			variable: 'buttonTextHoverColor',
			type: 'color:hover',
		},
	],

	[`${prefix}_page_meta_button_type_background_colors`]: [
		{
			selector: `[data-prefix="${prefix}"] .entry-header [data-type="pill"]`,
			variable: 'buttonInitialColor',
			type: 'color:default',
		},

		{
			selector: `[data-prefix="${prefix}"] .entry-header [data-type="pill"]`,
			variable: 'buttonHoverColor',
			type: 'color:hover',
		},
	],

	...typographyOption({
		id: `${prefix}_pageExcerptFont`,
		selector: `[data-prefix="${prefix}"] .entry-header .page-description`,
	}),

	[`${prefix}_pageExcerptColor`]: {
		selector: `[data-prefix="${prefix}"] .entry-header .page-description`,
		variable: 'color',
		type: 'color',
	},

	...typographyOption({
		id: `${prefix}_breadcrumbsFont`,
		selector: `[data-prefix="${prefix}"] .entry-header .ct-breadcrumbs`,
	}),

	[`${prefix}_breadcrumbsFontColor`]: [
		{
			selector: `[data-prefix="${prefix}"] .entry-header .ct-breadcrumbs`,
			variable: 'color',
			type: 'color:default',
		},

		{
			selector: `[data-prefix="${prefix}"] .entry-header .ct-breadcrumbs`,
			variable: 'linkInitialColor',
			type: 'color:initial',
		},

		{
			selector: `[data-prefix="${prefix}"] .entry-header .ct-breadcrumbs`,
			variable: 'linkHoverColor',
			type: 'color:hover',
		},
	],

	[`${prefix}_hero_alignment1`]: {
		selector: `[data-prefix="${prefix}"] .hero-section[data-type="type-1"]`,
		variable: 'alignment',
		unit: '',
		responsive: true,
	},

	[`${prefix}_hero_margin`]: {
		selector: `[data-prefix="${prefix}"] .hero-section[data-type="type-1"]`,
		variable: 'margin-bottom',
		responsive: true,
		unit: 'px',
	},

	[`${prefix}_hero_alignment2`]: {
		selector: `[data-prefix="${prefix}"] .hero-section[data-type="type-2"]`,
		variable: 'alignment',
		unit: '',
		responsive: true,
	},

	[`${prefix}_hero_vertical_alignment`]: {
		selector: `[data-prefix="${prefix}"] .hero-section[data-type="type-2"]`,
		variable: 'vertical-alignment',
		unit: '',
		responsive: true,
	},

	...handleBackgroundOptionFor({
		id: `${prefix}_pageTitleOverlay`,
		selector: `[data-prefix="${prefix}"] .hero-section[data-type="type-2"] > figure .ct-image-container:after`,
	}),

	...handleBackgroundOptionFor({
		id: `${prefix}_pageTitleBackground`,
		selector: `[data-prefix="${prefix}"] .hero-section[data-type="type-2"]`,
	}),

	[`${prefix}_pageTitlePadding`]: {
		selector: `[data-prefix="${prefix}"] .hero-section[data-type="type-2"]`,
		type: 'spacing',
		variable: 'container-padding',
		responsive: true,
	},

	[`${prefix}_hero_elements`]: (value) => {
		let additionalVariables = []

		value.map((layer) => {
			if (layer.typography) {
				additionalVariables = [
					...additionalVariables,
					...typographyOption({
						id: 'test',
						selector: `[data-prefix="${prefix}"] [data-field*="${layer.__id.substring(
							0,
							6
						)}"]`,
						extractValue: (value) => layer.typography,
					}).test,
				]
			}

			if (layer.color) {
				additionalVariables = [
					...additionalVariables,

					{
						selector: `[data-prefix="${prefix}"] [data-field*="${layer.__id.substring(
							0,
							6
						)}"]`,
						variable: 'color',
						type: 'color:default',
						extractValue: () => layer.color,
					},

					{
						selector: `[data-prefix="${prefix}"] [data-field*="${layer.__id.substring(
							0,
							6
						)}"]`,
						variable: 'linkHoverColor',
						type: 'color:hover',
						extractValue: () => layer.color,
					},
				]
			}
		})

		return [
			...additionalVariables,
			...getMetaSpacingVariables({ prefix }),
			{
				variable: 'description-max-width',
				unit: '%',
				selector: `[data-prefix="${prefix}"] .hero-section .page-description`,
				responsive: true,
				extractValue: (value) => {
					const hero = document.querySelector(
						`[data-prefix="${prefix}"] .hero-section`
					)

					if (hero.dataset.type !== 'type-1') {
						return 'CT_CSS_SKIP_RULE'
					}

					let key = 'custom_description'

					let component = value.find(
						(component) => component.id === key
					)

					let hero_item_max_width =
						(
							component || {
								hero_item_max_width: 100,
							}
						).hero_item_max_width || 100

					return hero_item_max_width === 100
						? 'CT_CSS_SKIP_RULE'
						: hero_item_max_width
				},
			},
		]
	},

	...typographyOption({
		id: 'courses_single_hero_title_font',
		selector: `[data-prefix="${prefix}"] .tutor-course-details-title`,
	}),

	courses_single_hero_title_font_color: {
		selector: `[data-prefix="${prefix}"] .tutor-course-details-title`,
		variable: 'heading-color',
		type: 'color',
	},

	...typographyOption({
		id: 'courses_single_hero_categories_font',
		selector: `[data-prefix="${prefix}"] .tutor-meta > *`,
	}),

	courses_single_hero_categories_colors: [
		{
			selector: `[data-prefix="${prefix}"] .tutor-meta`,
			variable: 'color',
			type: 'color:default',
		},

		{
			selector: `[data-prefix="${prefix}"] .tutor-meta`,
			variable: 'linkHoverColor',
			type: 'color:hover',
		},
	],

	...typographyOption({
		id: 'courses_single_hero_actions_font',
		selector: `[data-prefix="${prefix}"] .tutor-course-details-actions > a`,
	}),

	courses_single_hero_actions_colors: [
		{
			selector: `[data-prefix="${prefix}"] .tutor-course-details-actions > a`,
			variable: 'color',
			type: 'color:default',
		},

		{
			selector: `[data-prefix="${prefix}"] .tutor-course-details-actions > a`,
			variable: 'linkHoverColor',
			type: 'color:hover',
		},
	],

	...typographyOption({
		id: 'courses_single_hero_title_rating_font',
		selector: `[data-prefix="${prefix}"] .tutor-ratings`,
	}),

	courses_single_hero_title_rating_font_color: {
		selector: `[data-prefix="${prefix}"] .tutor-ratings`,
		variable: 'color',
		type: 'color',
	},

	hero_title_rating_font_color: {
		selector: `[data-prefix="${prefix}"] .tutor-ratings`,
		variable: 'color',
		type: 'color',
	},
})

export const getHeroVariables = () => {
	if (document.body.dataset.prefix !== getPrefixFor()) {
		return {}
	}

	if ((document.body.dataset.prefixCustom || '').indexOf('hero') > -1) {
		return {}
	}

	return getVariablesForPrefix(getPrefixFor())
}

watchOptionsWithPrefix({
	getPrefix: () => getPrefixFor(),
	getOptionsForPrefix: ({ prefix }) => [
		`${prefix}_hero_structure`,
		`${prefix}_hero_elements`,

		`${prefix}_parallax`,
	],

	render: ({ id, prefix }) => {
		if (id === `${prefix}_hero_structure`) {
			const heroStrcture = getOptionFor('hero_structure', getPrefixFor())

			const container = document.querySelector(
				'.hero-section [class*="ct-container"]'
			)

			container.classList.remove('ct-container', 'ct-container-narrow')

			container.classList.add(
				`ct-container${heroStrcture === 'narrow' ? '-narrow' : ''}`
			)
		}

		if (id === `${prefix}_hero_elements`) {
			const heroElements = getOptionFor('hero_elements', prefix)

			const heroElementsContainer = document.querySelector(
				'.hero-section .entry-header'
			)

			heroElements.map((singleLayer) => {
				if (singleLayer.id === 'custom_title' && prefix === 'author') {
					let { has_author_avatar, author_avatar_size } = singleLayer

					let image = heroElementsContainer.querySelector(
						'.ct-author-name .ct-image-container-static'
					)

					if (image) {
						const img = image.querySelector('img')

						if (img) {
							img.height = author_avatar_size || '60'
							img.width = author_avatar_size || '60'
							img.style.height = `${author_avatar_size || 60}px`
						}
					}
				}

				if (singleLayer.id === 'custom_description') {
					let description =
						heroElementsContainer.querySelector('.page-description')

					if (singleLayer.enabled && description) {
						responsiveClassesFor(
							singleLayer.description_visibility,
							description
						)
					}
				}

				if (singleLayer.id === 'custom_meta' && singleLayer.enabled) {
					if (
						prefix === 'single_blog_post' ||
						prefix === 'single_page'
					) {
						const metaElements = singleLayer.meta_elements

						let el =
							heroElementsContainer.querySelectorAll(
								'.entry-meta'
							)

						if (
							heroElements.filter(
								({ id }) => id === 'custom_meta'
							).length > 1
						) {
							if (
								heroElements
									.filter(({ id }) => id === 'custom_meta')
									.map(({ __id }) => __id)
									.indexOf(singleLayer.__id) === 0
							) {
								el = el[0]
							}

							if (
								heroElements
									.filter(({ id }) => id === 'custom_meta')
									.map(({ __id }) => __id)
									.indexOf(singleLayer.__id) === 1
							) {
								if (el.length > 1) {
									el = el[1]
								}
							}
						} else {
							el = el[0]
						}

						renderSingleEntryMeta({
							el,
							meta_elements: metaElements,
							...singleLayer,
						})
					}
				}
			})
		}

		if (id === `${prefix}_parallax`) {
			const type = getOptionFor('hero_section', prefix)

			document.querySelector('.hero-section').dataset.parallax = ''

			if (
				type === 'type-2' &&
				(getOptionFor('page_title_bg_type', prefix) ===
					'custom_image' ||
					getOptionFor('page_title_bg_type', prefix) ===
						'featured_image')
			) {
				const parallaxResult = getOptionFor('parallax', prefix)
				const parallaxOutput = [
					...(parallaxResult.desktop ? ['desktop'] : []),
					...(parallaxResult.tablet ? ['tablet'] : []),
					...(parallaxResult.mobile ? ['mobile'] : []),
				]

				if (
					document.querySelector('.hero-section figure') &&
					parallaxOutput.length > 0
				) {
					document.querySelector('.hero-section').dataset.parallax =
						parallaxOutput.join(':')
				}
			}

			ctEvents.trigger('blocksy:parallax:init')
		}
	},
})
