import {
	responsiveClassesFor,
	watchOptionsWithPrefix,
} from 'blocksy-customizer-sync'

import './variables'

wp.customize('newsletter_subscribe_subscribe_visibility', (val) =>
	val.bind((to) => {
		const block = document.querySelector('.ct-newsletter-subscribe-block')
		responsiveClassesFor('newsletter_subscribe_subscribe_visibility', block)
	})
)

if (
	document.body.classList.contains('single') ||
	document.body.classList.contains('page')
) {
	watchOptionsWithPrefix({
		getPrefix: () => '',
		getOptionsForPrefix: () => [
			'newsletter_subscribe_button_text',
			'newsletter_subscribe_title',
			'newsletter_subscribe_text',
			'newsletter_subscribe_name_label',
			'newsletter_subscribe_mail_label',
		],

		render: () => {
			if (
				!document.body.classList.contains('single') &&
				!document.body.classList.contains('page')
			) {
				return
			}

			const block = document.querySelector(
				'.ct-newsletter-subscribe-block'
			)

			if (!block) {
				return
			}

			responsiveClassesFor(
				'newsletter_subscribe_subscribe_visibility',
				block
			)

			if (block.querySelector('[name="FNAME"]')) {
				block
					.querySelector('[name="FNAME"]')
					.setAttribute(
						'placeholder',
						`${wp.customize('newsletter_subscribe_name_label')()}`
					)
			}

			block
				.querySelector('[name="EMAIL"]')
				.setAttribute(
					'placeholder',
					`${wp.customize('newsletter_subscribe_mail_label')()} *`
				)

			block.querySelector('button').innerHTML = wp.customize(
				'newsletter_subscribe_button_text'
			)()

			block.querySelector('h3').innerHTML = wp.customize(
				'newsletter_subscribe_title'
			)()

			block.querySelector(
				'.ct-newsletter-subscribe-description'
			).innerHTML = wp.customize('newsletter_subscribe_text')()
		},
	})
}
