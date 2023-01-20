import ctEvents from 'ct-events'
import { isTouchDevice } from './frontend/helpers/is-touch-device'

const loadSingleEntryPoint = ({
	els,
	events,
	forcedEvents,
	load,
	mount,
	condition,
	trigger,
}) => {
	if (!els) {
		els = []
	}

	if (!events) {
		events = []
	}

	if (!forcedEvents) {
		forcedEvents = []
	}

	if (!trigger) {
		trigger = []
	}

	if (!mount) {
		mount = ({ mount, el, ...everything }) =>
			el ? mount(el, everything) : mount()
	}

	if (els && {}.toString.call(els) === '[object Function]') {
		els = els()
	}

	const allEls = (Array.isArray(els) ? els : [els]).reduce(
		(a, selector) => [
			...a,
			...(Array.isArray(selector)
				? selector
				: typeof selector === 'string'
				? document.querySelectorAll(selector)
				: [selector]),
		],
		[]
	)

	if (allEls.length === 0) {
		return
	}

	if (
		condition &&
		!condition({
			els,
			allEls,
		})
	) {
		return
	}

	if (trigger.length > 0) {
		if (trigger.includes('click')) {
			allEls.map((el) => {
				if (el.hasLazyLoadClickListener) {
					return
				}

				el.hasLazyLoadClickListener = true

				el.addEventListener('click', (event) => {
					event.preventDefault()
					load().then((arg) => mount({ ...arg, event, el }))
				})
			})
		}

		if (trigger.includes('scroll')) {
			allEls.map((el) => {
				if (el.hasLazyLoadScrollListener) {
					return
				}

				el.hasLazyLoadScrollListener = true

				setTimeout(() => {
					let prevScroll = scrollY

					let cb = (event) => {
						if (Math.abs(scrollY - prevScroll) > 30) {
							document.removeEventListener('scroll', cb)

							load().then((arg) => {
								return mount({ ...arg, event, el })
							})

							return
						}
					}

					document.addEventListener('scroll', cb)
				}, 500)
			})
		}

		if (trigger.includes('input')) {
			allEls.map((el) => {
				if (el.hasLazyLoadInputListener) {
					return
				}

				el.hasLazyLoadInputListener = true

				el.addEventListener(
					'input',
					(event) => load().then((arg) => mount({ ...arg, el })),
					{ once: true }
				)
			})
		}

		if (trigger.includes('hover-with-touch')) {
			allEls.map((el) => {
				if (el.hasLazyLoadMouseOverListener) {
					return
				}

				if (el.dataset.autoplay && parseFloat(el.dataset.autoplay)) {
					setTimeout(() => {
						load().then((arg) =>
							mount({
								...arg,
								el,
							})
						)
					}, parseFloat(el.dataset.autoplay) * 1000)
					return
				}

				el.hasLazyLoadMouseOverListener = true

				el.forcedMount = (data = {}) =>
					load().then((arg) => mount({ ...arg, el, ...data }))
				;['mouseover', ...(isTouchDevice() ? ['touchstart'] : [])].map(
					(eventToRegister) => {
						el.addEventListener(
							eventToRegister,
							(event) => {
								load().then((arg) =>
									mount({
										...arg,
										...(event.type === 'touchstart'
											? { event }
											: {}),
										el,
									})
								)
							},
							{ once: true, passive: true }
						)
					}
				)
			})
		}

		if (trigger.includes('hover-with-click')) {
			allEls.map((el) => {
				if (el.hasLazyLoadClickHoverListener) {
					return
				}

				el.hasLazyLoadClickHoverListener = true

				el.addEventListener(
					isTouchDevice() ? 'click' : 'mouseover',
					(event) => {
						event.preventDefault()

						load().then((arg) =>
							mount({
								...arg,
								event,
								el,
							})
						)
					},
					{ once: true }
				)
			})
		}

		if (trigger.includes('hover')) {
			allEls.map((el) => {
				if (el.hasLazyLoadMouseOverListener) {
					return
				}

				el.hasLazyLoadHoverListener = true

				el.addEventListener(
					'mouseover',
					(event) => {
						load().then((arg) =>
							mount({
								...arg,
								event,
								el,
							})
						)
					},
					{ once: true }
				)
			})
		}

		if (trigger.includes('submit')) {
			allEls.map((el) => {
				if (el.hasLazyLoadSubmitListener) {
					return
				}

				el.hasLazyLoadSubmitListener = true

				el.addEventListener('submit', (event) => {
					event.preventDefault()
					load().then((arg) => mount({ ...arg, event, el }))
				})
			})
		}
	} else {
		load().then((arg) => {
			allEls.map((el) => {
				mount({ ...arg, el })
			})
		})
	}
}

