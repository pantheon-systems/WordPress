import { Fragment, createElement, Component } from '@wordpress/element'
import classnames from 'classnames'

const Divider = ({ option: { attr: { class: className, ...attr } = {} } }) => (
	<div className={classnames('ct-divider', className)} {...attr} />
)

Divider.renderingConfig = { design: 'none' }

export default Divider
