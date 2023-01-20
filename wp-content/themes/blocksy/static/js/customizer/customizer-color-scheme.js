export const listenToChanges = () => {
	const renderValue = () => {
		if (!wp.customize._value['customizer_color_scheme']) {
			return
		}

		if (!document.body) {
			return
		}

		const overlay = document.querySelector('.wp-full-overlay')
		document.body.classList.remove('ct-dark-mode')

		overlay.classList.add('ct-disable-transitions')

		setTimeout(
			() => overlay.classList.remove('ct-disable-transitions'),
			500
		)

		if (wp.customize._value['customizer_color_scheme']() === 'yes') {
			document.body.classList.add('ct-dark-mode')
		}
	}

	wp.customize.bind('change', (e) => {
		if (e.id !== 'customizer_color_scheme') return
		renderValue()
	})

	wp.customize.bind('ready', () => renderValue())
}
