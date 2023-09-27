import { Fragment, createElement, Component } from '@wordpress/element'

const Button = ({ option: { text = '', attr = {}, panel, url } }) => (
	<a
		{...{
			...(attr || {})
		}}
		href={url}
		target="_blank"
		onClick={e => {
			return
			e.preventDefault()

			if (panel && wp.customize && wp.customize.panel(panel)) {
				wp.customize.panel(panel).expand()
				return
			}

			url && location.assign(url)
		}}>
		{text}
	</a>
)

export default Button
