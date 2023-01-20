import $script from 'scriptjs'
import { fastOverlayHandleClick } from './frontend/fast-overlay'

let loadedChunks = {}
let intersectionObserver = null

const loadChunkWithPayload = (chunk, payload = {}, el = null) => {
	const immediateMount = () => {
		if (el) {
			loadedChunks[chunk.id].mount(el, payload)
		} else {
			;[...document.querySelectorAll(chunk.selector)].map((el) => {
				loadedChunks[chunk.id].mount(el, payload)
			})
		}
	}

	if (loadedChunks[chunk.id]) {
		immediateMount()
	} else {
		if (chunk.global_data) {
			chunk.global_data.map((data) => {
				if (!data.var || !data.data) {
					return
				}

				window[data.var] = data.data
			})
		}

		if (chunk.deps) {
			const depsThatAreNotLoadedIds = chunk.deps.filter(
				(id) =>
					!document.querySelector(
						`script[src*="${chunk.deps_data[id]}"]`
					)
			)
			const depsThatAreNotLoaded = depsThatAreNotLoadedIds.map(
				(id) => chunk.deps_data[id]
			)

			;[...depsThatAreNotLoadedIds, 'root']
				.map((x) => () => {
					return new Promise((resolve) => {
						if (x === 'root') {
							$script([chunk.url], () => {
								resolve()
								immediateMount()
							})
							return
						}

						$script([chunk.deps_data[x]], () => {
							resolve()
						})
					})
				})
				.reduce(
					(before, after) => before.then((_) => after()),
					Promise.resolve()
				)
		} else {
			$script(chunk.url, immediateMount)
		}
	}
}

const addChunkToIntersectionObserver = (chunk) => {
	if (!window.IntersectionObserver) {
		return
	}

	if (!intersectionObserver) {
		intersectionObserver = new IntersectionObserver((entries) => {
			entries.map(({ boundingClientRect, target, isIntersecting }) => {
				const chunk = target.__chunk__

				if (!isIntersecting && boundingClientRect.y > 0) {
					return
				}

				let state = `target-before-bottom`

				if (!isIntersecting && boundingClientRect.y < 0) {
					state = 'target-after-bottom'
				}

				if (
					state === 'target-before-bottom' &&
					!loadedChunks[chunk.id]
				) {
					return
				}

				loadChunkWithPayload(chunk, { state, target }, chunk.el)
			})
		})
	}

	;[...document.querySelectorAll(chunk.selector)].map((el) => {
		if (el.ioObserving) {
			return
		}

		el.ioObserving = true

		const target = document.querySelector(chunk.target)

		if (!target) {
			return
		}

		target.__chunk__ = { ...chunk, el }

		intersectionObserver.observe(target)
	})
}

export const mountDynamicChunks = () => {
	const requestIdleCallback =
		window.requestIdleCallback ||
		function (cb) {
			var start = Date.now()
			return setTimeout(function () {
				cb({
					didTimeout: false,
					timeRemaining: function () {
						return Math.max(0, 50 - (Date.now() - start))
					},
				})
			}, 1)
		}

	ct_localizations.dynamic_js_chunks.map((chunk) => {
		if (!chunk.id) {
			return
		}

		if (!document.querySelector(chunk.selector)) {
			return
		}

		if (chunk.trigger) {
			if (chunk.trigger === 'click') {
				;[...document.querySelectorAll(chunk.selector)].map((el) => {
					if (el.hasLazyLoadClickListener) {
						return
					}

					el.hasLazyLoadClickListener = true

					const cb = (event) => {
						if (
							chunk.ignore_click &&
							event.target.matches(chunk.ignore_click)
						) {
							return
						}

						event.preventDefault()

						if (
							el.closest('.ct-panel.active') &&
							el.matches(
								'.ct-header-account[href*="account-modal"]'
							)
						) {
							return
						}

						if (chunk.has_modal_loader) {
							const actuallyLoadChunk = () => {
								let hasLoader = true

								if (
									chunk.has_modal_loader &&
									chunk.has_modal_loader
										.skip_if_no_template &&
									!document.querySelector(
										`#${chunk.has_modal_loader.id}`
									) &&
									!loadedChunks[chunk.id]
								) {
									hasLoader = false
								}

								if (hasLoader) {
									const loadingHtml = `
                                <div data-behaviour="modal" class="ct-panel ${
									chunk.has_modal_loader.class
										? chunk.has_modal_loader.class
										: ''
								}" ${
										chunk.has_modal_loader.id
											? `id="${chunk.has_modal_loader.id}"`
											: ''
									}>
                                    <span data-loader="circles">
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </span>
                                </div>
                            `

									const div = document.createElement('div')

									div.innerHTML = loadingHtml

									let divRef = div.firstElementChild

									document
										.querySelector('.ct-drawer-canvas')
										.appendChild(div.firstElementChild)

									fastOverlayHandleClick(event, {
										openStrategy: 'fast',
										container: divRef,
									})
								}

								loadChunkWithPayload(chunk, { event }, el)
							}

							if (document.body.dataset.panel) {
								let currentPanel =
									document.querySelector('.ct-panel.active')

								if (currentPanel) {
									let maybeButton =
										document.querySelector(
											`[data-toggle-panel="#${currentPanel.id}"]`
										) ||
										document.querySelector(
											`[href="#${currentPanel.id}"]`
										)

									if (maybeButton) {
										maybeButton.click()

										setTimeout(() => {
											actuallyLoadChunk()
										}, 500)

										return
									}
								}
							} else {
								actuallyLoadChunk()
							}
						} else {
							loadChunkWithPayload(chunk, { event }, el)
						}
					}

					el.dynamicJsChunkStop = () => {
						el.removeEventListener('click', cb)
					}

					el.addEventListener('click', cb)
				})
			}

			if (chunk.trigger === 'submit') {
				;[...document.querySelectorAll(chunk.selector)].map((el) => {
					if (el.hasLazyLoadSubmitListener) {
						return
					}

					el.hasLazyLoadSubmitListener = true

					el.addEventListener('submit', (event) => {
						event.preventDefault()
						loadChunkWithPayload(chunk, { event }, el)
					})
				})
			}

			if (chunk.trigger === 'hover') {
				;[...document.querySelectorAll(chunk.selector)].map((el) => {
					if (el.hasLazyLoadHoverListener) {
						return
					}

					el.hasLazyLoadHoverListener = true

					el.addEventListener('mouseover', (event) => {
						event.preventDefault()
						loadChunkWithPayload(chunk, { event }, el)
					})
				})
			}

			if (chunk.trigger === 'intersection-observer') {
				addChunkToIntersectionObserver(chunk)
			}

			if (chunk.trigger === 'scroll') {
				setTimeout(() => {
					let prevScroll = scrollY

					let cb = (e) => {
						if (Math.abs(scrollY - prevScroll) > 30) {
							document.removeEventListener('scroll', cb)
							loadChunkWithPayload(chunk)
							return
						}
					}

					document.addEventListener('scroll', cb, { passive: true })
				}, 500)
			}
		} else {
			loadChunkWithPayload(chunk)
		}
	})
}

export const registerDynamicChunk = (id, implementation) => {
	if (loadedChunks[id]) {
		return
	}

	loadedChunks[id] = implementation
}
