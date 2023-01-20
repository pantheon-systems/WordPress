import {
	createElement,
	Component,
	useEffect,
	useState,
	Fragment,
} from '@wordpress/element'
import { __ } from 'ct-i18n'
import classnames from 'classnames'
import useExtensionReadme from '../helpers/useExtensionReadme'
import useActivationAction from '../helpers/useActivationAction'
import fileSaver from 'file-saver'
import Overlay from '../helpers/Overlay'

import { getPluginsMap } from './DemoInstall/Wizzard/Plugins'

const SiteExport = () => {
	const [isLoading, setIsLoading] = useState(false)
	const [isShowing, setIsShowing] = useState(false)

	const [name, setName] = useState('')
	const [builder, setBuilder] = useState('')
	const [plugins, setPlugins] = useState([])
	const [url, setUrl] = useState('')
	const [isPro, setIsPro] = useState(false)

	const downloadExport = async () => {
		setIsLoading(true)

		const body = new FormData()

		body.append('action', 'blocksy_demo_export')
		body.append('name', name)
		body.append('is_pro', isPro)
		body.append('url', url)
		body.append('builder', builder)
		body.append('plugins', plugins.join(','))
		body.append('wp_customize', 'on')

		try {
			const response = await fetch(ctDashboardLocalizations.ajax_url, {
				method: 'POST',
				body,
			})

			if (response.status === 200) {
				const { success, data } = await response.json()

				if (success) {
					var blob = new Blob([JSON.stringify(data.demo)], {
						type: 'text/plain;charset=utf-8',
					})

					fileSaver.saveAs(blob, `${name}.json`)
				}
			}
		} catch (e) {}

		setIsLoading(false)
	}

	if (!ct_localizations.is_dev_mode) {
		return null
	}

	return (
		<div className="ct-export">
			<button
				className="ct-button"
				onClick={(e) => {
					setIsShowing(true)
				}}>
				{__('Site export')}
			</button>

			<Overlay
				items={isShowing}
				className="ct-site-export-modal"
				onDismiss={() => setIsShowing(false)}
				render={() => (
					<div className="ct-site-export">
						<label>
							{__('Name', 'blocksy-companion')}

							<input
								type="text"
								placeholder={__('Name', 'blocksy-companion')}
								value={name}
								onChange={({ target: { value } }) =>
									setName(value)
								}
							/>
						</label>

						<label>
							{__('Preview URL', 'blocksy-companion')}
							<input
								type="text"
								placeholder={__(
									'Preview URL',
									'blocksy-companion'
								)}
								value={url}
								onChange={({ target: { value } }) =>
									setUrl(value)
								}
							/>
						</label>

						<label>
							{__('PRO', 'blocksy-companion')}
							<input
								type="checkbox"
								value={isPro}
								onChange={({ target: { value } }) =>
									setIsPro(!isPro)
								}
							/>
						</label>

						<label>
							{__('Builder', 'blocksy-companion')}
							<input
								type="text"
								placeholder={__('Builder', 'blocksy-companion')}
								value={builder}
								onChange={({ target: { value } }) =>
									setBuilder(value)
								}
							/>
						</label>

						<h3>Required plugins</h3>

						<div className="ct-bundled-plugins-list ct-modal-scroll">
							{Object.keys(getPluginsMap()).map((plugin) => (
								<label
									tabindex="0"
									onClick={(e) => {
										e.preventDefault()

										setPlugins((plugins) => {
											if (plugins.includes(plugin)) {
												return plugins.filter(
													(p) => p !== plugin
												)
											}

											return [...plugins, plugin]
										})
									}}>
									<span>{getPluginsMap()[plugin]}</span>

									<input
										type="checkbox"
										checked={plugins.indexOf(plugin) > -1}
										onChange={({
											target: { checked },
										}) => {}}
									/>
								</label>
							))}
						</div>

						<button
							className="ct-button"
							disabled={isLoading}
							onClick={() => downloadExport()}>
							{isLoading
								? __('Loading...', 'blocksy-companion')
								: __('Export site', 'blocksy-companion')}
						</button>
					</div>
				)}
			/>
		</div>
	)
}

export default SiteExport
