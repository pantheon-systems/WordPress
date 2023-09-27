import {
	createElement,
	Component,
	createRef,
	useState,
	useEffect,
	useRef,
} from '@wordpress/element'
import { __, sprintf } from 'ct-i18n'
import $ from 'jquery'

const Notification = ({ initialStatus, url, pluginUrl, pluginLink }) => {
	const [pluginStatus, setPluginStatus] = useState('installed')

	const [isLoading, setIsLoading] = useState(false)

	const containerEl = useRef(null)

	useEffect(() => {
		setPluginStatus(initialStatus)
	}, [])

	return (
		<div className="ct-blocksy-plugin-inner" ref={containerEl}>
			<button
				onClick={() => {
					containerEl.current
						.closest('.notice-blocksy-plugin')
						.parentNode.removeChild(
							containerEl.current.closest(
								'.notice-blocksy-plugin'
							)
						)

					$.ajax(ajaxurl, {
						type: 'POST',
						data: {
							action: 'blocksy_dismissed_notice_handler',
						},
					})
				}}
				type="button"
				className="notice-dismiss">
				<span className="screen-reader-text">
					{__('Dismiss this notice.', 'blocksy')}
				</span>
			</button>

			<span className="ct-notification-icon">
				<svg
					width="50"
					height="50"
					viewBox="0 0 50 50"
					xmlns="http://www.w3.org/2000/svg">
					<path
						d="M25 0c13.807 0 25 11.193 25 25S38.807 50 25 50 0 38.807 0 25 11.193 0 25 0zm4.735 25.637a.237.237 0 00-.312 0L19.28 34.83c-.069.063-.02.171.078.171h9.492c.116 0 .229-.042.312-.117l4.45-4.035a1.122 1.122 0 000-1.697zm0-10a.237.237 0 00-.312 0L18.13 25.873a.382.382 0 00-.129.282v7.613c0 .09.119.134.188.071l14.636-13.333c.517-.468.518-1.589 0-2.057zM27.674 15H18.22c-.122 0-.221.09-.221.2v8.568c0 .09.119.134.188.071l9.564-8.668c.07-.063.02-.171-.078-.171z"
						fill="#23282D"
						fillRule="evenodd"
					/>
				</svg>
			</span>

			<div className="ct-notification-content">
				<h2>
					{__('Thanks for installing Blocksy, you rock!', 'blocksy')}
				</h2>

				<p
					dangerouslySetInnerHTML={{
						__html: __(
							'We strongly recommend you to activate the <b>Blocksy Companion</b> plugin.<br>This way you will have access to custom extensions, demo templates and many other awesome features.',
							'blocksy'
						),
					}}
				/>

				<div className="notice-actions">
					{null && pluginStatus === 'uninstalled' && (
						<a
							className="button button-primary"
							href={pluginLink}
							target="_blank">
							{__('Download Blocksy Companion', 'blocksy')}
						</a>
					)}

					<button
						className="button button-primary"
						disabled={isLoading || pluginStatus === 'active'}
						onClick={() => {
							setIsLoading(true)

							setTimeout(() => {})
							$.ajax(ajaxurl, {
								type: 'POST',
								data: {
									action: 'blocksy_notice_button_click',
								},
							}).then(({ success, data }) => {
								if (success) {
									setPluginStatus(data.status)

									if (data.status === 'active') {
										location.assign(pluginUrl)
									}
								}

								setIsLoading(false)
							})
						}}>
						{isLoading
							? __('Installing & activating...', 'blocksy')
							: pluginStatus === 'uninstalled'
							? __('Install Blocksy Companion', 'blocksy')
							: pluginStatus === 'installed'
							? __('Activate Blocksy Companion', 'blocksy')
							: __('Blocksy Companion active!', 'blocksy')}
						{isLoading && (
							<i className="dashicons dashicons-update" />
						)}
					</button>

					<a
						className="ct-why-button button"
						href={'https://creativethemes.com/blocksy/companion/'}>
						{__('Why you need Blocksy Companion?', 'blocksy')}
					</a>
				</div>
			</div>
		</div>
	)
}

export default Notification
