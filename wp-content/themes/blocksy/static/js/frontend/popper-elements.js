export const mount = (reference) => {
	if (!reference.nextElementSibling) {
		return
	}

	const target = reference.nextElementSibling

	let placement =
		reference.getBoundingClientRect().left > innerWidth / 2
			? 'left'
			: 'right'

	if (
		reference.getBoundingClientRect().left +
			target.getBoundingClientRect().width >
		innerWidth
	) {
		placement = 'left'
	}

	if (
		reference.getBoundingClientRect().left -
			target.getBoundingClientRect().width <
		0
	) {
		placement = 'right'
	}

	target.dataset.placement = placement
}
