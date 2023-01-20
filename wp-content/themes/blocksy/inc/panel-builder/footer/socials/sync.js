import ctEvents from 'ct-events'
import { handleResponsiveSwitch } from '../../../../static/js/customizer/sync/helpers'
import {
	responsiveClassesFor,
	getRootSelectorFor,
	assembleSelector,
	mutateSelector,
	getColumnSelectorFor,
} from '../../../../static/js/customizer/sync/helpers'
import { typographyOption } from '../../../../static/js/customizer/sync/variables/typography'

ctEvents.on(
	'ct:footer:sync:collect-variable-descriptors',
	(variableDescriptors) => {
		variableDescriptors['socials'] = ({ fullItemId, itemId }) => ({
			socialsIconSize: {
				selector: assembleSelector(
					getRootSelectorFor({ itemId, panelType: 'footer' })
				),
				variable: 'icon-size',
				responsive: true,
				unit: 'px',
			},

			socialsIconSpacing: {
				selector: assembleSelector(
					getRootSelectorFor({ itemId, panelType: 'footer' })
				),
				variable: 'spacing',
				responsive: true,
				unit: 'px',
			},

			footerSocialsAlignment: {
				selector: assembleSelector(
					mutateSelector({
						selector: getRootSelectorFor({
							itemId,
							panelType: 'footer',
						}),
						operation: 'replace-last',
						to_add: getColumnSelectorFor({
							itemId: fullItemId,
						}),
					})
				),
				variable: 'horizontal-alignment',
				responsive: true,
				unit: '',
			},

			footerSocialsVerticalAlignment: {
				selector: assembleSelector(
					mutateSelector({
						selector: getRootSelectorFor({
							itemId,
							panelType: 'footer',
						}),
						operation: 'replace-last',
						to_add: getColumnSelectorFor({
							itemId: fullItemId,
						}),
					})
				),
				variable: 'vertical-alignment',
				responsive: true,
				unit: '',
			},

			...typographyOption({
				id: 'footer_socials_label_font',

				selector: assembleSelector(
					mutateSelector({
						selector: getRootSelectorFor({
							itemId,
							panelType: 'footer',
						}),
						operation: 'suffix',
						to_add: '.ct-label',
					})
				),
			}),

			footer_socials_font_color: [
				{
					selector: assembleSelector(
						mutateSelector({
							selector: getRootSelectorFor({
								itemId,
								panelType: 'footer',
							}),
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
							selector: getRootSelectorFor({
								itemId,
								panelType: 'footer',
							}),
							operation: 'suffix',
							to_add: 'a',
						})
					),
					variable: 'linkHoverColor',
					type: 'color:hover',
					responsive: true,
				},
			],

			footerSocialsIconColor: [
				{
					selector: assembleSelector(
						mutateSelector({
							selector: getRootSelectorFor({
								itemId,
								panelType: 'footer',
							}),
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
							selector: getRootSelectorFor({
								itemId,
								panelType: 'footer',
							}),
							operation: 'suffix',
							to_add: '[data-color="custom"]',
						})
					),
					variable: 'icon-hover-color',
					type: 'color:hover',
					responsive: true,
				},
			],

			footerSocialsIconBackground: [
				{
					selector: assembleSelector(
						mutateSelector({
							selector: getRootSelectorFor({
								itemId,
								panelType: 'footer',
							}),
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
							selector: getRootSelectorFor({
								itemId,
								panelType: 'footer',
							}),
							operation: 'suffix',
							to_add: '[data-color="custom"]',
						})
					),
					variable: 'background-hover-color',
					type: 'color:hover',
					responsive: true,
				},
			],

			footerSocialsMargin: {
				selector: assembleSelector(
					getRootSelectorFor({ itemId, panelType: 'footer' })
				),
				type: 'spacing',
				variable: 'margin',
				responsive: true,
				// important: true
			},

			socialsLabelVisibility: handleResponsiveSwitch({
				selector: assembleSelector(
					mutateSelector({
						selector: getRootSelectorFor({
							itemId,
							panelType: 'footer',
						}),
						operation: 'suffix',
						to_add: '.ct-label',
					})
				),
			}),

			footer_socials_direction: {
				selector: assembleSelector(
					getRootSelectorFor({ itemId, panelType: 'footer' })
				),
				variable: 'items-direction',
				responsive: true,
				unit: '',
			},
		})
	}
)

ctEvents.on(
	'ct:footer:sync:item:socials',
	({ itemId, optionId, optionValue, values }) => {
		const el = document.querySelector(`.ct-footer [data-id="${itemId}"]`)

		if (optionId === 'footer_socials_visibility') {
			responsiveClassesFor(optionValue, el)
		}

		if (optionId === 'socialsLabelVisibility') {
			if (
				optionValue.desktop ||
				optionValue.tablet ||
				optionValue.mobile
			) {
				;[...el.querySelectorAll('span.ct-label')].map((el) =>
					el.setAttribute('hidden', '')
				)
			} else {
				;[...el.querySelectorAll('span.ct-label')].map((el) =>
					el.removeAttribute('hidden')
				)
			}
		}

		if (optionId === 'socialsType' || optionId === 'socialsFillType') {
			const box = el.querySelector('.ct-social-box')

			box.dataset.iconsType = `${values.socialsType}${
				values.socialsType === 'simple'
					? ''
					: `:${values.socialsFillType || 'solid'}`
			}`
		}

		if (optionId === 'socialsIconSize') {
			el.querySelector('.ct-social-box').dataset.size =
				values.socialsIconSize
		}

		if (optionId === 'socialsLabelVisibility') {
			let socialsLabelVisibility = values.socialsLabelVisibility || {
				desktop: false,
				tablet: false,
				mobile: false,
			}

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
		}
	}
)
