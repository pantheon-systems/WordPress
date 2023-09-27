import { createElement } from '@wordpress/element'

import ProductReviews from './ProductReviews'

import ctEvents from 'ct-events'

ctEvents.on('ct:extensions:card', ({ CustomComponent, extension }) => {
	if (extension.name !== 'product-reviews') return
	CustomComponent.extension = ProductReviews
})
