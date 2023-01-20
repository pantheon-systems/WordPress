import ctEvents from 'ct-events'

let deepLinkLocation = null

export const getDeepLinkPanel = () =>
	deepLinkLocation ? deepLinkLocation.split(':')[1] : false
export const removeDeepLink = () => (deepLinkLocation = null)

if (wp.customize) {
	wp.customize.bind('ready', () => {
		wp.customize.previewer.bind('ct-initiate-deep-link', (location) => {
			const [section, panel] = location.split(':')
			const expanded = Object.values(
				wp.customize.section._value
			).find((e) => e.expanded())

			if (!expanded || expanded.id !== section) {
				deepLinkLocation = location
				wp.customize.section(section).expand()

				return
			}

			ctEvents.trigger('ct-deep-link-start', location)
		})
	})
}
