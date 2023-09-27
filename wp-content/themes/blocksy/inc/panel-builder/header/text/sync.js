import { typographyOption } from '../../../../static/js/customizer/sync/variables/typography'
import ctEvents from 'ct-events'
import { updateAndSaveEl } from '../../../../static/js/customizer/sync'
import { responsiveClassesFor } from '../../../../static/js/customizer/sync/helpers'
import {
	getRootSelectorFor,
	assembleSelector,
	mutateSelector,
	getColumnSelectorFor,
} from '../../../../static/js/customizer/sync/helpers'

const getVariables = ({ itemId, fullItemId, panelType }) => ({
	headerTextMaxWidth: {
		selector: assembleSelector(getRootSelectorFor({ itemId, panelType })),
		variable: 'max-width',
		responsive: true,
		unit: '%',
	},

	...typographyOption({
		id: 'headerTextFont',
		selector: assembleSelector(getRootSelectorFor({ itemId, panelType })),
	}),

	headerTextMargin: {
		selector: assembleSelector(getRootSelectorFor({ itemId, panelType })),
		type: 'spacing',
		variable: 'margin',
		responsive: true,
		important: true,
	},

	// default state
	headerTextColor: [
		{
			selector: assembleSelector(
				getRootSelectorFor({ itemId, panelType })
			),
			variable: 'color',
			type: 'color:default',
			responsive: true,
		},

		{
			selector: assembleSelector(
				getRootSelectorFor({ itemId, panelType })
			),
			variable: 'linkInitialColor',
			type: 'color:link_initial',
			responsive: true,
		},

		{
			selector: assembleSelector(
				getRootSelectorFor({ itemId, panelType })
			),
			variable: 'linkHoverColor',
			type: 'color:link_hover',
			responsive: true,
		},
	],

	// transparent state
	transparentHeaderTextColor: [
		{
			selector: assembleSelector(
				mutateSelector({
					selector: getRootSelectorFor({ itemId, panelType }),
					operation: 'between',
					to_add: '[data-transparent-row="yes"]',
				})
			),

			variable: 'color',
			type: 'color:default',
			responsive: true,
		},

		{
			selector: assembleSelector(
				mutateSelector({
					selector: getRootSelectorFor({ itemId, panelType }),
					operation: 'between',
					to_add: '[data-transparent-row="yes"]',
				})
			),

			variable: 'linkInitialColor',
			type: 'color:link_initial',
			responsive: true,
		},

		{
			selector: assembleSelector(
				mutateSelector({
					selector: getRootSelectorFor({ itemId, panelType }),
					operation: 'between',
					to_add: '[data-transparent-row="yes"]',
				})
			),

			variable: 'linkHoverColor',
			type: 'color:link_hover',
			responsive: true,
		},
	],

	// sticky state
	stickyHeaderTextColor: [
		{
			selector: assembleSelector(
				mutateSelector({
					selector: getRootSelectorFor({ itemId, panelType }),
					operation: 'between',
					to_add: '[data-sticky*="yes"]',
				})
			),
			variable: 'color',
			type: 'color:default',
			responsive: true,
		},

		{
			selector: assembleSelector(
				mutateSelector({
					selector: getRootSelectorFor({ itemId, panelType }),
					operation: 'between',
					to_add: '[data-sticky*="yes"]',
				})
			),
			variable: 'linkInitialColor',
			type: 'color:link_initial',
			responsive: true,
		},

		{
			selector: assembleSelector(
				mutateSelector({
					selector: getRootSelectorFor({ itemId, panelType }),
					operation: 'between',
					to_add: '[data-sticky*="yes"]',
				})
			),
			variable: 'linkHoverColor',
			type: 'color:link_hover',
			responsive: true,
		},
	],

	header_html_horizontal_alignment: {
		selector: assembleSelector(getRootSelectorFor({ itemId, panelType })),
		variable: 'horizontal-alignment',
		responsive: true,
		unit: '',
	},

	// footer html
	footer_html_horizontal_alignment: {
		selector: assembleSelector(
			mutateSelector({
				selector: getRootSelectorFor({
					itemId,
					panelType: 'footer',
				}),
				operation: 'replace-last',
				to_add: getColumnSelectorFor({ itemId: fullItemId }),
			})
		),
		variable: 'horizontal-alignment',
		responsive: true,
		unit: '',
	},

	footer_html_vertical_alignment: {
		selector: assembleSelector(
			mutateSelector({
				selector: getRootSelectorFor({
					itemId,
					panelType: 'footer',
				}),
				operation: 'replace-last',
				to_add: getColumnSelectorFor({ itemId: fullItemId }),
			})
		),
		variable: 'vertical-alignment',
		responsive: true,
		unit: '',
	},
})

ctEvents.on(
	'ct:header:sync:collect-variable-descriptors',
	(variableDescriptors) => {
		variableDescriptors['text'] = ({ itemId, fullItemId }) =>
			getVariables({ itemId, fullItemId, panelType: 'header' })
	}
)

ctEvents.on(
	'ct:footer:sync:collect-variable-descriptors',
	(variableDescriptors) => {
		variableDescriptors['text'] = ({ itemId, fullItemId }) =>
			getVariables({ itemId, fullItemId, panelType: 'footer' })
	}
)

ctEvents.on('ct:header:sync:item:text', ({ itemId, optionId, optionValue }) => {
	const selector = `[data-id="${itemId}"]`

	if (optionId === 'visibility') {
		updateAndSaveEl(selector, (el) =>
			responsiveClassesFor({ ...optionValue, desktop: true }, el)
		)
	}

	if (optionId === 'header_text') {
		updateAndSaveEl(selector, (el) => {
			el.querySelector('.entry-content').innerHTML = optionValue
		})
	}

	if (optionId === 'has_header_text_full_width') {
		updateAndSaveEl(selector, (el) => {
			el.removeAttribute('data-width')

			if (optionValue === 'yes') {
				el.dataset.width = 'stretch'
			}
		})
	}
})

ctEvents.on('ct:footer:sync:item:text', ({ itemId, optionId, optionValue }) => {
	const selector = `.ct-footer [data-id="${itemId}"]`
	const el = document.querySelector(selector)

	if (optionId === 'footer_visibility') {
		responsiveClassesFor(optionValue, el)
	}

	if (optionId === 'header_text') {
		el.querySelector('.entry-content').innerHTML = optionValue
	}

	if (optionId === 'has_header_text_full_width') {
		el.removeAttribute('data-width')

		if (optionValue === 'yes') {
			el.dataset.width = 'stretch'
		}
	}
})
