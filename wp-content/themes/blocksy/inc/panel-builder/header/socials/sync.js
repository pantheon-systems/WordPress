import ctEvents from 'ct-events'
import { handleResponsiveSwitch } from '../../../../static/js/customizer/sync/helpers'
import { updateAndSaveEl } from '../../../../static/js/customizer/sync'
import {
	responsiveClassesFor,
	getRootSelectorFor,
	assembleSelector,
	mutateSelector,
} from '../../../../static/js/customizer/sync/helpers'
import { typographyOption } from '../../../../static/js/customizer/sync/variables/typography'

ctEvents.on(
	'ct:header:sync:collect-variable-descriptors',
	(variableDescriptors) => {
		variableDescriptors['socials'] = ({ itemId }) => ({
			socialsIconSize: {
				selector: assembleSelector(getRootSelectorFor({ itemId })),
				variable: 'icon-size',
				responsive: true,
				unit: 'px',
			},

			socialsIconSpacing: {
				selector: assembleSelector(getRootSelectorFor({ itemId })),
				variable: 'spacing',
				responsive: true,
				unit: 'px',
			},

			headerSocialsMargin: {
				selector: assembleSelector(getRootSelectorFor({ itemId })),
				type: 'spacing',
				variable: 'margin',
				responsive: true,
				important: true,
			},

			socialsLabelVisibility: handleResponsiveSwitch({
				selector: assembleSelector(
					mutateSelector({
						selector: getRootSelectorFor({ itemId }),
						operation: 'suffix',
						to_add: '.ct-label',
					})
				),
			}),

			...typographyOption({
				id: 'socials_label_font',

				selector: assembleSelector(
					mutateSelector({
						selector: getRootSelectorFor({ itemId }),
						operation: 'suffix',
						to_add: '.ct-label',
					})
				),
			}),

			// default state
			header_socials_font_color: [
				{
					selector: assembleSelector(
						mutateSelector({
							selector: getRootSelectorFor({ itemId }),
							operation: 'suffix',
							to_add: 'a',
						})
					),
					variable: 'linkInitialColor',
					type: 'color:default',
					responsive: true,
				},

				{
					selector: assembleSelector(
						mutateSelector({
							selector: getRootSelectorFor({ itemId }),
							operation: 'suffix',
							to_add: 'a',
						})
					),
					variable: 'linkHoverColor',
					type: 'color:hover',
					responsive: true,
				},
			],

			headerSocialsIconColor: [
				{
					selector: assembleSelector(
						mutateSelector({
							selector: getRootSelectorFor({ itemId }),
							operation: 'suffix',
							to_add: '[data-color="custom"]',
						})
					),
					variable: 'icon-color',
					type: 'color:default',
					responsive: true,
				},

				{
					selector: assembleSelector(
						mutateSelector({
							selector: getRootSelectorFor({ itemId }),
							operation: 'suffix',
							to_add: '[data-color="custom"]',
						})
					),
					variable: 'icon-hover-color',
					type: 'color:hover',
					responsive: true,
				},
			],

			headerSocialsIconBackground: [
				{
					selector: assembleSelector(
						mutateSelector({
							selector: getRootSelectorFor({ itemId }),
							operation: 'suffix',
							to_add: '[data-color="custom"]',
						})
					),
					variable: 'background-color',
					type: 'color:default',
					responsive: true,
				},

				{
					selector: assembleSelector(
						mutateSelector({
							selector: getRootSelectorFor({ itemId }),
							operation: 'suffix',
							to_add: '[data-color="custom"]',
						})
					),
					variable: 'background-hover-color',
					type: 'color:hover',
					responsive: true,
				},
			],

			// transparent state
			transparent_header_socials_font_color: [
				{
					selector: assembleSelector(
						mutateSelector({
							selector: mutateSelector({
								selector: getRootSelectorFor({ itemId }),
								operation: 'suffix',
								to_add: 'a',
							}),

							operation: 'between',
							to_add: '[data-transparent-row="yes"]',
						})
					),
					variable: 'linkInitialColor',
					type: 'color:default',
					responsive: true,
				},

				{
					selector: assembleSelector(
						mutateSelector({
							selector: mutateSelector({
								selector: getRootSelectorFor({ itemId }),
								operation: 'suffix',
								to_add: 'a',
							}),

							operation: 'between',
							to_add: '[data-transparent-row="yes"]',
						})
					),
					variable: 'linkHoverColor',
					type: 'color:hover',
					responsive: true,
				},
			],

			transparentHeaderSocialsIconColor: [
				{
					selector: assembleSelector(
						mutateSelector({
							selector: mutateSelector({
								selector: getRootSelectorFor({ itemId }),
								operation: 'suffix',
								to_add: '[data-color="custom"]',
							}),

							operation: 'between',
							to_add: '[data-transparent-row="yes"]',
						})
					),

					variable: 'icon-color',
					type: 'color:default',
					responsive: true,
				},

				{
					selector: assembleSelector(
						mutateSelector({
							selector: mutateSelector({
								selector: getRootSelectorFor({ itemId }),
								operation: 'suffix',
								to_add: '[data-color="custom"]',
							}),

							operation: 'between',
							to_add: '[data-transparent-row="yes"]',
						})
					),

					variable: 'icon-hover-color',
					type: 'color:hover',
					responsive: true,
				},
			],

			transparentHeaderSocialsIconBackground: [
				{
					selector: assembleSelector(
						mutateSelector({
							selector: mutateSelector({
								selector: getRootSelectorFor({ itemId }),
								operation: 'suffix',
								to_add: '[data-color="custom"]',
							}),

							operation: 'between',
							to_add: '[data-transparent-row="yes"]',
						})
					),

					variable: 'background-color',
					type: 'color:default',
					responsive: true,
				},

				{
					selector: assembleSelector(
						mutateSelector({
							selector: mutateSelector({
								selector: getRootSelectorFor({ itemId }),
								operation: 'suffix',
								to_add: '[data-color="custom"]',
							}),

							operation: 'between',
							to_add: '[data-transparent-row="yes"]',
						})
					),

					variable: 'background-hover-color',
					type: 'color:hover',
					responsive: true,
				},
			],

			// sticky state
			sticky_header_socials_font_color: [
				{
					selector: assembleSelector(
						mutateSelector({
							selector: mutateSelector({
								selector: getRootSelectorFor({ itemId }),
								operation: 'suffix',
								to_add: 'a',
							}),

							operation: 'between',
							to_add: '[data-sticky*="yes"]',
						})
					),
					variable: 'linkInitialColor',
					type: 'color:default',
					responsive: true,
				},

				{
					selector: assembleSelector(
						mutateSelector({
							selector: mutateSelector({
								selector: getRootSelectorFor({ itemId }),
								operation: 'suffix',
								to_add: 'a',
							}),

							operation: 'between',
							to_add: '[data-sticky*="yes"]',
						})
					),
					variable: 'linkHoverColor',
					type: 'color:hover',
					responsive: true,
				},
			],

			stickyHeaderSocialsIconColor: [
				{
					selector: assembleSelector(
						mutateSelector({
							selector: mutateSelector({
								selector: getRootSelectorFor({ itemId }),
								operation: 'suffix',
								to_add: '[data-color="custom"]',
							}),

							operation: 'between',
							to_add: '[data-sticky*="yes"]',
						})
					),
					variable: 'icon-color',
					type: 'color:default',
					responsive: true,
				},

				{
					selector: assembleSelector(
						mutateSelector({
							selector: mutateSelector({
								selector: getRootSelectorFor({ itemId }),
								operation: 'suffix',
								to_add: '[data-color="custom"]',
							}),

							operation: 'between',
							to_add: '[data-sticky*="yes"]',
						})
					),
					variable: 'icon-hover-color',
					type: 'color:hover',
					responsive: true,
				},
			],

			stickyHeaderSocialsIconBackground: [
				{
					selector: assembleSelector(
						mutateSelector({
							selector: mutateSelector({
								selector: getRootSelectorFor({ itemId }),
								operation: 'suffix',
								to_add: '[data-color="custom"]',
							}),

							operation: 'between',
							to_add: '[data-sticky*="yes"]',
						})
					),
					variable: 'background-color',
					type: 'color:default',
					responsive: true,
				},

				{
					selector: assembleSelector(
						mutateSelector({
							selector: mutateSelector({
								selector: getRootSelectorFor({ itemId }),
								operation: 'suffix',
								to_add: '[data-color="custom"]',
							}),

							operation: 'between',
							to_add: '[data-sticky*="yes"]',
						})
					),
					variable: 'background-hover-color',
					type: 'color:hover',
					responsive: true,
				},
			],
		})
	}
)

