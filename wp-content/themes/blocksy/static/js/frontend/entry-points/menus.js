import ctEvents from 'ct-events'
import { getCurrentScreen } from '../helpers/current-screen'

const loadMenuEntry = () => import('../header/menu')

export const menuEntryPoints = [
	{
		els: () => ['header [data-device="desktop"] [data-id*="menu"] > .menu'],
		condition: () => getCurrentScreen() === 'desktop',
		load: loadMenuEntry,
		onLoad: false,
		mount: ({ el, mountMenuLevel }) =>
			mountMenuLevel(el, { startPosition: 'left' }),
		events: ['ct:general:device-change', 'ct:header:init-popper'],
	},

	{
		els: () => [
			'header [data-device="desktop"] [data-id*="menu"] > .menu .menu-item-has-children',
			'header [data-device="desktop"] [data-id*="menu"] > .menu .page_item_has_children',
		],
		load: loadMenuEntry,
		mount: ({ handleUpdate, el }) => handleUpdate(el),
		onLoad: false,
		events: ['ct:general:device-change', 'ct:header:init-popper'],
		condition: ({ allEls }) => getCurrentScreen() === 'desktop',
	},

	{
		els: () => [
			...document.querySelectorAll(
				'header [data-device="desktop"] [data-id^="menu"][data-responsive]'
			),
		],
		// load: () => new Promise((r) => r({ mount: mountResponsiveHeader })),
		load: () => import('../header/responsive-desktop-menu'),
		// onLoad: false,
		events: ['ct:general:device-change', 'ct:header:render-frame'],
		condition: () => {
			if (getCurrentScreen() !== 'desktop') {
				return false
			}

			let allResults = [
				...document.querySelectorAll(
					'header [data-device="desktop"] [data-id^="menu"][data-responsive]'
				),
			].map((menu) => {
				// true - no enough space
				// false enough space

				if (
					window.blocksyResponsiveMenuCache &&
					window.blocksyResponsiveMenuCache[menu.id] &&
					window.blocksyResponsiveMenuCache[menu.id].enabled
				) {
					return window.blocksyResponsiveMenuCache[menu.id].enabled
				}

				if (!menu.firstElementChild) {
					if (!window.blocksyResponsiveMenuCache) {
						window.blocksyResponsiveMenuCache = {}
					}

					window.blocksyResponsiveMenuCache = {
						...window.blocksyResponsiveMenuCache,
						[menu.id]: {
							enabled: false,
						},
					}

					return false
				}

				let baseContainer = menu.closest('[class*="ct-container"]')

				let hasResponsive =
					baseContainer.getBoundingClientRect().width -
						[
							...baseContainer.querySelectorAll(
								'[data-id]:not([data-id*="menu"])'
							),
						].reduce((t, item) => {
							let style = window.getComputedStyle(item)

							return (
								t +
								item.getBoundingClientRect().width +
								parseInt(
									style.getPropertyValue('margin-left')
								) +
								parseInt(style.getPropertyValue('margin-right'))
							)
						}, 0) <
					[
						...baseContainer.querySelectorAll(
							'[data-id*="menu"] > * > *'
						),
					].reduce((t, el) => {
						let style = window.getComputedStyle(
							el.closest('[data-id*="menu"]')
						)

						return (
							t +
							el.getBoundingClientRect().width +
							parseInt(style.getPropertyValue('margin-left')) +
							parseInt(style.getPropertyValue('margin-right'))
						)
					}, 0)

				if (!hasResponsive) {
					let hadResponsive = menu.dataset.responsive
					menu.dataset.responsive = 'yes'

					if (hadResponsive === 'no') {
						ctEvents.trigger('ct:header:init-popper')
					}
				}

				if (!window.blocksyResponsiveMenuCache) {
					window.blocksyResponsiveMenuCache = {}
				}

				window.blocksyResponsiveMenuCache = {
					...window.blocksyResponsiveMenuCache,
					[menu.id]: {
						enabled: hasResponsive,
					},
				}

				return hasResponsive
			})

			let finalRes = allResults.filter((r) => !!r).length > 0
			return finalRes
		},
	},

	{
		els: () =>
			'header [data-device="desktop"] [data-id^="menu"]:not([data-responsive])',
		load: () =>
			new Promise((r) =>
				r({
					mount: (el) => {
						ctEvents.trigger('ct:header:init-popper')
					},
				})
			),
	},
]
