import {
	createElement,
	Component,
	useEffect,
	useState,
	Fragment,
} from '@wordpress/element'
import { __ } from 'ct-i18n'
import classnames from 'classnames'

const SubmitSupport = () => {
	if (ctDashboardLocalizations.plugin_data.hide_support_section) {
		return null
	}

	return (
		<div className="ct-support-container">
			<h2>{__('Need help or advice?', 'blocksy')}</h2>
			<p>
				{__(
					'Got a question or need help with the theme? You can always submit a support ticket or ask for help in our friendly Facebook community.',
					'blocksy'
				)}
			</p>
			<a
				href={ctDashboardLocalizations.support_url}
				className="ct-button"
				data-hover="blue"
				target="_blank">
				{__('Submit a Support Ticket', 'blocksy')}
			</a>

			<a
				href="https://www.facebook.com/groups/blocksy.community"
				className="ct-button"
				data-hover="blue"
				target="_blank">
				{__('Join Facebook Community', 'blocksy')}
			</a>
		</div>
	)
}

export default SubmitSupport
