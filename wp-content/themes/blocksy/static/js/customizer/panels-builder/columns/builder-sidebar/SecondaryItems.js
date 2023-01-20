import {
	createElement,
	Component,
	useState,
	useContext,
	Fragment,
} from '@wordpress/element'
import DraggableItems from '../DraggableItems'
import { DragDropContext } from '../../../../options/options/ct-footer-builder'
import cls from 'classnames'
import Panel, { PanelMetaWrapper } from '../../../../options/options/ct-panel'
import { getValueFromInput } from '../../../../options/helpers/get-value-from-input'
import { getOriginalId, customItemsSeparator } from '../../placements/helpers'

import { Slot } from '@wordpress/components'

const SecondaryItems = ({
	builderValue,
	builderValueDispatch,
	inlinedItemsFromBuilder,
	displayList = true,
}) => {
	const { panelsState, panelsActions, currentView, isDragging } =
		useContext(DragDropContext)

	const inlinedItemsFromAllViewsBuilder = builderValue.rows.reduce(
		(currentItems, { columns }) => [
			...currentItems,
			...(columns || []).reduce((c, items) => [...c, ...items], []),
		],
		[]
	)

	const secondaryItems =
		ct_customizer_localizations.header_builder_data.secondary_items.footer.filter(
			({ config }) =>
				// config.devices.indexOf(currentView) > -1 &&
				config.enabled
		)
	const allItems = ct_customizer_localizations.header_builder_data.footer

	/**
	 * Dynamic items have a : in their ID
	 */
	const allDynamicItems = Object.keys(builderValue.items).filter(
		(id) => id.indexOf(customItemsSeparator()) > -1
	)

	return (
		<DraggableItems
			options={{ sort: false, filter: '.ct-item-in-builder' }}
			group={{
				name: 'header_sortables',
				put: false,
				pull: 'clone',
			}}
			draggableId={'available-items'}
			items={[...secondaryItems.map(({ id }) => id), ...allDynamicItems]
				.filter((el) =>
					allItems.some(({ id }) => id === getOriginalId(el))
				)
				.sort((a, b) => {
					const aItemData =
						ct_customizer_localizations.header_builder_data[
							'footer'
						].find(({ id }) => id === getOriginalId(a))

					const bItemData =
						ct_customizer_localizations.header_builder_data[
							'footer'
						].find(({ id }) => id === getOriginalId(b))

					return aItemData.config.name.localeCompare(
						bItemData.config.name
					)
				})}
			hasPointers={false}
			panelType="footer"
			displayWrapper={displayList}
			propsForItem={(item) => ({
				renderItem: ({ item, itemData, index }) => {
					const itemOptions = allItems.find(
						({ id }) => id === getOriginalId(item)
					).options

					const allClonesAndOriginal = [
						getOriginalId(item),
						...allDynamicItems.filter(
							(id) => getOriginalId(id) === getOriginalId(item)
						),
					]

					const itemName =
						allClonesAndOriginal.length > 1
							? `${itemData.config.name} ${
									allClonesAndOriginal.indexOf(item) + 1
							  }`
							: itemData.config.name

					const option = {
						label: itemName,
						'inner-options': itemOptions,
					}

					const itemInBuilder =
						inlinedItemsFromBuilder.indexOf(item) > -1

					const id = `builder_panel_${item}`

					return (
						<PanelMetaWrapper
							id={id}
							option={option}
							{...panelsActions}
							getActualOption={({ open, container }) => (
								<Fragment>
									{inlinedItemsFromAllViewsBuilder.indexOf(
										item
									) > -1 && (
										<Panel
											id={id}
											getValues={() => {
												let itemValue =
													builderValue.items[item]

												if (
													itemValue &&
													Object.keys(
														itemValue.values
													) > 5
												) {
													return itemValue.values
												}

												return getValueFromInput(
													itemOptions,
													itemValue
														? itemValue.values
														: {}
												)
											}}
											option={option}
											onChangeFor={(
												optionId,
												optionValue
											) => {
												const currentValue =
													builderValue.items[item]

												builderValueDispatch({
													type: 'ITEM_VALUE_ON_CHANGE',
													payload: {
														id: item,
														optionId,
														optionValue,
														values:
															!currentValue ||
															(currentValue &&
																Object.keys(
																	currentValue.values
																).length === 0)
																? getValueFromInput(
																		itemOptions,
																		{}
																  )
																: {},
													},
												})
											}}
											view="simple"
										/>
									)}

									{displayList && (
										<div
											ref={container}
											data-id={item}
											className={cls({
												'ct-item-in-builder':
													itemInBuilder,
												'ct-builder-item':
													!itemInBuilder,
											})}
											onClick={() => {
												if (isDragging) {
													return
												}
												itemInBuilder && open()
											}}>
											{itemName}

											<Slot
												name={`ColumnsBuilderSidebarItem_${index}`}
												fillProps={{
													item,
													itemInBuilder,
													itemData,
												}}
											/>
										</div>
									)}
								</Fragment>
							)}></PanelMetaWrapper>
					)
				},
			})}
			direction="vertical"
		/>
	)
}

export default SecondaryItems
