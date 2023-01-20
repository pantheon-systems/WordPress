import $ from 'jquery'
import ctEvents from 'ct-events'

let originalImageUpdate = null

const store = {}

function isTouchDevice() {
	try {
		document.createEvent('TouchEvent')
		return true
	} catch (e) {
		return false
	}
}

const cachedFetch = (url) =>
	store[url]
		? new Promise((resolve) => {
				resolve(store[url])
				store[url] = store[url].clone()
		  })
		: new Promise((resolve) =>
				fetch(url).then((response) => {
					resolve(response)
					store[url] = response.clone()
				})
		  )

const makeUrlFor = ({ variation, productId, isQuickView }) => {
	let url = new URL(ct_localizations.ajax_url)
	let params = new URLSearchParams(url.search.slice(1))

	params.append('action', 'blocksy_get_product_view_for_variation')
	params.append('variation_id', variation.variation_id)
	params.append('product_id', productId)
	params.append('is_quick_view', isQuickView)

	url.search = `?${params.toString()}`

	return url.toString()
}

const replaceFirstImage = ({ container, image }) => {
	if (!image) {
		return
	}

	const containersToReplace = []

	const selectorsToTry = [
		'.woocommerce-product-gallery > .ct-image-container',
		'.woocommerce-product-gallery .flexy-items > *:first-child > *',
		'.woocommerce-product-gallery .flexy-pills > ol > *:first-child > *',
	]

	selectorsToTry.map((selector) => {
		if (container.parentNode.querySelector(selector)) {
			containersToReplace.push(
				container.parentNode.querySelector(selector)
			)
		}
	})

	containersToReplace.map((imgContainer) => {
		if (imgContainer.href) {
			imgContainer.href = image.full_src
		}

		if (imgContainer.dataset.height) {
			imgContainer.dataset.height = image.full_src_h
		}

		if (imgContainer.dataset.width) {
			imgContainer.dataset.width = image.full_src_w
		}

		;[...imgContainer.querySelectorAll('.zoomImg')].map((img) => {
			img.remove()
		})
		;[...imgContainer.querySelectorAll('img, source')].map((img) => {
			if (img.matches('.zoomImg')) {
				return
			}

			if (img.getAttribute('width')) {
				img.width =
					image.width ||
					(img.closest('.flexy-pills')
						? image.gallery_thumbnail_src_w
						: image.src_w)
			}

			if (img.getAttribute('height')) {
				img.height =
					image.height ||
					(img.closest('.flexy-pills')
						? image.gallery_thumbnail_src_h
						: image.src_h)
			}

			img.src = img.closest('.flexy-pills')
				? image.gallery_thumbnail_src
				: image.src

			if (image.srcset && img.srcset && image.srcset !== 'false') {
				img.srcset = image.srcset
			} else {
				img.removeAttribute('srcset')
			}
		})

		if ($.fn.zoom) {
			if (
				(window.wp &&
					wp.customize &&
					wp.customize('has_product_single_zoom') &&
					wp.customize('has_product_single_zoom')() === 'yes') ||
				!window.wp ||
				!wp.customize
			) {
				const rect = imgContainer.getBoundingClientRect()

				if (
					parseFloat(imgContainer.getAttribute('data-width')) >
					imgContainer
						.closest('.woocommerce-product-gallery')
						.getBoundingClientRect().width
				) {
					$(imgContainer).zoom({
						url: imgContainer.href,
						touch: false,
						duration: 50,

						...(rect.width >
							parseFloat(imgContainer.dataset.width) ||
						rect.height > parseFloat(imgContainer.dataset.height)
							? {
									magnify: 2,
							  }
							: {}),

						...(isTouchDevice()
							? {
									on: 'toggle',
							  }
							: {}),
					})
				}
			}
		}
	})
}

const performInPlaceUpdate = ({
	container,
	currentVariationObj,
	nextVariationObj,
}) => {
	const currentImage = currentVariationObj
		? { id: currentVariationObj.image_id, ...currentVariationObj.image }
		: (nextVariationObj || {}).blocksy_original_image

	const nextImage = nextVariationObj
		? { id: nextVariationObj.image_id, ...nextVariationObj.image }
		: (currentVariationObj || {}).blocksy_original_image

	if (!nextImage) {
		return
	}

	if (
		currentImage &&
		parseFloat(nextImage.id) === parseFloat(currentImage.id)
	) {
		return
	}

	// Attempt slide to image

	if (container.querySelector(`.flexy-pills > *`)) {
		let maybePillImage = container.querySelector(
			`.flexy-items [srcset*="${nextImage.src}"]`
		)

		if (maybePillImage) {
			let pillIndex = [
				...container.querySelector(`.flexy-items`).children,
			].indexOf(maybePillImage.closest('div'))

			const pill =
				container.querySelector(`.flexy-pills > *`).children[pillIndex]

			if (pill) {
				if (
					container
						.querySelector('[data-flexy]')
						.dataset.flexy.indexOf('no') > -1
				) {
					if (container.querySelector('[data-flexy]').forcedMount) {
						container.querySelector('[data-flexy]').forcedMount()
					}

					setTimeout(() => {
						if (nextVariationObj) {
							replaceFirstImage({
								container,
								image: nextVariationObj.blocksy_original_image,
							})
						}
						pill.click()
					}, 500)

					return
				} else {
					if (nextVariationObj) {
						replaceFirstImage({
							container,
							image: nextVariationObj.blocksy_original_image,
						})
					}

					pill.click()
					return
				}
			}
		}
	}

	// Replace 1st image

	replaceFirstImage({ container, image: nextImage })

	if (container.querySelector(`.flexy-pills > *`)) {
		const pill = container.querySelector(`.flexy-pills > *`).children[0]

		if (pill) {
			pill.click()
		}
	}
}

