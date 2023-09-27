import {
	createElement,
	Component,
	useState,
	Fragment,
} from '@wordpress/element'
import DraggableItems from './DraggableItems'
import cls from 'classnames'
import Panel, { PanelMetaWrapper } from '../../../options/options/ct-panel'
import { getValueFromInput } from '../../../options/helpers/get-value-from-input'
import { __ } from 'ct-i18n'

import OptionsPanel from '../../../options/OptionsPanel'

import { Slot } from '@wordpress/components'

import SecondaryItems from './builder-sidebar/SecondaryItems'
import InvisiblePanels from './builder-sidebar/InvisiblePanels'
import PanelsManager from './builder-sidebar/PanelsManager'

const AvailableItems = ({
	builderValue,
	builderValueCollection,
	builderValueDispatch,
	inlinedItemsFromBuilder,
}) => {
	// panels | items | options
	const [currentTab, setCurrentTab] = useState('items')

	const secondaryItems =
		ct_customizer_localizations.header_builder_data.secondary_items.footer
	const allItems = ct_customizer_localizations.header_builder_data.footer

	const footerOptions =
		ct_customizer_localizations.header_builder_data.footer_data
			.footer_options

	return (
		<div className="ct-available-items ct-footer-builder-options">
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
								'type-1': __('Global Footer', 'blocksy'),
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
									panels: __('Footers', 'blocksy'),
									items: __('Elements', 'blocksy'),
								}[tab]
							}
						</li>
					))}
				</ul>

				<div className="ct-current-tab">
					{currentTab === 'panels' && (
						<Slot name="ColumnsBuilderPanelsManager">
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
