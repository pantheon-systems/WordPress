import {
	getRowInitialHeight,
	getRowStickyHeight,
	computeLinearScale,
	clamp,
} from './shrink-utils'

let logoShrinkCache = null

export const clearLogoShrinkCache = () => {
	logoShrinkCache = null
}

const getLogoShrinkData = ({ logo, row }) => {
	if (logoShrinkCache) {
		return logoShrinkCache
	}

	let initialHeight = parseFloat(
		getComputedStyle(logo).getPropertyValue('--logo-max-height') || 50
	)

	const stickyShrink = parseFloat(
		getComputedStyle(logo)
			.getPropertyValue('--logo-sticky-shrink')
			.toString()
			.replace(',', '.') || 1
	)

	let rowInitialHeight = getRowInitialHeight(row)
	let rowStickyHeight = getRowStickyHeight(row)

	logoShrinkCache = {
		initialHeight,
		stickyShrink,
		rowInitialHeight,
		rowStickyHeight,
	}

	return logoShrinkCache
}

export const shrinkHandleLogo = ({ stickyContainer, startPosition }) => {
	;[...stickyContainer.querySelectorAll('[data-row*="middle"]')].map(
		(row) => {
			if (!row.querySelector('[data-id="logo"] .site-logo-container')) {
				return
			}

			const logo = row.querySelector(
				'[data-id="logo"] .site-logo-container'
			)

			let {
				initialHeight,
				stickyShrink,
				rowInitialHeight,
				rowStickyHeight,
			} = getLogoShrinkData({ logo, row })

			const stickyHeight = initialHeight * stickyShrink

			if (stickyShrink === 1) {
				return
			}

			logo.style.setProperty(
				'--logo-shrink-height',
				`${
					computeLinearScale(
						[
							startPosition,
							startPosition +
								Math.abs(
									rowInitialHeight === rowStickyHeight
										? initialHeight - stickyHeight
										: rowInitialHeight - rowStickyHeight
								),
						],
						[1, stickyShrink],
						clamp(
							startPosition,
							startPosition +
								Math.abs(
									rowInitialHeight === rowStickyHeight
										? initialHeight - stickyHeight
										: rowInitialHeight - rowStickyHeight
								),

							scrollY
						)
					) * initialHeight
				}px`
			)
		}
	)
}
