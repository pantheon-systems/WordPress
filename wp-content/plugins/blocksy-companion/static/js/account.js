import ctEvents from 'ct-events'
import { loadStyle, registerDynamicChunk } from 'blocksy-frontend'
import { handleAccountModal, activateScreen } from './frontend/account'

let maybeTemplate = ''

registerDynamicChunk('blocksy_account', {
	mount: (el, { event }) => {
		event.preventDefault()

		if (!maybeTemplate) {
			let maybeAccount = document.querySelector('#account-modal')

			if (!maybeAccount) {
				location = document.querySelector('[data-id="account"]')
					? document.querySelector('[data-id="account"]').href
					: el.href

				return
			}

			maybeTemplate = maybeAccount.innerHTML
			maybeAccount.remove()
		}

		let panel = document.querySelector('.ct-drawer-canvas').lastElementChild
		panel.id = `account-modal`

		const maybeMatchingContainer =
			ct_localizations.dynamic_styles_selectors.find((descriptor) =>
				panel.matches(descriptor.selector)
			)

		const actuallyOpen = () => {
			panel.innerHTML = maybeTemplate

			handleAccountModal(panel)

			activateScreen(panel, {
				screen: el.dataset.view || 'login',
			})

			if (window.anr_onloadCallback) {
				window.anr_onloadCallback()
			}

			if (window.Dokan_Vendor_Registration) {
				window.Dokan_Vendor_Registration.init()
			}

			ctEvents.trigger('ct:overlay:handle-click', {
				e: event,
				href: '#account-modal',
				options: {
					openStrategy: 'skip',
					isModal: true,
				},
			})
		}

		if (!maybeMatchingContainer) {
			actuallyOpen()
		} else {
			loadStyle(maybeMatchingContainer.url).then(() => {
				actuallyOpen()
			})
		}
	},
})

ctEvents.on('ct:modal:closed', (modalContainer) => {
	if (!modalContainer.closest('#account-modal')) {
		return
	}

	modalContainer.remove()
})
