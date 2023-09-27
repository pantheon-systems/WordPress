import {
	createElement,
	Component,
	useEffect,
	useState,
	Fragment,
} from '@wordpress/element'
import ctEvents from 'ct-events'

import { OptionsPanel } from 'blocksy-options'
import nanoid from 'nanoid'

import classnames from 'classnames'
import { __, sprintf } from 'ct-i18n'
import Overlay from '../../../../../static/js/helpers/Overlay'

const EditSettings = ({ extsSyncLoading, extensionData, onExtsSync }) => {
	const [isEditing, setIsEditing] = useState(false)
	const [settings, setSettings] = useState(null)

	return (
		<Fragment>
			<button
				className="ct-button ct-config-btn"
				data-button="white"
				onClick={() => {
					setIsEditing(true)
					setSettings(extensionData.settings)
				}}>
				{__('Configure', 'blocksy-companion')}
			</button>

			<Overlay
				items={isEditing}
				onDismiss={() => setIsEditing(false)}
				className={'ct-product-reviews-settings-modal'}
				render={() => (
					<div className={classnames('ct-modal-content')}>
						<h2>{__('Product Reviews Settings', 'blocksy-companion')}</h2>

						<p className="ct-modal-description">
							{__(
								'Configure the slugs for single and category pages of the product review custom post type.',
								'blocksy-companion'
							)}
						</p>

						<div className="ct-controls-group">
							<section data-columns="medium:2">
								<OptionsPanel
									onChange={(optionId, optionValue) =>
										setSettings((settings) => ({
											...settings,
											[optionId]: optionValue,
										}))
									}
									options={{
										single_slug: {
											type: 'text',
											value: '',
											label: __('Single Slug', 'blocksy-companion'),
										},

										category_slug: {
											type: 'text',
											value: '',
											label: __('Category Slug', 'blocksy-companion'),
										},
									}}
									value={settings || {}}
									hasRevertButton={false}
								/>
							</section>
						</div>

						<div className="ct-modal-actions has-divider">
							<button
								className="button-primary"
								disabled={extsSyncLoading || !settings}
								onClick={(e) => {
									e.preventDefault()

									if (!settings) {
										return
									}

									onExtsSync({
										extAction: {
											type: 'persist',
											settings,
										},
									})

									setIsEditing(false)
								}}>
								{extsSyncLoading ? (
									<svg
										width="15"
										height="15"
										viewBox="0 0 100 100">
										<g transform="translate(50,50)">
											<g transform="scale(1)">
												<circle
													cx="0"
													cy="0"
													r="50"
													fill="#687c93"
												/>
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
								) : (
									__('Save', 'blocksy-companion')
								)}
							</button>
						</div>
					</div>
				)}
			/>
		</Fragment>
	)
}

export default EditSettings
