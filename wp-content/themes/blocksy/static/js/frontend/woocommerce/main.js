import { onDocumentLoaded } from '../../helpers'
import ctEvents from 'ct-events'
import $ from 'jquery'

function isTouchDevice() {
	try {
		document.createEvent('TouchEvent')
		return true
	} catch (e) {
		return false
	}
}

export const wooEntryPoints = [
	{
		els: 'body.single-product .woocommerce-product-gallery',
		condition: () =>
			!!document.querySelector(
				'.woocommerce-product-gallery .ct-image-container'
			),
		load: () => import('./single-product-gallery'),
		trigger: ['hover-with-click'],
	},

	{
		els: 'form.variations_form',
		condition: () =>
			!!document.querySelector(
				'.woocommerce-product-gallery .ct-image-container'
			),
		load: () => import('./variable-products'),
		...(isTouchDevice()
			? {}
			: {
					trigger: ['hover'],
			  }),
	},

	{
		els: '.quantity > *',
		load: () => import('./quantity-input'),
		trigger: ['click'],
	},

	{
		els: () => [
			...document.querySelectorAll('.ct-ajax-add-to-cart .cart'),
			...document.querySelectorAll('.ct-floating-bar .cart'),
		],
		load: () => import('./add-to-cart-single'),
		trigger: ['submit'],
	},

	{
		els: '.ct-header-cart, .ajax_add_to_cart',
		load: () => import('./mini-cart'),
		events: ['ct:header:update'],
		trigger: ['hover-with-touch'],
	},
]

const initShortcut = () => {
	setTimeout(() => {
		let maybeShortcutCart = document.querySelector(
			'.ct-shortcuts-container [data-shortcut="cart"]'
		)

		if (maybeShortcutCart && !maybeShortcutCart.hasClickListener) {
			maybeShortcutCart.hasClickListener = true

			const handleEvent = (event) => {
				let maybeCart = document.querySelector(
					'.ct-header-cart .ct-offcanvas-trigger'
				)

				if (!maybeCart) {
					return
				}

				event.preventDefault()

				maybeCart.dispatchEvent(
					new MouseEvent(event.type, {
						view: window,
						bubbles: true,
						cancelable: true,
					})
				)
			}

			maybeShortcutCart.addEventListener('mouseover', handleEvent)
			maybeShortcutCart.addEventListener('click', handleEvent)
		}

		;[...document.querySelectorAll('#woo-cart-panel .qty')].map((el) => {
			if (el.hasChangeListener) {
				return
			}

			el.hasChangeListener = true

			$(el).on('change', (e) => {
				var item_hash = $(el)
					.attr('name')
					.replace(/cart\[([\w]+)\]\[qty\]/g, '$1')

				var item_quantity = $(el).val()
				var currentVal = parseFloat(item_quantity)

				$.ajax({
					type: 'POST',
					url: ct_localizations.ajax_url,
					data: {
						action: 'blocksy_update_qty_cart',
						hash: item_hash,
						quantity: currentVal,
					},
					success: (data) => {
						jQuery('body').trigger('updated_wc_div')
						ctEvents.trigger('ct:header:update')
					},
				})
			})
		})
	}, 100)
}

onDocumentLoaded(initShortcut)
ctEvents.on('blocksy:frontend:init', initShortcut)
