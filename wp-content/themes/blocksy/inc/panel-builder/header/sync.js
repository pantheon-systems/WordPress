import { handleBackgroundOptionFor } from '../../../static/js/customizer/sync/variables/background'
import ctEvents from 'ct-events'
import { updateAndSaveEl } from '../../../static/js/frontend/header/render-loop'

ctEvents.on(
	'ct:header:sync:collect-variable-descriptors',
	(variableDescriptors) => {
		/*
		const handleBackgroundOptionForSpecific = id =>
			handleBackgroundOptionFor({
				id,
				selector: 'header',
				addToDescriptors: {
					fullValue: true
				},
				responsive: true,
				valueExtractor: ({
					is_absolute,
					headerBackground,
					absoluteHeaderBackground
				}) =>
					is_absolute === 'yes'
						? absoluteHeaderBackground
						: headerBackground
			})

		variableDescriptors['global'] = {
			...handleBackgroundOptionForSpecific('is_absolute'),
			...handleBackgroundOptionForSpecific('headerBackground'),
			...handleBackgroundOptionForSpecific('absoluteHeaderBackground')
		}
        */
	}
)

ctEvents.on('ct:header:sync:item:global', ({ optionId, optionValue }) => {})
