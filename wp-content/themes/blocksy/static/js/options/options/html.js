import {
	createElement,
	Component,
	Fragment,
	memo,
	useMemo,
} from '@wordpress/element'

const HTML = (props) => {
	const { option } = props

	return (
		<div
			dangerouslySetInnerHTML={{
				__html: option.html || '',
			}}></div>
	)
}

export default HTML
