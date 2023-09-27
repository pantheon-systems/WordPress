import {
	createElement,
	Component,
	useEffect,
	useState,
	Fragment,
} from '@wordpress/element'
import { __ } from 'ct-i18n'
import classnames from 'classnames'

import EditSettings from './EditSettings'
import useExtensionReadme from '../../../../../static/js/helpers/useExtensionReadme'
import useActivationAction from '../../../../../static/js/helpers/useActivationAction'

const ProductReviews = ({ extsSyncLoading, extension, onExtsSync }) => {
	const [isLoading, activationAction] = useActivationAction(extension, () =>
		onExtsSync()
	)

	const [showReadme, readme] = useExtensionReadme(extension)

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
					data-button="white"
					disabled={isLoading}
					onClick={() => {
						activationAction()
					}}>
					{extension.__object
						? __('Deactivate', 'blocksy-companion')
						: __('Activate', 'blocksy-companion')}
				</button>

				{extension.__object && (
					<EditSettings
						extsSyncLoading={extsSyncLoading}
						extensionData={extension.data}
						onExtsSync={onExtsSync}
					/>
				)}

				{extension.readme && (
					<button
						onClick={() => showReadme()}
						data-button="white"
						className="ct-minimal-button ct-instruction">
						<svg width="16" height="16" viewBox="0 0 24 24">
							<path d="M12,2C6.477,2,2,6.477,2,12s4.477,10,10,10s10-4.477,10-10S17.523,2,12,2z M12,17L12,17c-0.552,0-1-0.448-1-1v-4 c0-0.552,0.448-1,1-1h0c0.552,0,1,0.448,1,1v4C13,16.552,12.552,17,12,17z M12.5,9h-1C11.224,9,11,8.776,11,8.5v-1 C11,7.224,11.224,7,11.5,7h1C12.776,7,13,7.224,13,7.5v1C13,8.776,12.776,9,12.5,9z" />
						</svg>
					</button>
				)}
			</div>
			{readme}
		</li>
	)
}

export default ProductReviews
