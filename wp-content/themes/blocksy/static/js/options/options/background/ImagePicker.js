import {
	Fragment,
	createElement,
	Component,
	useRef,
	useEffect,
	useMemo,
	useCallback,
	useState,
} from '@wordpress/element'
import classnames from 'classnames'
import { __ } from 'ct-i18n'
import GenericOptionType from '../../GenericOptionType'

const ImagePicker = ({ option, value, onChange, setOutsideClickFreezed }) => {
	return (
		<Fragment>
			<GenericOptionType
				value={value['background_image']}
				values={value}
				option={{
					id: 'background_image',
					label: false,
					type: 'ct-image-uploader',
					value: option.value['background_image'],
					has_position_picker: true,
					emptyLabel: __('Select Image', 'blocksy'),
					filledLabel: __('Change Image', 'blocksy'),
					onFrameOpen: () => {
						setOutsideClickFreezed(true)
					},

					onFrameClose: () => {
						setOutsideClickFreezed(false)
					},
				}}
				hasRevertButton={false}
				onChange={(newValue) =>
					onChange({
						...value,
						background_image: newValue,
					})
				}
			/>

			<GenericOptionType
				value={value['background_repeat']}
				values={value}
				option={{
					id: 'background_repeat',
					label: __('Background Repeat', 'blocksy'),
					attr: { 'data-type': 'repeat' },
					type: 'ct-radio',
					view: 'text',
					design: 'block',
					value: option.value['background_repeat'],
					choices: {
						repeat: `<svg viewBox="0 0 16 16"><path d="M0,0h4v4H0V0z M6,0h4v4H6V0z M12,0h4v4h-4V0z M0,6h4v4H0V6z M6,6h4v4H6V6z M12,6h4v4h-4V6z M0,12h4v4H0V12z M6,12h4v4H6V12zM12,12h4v4h-4V12z"/></svg>
							<span class="ct-tooltip-top">${__('Repeat', 'blocksy')}</span>`,

						'repeat-y': `<svg viewBox="0 0 16 16"><rect x="6" width="4" height="4"/><rect x="6" y="6" width="4" height="4"/><rect x="6" y="12" width="4" height="4"/></svg>
							<span class="ct-tooltip-top">${__('Repeat Y', 'blocksy')}</span>`,

						'repeat-x': `<svg viewBox="0 0 16 16"><rect y="6" width="4" height="4"/><rect x="6" y="6" width="4" height="4"/><rect x="12" y="6" width="4" height="4"/></svg>
							<span class="ct-tooltip-top">${__('Repeat X', 'blocksy')}</span>`,

						'no-repeat': `<svg viewBox="0 0 16 16"><rect x="6" y="6" width="4" height="4"/></svg>
							<span class="ct-tooltip-top">${__('No Repeat', 'blocksy')}</span>`,
					},
				}}
				hasRevertButton={false}
				onChange={(newValue) =>
					onChange({
						...value,
						background_repeat: newValue,
					})
				}
			/>

			<GenericOptionType
				value={value['background_size']}
				values={value}
				option={{
					id: 'background_size',
					label: __('Background Size', 'blocksy'),
					type: 'ct-radio',
					view: 'text',
					design: 'block',
					value: option.value['background_size'],
					choices: {
						auto: __('Auto', 'blocksy'),
						cover: __('Cover', 'blocksy'),
						contain: __('Contain', 'blocksy'),
					},
				}}
				hasRevertButton={false}
				onChange={(newValue) =>
					onChange({
						...value,
						background_size: newValue,
					})
				}
			/>

			<GenericOptionType
				value={value['background_attachment']}
				values={value}
				option={{
					id: 'background_size',
					label: __('Background Attachment', 'blocksy'),
					type: 'ct-radio',
					view: 'text',
					design: 'block',
					value: option.value['background_attachment'],
					choices: {
						scroll: __('Scroll', 'blocksy'),
						fixed: __('Fixed', 'blocksy'),
						inherit: __('Inherit', 'blocksy'),
					},
				}}
				hasRevertButton={false}
				onChange={(newValue) =>
					onChange({
						...value,
						background_attachment: newValue,
					})
				}
			/>

			{value.background_image.url && (
				<Fragment>
					<GenericOptionType
						value={
							value.overlayColor &&
							value.overlayColor.default.color.indexOf(
								'CT_CSS_SKIP_RULE'
							) === -1
								? 'yes'
								: 'no'
						}
						values={{}}
						option={{
							id: 'has_overlay',
							label: __('Image Overlay', 'blocksy'),
							type: 'ct-radio',
							view: 'text',
							design: 'block',
							value: 'no',
							choices: {
								no: __('Disabled', 'blocksy'),
								yes: __('Enabled', 'blocksy'),
							},
						}}
						hasRevertButton={false}
						onChange={(newValue) => {
							let hasOverlay =
								value.overlayColor.default.color.indexOf(
									'CT_CSS_SKIP_RULE'
								) === -1

							onChange({
								...value,
								overlayColor: {
									default: {
										color: hasOverlay
											? 'CT_CSS_SKIP_RULE'
											: 'rgba(0, 0, 0, 0.2)',
									},
								},
							})
						}}
					/>

					{value.overlayColor &&
						value.overlayColor.default.color.indexOf(
							'CT_CSS_SKIP_RULE'
						) === -1 && (
							<GenericOptionType
								value={value['overlayColor']}
								values={value}
								option={{
									id: 'overlayColor',
									label: __('Image Overlay Color', 'blocksy'),
									type: 'ct-color-picker',
									design: 'inline',
									value: option.value['overlayColor'],
									pickers: [
										{
											title: __('Initial', 'blocksy'),
											id: 'default',
										},
									],
									skipArrow: true,
									appendToBody: false,
								}}
								hasRevertButton={false}
								onChange={(newValue) =>
									onChange({
										...value,
										overlayColor: newValue,
									})
								}
							/>
						)}
				</Fragment>
			)}
		</Fragment>
	)
}

export default ImagePicker
