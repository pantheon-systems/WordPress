import { setTransparencyFor } from '../sticky'

export const computeFadeSlide = ({
	stickyContainer,
	isSticky,
	startPosition,
	stickyComponents,
}) => {
	if (isSticky) {
		if (stickyContainer.dataset.sticky.indexOf('yes') === -1) {
			stickyContainer.dataset.sticky = [
				'yes-start',
				...stickyComponents,
			].join(':')

			setTimeout(() => {
				stickyContainer.dataset.sticky =
					stickyContainer.dataset.sticky.replace(
						'yes-start',
						'yes-end'
					)

				setTimeout(() => {
					stickyContainer.dataset.sticky =
						stickyContainer.dataset.sticky.replace('yes-end', 'yes')
				}, 200)
			}, 1)
		}

		setTransparencyFor(stickyContainer, 'no')
	} else {
		if (
			stickyContainer.dataset.sticky.indexOf('yes-hide') === -1 &&
			stickyContainer.dataset.sticky.indexOf('yes:') > -1
		) {
			if (Math.abs(window.scrollY - startPosition) > 10) {
				stickyContainer.dataset.sticky = stickyComponents.join(':')

				setTimeout(() => {
					Array.from(
						stickyContainer.querySelectorAll('[data-row]')
					).map((row) => row.removeAttribute('style'))
				}, 300)

				setTransparencyFor(stickyContainer, 'yes')
			} else {
				stickyContainer.dataset.sticky = [
					'yes-hide-start',
					...stickyComponents,
				].join(':')

				requestAnimationFrame(() => {
					stickyContainer.dataset.sticky =
						stickyContainer.dataset.sticky.replace(
							'yes-hide-start',
							'yes-hide-end'
						)

					setTimeout(() => {
						stickyContainer.dataset.sticky =
							stickyComponents.join(':')

						setTimeout(() => {
							Array.from(
								stickyContainer.querySelectorAll('[data-row]')
							).map((row) => row.removeAttribute('style'))
						}, 300)

						setTransparencyFor(stickyContainer, 'yes')
					}, 200)
				})
			}
		}
	}
}
