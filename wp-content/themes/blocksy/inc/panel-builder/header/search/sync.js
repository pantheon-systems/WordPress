import { handleBackgroundOptionFor } from '../../../../static/js/customizer/sync/variables/background'
import ctEvents from 'ct-events'
import { updateAndSaveEl } from '../../../../static/js/customizer/sync'
import { responsiveClassesFor } from '../../../../static/js/customizer/sync/helpers'

import {
	getRootSelectorFor,
	assembleSelector,
	mutateSelector,
} from '../../../../static/js/customizer/sync/helpers'
import { typographyOption } from '../../../../static/js/customizer/sync/variables/typography'

ctEvents.on(
	'ct:header:sync:collect-variable-descriptors',
	(variableDescriptors) => {
		variableDescriptors['search'] = ({ itemId }) => ({
			searchHeaderIconSize: {
				selector: assembleSelector(getRootSelectorFor({ itemId })),
				variable: 'icon-size',
				responsive: true,
				unit: 'px',
			},

			searchHeaderIconColor: [
				{
					selector: assembleSelector(getRootSelectorFor({ itemId })),
					variable: 'icon-color',
					type: 'color:default',
					responsive: true,
				},

				{
					selector: assembleSelector(getRootSelectorFor({ itemId })),
					variable: 'icon-hover-color',
					type: 'color:hover',
					responsive: true,
				},
			],

			...typographyOption({
				id: 'search_label_font',

				selector: assembleSelector(
					mutateSelector({
						selector: getRootSelectorFor({ itemId }),
						operation: 'suffix',
						to_add: '.ct-label',
					})
				),
			}),

			header_search_font_color: [
				{
					selector: assembleSelector(getRootSelectorFor({ itemId })),
					variable: 'linkInitialColor',
					type: 'color:default',
					responsive: true,
				},

				{
					selector: assembleSelector(getRootSelectorFor({ itemId })),
					variable: 'linkHoverColor',
					type: 'color:hover',
					responsive: true,
				},
			],

			// transparent state
			transparent_header_search_font_color: [
				{
					selector: assembleSelector(
						mutateSelector({
							selector: getRootSelectorFor({ itemId }),
							operation: 'between',
							to_add: '[data-transparent-row="yes"]',
						})
					),
					variable: 'linkInitialColor',
					type: 'color:default',
					responsive: true,
				},

				{
					selector: assembleSelector(
						mutateSelector({
							selector: getRootSelectorFor({ itemId }),
							operation: 'between',
							to_add: '[data-transparent-row="yes"]',
						})
					),
					variable: 'linkHoverColor',
					type: 'color:hover',
					responsive: true,
				},
			],

			transparentSearchHeaderIconColor: [
				{
					selector: assembleSelector(
						mutateSelector({
							selector: getRootSelectorFor({ itemId }),
							operation: 'between',
							to_add: '[data-transparent-row="yes"]',
						})
					),

					variable: 'icon-color',
					type: 'color:default',
					responsive: true,
				},

				{
					selector: assembleSelector(
						mutateSelector({
							selector: getRootSelectorFor({ itemId }),
							operation: 'between',
							to_add: '[data-transparent-row="yes"]',
						})
					),

					variable: 'icon-hover-color',
					type: 'color:hover',
					responsive: true,
				},
			],

			// sticky state
			sticky_header_search_font_color: [
				{
					selector: assembleSelector(
						mutateSelector({
							selector: getRootSelectorFor({ itemId }),
							operation: 'between',
							to_add: '[data-sticky*="yes"]',
						})
					),
					variable: 'linkInitialColor',
					type: 'color:default',
					responsive: true,
				},

				{
					selector: assembleSelector(
						mutateSelector({
							selector: getRootSelectorFor({ itemId }),
							operation: 'between',
							to_add: '[data-sticky*="yes"]',
						})
					),
					variable: 'linkHoverColor',
					type: 'color:hover',
					responsive: true,
				},
			],

			stickySearchHeaderIconColor: [
				{
					selector: assembleSelector(
						mutateSelector({
							selector: getRootSelectorFor({ itemId }),
							operation: 'between',
							to_add: '[data-sticky*="yes"]',
						})
					),
					variable: 'icon-color',
					type: 'color:default',
					responsive: true,
				},

				{
					selector: assembleSelector(
						mutateSelector({
							selector: getRootSelectorFor({ itemId }),
							operation: 'between',
							to_add: '[data-sticky*="yes"]',
						})
					),
					variable: 'icon-hover-color',
					type: 'color:hover',
					responsive: true,
				},
			],

			// modal
			...typographyOption({
				id: 'searchHeaderModalFont',

				selector: assembleSelector(
					`${
						getRootSelectorFor({ itemId })[0]
					} #search-modal .ct-search-results a`
				),
			}),

			searchHeaderLinkColor: [
				{
					selector: assembleSelector(
						`${getRootSelectorFor({ itemId })[0]} #search-modal`
					),
					variable: 'linkInitialColor',
					type: 'color:default',
					responsive: true,
				},

				{
					selector: assembleSelector(
						`${getRootSelectorFor({ itemId })[0]} #search-modal`
					),
					variable: 'linkHoverColor',
					type: 'color:hover',
					responsive: true,
				},
			],

			searchHeaderInputColor: [
				{
					selector: assembleSelector(
						`${getRootSelectorFor({ itemId })[0]} #search-modal`
					),
					variable: 'form-text-initial-color',
					type: 'color:default',
					responsive: true,
				},

				{
					selector: assembleSelector(
						`${getRootSelectorFor({ itemId })[0]} #search-modal`
					),
					variable: 'form-text-focus-color',
					type: 'color:focus',
					responsive: true,
				},
			],

			search_button_icon_color: [
				{
					selector: assembleSelector(
						`${
							getRootSelectorFor({ itemId })[0]
						} #search-modal form button`
					),
					variable: 'icon-color',
					type: 'color:default',
					responsive: true,
				},

				{
					selector: assembleSelector(
						`${
							getRootSelectorFor({ itemId })[0]
						} #search-modal form button`
					),
					variable: 'icon-focus-color',
					type: 'color:hover',
					responsive: true,
				},
			],

			search_button_background_color: [
				{
					selector: assembleSelector(
						`${
							getRootSelectorFor({ itemId })[0]
						} #search-modal form button`
					),
					variable: 'search-button-background',
					type: 'color:default',
					responsive: true,
				},

				{
					selector: assembleSelector(
						`${
							getRootSelectorFor({ itemId })[0]
						} #search-modal form button`
					),
					variable: 'search-button-focus-background',
					type: 'color:hover',
					responsive: true,
				},
			],

			search_close_button_color: [
				{
					selector: assembleSelector(
						`${
							getRootSelectorFor({ itemId })[0]
						} #search-modal .ct-toggle-close`
					),
					variable: 'icon-color',
					type: 'color:default',
				},

				{
					selector: assembleSelector(
						`${
							getRootSelectorFor({ itemId })[0]
						} #search-modal .ct-toggle-close:hover`
					),
					variable: 'icon-color',
					type: 'color:hover',
				},
			],

			search_close_button_border_color: [
				{
					selector: assembleSelector(
						`${
							getRootSelectorFor({ itemId })[0]
						} #search-modal .ct-toggle-close[data-type="type-2"]`
					),
					variable: 'toggle-button-border-color',
					type: 'color:default',
				},

				{
					selector: assembleSelector(
						`${
							getRootSelectorFor({ itemId })[0]
						} #search-modal .ct-toggle-close[data-type="type-2"]:hover`
					),
					variable: 'toggle-button-border-color',
					type: 'color:hover',
				},
			],

			search_close_button_shape_color: [
				{
					selector: assembleSelector(
						`${
							getRootSelectorFor({ itemId })[0]
						} #search-modal .ct-toggle-close[data-type="type-3"]`
					),
					variable: 'toggle-button-background',
					type: 'color:default',
				},

				{
					selector: assembleSelector(
						`${
							getRootSelectorFor({ itemId })[0]
						} #search-modal .ct-toggle-close[data-type="type-3"]:hover`
					),
					variable: 'toggle-button-background',
					type: 'color:hover',
				},
			],

			search_close_button_icon_size: {
				selector: assembleSelector(
					`${
						getRootSelectorFor({ itemId })[0]
					} #search-modal .ct-toggle-close`
				),
				variable: 'icon-size',
				unit: 'px',
			},

			search_close_button_border_radius: {
				selector: assembleSelector(
					`${
						getRootSelectorFor({ itemId })[0]
					} #search-modal .ct-toggle-close`
				),
				variable: 'toggle-button-radius',
				unit: 'px',
			},

			...handleBackgroundOptionFor({
				id: 'searchHeaderBackground',

				selector: assembleSelector(
					`${getRootSelectorFor({ itemId })[0]} #search-modal`
				),
			}),

			headerSearchMargin: {
				selector: assembleSelector(getRootSelectorFor({ itemId })),
				type: 'spacing',
				variable: 'margin',
				responsive: true,
				important: true,
			},
		})
	}
)

