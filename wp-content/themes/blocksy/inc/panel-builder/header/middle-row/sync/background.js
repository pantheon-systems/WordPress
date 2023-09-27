import {
	withKeys,
	disableTransitionsStart,
	disableTransitionsEnd,
} from '../../../../../static/js/customizer/sync/helpers'

import {
	getRootSelectorFor,
	assembleSelector,
	mutateSelector,
} from '../../../../../static/js/customizer/sync/helpers'

import { handleBackgroundOptionFor } from '../../../../../static/js/customizer/sync/variables/background'

import { maybePromoteScalarValueIntoResponsive } from 'customizer-sync-helpers/dist/promote-into-responsive'

const transformBgFor = ({ background, headerRowWidth, predicate }) => {
	let bg = maybePromoteScalarValueIntoResponsive(background)

	headerRowWidth = maybePromoteScalarValueIntoResponsive(headerRowWidth)

	return {
		desktop: predicate({ headerRowWidth: headerRowWidth.desktop })
			? bg.desktop
			: {
					...bg.desktop,
					background_type: 'color',
					backgroundColor: {
						default: {
							color: 'transparent',
						},
					},
			  },

		tablet: predicate({ headerRowWidth: headerRowWidth.mobile })
			? bg.tablet
			: {
					...bg.tablet,
					background_type: 'color',
					backgroundColor: {
						default: {
							color: 'transparent',
						},
					},
			  },

		mobile: predicate({ headerRowWidth: headerRowWidth.mobile })
			? bg.mobile
			: {
					...bg.mobile,
					background_type: 'color',
					backgroundColor: {
						default: {
							color: 'transparent',
						},
					},
			  },
	}
}

export const getRowBackgroundVariables = ({ itemId }) => {
	return {
		...withKeys(
			[
				'headerRowBackground',
				'headerRowWidth',
				'transparentHeaderRowBackground',
				'stickyHeaderRowBackground',
			],

			[
				...handleBackgroundOptionFor({
					id: 'headerRowBackground',
					selector: assembleSelector(getRootSelectorFor({ itemId })),
					responsive: true,

					addToDescriptors: {
						fullValue: true,
					},

					forced_background_image: true,

					valueExtractor: ({ headerRowBackground, headerRowWidth }) =>
						transformBgFor({
							background: headerRowBackground,
							headerRowWidth,
							predicate: ({ headerRowWidth }) =>
								headerRowWidth !== 'boxed',
						}),
				}).headerRowBackground,

				...handleBackgroundOptionFor({
					id: 'headerRowBackground',
					selector: assembleSelector(
						mutateSelector({
							selector: getRootSelectorFor({ itemId }),
							operation: 'suffix',
							to_add: '> div',
						})
					),

					responsive: true,
					forced_background_image: true,

					addToDescriptors: {
						fullValue: true,
					},

					valueExtractor: ({ headerRowBackground, headerRowWidth }) =>
						transformBgFor({
							background: headerRowBackground,
							headerRowWidth,
							predicate: ({ headerRowWidth }) =>
								headerRowWidth === 'boxed',
						}),
				}).headerRowBackground,

				// Transparent
				...handleBackgroundOptionFor({
					id: 'transparentHeaderRowBackground',

					forced_background_image: true,
					selector: assembleSelector(
						mutateSelector({
							selector: getRootSelectorFor({ itemId }),
							operation: 'el-prefix',
							to_add: '[data-transparent-row="yes"]',
						})
					),

					addToDescriptors: {
						fullValue: true,
					},

					valueExtractor: ({
						transparentHeaderRowBackground,
						headerRowWidth,
					}) =>
						transformBgFor({
							background: transparentHeaderRowBackground,
							headerRowWidth,
							predicate: ({ headerRowWidth }) =>
								headerRowWidth !== 'boxed',
						}),

					responsive: true,
				}).transparentHeaderRowBackground,

				...handleBackgroundOptionFor({
					id: 'transparentHeaderRowBackground',

					forced_background_image: true,
					selector: assembleSelector(
						mutateSelector({
							selector: mutateSelector({
								selector: getRootSelectorFor({ itemId }),
								operation: 'el-prefix',
								to_add: '[data-transparent-row="yes"]',
							}),

							operation: 'suffix',
							to_add: '> div',
						})
					),

					addToDescriptors: {
						fullValue: true,
					},

					valueExtractor: ({
						transparentHeaderRowBackground,
						headerRowWidth,
					}) =>
						transformBgFor({
							background: transparentHeaderRowBackground,
							headerRowWidth,
							predicate: ({ headerRowWidth }) =>
								headerRowWidth === 'boxed',
						}),

					responsive: true,
				}).transparentHeaderRowBackground,

				// Sticky
				...handleBackgroundOptionFor({
					id: 'stickyHeaderRowBackground',

					selector: assembleSelector(
						mutateSelector({
							selector: getRootSelectorFor({ itemId }),
							to_add: '[data-sticky*="yes"]',
						})
					),

					forced_background_image: true,
					addToDescriptors: {
						fullValue: true,
					},

					valueExtractor: ({
						stickyHeaderRowBackground,
						headerRowWidth,
					}) =>
						transformBgFor({
							background: stickyHeaderRowBackground,
							headerRowWidth,
							predicate: ({ headerRowWidth }) =>
								headerRowWidth !== 'boxed',
						}),

					responsive: true,
				}).stickyHeaderRowBackground,

				...handleBackgroundOptionFor({
					id: 'stickyHeaderRowBackground',

					selector: assembleSelector(
						mutateSelector({
							selector: mutateSelector({
								selector: getRootSelectorFor({ itemId }),
								to_add: '[data-sticky*="yes"]',
							}),

							operation: 'suffix',
							to_add: '> div',
						})
					),

					forced_background_image: true,
					addToDescriptors: {
						fullValue: true,
					},

					valueExtractor: ({
						stickyHeaderRowBackground,
						headerRowWidth,
					}) =>
						transformBgFor({
							background: stickyHeaderRowBackground,
							headerRowWidth,
							predicate: ({ headerRowWidth }) =>
								headerRowWidth === 'boxed',
						}),

					responsive: true,
				}).stickyHeaderRowBackground,
			]
		),
	}
}
