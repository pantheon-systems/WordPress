import {
	createElement,
	Component,
	useState,
	useContext,
	Fragment,
} from '@wordpress/element'

import cls from 'classnames'
import Panel, { PanelMetaWrapper } from '../../../../options/options/ct-panel'
import { getValueFromInput } from '../../../../options/helpers/get-value-from-input'
import { DragDropContext } from '../../../../options/options/ct-footer-builder'

const InvisiblePanels = ({ builderValue, builderValueDispatch }) => {
	const secondaryItems =
		ct_customizer_localizations.header_builder_data.secondary_items.footer
	const allItems = ct_customizer_localizations.header_builder_data.footer

	const { panelsState, panelsActions } = useContext(DragDropContext)

	const primaryItems = allItems.filter(
		({ id }) => !secondaryItems.find((item) => item.id === id)
	)

	return (
		<Fragment>
			{primaryItems.map((primaryItem) => {
				const option = {
					label: primaryItem.config.name,
					'inner-options': primaryItem.options,
				}

				const id = `builder_panel_${primaryItem.id}`

				return (
					<PanelMetaWrapper
						id={id}
						key={primaryItem.id}
						option={option}
						{...panelsActions}
						getActualOption={({ container }) => (
							<Fragment>
								<Panel
									id={id}
									getValues={() => {
										let itemValue =
											builderValue.items[primaryItem.id]

										const maybeRow = builderValue.rows.find(
											({ id }) => id === primaryItem.id
										)

										let hasWidgetAreas = false

										if (maybeRow) {
											if (
												maybeRow.columns
													.reduce(
														(allItems, current) => [
															...allItems,
															current,
														],
														[]
													)
													.join('')
													.indexOf('widget-area') > -1
											) {
												hasWidgetAreas = true
											}
										}

										if (
											itemValue &&
											Object.keys(itemValue.values) > 5
										) {
											return {
												...itemValue.values,
												...(maybeRow
													? {
															items_per_row: maybeRow.columns.length.toString(),
															has_widget_areas: hasWidgetAreas
																? 'yes'
																: 'no',
													  }
													: {}),
											}
										}

										return {
											...getValueFromInput(
												primaryItem.options,
												itemValue
													? itemValue.values
													: {}
											),

											...(maybeRow
												? {
														items_per_row: maybeRow.columns.length.toString(),
														has_widget_areas: hasWidgetAreas
															? 'yes'
															: 'no',
												  }
												: {}),
										}
									}}
									option={option}
									onChangeFor={(optionId, optionValue) => {
										const currentValue =
											builderValue.items[primaryItem.id]

										builderValueDispatch({
											type: 'ITEM_VALUE_ON_CHANGE',
											payload: {
												id: primaryItem.id,
												optionId,
												optionValue,
												values:
													!currentValue ||
													(currentValue &&
														Object.keys(
															currentValue.values
														).length === 0)
														? getValueFromInput(
																primaryItem.options,
																{}
														  )
														: {},
											},
										})
									}}
									view="simple"
								/>
							</Fragment>
						)}
					/>
				)
			})}
		</Fragment>
	)
}

export default InvisiblePanels
