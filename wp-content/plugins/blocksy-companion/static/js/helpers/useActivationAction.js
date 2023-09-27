import {
	createElement,
	Component,
	useEffect,
	useContext,
	useState,
	Fragment,
} from '@wordpress/element'
import { __, sprintf } from 'ct-i18n'
import { Dialog, DialogOverlay, DialogContent } from './reach/dialog'
import Overlay from './Overlay'

import DashboardContext from '../DashboardContext'

const useActivationAction = (extension, cb = () => {}) => {
	const [isLoading, setIsLoading] = useState(false)
	const [isDisplayed, setIsDisplayed] = useState(false)

	const { Link, history } = useContext(DashboardContext)

	const is_pro = ctDashboardLocalizations.plugin_data.is_pro

	const makeAction = async () => {
		if (!is_pro && extension.config.pro) {
			setIsDisplayed(true)

			return
		}

		const body = new FormData()

		body.append('ext', extension.name)
		body.append(
			'action',
			extension.__object
				? 'blocksy_extension_deactivate'
				: 'blocksy_extension_activate'
		)

		setIsLoading(true)

		try {
			await fetch(ctDashboardLocalizations.ajax_url, {
				method: 'POST',
				body,
			})

			if (extension.config.require_refresh) {
				location.reload()
			}

			cb()
		} catch (e) {}

		// await new Promise(r => setTimeout(() => r(), 1000))

		setIsLoading(false)
	}

	return [
		isLoading,
		makeAction,
		!is_pro && extension.config.pro ? (
			<Overlay
				items={isDisplayed}
				className="ct-onboarding-modal"
				onDismiss={() => setIsDisplayed(false)}
				render={() => (
					<div className="ct-modal-content">
						<svg width="55" height="55" viewBox="0 0 40.5 48.3">
							<path
								fill="#2d82c8"
								d="M33.4 29.4l7.1 12.3-7.4.6-4 6-7.3-12.9"
							/>
							<path
								d="M33.5 29.6L26 42.7l-4.2-7.3 11.6-6 .1.2zM0 41.7l7.5.6 3.9 6 7.2-12.4-11-7.3L0 41.7z"
								fill="#2271b1"
							/>
							<path
								d="M39.5 18.7c0 1.6-2.4 2.8-2.7 4.3-.4 1.5 1 3.8.2 5.1-.8 1.3-3.4 1.2-4.5 2.3-1.1 1.1-1 3.7-2.3 4.5-1.3.8-3.6-.6-5.1-.2-1.5.4-2.7 2.7-4.3 2.7S18 35 16.5 34.7c-1.5-.4-3.8 1-5.1.2s-1.2-3.4-2.3-4.5-3.7-1-4.5-2.3.6-3.6.2-5.1-2.7-2.7-2.7-4.3 2.4-2.8 2.7-4.3c.4-1.5-1-3.8-.2-5.1C5.4 8 8.1 8.1 9.1 7c1.1-1.1 1-3.7 2.3-4.5s3.6.6 5.1.2C18 2.4 19.2 0 20.8 0c1.6 0 2.8 2.4 4.3 2.7 1.5.4 3.8-1 5.1-.2 1.3.8 1.2 3.4 2.3 4.5 1.1 1.1 3.7 1 4.5 2.3s-.6 3.6-.2 5.1c.3 1.5 2.7 2.7 2.7 4.3z"
								fill="#599fd9"
							/>
							<path
								d="M23.6 7c-6.4-1.5-12.9 2.5-14.4 8.9-.7 3.1-.2 6.3 1.5 9.1 1.7 2.7 4.3 4.6 7.4 5.4.9.2 1.9.3 2.8.3 2.2 0 4.4-.6 6.3-1.8 2.7-1.7 4.6-4.3 5.4-7.5C34 15 30 8.5 23.6 7zm7 14c-.6 2.6-2.2 4.8-4.5 6.2-2.3 1.4-5 1.8-7.6 1.2-2.6-.6-4.8-2.2-6.2-4.5-1.4-2.3-1.8-5-1.2-7.6.6-2.6 2.2-4.8 4.5-6.2 1.6-1 3.4-1.5 5.2-1.5.8 0 1.5.1 2.3.3 5.4 1.3 8.7 6.7 7.5 12.1zm-8.2-4.5l3.7.5-2.7 2.7.7 3.7-3.4-1.8-3.3 1.8.6-3.7-2.7-2.7 3.8-.5 1.6-3.4 1.7 3.4z"
								fill="#fff"
							/>
						</svg>

						<h2 className="ct-modal-title">
							This is a Pro extension
						</h2>

						<p>
							{__(
								'Upgrade to the Pro version and get instant access to all premium extensions, features and future updates.',
								'blocksy-companion'
							)}
						</p>

						<div
							className="ct-modal-actions has-divider"
							data-buttons="2">
							<a
								onClick={(e) => {
									e.preventDefault()
									setIsDisplayed(false)

									setTimeout(() => {
										history.navigate('/pro')
									}, 300)
								}}
								className="button">
								{__('Free vs Pro', 'blocksy')}
							</a>

							<a
								href="https://creativethemes.com/blocksy/pricing/"
								target="_blank"
								className="button button-primary">
								{__('Upgrade Now', 'blocksy-companion')}
							</a>
						</div>
					</div>
				)}
			/>
		) : null,
	]
}

export default useActivationAction
