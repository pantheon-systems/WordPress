import ctEvents from 'ct-events'
import { registerDynamicChunk } from 'blocksy-frontend'

registerDynamicChunk('blocksy_dark_mode', {
	mount: (el, { event }) => {
		event.preventDefault()

		if (document.querySelector('html').dataset.palette) {
			document.querySelector('html').removeAttribute('data-palette')
			return
		}

		document.querySelector('html').dataset.palette = 'dark'
	},
})
