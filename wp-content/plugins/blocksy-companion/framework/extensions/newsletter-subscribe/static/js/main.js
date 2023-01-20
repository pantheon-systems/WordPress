import { registerDynamicChunk } from 'blocksy-frontend'

const submitMailchimp = (form) => {
	if (!form.querySelector('[type="email"]').value.trim()) {
		return
	}

	// Check for spam
	if (
		document.getElementById('js-validate-robot') &&
		document.getElementById('js-validate-robot').value !== ''
	) {
		return false
	}

	// Get url for mailchimp
	var url = form.action.replace('subscribe', 'subscribe/post-json')

	// Add form data to object
	var data = ''

	var callback = 'mailchimpCallback'

	var inputs = form.querySelectorAll('input')

	for (var i = 0; i < inputs.length; i++) {
		data += '&' + inputs[i].name + '=' + encodeURIComponent(inputs[i].value)
	}

	data += `&c=${callback}`

	// Create & add post script to the DOM
	var script = document.createElement('script')
	script.src = url + data

	document.body.appendChild(script)

	form.classList.remove('subscribe-error', 'subscribe-success')
	form.classList.add('subscribe-loading')

	// Callback function
	window[callback] = function (data) {
		// Remove post script from the DOM
		delete window[callback]
		document.body.removeChild(script)

		form.classList.remove('subscribe-loading')

		if (!data) {
			return
		}

		form.classList.add(
			data.result === 'error' ? 'subscribe-error' : 'subscribe-success'
		)

		form.querySelector(
			'.ct-newsletter-subscribe-message'
		).innerHTML = data.msg.replace('0 - ', '')
	}
}

const submitMailerlite = (form) => {
	const body = new FormData(form)

	body.append(
		'action',
		'blc_newsletter_subscribe_process_mailerlite_subscribe'
	)

	body.append('GROUP', form.dataset.provider.split(':')[1])

	form.classList.remove('subscribe-error', 'subscribe-success')
	form.classList.add('subscribe-loading')

	fetch(ct_localizations.ajax_url, {
		method: 'POST',
		body,
	})
		.then((r) => r.json())
		.then(({ success, data }) => {
			form.classList.remove('subscribe-loading')

			form.classList.add(
				data.result === 'no' ? 'subscribe-error' : 'subscribe-success'
			)

			form.querySelector('.ct-newsletter-subscribe-message').innerHTML =
				data.message
		})
}

registerDynamicChunk('blocksy_ext_newsletter_subscribe', {
	mount: (el, { event }) => {
		const form = event.target

		if (form.dataset.provider === 'mailchimp') {
			submitMailchimp(form)
		}

		if (form.dataset.provider.indexOf('mailerlite') > -1) {
			submitMailerlite(form)
		}
	},
})
