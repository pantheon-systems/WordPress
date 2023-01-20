import { createElement, render } from '@wordpress/element'
import Notification from './Notification'
import $ from 'jquery'

export const mount = (el) => {
	if (el.querySelector('.notice-blocksy-plugin-root')) {
		render(
			<Notification
				initialStatus={
					el.querySelector('.notice-blocksy-plugin-root').dataset
						.pluginStatus
				}
				url={
					el.querySelector('.notice-blocksy-plugin-root').dataset.url
				}
				pluginUrl={
					el.querySelector('.notice-blocksy-plugin-root').dataset
						.pluginUrl
				}
				pluginLink={
					el.querySelector('.notice-blocksy-plugin-root').dataset.link
				}
			/>,
			el.querySelector('.notice-blocksy-plugin-root')
		)
	}

	;[...document.querySelectorAll('[data-dismiss]')].map((el) => {
		el.addEventListener('click', (e) => {
			e.preventDefault()

			el.closest('.notice-blocksy-woo-deprecation').remove()

			$.ajax(ajaxurl, {
				type: 'POST',
				data: {
					action: 'blocksy_dismissed_notice_woo_deprecation',
				},
			})
		})
	})
}
