import { createElement, Component, useState } from '@wordpress/element'
import { __ } from 'ct-i18n'
import InputWithOnlyNumbers from '../components/InputWithOnlyNumbers'
import cls from 'classnames'
import OutsideClickHandler from './react-outside-click-handler'

const Spacing = ({ value, option, onChange }) => {
	const [isOpen, setIsOpen] = useState(false)

	const units = [
		{ unit: 'px' },
		{ unit: '%' },
		{ unit: 'em' },
		{ unit: 'rem' },
		{ unit: 'pt' },
	]

	const withDefault = (currentUnit, defaultUnit) =>
		units.find(({ unit }) => unit === currentUnit)
			? currentUnit
			: currentUnit || units[0].unit

	const getLinkedLeader = () =>
		['top', 'right', 'bottom', 'left'].find((v) => value[v] !== 'auto')

	const getCurrentUnit = () =>
		withDefault(
			value[getLinkedLeader()]
				.toString()
				.replace(/[0-9]/g, '')
				.replace('-', '')
				.replace(/\./g, '')
		)

	const getNumericValue = (value, unit = '') => {
		if (value === 'auto') {
			return value
		}

		return `${parseFloat(value) === 0 ? 0 : parseFloat(value) || ''}${unit}`
	}

	const handleChange = (futureValue, position) => {
		if (value.linked) {
			onChange({
				...value,
				top:
					value.top === 'auto'
						? value.top
						: getNumericValue(futureValue, getCurrentUnit()),
				left:
					value.left === 'auto'
						? value.left
						: getNumericValue(futureValue, getCurrentUnit()),
				right:
					value.right === 'auto'
						? value.right
						: getNumericValue(futureValue, getCurrentUnit()),
				bottom:
					value.bottom === 'auto'
						? value.bottom
						: getNumericValue(futureValue, getCurrentUnit()),
			})

			return
		}

		onChange({
			...value,
			[position]: getNumericValue(futureValue, getCurrentUnit()),
		})
	}

	return (
		<div
			className={cls('ct-option-spacing', {
				linked: value.linked,
			})}>
			{['top', 'right', 'bottom', 'left'].map((side) => (
				<span key={side}>
					<InputWithOnlyNumbers
						placeholder=""
						value={getNumericValue(value[side])}
						onChange={(v) => handleChange(v, side)}
						{...{ placeholder: '', ...option.inputAttr }}
					/>

					<small>
						{
							{
								top: __('Top', 'blocksy'),
								bottom: __('Bottom', 'blocksy'),
								left: __('Left', 'blocksy'),
								right: __('Right', 'blocksy'),
							}[side]
						}
					</small>
				</span>
			))}

			<div
				className={cls('ct-spacing-controls ct-value-changer', {
					active: isOpen,
				})}>
				<a
					onClick={(e) => {
						e.preventDefault()

						if (value.linked) {
							onChange({
								...value,
								linked: false,
							})

							return
						}

						const futureValue = value[getLinkedLeader()]

						onChange({
							...value,
							top: value.top !== 'auto' ? futureValue : value.top,
							left:
								value.left !== 'auto'
									? futureValue
									: value.left,
							bottom:
								value.bottom !== 'auto'
									? futureValue
									: value.bottom,
							right:
								value.right !== 'auto'
									? futureValue
									: value.right,

							linked: true,
						})
					}}>
					<svg width="10" height="10" viewBox="0 0 15 15">
						{value.linked ? (
							<path d="M12.2,5.5V4.7c0-2.6-2.1-4.7-4.7-4.7S2.8,2.1,2.8,4.7v0.8c-0.9,0-1.6,0.7-1.6,1.6v6.3c0,0.9,0.7,1.6,1.6,1.6h9.5c0.9,0,1.6-0.7,1.6-1.6V7.1C13.8,6.2,13.1,5.5,12.2,5.5z M10.7,5.5H4.3V4.7c0-1.8,1.4-3.2,3.2-3.2s3.2,1.4,3.2,3.2V5.5z" />
						) : (
							<path d="M12.2,5.5h-1.6H9.9h-5H4.7l0-0.2C4.4,3.5,5.5,1.9,7.3,1.5c1.1-0.2,2.2,0.1,2.8,0.9l1.3-0.9c-1-1.1-2.6-1.8-4.3-1.5C4.5,0.5,2.8,2.9,3.2,5.5H2.8c-0.9,0-1.6,0.7-1.6,1.6v6.3c0,0.9,0.7,1.6,1.6,1.6h9.5c0.9,0,1.6-0.7,1.6-1.6V7.1C13.8,6.2,13.1,5.5,12.2,5.5z" />
						)}
					</svg>
				</a>

				<div
					onClick={() => setIsOpen(!isOpen)}
					className="ct-current-value">
					{getCurrentUnit() || '―'}
				</div>

				<OutsideClickHandler
					className="ct-units-list"
					onOutsideClick={() => {
						if (!isOpen) {
							return
						}

						setIsOpen(false)
					}}>
					{units
						.filter(({ unit }) => unit !== getCurrentUnit())

						.map(({ unit }) => (
							<span
								key={unit}
								data-unit={unit}
								onClick={() => {
									onChange({
										...value,
										top: getNumericValue(value.top, unit),
										left: getNumericValue(value.left, unit),
										right: getNumericValue(
											value.right,
											unit
										),
										bottom: getNumericValue(
											value.bottom,
											unit
										),
									})
									setIsOpen(false)
								}}>
								{unit || '―'}
							</span>
						))}
				</OutsideClickHandler>
			</div>
		</div>
	)
}

export default Spacing
