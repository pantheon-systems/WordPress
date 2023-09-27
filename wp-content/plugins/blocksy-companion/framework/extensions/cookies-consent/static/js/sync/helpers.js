export const responsiveClassesFor = (id, el) => {
	el.classList.remove('ct-hidden-sm', 'ct-hidden-md', 'ct-hidden-lg')

	if (!wp.customize(id)) return

	const data = wp.customize(id)() || {
		mobile: false,
		tablet: true,
		desktop: true,
	}

	if (!data.mobile) {
		el.classList.add('ct-hidden-sm')
	}

	if (!data.tablet) {
		el.classList.add('ct-hidden-md')
	}

	if (!data.desktop) {
		el.classList.add('ct-hidden-lg')
	}
}
