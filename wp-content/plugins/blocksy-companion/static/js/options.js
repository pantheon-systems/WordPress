import { createElement, Fragment, Component } from '@wordpress/element'
import { Fill } from '@wordpress/components'
import DisplayCondition from './options/DisplayCondition'
import CustomizerOptionsManager from './options/CustomizerOptionsManager'

import { onDocumentLoaded } from 'blocksy-options'

import ctEvents from 'ct-events'

import PanelsManager from './header/PanelsManager'

ctEvents.on('blocksy:options:before-option', (args) => {
	if (!args.option) {
		return
	}

	if (args.option.type === 'ct-header-builder') {
		let prevHeaderBuilder = args.content

		args.content = (
			<Fragment>
				{prevHeaderBuilder}

				<Fill name="PlacementsBuilderPanelsManager">
					<PanelsManager />
				</Fill>
			</Fragment>
		)
	}
})

ctEvents.on('blocksy:options:register', (opts) => {
	opts['blocksy-display-condition'] = DisplayCondition
	opts['blocksy-customizer-options-manager'] = CustomizerOptionsManager
})
