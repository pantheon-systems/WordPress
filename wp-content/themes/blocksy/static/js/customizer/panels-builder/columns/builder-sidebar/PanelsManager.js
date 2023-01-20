import {
	createElement,
	Component,
	useState,
	useContext,
	Fragment,
} from '@wordpress/element'
import cls from 'classnames'

import { DragDropContext } from '../../../../options/options/ct-footer-builder'
import Panel, { PanelMetaWrapper } from '../../../../options/options/ct-panel'
import { getValueFromInput } from '../../../../options/helpers/get-value-from-input'

import { __ } from 'ct-i18n'

const PanelsManager = () => {
	const secondaryItems =
		ct_customizer_localizations.header_builder_data.secondary_items.footer
	const allItems = ct_customizer_localizations.header_builder_data.footer

	const {
		builderValueCollection,
		builderValue,
		builderValueDispatch,
		panelsActions,
	} = useContext(DragDropContext)

	const allSections = builderValueCollection.sections.filter(
		({ id }) =>
			id !== 'type-2' && id !== 'type-3' && id.indexOf('ct-custom') === -1
	)

	return (
		<div className="ct-panels-manager">
			{allSections.map(({ name, id }) => {
				return <div>{name}</div>
			})}

			<ul className={cls('ct-panels-list')}>
				{allSections.map(({ name, id }) => {
					let panelLabel =
						name ||
						{
							'type-1': __('Global Footer', 'blocksy'),
						}[id] ||
						id

					const panelId = `builder_footer_panel_${id}`

					const footerOptions =
						ct_customizer_localizations.header_builder_data
							.footer_data.footer_options

					const option = {
						label: panelLabel,
						'inner-options': footerOptions,
					}

					return (
						<PanelMetaWrapper
							id={panelId}
							key={id}
							option={option}
							{...panelsActions}
							getActualOption={({ open }) => (
								<Fragment>
									{id === builderValue.id && (
										<Panel
											id={panelId}
											getValues={() =>
												getValueFromInput(
													footerOptions,

													Array.isArray(
														builderValue.settings
													)
														? {}
														: builderValue.settings
												)
											}
											option={option}
											onChangeFor={(
												optionId,
												optionValue
											) => {
												builderValueDispatch({
													type:
														'BUILDER_GLOBAL_SETTING_ON_CHANGE',
													payload: {
														optionId,
														optionValue,
													},
												})
											}}
											view="simple"
										/>
									)}

									{id === builderValue.id && (
										<li
											className={cls({
												active: id === builderValue.id,
												'ct-global': id === 'type-1',
											})}
											onClick={() => {
												open()
											}}>
											<span className="ct-panel-name">
												{panelLabel}
											</span>
										</li>
									)}
								</Fragment>
							)}></PanelMetaWrapper>
					)
				})}
			</ul>
		</div>
	)
}

export default PanelsManager
