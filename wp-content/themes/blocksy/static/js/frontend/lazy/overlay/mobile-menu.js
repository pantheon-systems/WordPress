const activateSubMenu = (container) => {
	const subMenu = container.querySelector('ul')

	requestAnimationFrame(() => {
		const actualHeight = subMenu.getBoundingClientRect().height
		subMenu.style.height = '0px'
		subMenu.classList.add('is-animating')

		requestAnimationFrame(() => {
			subMenu.style.height = `${actualHeight}px`

			whenTransitionEnds(subMenu, () => {
				subMenu.classList.remove('is-animating')
				subMenu.removeAttribute('style')
			})
		})
	})
}

const deactivateSubMenu = (container, cb) => {
	const subMenu = container.querySelector('ul')

	requestAnimationFrame(() => {
		const actualHeight = subMenu.getBoundingClientRect().height

		subMenu.style.height = `${actualHeight}px`
		subMenu.classList.add('is-animating')

		requestAnimationFrame(() => {
			subMenu.style.height = '0px'

			whenTransitionEnds(subMenu, () => {
				subMenu.classList.remove('is-animating')
				subMenu.removeAttribute('style')
				cb()
			})
		})
	})
}

const handleContainer = (container) => {
	if (!container) {
		return
	}

	const arrow = container.querySelector('.ct-toggle-dropdown-mobile')

	if (container.classList.contains('dropdown-active')) {
		arrow.setAttribute('aria-expanded', 'false')
		arrow.setAttribute('aria-label', ct_localizations.expand_submenu)

		deactivateSubMenu(container, () => {
			container.classList.toggle('dropdown-active')
			;[
				...container.querySelectorAll(
					'.menu-item-has-children.dropdown-active, .page_item_has_children.dropdown-active'
				),
			].map((el) => el.classList.remove('dropdown-active'))
		})
	} else {
		arrow.setAttribute('aria-expanded', 'true')
		arrow.setAttribute('aria-label', ct_localizations.collapse_submenu)
		;[...container.parentNode.children].map(
			(el) =>
				el.classList.contains('dropdown-active') && handleContainer(el)
		)

		container.classList.toggle('dropdown-active')
		activateSubMenu(container)
	}
}

export const mount = (arrow) => {
	if (arrow.hasListener) {
		return
	}

	arrow.hasListener = true

	let parentHref = arrow.previousElementSibling.getAttribute('href')

	if (!parentHref || parentHref === '#') {
		arrow.previousElementSibling.addEventListener('click', (e) => {
			e.preventDefault()
			e.stopPropagation()

			handleContainer(
				arrow.closest(
					'.menu-item-has-children, .page_item_has_children'
				)
			)
		})
	}

	arrow.addEventListener('click', (e) => {
		e.preventDefault()
		e.stopPropagation()

		handleContainer(
			arrow.closest('.menu-item-has-children, .page_item_has_children')
		)
	})
}

function whenTransitionEnds(el, cb) {
	const end = () => {
		el.removeEventListener('transitionend', onEnd)
		cb()
	}

	const onEnd = (e) => {
		if (e.target === el) {
			end()
		}
	}

	el.addEventListener('transitionend', onEnd)
}
