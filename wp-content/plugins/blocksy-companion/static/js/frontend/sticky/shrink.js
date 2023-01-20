import { setTransparencyFor } from '../sticky'

import { shrinkHandleLogo } from './shrink-handle-logo'
import { shrinkHandleMiddleRow } from './shrink-handle-middle-row'

export const computeShrink = ({
	containerInitialHeight,
	stickyContainer,
	stickyContainerHeight,
	isSticky,
	startPosition,
	stickyComponents,
}) => {
	if (startPosition === 0 && window.scrollY === 0) {
		stickyContainer.dataset.sticky = ['fixed', ...stickyComponents].join(
			':'
		)
	}

	if (isSticky) {
		if (stickyComponents.indexOf('yes') > -1) {
			return
		}

		if (stickyContainer.dataset.sticky.indexOf('yes') === -1) {
			setTransparencyFor(stickyContainer, 'no')

			stickyContainer.dataset.sticky = ['yes', ...stickyComponents].join(
				':'
			)
		}

		shrinkHandleLogo({ stickyContainer, startPosition })
		shrinkHandleMiddleRow({
			stickyContainer,
			containerInitialHeight,
			startPosition,
		})
	} else {
		Array.from(stickyContainer.querySelectorAll('[data-row]')).map((row) =>
			row.removeAttribute('style')
		)

		Array.from(
			stickyContainer.querySelectorAll(
				'[data-row*="middle"] .site-logo-container'
			)
		).map((el) => el.removeAttribute('style'))

		setTransparencyFor(stickyContainer, 'yes')

		if (startPosition === 0 && window.scrollY <= 0) {
			stickyContainer.dataset.sticky = [
				'fixed',
				...stickyComponents,
			].join(':')
		} else {
			stickyContainer.dataset.sticky = stickyComponents.join(':')
		}
	}
}
