import {
	createElement,
	Component,
	Fragment,
	memo,
	useMemo,
} from '@wordpress/element'

const Jsx = (props) => {
	const { option } = props

	if (option.render) {
		return option.render(props)
	}

	return null
}

export default Jsx
