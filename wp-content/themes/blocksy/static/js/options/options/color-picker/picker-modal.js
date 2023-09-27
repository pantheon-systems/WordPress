import {
	createElement,
	Component,
	useRef,
	useCallback,
	useMemo,
	createRef,
	Fragment,
} from '@wordpress/element'
import ColorPickerIris from './color-picker-iris.js'
import classnames from 'classnames'
import { __ } from 'ct-i18n'

import { nullifyTransforms } from '../../helpers/usePopoverMaker'

export const getNoColorPropFor = (option) =>
	option.noColorTransparent ? 'transparent' : `CT_CSS_SKIP_RULE`

const focusOrOpenCustomizerSectionProps = (section) => ({
	target: '_blank',
	href: `${
		window.ct_localizations ? window.ct_localizations.customizer_url : ''
	}${encodeURIComponent(`[section]=${section}`)}`,
	...(wp && wp.customize && wp.customize.section
		? {
				onClick: (e) => {
					e.preventDefault()
					wp.customize.section(section).expand()
				},
		  }
		: {}),
})

const getLeftForEl = (modal, el) => {
	if (!modal) return
	if (!el) return

	let style = getComputedStyle(modal)

	let wrapperLeft = parseFloat(style.left)

	el = el.firstElementChild.getBoundingClientRect()

	return {
		'--option-modal-arrow-position': `${
			el.left + el.width / 2 - wrapperLeft - 6
		}px`,
	}
}

const PickerModal = ({
	containerRef,
	el,
	value,
	picker,
	onChange,
	option,
	style,
	wrapperProps = {},
	inline_modal,
	appendToBody,
	inheritValue,
}) => {
	const getValueForPicker = useMemo(() => {
		if (value.color === getNoColorPropFor(option)) {
			return { color: '', key: 'empty' }
		}

		if ((value.color || '').indexOf(getNoColorPropFor(option)) > -1) {
			return {
				key: '',
				color: '',
			}
		}

		if (
			(value.color || '').indexOf(getNoColorPropFor(option)) > -1 &&
			picker.inherit
		) {
			return {
				key: 'picker' + inheritValue,
				color: getComputedStyle(document.documentElement)
					.getPropertyValue(
						inheritValue.replace(/var\(/, '').replace(/\)/, '')
					)
					.trim()
					.replace(/\s/g, ''),
			}
		}

		if ((value.color || '').indexOf('var') > -1) {
			return {
				key: 'var' + value.color,
				color: getComputedStyle(document.documentElement)
					.getPropertyValue(
						value.color.replace(/var\(/, '').replace(/\)/, '')
					)
					.trim()
					.replace(/\s/g, ''),
			}
		}

		return { key: 'color', color: value.color }
	}, [value, option, picker, inheritValue])

	let valueToCheck = value.color

	if (
		(value.color || '').indexOf(getNoColorPropFor(option)) > -1 &&
		picker.inherit
	) {
		valueToCheck = inheritValue
	}

	const arrowLeft = useMemo(
		() =>
			wrapperProps.ref &&
			wrapperProps.ref.current &&
			el &&
			getLeftForEl(wrapperProps.ref.current, el.current),
		[wrapperProps.ref && wrapperProps.ref.current, el && el.current]
	)

	return (
		<Fragment>
			<div
				tabIndex="0"
				className={classnames(
					`ct-color-picker-modal`,
					{
						'ct-option-modal': !inline_modal && appendToBody,
					},
					option.modalClassName
				)}
				style={{
					...arrowLeft,
					...(style ? style : {}),
				}}
				{...wrapperProps}>
				{!option.predefined && (
					<div className="ct-color-picker-top">
						<ul className="ct-color-picker-skins">
							{[
								'paletteColor1',
								'paletteColor2',
								'paletteColor3',
								'paletteColor4',
								'paletteColor5',
								'paletteColor6',
								'paletteColor7',
								'paletteColor8',
							].map((color) => (
								<li
									key={color}
									style={{
										background: `var(--${color})`,
									}}
									className={classnames({
										active:
											valueToCheck === `var(--${color})`,
									})}
									onClick={() =>
										onChange({
											...value,
											color: `var(--${color})`,
										})
									}>
									<div className="ct-tooltip-top">
										{
											{
												paletteColor1: __(
													'Color 1',
													'blocksy'
												),
												paletteColor2: __(
													'Color 2',
													'blocksy'
												),
												paletteColor3: __(
													'Color 3',
													'blocksy'
												),
												paletteColor4: __(
													'Color 4',
													'blocksy'
												),
												paletteColor5: __(
													'Color 5',
													'blocksy'
												),
												paletteColor6: __(
													'Color 6',
													'blocksy'
												),
												paletteColor7: __(
													'Color 7',
													'blocksy'
												),
												paletteColor8: __(
													'Color 8',
													'blocksy'
												),
											}[color]
										}
									</div>
								</li>
							))}

							{!option.skipNoColorPill && false && (
								<li
									onClick={() =>
										onChange({
											...value,
											color: getNoColorPropFor(option),
										})
									}
									className={classnames('ct-no-color-pill', {
										active:
											value.color ===
											getNoColorPropFor(option),
									})}>
									<i className="ct-tooltip-top">
										{__('No Color', 'blocksy')}
									</i>
								</li>
							)}
						</ul>
					</div>
				)}

				<ColorPickerIris
					onChange={(v) => onChange(v)}
					value={{
						...value,
						color: getValueForPicker.color,
					}}
				/>
			</div>
		</Fragment>
	)
}

export default PickerModal
