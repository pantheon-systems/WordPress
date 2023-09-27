import $ from 'jquery'

var currentTask

function singleProductAddToCart(wrapper) {
	if (!$) return

	var form = wrapper.closest('form')
	var button = form.find('button.button')
	var formUrl = $(form)[0].action
	var formMethod = form.attr('method')

	if (typeof formMethod === 'undefined' || formMethod == '') {
		formMethod = 'POST'
	}

	var formData = new FormData(form[0])
	formData.append(button.attr('name'), button.val())

	const quantity = [...formData.entries()].reduce(
		(total, current) =>
			total +
			(current[0].indexOf('quantity') > -1
				? parseInt(current[1], 10)
				: 0),
		0
	)

	if (quantity === 0) {
		// return
	}

	if (form.closest('.quick-view-modal').length) {
		form.closest('.quick-view-modal')
			.find('.ct-quick-add')
			.removeClass('added')

		form.closest('.quick-view-modal')
			.find('.ct-quick-add')
			.addClass('loading')
	}

	button.removeClass('added')
	button.addClass('loading')

	// Trigger event.
	$(document.body).trigger('adding_to_cart', [button, {}])

	currentTask = fetch(formUrl, {
		method: formMethod,
		body: formData,
		/*
		cache: false,
		contentType: false,
		processData: false,
        */
	})
		.then((r) => r.text())
		.then((data, textStatus, jqXHR) => {
			const div = document.createElement('div')
			div.innerHTML = data

			let error = div.querySelector('.woocommerce-error')

			if (error && error.innerHTML.length > 0) {
				let notices = document.querySelector(
					'.woocommerce-notices-wrapper'
				)

				if (notices.querySelector('.woocommerce-error')) {
					notices.querySelector('.woocommerce-error').remove()
				}

				if (notices) {
					notices.appendChild(error)
				}

				return
			}

			$(document.body).trigger('wc_fragment_refresh')

			$.ajax({
				url: wc_cart_fragments_params.wc_ajax_url
					.toString()
					.replace('%%endpoint%%', 'get_refreshed_fragments'),
				type: 'POST',
				success: (data) => {
					if (data && data.fragments) {
						$.each(data.fragments, function (key, value) {
							$(key).replaceWith(value)
						})

						$(document.body).trigger('wc_fragments_refreshed')
					}

					if (form.closest('.quick-view-modal').length) {
						form.closest('.quick-view-modal')
							.find('.ct-quick-add')
							.addClass('added')

						form.closest('.quick-view-modal')
							.find('.ct-quick-add')
							.removeClass('loading')
					}

					$(document.body).trigger('added_to_cart', [
						data.fragments,
						data.cart_hash,
						button,
						quantity,
					])
				},
			})
		})
		.catch(() => button.removeClass('loading'))
		.finally(() => button.removeClass('loading'))
}

export const mount = (el, { event }) => {
	if (!$) {
		return
	}

	ctEvents.trigger('ct:header:update')
	singleProductAddToCart($(el))
}
