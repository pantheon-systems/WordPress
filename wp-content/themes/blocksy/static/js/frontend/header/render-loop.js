import ctEvents from 'ct-events'

let prevInnerWidth = null

let prevScrollY = null

const renderHeader = () => {
	if (!prevInnerWidth || window.innerWidth !== prevInnerWidth) {
		prevInnerWidth = window.innerWidth
		ctEvents.trigger('ct:header:render-frame')
	}

	if (prevScrollY === null || window.scrollY !== prevScrollY) {
		prevScrollY = window.scrollY
		ctEvents.trigger('ct:scroll:render-frame')
	}

	requestAnimationFrame(renderHeader)
}

export const mountRenderHeaderLoop = () => {
	requestAnimationFrame(renderHeader)
}
