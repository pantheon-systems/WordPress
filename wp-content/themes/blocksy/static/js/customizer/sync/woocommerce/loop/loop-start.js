import { watchOptionsWithPrefix } from '../../helpers'
import { replaceCards } from '../archive-product'

watchOptionsWithPrefix({
	getOptionsForPrefix: () => ['shop_structure', 'shop_columns'],
	render: () => {
		;[...document.querySelectorAll('.shop-entries')].map((el) => {
			const structure = wp.customize('shop_structure')()

			el.dataset.layout = structure
		})

		replaceCards()
	},
})
