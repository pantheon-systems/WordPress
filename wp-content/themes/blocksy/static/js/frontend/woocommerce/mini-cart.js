import $ from 'jquery'
import ctEvents from 'ct-events'

let mounted = false

export const mount = () => {
	if (!$) return

	const selector = '.ct-header-cart, .ct-shortcuts-container [data-id="cart"]'

	if (mounted) {
		return
	}

	mounted = true

	$(document.body).on('adding_to_cart', () =>
		[...document.querySelectorAll(selector)].map((cart) => {
			if (!cart.closest('.ct-shortcuts-container')) {
				cart = cart.firstElementChild
			}

			cart.classList.remove('ct-added')
			cart.classList.add('ct-adding')
		})
	)

	$(document.body).on('wc_fragments_loaded', () => {
		setTimeout(() => ctEvents.trigger('ct:popper-elements:update'))
		setTimeout(() => ctEvents.trigger('blocksy:frontend:init'))
	})

	$(document.body).on('wc_cart_button_updated', () => {
		setTimeout(() => {
			;[...document.querySelectorAll(selector)].map((cart, index) => {
				if (index > 0) {
					return
				}

				if (
					!document.querySelector('.quick-view-modal.active') &&
					((!document.body.classList.contains('single-product') &&
						cart.querySelector('[data-auto-open*="archive"]')) ||
						(document.body.classList.contains('single-product') &&
							cart.querySelector('[data-auto-open*="product"]')))
				) {
					cart.querySelector('[data-auto-open]').focusDisabled = true
					cart.querySelector('[data-auto-open]').click()

				}
			})
		}, 100)
	})

	$(document.body).on(
		'added_to_cart',
		(_, fragments, __, button, quantity) => {
			button = button[0]
			;[...document.querySelectorAll(selector)].map((cart, index) => {
				let elForOpen = cart

				if (!cart.closest('.ct-shortcuts-container')) {
					elForOpen = cart.firstElementChild
				}

				elForOpen.classList.remove('ct-adding')
				elForOpen.classList.add('ct-added')

				if (document.querySelector('.ct-cart-content')) {
					if (cart.querySelector('.ct-cart-content')) {
						cart.querySelector('.ct-cart-content').innerHTML =
							Object.values(fragments)[0]

						if (
							cart.querySelector('.ct-cart-total') &&
							cart.querySelector(
								'.ct-cart-content .woocommerce-mini-cart__total .woocommerce-Price-amount'
							)
						) {
							cart.querySelector(
								'.ct-cart-total'
							).firstElementChild.innerHTML = cart.querySelector(
								'.ct-cart-content .woocommerce-mini-cart__total .woocommerce-Price-amount'
							).innerHTML
						}
					}
				}
			})
		}
	)

	$(document.body).on('removed_from_cart', (_, __, ___, button) =>
		[...document.querySelectorAll(selector)].map((cart) => {
			if (!button) return

			try {
				button[0]
					.closest('li')
					.parentNode.removeChild(button[0].closest('li'))
			} catch (e) {}
		})
	)

	$(document).on('uael_quick_view_loader_stop', () => {
		ctEvents.trigger('ct:add-to-cart:quantity')
	})

	$(document).on('facetwp-loaded', () => {
		ctEvents.trigger('ct:custom-select:init')
	})

	$(window).on('wpf_ajax_success', function () {
		ctEvents.trigger('blocksy:frontend:init')
	})

	$(document).on('prdctfltr-reload', function () {
		ctEvents.trigger('blocksy:frontend:init')
	})

	setTimeout(() => {
		if (window.woof_mass_reinit) {
			const prevFn = window.woof_mass_reinit

			window.woof_mass_reinit = () => {
				ctEvents.trigger('blocksy:frontend:init')
				prevFn()
			}
		}
	}, 1000)

	const clearCartContent = () => {
		let maybeCartContent = document.querySelector(
			'.ct-header-cart .ct-cart-content'
		)

		if (maybeCartContent) {
			maybeCartContent.removeAttribute('style')
		}
	}

	$(document.body).on('wc_fragments_refreshed', () => {
		setTimeout(() => {
			ctEvents.trigger('blocksy:frontend:init')
			ctEvents.trigger('ct:popper-elements:update')
			clearCartContent()
		})
	})

	$(document.body).on('wc_fragments_loaded', () => {
		setTimeout(() => {
			ctEvents.trigger('blocksy:frontend:init')
			ctEvents.trigger('ct:popper-elements:update')

			clearCartContent()
		})
	})
}
