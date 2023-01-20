import { createElement, Component } from '@wordpress/element'
import _ from 'underscore'
import classnames from 'classnames'
import InputWithOnlyNumbers from '../components/InputWithOnlyNumbers'

import { clamp, round } from './ct-slider'

const NumberOption = ({
	value,
	option,
	option: { attr, step = 1, markAsAutoFor },
	device,
	onChange,
}) => {
	const parsedValue =
		markAsAutoFor && markAsAutoFor.indexOf(device) > -1 ? 'auto' : value

	const min = !option.min && option.min !== 0 ? -Infinity : option.min
	const max = !option.max && option.max !== 0 ? -Infinity : option.max

	return (
		<div
			className={classnames('ct-option-number', {
				[`ct-reached-limits`]:
					parseFloat(parsedValue) === parseInt(min) ||
					parseFloat(parsedValue) === parseInt(max),
			})}
			{...(attr || {})}>
			<a
				className={classnames('ct-minus', {
					['ct-disabled']: parseFloat(parsedValue) === parseInt(min),
				})}
				onClick={() =>
					onChange(
						round(
							clamp(
								min,
								max,
								parseFloat(parsedValue) - parseFloat(step)
							)
						)
					)
				}
			/>

			<a
				className={classnames('ct-plus', {
					['ct-disabled']: parseFloat(parsedValue) === parseInt(max),
				})}
				onClick={() =>
					onChange(
						round(
							clamp(
								min,
								max,
								parseFloat(parsedValue) + parseFloat(step)
							)
						)
					)
				}
			/>

			<InputWithOnlyNumbers
				value={parsedValue}
				step={step}
				onBlur={() =>
					parseFloat(parsedValue)
						? onChange(round(clamp(min, max, parsedValue)))
						: []
				}
				onChange={(value, can_safely_parse) =>
					can_safely_parse && _.isNumber(parseFloat(value))
						? onChange(round(clamp(min, max, value)))
						: parseFloat(value)
						? onChange(round(Math.min(parseFloat(value), max)))
						: onChange(round(value))
				}
			/>
		</div>
	)
}

export default NumberOption
