const renderPassepartout = () => {
	document.body.removeAttribute('data-frame')

	if (wp.customize('has_passepartout')() === 'yes') {
		document.body.dataset.frame = 'default'
	}
}

wp.customize('has_passepartout', (val) =>
	val.bind((to) => {
		renderPassepartout()
	})
)
