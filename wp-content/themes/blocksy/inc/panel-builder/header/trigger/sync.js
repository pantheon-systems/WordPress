import ctEvents from 'ct-events'
import { updateAndSaveEl } from '../../../../static/js/customizer/sync'
import {
	getRootSelectorFor,
	assembleSelector,
	mutateSelector,
	responsiveClassesFor,
} from '../../../../static/js/customizer/sync/helpers'
import { typographyOption } from '../../../../static/js/customizer/sync/variables/typography'

ctEvents.on(
	'ct:header:sync:collect-variable-descriptors',
	(variableDescriptors) => {
		variableDescriptors['trigger'] = ({ itemId }) => ({
			trigger_icon_size: {
				selector: assembleSelector(getRootSelectorFor({ itemId })),
				variable: 'icon-size',
				responsive: true,
				unit: 'px',
			},

			triggerMargin: {
				selector: assembleSelector(getRootSelectorFor({ itemId })),

				type: 'spacing',
				variable: 'margin',
				responsive: true,
				important: true,
			},

			trigger_border_radius: {
				selector: assembleSelector(getRootSelectorFor({ itemId })),
				variable: 'toggle-button-radius',
				unit: 'px',
			},

			// default state
			triggerIconColor: [
				{
					selector: assembleSelector(getRootSelectorFor({ itemId })),
					variable: 'icon-color',
					type: 'color:default',
					responsive: true,
				},

				{
					selector: assembleSelector(getRootSelectorFor({ itemId })),
					variable: 'icon-hover-color',
					type: 'color:hover',
					responsive: true,
				},
			],

			triggerSecondColor: [
				{
					selector: assembleSelector(getRootSelectorFor({ itemId })),
					variable: 'secondColor',
					type: 'color:default',
					responsive: true,
				},

				{
					selector: assembleSelector(getRootSelectorFor({ itemId })),
					variable: 'secondColorHover',
					type: 'color:hover',
					responsive: true,
				},
			],

			...typographyOption({
				id: 'trigger_label_font',

				selector: assembleSelector(
					mutateSelector({
						selector: getRootSelectorFor({ itemId }),
						operation: 'suffix',
						to_add: '.ct-label',
					})
				),
			}),

			header_trigger_font_color: [
				{
					selector: assembleSelector(getRootSelectorFor({ itemId })),
					variable: 'linkInitialColor',
					type: 'color:default',
					responsive: true,
				},

				{
					selector: assembleSelector(getRootSelectorFor({ itemId })),
					variable: 'linkHoverColor',
					type: 'color:hover',
					responsive: true,
				},
			],

			// transparent state
			transparent_header_trigger_font_color: [
				{
					selector: assembleSelector(
						mutateSelector({
							selector: getRootSelectorFor({ itemId }),
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
							selector: getRootSelectorFor({ itemId }),
							operation: 'between',
							to_add: '[data-transparent-row="yes"]',
						})
					),
					variable: 'linkHoverColor',
					type: 'color:hover',
					responsive: true,
				},
			],

			transparentTriggerIconColor: [
				{
					selector: assembleSelector(
						mutateSelector({
							selector: getRootSelectorFor({ itemId }),
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
							selector: getRootSelectorFor({ itemId }),
							operation: 'between',
							to_add: '[data-transparent-row="yes"]',
						})
					),

					variable: 'icon-hover-color',
					type: 'color:hover',
					responsive: true,
				},
			],

			transparentTriggerSecondColor: [
				{
					selector: assembleSelector(
						mutateSelector({
							selector: getRootSelectorFor({ itemId }),
							operation: 'between',
							to_add: '[data-transparent-row="yes"]',
						})
					),

					variable: 'secondColor',
					type: 'color:default',
					responsive: true,
				},

				{
					selector: assembleSelector(
						mutateSelector({
							selector: getRootSelectorFor({ itemId }),
							operation: 'between',
							to_add: '[data-transparent-row="yes"]',
						})
					),

					variable: 'secondColorHover',
					type: 'color:hover',
					responsive: true,
				},
			],

			// sticky state
			sticky_header_trigger_font_color: [
				{
					selector: assembleSelector(
						mutateSelector({
							selector: getRootSelectorFor({ itemId }),
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
							selector: getRootSelectorFor({ itemId }),
							operation: 'between',
							to_add: '[data-sticky*="yes"]',
						})
					),
					variable: 'linkHoverColor',
					type: 'color:hover',
					responsive: true,
				},
			],

			stickyTriggerIconColor: [
				{
					selector: assembleSelector(
						mutateSelector({
							selector: getRootSelectorFor({ itemId }),
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
							selector: getRootSelectorFor({ itemId }),
							operation: 'between',
							to_add: '[data-sticky*="yes"]',
						})
					),
					variable: 'icon-hover-color',
					type: 'color:hover',
					responsive: true,
				},
			],

			stickyTriggerSecondColor: [
				{
					selector: assembleSelector(
						mutateSelector({
							selector: getRootSelectorFor({ itemId }),
							operation: 'between',
							to_add: '[data-sticky*="yes"]',
						})
					),
					variable: 'secondColor',
					type: 'color:default',
					responsive: true,
				},

				{
					selector: assembleSelector(
						mutateSelector({
							selector: getRootSelectorFor({ itemId }),
							operation: 'between',
							to_add: '[data-sticky*="yes"]',
						})
					),
					variable: 'secondColorHover',
					type: 'color:hover',
					responsive: true,
				},
			],
		})
	}
)

