import {
	useRef,
	useState,
	useEffect,
	createElement,
	Fragment,
} from '@wordpress/element'
import { __ } from 'ct-i18n'
import saveAs from './file-saver'
import { Overlay } from 'blocksy-options'

import classnames from 'classnames'

import phpUnserialize from 'phpunserialize'

const CustomizerOptionsManager = () => {
	const [futureConfig, setFutureConfig] = useState(null)
	const [isCopyingOptions, setIsCopyingOptions] = useState(null)
	const [isDraggedOver, setIsDraggerOver] = useState(false)

	const [isExporting, setIsExporting] = useState(false)
	const [dataToExport, setDataToExport] = useState(['options'])

	const inputRef = useRef()

	const dropZoneRef = useRef()

	useEffect(() => {
		const onDragOver = (e) => {
			e.stopPropagation()
			e.preventDefault()
			e.dataTransfer.dropEffect = 'copy'

			setIsDraggerOver(true)
		}

		const onDragLeave = (e) => {
			e.stopPropagation()
			e.preventDefault()
			setIsDraggerOver(false)
		}

		const onDrop = (e) => {
			e.stopPropagation()
			e.preventDefault()

			setIsDraggerOver(false)

			const files = Array.from(e.dataTransfer.files || [])
			const items = Array.from(e.dataTransfer.items || [])

			if (items.length > 0) {
				const futureConfig = e.dataTransfer.items[0].getAsFile()
				setFutureConfig(futureConfig)
			} else {
				if (files.length > 0) {
					setFutureConfig(files[0])
				}
			}
		}

		dropZoneRef.current.addEventListener('dragover', onDragOver, false)
		dropZoneRef.current.addEventListener('dragleave', onDragLeave, false)
		dropZoneRef.current.addEventListener('drop', onDrop, false)

		return () => {
			dropZoneRef.current.removeEventListener(
				'dragover',
				onDragOver,
				false
			)
			dropZoneRef.current.removeEventListener(
				'dragleave',
				onDragLeave,
				false
			)
			dropZoneRef.current.removeEventListener('drop', onDrop, false)
		}
	}, [])

	return (
		<div className="ct-import-export">
			<div className="ct-title" data-type="simple">
				<h3>{__('Export Options', 'blocksy-companion')}</h3>

				<div className="ct-option-description">
					{__(
						'Easily export the theme customizer settings.',
						'blocksy-companion'
					)}
				</div>
			</div>

			<div className="ct-control" data-design="block">
				<header></header>

				<section>
					<button
						className="button"
						onClick={(e) => {
							e.preventDefault()
							setIsExporting(true)
						}}>
						{__('Export Customizations', 'blocksy-companion')}
					</button>
				</section>
			</div>

			<div className="ct-title" data-type="simple">
				<h3>{__('Import Options', 'blocksy-companion')}</h3>

				<div className="ct-option-description">
					{__(
						'Easily import the theme customizer settings.',
						'blocksy-companion'
					)}
				</div>
			</div>

			<div className="ct-control" data-design="block">
				<header></header>

				<section>
					<div className="ct-file-upload">
						<button
							type="button"
							className={classnames('button ct-upload-button', {
								active: isDraggedOver,
							})}
							ref={dropZoneRef}
							onClick={() => {
								inputRef.current.click()
							}}>
							{futureConfig
								? futureConfig.name
								: __(
										'Click or drop to upload a file...',
										'blocksy-companion'
								  )}
						</button>

						<input
							ref={inputRef}
							type="file"
							onChange={({
								target: {
									files: [config],
								},
							}) => {
								setFutureConfig(config)
							}}
						/>

						<button
							className="button"
							onClick={(e) => {
								e.preventDefault()

								if (!futureConfig) {
									return
								}

								var reader = new FileReader()
								reader.readAsText(futureConfig, 'UTF-8')

								reader.onload = function (evt) {
									try {
										fetch(
											`${window.ajaxurl}?action=blocksy_customizer_import&wp_customize=on&nonce=${ct_customizer_localizations.customizer_reset_none}`,
											{
												method: 'POST',
												headers: {
													Accept: 'application/json',
													'Content-Type':
														'application/json',
												},
												body: JSON.stringify(
													phpUnserialize(
														evt.target.result
													)
												),
											}
										).then((response) => {
											if (response.status === 200) {
												response
													.json()
													.then(
														({ success, data }) => {
															location.reload()
														}
													)
											}
										})
									} catch (e) {}
								}
							}}>
							{__('Import Customizations', 'blocksy-companion')}
						</button>
					</div>
				</section>
			</div>

			{ct_customizer_localizations.has_child_theme && (
				<Fragment>
					<div className="ct-title" data-type="simple">
						<h3>{__('Copy Options', 'blocksy-companion')}</h3>

						<div className="ct-option-description">
							{__(
								'Copy and import your customizations from parent or child theme.',
								'blocksy-companion'
							)}
						</div>
					</div>
					{ct_customizer_localizations.is_parent_theme && (
						<div className="ct-control" data-design="block">
							<header></header>

							<section>
								<button
									className="button"
									onClick={(e) => {
										e.preventDefault()
										setIsCopyingOptions('child')
									}}>
									{__(
										'Copy From Child Theme',
										'blocksy-companion'
									)}
								</button>
							</section>
						</div>
					)}

					{!ct_customizer_localizations.is_parent_theme && (
						<div className="ct-control" data-design="block">
							<header></header>

							<section>
								<button
									className="button"
									onClick={(e) => {
										e.preventDefault()
										setIsCopyingOptions('parent')
									}}>
									{__(
										'Copy From Parent Theme',
										'blocksy-companion'
									)}
								</button>
							</section>
						</div>
					)}
				</Fragment>
			)}

			<Overlay
				items={isCopyingOptions}
				className="ct-admin-modal ct-import-export-modal"
				onDismiss={() => setIsCopyingOptions(false)}
				render={() => (
					<div className="ct-modal-content">
						<svg width="35" height="35" viewBox="0 0 66 66">
							<path d="M66 33.1c0 2.8-.4 5.5-1.1 8.2 0 0-1.7-.6-1.9-.6 3.4-13.1-2.2-27.4-14.5-34.5C41.3 2 33 .9 25 3.1c-3.5.9-6.7 2.4-9.5 4.4L20 12 6 15 9 1l5 5c3.1-2.2 6.6-3.9 10.5-4.9 2.7-.7 5.4-1.1 8-1.1 5.9-.1 11.7 1.4 17 4.4C60.1 10.5 66 21.7 66 33.1zm-49 6.3l2.4-3c-.3-1.2-.4-2.3-.4-3.4s.1-2.2.4-3.3l-2.4-3 2.5-4.3 3.8.5c1.6-1.6 3.6-2.7 5.8-3.3l1.4-3.6h5l1.4 3.6c2.2.6 4.2 1.8 5.8 3.3l3.8-.5 2.5 4.3-2.4 3c.3 1.1.4 2.2.4 3.3s-.1 2.2-.4 3.3l2.4 3-2.5 4.3-3.8-.5c-1.6 1.6-3.6 2.7-5.8 3.3L35.4 50h-5L29 46.4c-2.2-.6-4.2-1.8-5.8-3.3l-3.8.5-2.4-4.2zm8-6.4c0 4.4 3.6 8 8 8s8-3.6 8-8-3.6-8-8-8-8 3.6-8 8zm25.9 25.3c-3 2.1-6.3 3.7-9.9 4.7-8 2.1-16.4 1-23.5-3.1C5.2 52.8-.4 38.5 3 25.4c-.7-.1-1.3-.3-2-.5-.7 2.7-1 5.3-1 8 0 11.4 5.9 22.5 16.5 28.6 7.6 4.4 16.5 5.6 25 3.3 4-1.1 7.6-2.8 10.8-5.2l4.6 4.6 3-14-14 3 5 5.1z" />
						</svg>

						<h2 className="ct-modal-title">
							{!ct_customizer_localizations.is_parent_theme &&
								__(
									'Copy From Parent Theme',
									'blocksy-companion'
								)}

							{ct_customizer_localizations.is_parent_theme &&
								__(
									'Copy From Child Theme',
									'blocksy-companion'
								)}
						</h2>
						<p>
							{!ct_customizer_localizations.is_parent_theme &&
								__(
									'You are about to copy all the settings from your parent theme into the child theme. Are you sure you want to continue?',
									'blocksy-companion'
								)}

							{ct_customizer_localizations.is_parent_theme &&
								__(
									'You are about to copy all the settings from your child theme into the parent theme. Are you sure you want to continue?',
									'blocksy-companion'
								)}
						</p>

						<div
							className="ct-modal-actions has-divider"
							data-buttons="2">
							<button
								onClick={(e) => {
									e.preventDefault()
									e.stopPropagation()
									setIsCopyingOptions(false)
								}}
								className="button">
								{__('Cancel', 'blocksy-companion')}
							</button>

							<button
								className="button button-primary"
								onClick={(e) => {
									e.preventDefault()
									const body = new FormData()

									body.append(
										'action',
										'blocksy_customizer_copy_options'
									)
									body.append('wp_customize', 'on')
									body.append('strategy', isCopyingOptions)

									try {
										fetch(window.ajaxurl, {
											method: 'POST',
											body,
										}).then((response) => {
											if (response.status === 200) {
												response
													.json()
													.then(
														({ success, data }) => {
															location.reload()
														}
													)
											}
										})
									} catch (e) {}
								}}>
								{__('Yes, I am sure', 'blocksy-companion')}
							</button>
						</div>
					</div>
				)}
			/>

			<Overlay
				items={isExporting}
				className="ct-admin-modal ct-export-modal"
				onDismiss={() => setIsExporting(false)}
				render={() => (
					<div className="ct-modal-content">
						<h2 className="ct-modal-title">
							{__('Export Settings', 'blocksy-companion')}
						</h2>

						<p>
							{__(
								'Choose what set of settings you want to export.',
								'blocksy-companion'
							)}
						</p>

						<div className="ct-export-options">
							{['options', 'widgets'].map((component) => (
								<div
									className="ct-checkbox-container"
									onClick={() => {
										if (
											dataToExport.length === 1 &&
											dataToExport[0] === component
										) {
											return
										}

										setDataToExport((dataToExport) =>
											dataToExport.includes(component)
												? dataToExport.filter(
														(c) => c !== component
												  )
												: [...dataToExport, component]
										)
									}}>
									{
										{
											options: __(
												'Customizer settings',
												'blocksy-companion'
											),
											widgets: __(
												'Widgets settings',
												'blocksy-companion'
											),
										}[component]
									}
									<span
										className={classnames('ct-checkbox', {
											active: dataToExport.includes(
												component
											),
										})}>
										<svg
											width="10"
											height="8"
											viewBox="0 0 11.2 9.1">
											<polyline
												className="check"
												points="1.2,4.8 4.4,7.9 9.9,1.2 "></polyline>
										</svg>
									</span>
								</div>
							))}
						</div>

						<div
							className="ct-modal-actions has-divider"
							data-buttons="2">
							<button
								onClick={(e) => {
									e.preventDefault()
									e.stopPropagation()
									setIsExporting(false)
								}}
								className="button">
								{__('Cancel', 'blocksy-companion')}
							</button>

							<button
								className="button button-primary"
								onClick={(e) => {
									e.preventDefault()

									const body = new FormData()

									body.append(
										'action',
										'blocksy_customizer_export'
									)

									body.append(
										'strategy',
										dataToExport.join(':')
									)

									body.append('wp_customize', 'on')

									try {
										fetch(window.ajaxurl, {
											method: 'POST',
											body,
										}).then((response) => {
											if (response.status === 200) {
												response
													.json()
													.then(
														({ success, data }) => {
															if (!success) {
																return
															}

															var blob = new Blob(
																[data.data],
																{
																	type:
																		'application/octet-stream;charset=utf-8',
																}
															)

															saveAs(
																blob,
																`${data.site_url
																	.replace(
																		'http://',
																		''
																	)
																	.replace(
																		'https://',
																		''
																	)
																	.replace(
																		'.',
																		'-'
																	)
																	.replace(
																		'/',
																		'-'
																	)}-export.dat`
															)

															setIsExporting(
																false
															)
														}
													)
											}
										})
									} catch (e) {}
								}}>
								{__('Export', 'blocksy-companion')}
							</button>
						</div>
					</div>
				)}
			/>
		</div>
	)
}

export default CustomizerOptionsManager
