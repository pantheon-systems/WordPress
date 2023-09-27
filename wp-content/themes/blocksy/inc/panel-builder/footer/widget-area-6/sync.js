import {
	handleWidgetAreaVariables,
	handleWidgetAreaOptions,
} from '../widget-area-1/sync'
import ctEvents from 'ct-events'

ctEvents.on(
	'ct:footer:sync:collect-variable-descriptors',
	(variableDescriptors) => {
		variableDescriptors['widget-area-6'] = handleWidgetAreaVariables({
			selector: '[data-column="widget-area-6"]',
		})
	}
)

ctEvents.on('ct:footer:sync:item:widget-area-6', (changeDescriptor) =>
	handleWidgetAreaOptions({
		selector: '[data-column="widget-area-6"]',
		changeDescriptor,
	})
)
