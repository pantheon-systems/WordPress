import { getOptionFor, setRatioFor, watchOptionsWithPrefix } from '../helpers'
import ctEvents from 'ct-events'

export const replaceCards = () => {
	if (!document.querySelector('[data-products]')) {
		return
	}

	;[...document.querySelectorAll('[data-products]')].map((el) => {
		el.classList.add('ct-disable-transitions')
	})
	;[...document.querySelectorAll('[data-products] > *')].map((product) => {
		const productsContainer = product.closest('[data-products]')
		const nextType = productsContainer.dataset.products

		// productsContainer.removeAttribute('data-alignment')

		// if (nextType === 'type-1') {
		// 	productsContainer.dataset.alignment = getOptionFor(
		// 		'shop_cards_alignment_1'
		// 	)
		// }

		const ratio = wp.customize('blocksy_woocommerce_thumbnail_cropping')()

		setRatioFor(
			ratio === 'uncropped'
				? 'original'
				: ratio === 'custom' || ratio === 'predefined'
				? `${wp.customize(
						'woocommerce_thumbnail_cropping_custom_width'
				  )()}/${wp.customize(
						'woocommerce_thumbnail_cropping_custom_height'
				  )()}`
				: '1/1',
			product.querySelector('.ct-image-container')
		)
	})
	;[...document.querySelectorAll('[data-products]')].map((el) => {
		if (el.closest('.related') || el.closest('.upsells')) {
			return
		}

		el.classList.remove('columns-2', 'columns-3', 'columns-4', 'columns-5')

		el.classList.add(
			`columns-${getOptionFor('woocommerce_catalog_columns')}`
		)
	})

	setTimeout(() => {
		;[...document.querySelectorAll('[data-products]')].map((el) => {
			el.classList.remove('ct-disable-transitions')
		})
	})
}

watchOptionsWithPrefix({
	getOptionsForPrefix: () => [
		'woocommerce_catalog_columns',
		'blocksy_woocommerce_thumbnail_cropping',
		'woocommerce_thumbnail_cropping_custom_width',
		'woocommerce_thumbnail_cropping_custom_height',
		// 'shop_cards_alignment_1',
	],

	events: ['ct:archive-product-replace-cards:perform'],

	render: () => replaceCards(),
})
