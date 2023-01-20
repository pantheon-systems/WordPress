// ------------------------------------------
// Rellax.js - v1.0.0
// Buttery smooth parallax library
// Copyright (c) 2016 Moe Amaya (@moeamaya)
// MIT license
//
// Thanks to Paraxify.js and Jaime Cabllero
// for parallax concepts
// ------------------------------------------

import { getCurrentScreen } from '../helpers/current-screen'
import innerHeight from './ios-inner-height'

// Ahh a pure function, gets new transform value
// based on scrollPostion and speed
// Allow for decimal pixel values
const updatePosition = (percentage, speed) => speed * (100 * (1 - percentage))

// We want to cache the parallax blocks'
// values: base, top, height, speed
// el: is dom object, return: el cache values
const createBlock = ({
	el = null,
	speed = null,
	fitInsideContainer = null,
	isVisible = false,
	shouldSetHeightToIncrease = true,
	parallaxBehavior = 'desktop:tablet:mobile',
}) => {
	// Optional individual block speed as data attr, otherwise global speed
	// Check if has percentage attr, and limit speed to 5, else limit it to 10
	// The function is named clamp
	speed = speed <= -5 ? -5 : speed >= 5 ? 5 : speed

	// We need to guess the position the background will be, when the section
	// will reach the top of the viewport. This calculation will be based on the
	// speed for sure
	if (fitInsideContainer && shouldSetHeightToIncrease) {
		let heightWeWantToIncrease = 0

		if (speed > 0) {
			heightWeWantToIncrease = updatePosition(0.5, speed)
		} else {
			heightWeWantToIncrease =
				updatePosition(
					innerHeight() /
						(fitInsideContainer.clientHeight + innerHeight()),
					speed
				) - updatePosition(0.5, speed)
		}

		heightWeWantToIncrease = Math.abs(heightWeWantToIncrease) * 2

		el.parentNode.style.height = !isVisible
			? '100%'
			: `calc(100% + ${heightWeWantToIncrease}px)`
	}

	// initializing at scrollY = 0 (top of browser)
	// ensures elements are positioned based on HTML layout.

	let { top, height } = nullifyTransforms(
		fitInsideContainer ? fitInsideContainer : el
	)

	var blockTop = pageYOffset + top

	return {
		parallaxBehavior,
		shouldSetHeightToIncrease,
		fitInsideContainer,
		el,
		top: blockTop,
		height,
		speed,
		isVisible,
	}
}

function elementInViewport(el) {
	var rect = el.getBoundingClientRect()

	return (
		rect.bottom > -450 &&
		rect.top - 450 <
			(innerHeight() ||
				document.documentElement
					.clientHeight) /* or $(window).height() */
	)
}

function nullifyTransforms(el) {
	if (!el) return null

	//add sanity checks and default values

	let { top, left, right, width, height } = el.getBoundingClientRect()

	let transformArr = window
		.getComputedStyle(el)
		.transform.split(/\(|,|\)/)
		.slice(1, -1)
		.map((v) => parseFloat(v))

	if (transformArr.length != 6) {
		return el.getBoundingClientRect()
	}

	// 2D matrix
	// need some math to apply inverse of matrix
	// That is the matrix of the transformation of the element
	var t = transformArr
	let det = t[0] * t[3] - t[1] * t[2]

	/*if (transformArr.length > 6)*/
	//3D matrix
	//haven't done the calculation to apply inverse of 4x4 matrix

	return {
		width: width / t[0],
		height: height / t[3],
		left: (left * t[3] - top * t[2] + t[2] * t[5] - t[4] * t[3]) / det,
		right: (right * t[3] - top * t[2] + t[2] * t[5] - t[4] * t[3]) / det,
		top: (-left * t[1] + top * t[0] + t[4] * t[1] - t[0] * t[5]) / det,
	}
}

