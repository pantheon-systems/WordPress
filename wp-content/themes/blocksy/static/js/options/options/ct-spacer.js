import { Fragment, createElement, Component } from '@wordpress/element'
import classnames from 'classnames'

const Spacer = ({
	option: { height = 10, attr: { class: className, ...attr } = {} },
}) => (
	<div
		className={classnames('ct-spacer', className)}
		{...attr}
		style={{
			height: `${height}px`,
		}}
	/>
)

Spacer.renderingConfig = { design: 'none' }

export default Spacer
