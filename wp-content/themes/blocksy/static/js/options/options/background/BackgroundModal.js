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
import PatternPicker from './PatternPicker'
import ImagePicker from './ImagePicker'

import GradientPicker from './GradientPicker'

const BackgroundModal = ({
	option,
	option: { activeTabs = ['color', 'gradient', 'image'] },
	value,
	isOpen,
	onChange,
	setOutsideClickFreezed,
}) => {
	return (
		<Fragment>
			<ul
				className="ct-modal-tabs"
				onMouseUp={(e) => {
					e.preventDefault()
				}}>
				{activeTabs.map((type) => (
					<li
						data-type={type}
						key={type}
						className={classnames({
							active:
								type === value.background_type ||
								(type === 'image' &&
									(value.background_type === 'pattern' ||
										value.background_type === 'image')),
						})}
						onClick={() =>
							onChange({
								...value,
								background_type: type,

								...(type === 'gradient' && !value.gradient
									? {
											gradient:
												'linear-gradient(135deg,rgba(6,147,227,1) 0%,rgb(155,81,224) 100%)',
									  }
									: {}),
							})
						}>
						{
							{
								color: __('Color', 'blocksy'),
								gradient: __('Gradient', 'blocksy'),
								// pattern: __('Pattern', 'blocksy'),
								image: __('Image', 'blocksy'),
							}[type]
						}
					</li>
				))}
			</ul>

			<div
				className={classnames(
					{
						'ct-image-tab ct-options-container':
							value.background_type === 'image' ||
							value.background_type === 'pattern',
						'ct-color-picker-modal':
							value.background_type === 'gradient',
						'ct-gradient-tab': value.background_type === 'gradient',
						'ct-color-tab': value.background_type === 'color',
					},
					'ct-modal-tabs-content'
				)}>
				{(value.background_type === 'image' ||
					value.background_type === 'pattern') && (
					<ul
						className="ct-radio-option ct-buttons-group"
						onMouseUp={(e) => {
							e.preventDefault()
						}}>
						{['image', 'pattern'].map((type) => (
							<li
								data-type={type}
								key={type}
								className={classnames({
									active: type === value.background_type,
								})}
								onClick={() =>
									onChange({
										...value,
										background_type: type,
									})
								}>
								{
									{
										pattern: __('Pattern', 'blocksy'),
										image: __('Image', 'blocksy'),
									}[type]
								}
							</li>
						))}
					</ul>
				)}

				{value.background_type === 'pattern' && (
					<PatternPicker
						option={option}
						onChange={onChange}
						value={value}
					/>
				)}

				{value.background_type === 'image' && (
					<ImagePicker
						setOutsideClickFreezed={setOutsideClickFreezed}
						option={option}
						onChange={onChange}
						value={value}
					/>
				)}

				{value.background_type === 'gradient' && (
					<GradientPicker value={value} onChange={onChange} />
				)}

				{value.background_type !== 'gradient' && (
					<GenericOptionType
						value={value['backgroundColor']}
						values={value}
						option={{
							id: 'backgroundColor',
							label:
								value.background_type === 'color'
									? false
									: __('Background Color', 'blocksy'),
							type: 'ct-color-picker',
							skipNoColorPill: true,
							design:
								value.background_type === 'color'
									? 'none'
									: 'inline',
							value: option.value['backgroundColor'],
							pickers: [
								{
									title: __('Initial', 'blocksy'),
									id: 'default',
								},
							],
							inline_modal: value.background_type === 'color',
							skipArrow: true,
							appendToBody: false,
						}}
						hasRevertButton={false}
						onChange={(newValue) =>
							onChange({
								...value,
								backgroundColor: newValue,
							})
						}
					/>
				)}
			</div>
		</Fragment>
	)
}

export default BackgroundModal
