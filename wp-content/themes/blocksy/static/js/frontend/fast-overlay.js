import { loadStyle } from '../helpers'

export const fastOverlayHandleClick = (e, settings) => {
	settings = {
		container: null,

		// full | fast | skip
		openStrategy: 'full',
		...settings,
	}

	if (
		document.body.hasAttribute('data-panel') &&
		settings.openStrategy !== 'skip'
	) {
		return
	}

	if (settings.openStrategy !== 'skip') {
		if (settings.container) {
			settings.container.classList.add('active')
		}

		document.body.dataset.panel = `in${
			settings.container.dataset.behaviour.indexOf('left') > -1
				? ':left'
				: settings.container.dataset.behaviour.indexOf('right') > -1
				? ':right'
				: ''
		}`
	}

	if (settings.openStrategy === 'full' || settings.openStrategy === 'skip') {
		import('./lazy/overlay').then(({ handleClick }) => {
			handleClick(e, settings)
		})
	}
}

export const fastOverlayMount = (el, { event, focus = false }) => {
	fastOverlayHandleClick(event, {
		isModal: true,
		container: document.querySelector(el.dataset.togglePanel || el.hash),
		clickOutside: true,
		focus,
	})
}
