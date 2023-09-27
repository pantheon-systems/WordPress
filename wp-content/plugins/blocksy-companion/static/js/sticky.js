import ctEvents from 'ct-events'
import { registerDynamicChunk } from 'blocksy-frontend'
import { mountStickyHeader } from './frontend/sticky'

if (document.body.className.indexOf('e-preview') > -1) {
	setTimeout(() => {
		mountStickyHeader()
	}, 500)
} else {
	mountStickyHeader()
}

registerDynamicChunk('blocksy_sticky_header', {
	mount: (el) => {},
})