ctEvents.on('ct:header:sync:item:search', ({ optionId, optionValue }) => {
	const selector = '[data-id="search"]'

	if (optionId === 'search_label') {
		updateAndSaveEl(selector, (el) => {
			;[...el.querySelectorAll('.ct-label')].map((label) => {
				label.innerHTML = optionValue
			})
		})
	}

	if (optionId === 'search_label_visibility') {
		updateAndSaveEl(selector, (el) => {
			;[...el.querySelectorAll('.ct-label')].map((label) => {
				responsiveClassesFor(optionValue, label)
			})
		})
	}

	if (optionId === 'search_label_position') {
		updateAndSaveEl(
			selector,
			(el) => {
				if (!optionValue.desktop) {
					optionValue = {
						desktop: optionValue,
						mobile: optionValue,
					}
				}

				el.dataset.label = optionValue.desktop
			},
			{ onlyView: 'desktop' }
		)

		updateAndSaveEl(
			selector,
			(el) => {
				if (!optionValue.desktop) {
					optionValue = {
						desktop: optionValue,
						mobile: optionValue,
					}
				}

				el.dataset.label = optionValue.mobile
			},
			{ onlyView: 'mobile' }
		)
	}

	if (optionId === 'header_search_visibility') {
		updateAndSaveEl(selector, (el) =>
			responsiveClassesFor({ ...optionValue, desktop: true }, el)
		)
	}

	if (optionId === 'header_search_placeholder') {
		document.querySelector('#search-modal [type="search"]').placeholder =
			optionValue
	}

	if (optionId === 'searchHeaderImages') {
		let searchModal = document.querySelector(
			'#search-modal [data-live-results]'
		)
		let liveResultsAttr = searchModal.dataset.liveResults.split(':')
		if (optionValue === 'yes') {
			liveResultsAttr.push('thumbs')
		} else {
			liveResultsAttr = liveResultsAttr.filter(
				(attr) => attr !== 'thumbs'
			)
		}

		searchModal.dataset.liveResults = liveResultsAttr.join(':')
	}

	if (optionId === 'searchHeaderProductPrice') {
		let searchModal = document.querySelector(
			'#search-modal [data-live-results]'
		)
		let liveResultsAttr = searchModal.dataset.liveResults.split(':')
		if (optionValue === 'yes') {
			liveResultsAttr.push('product_price')
		} else {
			liveResultsAttr = liveResultsAttr.filter(
				(attr) => attr !== 'product_price'
			)
		}

		searchModal.dataset.liveResults = liveResultsAttr.join(':')
		searchModal.querySelector('[name="ct_product_price"]').value =
			optionValue === 'yes'
	}

	if (optionId === 'search_close_button_type') {
		let searchModalClose = document.querySelector(
			'#search-modal .ct-toggle-close'
		)

		setTimeout(() => {
			searchModalClose.classList.add('ct-disable-transitions')

			requestAnimationFrame(() => {
				if (searchModalClose) {
					searchModalClose.dataset.type = optionValue
				}

				setTimeout(() => {
					searchModalClose.classList.remove('ct-disable-transitions')
				})
			})
		}, 300)
	}
})
