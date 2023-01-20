import { getPrefixFor, watchOptionsWithPrefix, getOptionFor } from '../helpers'
import { makeVariablesWithCondition } from '../helpers/variables-with-conditions'
import { handleBackgroundOptionFor } from '../variables/background'
import { maybePromoteScalarValueIntoResponsive } from 'customizer-sync-helpers/dist/promote-into-responsive'

const getSelectorSuffixFor = (prefix) => {
	let selectorSuffix = '[class*="ct-container"] > article[class*="post"]'

	if (prefix === 'courses_single') {
		selectorSuffix = '.tutor-col-xl-8'
	}

	return selectorSuffix
}

watchOptionsWithPrefix({
	getPrefix: () => getPrefixFor(),
	getOptionsForPrefix: ({ prefix }) => [`${prefix}_content_area_spacing`],
	render: ({ prefix, id }) => {
		if (id !== `${prefix}_content_area_spacing`) {
			return
		}

		if (
			(document.body.dataset.prefixCustom || '').indexOf(
				'vertical-spacing'
			) > -1
		) {
			return
		}

		let el = document.querySelector('.site-main > div')

		if (!el) {
			return
		}

		let spacingComponents = []

		let contentAreaSpacing = getOptionFor('content_area_spacing', prefix)

		if (contentAreaSpacing === 'both' || contentAreaSpacing === 'top') {
			spacingComponents.push('top')
		}

		if (contentAreaSpacing === 'both' || contentAreaSpacing === 'bottom') {
			spacingComponents.push('bottom')
		}

		el.removeAttribute('data-vertical-spacing')

		if (spacingComponents.length > 0) {
			el.dataset.verticalSpacing = spacingComponents.join(':')
		}
	},
})

export const getSingleContentVariablesFor = () => {
	const prefix = getPrefixFor()

	if (
		(document.body.dataset.prefixCustom || '').indexOf('content-style') > -1
	) {
		return {}
	}

	return {
		...makeVariablesWithCondition(
			`${prefix}_content_style`,
			{
				[`${prefix}_content_style`]: [
					{
						selector: `[data-prefix="${prefix}"] ${getSelectorSuffixFor(
							prefix
						)}`,
						variable: 'has-boxed',
						responsive: true,
						skipOutputCheck: true,
						extractValue: (value) => ({
							desktop:
								maybePromoteScalarValueIntoResponsive(value)
									.desktop === 'boxed'
									? 'var(--true)'
									: 'var(--false)',

							tablet:
								maybePromoteScalarValueIntoResponsive(value)
									.tablet === 'boxed'
									? 'var(--true)'
									: 'var(--false)',

							mobile:
								maybePromoteScalarValueIntoResponsive(value)
									.mobile === 'boxed'
									? 'var(--true)'
									: 'var(--false)',
						}),
						unit: '',
					},

					{
						selector: `[data-prefix="${prefix}"] ${getSelectorSuffixFor(
							prefix
						)}`,
						variable: 'has-wide',
						responsive: true,
						skipOutputCheck: true,
						extractValue: (value) => ({
							desktop:
								maybePromoteScalarValueIntoResponsive(value)
									.desktop === 'wide'
									? 'var(--true)'
									: 'var(--false)',

							tablet:
								maybePromoteScalarValueIntoResponsive(value)
									.tablet === 'wide'
									? 'var(--true)'
									: 'var(--false)',

							mobile:
								maybePromoteScalarValueIntoResponsive(value)
									.mobile === 'wide'
									? 'var(--true)'
									: 'var(--false)',
						}),
						unit: '',
					},
				],

				...handleBackgroundOptionFor({
					id: `${prefix}_content_background`,
					selector: `[data-prefix="${prefix}"] ${getSelectorSuffixFor(
						prefix
					)}`,
					responsive: true,
					conditional_var: '--has-background',
				}),

				[`${prefix}_boxed_content_spacing`]: {
					selector: `[data-prefix="${prefix}"] ${getSelectorSuffixFor(
						prefix
					)}`,
					type: 'spacing',
					variable: 'boxed-content-spacing',
					responsive: true,
					unit: '',
				},

				[`${prefix}_content_boxed_radius`]: {
					selector: `[data-prefix="${prefix}"] ${getSelectorSuffixFor(
						prefix
					)}`,
					type: 'spacing',
					variable: 'border-radius',
					responsive: true,
				},

				[`${prefix}_content_boxed_shadow`]: {
					selector: `[data-prefix="${prefix}"] ${getSelectorSuffixFor(
						prefix
					)}`,
					type: 'box-shadow',
					variable: 'box-shadow',
					responsive: true,
				},

				[`${prefix}_content_boxed_border`]: {
					selector: `[data-prefix="${prefix}"] ${getSelectorSuffixFor(
						prefix
					)}`,
					variable: 'boxed-content-border',
					type: 'border',
					responsive: true,
					skip_none: true,
				},
			},
			() => true
		),
	}
}
