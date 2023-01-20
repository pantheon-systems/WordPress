import {
	createElement,
	Component,
	useState,
	useRef,
	useContext,
	Fragment,
} from '@wordpress/element'
import OutsideClickHandler from './react-outside-click-handler'
import classnames from 'classnames'
import ColorPicker from './ct-color-picker'

import { __ } from 'ct-i18n'

const clamp = (min, max, value) => Math.max(min, Math.min(max, value))

const Border = ({ value, option, onChange }) => {
	const [isOpen, setIsOpen] = useState(false)

	return (
		<div className={classnames('ct-option-border')}>
			<div
				className={classnames('ct-value-changer', {
					['active']: isOpen,
				})}>
				{value.style !== 'none' && !value.inherit && (
					<input
						type="number"
						value={value.width}
						// disabled={value.style === 'none'}
						onChange={({ target: { value: width } }) =>
							onChange({
								...value,
								width: clamp(1, 5, parseInt(width, 10) || 1),
							})
						}
					/>
				)}

				<span
					className="ct-current-value"
					data-style={value.inherit ? 'none' : value.style}
					onClick={() => setIsOpen(!isOpen)}>
					{value.inherit
						? __('Inherit', 'blocksy')
						: value.style === 'none'
						? __('none', 'blocksy')
						: null}
				</span>
				<OutsideClickHandler
					className="ct-units-list"
					disabled={!isOpen}
					onOutsideClick={() => {
						if (!isOpen) return
						setIsOpen(false)
					}}>
					{['solid', 'dashed', 'dotted', 'none'].map((style) => (
						<span
							key={style}
							onClick={() => {
								onChange({
									...value,
									style,
									...(Object.keys(option.value).indexOf(
										'inherit'
									) > -1
										? {
												inherit: false,
										  }
										: {}),
								})
								setIsOpen(false)
							}}
							data-style={style}>
							{style === 'none' ? __('None', 'blocksy') : null}
						</span>
					))}
				</OutsideClickHandler>
			</div>

			{value.style !== 'none' && !value.inherit && (
				<Fragment>
					<ColorPicker
						onChange={(colorValue) =>
							onChange({
								...value,
								color: colorValue.default,
							})
						}
						option={{
							pickers: [
								{
									id: 'default',
									title: __('Initial', 'blocksy'),
								},
							],
						}}
						value={{
							default: value.color,
						}}
					/>

					{option.secondColor && (
						<ColorPicker
							onChange={(colorValue) =>
								onChange({
									...value,
									secondColor: colorValue.default,
								})
							}
							option={{
								pickers: [
									{
										id: 'default',
										title: __('Hover', 'blocksy'),
									},
								],
							}}
							value={{
								default:
									value.secondColor ||
									option.value.secondColor,
							}}
						/>
					)}
				</Fragment>
			)}
		</div>
	)
}

export default Border
