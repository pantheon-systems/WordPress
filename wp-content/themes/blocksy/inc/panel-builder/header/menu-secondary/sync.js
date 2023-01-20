import { handleMenuVariables, handleMenuOptions } from '../menu/sync'
import ctEvents from 'ct-events'

ctEvents.on(
	'ct:header:sync:collect-variable-descriptors',
	(variableDescriptors) => {
		variableDescriptors['menu-secondary'] = handleMenuVariables
		variableDescriptors['menu-tertiary'] = handleMenuVariables
	}
)

ctEvents.on('ct:header:sync:item:menu-secondary', (changeDescriptor) => {
	handleMenuOptions({
		selector: '.header-menu-2',
		changeDescriptor,
	})
})

ctEvents.on('ct:header:sync:item:menu-tertiary', (changeDescriptor) => {
	handleMenuOptions({
		selector: '.header-menu-3',
		changeDescriptor,
	})
})