ctEvents.on(
	'ct:header:sync:item:trigger',
	({ optionId, optionValue, values }) => {
		const selector = '[data-id="trigger"]'

		if (optionId === 'mobile_menu_trigger_type') {
			updateAndSaveEl(
				selector,
				(el) =>
					(el.querySelector('.ct-icon').dataset.type = optionValue)
			)
		}

		if (optionId === 'trigger_design') {
			updateAndSaveEl(selector, (el) => (el.dataset.design = optionValue))
		}

		if (optionId === 'trigger_label') {
			updateAndSaveEl(selector, (el) => {
				;[...el.querySelectorAll('.ct-label')].map((label) => {
					label.innerHTML = optionValue
				})
			})

			updateAndSaveEl(
				selector,
				(el) => {
					if (!optionValue.desktop) {
						optionValue = {
							desktop: optionValue,
							mobile: optionValue,
						}
					}

					;[...el.querySelectorAll('.ct-label')].map((label) => {
						label.innerHTML = optionValue.desktop
					})
				},
				{ onlyView: 'desktop' }
			)

			updateAndSaveEl(
				selector,
				(el) => {
					if (!optionValue.desktop) {
						optionValue = {
							desktop: optionValue,
							mobile: optionValue,
						}
					}

					;[...el.querySelectorAll('.ct-label')].map((label) => {
						label.innerHTML = optionValue.mobile
					})
				},
				{ onlyView: 'mobile' }
			)
		}

		if (optionId === 'trigger_label_visibility') {
			updateAndSaveEl(selector, (el) => {
				;[...el.querySelectorAll('.ct-label')].map((label) => {
					responsiveClassesFor(optionValue, label)
				})
			})
		}

		if (optionId === 'trigger_label_alignment') {
			updateAndSaveEl(
				selector,
				(el) => {
					if (!optionValue.desktop) {
						optionValue = {
							desktop: optionValue,
							mobile: optionValue,
						}
					}

					el.dataset.label = optionValue.desktop
				},
				{ onlyView: 'desktop' }
			)

			updateAndSaveEl(
				selector,
				(el) => {
					if (!optionValue.desktop) {
						optionValue = {
							desktop: optionValue,
							mobile: optionValue,
						}
					}

					el.dataset.label = optionValue.mobile
				},
				{ onlyView: 'mobile' }
			)
		}

		if (optionId === 'header_trigger_visibility') {
			updateAndSaveEl(selector, (el) =>
				responsiveClassesFor({ ...optionValue, desktop: true }, el)
			)
		}
	}
)