ctEvents.on(
	'ct:header:sync:item:socials',
	({ itemId, optionId, optionValue, values }) => {
		const selector = `[data-id="${itemId}"]`

		if (optionId === 'socialsType' || optionId === 'socialsFillType') {
			updateAndSaveEl(selector, (el) => {
				const box = el.querySelector('.ct-social-box')

				box.dataset.iconsType = `${values.socialsType}${
					values.socialsType === 'simple'
						? ''
						: `:${values.socialsFillType || 'solid'}`
				}`
			})
		}

		if (optionId === 'socialsIconSize') {
			updateAndSaveEl(
				selector,
				(el) =>
					(el.querySelector('.ct-social-box').dataset.size =
						values.socialsIconSize)
			)
		}

		if (optionId === 'visibility') {
			updateAndSaveEl(selector, (el) =>
				responsiveClassesFor({ ...optionValue, desktop: true }, el)
			)
		}

		if (optionId === 'socialsLabelVisibility') {
			const socialsLabelVisibility = values.socialsLabelVisibility || {
				desktop: false,
				tablet: false,
				mobile: false,
			}

			updateAndSaveEl(selector, (el) => {
				if (
					socialsLabelVisibility.desktop ||
					socialsLabelVisibility.tablet ||
					socialsLabelVisibility.mobile
				) {
					;[...el.querySelectorAll('span.ct-label')].map((el) =>
						el.removeAttribute('hidden')
					)
				} else {
					;[...el.querySelectorAll('span.ct-label')].map((el) =>
						el.setAttribute('hidden', '')
					)
				}
			})
		}
	}
)