export const onDocumentLoaded = (cb) => {
	if (/comp|inter|loaded/.test(document.readyState)) {
		cb()
	} else {
		document.addEventListener('DOMContentLoaded', cb, false)
	}
}

export const handleEntryPoints = (mountEntryPoints, args) => {
	const { immediate = false, skipEvents = false } = args || {}

	if (!skipEvents) {
		;[
			...new Set(
				mountEntryPoints.reduce(
					(currentEvents, entry) => [
						...currentEvents,
						...(entry.events || []),
						...(entry.forcedEvents || []),
					],
					[]
				)
			),
		].map((distinctEvent) => {
			ctEvents.on(distinctEvent, () => {
				mountEntryPoints
					.filter(
						({ events = [] }) => events.indexOf(distinctEvent) > -1
					)
					.map((c) => loadSingleEntryPoint({ ...c, trigger: [] }))

				mountEntryPoints
					.filter(
						({ forcedEvents = [] }) =>
							forcedEvents.indexOf(distinctEvent) > -1
					)
					.map((entry) =>
						loadSingleEntryPoint({
							...entry,
							...(entry.forcedEventsElsSkip
								? {}
								: {
										els: ['body'],
								  }),
							condition: () => true,
							trigger: [],
						})
					)
			})
		})
	}

	const loadInitialEntryPoints = () => {
		mountEntryPoints
			.filter(({ onLoad = true }) => {
				if ({}.toString.call(onLoad) === '[object Function]') {
					return onLoad()
				}

				return !!onLoad
			})
			.map(loadSingleEntryPoint)
	}

	if (immediate) {
		loadInitialEntryPoints()
	} else {
		onDocumentLoaded(loadInitialEntryPoints)
	}
}

var loadCSS = function (href, before, media, attributes) {
	var doc = document
	var ss = doc.createElement('link')
	var ref

	if (before) {
		ref = before
	} else {
		var refs = (doc.body || doc.getElementsByTagName('head')[0]).childNodes
		ref = refs[refs.length - 1]
	}

	var sheets = doc.styleSheets
	ss.rel = 'stylesheet'
	ss.href = href
	// ss.media = 'only x'

	// ref.parentNode.insertBefore(ss, before ? ref : ref.nextSibling)
	document.body.appendChild(ss)

	var onloadcssdefined = function (cb) {
		var resolvedHref = ss.href
		var i = sheets.length
		while (i--) {
			if (sheets[i].href === resolvedHref) {
				return cb()
			}
		}
		setTimeout(function () {
			onloadcssdefined(cb)
		})
	}

	function loadCB() {
		if (ss.addEventListener) {
			ss.removeEventListener('load', loadCB)
		}
		// ss.media = media || 'all'
	}

	if (ss.addEventListener) {
		ss.addEventListener('load', loadCB)
	}
	ss.onloadcssdefined = onloadcssdefined
	onloadcssdefined(loadCB)
	return ss
}

function onloadCSS(ss, callback) {
	var called

	function newcb() {
		if (!called && callback) {
			called = true
			callback.call(ss)
		}
	}

	if (ss.addEventListener) {
		ss.addEventListener('load', newcb)
	}

	if (ss.attachEvent) {
		ss.attachEvent('onload', newcb)
	}

	if ('isApplicationInstalled' in navigator && 'onloadcssdefined' in ss) {
		ss.onloadcssdefined(newcb)
	}
}

export const loadStyle = (src, hasDisable = false) =>
	new Promise((resolve, reject) => {
		if (document.querySelector(`[href="${src}"]`)) {
			resolve()
			return
		}

		requestAnimationFrame(() => {
			const ss = loadCSS(src)

			onloadCSS(ss, () => {
				requestAnimationFrame(() => {
					resolve()
				})
			})
		})
	})
