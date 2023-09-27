import { createElement, Component, createRef } from '@wordpress/element'
import { ColorPicker } from '@wordpress/components'
import _ from '_'
import $ from 'jquery'
import { __ } from 'ct-i18n'

import { normalizeColor } from '../../helpers/normalize-color'

const ColorPickerIris = ({ onChange, value, value: { color } }) => {
	const isNew = wp.components.GradientPicker

	return (
		<div
			className={
				isNew
					? 'ct-gutenberg-color-picker-new'
					: 'ct-gutenberg-color-picker'
			}>
			<ColorPicker
				color={color}
				enableAlpha
				{...(isNew
					? {
							onChange: (color) => {
								onChange({
									...value,
									color: normalizeColor(color),
								})
							},
					  }
					: {
							onChangeComplete: (result) => {
								onChange({
									...value,
									color:
										result.rgb.a === 1
											? result.hex
											: `rgba(${result.rgb.r}, ${result.rgb.g}, ${result.rgb.b}, ${result.rgb.a})`,
								})
							},
					  })}
			/>

			<div className="ct-color-picker-value">
				<input
					onChange={({ target: { value: color } }) => {
						onChange({
							...value,
							color: normalizeColor(color),
						})
					}}
					value={normalizeColor(color)}
					type="text"
				/>
			</div>
		</div>
	)
}

export default ColorPickerIris
