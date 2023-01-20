import { getCurrentScreen } from '../helpers/current-screen'
import { isTouchDevice } from '../helpers/is-touch-device'

const tabbables = [
	'button:enabled:not([readonly])',
	'select:enabled:not([readonly])',
	'textarea:enabled:not([readonly])',
	'input:enabled:not([readonly])',

	'a[href]',
	'area[href]',

	'iframe',
	'object',
	'embed',

	'[tabindex]',
	'[contenteditable]',
	'[autofocus]',
]

let lockedElement = null

const handleKeydown = (e) => {
	if (!lockedElement) {
		return
	}

	let focusableEls = [...lockedElement.querySelectorAll(tabbables.join(','))]

	if (
		lockedElement.querySelector('[data-device="mobile"]') &&
		getCurrentScreen() !== 'mobile'
	) {
		focusableEls = focusableEls.filter(
			(el) => !el.closest('[data-device="mobile"]')
		)
	}

	const firstFocusableEl = focusableEls[0]
	const lastFocusableEl = focusableEls[focusableEls.length - 1]

	if (e.key !== 'Tab' && e.keyCode !== 9) {
		return
	}

	if (!focusableEls.includes(document.activeElement)) {
		firstFocusableEl.focus()
		e.preventDefault()
	}

	if (e.shiftKey) {
		if (document.activeElement === firstFocusableEl) {
			lastFocusableEl.focus()
			e.preventDefault()
		}
	} else {
		if (document.activeElement === lastFocusableEl) {
			firstFocusableEl.focus()
			e.preventDefault()
		}
	}
}

const focusLockOn = (element, settings = {}) => {
	settings = {
		focusOnMount: true,
		...settings,
	}

	if (lockedElement && lockedElement !== element) {
		return
	}

	const focusableEls = element.querySelectorAll(tabbables.join(','))

	if (focusableEls.length === 0) {
		return
	}

	lockedElement = element
	document.addEventListener('keydown', handleKeydown)

	if (settings.focusOnMount && !isTouchDevice()) {
		setTimeout(() => {
			focusableEls[0].focus()
		}, 200)
	}
}

const focusLockOff = (element) => {
	element.removeEventListener('keydown', handleKeydown)
	lockedElement = null
}

export const focusLockManager = () => {
	if (window.ctFrontend && window.ctFrontend.focusLockManager) {
		return window.ctFrontend.focusLockManager
	}

	window.ctFrontend = window.ctFrontend || {}

	window.ctFrontend.focusLockManager = {
		focusLockOn,
		focusLockOff,
	}

	return window.ctFrontend.focusLockManager
}
