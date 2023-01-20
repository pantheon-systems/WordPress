import './variables'
import ctEvents from 'ct-events'

const render = () => {
	const notification = document.querySelector('.cookie-notification')

	if (!notification) {
		return
	}

	if (notification.querySelector('.ct-cookies-content')) {
		notification.querySelector('.ct-cookies-content').innerHTML =
			wp.customize('cookie_consent_content')()
	}

	notification.querySelector('button.ct-cookies-accept-button').innerHTML =
		wp.customize('cookie_consent_button_text')()

	const type = wp.customize('cookie_consent_type')()

	notification.dataset.type = type

	notification.firstElementChild.classList.remove('ct-container', 'container')
	notification.firstElementChild.classList.add(
		type === 'type-1' ? 'container' : 'ct-container'
	)
}

wp.customize('cookie_consent_content', (val) =>
	val.bind((to) => {
		render()
	})
)
wp.customize('cookie_consent_button_text', (val) => val.bind((to) => render()))
wp.customize('cookie_consent_type', (val) => val.bind((to) => render()))

wp.customize('forms_cookie_consent_content', (val) =>
	val.bind((to) =>
		[...document.querySelectorAll('.gdpr-confirm-policy label')].map(
			(el) => (el.innerHTML = to)
		)
	)
)
