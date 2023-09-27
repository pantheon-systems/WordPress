import $script from 'scriptjs'

export const mount = (el, { event }) => {
	if (el.dataset.network === 'pinterest') {
		event.preventDefault()
		if (window.PinUtils) {
			window.PinUtils.pinAny()
		} else {
			$script(
				'https://assets.pinterest.com/js/pinit.js',

				() => {
					// $log.info('Pinterest script loaded.')

					setTimeout(() => {
						window.PinUtils.pinAny()
					}, 300)
				}
			)
		}

		return
	}

	event.preventDefault()

	const url = el.href
	const title = ''
	const w = 600
	const h = 500

	// PopupCenter(el.querySelector('a').href, '', 600, 500)
	// Fixes dual-screen position
	// Most browsers      Firefox
	var dualScreenLeft =
		window.screenLeft != undefined ? window.screenLeft : screen.left
	var dualScreenTop =
		window.screenTop != undefined ? window.screenTop : screen.top

	var width = window.innerWidth
		? window.innerWidth
		: document.documentElement.clientWidth
		? document.documentElement.clientWidth
		: screen.width
	var height = window.innerHeight
		? window.innerHeight
		: document.documentElement.clientHeight
		? document.documentElement.clientHeight
		: screen.height

	var left = width / 2 - w / 2 + dualScreenLeft
	var top = height / 2 - h / 2 + dualScreenTop

	var newWindow = window.open(
		url,
		title,
		'scrollbars=yes, width=' +
			w +
			', height=' +
			h +
			', top=' +
			top +
			', left=' +
			left
	)

	// Puts focus on the newWindow
	if (window.focus) {
		newWindow.focus()
	}
}
