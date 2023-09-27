import { render, createElement } from '@wordpress/element'

const getDocument = x =>
	x.document || x.contentDocument || x.contentWindow.document

const buildersMaps = {
	header: {
		panelType: 'header',
		customizerFieldKey: 'header_placements'
	},

	footer: {
		panelType: 'footer',
		customizerFieldKey: 'footer_placements'
	}
}

const openBuilderFor = key => {
	document.querySelector('.ct-panel-builder').dataset.builder =
		buildersMaps[key].panelType

	document.querySelector('.wp-full-overlay').classList.add('ct-show-builder')

	if (buildersMaps[key].panelType === 'footer') {
		document.body.classList.add('ct-footer-builder')
	}
}

const closeBuilderFor = key => {
	document
		.querySelector('.wp-full-overlay')
		.classList.remove('ct-show-builder')
	document.body.classList.remove('ct-footer-builder')
}

export const initBuilder = () => {
	const root = document.createElement('div')
	root.classList.add('ct-panel-builder')

	document.querySelector('.wp-full-overlay').appendChild(root)

	Object.keys(buildersMaps).map(singleKey =>
		(wp.customize.panel(singleKey)
			? wp.customize.panel
			: wp.customize.section)(singleKey, section =>
			section.expanded.bind(value =>
				value ? openBuilderFor(singleKey) : closeBuilderFor(singleKey)
			)
		)
	)
}
