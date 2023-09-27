import {
	Fragment,
	createElement,
	Component,
	useRef,
	useEffect,
	useState,
} from '@wordpress/element'
import classnames from 'classnames'
import { __ } from 'ct-i18n'

import { animated } from '@react-spring/web'

import GenericOptionType from '../../GenericOptionType'

const FontOptions = ({ option, value, sizeRef, onChange, props }) => {
	return (
		<animated.ul
			style={props}
			className="ct-typography-options"
			key="options">
			<li key="size">
				<GenericOptionType
					value={value.size}
					values={value}
					id="size"
					option={{
						id: 'size',
						label: __('Font Size', 'blocksy'),
						type: 'ct-slider',
						value: option.value.size,
						ref: sizeRef,
						responsive: option.typography_responsive || true,
						units: [
							{
								unit: 'px',
								min: 0,
								max: 200,
							},

							{
								unit: 'em',
								min: 0,
								max: 50,
							},

							{
								unit: 'rem',
								min: 0,
								max: 50,
							},

							{
								unit: 'pt',
								min: 0,
								max: 50,
							},

							{
								unit: 'vw',
								min: 0,
								max: 100,
							},

							{
								unit: '',
								type: 'custom',
							},
						],
					}}
					hasRevertButton={true}
					onChange={(newValue) =>
						onChange({
							...value,
							size: newValue,
						})
					}
				/>
			</li>

			<li key="line-height">
				<GenericOptionType
					value={value['line-height']}
					values={value}
					id="line-height"
					option={{
						id: 'line-height',
						label: __('Line Height', 'blocksy'),
						type: 'ct-slider',
						value: option.value['line-height'],
						responsive: option.typography_responsive || true,
						units: [
							{
								unit: '',
								min: 0,
								max: 10,
								decimals: 1,
							},

							{
								unit: 'px',
								min: 0,
								max: 100,
							},

							{
								unit: 'em',
								min: 0,
								max: 100,
							},

							{
								unit: 'pt',
								min: 0,
								max: 100,
							},

							{
								unit: '%',
								min: 0,
								max: 100,
							},
						],
					}}
					hasRevertButton={true}
					onChange={(newValue) =>
						onChange({
							...value,
							'line-height': newValue,
						})
					}
				/>
			</li>

			<li key="letter-spacing">
				<GenericOptionType
					value={value['letter-spacing']}
					values={value}
					id="letter-spacing"
					option={{
						id: 'letter-spacing',
						label: __('Letter Spacing', 'blocksy'),
						type: 'ct-slider',
						value: option.value['letter-spacing'],
						responsive: option.typography_responsive || true,
						defaultPosition: 'center',
						units: [
							{
								unit: 'em',
								min: -5,
								max: 5,
								decimals: 1,
							},

							{
								unit: 'px',
								min: -20,
								max: 20,
								decimals: 1,
							},

							{
								unit: 'rem',
								min: -5,
								max: 5,
								decimals: 1,
							},
						],
					}}
					hasRevertButton={true}
					onChange={(newValue) =>
						onChange({
							...value,
							'letter-spacing': newValue,
						})
					}
				/>
			</li>

			<li key="variant" className="ct-typography-variant">
				<ul className={classnames('ct-text-transform')}>
					{['capitalize', 'uppercase'].map((variant) => (
						<li
							key={variant}
							onClick={() =>
								onChange({
									...value,
									'text-transform':
										value['text-transform'] === variant
											? 'none'
											: variant,
								})
							}
							className={classnames({
								active: variant === value['text-transform'],
							})}
							data-variant={variant}>
							<i className="ct-tooltip-top">
								{
									{
										capitalize: __('Capitalize', 'blocksy'),
										uppercase: __('Uppercase', 'blocksy'),
									}[variant]
								}
							</i>
						</li>
					))}
				</ul>

				<ul className={classnames('ct-text-decoration')}>
					{['line-through', 'underline'].map((variant) => (
						<li
							key={variant}
							onClick={() =>
								onChange({
									...value,
									'text-decoration':
										value['text-decoration'] === variant
											? 'none'
											: variant,
								})
							}
							className={classnames({
								active: variant === value['text-decoration'],
							})}
							data-variant={variant}>
							<i className="ct-tooltip-top">
								{
									{
										'line-through': __(
											'Line Through',
											'blocksy'
										),
										underline: __('Underline', 'blocksy'),
									}[variant]
								}
							</i>
						</li>
					))}
				</ul>
			</li>
		</animated.ul>
	)
}

export default FontOptions
