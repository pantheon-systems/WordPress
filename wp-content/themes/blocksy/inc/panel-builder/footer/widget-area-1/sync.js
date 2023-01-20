import ctEvents from 'ct-events'
import {
	getRootSelectorFor,
	assembleSelector,
	mutateSelector,
} from '../../../../static/js/customizer/sync/helpers'

export const handleWidgetAreaVariables = ({ selector }) => ({ itemId }) => ({

	horizontal_alignment: [
		{
			selector: assembleSelector(
				mutateSelector({
					selector: getRootSelectorFor({
						itemId,
						panelType: 'footer',
					}),
					operation: 'replace-last',
					to_add: selector,
				})
			),
			variable: 'text-horizontal-alignment',
			responsive: true,
			unit: '',
		},

		{
			selector: assembleSelector(
				mutateSelector({
					selector: getRootSelectorFor({
						itemId,
						panelType: 'footer',
					}),
					operation: 'replace-last',
					to_add: selector,
				})
			),
			variable: 'horizontal-alignment',
			responsive: true,
			unit: '',
			extractValue: (value) => {
				if (!value.desktop) {
					return value
				}

				if (value.desktop === 'left') {
					value.desktop = 'flex-start'
				}

				if (value.desktop === 'right') {
					value.desktop = 'flex-end'
				}

				if (value.tablet === 'left') {
					value.tablet = 'flex-start'
				}

				if (value.tablet === 'right') {
					value.tablet = 'flex-end'
				}

				if (value.mobile === 'left') {
					value.mobile = 'flex-start'
				}

				if (value.mobile === 'right') {
					value.mobile = 'flex-end'
				}

				return value
			},
		},
	],

	vertical_alignment: {
		selector: assembleSelector(
			mutateSelector({
				selector: getRootSelectorFor({
					itemId,
					panelType: 'footer',
				}),
				operation: 'replace-last',
				to_add: selector,
			})
		),
		variable: 'vertical-alignment',
		responsive: true,
		unit: '',
	},

	widget_area_colors: [
		{
			selector: assembleSelector(
				mutateSelector({
					selector: mutateSelector({
						selector: getRootSelectorFor({
							itemId,
							panelType: 'footer',
						}),
						operation: 'replace-last',
						to_add: selector,
					}),
					operation: 'suffix',
					to_add: '.ct-widget',
				})
			),
			variable: 'color',
			type: 'color:default',
			responsive: true,
		},

		{
			selector: assembleSelector(
				mutateSelector({
					selector: mutateSelector({
						selector: getRootSelectorFor({
							itemId,
							panelType: 'footer',
						}),
						operation: 'replace-last',
						to_add: selector,
					}),
					operation: 'suffix',
					to_add: '.ct-widget',
				})
			),
			variable: 'linkInitialColor',
			type: 'color:link_initial',
			responsive: true,
		},

		{
			selector: assembleSelector(
				mutateSelector({
					selector: mutateSelector({
						selector: getRootSelectorFor({
							itemId,
							panelType: 'footer',
						}),
						operation: 'replace-last',
						to_add: selector,
					}),
					operation: 'suffix',
					to_add: '.ct-widget',
				})
			),
			variable: 'linkHoverColor',
			type: 'color:link_hover',
			responsive: true,
		},
	],

	widget_area_margin: {
		selector: assembleSelector(
			mutateSelector({
				selector: getRootSelectorFor({
					itemId,
					panelType: 'footer',
				}),
				operation: 'replace-last',
				to_add: selector,
			})
		),
		type: 'spacing',
		variable: 'margin',
		responsive: true,
		important: true,
	},
})

export const handleWidgetAreaOptions = ({
	selector,
	changeDescriptor: { optionId, optionValue, values },
}) => {
	let el = document.querySelector(selector)

	if (optionId === 'widgets_link_type') {
		el.removeAttribute('data-link')

		if (optionValue !== 'inherit') {
			el.dataset.link = optionValue
		}
	}
}

ctEvents.on(
	'ct:footer:sync:collect-variable-descriptors',
	(variableDescriptors) => {
		variableDescriptors['widget-area-1'] = handleWidgetAreaVariables({
			selector: '[data-column="widget-area-1"]',
		})
	}
)

ctEvents.on('ct:footer:sync:item:widget-area-1', (changeDescriptor) =>
	handleWidgetAreaOptions({
		selector: '[data-column="widget-area-1"]',
		changeDescriptor,
	})
)
