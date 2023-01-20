import {
	createElement,
	Component,
	useEffect,
	useMemo,
	useState,
	Fragment,
} from '@wordpress/element'
import { __ } from 'ct-i18n'
import classnames from 'classnames'
import useExtensionReadme from '../helpers/useExtensionReadme'
import useActivationAction from '../helpers/useActivationAction'
import { Transition, animated } from 'blocksy-options'
import SubmitSupport from '../helpers/SubmitSupport'
import ctEvents from 'ct-events'

let exts_status_cache = null

const Extension = ({ extension, onExtsSync }) => {
	const [showReadme, readme] = useExtensionReadme(extension)
	const [
		isLoading,
		activationAction,
		activationContent,
	] = useActivationAction(extension, () => {
		onExtsSync()
	})

	return (
		<li className={classnames({ active: !!extension.__object })}>
			<h4 className="ct-extension-title">
				{extension.config.name}

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

			{extension.config.description && (
				<div className="ct-extension-description">
					{extension.config.description}
				</div>
			)}

			<div className="ct-extension-actions">
				<button
					className={classnames(
						extension.__object ? 'ct-button' : 'ct-button-primary'
					)}
					data-hover="white"
					disabled={isLoading}
					onClick={() => {
						activationAction()
					}}>
					{extension.__object
						? __('Deactivate', 'blocksy-companion')
						: __('Activate', 'blocksy-companion')}
				</button>

				{extension.readme && (
					<button
						onClick={() => showReadme()}
						className="ct-minimal-button ct-instruction">
						<svg width="16" height="16" viewBox="0 0 24 24">
							<path d="M12,2C6.477,2,2,6.477,2,12s4.477,10,10,10s10-4.477,10-10S17.523,2,12,2z M12,17L12,17c-0.552,0-1-0.448-1-1v-4 c0-0.552,0.448-1,1-1h0c0.552,0,1,0.448,1,1v4C13,16.552,12.552,17,12,17z M12.5,9h-1C11.224,9,11,8.776,11,8.5v-1 C11,7.224,11.224,7,11.5,7h1C12.776,7,13,7.224,13,7.5v1C13,8.776,12.776,9,12.5,9z" />
						</svg>
					</button>
				)}

				{extension.__object &&
					extension.config &&
					extension.config.buttons &&
					extension.config.buttons.map(({ text, url }, index) => (
						<a
							href={url}
							className="ct-button ct-config-btn"
							dataButton="white">
							{text}
						</a>
					))}
			</div>

			{readme}
			{activationContent}
		</li>
	)
}

const Extensions = () => {
	const [isLoading, setIsLoading] = useState(!exts_status_cache)
	const [exts_status, setExtsStatus] = useState(exts_status_cache || [])
	const [extsSyncLoading, setExtsSyncLoading] = useState(false)

	// free | pro
	const [currentTab, setCurrentTab] = useState('free')

	const syncExts = async (args = {}) => {
		let { verbose, extension, extAction } = {
			verbose: false,
			extension: null,
			extAction: null,
			...args,
		}

		if (verbose) {
			setIsLoading(true)
		}

		setExtsSyncLoading(true)

		const response = await fetch(
			`${wp.ajax.settings.url}?action=blocksy_extensions_status`,

			{
				method: 'POST',
				...(extension && extAction
					? {
							body: JSON.stringify({
								extension,
								extAction,
							}),
					  }
					: {}),
			}
		)

		if (response.status !== 200) {
			return
		}

		const { success, data } = await response.json()

		if (!success) {
			return
		}

		setExtsSyncLoading(false)
		setIsLoading(false)

		setExtsStatus(data)
		exts_status_cache = data

		if (extension) {
			return data[extension]
		}

		return data
	}

	useEffect(() => {
		syncExts({ verbose: !exts_status_cache })

		const cb = () => {
			syncExts()
		}

		ctEvents.on('blocksy_exts_sync_exts', cb)

		return () => {
			ctEvents.off('blocksy_exts_sync_exts', cb)
		}
	}, [])

	const hasProExt = useMemo(
		() =>
			Object.values(exts_status)
				.map((ext, index) => ({
					...ext,
					name: Object.keys(exts_status)[index],
				}))
				.find(({ config }) => config.pro),
		[exts_status]
	)

	const exts = useMemo(
		() =>
			Object.values(exts_status)
				.map((ext, index) => ({
					...ext,
					name: Object.keys(exts_status)[index],
				}))
				.filter(({ config }) => !config.hidden)
				.filter(({ config }) =>
					currentTab === 'free' ? !config.pro : config.pro
				),
		[currentTab, exts_status]
	)

	return (
		<Fragment>
			<div className="ct-extensions-container">
				<Transition
					items={isLoading}
					from={{ opacity: 0 }}
					enter={[{ opacity: 1 }]}
					leave={[{ opacity: 0 }]}
					initial={null}
					config={(key, phase) => {
						return phase === 'leave'
							? {
									duration: 300,
							  }
							: {
									delay: 300,
									duration: 300,
							  }
					}}>
					{(isLoading) => {
						if (isLoading) {
							return (props) => (
								<animated.p
									style={props}
									className="ct-loading-text">
									<span />

									{__(
										'Loading Extensions Status...',
										'blocksy-companion'
									)}
								</animated.p>
							)
						}

						return (props) => (
							<animated.div style={props}>
								<Fragment>
									{hasProExt && (
										<ul className="ct-extensions-sourse">
											{['free', 'pro'].map((plan) => (
												<li
													key={plan}
													onClick={() =>
														setCurrentTab(plan)
													}
													className={classnames({
														active:
															plan === currentTab,
													})}>
													{
														{
															free: __(
																'Free Extensions',
																'blocksy-companion'
															),
															pro: __(
																'Pro Extensions',
																'blocksy-companion'
															),
														}[plan]
													}
												</li>
											))}
										</ul>
									)}

									<ul
										className={classnames(
											'ct-extensions-list',
											{
												'is-pro': currentTab === 'pro',
											}
										)}>
										{exts.map((ext) => {
											let CustomComponent = {
												extension: Extension,
											}

											ctEvents.trigger(
												'ct:extensions:card',
												{
													CustomComponent,
													extension: ext,
												}
											)

											return (
												<CustomComponent.extension
													key={ext.name}
													extension={ext}
													extsSyncLoading={
														extsSyncLoading
													}
													onExtsSync={(
														payload = {}
													) => {
														if (
															!payload.extAction
														) {
															exts_status[
																ext.name
															].__object = !exts_status[
																ext.name
															].__object

															setExtsStatus(
																exts_status
															)
														}

														return syncExts({
															...payload,
															extension: ext.name,
														})
													}}
												/>
											)
										})}
									</ul>

									<SubmitSupport />
								</Fragment>
							</animated.div>
						)
					}}
				</Transition>
			</div>
		</Fragment>
	)
}

export default Extensions
