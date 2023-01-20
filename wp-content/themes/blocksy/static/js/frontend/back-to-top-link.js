import ctEvents from 'ct-events'
import { areWeDealingWithSafari } from '../main'

export const mount = (backTop) => {
	if (backTop.hasListener) {
		return
	}

	backTop.hasListener = true

	// browser window scroll (in pixels) after which the "back to top" link is shown
	// browser window scroll (in pixels) after which the "back to top" link opacity is reduced
	var scrolling = false

	const compute = () => {
		var backTop = document.querySelector('.ct-back-to-top')

		if (!backTop) return

		window.scrollY > 500
			? backTop.classList.add('ct-show')
			: backTop.classList.remove('ct-show')
	}

	compute()

	ctEvents.on('ct:scroll:render-frame', () => {
		compute()
	})

	backTop.addEventListener('click', (event) => {
		event.preventDefault()

		var start = window.scrollY
		var currentTime = null

		const animateScroll = (timestamp) => {
			if (!currentTime) currentTime = timestamp
			var progress = timestamp - currentTime

			const easeInOutQuad = (t, b, c, d) => {
				t /= d / 2
				if (t < 1) return (c / 2) * t * t + b
				t--
				return (-c / 2) * (t * (t - 2) - 1) + b
			}

			var val = Math.max(easeInOutQuad(progress, start, -start, 700), 0)

			scrollTo(0, val)

			if (progress < 700) {
				requestAnimationFrame(animateScroll)
			}
		}

		if (areWeDealingWithSafari) {
			requestAnimationFrame(animateScroll)
		} else {
			scrollTo(0, 0)
		}
	})
}