export class Rellax {
	constructor() {
		this.blocks = []
		this.oldPosY = false

		this.intersectionObserver = new IntersectionObserver(
			(entries) => {
				entries.map(
					({ target: el, isIntersecting, intersectionRatio }) => {
						let blocks = this.blocks.filter(
							({ fitInsideContainer, el: blockEl }) =>
								blockEl.closest('svg')
									? blockEl.closest('svg') === el
									: fitInsideContainer === el ||
									  blockEl === el
						)

						blocks.map((block) => {
							block.isVisible =
								isIntersecting &&
								block.parallaxBehavior.indexOf(
									getCurrentScreen({ withTablet: true })
								) > -1

							this.blocks = this.blocks.map((nestedBlock) =>
								nestedBlock.el === block.el
									? block
									: nestedBlock
							)

							if (!block.isVisible)
								block.el.removeAttribute('style')
						})
					}
				)
			},
			{
				rootMargin: '450px',
			}
		)

		window.addEventListener('resize', () => {
			this.oldPosY = false
			this.blocks = this.blocks.map((block) =>
				createBlock({
					...block,
					isVisible:
						elementInViewport(
							block.fitInsideContainer
								? block.fitInsideContainer
								: block.el
						) &&
						block.parallaxBehavior.indexOf(
							getCurrentScreen({ withTablet: true })
						) > -1,
				})
			)
			this.animate()
		})

		// Start the loop
		this.update()

		// The loop does nothing if the scrollPosition did not change
		// so call animate to make sure every element has their transforms
		this.animate()
	}

	removeEl({ el }) {
		el.removeAttribute('style')
		this.blocks = this.blocks.filter(({ el: e }) => e !== el)
	}

	addEl({
		el,
		speed,
		fitInsideContainer = null,
		shouldSetHeightToIncrease = true,
		parallaxBehavior = 'desktop:tablet:mobile',
	}) {
		if (fitInsideContainer) {
			this.intersectionObserver.observe(fitInsideContainer)
		} else {
			this.intersectionObserver.observe(
				el.closest('svg') ? el.closest('svg') : el
			)
		}

		this.blocks.push(
			createBlock({
				el,
				speed,
				fitInsideContainer,
				isVisible:
					elementInViewport(
						fitInsideContainer ? fitInsideContainer : el
					) &&
					parallaxBehavior.indexOf(
						getCurrentScreen({ withTablet: true })
					) > -1,

				shouldSetHeightToIncrease,
				parallaxBehavior,
			})
		)
	}

	update() {
		if (!this.oldPosY && this.oldPosY !== 0) {
			this.animate()
		}

		if (this.setPosition()) {
			this.animate()
		}

		requestAnimationFrame(this.update.bind(this))
	}

	setPosition() {
		if (this.blocks.length === 0) return false

		let old = this.oldPosY
		this.oldPosY = pageYOffset

		return old != pageYOffset
	}

	animate() {
		this.blocks.map((block) => {
			if (!block.isVisible) {
				block.el.removeAttribute('style')
				return
			}

			var percentage =
				(pageYOffset - block.top + innerHeight()) /
				(block.height + innerHeight())

			let { top, height } = nullifyTransforms(
				block.fitInsideContainer ? block.fitInsideContainer : block.el
			)

			if (!height) {
				height = (block.fitInsideContainer
					? block.fitInsideContainer
					: block.el
				).getBoundingClientRect().height
			}

			const newPercentage =
				1 -
				(top +
					(block.el.dataset.percentage &&
					parseInt(block.el.dataset.percentage, 10) === 0
						? 0
						: height / 2)) /
					innerHeight()

			// Subtracting initialize value, so element stays in same spot as HTML
			var position =
				updatePosition(
					block.fitInsideContainer ? percentage : newPercentage,
					block.speed
				) -
				updatePosition(
					block.el.dataset.percentage
						? parseInt(block.el.dataset.percentage, 10)
						: 0.5,
					block.speed
				)

			// Move that element
			block.el.style.transform = `translate3d(0, ${position}px, 0)`
		})
	}
}
