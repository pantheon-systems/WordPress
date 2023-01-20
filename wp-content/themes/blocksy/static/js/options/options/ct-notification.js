import { Fragment, createElement, Component } from '@wordpress/element'

const Notification = ({ option: { text = '', attr = {} } }) => (
	<Fragment>
		<div
			className="ct-notification"
			{...{
				...(attr || {}),
			}}
			dangerouslySetInnerHTML={{
				__html: text,
			}}
		/>
	</Fragment>
)

Notification.renderingConfig = { design: 'none' }

export default Notification
