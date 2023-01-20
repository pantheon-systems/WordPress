import { loadPage } from './trending-block'
import { registerDynamicChunk } from 'blocksy-frontend'

registerDynamicChunk('blocksy_ext_trending', {
	mount: (el, { event }) => {
		const loadingEl = el.closest('[data-page]')

		if (el.classList.contains('ct-arrow-left')) {
			loadPage({ el: loadingEl, action: 'prev' })
		}

		if (el.classList.contains('ct-arrow-right')) {
			loadPage({ el: loadingEl, action: 'next' })
		}
	},
})
