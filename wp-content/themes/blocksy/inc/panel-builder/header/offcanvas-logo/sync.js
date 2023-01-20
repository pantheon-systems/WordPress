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
	off_canvas_logo_max_height: {
		selector: assembleSelector(getRootSelectorFor({ itemId, panelType })),
		variable: 'logo-max-height',
		responsive: true,
		unit: 'px',
	},

	off_canvas_logo_margin: {
		selector: assembleSelector(getRootSelectorFor({ itemId, panelType })),
		type: 'spacing',
		variable: 'margin',
		responsive: true,
		important: true,
	},
})

ctEvents.on(
	'ct:header:sync:collect-variable-descriptors',
	(variableDescriptors) => {
		variableDescriptors['offcanvas-logo'] = ({ itemId }) =>
			getVariables({ itemId, panelType: 'header' })
	}
)
