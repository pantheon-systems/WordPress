import { isTouchDevice } from '../helpers/is-touch-device'

const isRtl = () => document.querySelector('html').dir === 'rtl'

const isEligibleForSubmenu = (el) =>
	el.classList.contains('animated-submenu') &&
	(!el.parentNode.classList.contains('menu') ||
		(el.className.indexOf('ct-mega-menu') === -1 &&
			el.parentNode.classList.contains('menu')))

const getAllParents = (a) => {
	var els = []

	while (a) {
		els.unshift(a)
		a = a.parentNode
	}

	return els
}

function furthest(el, s) {
	var nodes = []

	while (el.parentNode) {
		if (
			el.parentNode &&
			el.parentNode.matches &&
			el.parentNode.matches(s)
		) {
			nodes.push(el.parentNode)
		}

		el = el.parentNode
	}

	return nodes[nodes.length - 1]
}

const isIosDevice =
	typeof window !== 'undefined' &&
	window.navigator &&
	window.navigator.platform &&
	(/iP(ad|hone|od)/.test(window.navigator.platform) ||
		(window.navigator.platform === 'MacIntel' &&
			window.navigator.maxTouchPoints > 1))

const getPreferedPlacementFor = (el) => {
	const farmost = furthest(el, 'li.menu-item')

	if (!farmost) {
		return isRtl() ? 'left' : 'right'
	}

	if (!farmost.querySelector('.sub-menu .sub-menu .sub-menu')) {
		return isRtl() ? 'left' : 'right'
	}

	return farmost.getBoundingClientRect().left > innerWidth / 2
		? 'left'
		: 'right'
}

const computeItemSubmenuFor = (
	reference,
	{
		// left -- 1st level menu items
		// end  -- submenus
		startPosition = 'end',
	}
) => {
	const menu = reference.querySelector('.sub-menu')
	const placement = getPreferedPlacementFor(menu)

	const { left, width, right } = menu.getBoundingClientRect()

	let futurePlacement = placement
	let referenceRect = reference.getBoundingClientRect()

	if (placement === 'left') {
		let referencePoint =
			startPosition === 'end' ? referenceRect.left : referenceRect.right

		if (referencePoint - width < 0) {
			futurePlacement = 'right'
		}
	}

	if (placement === 'right') {
		let referencePoint =
			startPosition === 'end' ? referenceRect.right : referenceRect.left

		if (referencePoint + width > innerWidth) {
			futurePlacement = 'left'
		}
	}

	reference.dataset.submenu = futurePlacement

	reference.addEventListener('click', () => {})
}

const openSubmenu = (e) => {
	const li = e.target.closest('li')

	li.classList.add('ct-active')

	let childIndicator = [...li.children].find((el) =>
		el.matches('.ct-toggle-dropdown-desktop-ghost')
	)

	if (!childIndicator) {
		childIndicator = li.firstElementChild
	}

	if (childIndicator) {
		childIndicator.setAttribute('aria-expanded', 'true')
		if (childIndicator.tagName.toLowerCase() === 'button') {
			childIndicator.setAttribute(
				'aria-label',
				ct_localizations.collapse_submenu
			)
		}
	}

	mouseenterHandler({ target: li })
}

const closeSubmenu = (e) => {
	if (!e.target) {
		return
	}

	const li = e.target.closest('li')
	li.classList.remove('ct-active')

	let childIndicator = [...li.children].find((el) =>
		el.matches('.ct-toggle-dropdown-desktop-ghost')
	)

	if (!childIndicator) {
		childIndicator = li.firstElementChild
	}

	if (childIndicator) {
		childIndicator.setAttribute('aria-expanded', 'false')

		if (childIndicator.tagName.toLowerCase() === 'button') {
			childIndicator.setAttribute(
				'aria-label',
				ct_localizations.expand_submenu
			)
		}
	}

	setTimeout(() => {
		;[...li.querySelectorAll('[data-submenu]')].map((el) => {
			el.removeAttribute('data-submenu')
		})
		;[...li.querySelectorAll('.ct-active')].map((el) => {
			el.classList.remove('ct-active')
		})
	}, 30)
}

