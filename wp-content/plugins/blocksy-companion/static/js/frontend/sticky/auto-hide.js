import { setTransparencyFor } from '../sticky'
import {
	maybeSetStickyHeightAnimated,
	getRowStickyHeight,
	computeLinearScale,
	clamp,
} from './shrink-utils'

import { shrinkHandleLogo } from './shrink-handle-logo'
import { shrinkHandleMiddleRow } from './shrink-handle-middle-row'

const getData = ({ stickyContainer }) => {
	const stickyContainerHeight = [
		...stickyContainer.querySelectorAll('[data-row]'),
	].reduce((res, row) => res + getRowStickyHeight(row, false), 0)

	return {
		stickyContainerHeight,
		stickyContainerHeightAbsolute:
			stickyContainerHeight +
			parseFloat(getComputedStyle(stickyContainer).top),
	}
}

let prevOffset = null

export const computeAutoHide = (args) => {
	let {
		currentScrollY,
		stickyContainer,
		containerInitialHeight,
		headerInitialHeight,
		startPosition,
		isSticky,
		stickyComponents,
	} = args

	if (isSticky && currentScrollY - args.prevScrollY === 0) {
		maybeSetStickyHeightAnimated(() => {
			return '0px'
		})
	}

	if (isSticky) {
		if (
			stickyContainer.dataset.sticky.indexOf('yes') === -1 &&
			currentScrollY > headerInitialHeight * 2 + startPosition
		) {
			stickyContainer.dataset.sticky = ['yes', ...stickyComponents].join(
				':'
			)

			shrinkHandleLogo({ stickyContainer, startPosition })
			shrinkHandleMiddleRow({
				stickyContainer,
				containerInitialHeight,
				startPosition,
			})
			setTransparencyFor(stickyContainer, 'no')
			document.body.removeAttribute('style')
		}
	} else {
		Array.from(stickyContainer.querySelectorAll('[data-row]')).map((row) =>
			row.removeAttribute('style')
		)
		Array.from(
			stickyContainer.querySelectorAll(
				'[data-row*="middle"] .site-logo-container'
			)
		).map((el) => el.removeAttribute('style'))

		stickyContainer.dataset.sticky = [...stickyComponents].join(':')

		setTransparencyFor(stickyContainer, 'yes')

		maybeSetStickyHeightAnimated(() => {
			return '0px'
		})

		prevOffset = null
	}

	if (prevOffset === null) {
		prevOffset = 1000
	}

	var elTopOff = prevOffset + args.prevScrollY - currentScrollY

	let offset = 0

	if (
		currentScrollY > headerInitialHeight * 2 + startPosition ||
		stickyContainer.dataset.sticky.indexOf('yes') > -1
	) {
		if (currentScrollY <= startPosition) {
			offset = 0
		} else if (currentScrollY > args.prevScrollY) {
			let { stickyContainerHeightAbsolute } = getData({ stickyContainer })

			offset =
				Math.abs(elTopOff) > stickyContainerHeightAbsolute
					? -stickyContainerHeightAbsolute
					: elTopOff
		} else {
			offset = elTopOff > 0 ? 0 : elTopOff
		}

		stickyContainer.style.transform = `translateY(${offset}px)`

		prevOffset = offset
	} else {
		stickyContainer.removeAttribute('style')
	}

	if (stickyContainer.dataset.sticky.indexOf('yes') > -1) {
		if (currentScrollY <= startPosition) {
		} else if (currentScrollY > args.prevScrollY) {
		} else {
			shrinkHandleLogo({ stickyContainer, startPosition })
			shrinkHandleMiddleRow({
				stickyContainer,
				containerInitialHeight,
				startPosition,
			})
		}
	}

	maybeSetStickyHeightAnimated(() => {
		let { stickyContainerHeight } = getData({ stickyContainer })
		return `${stickyContainerHeight - Math.abs(offset)}px`
	})
}
