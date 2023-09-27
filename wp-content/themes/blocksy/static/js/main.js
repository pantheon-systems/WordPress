import './public-path.js'
import './events'

import ctEvents from 'ct-events'
import $ from 'jquery'

import { watchLayoutContainerForReveal } from './frontend/animated-element'
import { onDocumentLoaded, handleEntryPoints, loadStyle } from './helpers'

import { getCurrentScreen } from './frontend/helpers/current-screen'
import { mountDynamicChunks } from './dynamic-chunks'

import { mountRenderHeaderLoop } from './frontend/header/render-loop'

import { menuEntryPoints } from './frontend/entry-points/menus'
import { liveSearchEntryPoints } from './frontend/entry-points/live-search'
import { wooEntryPoints } from './frontend/woocommerce/main'

import { mountElementorIntegration } from './frontend/integration/elementor'

/**
 * iOS hover fix
 */
document.addEventListener('click', (x) => 0)

export const areWeDealingWithSafari = /apple/i.test(navigator.vendor)

export { getCurrentScreen } from './frontend/helpers/current-screen'

import {
	fastOverlayHandleClick,
	fastOverlayMount,
} from './frontend/fast-overlay'

export const allFrontendEntryPoints = [
	...menuEntryPoints,
	...liveSearchEntryPoints,
	...wooEntryPoints,

	/*
	{
		els: '#main [data-sticky]',
		load: () => import('./frontend/sticky'),
		condition: () => areWeDealingWithSafari,
	},
    */

	{
		els: '[data-parallax]',
		load: () => import('./frontend/parallax/register-listener'),
		events: ['blocksy:parallax:init'],
	},

	{
		els: '.flexy-container[data-flexy*="no"]',
		load: () => import('./frontend/flexy'),
		events: ['ct:flexy:update'],
		trigger: ['hover-with-touch'],
	},

	{
		els: '.ct-share-box [data-network="pinterest"]',
		load: () => import('./frontend/social-buttons'),
		trigger: ['click'],
	},

	{
		els: '.ct-share-box [data-network]:not([data-network="pinterest"]):not([data-network="email"])',
		load: () => import('./frontend/social-buttons'),
		trigger: ['click'],
		condition: () =>
			!/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
				navigator.userAgent
			),
	},

	{
		els: [
			...(document.querySelector('.ct-header-cart > .ct-cart-content')
				? ['.ct-header-cart > .ct-cart-item']
				: []),
			'.ct-language-switcher > .ct-active-language',
		],
		load: () => import('./frontend/popper-elements'),
		trigger: ['hover'],
		events: ['ct:popper-elements:update'],
	},

	{
		els: '.ct-back-to-top, .ct-shortcuts-container [data-shortcut*="scroll_top"]',
		load: () => import('./frontend/back-to-top-link'),
		events: ['ct:back-to-top:mount'],
		trigger: ['scroll'],
	},

	{
		els: '.ct-pagination:not([data-pagination="simple"])',
		load: () => import('./frontend/layouts/infinite-scroll'),
		trigger: ['scroll'],
	},

	{
		els: ['.entries[data-layout]', '[data-products].products'],
		load: () =>
			new Promise((r) => r({ mount: watchLayoutContainerForReveal })),
	},

	{
		els: ['.ct-modal-action'],
		load: () => new Promise((r) => r({ mount: fastOverlayMount })),
		events: ['ct:header:update'],
		trigger: ['click'],
	},

	{
		els: ['.ct-header-search'],
		load: () => new Promise((r) => r({ mount: fastOverlayMount })),
		mount: ({ mount, el, ...rest }) => {
			mount(el, {
				...rest,
				focus: true,
			})
		},
		events: [],
		trigger: ['click'],
	},
]

handleEntryPoints(allFrontendEntryPoints, {
	immediate: /comp|inter|loaded/.test(document.readyState),
})

const initOverlayTrigger = () => {
	;[
		...document.querySelectorAll('.ct-header-trigger'),
		...document.querySelectorAll('.ct-offcanvas-trigger'),
	].map((menuToggle) => {
		if (menuToggle && !menuToggle.hasListener) {
			menuToggle.hasListener = true

			menuToggle.addEventListener('click', (event) => {
				event.preventDefault()

				if (!menuToggle.dataset.togglePanel && !menuToggle.hash) {
					return
				}

				let offcanvas = document.querySelector(
					menuToggle.dataset.togglePanel || menuToggle.hash
				)

				if (!offcanvas) {
					return
				}

				fastOverlayHandleClick(event, {
					container: offcanvas,
					closeWhenLinkInside: !menuToggle.closest('.ct-header-cart'),
					computeScrollContainer: () =>
						offcanvas.querySelector('.cart_list') &&
						!offcanvas.querySelector('[data-id="cart"] .cart_list')
							? offcanvas.querySelector('.cart_list')
							: getCurrentScreen() === 'mobile' &&
							  offcanvas.querySelector('[data-device="mobile"]')
							? offcanvas.querySelector('[data-device="mobile"]')
							: offcanvas.querySelector('.ct-panel-content'),
				})
			})
		}
	})
}

