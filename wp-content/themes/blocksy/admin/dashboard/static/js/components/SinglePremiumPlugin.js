import { createElement, Component, useState } from '@wordpress/element'
import { __, sprintf } from 'ct-i18n'

const SinglePremiumPlugin = ({ status, plugin, onPluginsSync }) => {
	const [isLoading, setIsLoading] = useState(false)

	const makeAction = async (plugin, actionName) => {
		const body = new FormData()

		body.append('plugin', plugin)
		body.append('action', actionName)

		setIsLoading(true)

		try {
			await fetch(ctDashboardLocalizations.ajax_url, {
				method: 'POST',
				body,
			})

			onPluginsSync()
		} catch (e) {}

		setIsLoading(false)
	}

	return (
		<li>
			<h4 className="ct-extension-title">
				{plugin.title}

				{isLoading && (
					<svg width="15" height="15" viewBox="0 0 100 100">
						<g transform="translate(50,50)">
							<g transform="scale(1)">
								<circle cx="0" cy="0" r="50" fill="#687c93" />
								<circle
									cx="0"
									cy="-26"
									r="12"
									fill="#ffffff"
									transform="rotate(161.634)">
									<animateTransform
										attributeName="transform"
										type="rotate"
										calcMode="linear"
										values="0 0 0;360 0 0"
										keyTimes="0;1"
										dur="1s"
										begin="0s"
										repeatCount="indefinite"
									/>
								</circle>
							</g>
						</g>
					</svg>
				)}
			</h4>

			{plugin.description && (
				<div className="ct-extension-description">
					{plugin.description}
				</div>
			)}

			<div className="ct-extension-actions">
				{status === 'activated' && (
					<a
						onClick={() =>
							makeAction(plugin.name, 'premium_plugin_deactivate')
						}
						className="ct-button">
						{__('Deactivate', 'blocksy')}
					</a>
				)}

				{status === 'deactivated' && (
					<a
						onClick={() =>
							makeAction(plugin.name, 'premium_plugin_activate')
						}
						className="ct-button-primary">
						{__('Activate', 'blocksy')}
					</a>
				)}

				{status === 'uninstalled' &&
					!plugin.comingsoon &&
					plugin.type !== 'link' && (
						<a
							onClick={() =>
								makeAction(
									plugin.name,
									'premium_plugin_download'
								)
							}
							className="ct-button">
							{__('Install', 'blocksy')}
						</a>
					)}

				{status === 'uninstalled' &&
					!plugin.comingsoon &&
					plugin.type === 'link' && (
						<a
							href={plugin.link}
							className="ct-button"
							target="_blank">
							{__('Download', 'blocksy')}
						</a>
					)}

				{status === 'uninstalled' && plugin.comingsoon && (
					<span className="ct-badge">
						{__('COMING SOON', 'blocksy')}
					</span>
				)}
			</div>
		</li>
	)
}

export default SinglePremiumPlugin
