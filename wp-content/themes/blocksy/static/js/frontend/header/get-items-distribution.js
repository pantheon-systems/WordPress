import { getCacheFor } from './responsive-desktop-menu'

const getItemWidthsFrom = (container) =>
	[...container.querySelectorAll('[data-items] > [data-id]')]
		.filter((el) => el.dataset.id.indexOf('menu') === -1)
		.reduce((sum, el) => {
			let style = window.getComputedStyle(el)

			return (
				sum +
				el.getBoundingClientRect().width +
				parseInt(style.getPropertyValue('margin-left')) +
				parseInt(style.getPropertyValue('margin-right'))
			)
		}, 0)

const getTotalItemsWidthFor = (nav) => {
	let navStyle = window.getComputedStyle(nav)

	return (
		getCacheFor(nav.__id).itemsWidth.reduce((sum, n) => sum + n, 0) +
		(parseInt(navStyle.getPropertyValue('margin-left')) +
			parseInt(navStyle.getPropertyValue('margin-right')))
	)
}

/**
 * 1. Nav is in side with NO items in middle
 * 2. Nav is in middle
 * 3. Nav is either:
 *   a. Secondary
 *   b. Side, but with middle
 */
const computeAvailableSpaceFor = (nav) => {
	let baseContainer = nav.closest('[class*="ct-container"]')
	let baseWidth = baseContainer.getBoundingClientRect().width

	// side | middle | secondary
	// TODO: compute sides
	let closestColumn = nav.closest('[data-column]').dataset.column

	let navSide =
		closestColumn === 'start' || closestColumn === 'end'
			? 'side'
			: closestColumn === 'middle'
			? 'middle'
			: 'secondary'

	let hasMiddle = baseContainer.querySelector('[data-column="middle"]')

	// Case 1
	if (navSide === 'side' && !hasMiddle) {
		let allNavs = baseContainer.querySelectorAll('[data-id*="menu"]')

		const totalItemsWidthFromAllNavs = [...allNavs].reduce(
			(total, nav) => total + getTotalItemsWidthFor(nav),
			0
		)

		const totalItemsWidth = getTotalItemsWidthFor(nav)

		let containerWidth = baseWidth - getItemWidthsFrom(baseContainer)

		if (allNavs.length > 1) {
			containerWidth *=
				(100 * totalItemsWidth) / totalItemsWidthFromAllNavs / 100
		}

		return containerWidth
	}

	if (navSide === 'middle') {
		return (
			baseWidth -
			Math.max(
				baseContainer.querySelector('[data-column="start"]')
					? getItemWidthsFrom(
							baseContainer.querySelector('[data-column="start"]')
					  )
					: 0,
				baseContainer.querySelector('[data-column="end"]')
					? getItemWidthsFrom(
							baseContainer.querySelector('[data-column="end"]')
					  )
					: 0
			) *
				2
		)
	}

	return (
		(baseWidth -
			(baseContainer.querySelector('[data-column="middle"]')
				? getItemWidthsFrom(
						baseContainer.querySelector('[data-column="middle"]')
				  )
				: 0)) /
			2 -
		getItemWidthsFrom(nav.closest('[data-column]'))
	)
}

export const getItemsDistribution = (nav) => {
	let containerWidth = computeAvailableSpaceFor(nav)

	let baseContainer = nav.closest('[class*="ct-container"]')

	let navStyle = window.getComputedStyle(nav)

	const totalItemsWidth = getTotalItemsWidthFor(nav)

	const hasAnyOverlap = totalItemsWidth > containerWidth

	if (!hasAnyOverlap) {
		return {
			fit: getCacheFor(nav.__id).children,
			notFit: [],
		}
	}

	let allNavs = baseContainer.querySelectorAll('[data-id*="menu"]')

	return getCacheFor(nav.__id).children.reduce(
		({ fit, notFit }, currentEl, currentIndex) => ({
			...(getCacheFor(nav.__id)
				.itemsWidth.slice(0, currentIndex + 1)
				.reduce((sum, n) => sum + n, 0) <
			containerWidth -
				100 / allNavs.length -
				(parseInt(navStyle.getPropertyValue('margin-left')) +
					parseInt(navStyle.getPropertyValue('margin-right')))
				? {
						fit: [...fit, currentEl],
						notFit,
				  }
				: {
						notFit: [...notFit, currentEl],
						fit,
				  }),
		}),

		{
			fit: [],
			notFit: [],
		}
	)
}
