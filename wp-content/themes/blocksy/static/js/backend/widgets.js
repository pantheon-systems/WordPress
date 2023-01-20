import { initAllPanels } from '../options/initPanels'

export const initWidget = (widget) => {
	if (
		widget.querySelector('.ct-options-panel') &&
		widget.querySelector('.ct-options-panel').innerHTML.indexOf('__i__') >
			-1
	) {
		const panel = widget.querySelector('.ct-options-panel')

		const widgetNumber = widget.querySelector('input.multi_number').value
		panel.innerHTML = panel.innerHTML.replace(/__i__|%i%/g, widgetNumber)
	}

	initAllPanels()
}