export const mountMenuLevel = (menuLevel, args = {}) => {
	;[...menuLevel.children]
		.filter((el) =>
			el.matches('.menu-item-has-children, .page_item_has_children')
		)
		.map((el) => {
			if (el.classList.contains('ct-mega-menu-custom-width')) {
				const menu = el.querySelector('.sub-menu')
				const elRect = el.getBoundingClientRect()
				const menuRect = menu.getBoundingClientRect()

				if (
					elRect.left + elRect.width / 2 + menuRect.width / 2 >
					innerWidth
				) {
					el.dataset.submenu = 'left'
				}

				if (elRect.left + elRect.width / 2 - menuRect.width / 2 < 0) {
					el.dataset.submenu = 'right'
				}
			}

			if (isEligibleForSubmenu(el)) {
				computeItemSubmenuFor(el, args)
			}

			let childIndicator = [...el.children].find((el) =>
				el.matches('.ct-toggle-dropdown-desktop-ghost')
			)

			let hasClickInteraction = el.matches(
				'[data-interaction*="click"] *'
			)

			if (!el.hasFocusEventListener) {
				el.hasFocusEventListener = true

				el.addEventListener('keydown', function (e) {
					if (e.keyCode == 27) {
						closeSubmenu({
							target: el.firstElementChild,
						})
					}
				})

				el.addEventListener('focusout', (evt) => {
					if (el.contains(evt.relatedTarget)) {
						return
					}

					closeSubmenu({
						target: el.firstElementChild,
					})
				})
			}

			if (!hasClickInteraction) {
				el.addEventListener('mouseenter', (e) => {
					// So that mouseenter event is catched before the open itself
					if (isIosDevice) {
						openSubmenu({ target: el.firstElementChild })
					} else {
						requestAnimationFrame(() => {
							openSubmenu({ target: el.firstElementChild })
						})
					}

					// If first level
					if (!el.parentNode.classList.contains('.sub-menu')) {
						;[...el.parentNode.children]
							.filter((firstLevelEl) => firstLevelEl !== el)
							.map((firstLevelEl) => {
								closeSubmenu({
									target: firstLevelEl.firstElementChild,
								})
							})
					}

					e.target.closest('li').addEventListener(
						'mouseleave',
						() => {
							closeSubmenu({ target: el.firstElementChild })
						},
						{ once: true }
					)
				})

				// On Android devices, allow only 2nd click to open the link.
				// First click will ensure the submenu is opened
				//
				// iOS has this behaviour out of the box.
				//
				// Important: only perform this for touch devices so that keyboard
				// users are not affected.
				if (isTouchDevice()) {
					el.addEventListener('click', (e) => {
						if (!el.classList.contains('ct-active')) {
							e.preventDefault()
						}
					})
				}
			}

			if (hasClickInteraction) {
				let itemTarget = el.matches('[data-interaction*="item"] *')
					? el.firstElementChild
					: el.firstElementChild.querySelector(
							'.ct-toggle-dropdown-desktop'
					  )

				if (!itemTarget.hasEventListener) {
					itemTarget.hasEventListener = true
					itemTarget.addEventListener('click', (e) => {
						e.preventDefault()

						if (
							e.target
								.closest('li')
								.classList.contains('ct-active')
						) {
							closeSubmenu(e)
						} else {
							openSubmenu(e)

							if (isIosDevice) {
								e.target.closest('li').addEventListener(
									'mouseleave',
									() => {
										closeSubmenu({
											target: el.firstElementChild,
										})
									},
									{ once: true }
								)
							}

							if (!e.target.hasDocumentListener) {
								e.target.hasDocumentListener = true
								// Add the event a bit later
								setTimeout(() => {
									document.addEventListener(
										'click',
										(evt) => {
											if (
												!e.target
													.closest('li')
													.contains(evt.target)
											) {
												closeSubmenu(e)
											}
										}
									)
								})
							}
						}
					})
				}
			}

			if (childIndicator && !childIndicator.hasEventListener) {
				childIndicator.hasEventListener = true

				childIndicator.addEventListener('click', (e) => {
					if (
						e.target.closest('li').classList.contains('ct-active')
					) {
						closeSubmenu(e)
					} else {
						openSubmenu(e)
					}
				})
			}
		})
}

const mouseenterHandler = ({ target }) => {
	if (!target.matches('.menu-item-has-children, .page_item_has_children')) {
		target = target.closest(
			'.menu-item-has-children, .page_item_has_children'
		)
	}

	if (
		target.parentNode.classList.contains('menu') &&
		target.className.indexOf('ct-mega-menu') > -1 &&
		target.className.indexOf('ct-mega-menu-custom-width') === -1 &&
		window.wp &&
		wp &&
		wp.customize &&
		wp.customize('active_theme')
	) {
		const menu = target.querySelector('.sub-menu')

		menu.style.left = `${
			Math.round(
				target
					.closest('[class*="ct-container"]')
					.firstElementChild.getBoundingClientRect().x
			) - Math.round(target.closest('nav').getBoundingClientRect().x)
		}px`
	}

	if (!isEligibleForSubmenu(target)) {
		return
	}

	const menu = target.querySelector('.sub-menu')

	mountMenuLevel(menu)

	if (menu.closest('[data-interaction="hover"]')) {
		menu.parentNode.addEventListener(
			'mouseleave',
			() => {
				;[...menu.children]
					.filter((el) => isEligibleForSubmenu(el))
					.map((el) => el.removeAttribute('data-submenu'))
			},
			{ once: true }
		)
	}
}

export const handleUpdate = (menu) => {
	if (!menu.parentNode) {
		menu = document.querySelector(`[class="${menu.className}"]`)
	}

	if (!menu) {
		return
	}

	if (
		!menu.querySelector('.menu-item-has-children') &&
		!menu.querySelector('.page_item_has_children')
	) {
		return
	}

	if (menu.closest('[data-interaction="hover"]')) {
		menu.removeEventListener('mouseenter', mouseenterHandler)
		menu.addEventListener('mouseenter', mouseenterHandler)
	}
}
