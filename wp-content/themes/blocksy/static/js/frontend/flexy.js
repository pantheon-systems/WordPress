import $ from 'jquery'
import { Flexy, adjustContainerHeightFor } from 'flexy'
import ctEvents from 'ct-events'
import { getCurrentScreen } from '../frontend/helpers/current-screen'

export const mount = (sliderEl, args) => {
	// sliderEl = sliderEl.parentNode

	if (sliderEl.flexy) {
		return
	}

	let maybePillsSlider = sliderEl.querySelector('.flexy-pills [data-flexy]')

	const inst = new Flexy(sliderEl.querySelector('.flexy-items'), {
		flexyAttributeEl: sliderEl,
		elementsThatDoNotStartDrag: ['.twentytwenty-handle'],
		adjustHeight: !!sliderEl.querySelector('.flexy-items').dataset.height,

		...(args.event ? { initialDragEvent: args.event } : {}),

		autoplay:
			Object.keys(sliderEl.dataset).indexOf('autoplay') > -1 &&
			parseInt(sliderEl.dataset.autoplay, 10)
				? sliderEl.dataset.autoplay
				: false,

		...(sliderEl.querySelector('.flexy-pills')
			? {
					pillsContainerSelector: sliderEl.querySelector(
						'.flexy-pills'
					).firstElementChild,
			  }
			: {}),
		leftArrow: sliderEl.querySelector('.flexy .flexy-arrow-prev'),
		rightArrow: sliderEl.querySelector('.flexy .flexy-arrow-next'),
		scaleRotateEffect: false,

		onDragStart: (e) => {
			if (!e.target.closest('.flexy-items')) {
				return
			}

			Array.from(
				e.target.closest('.flexy-items').querySelectorAll('.zoomImg')
			).map((img) => {
				$(img).stop().fadeTo(120, 0)
			})
		},

		// viewport | container
		wrapAroundMode:
			sliderEl.dataset.wrap === 'viewport' ? 'viewport' : 'container',

		...(maybePillsSlider
			? {
					pillsFlexyInstance: maybePillsSlider,
			  }
			: {}),
	})

	if (maybePillsSlider) {
		const inst = new Flexy(maybePillsSlider, {
			elementsThatDoNotStartDrag: ['.twentytwenty-handle'],
			// viewport | container
			wrapAroundMode:
				maybePillsSlider.dataset.wrap === 'viewport'
					? 'viewport'
					: 'container',

			leftArrow: maybePillsSlider.parentNode.querySelector(
				'.flexy-arrow-prev'
			),
			rightArrow: maybePillsSlider.parentNode.querySelector(
				'.flexy-arrow-next'
			),
			hasDragAndDrop: false,

			...(maybePillsSlider.closest('.thumbs-left') &&
			getCurrentScreen({ withTablet: true }) !== 'mobile'
				? {
						orientation: 'vertical',
				  }
				: {}),
		})

		maybePillsSlider.flexy = inst
	}

	sliderEl.flexy = inst
}

ctEvents.on('ct:flexy:update-height', () => {
	;[...document.querySelectorAll('.flexy-container')].map((el) => {
		if (!el.flexy) {
			return
		}

		adjustContainerHeightFor(el.flexy)
	})
})
