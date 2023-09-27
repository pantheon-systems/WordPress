export const clamp = (min, max, value) => Math.max(min, Math.min(max, value))

export const computeLinearScale = (domain, range, value) =>
	range[0] +
	((range[1] - range[0]) / (domain[1] - domain[0])) * (value - domain[0])

export const getRowInitialMinHeight = (el) => {
	const elComp = getComputedStyle(el)
	let containerStyles = getComputedStyle(el.firstElementChild)

	let borderHeight =
		parseFloat(elComp.borderTopWidth) +
		parseFloat(elComp.borderBottomWidth) +
		parseFloat(containerStyles.borderTopWidth) +
		parseFloat(containerStyles.borderBottomWidth)

	let rowHeight = parseFloat(elComp.getPropertyValue('--height'))

	if (el.querySelector('[data-items] > [data-id="logo"]')) {
		const logoComp = getComputedStyle(
			el.querySelector('[data-items] > [data-id="logo"]')
		)

		let logoHeight = parseFloat(logoComp.height)

		let marginHeight =
			parseFloat(logoComp.marginTop) + parseFloat(logoComp.marginBottom)

		logoHeight = logoHeight + marginHeight

		if (el.querySelector('.site-logo-container')) {
			const logoImgComp = getComputedStyle(
				el.querySelector('.site-logo-container')
			)

			let maybeShrink = parseFloat(
				logoImgComp.getPropertyValue('--logo-shrink-height') || 0
			)

			if (maybeShrink > 0) {
				logoHeight =
					logoHeight -
					maybeShrink +
					parseFloat(
						logoImgComp.getPropertyValue('--logo-max-height') || 50
					)
			}
		}

		if (logoHeight > rowHeight) {
			rowHeight = logoHeight
		}
	}

	return rowHeight + borderHeight
}

export const getRowInitialHeight = (el) => {
	if (el.blcInitialHeight) {
		return el.blcInitialHeight
	}

	let elToCheck = el.firstElementChild

	if (el.firstElementChild.firstElementChild) {
		elToCheck = el.firstElementChild.firstElementChild
	}

	let initialHeight = elToCheck.getBoundingClientRect().height

	el.blcInitialHeight = initialHeight

	return initialHeight
}

export const getRowStickyHeight = (el, hasBorder = true) => {
	if (el.blcStickyHeight) {
		return el.blcStickyHeight
	}

	let rowStickyHeight = getRowInitialHeight(el)

	let styles = getComputedStyle(el)
	let containerStyles = getComputedStyle(el.firstElementChild)

	if (el.closest('[data-sticky*="yes"]')) {
		let borderHeight =
			parseFloat(styles.borderTopWidth) +
			parseFloat(styles.borderBottomWidth) +
			parseFloat(containerStyles.borderTopWidth) +
			parseFloat(containerStyles.borderBottomWidth)

		if (!hasBorder) {
			borderHeight = 0
		}

		let stickyHeight = el.getBoundingClientRect().height - borderHeight

		if (
			stickyHeight !== rowStickyHeight ||
			// case when content is forcing the initial height to be bigger
			rowStickyHeight > getRowInitialMinHeight(el)
		) {
			el.blcStickyHeight = el.getBoundingClientRect().height
			return stickyHeight
		}
	}

	let maybeShrink = 100

	if (el.dataset.row.includes('middle')) {
		maybeShrink = styles.getPropertyValue('--sticky-shrink')
	}

	let finalInitialHeight = 0

	// if (el.querySelector('.site-logo-container')) {
	// 	let computedLogo = getComputedStyle(
	// 		el.querySelector('.site-logo-container')
	// 	)

	// 	let logoInitialHeight = parseFloat(
	// 		computedLogo.getPropertyValue('--logo-max-height') || '50px'
	// 	)

	// 	let logoStickyShrink = parseFloat(
	// 		computedLogo.getPropertyValue('--logo-sticky-shrink') || '1'
	// 	)

	// 	if (logoStickyShrink < 1) {
	// 		let rowInitialMinHeight = getRowInitialMinHeight(el)

	// 		if (maybeShrink) {
	// 			rowInitialMinHeight *= parseFloat(maybeShrink) / 100
	// 		}

	// 		let logoStickyHeight = logoInitialHeight * logoStickyShrink

	// 		let finalInitialHeight =
	// 			rowStickyHeight - logoInitialHeight + logoStickyHeight

	// 		return Math.max(rowInitialMinHeight, finalInitialHeight)
	// 	}
	// }

	if (maybeShrink) {
		rowStickyHeight *= parseFloat(maybeShrink) / 100
	}

	return rowStickyHeight
}

export const maybeSetStickyHeightAnimated = (cb = () => 0) => {
	const maybeFloatingCart = document.querySelector('.ct-floating-bar')

	if (!maybeFloatingCart) {
		return
	}

	maybeFloatingCart.style.setProperty('--header-sticky-height-animated', cb())
}
