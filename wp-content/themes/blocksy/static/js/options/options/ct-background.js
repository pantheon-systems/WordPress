import {
	Fragment,
	createElement,
	Component,
	useRef,
	useEffect,
	useMemo,
	useCallback,
	createPortal,
	useState,
} from '@wordpress/element'
import cls from 'classnames'
import BackgroundModal from './background/BackgroundModal'
import OutsideClickHandler from './react-outside-click-handler'
import { getUrlForPattern } from './background/PatternPicker'
import { __ } from 'ct-i18n'

import usePopoverMaker from '../helpers/usePopoverMaker'

/**
 * Support color picker values inside the background picker.
 * Which means transitions from ct-color-picker are made possible thanks to
 * this logic.
 */
const maybeConvertFromColorPickerTo = (value) => {
	if (!value.background_type) {
		if (value[Object.keys(value)[0]].color) {
			return {
				background_type: 'color',
				background_pattern: 'type-1',
				background_image: {
					attachment_id: null,
					x: 0,
					y: 0,
				},

				background_repeat: 'no-repeat',
				background_size: 'auto',
				background_attachment: 'scroll',

				patternColor: {
					default: {
						color: '#e5e7ea',
					},
				},

				backgroundColor: {
					default: value[Object.keys(value)[0]],
				},
			}
		}
	}

	return value
}

const Background = ({ option, value, onChange }) => {
	const [isOpen, setIsOpen] = useState(false)
	const [outsideClickFreezed, setOutsideClickFreezed] = useState(false)
	const backgroundWrapper = useRef()

	value = maybeConvertFromColorPickerTo(value)

	const isInherit =
		!option.has_no_color &&
		value.background_type === 'color' &&
		(value.backgroundColor.default.color === 'CT_CSS_SKIP_RULE' ||
			value.backgroundColor.default.color === 'transparent')

	const isNoColor =
		option.has_no_color &&
		value.background_type === 'color' &&
		(value.backgroundColor.default.color === 'CT_CSS_SKIP_RULE' ||
			value.backgroundColor.default.color === 'transparent')

	const { styles, popoverProps } = usePopoverMaker({
		ref: backgroundWrapper,
		defaultHeight: 434,
		shouldCalculate: backgroundWrapper && backgroundWrapper.current,
	})

	return (
		<div
			ref={backgroundWrapper}
			className={cls('ct-background', {
				active: isOpen,
			})}>
			<div
				className={cls('ct-background-preview', {
					'ct-color-inherit': isInherit,
					'ct-no-color': isNoColor,
				})}
				onClick={(e) => {
					e.preventDefault()
					setIsOpen(!isOpen)

					if (value.background_type === 'color') {
						if (
							value.backgroundColor.default.color ===
								'CT_CSS_SKIP_RULE' ||
							value.backgroundColor.default.color ===
								'transparent'
						) {
							onChange({
								...value,
								backgroundColor: {
									default: {
										color:
											option.default_inherit_color ||
											'#ffffff',
									},
								},
							})
						}
					}
				}}
				data-background-type={value.background_type}
				style={{
					...(value.backgroundColor.default.color.indexOf(
						'CT_CSS_SKIP_RULE'
					) > -1
						? {}
						: {
								backgroundColor:
									value.backgroundColor.default.color,
						  }),

					'--background-position': `${Math.round(
						parseFloat(value.background_image.x) * 100
					)}% ${Math.round(
						parseFloat(value.background_image.y) * 100
					)}%`,

					'--pattern-mask':
						value.background_type === 'pattern'
							? `url(${getUrlForPattern(
									value.background_pattern
							  )})`
							: '',

					'--background-image':
						value.background_type === 'gradient'
							? value.gradient
							: value.background_image.url
							? `${
									value.overlayColor &&
									value.overlayColor.default.color.indexOf(
										'CT_CSS_SKIP_RULE'
									) === -1
										? `linear-gradient(${value.overlayColor.default.color}, ${value.overlayColor.default.color}), `
										: ''
							  }url(${value.background_image.url})`
							: 'none',
					'--pattern-color': value.patternColor.default.color,
				}}>
				<i className="ct-tooltip-top">
					{
						{
							inherit: __('Inherited', 'blocksy'),
							no_color: __('No Color', 'blocksy'),
							pattern: __('Pattern', 'blocksy'),
							gradient: __('Gradient', 'blocksy'),
							color: __('Color', 'blocksy'),
							image: __('Image', 'blocksy'),
						}[
							isNoColor
								? 'no_color'
								: isInherit
								? 'inherit'
								: value.background_type
						]
					}
				</i>

				{isInherit && (
					<svg width="25" height="25" viewBox="0 0 30 30">
						<path d="M15 3c-3 0-5.7 1.1-7.8 2.9-.4.3-.5.9-.2 1.4.3.4 1 .5 1.4.2h.1C10.3 5.9 12.5 5 15 5c5.2 0 9.5 3.9 10 9h-3l4 6 4-6h-3.1C26.4 7.9 21.3 3 15 3zM4 10l-4 6h3.1c.5 6.1 5.6 11 11.9 11 3 0 5.7-1.1 7.8-2.9.4-.3.5-1 .2-1.4-.3-.4-1-.5-1.4-.2h-.1c-1.7 1.5-4 2.4-6.5 2.4-5.2 0-9.5-3.9-10-9h3L4 10z" />
					</svg>
				)}
			</div>

			{backgroundWrapper &&
				backgroundWrapper.current &&
				createPortal(
					<OutsideClickHandler
						useCapture={false}
						display="block"
						disabled={!isOpen || outsideClickFreezed}
						onOutsideClick={() => {
							setTimeout(() => setIsOpen(false))
						}}
						wrapperProps={{
							style: styles,
							...popoverProps,
							className: cls(
								'ct-option-modal ct-background-modal',
								{
									active: isOpen,
								}
							),
						}}>
						<BackgroundModal
							onChange={onChange}
							value={value}
							option={option}
							isOpen={isOpen}
							setOutsideClickFreezed={setOutsideClickFreezed}
						/>
					</OutsideClickHandler>,
					document.body
				)}
		</div>
	)
}

export default Background
