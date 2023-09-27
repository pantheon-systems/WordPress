import { getCurrentScreen } from './helpers/current-screen'

let intersectionObserver = null

const setStickyValue = (target, value) => {
	target.dataset.sticky = value

	if (value) {
		target.style.width = `${Math.round(
			target.parentNode.getBoundingClientRect().width
		)}px`
	} else {
		target.removeAttribute('style')
	}
}

const handleTopSentinel = ({ boundingClientRect, target, isIntersecting }) => {
	const bottomSentinel = target.parentNode
		.querySelector('[data-sentinel="bottom"]')
		.getBoundingClientRect()

	if (bottomSentinel.top < boundingClientRect.top) {
		return
	}

	if (getCurrentScreen() !== 'desktop') {
		target.nextElementSibling.dataset.sticky = ''
		return
	}

	setStickyValue(
		target.nextElementSibling,
		// (isIntersecting && boundingClientRect.top < 0) ||
		// (!isIntersecting && boundingClientRect.top > 0)
		!isIntersecting ? 'top' : ''
	)
}

const handleBottomSentinel = ({
	boundingClientRect,
	target,
	isIntersecting,
}) => {
	const topSentinel = target.parentNode
		.querySelector('[data-sentinel="top"]')
		.getBoundingClientRect()

	if (Math.abs(topSentinel.top) < Math.abs(boundingClientRect.top)) {
		return
	}

	if (getCurrentScreen() !== 'desktop') {
		target.previousElementSibling.dataset.sticky = ''
		return
	}

	setStickyValue(
		target.previousElementSibling,

		(boundingClientRect.top < 0 && isIntersecting) ||
			(!isIntersecting && boundingClientRect.top < 0)
			? 'bottom'
			: 'top'

		// (isIntersecting && boundingClientRect.top < 0) ||
		// (!isIntersecting && boundingClientRect.top > 0)
		// !isIntersecting ? 'top' : ''
	)
}

export const mount = (el) => {
	if (!window.IntersectionObserver) {
		return
	}

	if (!intersectionObserver) {
		intersectionObserver = new IntersectionObserver((entries) => {
			entries.map((observeDescriptor) => {
				if (observeDescriptor.target.dataset.sentinel === 'top') {
					handleTopSentinel(observeDescriptor)
				}

				if (observeDescriptor.target.dataset.sentinel === 'bottom') {
					handleBottomSentinel(observeDescriptor)
				}
			})
		})
	}

	if (el.hasIoListener) {
		return
	}

	if (
		el.getBoundingClientRect().height >=
		el.parentNode.getBoundingClientRect().height
	) {
		return
	}

	el.hasIoListener = true

	el.parentNode.insertAdjacentHTML(
		'afterbegin',
		`<div data-sentinel="top"></div>`
	)

	el.parentNode.insertAdjacentHTML(
		'beforeend',
		`<div data-sentinel="bottom"></div>`
	)

	el.parentNode.lastElementChild.style.setProperty(
		'--sidebar-height',
		`${el.getBoundingClientRect().height}px`
	)

	intersectionObserver.observe(el.parentNode.firstElementChild)
	intersectionObserver.observe(el.parentNode.lastElementChild)
}
