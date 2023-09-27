import { createElement, render } from '@wordpress/element'
import OptionsRoot from './OptionsRoot.js'
import { getValueFromInput } from './helpers/get-value-from-input'
import $ from 'jquery'

export const initAllPanels = () =>
	[...document.querySelectorAll('.ct-options-panel')].map((singleTarget) => {
		if (singleTarget.closest('[id="available-widgets"]')) {
			return
		}

		if (singleTarget.ctHasOptions) return
		singleTarget.ctHasOptions = true

		$(singleTarget).on('remove', () => setTimeout(() => initAllPanels()))
		$(singleTarget).on('remove', () => () => initAllPanels())

		render(
			<OptionsRoot
				options={JSON.parse(
					singleTarget.firstElementChild.dataset.ctOptions
				)}
				value={getValueFromInput(
					JSON.parse(
						singleTarget.firstElementChild.dataset.ctOptions
					),
					JSON.parse(singleTarget.firstElementChild.value),
					null,
					false
				)}
				input_id={singleTarget.firstElementChild.id}
				input_name={singleTarget.firstElementChild.name}
				hasRevertButton={
					Object.keys(singleTarget.dataset).indexOf(
						'disableReverseButton'
					) === -1
				}
			/>,
			singleTarget
		)
	})
