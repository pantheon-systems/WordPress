import {
	getPrefixFor,
	getOptionFor,
	responsiveClassesFor,
	watchOptionsWithPrefix,
	applyPrefixFor,
} from './helpers'

const prefix = getPrefixFor({
	allowed_prefixes: ['blog', 'woo_categories'],
	default_prefix: 'blog',
})

watchOptionsWithPrefix({
	getPrefix: () => prefix,
	getOptionsForPrefix: () => [
		`${prefix}_load_more_label`,
		`${prefix}_paginationDivider`,
		`${prefix}_numbers_visibility`,
		`${prefix}_arrows_visibility`,
	],

	render: () => {
		if (document.querySelector('.ct-load-more')) {
			document.querySelector('.ct-load-more').innerHTML = getOptionFor(
				'load_more_label',
				prefix
			)
		}

		;[...document.querySelectorAll('.ct-pagination')].map((el) => {
			el.removeAttribute('data-divider')
			;[...el.parentNode.querySelectorAll('nav > a')].map((el) => {
				responsiveClassesFor(
					getOptionFor('arrows_visibility', prefix),
					el
				)
			})
			;[...el.parentNode.querySelectorAll('nav > div')].map((el) => {
				responsiveClassesFor(
					getOptionFor('numbers_visibility', prefix),
					el
				)
			})

			if (getOptionFor('paginationDivider', prefix).style === 'none') {
				return
			}

			if (
				getOptionFor('pagination_global_type', prefix) ===
				'infinite_scroll'
			) {
				return
			}

			el.dataset.divider = ''
		})
	},
})

export const getPaginationVariables = () => ({
	[`${prefix}_paginationSpacing`]: {
		selector: applyPrefixFor(
			'.ct-pagination',
			prefix === 'blog' ? '' : prefix
		),
		variable: 'spacing',
		responsive: true,
		unit: 'px',
	},

	[`${prefix}_paginationDivider`]: {
		selector: applyPrefixFor(
			'.ct-pagination[data-divider]',
			prefix === 'blog' ? '' : prefix
		),
		variable: 'pagination-divider',
		type: 'border',
		skip_none: true,
	},

	[`${prefix}_simplePaginationFontColor`]: [
		{
			selector: applyPrefixFor(
				'[data-pagination="simple"], [data-pagination="next_prev"]',
				prefix === 'blog' ? '' : prefix
			),
			variable: 'color',
			type: 'color:default',
		},

		{
			selector: applyPrefixFor(
				'.ct-pagination[data-pagination="simple"]',
				prefix === 'blog' ? '' : prefix
			),
			variable: 'colorActive',
			type: 'color:active',
		},

		{
			selector: applyPrefixFor(
				'[data-pagination="simple"], [data-pagination="next_prev"]',
				prefix === 'blog' ? '' : prefix
			),
			variable: 'linkHoverColor',
			type: 'color:hover',
		},
	],

	[`${prefix}_paginationButtonText`]: [
		{
			selector: applyPrefixFor(
				'[data-pagination="load_more"]',
				prefix === 'blog' ? '' : prefix
			),
			variable: 'buttonTextInitialColor',
			type: 'color:default',
		},

		{
			selector: applyPrefixFor(
				'[data-pagination="load_more"]',
				prefix === 'blog' ? '' : prefix
			),
			variable: 'buttonTextHoverColor',
			type: 'color:hover',
		},
	],

	[`${prefix}_paginationButton`]: [
		{
			selector: applyPrefixFor(
				'[data-pagination="load_more"]',
				prefix === 'blog' ? '' : prefix
			),
			variable: 'buttonInitialColor',
			type: 'color:default',
		},

		{
			selector: applyPrefixFor(
				'[data-pagination="load_more"]',
				prefix === 'blog' ? '' : prefix
			),
			variable: 'buttonHoverColor',
			type: 'color:hover',
		},
	],

	[`${prefix}_pagination_border_radius`]: {
		selector: applyPrefixFor('.ct-pagination', prefix),
		type: 'spacing',
		variable: 'border-radius',
		// responsive: true,
	},
})
