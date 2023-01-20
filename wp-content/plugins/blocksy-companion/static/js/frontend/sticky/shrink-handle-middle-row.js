import {
	getRowInitialHeight,
	getRowStickyHeight,
	computeLinearScale,
	clamp,
} from './shrink-utils'

let shrinkCache = null

export const clearShrinkCache = () => {
	shrinkCache = null
}

const getShrinkData = ({ row }) => {
	if (shrinkCache) {
		return shrinkCache
	}

	let rowInitialHeight = getRowInitialHeight(row)
	let rowStickyHeight = getRowStickyHeight(row)

	shrinkCache = { rowInitialHeight, rowStickyHeight }

	return shrinkCache
}

export const shrinkHandleMiddleRow = ({
	stickyContainer,
	containerInitialHeight,
	startPosition,
}) => {
	if (!stickyContainer.querySelector('[data-row*="middle"]')) {
		return
	}

	;[stickyContainer.querySelector('[data-row*="middle"]')].map((row) => {
		let { rowInitialHeight, rowStickyHeight } = getShrinkData({ row })

		if (rowInitialHeight !== rowStickyHeight) {
			let shrinkHeight = rowStickyHeight

			if (true || stickyContainer.dataset.sticky.indexOf('auto-hide') === -1) {
				shrinkHeight = computeLinearScale(
					[
						startPosition,
						startPosition +
							Math.abs(rowInitialHeight - rowStickyHeight),
					],
					[rowInitialHeight, rowStickyHeight],
					clamp(
						startPosition,

						startPosition +
							Math.abs(rowInitialHeight - rowStickyHeight),

						scrollY
					)
				)
			}

			row.style.setProperty('--shrink-height', `${shrinkHeight}px`)
		}
	})
}
