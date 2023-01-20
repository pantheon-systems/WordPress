import {
	createElement,
	Component,
	useState,
	Fragment,
} from '@wordpress/element'
import cls from 'classnames'
import { __, sprintf } from 'ct-i18n'
import NumberOption from './ct-number'
import classnames from 'classnames'

const WooColumnsAndRows = ({
	onChange,
	value,
	option,
	option: { columns_id, rows_id },
	device,
	onChangeFor,
	values,
	values: { woocommerce_catalog_columns, woocommerce_catalog_rows },
}) => {
	const rowsValue = rows_id ? values[rows_id] : woocommerce_catalog_rows

	return (
		<div
			className={classnames('ct-woo-columns-and-rows', {})}
			{...(device !== 'desktop' ? { 'data-disabled-last': '' } : {})}>
			<div>
				<NumberOption
					option={{
						...option,
						attr: {
							...(option.attr || {}),
							'data-width': 'full',
						},
					}}
					value={
						!columns_id && device === 'desktop'
							? woocommerce_catalog_columns
							: value
					}
					onChange={(val) => {
						device === 'desktop' && !columns_id
							? onChangeFor('woocommerce_catalog_columns', val)
							: onChange(val)
					}}
				/>
				<p className="ct-option-description">
					{__('Number of columns', 'blc')}
				</p>
			</div>

			<div>
				<NumberOption
					option={{
						min: 1,
						max: 100,
						responsive: false,
						value: 4,
						attr: {
							'data-width': 'full',
						},
					}}
					value={device === 'desktop' ? rowsValue : 'auto'}
					onChange={(val) => {
						device === 'desktop' &&
							onChangeFor(
								rows_id || 'woocommerce_catalog_rows',
								val
							)

						if (wp.customize && wp.customize.previewer) {
							wp.customize.previewer.send(
								'ct:sync:refresh_partial',
								{
									id: rows_id || 'woocommerce_catalog_rows',
								}
							)
						}
					}}
				/>
				<p className="ct-option-description">
					{__('Number of rows', 'blc')}
				</p>
			</div>
		</div>
	)
}

WooColumnsAndRows.renderingConfig = {
	getValueForRevert: ({
		value,
		values: { woocommerce_catalog_columns, woocommerce_catalog_rows },
		option,
		option: { columns_id, rows_id },
		values,
		device,
	}) => {
		const rowsValue = rows_id ? values[rows_id] : woocommerce_catalog_rows

		let myResult = {
			...value,
			desktop: woocommerce_catalog_columns,
			woocommerce_catalog_columns,
			woocommerce_catalog_rows,
		}

		return myResult
	},

	computeOptionValue: (v) => ({
		...v,
		woocommerce_catalog_columns: 4,
		woocommerce_catalog_rows: 4,
	}),

	computeOptionValue: (v) => {
		const result = {
			...v,
			woocommerce_catalog_columns: 4,
			woocommerce_catalog_rows: 4,
		}

		return result
	},

	performRevert: ({ onChangeFor }) => {
		onChangeFor('woocommerce_catalog_columns', 4)
		onChangeFor('woocommerce_catalog_rows', 4)
	},
}

export default WooColumnsAndRows
