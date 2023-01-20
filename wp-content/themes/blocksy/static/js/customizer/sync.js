import $ from 'jquery'
import ctEvents from 'ct-events'

$(document).on('click', '.customize-partial-edit-shortcut-button', (e) => {
	e.preventDefault()
	e.stopPropagation()
	e.stopImmediatePropagation()

	wp.customize.selectiveRefresh
		.partial(
			e.target.closest('[data-customize-partial-id]')
				? e.target.closest('[data-customize-partial-id]').dataset
						.customizePartialId
				: [
						...e.target.closest('.customize-partial-edit-shortcut')
							.classList,
				  ]
						.filter(
							(c) =>
								c.length >
								'customize-partial-edit-shortcut'.length
						)[0]
						.replace(/customize-partial-edit-shortcut-/, '')
		)
		.showControl()
})

let requireTest = require.context(
	'../../../inc/panel-builder/header/',
	true,
	/sync\.js$/
)
requireTest.keys().forEach(requireTest)

requireTest = require.context(
	'../../../inc/panel-builder/footer/',
	true,
	/sync\.js$/
)
requireTest.keys().forEach(requireTest)

requireTest = require.context('./sync', true, /\.js$/)
requireTest.keys().forEach(requireTest)

wp.customize.bind('change', (e) => {
	if (e.id !== 'header_placements') {
		return
	}

	ctEvents.trigger('ct:header:update-variables', e())
})

// Site title and description.
wp.customize('blogname', (value) =>
	value.bind((to) => $('.site-title a').text(to))
)
wp.customize('blogdescription', (value) =>
	value.bind((to) => $('.site-description').text(to))
)

export const updateAndSaveEl = (
	selector,
	cb,
	{ onlyView = false, isRoot = false } = {}
) => {
	if (!isRoot) {
		;(onlyView
			? [
					...document.querySelectorAll(
						`header#header [data-device="${onlyView}"] ${selector}`
					),
					...document.querySelectorAll(
						`#offcanvas [data-device="${onlyView}"] ${selector}`
					),
			  ]
			: [
					...document.querySelectorAll(`header#header ${selector}`),
					...document.querySelectorAll(`#offcanvas ${selector}`),
			  ]
		).map((el) => cb(el))
	}

	if (isRoot) {
		cb(document.querySelector(`header#header`))
	}
}

export { handleBackgroundOptionFor } from './sync/variables/background'
export {
	withKeys,
	assembleSelector,
	mutateSelector,
	getRootSelectorFor,
	getPrefixFor,
	getOptionFor,
	applyPrefixFor,
	watchOptionsWithPrefix,
} from './sync/helpers'
export { responsiveClassesFor } from './sync/helpers'
export { typographyOption } from './sync/variables/typography'
