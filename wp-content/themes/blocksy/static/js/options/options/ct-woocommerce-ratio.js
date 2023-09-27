import { createElement, Component, useState } from '@wordpress/element'
import cls from 'classnames'
import { __, sprintf } from 'ct-i18n'
import Ratio from './ct-ratio'

const WooCommerceRatio = ({
	value,
	onChange,
	onChangeFor,
	values,
	values: {
		woocommerce_thumbnail_cropping_custom_width,
		woocommerce_thumbnail_cropping_custom_height,
	},
	option,
	...props
}) => {
	return (
		<Ratio
			onChange={(val) => {
				let isCustom = val.indexOf('/') === -1
				let [width, height] = val.split(isCustom ? ':' : '/')

				if (val === 'original') {
					onChangeFor('woocommerce_thumbnail_cropping', 'uncropped')
					onChange('uncropped')
					return
				}

				onChange(isCustom ? 'custom' : 'predefined')
				onChangeFor('woocommerce_thumbnail_cropping', 'custom')

				onChangeFor(
					'woocommerce_thumbnail_cropping_custom_height',
					parseFloat(height || '0') || 0
				)

				onChangeFor(
					'woocommerce_thumbnail_cropping_custom_width',
					parseFloat(width || '0') || 0
				)
			}}
			value={
				value === 'uncropped'
					? 'original'
					: value === '1:1'
					? `1/1`
					: `${woocommerce_thumbnail_cropping_custom_width}${
							value === 'custom' ? ':' : '/'
					  }${woocommerce_thumbnail_cropping_custom_height}`
			}
			option={{
				...option,
				value: '1/1',
			}}
			onChangeFor={onChangeFor}
			values={values}
			{...props}
		/>
	)
}

WooCommerceRatio.ControlEnd = () => (
	<div
		className="ct-color-modal-wrapper"
		onMouseDown={(e) => e.stopPropagation()}
		onMouseUp={(e) => e.stopPropagation()}
	/>
)

export default WooCommerceRatio