export const mount = (el) => {
	if (!$ || !$.fn || !$.fn.wc_variations_image_update) {
		return
	}

	originalImageUpdate = $.fn.wc_variations_image_update

	$.fn.wc_variations_image_update = function (variation) {
		const currentElement = this[0]

		if (
			currentElement.closest('.woobt-products') ||
			currentElement.closest('.upsells') ||
			currentElement.closest('.related')
		) {
			return
		}

		const currentVariation = el
			.closest('.product')
			.querySelector('.woocommerce-product-gallery')

		let productContainer = currentVariation.closest('.type-product')

		let isQuickView = 'no'

		let productId = productContainer.id.replace('product-', '')

		if (!productId) {
			productId = currentVariation
				.closest('[class*="ct-quick-view"]')
				.querySelector('[data-product_id]').dataset.product_id

			if (productId) {
				isQuickView = 'yes'
			}
		}

		const allVariations = JSON.parse(el.dataset.product_variations)

		let nextVariationObj = false
		let currentVariationObj = false

		if (allVariations) {
			nextVariationObj = variation.variation_id
				? allVariations.find(
						({ variation_id }) =>
							parseInt(variation_id) ===
							parseInt(variation.variation_id)
				  )
				: false
			currentVariationObj = currentVariation.dataset.currentVariation
				? allVariations.find(
						({ variation_id }) =>
							parseInt(variation_id) ===
							parseInt(currentVariation.dataset.currentVariation)
				  )
				: false
		}

		let defaultCanDoInPlaceUpdate = '__DEFAULT__'

		if (
			variation &&
			!variation.variation_id &&
			currentElement.querySelector('.wcpa_form_outer')
		) {
			defaultCanDoInPlaceUpdate = true
			nextVariationObj = variation
		}

		if (
			defaultCanDoInPlaceUpdate === '__DEFAULT__' &&
			!variation.variation_id &&
			!currentVariation.dataset.currentVariation
		) {
			return
		}

		if (
			defaultCanDoInPlaceUpdate === '__DEFAULT__' &&
			parseInt(variation.variation_id) ===
				parseInt(currentVariation.dataset.currentVariation)
		) {
			return
		}

		if (
			variation.variation_id ||
			defaultCanDoInPlaceUpdate === '__DEFAULT__'
		) {
			currentVariation.dataset.currentVariation =
				variation.variation_id || '0'
		} else {
			currentVariation.removeAttribute('data-current-variation')
		}

		const canDoInPlaceUpdate =
			defaultCanDoInPlaceUpdate === '__DEFAULT__'
				? allVariations &&
				  [nextVariationObj, currentVariationObj].every((variation) => {
						if (!variation) {
							return true
						}

						return variation.blocksy_gallery_source === 'default'
				  })
				: defaultCanDoInPlaceUpdate

		if (canDoInPlaceUpdate) {
			performInPlaceUpdate({
				container: currentVariation,
				nextVariationObj,
				currentVariationObj,
			})
			return
		}

		const acceptHtml = (html, style) => {
			const div = document.createElement('div')
			div.innerHTML = html
			;[...div.firstElementChild.children].map((el, index) => {
				if (
					!el.matches(
						'.flexy-container, .ct-image-container, .ct-before-gallery'
					)
				) {
					el.remove()
				}
			})
			let didInsert = false
			;[...currentVariation.children].map((el, index) => {
				if (el.matches('.flexy-container, .ct-image-container')) {
					if (!didInsert) {
						didInsert = true
						el.insertAdjacentHTML(
							'beforebegin',
							div.firstElementChild.innerHTML
						)
					}
				}

				if (
					el.matches(
						'.flexy-container, .ct-image-container, .ct-before-gallery'
					)
				) {
					el.remove()
				}
			})

			currentVariation
				.closest('.product')
				.classList.remove('thumbs-left', 'thumbs-bottom')

			if (currentVariation.querySelector('.flexy-container')) {
				currentVariation.closest('.product').classList.add(style)
			}

			currentVariation.hasLazyLoadClickHoverListener = false

			setTimeout(() => {
				ctEvents.trigger('blocksy:frontend:init')
				currentVariation.removeAttribute('data-state')
			})
		}

		if (variation.blocksy_gallery_html) {
			acceptHtml(
				variation.blocksy_gallery_html,
				variation.blocksy_gallery_style
			)
			return
		}

		currentVariation.removeAttribute('style')
		requestAnimationFrame(() => {
			currentVariation.dataset.state = 'loading'
		})

		let maybeLoadedVariation = allVariations
			? allVariations.find(
					(nestedVariation) =>
						store[
							makeUrlFor({
								variation: nestedVariation,
								productId,
								isQuickView,
							})
						] &&
						nestedVariation.image_id === variation.image_id &&
						variation.blocksy_gallery_source === 'default' &&
						nestedVariation.blocksy_gallery_source === 'default'
			  )
			: null

		cachedFetch(
			makeUrlFor({
				variation: maybeLoadedVariation || variation,
				productId,
				isQuickView,
			}),
			{
				method: 'POST',
			}
		)
			.then((response) => response.json())
			.then(({ success, data }) => {
				if (!success) {
					return
				}

				acceptHtml(data.html, data.blocksy_gallery_style)
			})
	}
}
