import {
	createElement,
	Component,
	useState,
	useContext,
	Fragment,
} from '@wordpress/element'
import cls from 'classnames'
import { __, sprintf } from 'ct-i18n'

import { Slot } from '@wordpress/components'

import SecondaryItems from './builder-sidebar/SecondaryItems'
import InvisiblePanels from './builder-sidebar/InvisiblePanels'

import PanelsManager from './builder-sidebar/PanelsManager'

import { DragDropContext } from './BuilderRoot'

import classnames from 'classnames'

const AvailableItems = ({
	allBuilderSections,
	builderValue,
	builderValueDispatch,
	inlinedItemsFromBuilder,
}) => {
	// panels | items | options
	const [currentTab, setCurrentTab] = useState('items')

	const { builderValueCollection } = useContext(DragDropContext)

	const secondaryItems =
		ct_customizer_localizations.header_builder_data.secondary_items.header
	const allItems = ct_customizer_localizations.header_builder_data.header

	const headerOptions =
		ct_customizer_localizations.header_builder_data.header_data
			.header_options

	return (
		<div className="ct-available-items">
			<h3
				className="ct-title"
				dangerouslySetInnerHTML={{
					__html: sprintf(
						__('Customizing: %s', 'blocksy'),
						`<span>${
							(
								builderValueCollection.sections.find(
									({ id }) => id === builderValue.id
								) || {}
							).name ||
							{
								'type-1': __('Global Header', 'blocksy'),
							}[builderValue.id] ||
							builderValue.id
						}</span>`
					),
				}}
			/>

			<div className="ct-tabs">
				<ul>
					{['items', 'panels'].map((tab) => (
						<li
							key={tab}
							onClick={(e) => {
								e.preventDefault()
								setCurrentTab(tab)
							}}
							className={cls({
								active: tab === currentTab,
							})}>
							{
								{
									panels: __('Headers', 'blocksy'),
									items: __('Elements', 'blocksy'),
								}[tab]
							}
						</li>
					))}
				</ul>

				<div className="ct-current-tab">
					{currentTab === 'panels' && (
						<Slot name="PlacementsBuilderPanelsManager">
							{(fills) =>
								fills.length === 0 ? <PanelsManager /> : fills
							}
						</Slot>
					)}

					<SecondaryItems
						builderValue={builderValue}
						builderValueDispatch={builderValueDispatch}
						inlinedItemsFromBuilder={inlinedItemsFromBuilder}
						displayList={currentTab === 'items'}
					/>
				</div>
			</div>

			<InvisiblePanels
				builderValue={builderValue}
				builderValueDispatch={builderValueDispatch}
			/>
		</div>
	)
}

export default AvailableItems
