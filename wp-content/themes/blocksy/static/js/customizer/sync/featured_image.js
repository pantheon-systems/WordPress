import {
	setRatioFor,
	watchOptionsWithPrefix,
	getOptionFor,
	responsiveClassesFor,
} from './helpers'
import { getPrefixFor } from './hero-section'

watchOptionsWithPrefix({
	getPrefix: getPrefixFor,

	getOptionsForPrefix: ({ prefix }) => [
		`${prefix}_featured_image_width`,
		`${prefix}_featured_image_ratio`,
		`${prefix}_featured_image_visibility`,
		`${prefix}_content_style`,
	],

	render: ({ prefix, id }) => {
		const image = document.querySelector(
			'.site-main article .ct-featured-image'
		)

		if (!image) {
			return
		}

		if (
			id === `${prefix}_featured_image_width` ||
			id === `${prefix}_content_style`
		) {
			image.classList.remove('alignwide')
			image.classList.remove('alignfull')

			if (getOptionFor('content_style', prefix) !== 'boxed') {
				if (getOptionFor('featured_image_width', prefix) === 'wide') {
					image.classList.add('alignwide')
				}

				if (getOptionFor('featured_image_width', prefix) === 'full') {
					image.classList.add('alignfull')
				}
			}
		}

		if (id === `${prefix}_featured_image_ratio`) {
			setRatioFor(
				getOptionFor('featured_image_ratio', prefix),
				image.querySelector('.ct-image-container')
			)
		}

		if (id === `${prefix}_featured_image_visibility`) {
			responsiveClassesFor(
				getOptionFor('featured_image_visibility', prefix),
				image
			)
		}
	},
})