const mountAsideType4 = () => {
	;[...document.querySelectorAll('aside[data-type="type-4"]')].map(
		(sidebar) => {
			let scrollbarWidth =
				window.innerWidth - document.documentElement.clientWidth

			if (scrollbarWidth > 0) {
				sidebar.style.setProperty(
					'--scrollbar-width',
					`${scrollbarWidth}px`
				)
			}

			sidebar.style.setProperty('--has-scrollbar', 1)
		}
	)
}

onDocumentLoaded(() => {
	document.body.addEventListener(
		'mouseover',
		() => {
			loadStyle(ct_localizations.dynamic_styles.lazy_load)
		},
		{ once: true, passive: true }
	)

	let inputs = [
		...document.querySelectorAll(
			'.comment-form [class*="comment-form-field"]'
		),
	]
		.reduce(
			(result, parent) => [
				...result,
				parent.querySelector('input,textarea'),
			],
			[]
		)
		.filter((input) => input.type !== 'hidden' && input.type !== 'checkbox')

	const renderEmptiness = () => {
		inputs.map((input) => {
			input.parentNode.classList.remove('ct-not-empty')

			if (!input.value) {
				return
			}

			if (input.value.trim().length > 0) {
				input.parentNode.classList.add('ct-not-empty')
			}
		})
	}

	setTimeout(() => {
		renderEmptiness()
	})

	inputs.map((input) => input.addEventListener('input', renderEmptiness))

	mountDynamicChunks()
	mountAsideType4()
	setTimeout(() => document.body.classList.remove('ct-loading'), 1500)

	setTimeout(() => {
		initOverlayTrigger()
	})

	mountRenderHeaderLoop()

	mountElementorIntegration()
})

if ($) {
	$(document.body).on('wc_fragments_refreshed', () => {
		ctEvents.trigger('blocksy:frontend:init')
	})

	// https://woocommerce.com/document/composite-products/composite-products-js-api-reference/#using-the-api
	$('.composite_data').on('wc-composite-initializing', (event, composite) => {
		composite.actions.add_action('component_selection_changed', () => {
			setTimeout(() => {
				ctEvents.trigger('blocksy:frontend:init')
			}, 1000)
		})
	})

	$(document.body).on('wc_fragments_loaded', () => {
		ctEvents.trigger('blocksy:frontend:init')
	})

	$(document).on('jet-filter-content-rendered', () => {
		ctEvents.trigger('blocksy:frontend:init')
	})

	$(document).on('yith_infs_added_elem', function () {
		ctEvents.trigger('blocksy:frontend:init')
	})

	jQuery(document).on('yith-wcan-ajax-filtered', function () {
		ctEvents.trigger('blocksy:frontend:init')
	})

	$(document).on('berocket_ajax_filtering_end', () => {
		setTimeout(() => {
			ctEvents.trigger('blocksy:frontend:init')
		}, 100)
	})

	$(document).on('preload', () => {
		ctEvents.trigger('blocksy:frontend:init')
	})

	document.addEventListener('wpfAjaxSuccess', (e) => {
		ctEvents.trigger('blocksy:frontend:init')
	})

	document.addEventListener('facetwp-loaded', () => {
		ctEvents.trigger('blocksy:frontend:init')
	})

	$(document).on('sf:ajaxfinish', () => {
		ctEvents.trigger('blocksy:frontend:init')
	})

	$(document).on('ddwcpoRenderVariation', () => {
		setTimeout(() => {
			ctEvents.trigger('blocksy:frontend:init')
		})
	})
}

ctEvents.on('blocksy:frontend:init', () => {
	handleEntryPoints(allFrontendEntryPoints, {
		immediate: true,
		skipEvents: true,
	})

	mountDynamicChunks()

	mountAsideType4()
	initOverlayTrigger()
})

ctEvents.on(
	'ct:overlay:handle-click',
	({ e, href, container, options = {} }) => {
		fastOverlayHandleClick(e, {
			...(href
				? {
						container: document.querySelector(href),
				  }
				: {}),

			...(container ? { container } : {}),
			...options,
		})
	}
)

export { loadStyle, handleEntryPoints, onDocumentLoaded } from './helpers'
export { registerDynamicChunk } from './dynamic-chunks'
