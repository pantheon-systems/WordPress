import { Fragment, createElement, Component } from '@wordpress/element'

const Hidden = ({ option: { text = '', attr = {} } }) => <Fragment />

Hidden.renderingConfig = { design: 'none' }
Hidden.MetaWrapper = () => null

export default Hidden
