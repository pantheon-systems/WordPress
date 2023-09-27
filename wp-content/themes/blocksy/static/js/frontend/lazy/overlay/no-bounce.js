import {
	clearAllBodyScrollLocks,
	enableBodyScroll,
	disableBodyScroll,
} from 'body-scroll-lock'

const isIosDevice =
	typeof window !== 'undefined' &&
	window.navigator &&
	window.navigator.platform &&
	(/iP(ad|hone|od)/.test(window.navigator.platform) ||
		(window.navigator.platform === 'MacIntel' &&
			window.navigator.maxTouchPoints > 1))

export var enable = function (el) {
	if (!isIosDevice) {
		document.body.style.overflow = ''
		document.body.style.removeProperty('--scrollbar-width')
	} else {
		clearAllBodyScrollLocks()
	}
}

export var disable = function (el) {
	if (!isIosDevice) {
		let scrollbarWidth =
			window.innerWidth - document.documentElement.clientWidth

		if (scrollbarWidth > 0) {
			document.body.style.setProperty(
				'--scrollbar-width',
				`${scrollbarWidth}px`
			)
		}

		document.body.style.overflow = 'hidden'
	} else {
		if (el) {
			disableBodyScroll(el, {
				// reserveScrollBarGap: true,

				allowTouchMove: (el) => {
					if (el.closest('.select2-container')) {
						return true
					}

					return false
				},
			})
		}
	}
}

export const scrollLockManager = () => {
	if (window.ctFrontend && window.ctFrontend.scrollLockManager) {
		return window.ctFrontend.scrollLockManager
	}

	window.ctFrontend = window.ctFrontend || {}

	window.ctFrontend.scrollLockManager = {
		enable,
		disable,
	}

	return window.ctFrontend.scrollLockManager
}
