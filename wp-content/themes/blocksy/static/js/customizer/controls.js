import './public-path'
import {
	createElement,
	render,
	unmountComponentAtNode,
} from '@wordpress/element'
import { defineCustomizerControl } from './controls/utils.js'
import { listenToChanges } from './customizer-color-scheme.js'
import './preview-events'
import { listenToVariables } from './customizer-variables'
import './reset'
import { initAllPanels } from '../options/initPanels'

import { initBuilder } from './panels-builder'

import Options from './controls/options.js'
import { initWidget } from '../backend/widgets'

import $ from 'jquery'
import ctEvents from 'ct-events'
import ProOverlay from './components/ProOverlay'

import WidgetArea from './options/ct-widget-area'

ctEvents.on('blocksy:options:register', (opts) => {
	opts['ct-widget-area'] = WidgetArea
})

listenToChanges()
listenToVariables()

defineCustomizerControl('ct-options', Options)

if ($ && $.fn) {
	$(document).on('widget-added', (event, widget) => {
		initWidget(widget[0])
	})

	if (wp && wp.customize && wp.customize.control) {
		wp.customize.control.bind('add', (control) => {
			setTimeout(() => {
				initWidget(control.container[0])
			}, 100)
		})
	}
}

document.addEventListener('DOMContentLoaded', () => {
	initAllPanels()
	initBuilder()

	setTimeout(() => {
		if (document.querySelector('.ct-onboarding-button')) {
			render(
				<ProOverlay
					username={
						document.querySelector('.ct-onboarding-button button')
							.dataset.username
					}
				/>,
				document.querySelector('.ct-onboarding-button')
			)
		}
	}, 50)

	setTimeout(() => {
		Object.values(wp.customize.control._value)
			.filter(({ params: { type } }) => type === 'ct-options')
			.map((control) => {
				if (wp.customize.section(control.section)) {
					wp.customize
						.section(control.section)
						.container.on('keydown', function (event) {
							return

							// Pressing the escape key fires a theme:collapse event
							if (27 === event.keyCode) {
								if (section.$body.hasClass('modal-open')) {
									// Escape from the details modal.
									section.closeDetails()
								} else {
									// Escape from the inifinite scroll list.
									section.headerContainer
										.find('.customize-themes-section-title')
										.focus()
								}
								event.stopPropagation() // Prevent section from being collapsed.
							}
						})
				}

				;(wp.customize.panel(control.section())
					? wp.customize.panel
					: wp.customize.section)(control.section(), (section) => {
					section.expanded.bind((value) => {
						if (value) {
							const ChildComponent = Options

							let MyChildComponent = Options

							// block | inline
							let design = 'none'

							render(
								<MyChildComponent
									id={control.id}
									onChange={(v) => control.setting.set(v)}
									value={control.setting.get()}
									option={control.params.option}>
									{(props) => <ChildComponent {...props} />}
								</MyChildComponent>,

								control.container[0]
							)

							return
						}

						setTimeout(() => {
							unmountComponentAtNode(control.container[0])
						}, 500)
					})
				})
			})
	})

	if ($ && $.fn) {
		$(document).on('click', '[data-trigger-section]', (e) => {
			e.preventDefault()

			wp.customize.previewer.trigger(
				'ct-initiate-deep-link',
				e.target.dataset.triggerSection
			)
		})

		var urlParams = new URLSearchParams(window.location.search)
		if (urlParams.get('ct_autofocus')) {
			wp.customize.previewer.trigger(
				'ct-initiate-deep-link',
				urlParams.get('ct_autofocus')
			)
		}
	}
})

export { default as Overlay } from './components/Overlay'
export { getValueFromInput } from '../options/helpers/get-value-from-input'
export { default as OptionsPanel } from '../options/OptionsPanel'
export { default as Panel, PanelMetaWrapper } from '../options/options/ct-panel'
export { DeviceManagerProvider } from './components/useDeviceManager'
export { default as PanelLevel } from '../options/components/PanelLevel'
export { default as Switch } from '../options/options/ct-switch'
export { default as ImageUploader } from '../options/options/ct-image-uploader'
export { default as Select } from '../options/options/ct-select'

export { default as OutsideClickHandler } from '../options/options/react-outside-click-handler'

export { Transition, animated } from 'react-spring/renderprops'
export { default as bezierEasing } from 'bezier-easing'
export { default as usePopoverMaker } from '../options/helpers/usePopoverMaker'

/**
 * Expose builder values
 */
export { DragDropContext as PlacementsDragDropContext } from './panels-builder/placements/BuilderRoot'
export { DragDropContext as ColumnsDragDropContext } from '../options/options/ct-footer-builder'
export const onDocumentLoaded = (cb) => {
	if (/comp|inter|loaded/.test(document.readyState)) {
		cb()
	} else {
		document.addEventListener('DOMContentLoaded', cb, false)
	}
}
